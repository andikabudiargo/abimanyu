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
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Category;
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

class TicketController extends Controller
{
     public function index() {
    $departments = Department::orderBy('name')->get(); // ambil semua department
    $totalTickets = Ticket::count();
    $openTickets = Ticket::where('status', 'Pending')->count();
    $closedTickets = Ticket::where('status', 'Closed')->count();
     $approvedTickets = Ticket::where('status', 'Approved')->count();
     $totalThisMonth = Ticket::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

$completedThisMonth = Ticket::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->where('status', 'Closed')
    ->count();

$completionPercentage = $totalThisMonth > 0 
    ? round(($completedThisMonth / $totalThisMonth) * 100, 1) 
    : 0;
     $wipTickets = Ticket::where('status', 'Work in Progress')->count();
    $overdueTickets = Ticket::where('status', '!=', 'Closed')
   
    ->where('due_date', '<', now())->count();
    $topCategories = DB::table('tickets')
    ->join('categories', 'tickets.category_id', '=', 'categories.id')
    ->select('categories.description as category', DB::raw('COUNT(tickets.id) as total'))
    ->groupBy('categories.description')
    ->orderByDesc('total')
    ->limit(5)
    ->get();
$deptTickets = DB::table('tickets')
    ->leftJoin('users', 'tickets.request_by', '=', 'users.id')
    ->leftJoinSub(function($query) {
        $query->from('department_user')
            ->join('departments', 'department_user.department_id', '=', 'departments.id')
            ->select('department_user.user_id', DB::raw('MIN(departments.name) as department_name'))
            ->groupBy('department_user.user_id');
    }, 'user_dept', 'users.id', '=', 'user_dept.user_id')
    ->selectRaw('COALESCE(user_dept.department_name, "Unknown") as department_name, COUNT(DISTINCT tickets.id) as total')
    ->groupBy('user_dept.department_name')
    ->get();
    $processedTickets = Ticket::with('process')
    ->get()
    ->groupBy(fn($ticket) => $ticket->process->name ?? 'Not Assign')  // group berdasarkan nama user
    ->map(function($tickets, $name) {
        return [
            'processed_name' => $name,
            'total' => count($tickets),
        ];
    })
    ->values();
$assignTickets = DB::table('tickets')
    ->join('categories', 'tickets.category_id', '=', 'categories.id')
    ->join('departments', 'categories.department_id', '=', 'departments.id')
    ->select('departments.name as department_name', DB::raw('COUNT(tickets.id) as total'))
    ->groupBy('departments.name')
    ->orderBy('departments.name')
    ->get();






        return view('it.ticket', compact(
            'departments',
        'totalTickets',
        'openTickets',
        'closedTickets',
        'overdueTickets',
        'topCategories',
        'deptTickets',
        'processedTickets',
        'approvedTickets',
        'wipTickets',
        'totalThisMonth',
        'completedThisMonth',
        'completionPercentage',
        'assignTickets'




    ));
    }

