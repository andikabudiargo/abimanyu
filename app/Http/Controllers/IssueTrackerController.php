<?php

namespace App\Http\Controllers;

use App\Mail\TicketDoneMail;
use App\Mail\TicketProcessMail;
use App\Mail\TicketRequestsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Department;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\IssueTracker;
use App\Models\IssueTrackerMaterial;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Models\TicketAttachment;
use App\Mail\TicketRequestMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;

class IssueTrackerController extends Controller
{
     public function index(Request $request) {
    $departments = Department::orderBy('name')->get(); // ambil semua department

  // Filter bulan & tahun
    $month = $request->get('month', now()->month);
    $year  = $request->get('year', now()->year);

    // Ambil semua issue bulan & tahun ini
    $issues = IssueTracker::with('materials')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

    // Hitung jumlah issue yang sudah Closed
    $closedCount = $issues->where('status', 'Closed')->count();

   // Hitung total biaya dari materials
$totalCost = $issues->flatMap(fn($issue) => $issue->materials)->sum('subtotal');

// Hitung jumlah issue yang berkontribusi pada biaya
$totalIssuesCount = $issues->filter(fn($issue) => $issue->materials->isNotEmpty())->count();


    // Kategori kerusakan paling banyak
    $topCategoryGroup = $issues->groupBy('request_type')
                          ->map(fn($g) => $g->count())
                          ->sortDesc();
                          $topCategory = $topCategoryGroup->keys()->first();
$topCategoryCount = $topCategoryGroup->first(); // jumlah kemunculan

    // Dropdown filter bulan & tahun
    $months = collect(range(1,12))->mapWithKeys(fn($m) => [$m => Carbon::create()->month($m)->format('F')]);
    $years  = IssueTracker::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');



        return view('it.issue_tracker', compact(
            'departments',
       'closedCount', 'totalCost', 'topCategory',
    'month', 'year', 'months', 'years', 'topCategoryCount', 'totalIssuesCount'




    ));
    }

