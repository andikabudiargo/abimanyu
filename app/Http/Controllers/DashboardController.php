<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Todo;
use App\Models\User;
use App\Models\BookingRoom;
use App\Models\CancelBookingRoom;
use App\Models\Document;
use App\Models\DocumentRevision;
use App\Models\Department;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
 public function index()
{
    $user = Auth::user();
    $users = User::all(); // <- ini penting
    $departments = Department::all();

    // Ambil daftar nama role & departemen user (via pivot)
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');

    // Cek apakah user adalah Approver
    $isApprover = $userRoles->contains(function ($role) {
        return in_array($role, ['Supervisor Special Access', 'Manager Special Access']);
    }) && $userDepartments->contains('Information & Technology');

    

    // Cek apakah user adalah Staff IT yang memproses
    $isITSpecialStaff = $userRoles->contains('IT Special Access') &&
                        $userDepartments->contains('Information & Technology');

                        // ✅ Cek apakah Supervisor Special Access dari departemen Maintenance
$isMaintenanceSpecialSupervisor = $userRoles->contains('Supervisor Special Access') &&
                                   $userDepartments->contains('Maintenance');

    // Default variabel
    $ticketsToApprove = collect();
    $ticketSectionTitle = null;
    $userRoleLabel = null;

    if ($isApprover) {
    $ticketsToApprove = Ticket::where('status', 'Pending')
        ->latest()
        ->take(10)
        ->get();
    $ticketSectionTitle = 'Tickets Need Approval';
    $userRoleLabel = 'approve';
} elseif ($isITSpecialStaff || $isMaintenanceSpecialSupervisor) {
    $ticketsToApprove = Ticket::where('status', 'Approved')
        ->whereHas('category.department', function ($q) use ($isITSpecialStaff, $isMaintenanceSpecialSupervisor) {
            if ($isITSpecialStaff) {
                $q->where('name', 'Information & Technology'); // hanya tiket IT
            }
            if ($isMaintenanceSpecialSupervisor) {
                $q->where('name', 'Maintenance'); // hanya tiket Maintenance
            }
        })
        ->latest()
        ->take(10)
        ->get();

    $ticketSectionTitle = 'Tickets Need to Be Processed';
    $userRoleLabel = 'process';
}

// Ambil tiket Done milik user yang login
$ticketsToClose = Ticket::where('status', 'Done')
    ->where('request_by', $user->id) // milik user login
    ->latest()
    ->take(10) // jumlah tiket maksimal ditampilkan
    ->get();

$ticketCloseSectionTitle = null;
if($ticketsToClose->count() > 0) {
    $ticketCloseSectionTitle = 'Your Ticket is Done, Please Close Immediately';
}


// ✅ Logika Dokumen
    $documentsToReview = collect();
    $documentSectionTitle = null;

    // 1. Jika user Supervisor → tampilkan dokumen dari requestor departemennya yg statusnya "Under Review"
    if ($userRoles->contains('Supervisor Special Access') || $userRoles->contains('Manager Special Access')) {
        $documentsToReview = Document::where('status', 'Draft')
            ->whereHas('revisions.requestor.departments', function ($q) use ($userDepartments) {
                $q->whereIn('name', $userDepartments); // hanya dokumen dari departemen dia
            })
             ->orderByDesc(
        DocumentRevision::select('created_at')
            ->whereColumn('document_id', 'documents.id')
            ->latest()
            ->limit(1)
    )
            ->take(10)
            ->get();

        $documentSectionTitle = 'Documents Submission Need Your Approval';
    }

    // 2. Jika user dari departemen "Management Representative" → tampilkan dokumen yg sudah Approved
    if ($userDepartments->contains('Management Representative')) {
        $documentsToReview = Document::where('status', 'Approved')
            ->orderByDesc(
        DocumentRevision::select('created_at')
            ->whereColumn('document_id', 'documents.id')
            ->latest()
            ->limit(1)
    )
            ->take(10)
            ->get();

        $documentSectionTitle = 'Documents Need Your Review';
    }


   $todos = Todo::where('user_id', auth()->id()) // user pembuat
             ->orWhereHas('users', function ($q) {
                 $q->where('users.id', auth()->id()); // user diundang
             })
             ->with('users')
             ->latest()
             ->get();


$today = Carbon::today()->toDateString();

    // Ambil semua announcement yang aktif hari ini
    $announcements = Announcement::with(['createdBy.departments', 'attachments'])
        ->where('display_start', '<=', $today)
        ->where('display_end', '>=', $today)
        ->orderBy('display_start', 'desc')
        ->get();

        $today = now()->toDateString();
$now   = now();

if ($user->roles->pluck('name')->contains('Admin GA')) {
    // === Admin GA lihat booking yang butuh approval ===
    $activeBookings = BookingRoom::with('room', 'creator')
        ->where('status', 'Waiting Approval')
        ->where('booking_date', $today)
        ->whereTime('end_time', '>=', $now->format('H:i:s'))
        ->orderBy('booking_date')
        ->orderBy('start_time')
        ->get();
} else {
    // === User biasa hanya lihat booking miliknya ===
    $activeBookings = BookingRoom::with('room')
        ->where('created_by', $user->id)
        ->where('booking_date', $today)
        ->whereTime('end_time', '>=', $now->format('H:i:s'))
         ->where('status', '!=', 'Cancelled') // ⬅️ Tambahan filter
        ->orderBy('booking_date')
        ->orderBy('start_time')
        ->get();
}

$cancelledBookings = BookingRoom::with('room')
    ->where('created_by', Auth::id()) // hanya booking milik user ini
     ->where('status', '=', 'Cancelled') // ⬅️ Tambahan filter
    ->orderBy('created_at', 'desc')
    ->get();


   return view('dashboard', compact(
    'user',
    'ticketsToApprove',
    'ticketSectionTitle',
    'ticketsToClose',
    'ticketCloseSectionTitle',
    'userRoleLabel',
     'documentsToReview',      // ⬅️ tambahkan ke view
        'documentSectionTitle',   // ⬅️ tambahkan ke view
    'todos',
    'users',
    'departments',
    'announcements',
     'activeBookings',
     'cancelledBookings', // ⬅️ tambahkan ke view
));

}

public function todaySchedule()
{
    $today = now()->toDateString();

    $bookings = BookingRoom::with('room', 'creator')
     ->where('status', '!=', 'Cancelled') // ⬅️ Tambahan filter
        ->whereDate('booking_date', $today)
        ->orderBy('start_time')
        ->get();

    return response()->json($bookings);
}



}