    public function data(Request $request)
{
     $user = auth()->user();
     // Ambil semua nama departemen user dari relasi pivot
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name')->toArray(); // asumsi relasi `roles` tersedia

  $query = Ticket::with(['requestor.departments', 'requestor.roles', 'approved', 'process', 'category', 'category.department'])
    ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Work in Progress', 'On Hold', 'Done', 'Closed', 'Rejected')")
    ->orderByRaw("FIELD(priority, 'Critical', 'Urgent', 'Medium', 'Low')")
    ->orderBy('created_at', 'desc');


    if ($request->ticket_number) {
        $query->where('ticket_number', 'like', '%' . $request->ticket_number . '%');
    }

    if ($request->filled('status')) {
    $query->where('tickets.status', $request->status);
}


   if ($request->filled('processed_by')) {
    $query->whereHas('process', function ($q) use ($request) {
        $q->where('name', $request->processed_by);
    });
}



    if ($request->category) {
    $query->where('category_id', $request->category);
}


   if ($request->date) {
    $dates = explode(' to ', $request->date);

    if (count($dates) === 2) {
        $start = Carbon::parse($dates[0])->startOfDay();
        $end   = Carbon::parse($dates[1])->endOfDay();
        $query->whereBetween('created_at', [$start, $end]);
    } else {
        // Satu tanggal saja
        $start = Carbon::parse($dates[0])->startOfDay();
        $end   = Carbon::parse($dates[0])->endOfDay();
        $query->whereBetween('created_at', [$start, $end]);
    }
}

    if ($request->department) {
        $query->whereHas('requestor.departments', function ($q) use ($request) {
            $q->where('name', $request->department);
        });
    }
if (!in_array('Superuser', $userRoles)) {
    $query->where(function ($q) use ($userDepartments, $user) {
        // Tiket yang kategori-nya sesuai dengan departemen user
        $q->whereHas('category.department', function ($dept) use ($userDepartments) {
            $dept->whereIn('name', $userDepartments);
        })
        // ATAU tiket yang dibuat oleh user itu sendiri
        ->orWhere('request_by', $user->id);
    });
}


    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->request_by === Auth::id();
    $detail_url = route('it.ticket.show', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $edit_url = route('it.ticket.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $ticketNumber = $row->ticket_number ?? 'Unknown';


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
    if (!$userRoles->contains('IT Special Access')) {
        $actionButtons .= '
            <a href="' . $edit_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete(' . $row->id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>';
    } else {
        // IT Special Access (owner) hanya bisa edit, tanpa delete di Pending
        $actionButtons .= '
            <a href="' . $edit_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>';
    }
}
   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Pending' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Supervisor Special Access', 'Manager Special Access']);
    }) &&
    $userDepartments->contains('Information & Technology')
) {

        $actionButtons .= '
        <button onclick="approveTicket(' . $id . ', \'' . $ticketNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
        </button>
        <button onclick="rejectTicket(' . $id . ', \'' . $ticketNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

   $categoryDeptName = $row->category->department->name ?? null;

if (
    $row->status === 'Approved' &&
    ($userRoles->contains('IT Special Access') || $userRoles->contains('Supervisor Special Access')) &&
    $categoryDeptName &&
    $userDepartments->contains($categoryDeptName)
) {
    $actionButtons .= '
    <button type="button" onclick="openProcessModal(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-yellow-100 text-yellow-700">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline mr-2"></i> Process
    </button>';
}

// Tombol Delete khusus IT Special Access jika status bukan Closed
if (
    $row->status !== 'Closed' &&
    $userRoles->contains('IT Special Access')
) {
    $actionButtons .= '
    <button type="button" onclick="confirmDelete(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i> Delete
    </button>';
}


if (
    $row->status === 'Work in Progress' &&
    $row->processed_by === Auth::id()// hanya user yang memproses
) {
    $actionButtons .= '
      <button onclick="openHoldModal(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-orange-100 text-orange-700">
            <i data-feather="alert-circle" class="w-4 h-4 inline mr-2"></i>Hold Ticket
        </button>
        <button onclick="showDoneModal(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Mark as Done
        </button>';
}

if (
    $row->status === 'On Hold' &&
    $row->processed_by === Auth::id()// hanya user yang memproses
) {
   $actionButtons .= '
  <button onclick="resumeTicket(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-teal-100 text-teal-700">
        <i data-feather="play" class="w-4 h-4 inline mr-2"></i>Resume Ticket
    </button>';

}

if (
    $row->status === 'Done' &&
    $row->request_by === Auth::id()// hanya user yang memproses
) {
   $actionButtons .= '
 <button onclick="showCloseModal(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
    <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Close Ticket
</button>
';

}

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('request_by', function ($row) {
    return $row->requestor ? $row->requestor->name : '-';
})
->addColumn('department', function ($row) {
    return $row->requestor && $row->requestor->departments->first()
        ? $row->requestor->departments->first()->name
        : '-';
})
->addColumn('approved_by', function ($row) {
    return $row->approved ? $row->approved->name : '-';
})
->addColumn('processed_by', function ($row) {
    return $row->process ? $row->process->name : '-';
})
 ->addColumn('category', function ($row) {
    return $row->category ? $row->category->description : '-';
})


->editColumn('status', function ($row) {
    if ($row->status === 'On Hold') {
        $lastHold = $row->holds()->latest('start_at')->first();
        $reason = $lastHold ? $lastHold->reason : 'No reason';
        $startAt = $lastHold
    ? Carbon::parse($lastHold->start_at)->format('d-m-Y H:i')
    : '-';

$duration = '-';
if ($lastHold) {
    $start = Carbon::parse($lastHold->start_at);
    $diff = $start->diff(now());

    $interval = CarbonInterval::days($diff->d)
        ->hours($diff->h)
        ->minutes($diff->i)
        ->seconds($diff->s);

   $duration = $interval->forHumans();

}
  return '<span class="bg-yellow-500 inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl hover:bg-yellow-700"
                      onclick="showHoldReason(`'.e($reason).'`, `'.$startAt.'`, `'.$duration.'`)">
                    On Hold <i data-feather="alert-circle" class="w-4 h-4 inline align-middle ml-1"></i>
                </span>';
    }

    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

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
->editColumn('priority', function ($row) {
    $commonClasses = 'inline-block w-24 text-center text-xs font-semibold p-1 rounded-lg';

    if ($row->priority === 'Low') {
        return '<span class="text-green-600 ' . $commonClasses . '">Low</span>';
    } elseif ($row->priority === 'Medium') {
        return '<span class="text-blue-600 ' . $commonClasses . '">Medium</span>';
    } elseif ($row->priority === 'Urgent') {
        return '<span class="text-yellow-600 ' . $commonClasses . '">Urgent</span>';
    } elseif ($row->priority === 'Critical') {
        return '<span class="text-red-600 ' . $commonClasses . '">Critical</span>';
    } elseif ($row->priority === 'Under Review') {
        return '<span class="text-gray-600 ' . $commonClasses . '">Under Review</span>';
    }

    return '<span class="text-gray-500 ' . $commonClasses . '">Under Review</span>';
})

        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action', 'category', 'status', 'priority'])
        ->make(true);
}

     public function create()
    {
        return view('it.create-ticket');
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|string|max:255',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'attachments.*' => 'nullable|file|mimes:jpg,png,pdf,xlsx,doc,docx|max:5048', // 2MB per file
    ]);

    $ticketNumber = $this->generateTicketNumber($request->category_id);

    // Simpan ticket dulu
    $ticket = Ticket::create([
        'ticket_number' => $ticketNumber,
        'category_id' => $request->category_id,
        'title' => $request->title,
        'description' => $request->description,
        'status' => 'Pending',
        'request_by' => Auth::id(),
        'created_at' => now(),
    ]);

   if ($request->hasFile('attachments')) {
    foreach ($request->file('attachments') as $file) {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $storagePath = 'tickets';
        $fullName = $originalName . '.' . $extension;
        $i = 1;

        // Cek apakah file sudah ada di storage
        while (Storage::disk('public')->exists($storagePath . '/' . $fullName)) {
            $fullName = $originalName . '_' . $i . '.' . $extension;
            $i++;
        }

        // Simpan file
        $path = $file->storeAs($storagePath, $fullName, 'public');

        // Simpan ke database
        DB::table('ticket_attachments')->insert([
            'ticket_id' => $ticket->id,
            'path' => $path,
            'created_at' => now(),
        ]);
    }
}

    return response()->json([
        'success' => true,
        'ticket_number' => $ticketNumber,
        'message' => 'Ticket successfully submitted.'
    ]);
}