    public function data(Request $request)
{
     $user = auth()->user();
     // Ambil semua nama departemen user dari relasi pivot
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name')->toArray(); // asumsi relasi `roles` tersedia

  $query = IssueTracker::with(['creator.departments', 'creator.roles', 'approver', 'checker', 'verifier','authorizer', 'finisher', 'closer'])
    ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Work in Progress', 'Done', 'Closed', 'Rejected')")
    ->orderBy('created_at', 'desc');


    if ($request->request_number) {
        $query->where('request_number', 'like', '%' . $request->request_number . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
}

  if ($request->filled('urgency')) {
    $query->where('urgency', $request->urgency);
}

 if ($request->request_type && is_array($request->request_type)) {
    $query->whereIn('request_type', $request->request_type);
}



   if ($request->date) {
    $dates = explode(' to ', $request->date);

    if (count($dates) === 2) {
        $start = Carbon::parse($dates[0])->startOfDay();
        $end   = Carbon::parse($dates[1])->endOfDay();
        $query->whereBetween('closed_at', [$start, $end]);
    } else {
        // Satu tanggal saja
        $start = Carbon::parse($dates[0])->startOfDay();
        $end   = Carbon::parse($dates[0])->endOfDay();
        $query->whereBetween('closed_at', [$start, $end]);
    }
}

if ($request->department) {
        $query->whereHas('creator.departments', function ($q) use ($request) {
            $q->where('name', $request->department);
        });
    }


    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;
    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->created_by === Auth::id();
   $hasSameDepartment = $userDepartments->intersect($userDepartments)->isNotEmpty();
    $detail_url = route('it.issue.show', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $edit_url = route('it.issue.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $requestNumber = $row->request_number ?? 'Unknown';


$actionButtons = '
<div class="relative inline-block text-left">
  <button type="button"
    data-dropdown-id="' . $dropdownId . '"
    onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
    class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
    <i data-feather=\'align-justify\'></i>
  </button>
  <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">';


 $actionButtons .= '
            <a href="' . $detail_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
            </a>';



    // Tombol edit + delete default untuk owner jika status Pending
if ($isOwner && $row->status === 'Pending') {
    // Jika pengaju BUKAN IT Special Access → tampilkan tombol delete
        $actionButtons .= '
            <a href="' . $edit_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete(' . $id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>';
}
   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Pending' &&
    $userDepartments === 'General Affair' && // hanya untuk GA
    $userRoles->contains(function ($role) {
        return in_array($role, [
            'Manager Special Access',  'Supervisor Special Access' // hanya Manager Special Access
        ]);
    })
) {

    $actionButtons .= '
    <button onclick="approveRequest(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
        <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
    </button>
    <button onclick="rejectRequest(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
        <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
    </button>';
}

    // Tombol Checking untuk GA setelah Approved
if (
    $row->status === 'Approved' &&
    $userDepartments->contains('General Affair') // pastikan $userDepartments sudah pluck('name')
) {
   $actionButtons .= '
    <button onclick="openCheckingModal(' .  $id . ')" class="block w-full text-left px-4 py-2 hover:bg-blue-100 text-blue-700">
        <i data-feather="check-square" class="w-4 h-4 inline mr-2"></i>Checking
    </button>';

}

if (
    $row->status === 'Work in Progress' &&
    $userDepartments->contains('General Affair') &&
    $row->checked_by == auth()->id()
) {
    $actionButtons .= '
        <button onclick="openDoneModal(' .  $id . ')" 
                class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check-square" class="w-4 h-4 inline mr-2"></i>Done
        </button>
         <button onclick="rejectRequest(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
}

if (
    $row->status === 'Done' &&
    $row->created_by === Auth::id()// hanya user yang memproses
) {
   $actionButtons .= '
 <button onclick="showCloseModal(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-teal-100 text-teal-700">
    <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Close
</button>
';

}

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('request_by', function ($row) {
    return $row->creator ? $row->creator->name : '-';
})
->addColumn('department', function ($row) {
    return $row->creator && $row->creator->departments->first()
        ? $row->creator->departments->first()->name
        : '-';
})
->addColumn('approved_by', function ($row) {
    return $row->approver ? $row->approver->name : '-';
})

->addColumn('checked_by', function ($row) {
    return $row->checker ? $row->checker->name : '-';
})

->addColumn('verification_by', function ($row) {
    return $row->verifier ? $row->verifier->name : '-';
})

->addColumn('authorized_by', function ($row) {
    return $row->authorizer ? $row->authorizer->name : '-';
})

->addColumn('done_by', function ($row) {
    return $row->finisher ? $row->finisher->name : '-';
})

->addColumn('closed_by', function ($row) {
    return $row->closer ? $row->closer->name : '-';
})

->editColumn('status', function ($row) {

    $commonClasses = 'inline-block text-center w-48 text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Pending') {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Pending</span>';
    } elseif ($row->status === 'Approved') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Approved</span>';
    } elseif ($row->status === 'Work in Progress') {
        return '<span class="bg-blue-500 ' . $commonClasses . '">Work in Progress</span>';
    } elseif ($row->status === 'Done') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Done</span>';
    } elseif ($row->status === 'Closed') {
        return '<span class="bg-teal-500 ' . $commonClasses . '">Closed</span>';
    } elseif ($row->status === 'Rejected') {
        return '<span class="bg-red-500 ' . $commonClasses . '">Rejected</span>';
    }
})


->editColumn('urgency', function ($row) {

    if ($row->urgency === 'normal') {
        return '<span class="text-green-600">Normal</span>';
    } elseif ($row->urgency === 'segera') {
        return '<span class="text-yellow-600">Segera</span>';
    } elseif ($row->urgency === 'darurat') {
        return '<span class="text-red-600">Darurat</span>';
    }
})