public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('it.edit-ticket', compact('ticket'));
    }

    public function update(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);

    $request->validate([
        'category_id'   => 'required|integer',
        'title'         => 'required|string|max:255',
        'description'   => 'nullable|string',
        'attachments.*' => 'file|mimes:jpg,png,pdf,doc,docx,xlsx|max:2048',
    ]);

    // Update data ticket
    $ticket->update([
        'category_id' => $request->category_id,
        'title'       => $request->title,
        'description' => $request->description,
    ]);

    // Jika ada file baru diupload
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('tickets', 'public');

            // Simpan ke tabel ticket_attachment
            \App\Models\TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'path'      => $path,
            ]);
        }
    }

    return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
}



public function getLocations()
{
    $warehouses = Warehouse::select('id', 'name')->get()->map(function ($w) {
        return [
            'id' => 'W-' . $w->id,  // prefix W-
            'text' => $w->name
        ];
    });

    $suppliers = Supplier::select('id', 'name')->get()->map(function ($s) {
        return [
            'id' => 'S-' . $s->id,  // prefix S-
            'text' => $s->name
        ];
    });

    // gabungkan keduanya
    $locations = $warehouses->merge($suppliers);

    return response()->json($locations);
}

protected function generateTicketNumber($categoryId)
{
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];

    $now = now();
    $bulan = $bulanRomawi[(int)$now->format('m')];
    $tahun = $now->format('Y');

    // Ambil department dari kategori
    $category = \App\Models\Category::with('department')->find($categoryId);
    $deptName = $category?->department?->name ?? 'GEN';

    // Mapping prefix berdasarkan nama departemen
    $prefix = match ($deptName) {
        'Maintenance' => 'MTC-ASN',
        'Information & Technology' => 'IT-ASN',
        default => 'GEN-ASN',
    };

    // Hitung nomor urut terakhir berdasarkan prefix + bulan + tahun
    $prefixFull = "$prefix-$bulan-$tahun";
    $lastTicket = \App\Models\Ticket::where('ticket_number', 'like', "$prefixFull-%")
        ->orderBy('ticket_number', 'desc')
        ->first();

    if ($lastTicket) {
        $lastNumber = (int)substr($lastTicket->ticket_number, -4);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $nextNumber = '0001';
    }

    return "$prefixFull-$nextNumber";
}