 ->editColumn('request_number', function ($row) {
    $colorClass = '';
    switch ($row->status) {
        case 'Pending':
            $colorClass = 'bg-gray-500';
            break;
        case 'Approved':
            $colorClass = 'bg-yellow-500';
            break;
        case 'Work in Progress':
            $colorClass = 'bg-blue-500';
            break;
             case 'Done':
            $colorClass = 'bg-green-400';
            break;
        case 'Closed':
            $colorClass = 'bg-teal-400';
            break;
            case 'Rejected':
            $colorClass = 'bg-red-600';
            break;
        default:
            $colorClass = 'bg-gray-300';
    }

    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->request_number . '</span>';
})

        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action', 'status', 'urgency', 'request_number'])
        ->make(true);
}

    public function create()
{
    // Ambil ID user yang sedang login
    $userId = Auth::id();

    // Ambil data user lengkap berdasarkan ID
    $user = User::with('departments')->findOrFail($userId);

    // Ambil 1 departemen user (kalau punya banyak, ambil yang pertama)
    $department = $user->departments->first();

    // Ambil semua departemen (untuk admin / GA)
    $departments = Department::all();

    return view('it.create_issue_tracker', compact('user', 'department', 'departments'));
}

    public function store(Request $request)
{
    // validasi input
        $request->validate([
            'location_area' => 'required|string',
            'request_type'   => 'required|string',
            'description'     => 'required|string',
            'urgency'         => 'required|string',
            'recommendation'  => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:jpg,png,pdf,xlsx,doc,docx|max:5048', // 2MB per file
    ]);

  
        // ============================
    // 1. Generate Request Number per BULAN
    // ============================
    $yearMonth = now()->format('Ymd'); // YYYYMM
    $countThisMonth = IssueTracker::whereYear('created_at', now()->year)
                                  ->whereMonth('created_at', now()->month)
                                  ->count() + 1;
    $numberPart = str_pad($countThisMonth, 3, '0', STR_PAD_LEFT); // 001, 002, ...
    $requestNumber = "GA-MNT-{$yearMonth}-{$numberPart}";

    // 3️⃣ Handle upload attachment (single)
    $fileName = null;

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $destinationPath = '/home/abimany3/public_html/attachment';

        // Pastikan folder ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Buat nama aman (jika ada file dengan nama sama, tambahkan -1, -2, dst)
        $originalName = $file->getClientOriginalName();
        $fileBase = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $fileName = $fileBase . '.' . $extension;
        $counter = 1;

        while (file_exists($destinationPath . '/' . $fileName)) {
            $fileName = $fileBase . '-' . $counter . '.' . $extension;
            $counter++;
        }

        // Pindahkan file ke folder tujuan
        $file->move($destinationPath, $fileName);
    }

    // ============================
    // 3. Simpan ke database (Request)
    // ============================
    $requestRecord = IssueTracker::create([
        'request_number'  => $requestNumber,
        'location_area' => $request->location_area,
        'request_type'   => $request->request_type,
        'description'     => $request->description,
        'urgency'         => $request->urgency,
        'attachment'     => $fileName, // 👈 kolom singular
        'recommendation' => $request->recommendation,
        'created_by'      => Auth::id(),
        'created_at'      => now(),
    ]);

    // ============================
    // 4. Response JSON
    // ============================
    return response()->json([
        'success' => true,
        'message' => 'Request berhasil dibuat!',
        'data' => [
            'request_number' => $requestNumber,
            'request_id'     => $requestRecord->id,
        ],
    ]);
}


public function edit($id)
    {
         // Ambil ID user yang sedang login
    $userId = Auth::id();

    // Ambil data user lengkap berdasarkan ID
    $user = User::with('departments')->findOrFail($userId);

    // Ambil 1 departemen user (kalau punya banyak, ambil yang pertama)
    $department = $user->departments->first();

    // Ambil semua departemen (untuk admin / GA)
    $departments = Department::all();
        $issue = IssueTracker::findOrFail($id);
        return view('it.edit_issue_tracker', compact('issue','user', 'department', 'departments'));
    }

    public function update(Request $request, $id)
{
    $issue = IssueTracker::findOrFail($id);
    $destinationPath = '/home/abimany3/public_html/attachment';

    $request->validate([
        'location_area'   => 'required|string',
        'request_type'         => 'required|string|max:255',
        'description'   => 'nullable|string',
        'urgency'   => 'required|string',
        'recommendation'   => 'nullable|string',
        'attachment' => 'file|mimes:jpg,png,pdf,doc,docx,xlsx|max:2048',
    ]);

    // Update data ticket
    $issue->update([
        'location_area' => $request->location_area,
        'request_type'       => $request->request_type,
        'description' => $request->description,
        'urgency' => $request->urgency,
        'recommendation' => $request->recommendation,
    ]);

   // Hapus file lama
if ($issue->attachment && file_exists($destinationPath.'/'.$issue->attachment)) {
    unlink($destinationPath.'/'.$issue->attachment);
}

// Cek ada file baru diupload
if ($request->hasFile('attachment')) {
    $file = $request->file('attachment'); // hanya ambil 1 file
    $filename = time().'_'.$file->getClientOriginalName();
    $file->move($destinationPath, $filename);

    // Update kolom attachment di issue_tracker
    $issue->attachment = $filename;
}

    return response()->json(['success' => true, 'message' => 'Request updated successfully']);
}