public function approve($id)
{
    $ticket = Ticket::findOrFail($id);
    $ticket->status = 'Approved';
    $ticket->approved_by = auth()->id();
    $ticket->approved_at = now();
    $ticket->save();

    
   $emails = [
    'it@asnusantara.co.id',
    'it2@asnusantara.co.id'
];

Mail::to($emails)->send(new TicketProcessMail($ticket));


    return response()->json([
        'success' => true,
        'message' => 'Ticket Approved.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $ticket = Ticket::findOrFail($id);
    $ticket->status = 'Rejected';
    $ticket->rejected_reason = $request->input('rejected_reason', 'No reason provided.');
    $ticket->reject_by = auth()->id();
    $ticket->reject_at = now();
    $ticket->save();

     return response()->json([
        'success' => true,
        'message' => 'Ticket rejected successfully.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

public function process(Request $request, $id)
{
    $request->validate([
        'due_date' => 'required|date|after_or_equal:today',
        'priority' => 'required|string|max:100',
    ]);

    $ticket = Ticket::findOrFail($id);
    $ticket->due_date = $request->due_date;
    $ticket->priority = $request->priority;
    $ticket->status = 'Work in Progress';
    $ticket->processed_by = auth()->id(); // jika ingin mencatat siapa yang proses
    $ticket->processed_at = now();
    $ticket->save();

    return response()->json([
        'success' => true,
        'message' => 'Ticket processed and marked as In Progress.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

public function hold(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);

    $request->validate([
        'hold_reason' => 'required|string',
        'custom_hold_reason' => 'nullable|string',
    ]);

    $reason = $request->hold_reason === 'Other' ? $request->custom_hold_reason : $request->hold_reason;

    // simpan riwayat hold
    $ticket->holds()->create([
        'reason' => $reason,
        'description' => $request->custom_hold_reason ?? null,
        'start_at' => now(),
        'created_by' => auth()->id(),
    ]);

    $ticket->status = 'On Hold';
    $ticket->save();

    return response()->json([
        'success' => true,
        'message' => 'Ticket status set to Waiting.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

public function resume(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);

    if ($ticket->status !== 'On Hold') {
        return response()->json(['success' => false, 'message' => 'Ticket is not on hold.']);
    }

    if ($ticket->processed_by !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'You are not authorized to resume this ticket.']);
    }

    $lastHold = $ticket->holds()->latest('start_at')->first();
    if ($lastHold && !$lastHold->end_at) {
        $lastHold->end_at = now();
        $lastHold->save();
    }

    $ticket->status = 'Work in Progress';
    $ticket->save();

     return response()->json([
        'success' => true,
        'message' => 'Ticket processed and marked as In Progress.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

  // IT mengubah status menjadi Done (dengan CA/PA & Evidence)
    public function done(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);

    if ($ticket->processed_by !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'corrective_action' => 'nullable|string',
        'evidence' => 'nullable|array', // Bisa banyak file
        'evidence.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,xlsx,zip|max:5120'
    ]);

    $ticket->corrective_action = $request->corrective_action;
    $ticket->done_at = now();
    $ticket->status = 'Done';
    $ticket->save();

    // Simpan semua evidence ke tabel ticket_evidences
    if ($request->hasFile('evidence')) {
        foreach ($request->file('evidence') as $file) {
            $filename = $file->store('evidence', 'public');

            TicketEvidence::create([
                'ticket_id' => $ticket->id,
                'path'      => $filename
            ]);
        }
    }

     if (!empty($ticket->requestor->email)) {
    Mail::to($ticket->requestor->email)
        ->send(new TicketDoneMail($ticket));
}

    return response()->json([
        'success' => true,
        'message' => 'Ticket marked as Done.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

   public function close(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);

    if ($ticket->request_by !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

      $request->validate([
            'feedback' => 'nullable|string',
        ]);

    $ticket->feedback = $request->feedback;
    $ticket->status = 'Closed';
    $ticket->closed_at = now();
    $ticket->save();

     return response()->json([
        'success' => true,
        'message' => 'Ticket closed succesfully.',
        'ticket_number' => $ticket->ticket_number
    ]);
}

public function show($id)
{
    $ticket = Ticket::with(['requestor', 'approved', 'process', 'holds', 'attachments'])->findOrFail($id);
$user = auth()->user();
    $statusOptions = [];

    // Hanya user tertentu yang bisa ubah status
    if ($user->id === $ticket->request_by || $user->id === $ticket->approved_by || $user->id === $ticket->processed_by) {
        // Status awal -> bisa diubah ke:
       switch ($ticket->status) {
    case 'Pending':
        $statusOptions = ['Approved', 'Rejected'];
        break;
    case 'Approved':
        $statusOptions = ['Process'];
        break;
    case 'Work in Progress':
        $statusOptions = ['Hold', 'Done'];
        break;
    case 'On Hold':
        $statusOptions = ['Work in Progress'];
        break;
    case 'Done':
        $statusOptions = ['Closed'];
        break;
}

    }
    return view('it.detail-ticket', compact('ticket','statusOptions'));
}

public function destroy($id)
{
    $ticket = Ticket::findOrFail($id);
    $ticket->delete();

    return response()->json([
        'success' => true,
        'message' => 'Ticket berhasil dihapus.'
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


}