public function approve($id)
{
    $request = IssueTracker::findOrFail($id);
    $request->status = 'Approved';
    $request->approved_by = auth()->id();
    $request->approved_at = now();
    $request->save();

    return response()->json([
        'success' => true,
        'message' => 'Request Berhasil Disetujui.',
        'request_number' => $request->request_number
    ]);
}

public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $issue = IssueTracker::findOrFail($id);
    $issue->status = 'Rejected';
    $issue->rejected_reason = $request->input('rejected_reason', 'No reason provided.');
    $issue->rejected_by = auth()->id();
    $issue->rejected_at = now();
    $issue->save();

     return response()->json([
        'success' => true,
        'message' => 'Request rejected successfully.',
        'request_number' => $issue->request_number
    ]);
}

public function checking(Request $request, $id)
{
    $request->validate([
        'check_result' => 'required|string',
        'duration_work' => 'required|numeric',
        'recommended_action' => 'nullable|string',

        // validasi array material
        'material.*' => 'nullable|string',
        'qty.*' => 'nullable|numeric',
        'uom.*' => 'nullable|string',
        'vendor.*' => 'nullable|string',
        'price.*' => 'nullable|numeric',
    ]);

    DB::beginTransaction();
    try {
        // Update issue header
        $issue = IssueTracker::findOrFail($id);
        $issue->check_result = $request->check_result;
        $issue->duration_work = $request->duration_work;
        $issue->status = 'Work in Progress';
        $issue->recommended_action = $request->recommended_action;
        $issue->checked_by = auth()->id();
        $issue->checked_at = now();
        $issue->save();

        // Hapus material lama (jika pernah diinput ulang)
        IssueTrackerMaterial::where('issue_tracker_id', $id)->delete();

        // Simpan material baru (jika ada)
        if ($request->has('material') && is_array($request->material)) {
            foreach ($request->material as $index => $mat) {
                if (empty($mat)) continue; // skip jika kosong

                IssueTrackerMaterial::create([
                    'issue_tracker_id' => $id,
                    'material' => $mat,
                    'qty' => $request->qty[$index] ?? 0,
                    'uom' => $request->uom[$index] ?? '',
                    'vendor' => $request->vendor[$index] ?? '',
                    'price' => $request->price[$index] ?? 0,
                    'subtotal' => ($request->qty[$index] ?? 0) * ($request->price[$index] ?? 0),
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pengecekan awal berhasil disimpan.',
            'request_number' => $issue->request_number,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}
  // IT mengubah status menjadi Done (dengan CA/PA & Evidence)
   public function done(Request $request, $id)
{
    $ticket = IssueTracker::findOrFail($id);

    // Hanya user yang melakukan pengecekan yang boleh Done
    if ($ticket->checked_by != Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'assigned_by'     => 'required|string',
        'work_start'    => 'required|date',
        'work_end'      => 'required|date|after_or_equal:work_start',
        'note_done'        => 'nullable|string',
        'evidence_before'  => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        'evidence_after'   => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
    ]);

$photoBeforeName = null;
$photoAfterName  = null;

    $destinationPath = '/home/abimany3/public_html/evidence';

    // Upload file sebelum
if ($request->hasFile('evidence_before')) {
    $beforeFile = $request->file('evidence_before');
    $beforeName = time() . '_before_' . $beforeFile->getClientOriginalName();
    $beforeFile->move($destinationPath, $beforeName);

    // Hanya simpan nama file (tidak termasuk folder)
    $photoBeforeName = $beforeName;
}

// Upload file sesudah
if ($request->hasFile('evidence_after')) {
    $afterFile = $request->file('evidence_after');
    $afterName = time() . '_after_' . $afterFile->getClientOriginalName();
    $afterFile->move($destinationPath, $afterName);

    // Hanya simpan nama file (tidak termasuk folder)
    $photoAfterName = $afterName;
}

    // Update data ticket
    $ticket->update([
        'assigned_by'      => $request->assigned_by,
        'work_start'    => $request->work_start,
        'work_end'      => $request->work_end,
        'note_done'          => $request->note_done,
        'evidence_before'  => $photoBeforeName,
        'evidence_after'   => $photoAfterName,
        'status'        => 'Done',
        'done_by' => auth()->id(),
        'done_at'       => now(),
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'Pekerjaan berhasil diselesaikan!',
        'request_number' => $ticket->request_number
    ]);
}

    public function close(Request $request)
    {
        $request->validate([
            'request_id'        => 'required|exists:issue_trackers,id',
            'work_verification' => 'required|in:Sesuai,Tidak Sesuai',
            'confirmation'      => 'required|in:0,1',
            'rating'            => 'required|numeric|max:5',
            'feedback'          => 'nullable|string',
        ]);

        $ticket = IssueTracker::findOrFail($request->request_id);

        // Optional: hanya user yang authorized bisa menutup
        if ($ticket->status !== 'Done') {
            return response()->json(['error' => 'Ticket belum Done'], 403);
        }

        // Update data Closed / Verifikasi
        $ticket->update([
            'work_verification'   => $request->work_verification,
            'confirmation'=> $request->confirmation,
            'rating'              => $request->rating,
            'feedback'            => $request->feedback,
            'closed_by'           => Auth::id(),
            'closed_at'           => now(),
            'status'              => 'Closed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil ditutup dan diverifikasi!',
            'request_number' => $ticket->request_number,
        ]);
    }

public function show($id)
{
     $request = IssueTracker::with('materials')->findOrFail($id);
    return view('it.detail_issue_tracker', compact('request'));
}

public function destroy($id)
{
    $issue = IssueTracker::findOrFail($id);
    $issue->delete();

    return response()->json([
        'success' => true,
        'message' => 'Request berhasil dihapus.'
    ]);
}

public function dailyReport()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ====== Logo di kiri (A1 sampai C3) ======
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Company Logo');
    $drawing->setPath(public_path('img/logo-2.jpg'));
    $drawing->setCoordinates('A4');  // Posisi kiri atas logo
    $drawing->setHeight(60);
    $sheet->mergeCells('A1:A10');
    // Geser logo ke kanan 20px dan ke bawah 5px
$drawing->setOffsetX(20);  // ke kanan
$drawing->setOffsetY(5);
    $drawing->setWorksheet($sheet);

    // ====== Judul di tengah (D1:M1) ======
    $sheet->mergeCells('B1:I6');
    $sheet->setCellValue('B1', 'IT DAILY REPORT ACTIVITY');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('B1')->getAlignment()
    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // ====== Tanggal di bawah judul (D2:M2) ======
    $sheet->mergeCells('B7:I10');
    $sheet->setCellValue('B7', 'Periode: ' . date('d-m-Y'));
    $sheet->getStyle('B7')->getFont()->setSize(11);
    $sheet->getStyle('B7')->getAlignment()
    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);;

    // Merge dulu cell untuk header tanda tangan
$sheet->mergeCells('J1:J2');
$sheet->mergeCells('K1:K2');
$sheet->mergeCells('L1:L2');

// Set value hanya di sel paling kiri/atas dari merge
$sheet->setCellValue('J1', 'Dibuat');
$sheet->setCellValue('K1', 'Diperiksa');
$sheet->setCellValue('L1', 'Diketahui');


// Kosong untuk tanda tangan (baris 2)
$sheet->mergeCells('J3:J8');
$sheet->mergeCells('K3:K8');
$sheet->mergeCells('L3:L8');
$sheet->mergeCells('M1:P10');

$lastRow = 100; // misalnya sampai baris 100
for ($row = 5; $row <= $lastRow; $row++) {
    $sheet->mergeCells("M{$row}:P{$row}");
}

// Merge cell dulu
$sheet->mergeCells('J9:J10');
$sheet->mergeCells('K9:K10');
$sheet->mergeCells('L9:L10');

// Set value hanya di sel pertama dari merge
$sheet->setCellValue('J9', auth()->user()->name ?? 'Pembuat');
$sheet->setCellValue('K9', 'Joko Sriyanto');
$sheet->setCellValue('L9', 'Budi Mulyadi');





foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

    // ====== Header tabel mulai baris 5 ======
    $sheet->fromArray(['Ticket Number', 'Subject', 'Status', 'Priority',  'Department', 'Request By', 'Request At', 'Approved At', 'Assign By', 'Assign At', 'Done At', 'Closed At', 'Evidence'], NULL, 'A11');
    $sheet->getStyle('A11:M11')->getFont()->setBold(true);
    // Background warna header (misal: biru muda)
$sheet->getStyle('A11:M11')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFB6D7A8'); // format ARGB, FF di depan wajib
    // ====== Ambil data dari database ======
   $authId = auth()->id(); // Ambil ID user yang login
   $dateNow = now()->format('Y-m-d'); // format: 2025-08-29

$tickets = Ticket::with(['requestor.departments', 'process', 'evidences'])
    ->select(
        'id', 'ticket_number', 'title', 'status', 'priority', 
        'created_at', 'request_by', 'approved_at', 
        'processed_by', 'processed_at', 'done_at', 'closed_at'
    )
     ->where('processed_by', $authId) // filter user yang login
    ->where(function($q) {
        $q->whereDate('created_at', now()->toDateString())   // tiket dibuat hari ini
          ->orWhereNull('closed_at')                         // tiket belum ditutup
          ->orWhereDate('closed_at', now()->toDateString());  // tiket ditutup hari ini
    })
    // ->where('processed_by', auth()->id()) // kalau mau filter processed_by
    ->get();

    // Hitung status
$statusCount = [
    'Pending' => 0,
    'Work in Progress' => 0,
    'Done' => 0,
    'Closed' => 0,
];
foreach ($tickets as $t) {
    if (isset($statusCount[$t->status])) {
        $statusCount[$t->status]++;
    }
}


// Masukkan data status di kolom Q untuk chart
$sheet->setCellValue('Z1', 'Status');
$sheet->setCellValue('Y1', 'Jumlah');

$rowStatus = 2;
foreach ($statusCount as $status => $jumlah) {
    $sheet->setCellValue("Y{$rowStatus}", "{$status}: {$jumlah}"); // gabungkan label + jumlah
    $sheet->setCellValue("Z{$rowStatus}", $jumlah);                 // angka untuk chart
    $rowStatus++;
}

$xAxisTickValues = [
    new DataSeriesValues('String', 'Worksheet!$Y$2:$Y$' . ($rowStatus-1), null, ($rowStatus-2))
];
$dataSeriesValues = [
    new DataSeriesValues('Number', 'Worksheet!$Z$2:$Z$' . ($rowStatus-1), null, ($rowStatus-2))
];


$series = new DataSeries(
    DataSeries::TYPE_PIECHART,
    null,
    range(0, count($dataSeriesValues)-1),
    [], // kosongkan judul series
    $xAxisTickValues,
    $dataSeriesValues
); 

// Plot area & legend
$plotArea = new PlotArea(null, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// Chart tanpa judul
$chart = new Chart(
    'chart1',
    null,      // judul dihilangkan
    $legend,
    $plotArea,
    true,
    0,
    null,
    null
);

// Atur posisi chart lebih kecil
$chart->setTopLeftPosition('M1');
$chart->setBottomRightPosition('Q10'); // lebih kecil, 2 kolom x 4 baris


$sheet->addChart($chart);


    $row = 12;
    foreach ($tickets as $t) {
        $sheet->fromArray([$t->ticket_number, $t->title, $t->status, $t->priority, $t->requestor->departments->first()->name ?? '-', $t->requestor->name, $t->created_at, $t->approved_at,  $t->process?->name ?? '-', $t->processed_at, $t->done_at, $t->closed_at], NULL, 'A' . $row);
        // Ambil hanya 1 evidence (misal evidence pertama)
    // Ambil hanya 1 evidence (misal evidence pertama)
$evidence = $t->evidences->first();

if ($evidence) {
    $imagePath = public_path('storage/' . $evidence->path);

    if (file_exists($imagePath)) {
        // Tentukan ukuran gambar
        $imageHeight = 200;
        $imageWidth = 250;

        // Masukkan gambar
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath($imagePath);
        $drawing->setHeight($imageHeight);
        $drawing->setWidth($imageWidth);
        $drawing->setCoordinates('M' . $row);

        // Hitung lebar kolom & tinggi baris (Excel -> pixel)
        $columnWidth = $sheet->getColumnDimension('M')->getWidth() * 7; // 1 width = ~7px
        $rowHeight = $sheet->getRowDimension($row)->getRowHeight(); 
        if ($rowHeight == -1) {
            $rowHeight = $imageHeight; // default row height = image height
        }

        // Offset supaya gambar center
        $offsetX = max(0, ($columnWidth - $imageWidth) / 2);
        $offsetY = max(0, ($rowHeight - $imageHeight) / 2);

        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY($offsetY);

        $drawing->setWorksheet($sheet);

        // Atur tinggi baris & lebar kolom agar sesuai
        $sheet->getRowDimension($row)->setRowHeight($imageHeight); // konversi pixel → point
        $sheet->getColumnDimension('M')->setWidth($imageWidth * 0.14);

        // Align sel supaya konten (jika ada) center
        $sheet->getStyle('M' . $row)->getAlignment()->setHorizontal('center')->setVertical('center');
    }
}




        $row++;
    }

    // Style alignment tengah semua kolom tanda tangan
$sheet->getStyle('J1:L10')->getAlignment()->setHorizontal('center')->setVertical('center');
$sheet->getStyle('J1:L1')->getFont()->setBold(true);
$sheet->getStyle('A6:L100')->getAlignment()->setVertical('center');

$startRow = 1; // baris paling atas yang ingin diborder
$endColumn = 'P'; 

$dataStartRow = 12; // baris awal data tiket
$dataCount = count($tickets); // jumlah tiket yang sebenarnya
$lastRow = $dataStartRow + max($dataCount - 1, 0); // terakhir sesuai data

$sheet->getStyle("A{$startRow}:{$endColumn}{$lastRow}")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
]);


    // ====== Output Excel ======
    $assign = $tickets->first()?->process?->name ?? $authId;
    $filename = "Daily Report Activity IT_{$assign}_{$dateNow}.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->setIncludeCharts(true); // penting untuk chart
    $writer->save('php://output');
    exit;
}

 public function destroy_attachment($id)
    {
        $attachment = TicketAttachment::findOrFail($id);

        // Hapus file dari storage
        if (Storage::exists('public/'.$attachment->path)) {
            Storage::delete('public/'.$attachment->path);
        }

        // Hapus record dari DB
        $attachment->delete();

        return response()->json(['success' => true]);
    }

public function monthlyReport(Request $request)
{
    // Filter bulan & tahun
    $month = $request->get('month', now()->month);
    $year  = $request->get('year', now()->year);

    // Ambil semua issue bulan & tahun ini
    $issues = IssueTracker::with('materials')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

    // Hitung jumlah issue yang sudah Closed
    $closedCount = $issues->where('status', 'Closed')->count();

    // Hitung total biaya dari materials
    $totalCost = $issues->flatMap(fn($issue) => $issue->materials)->sum('subtotal');

    // Kategori kerusakan paling banyak
    $topCategory = $issues->groupBy('request_type')
                          ->map(fn($g) => $g->count())
                          ->sortDesc()
                          ->keys()
                          ->first();

    // Dropdown filter bulan & tahun
    $months = collect(range(1,12))->mapWithKeys(fn($m) => [$m => Carbon::create()->month($m)->format('F')]);
    $years  = IssueTracker::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');

    return view('reports.monthly', compact(
        'closedCount',
        'totalCost',
        'topCategory',
        'month',
        'year',
        'months',
        'years'
    ));
}


}

