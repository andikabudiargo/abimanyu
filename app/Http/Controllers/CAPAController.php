<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\CAPA;
use App\Models\CAPAAuditor;
use App\Models\CAPAAction;
use App\Models\CAPACommentary;
use App\Models\CAPAEvidence;
use App\Models\Department;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Carbon\Carbon;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;

use function Symfony\Component\Clock\now;

class CAPAController extends Controller
{

private function resolveDepartmentGroup($deptId)
{
    $groups = [
        'HRGAIT' => [2,3,5],
    ];

    // Kalau string HRGAIT
    if (array_key_exists($deptId, $groups)) {
        return $groups[$deptId];
    }

    // Kalau numeric dan termasuk dalam group
    foreach ($groups as $group) {
        if (in_array($deptId, $group)) {
            return $group;
        }
    }

    return [$deptId];
}


public function userList($id)
{
    $deptIds = $this->resolveDepartmentGroup($id);

    $users = User::whereHas('departments', function ($q) use ($deptIds) {
            $q->whereIn('departments.id', $deptIds);
        })
        ->whereDoesntHave('roles', function ($q) {
            $q->where('roles.id', 9); // exclude Operator
        })
        ->where('status', '!=', 0) // ⛔ exclude status 0
        ->select('users.id', 'users.name')
        ->get();

    return response()->json($users);
}



    public function index()
{
    $departments = Department::orderBy('name')->get();
    $users       = User::orderBy('name')->get();

    // =========================
    // COUNT PER STATUS
    // =========================
    $total = CAPA::count();

    $draft = CAPA::where('status', 'Draft')->count();

    $posted = CAPA::where('status', 'Posted')->count();

    $verified = CAPA::where('status', 'Verified')->count();

    $inProgress = CAPA::where('status', 'In Progress')->count();

    $submitted = CAPA::where('status', 'Submitted')->count();

    $authorized = CAPA::where('status', 'Authorized')->count();

    $closed = CAPA::where('status', 'Closed')->count();


    // =========================
    // OVERDUE (PA)
    // =========================

    $today = Carbon::today();

  $overdue = CAPAAction::where('type', 'PA')
    ->whereDate('due_date', '<', $today)
    ->whereHas('capa', function ($q) {
        $q->where('status', 'In Progress');
    })
    ->count();



        $overdueList = CAPAAction::with(['capa.representative'])
    ->where('type', 'PA')
    ->whereDate('due_date', '<', $today)
    ->whereHas('capa', function ($q) {
        $q->where('status', 'In Progress');
    })
    ->orderBy('due_date', 'asc')
    ->get();


    return view('mr.capa', compact(
        'departments',
        'users',
        'draft',
        'total',
        'posted',
        'verified',
        'inProgress',
        'submitted',
        'authorized',
        'overdueList',
        'closed',
        'overdue'
    ));
}

private function formatTanggalIndonesia($date): string
{
    if (!$date) return '-';
    
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $carbon = \Carbon\Carbon::parse($date);
    return $carbon->day . ' ' . $bulan[$carbon->month] . ' ' . $carbon->year;
}

    public function data(Request $request)
{

  $query = CAPA::with(['user', 'departemen', 'representative', 'auditors.users','postedBy','verifiedBy','processedBy','submittedBy','returnedBy','authorizedBy','approvedBy','ca', 'pa','rca'])
    ->orderBy('created_at', 'desc');

  $currentUserId = Auth::id();
$tab = $request->tab ?? 'auditor';

/**
 * Cek apakah user adalah MR
 */
$isMRCapa = Auth::user()
    ->departments() // sesuaikan dengan relasi kamu
    ->where('name', 'Management Representative')
    ->exists();


if (!$isMRCapa) {

    // Kalau BUKAN MR → baru difilter
    if ($tab === 'auditor') {

        $query->whereHas('auditors', function ($q) use ($currentUserId) {
            $q->where('user_id', $currentUserId);
        });

    } else {

      $userDeptIds = Auth::user()
    ->departments()
    ->pluck('departments.id')
    ->toArray();

// Kalau termasuk HRGAIT family → tambahkan grup HRGAIT lengkap, TANPA menghapus departemen lain
if (array_intersect($userDeptIds, [2,3,5])) {
    $userDeptIds = array_unique(array_merge($userDeptIds, [2,3,5]));
}

$query->whereIn('dept_id', $userDeptIds);


    }

}
    // Filter document_number
if ($request->capa_number) {
    $query->where('capa_number', 'like', '%' . $request->capa_number . '%');
}

// Filter status di master documents
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// Filter status di master documents
if ($request->filled('dept')) {
    $query->where('dept_id', $request->dept);
}

if ($request->filled('auditor')) {

    $query->whereHas('auditors.users', function ($q) use ($request) {
        $q->where('id', $request->auditor);
    });

}



// Filter status di master documents
if ($request->filled('category')) {
    $query->where('category', $request->category);
}

if ($request->report_date) {
        [$start, $end] = explode(' to ', $request->report_date);
        $query->whereBetween('report_date', [$start, $end]);
    }


    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $user = Auth::user();
    $userId = $user->id;
    $dropdownId = 'dropdown-' . $row->id;
    $detail_url = route('mr.capa.detail', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $edit_url = route('mr.capa.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $verified_url = route('mr.capa.verified', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $process_url = route('mr.capa.process', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $submit_url = route('mr.capa.submit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $authorized_url = route('mr.capa.authorized', ['id' => $row->id]); // ✅ Diganti $ticket jadi $ro
    $review_url = route('mr.capa.review', ['id' => $row->id]); // ✅ Diganti $ticket jadi $ro
    $pdf_url = route('mr.capa.pdf', ['id' => $row->id]); 
    $print_url = route('mr.capa.print', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $isMR = $user->departments()
    ->where('name', 'Management Representative')
    ->exists();
    $isAuditor = in_array($userId, $row->auditors->pluck('user_id')->toArray());
   $deptGroupIds = in_array($row->dept_id, [2,3,5])
    ? [2,3,5]
    : [$row->dept_id];


$isAuditee = $user->departments()
    ->whereIn('departments.id', $deptGroupIds)
    ->exists();

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
            <a href="'. $detail_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
            </a>';

   // Tombol edit + delete default untuk owner jika status Pending
if ($isAuditor && $row->status == 'Draft') {
        $actionButtons .= '
            <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete(' . $row->id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>
             <button onclick="confirmPosting(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-yellow-100 text-yellow-700">
            <i data-feather="bookmark" class="w-4 h-4 inline mr-2"></i>Posting
            </button>
            ';
}
if ($isMR && $row->status == 'Open') {
      $actionButtons .= '
      <a href="'. $verified_url .'" class="block px-4 py-2 hover:bg-blue-100 text-blue-700">
                <i data-feather="search" class="w-4 h-4 inline mr-2"></i>Verified
            </a>';
}
if ($isAuditee && in_array($row->status, ['Verified', 'Returned for Action'])) {
     $actionButtons .= '
      <a href="'. $process_url .'" class="block px-4 py-2 hover:bg-orange-100 text-orange-700">
                <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>Process
            </a>';
}
if ($isMR && $row->status == 'Verified') {
      $actionButtons .= '
         <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>';
}   
if ($isMR && $row->status == 'Draft') {
      $actionButtons .= '
         <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete(' . $row->id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>';
}     
if ($isMR && $row->status == 'In Progress') {
      $actionButtons .= '
         <a href="'. $review_url .'" class="block px-4 py-2 hover:bg-lime-100 text-lime-700">
                <i data-feather="message-circle" class="w-4 h-4 inline mr-2"></i>Review
            </a>';
}
if ($isAuditee && in_array ($row->status, ['Approved', 'Returned for Evidence'])) {
      $actionButtons .= '
         <a href="'. $submit_url .'" class="block px-4 py-2 hover:bg-purple-100 text-purple-700">
                <i data-feather="send" class="w-4 h-4 inline mr-2"></i>Submit
            </a>';
}
if ($isMR && $row->status == 'Submitted') {
      $actionButtons .= '
            <a href="'. $authorized_url .'" class="block px-4 py-2 hover:bg-green-100 text-green-700">
                <i data-feather="feather" class="w-4 h-4 inline mr-2"></i>Authorized
            </a>
            ';
}
if ($isAuditor && $row->status == 'Authorized') {
      $actionButtons .='
             <a href="'. $detail_url .'" class="block px-4 py-2 hover:bg-teal-100 text-teal-700">
                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>Closed
            </a>
            ';
}
if ($row->status == 'Closed') {
      $actionButtons .='
             <a href="'. $pdf_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="file-text" class="w-4 h-4 inline mr-2"></i>Generate PDF
            </a>
              <a href="'. $print_url .'" class="block px-4 py-2 hover:bg-gray-100" target="_blank">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>Print
            </a>
            ';
}

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('rca', fn($row) => optional($row->rca)->description ?? '-')
->addColumn('ca', fn($row) => optional($row->ca)->description ?? '-')
->addColumn('ca_due_date', function ($row) {
    return $row->ca && $row->ca->due_date
        ? $this->formatTanggalIndonesia($row->ca->due_date)
        : '-';
})
->addColumn('pa', fn($row) => optional($row->pa)->description ?? '-')
->addColumn('pa_due_date', function ($row) {
    return $row->pa && $row->pa->due_date
        ? $this->formatTanggalIndonesia($row->pa->due_date)
        : '-';
})

->addColumn('approved_at', function ($row) {
    return $row->approved_at
        ? $this->formatTanggalIndonesia($row->approved_at)
        : '-';
})

->addColumn('deadline', function ($row) {

    $today = \Carbon\Carbon::today();

    $ca = $row->ca;
    $pa = $row->pa;

    // ======================
    // JIKA CA & PA TIDAK ADA
    // ======================
    if (!$ca && !$pa) {
        return 'Due Date not set';
    }

    $labels = [];

    // ======================
    // CEK CA
    // ======================
    if ($ca) {

        $caDue = $ca->due_date ? \Carbon\Carbon::parse($ca->due_date) : null;

        if ($ca->status === 'Open' && $caDue && $caDue->lt($today)) {
            $labels[] = 'CA Overdue';
        }
    }

    // ======================
    // CEK PA
    // ======================
    if ($pa) {

        $paDue = $pa->due_date ? \Carbon\Carbon::parse($pa->due_date) : null;

        if ($pa->status === 'Open' && $paDue && $paDue->lt($today)) {
            $labels[] = 'PA Overdue';
        }
    }

    // ======================
    // JIKA ADA OVERDUE
    // ======================
    if (!empty($labels)) {
        return '<span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">
                    ' . implode(' & ', $labels) . '
                </span>';
    }

    // ======================
    // CEK NOT DUE YET
    // ======================
    $notDue = false;

    if ($ca && $ca->status === 'Open' && $ca->due_date) {
        if (\Carbon\Carbon::parse($ca->due_date)->gte($today)) {
            $notDue = true;
        }
    }

    if ($pa && $pa->status === 'Open' && $pa->due_date) {
        if (\Carbon\Carbon::parse($pa->due_date)->gte($today)) {
            $notDue = true;
        }
    }

    if ($notDue) {
        return '<span class="px-2 py-1 text-xs font-semibold text-white bg-yellow-500 rounded">
                    Not Due Yet
                </span>';
    }

    // ======================
    // JIKA SEMUA CLOSED
    // ======================
    if (
        (!$ca || $ca->status === 'Closed') &&
        (!$pa || $pa->status === 'Closed')
    ) {
        return '<span class="px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded">
                    On Time
                </span>';
    }

    return '-';
})

->addColumn('created_by', function ($row) {
    return $row->user ? $row->user->name : '-';
})

->addColumn('departemen', function ($row) {
    return $row->departemen ? $row->departemen->name : '-';
})

->addColumn('dept_representative', function ($row) {

    $html = '';

    $rep1 = $row->representative->name ?? null;
    $rep2 = $row->representative2->name ?? null;

    if ($rep1 || $rep2) {

        $html .= '<div class="flex flex-wrap gap-2 mt-1">';

        if ($rep1) {
            $html .= '
                <span class="inline-flex items-center gap-1 py-1 text-sm font-medium text-indigo-700">
                    <i class="fa fa-user text-xs"></i>
                    ' . $rep1 . '
                </span>
            ';
        }

        if ($rep2) {
            $html .= '
                <span class="inline-flex items-center gap-1 py-1 text-sm font-medium text-indigo-700">
                    <i class="fa fa-user text-xs"></i>
                    ' . $rep2 . '
                </span>
            ';
        }

        $html .= '</div>';

    } else {
        $html = '<span class="text-gray-400 italic text-xs">No dept representative assigned</span>';
    }

    return $html;
})

->addColumn('posted_by', function ($row) {
    return $row->postedBy ? $row->postedBy->name : '-';
})
->addColumn('verified_by', function ($row) {
    return $row->verifiedBy ? $row->verifiedBy->name : '-';
})
->addColumn('processed_by', function ($row) {
    return $row->processedBy ? $row->processedBy->name : '-';
})
->addColumn('review_by', function ($row) {
    return $row->reviewBy ? $row->reviewBy->name : '-';
})
->addColumn('submitted_by', function ($row) {
    return $row->submittedBy ? $row->submittedBy->name : '-';
})
->addColumn('returned_by', function ($row) {
    return $row->returnedBy ? $row->returnedBy->name : '-';
})
->addColumn('authorized_by', function ($row) {
    return $row->authorizedBy ? $row->authorizedBy->name : '-';
})
->addColumn('approved_by', function ($row) {
    return $row->approvedBy ? $row->approvedBy->name : '-';
})

->editColumn('auditors', function ($row) {
    $auditorList = '';

    if ($row->auditors && $row->auditors->count() > 0) {
        foreach ($row->auditors as $auditor) {
            $auditorName = $auditor->users->name ?? '-';
           $auditorList .= '<div class="flex flex-wrap gap-2 mt-1">';
$auditorList .= '<span class="inline-flex items-center gap-1 py-1 text-sm font-medium text-indigo-700" title="' . ($auditor->user->departments->users->name ?? '-') . '">
                    <i class="fa fa-user text-xs"></i>
                    ' . $auditorName . '
                  </span>';
$auditorList .= '</div>';

        }
    } else {
        $auditorList = '<span class="text-gray-400 italic text-xs">No auditor assigned</span>';
    }

    return $auditorList;
})

->editColumn('departemen', function ($row) {

    if (empty($row->departemen)) {
        return '<span class="italic text-xs text-gray-400">No Department</span>';
    }

    $deptId = $row->dept_id;

    // Jika termasuk HRGAIT group
    if (in_array($deptId, [2,3,5])) {
        $deptName = 'HRGAIT';
    } else {
        $deptName = $row->departemen->name;
    }

    return '
        <div class="flex items-center justify-start gap-2 text-sm text-gray-700 font-medium">
            <i class="fa-solid fa-building text-slate-500"></i>
            <span>'.e($deptName).'</span>
        </div>
    ';
})

->editColumn('report_date', function ($row) {
    if (empty($row->report_date)) {
        return '<span class="block w-full italic text-xs text-center text-gray-400">No report date added</span>';
    }

    $formatted = $this->formatTanggalIndonesia($row->report_date);

    return '
        <div class="flex items-center justify-center gap-2 text-sm text-gray-700 font-medium">
            <i class="fa-solid fa-calendar text-slate-500"></i>
            <span>' . e($formatted) . '</span>
        </div>
    ';
})


->editColumn('capa_number', function ($row) {

    // Kalau belum ada nomor
 if (empty($row->capa_number)) {
    return '<span class="block w-full italic text-xs text-center text-gray-400">Not numbered yet</span>';
}



    $commonClasses = 'block w-full text-center text-white text-xs font-medium px-2 py-1 rounded-xl break-words';

    $statusColors = [
        'draft'       => 'bg-gray-500',
        'posted'      => 'bg-yellow-500',
        'verified'    => 'bg-blue-500',
        'in progress' => 'bg-orange-500',
        'approved' => 'bg-lime-300',
        'submitted'      => 'bg-indigo-500',
        'returned for evidence' => 'bg-red-500',
        'returned for action'   => 'bg-red-500',
        'authorized'  => 'bg-green-500',
        'closed'      => 'bg-teal-500',
    ];

    $status = strtolower(trim($row->status));

    $bgClass = $statusColors[$status] ?? 'bg-slate-400';

    return '<span class="'.$bgClass.' '.$commonClasses.'">'
            . e($row->capa_number) .
           '</span>';
})


->editColumn('status', function ($row) {



    $commonClasses = 'block w-full text-center text-white text-xs font-medium px-2 py-1 rounded-xl break-words';

    $statusColors = [
        'draft'       => 'bg-gray-500',
        'posted'      => 'bg-yellow-500',
        'verified'    => 'bg-blue-500',
        'in progress' => 'bg-orange-500',
        'approved' => 'bg-lime-300',
        'submitted'      => 'bg-indigo-500',
        'returned for evidence' => 'bg-red-500',
        'returned for action'   => 'bg-red-500',
        'authorized'  => 'bg-green-500',
        'closed'      => 'bg-teal-500',
    ];

    $status = strtolower(trim($row->status));

    $bgClass = $statusColors[$status] ?? 'bg-slate-400';

    return '<span class="'.$bgClass.' '.$commonClasses.'">'
            . e($row->status) .
           '</span>';
})

->editColumn('category', function ($row) {

    $commonClasses = 'block w-full text-center text-xs font-semibold';

    if ($row->category == 'Critical') {
        return '<span class="text-red-600 ' . $commonClasses . '">Critical</span>';
    } elseif ($row->category == 'Minor') {
        return '<span class="text-blue-600 ' . $commonClasses . '">Minor</span>';
    } elseif ($row->category == 'Major') {
        return '<span class="text-yellow-600 ' . $commonClasses . '">Major</span>';
    } elseif ($row->category == 'Observation') {
        return '<span class="text-green-600 ' . $commonClasses . '">Observation</span>';
    }

    return '<span class="text-gray-500 ' . $commonClasses . '">Default</span>';
})


        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action', 'category', 'status', 'auditors', 'capa_number','departemen', 'dept_representative','status','report_date','deadline','approved_at'])
        ->make(true);
}

      public function create()
    {$departments = Department::whereNotIn('id', [2,3,5,11,15,18,20,21])
    ->orderBy('name')
    ->get();
        $users = User::orderBy('name')->get(); // ambil semua department
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('mr.create-capa',compact('departments','users'));
    }

   public function store(Request $request)
{
    // Validasi
    $validated = $request->validate([
        'source' => 'required',
        'category' => 'required',
        'dept_id' => 'required|integer',
        'dept_representative' => 'required|integer',
         'dept_representative_2' => 'nullable|integer',
        'detail_of_information' => 'required|string',
        'Problem' => 'required|string',
        'auditors' => 'required|array|min:1',
        'auditors.*' => 'exists:users,id',
    ]);

    DB::beginTransaction();

    try {

        // Simpan data CAPA
        $capa = Capa::create([
            'source_of_finding' => $request->source,
            'category' => $request->category,
            'dept_id' => $request->dept_id,
            'dept_representative' => $request->dept_representative,
            'dept_representative_2' => $request->dept_representative_2,
            'detail_of_information' => $request->detail_of_information,
            'problem' => $request->Problem,
            'created_by' => Auth::id()
        ]);

        // 🔹 Simpan auditors ke tabel relasi
        $auditorsData = collect($request->auditors)->map(fn ($userId) => [
            'capa_id' => $capa->id,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now()
        ])->toArray();

        CAPAAuditor::insert($auditorsData);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'CAPA berhasil disimpan sebagai Draft.',
            'data' => $capa
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Ajax get users by department
     */
    public function getUsersByDepartment($deptId)
    {
        $users = User::where('department_id', $deptId)
                     ->select('id', 'name')
                     ->orderBy('name')
                     ->get();

        return response()->json($users);
    }

   public function posted(Request $request, $id)
{
    $capa = CAPA::findOrFail($id);
    $capa->status = 'Open';
    $capa->posted_by = auth()->id();
    $capa->posted_at = now();
    $capa->save();

    // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

    // Ambil user MR (department_id = 8)
    $targetUsers = User::join('department_user', 'users.id', '=', 'department_user.user_id')
        ->where('department_user.department_id', 8)
        ->select('users.*')
        ->distinct()
        ->get();

    // Loop user MR
    foreach ($targetUsers as $user) {

        // Ambil subscription per user
        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            continue; // skip kalau tidak ada subscription
        }

        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "🔍 CAPA Baru Butuh Diverifikasi | Abimanyu Live",
                    'body'  => "Auditor mengajukan CAPA baru untuk diverifikasi. Klik Disini!",
                    'url'   => url("/mr/capa/{$capa->id}/verified")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();

    return response()->json([
        'success' => true,
        'message' => 'CAPA berhasil diposting!',
    ]);
}


public function verified($id)
    {
        $capa = CAPA::findOrFail($id);
        return view('mr.verified-capa', compact('capa'));
    }

public function updateVerified(Request $request, $id)
{
    try {
        $capa = CAPA::findOrFail($id);

        // Validasi hanya field yang boleh diedit
        $request->validate([
            'capa_number' => 'required|string',
            'report_date' => 'required|date',
            'category' => 'required|string',
            'detail_of_information' => 'required|string',
            'problem' => 'required|string',
        ]);

        // Update field yang boleh diubah
        $capa->update([
            'capa_number' => $request->capa_number,
            'report_date' => $request->report_date,
            'category' => $request->category,
            'detail_of_information' => $request->detail_of_information,
            'problem' => $request->problem,
            'status' => 'Verified',
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

         // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

// Ambil hanya 2 user (rep1 & rep2)
$targetUserIds = array_filter([
    $capa->dept_representative,
    $capa->dept_representative_2
]);

$targetUsers = User::whereIn('id', $targetUserIds)
    ->where('status', 1)
    ->get();

foreach ($targetUsers as $user) {

    // =========================
    // 🔥 PUSH NOTIFICATION
    // =========================
    $subscriptions = DB::table('subscriptions')
        ->where('user_id', $user->id)
        ->get();

    foreach ($subscriptions as $subRow) {

        $subData = json_decode($subRow->subscription, true);
        if (!$subData) continue;

        $sub = Subscription::create($subData);

        $webPush->sendOneNotification(
            $sub,
            json_encode([
                'title' => "⚙️ CAPA Baru butuh Diproses | Abimanyu Live",
                'body'  => "CAPA baru butuh dilengkapi actionnya. Klik disini!",
                'url'   => url("/mr/capa/{$capa->id}/process")
            ])
        );
    }

}

// Flush push
$webPush->flush();

        // Response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'CAPA berhasil diverifikasi.',
        ]);

    } catch (\Exception $e) {
        // Jika error server
        return response()->json([
            'success' => false,
            'message' => 'CAPA gagal diverifikasi: ' . $e->getMessage(),
        ], 500);
    }
}

private function generateIndexedFilename($directory, $originalName)
{
    $name = pathinfo($originalName, PATHINFO_FILENAME);
    $ext  = pathinfo($originalName, PATHINFO_EXTENSION);

    $filename = $originalName;
    $counter = 1;

    while (file_exists($directory . '/' . $filename)) {

        $filename = $name . '_' . $counter . '.' . $ext;
        $counter++;
    }

    return $filename;
}

public function review($id)
{
    $capa = CAPA::with([
        'user',               // created_by
        'departemen',
        'representative',
        'auditors.users',
        'rca',
        'ca',
         'comments.user', // 👈 penting: relasi user komentar
        'pa',
        'evidences'
    ])->findOrFail($id);

     $currentUser = [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'photo' => auth()->user()->avatar ?? 'https://via.placeholder.com/40',
    ];

    return view('mr.review-capa', compact('capa','currentUser'));
}

public function updateReview(Request $request, $id)
{
    // Ambil data CAPA
    $capa = CAPA::findOrFail($id);

    // Update status + authorized info
    $capa->status = 'Approved';
    $capa->review_by = Auth::id();
    $capa->review_at = now();

    $capa->save();

       // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

 // Ambil hanya 2 user (rep1 & rep2)
$targetUserIds = array_filter([
    $capa->dept_representative,
    $capa->dept_representative_2
]);

$targetUsers = User::whereIn('id', $targetUserIds)
    ->where('status', 1)
    ->get();

foreach ($targetUsers as $user) {

    // =========================
    // 🔥 PUSH NOTIFICATION
    // =========================
    $subscriptions = DB::table('subscriptions')
        ->where('user_id', $user->id)
        ->get();

    foreach ($subscriptions as $subRow) {

        $subData = json_decode($subRow->subscription, true);
        if (!$subData) continue;

        $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "✅ Action CAPA telah disetujui MR | Abimanyu Live",
                    'body'  => "Segera lengkapi Evidencenya dan Submit. Klik Disini!",
                    'url'   => url("/mr/capa/{$capa->id}/process")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();

    return response()->json([
        'success' => true,
        'message' => 'CAPA successfully approved.'
    ]);
}

public function submit($id)
{
  $capa = CAPA::with([
    'rca',
    'ca',
    'pa',
    'evidences',
    'comments' => function ($q) {
        $q->where('type', 'Returned for Evidence')
          ->with('user')
          ->orderBy('created_at', 'desc');
    }
])->findOrFail($id);


    $users = User::whereHas('departments', function ($q) use ($capa) {
                    $q->where('department_id', $capa->dept_id);
                })
                ->orderBy('name')
                ->get();

    return view('mr.submit-capa', compact('capa', 'users'));
}

private function generateUniqueFilenamePublic($directory, $originalName)
{
    $name = pathinfo($originalName, PATHINFO_FILENAME);
    $ext  = pathinfo($originalName, PATHINFO_EXTENSION);

    $filename = $originalName;
    $counter  = 1;

    while (file_exists($directory . '/' . $filename)) {
        $filename = $name . '(' . $counter . ').' . $ext;
        $counter++;
    }

    return $filename;
}



   public function updateSubmitted(Request $request, $id)
{
    try {

        $capa = CAPA::with('evidences')->findOrFail($id);

    // Cek apakah sudah ada evidence di DB
    $hasEvidence = $capa->evidences->count() > 0;

    $rules = [

        'ca_supporting' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

        'pa_supporting' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

        // Array-nya
        'evidence_files' => $hasEvidence
            ? 'nullable|array'
            : 'nullable|array',

        // File-nya
        'evidence_files.*' => 'image|mimes:jpg,jpeg,png|max:5120',
    ];

    $request->validate($rules);

        DB::beginTransaction();

        /* ===============================
           UPDATE STATUS
        =============================== */

        $capa->update([
            'status' => 'Submitted',
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        /* ===============================
           FOLDER CAPA DOCUMENT
        =============================== */

       $basePath = '/home/abimany3/public_html/capa_document/' . $capa->id;

        if (!file_exists($basePath)) {
            mkdir($basePath, 0755, true);
        }

        /* ===============================
           SAVE CA DOCUMENT
        =============================== */

       if ($request->hasFile('ca_supporting')) {

    $file = $request->file('ca_supporting');

    $originalName = $file->getClientOriginalName();

    // Generate nama unik tanpa timestamp
    $filename = $this->generateIndexedFilename(
        $basePath,
        $originalName
    );

    $file->move($basePath, $filename);

    DB::table('capa_actions')
        ->where('capa_id', $capa->id)
        ->where('type', 'CA')
        ->update([
            'supporting_document' => $filename,
            'updated_at' => now()
        ]);
}


        /* ===============================
           SAVE PA DOCUMENT
        =============================== */

       if ($request->hasFile('pa_supporting')) {

    $file = $request->file('pa_supporting');

    $originalName = $file->getClientOriginalName();

    // Generate nama unik tanpa timestamp
    $filename = $this->generateIndexedFilename(
        $basePath,
        $originalName
    );

    $file->move($basePath, $filename);

    DB::table('capa_actions')
        ->where('capa_id', $capa->id)
        ->where('type', 'PA')
        ->update([
            'supporting_document' => $filename,
            'updated_at' => now()
        ]);
}

        /* ===============================
           SAVE EVIDENCE
        =============================== */

        if ($request->hasFile('evidence_files')) {

          $publicDir = '/home/abimany3/public_html/evidence_capa/' . $capa->id;

            if (!file_exists($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            foreach ($request->file('evidence_files') as $file) {

                $originalName = $file->getClientOriginalName();

                $filename = $this->generateUniqueFilenamePublic(
                    $publicDir,
                    $originalName
                );

                $file->move($publicDir, $filename);

                CAPAEvidence::create([
                    'capa_id'   => $capa->id,
                    'file_name' => $filename,
                ]);
            }
        }

        DB::commit();

         // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

    // Ambil user MR (department_id = 8)
    $targetUsers = User::join('department_user', 'users.id', '=', 'department_user.user_id')
        ->where('department_user.department_id', 8)
        ->select('users.*')
        ->distinct()
        ->get();

    // Loop user MR
    foreach ($targetUsers as $user) {

        // Ambil subscription per user
        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            continue; // skip kalau tidak ada subscription
        }

        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "✅ CAPA Baru Butuh Diotorisasi | Abimanyu Live",
                    'body'  => "Auditee telah melengkapi action dan evidence. Klik Disini!",
                    'url'   => url("/mr/capa/{$capa->id}/authorized")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();

        return response()->json([
            'success' => true,
            'message' => 'CAPA successfully submitted.'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to submit CAPA: ' . $e->getMessage()
        ], 500);
    }
}


public function authorized($id)
{
    $capa = CAPA::with([
        'user',               // created_by
        'departemen',
        'representative',
        'auditors.users',
        'rca',
        'ca',
          'comments' => function ($q) {
        $q->where('type', 'Returned for Evidence')
          ->with('user')
          ->orderBy('created_at', 'desc');
    }, // 👈 penting: relasi user komentar
        'pa',
        'evidences'
    ])->findOrFail($id);

     $currentUser = [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'photo' => auth()->user()->avatar ?? 'https://via.placeholder.com/40',
    ];


    return view('mr.authorized-capa', compact('capa','currentUser'));
}

public function updateAuthorized(Request $request, $id)
{
    // Validasi input
    $validated = $request->validate([
        'new_capa_needed' => 'required|in:yes,no',
        'new_capa_reason' => 'nullable|required_if:new_capa_needed,yes|string',
        'mr_statement'    => 'required|string',
    ]);

    // Ambil data CAPA
    $capa = CAPA::findOrFail($id);

    // Update data utama
    $capa->status        = 'Authorized';
    $capa->authorized_by = Auth::id();
    $capa->authorized_at = now();

    // Simpan data dari modal
    $capa->new_capa_needed = $validated['new_capa_needed'];
    $capa->new_capa_reason = $validated['new_capa_reason'] ?? null;
    $capa->mr_statement    = $validated['mr_statement'];

    $capa->save();

    DB::table('capa_actions')
    ->where('capa_id', $capa->id)
    ->whereIn('type', ['RCA', 'CA', 'PA'])
    ->update([
        'status' => 'Closed',
        'updated_at' => now()
    ]);

    /* ============================
       Setup WebPush
    ============================ */

    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);


    // Ambil auditor
    $targetUsers = User::join('capa_auditors', 'users.id', '=', 'capa_auditors.user_id')
        ->where('capa_auditors.capa_id', $capa->id)
        ->select('users.*')
        ->distinct()
        ->get();


    foreach ($targetUsers as $user) {

        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) continue;


        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "✅ CAPA Authorized",
                    'body'  => "MR telah menyetujui CAPA. Klik untuk detail.",
                    'url'   => url("/mr/capa/{$capa->id}/detail")
                ])
            );
        }
    }

    $webPush->flush();


    return response()->json([
        'success' => true,
        'message' => 'CAPA successfully authorized.'
    ]);
}


public function getComments($capa_id)
    {
        $comments = CAPACommentary::with('user')
            ->where('capa_id', $capa_id)
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json($comments);
    }

     public function returnAction(Request $request)
    {
      $request->validate([
        'capa_id' => 'required|integer',
        'comments' => 'required|array',
        'comments.*.user_id' => 'required|integer',
        'comments.*.comment' => 'required|string',
    ]);

    // Simpan semua komentar
    $savedComments = [];
    foreach($request->comments as $c){
        $comment = CAPACommentary::create([
            'capa_id' => $request->capa_id,
            'user_id' => $c['user_id'],
            'comment' => $c['comment'],
            'type'    => 'Returned for Action' // 👈 otomatis isi type
        ]);
        $comment->load('user');
        $savedComments[] = $comment;
    }

    // Update status CAPA
    $capa = CAPA::findOrFail($request->capa_id);
    $capa->status = 'Returned for Action';
    $capa->returned_by = Auth::id();
    $capa->returned_at = now();
    $capa->save();

     // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

  $deptGroupIds = $this->resolveDepartmentGroup($capa->dept_id);


$targetUsers = User::whereHas('departments', function ($q) use ($deptGroupIds) {
        $q->whereIn('departments.id', $deptGroupIds);
    })
    ->where('status', 1)
    ->distinct()
    ->get();



    // Loop user MR
    foreach ($targetUsers as $user) {

        // Ambil subscription per user
        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            continue; // skip kalau tidak ada subscription
        }

        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "↩️ CAPA Direturn oleh MR | Abimanyu Live",
                    'body'  => "CAPA direturn untuk dilengkapi actionnya. Klik Disini untuk melihat komentar!",
                    'url'   => url("/mr/capa/{$capa->id}/process")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();


    return response()->json([
        'success' => true,
        'comments' => $savedComments
    ]);

}

public function returnEvidence(Request $request)
    {
      $request->validate([
        'capa_id' => 'required|integer',
        'revision_type' => 'required|in:accept_ca,accept_pa,reject_all',
        'comments' => 'required|array',
        'comments.*.user_id' => 'required|integer',
        'comments.*.comment' => 'required|string',
    ]);

    // Simpan semua komentar
    $savedComments = [];
    foreach($request->comments as $c){
        $comment = CAPACommentary::create([
            'capa_id' => $request->capa_id,
            'user_id' => $c['user_id'],
            'comment' => $c['comment'],
            'type'    => 'Returned for Evidence' // 👈 otomatis isi type
        ]);
        $comment->load('user');
        $savedComments[] = $comment;
    }

    // Update status CAPA
    $capa = CAPA::findOrFail($request->capa_id);
    $capa->status = 'Returned for Evidence';
    $capa->returned_by = Auth::id();
    $capa->returned_at = now();
    $capa->save();

    $revisionType = $request->revision_type;

if ($revisionType === 'accept_ca') {

    // Close Corrective Action saja
    DB::table('capa_actions')
        ->where('capa_id', $request->capa_id)
        ->where('type', 'CA')
        ->update([
            'status' => 'Closed',
            'updated_at' => now()
        ]);

}

elseif ($revisionType === 'accept_pa') {

    // Close Preventive Action saja
    DB::table('capa_actions')
        ->where('capa_id', $request->capa_id)
        ->where('type', 'PA')
        ->update([
            'status' => 'Closed',
            'updated_at' => now()
        ]);

}


     // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

   $deptGroupIds = $this->resolveDepartmentGroup($capa->dept_id);


$targetUsers = User::whereHas('departments', function ($q) use ($deptGroupIds) {
        $q->whereIn('departments.id', $deptGroupIds);
    })
    ->where('status', 1)
    ->distinct()
    ->get();



    // Loop user MR
    foreach ($targetUsers as $user) {

        // Ambil subscription per user
        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            continue; // skip kalau tidak ada subscription
        }

        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "↩️ CAPA Direturn Oleh MR | Abimanyu Live",
                    'body'  => "CAPA direturn untuk dilengkapi evidencenya. Klik Disini untuk melihat komentar!",
                    'url'   => url("/mr/capa/{$capa->id}/submit")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();


    return response()->json([
        'success' => true,
        'comments' => $savedComments
    ]);

}

public function destroyEvidence($id, $type)
{
    $evidence = CAPAEvidence::findOrFail($id);

    $capaId   = $evidence->capa_id;
    $file    = $evidence->file_name;

    // Build path absolut
   $filePath = "/home/abimany3/public_html/evidence_capa/{$capaId}/{$file}";

// Hapus file fisik
if (file_exists($filePath)) {

    unlink($filePath);

    $dirPath = "/home/abimany3/public_html/evidence_capa/{$capaId}";

    if (is_dir($dirPath) && count(scandir($dirPath)) == 2) {
        rmdir($dirPath);
    }
}


    // Hapus DB
    $evidence->delete();


    return response()->json([
        'status' => 'success',
        'message' => 'Evidence {$type} berhasil dihapus'
    ]);
}

public function updateApprove(Request $request, $id)
{
    // Ambil data CAPA
    $capa = CAPA::findOrFail($id);

    // Update status + authorized info
    $capa->status = 'Closed';
    $capa->approved_by = Auth::id();
    $capa->approved_at = now();

    $capa->save();

    return response()->json([
        'success' => true,
        'message' => 'CAPA successfully closed.'
    ]);
}

public function pdf($id, Request $request)
{
    $capa = CAPA::with([
        'user',
        'departemen',
        'representative',
        'rca',
        'ca',
        'pa',
        'comments.user',
        'evidences',
        'authorizedBy',
        'verifiedBy'
    ])->findOrFail($id);

    /* =====================
       LOGO BASE64
    ===================== */
    $logoPath = public_path('img/logo-2.jpg');
    $logo = null;

    if (file_exists($logoPath)) {
        $logo = 'data:image/jpeg;base64,' . base64_encode(
            file_get_contents($logoPath)
        );
    }

    /* =====================
       EVIDENCE BASE64
    ===================== */
    $evidenceImages = [];

    foreach ($capa->evidences as $evidence) {

        $path = '/home/abimany3/public_html/evidence_capa/'
                .$capa->id.'/'
                .$evidence->file_name;

        $realPath = realpath($path);

        if ($realPath && file_exists($realPath)) {
            $evidenceImages[] = [
                'src'  => 'file://' . $realPath,
                'name' => $evidence->file_name
            ];
        }
    }

    /* =====================
       SUPPORTING DOCUMENTS (CA & PA)
    ===================== */
    $supportingDocBasePath = '/home/abimany3/public_html/capa_document/' . $capa->id . '/';

    $supporting = $this->collectSupportingDocuments($supportingDocBasePath, [
        'ca' => $capa->ca,
        'pa' => $capa->pa,
    ]);

    $supportingPdfs        = $supporting['pdfs'];
    $supportingDocxContent = $supporting['docxContent'];
    $supportingDocs        = $supporting['fallbackLinks'];

    /* =====================
       RENDER VIEW
    ===================== */
    $html = view(
        'mr.capa-pdf',
        compact('capa', 'logo', 'evidenceImages', 'supportingDocs', 'supportingDocxContent')
    )->render();

    $mpdf = new Mpdf([
        'format' => 'A4',
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_left' => 12,
        'margin_right' => 12,
    ]);

    $mpdf->WriteHTML($html);

    $filename = 'CAPA-' . $capa->capa_number . '.pdf';

    /* =====================
       MERGE PDF SUPPORTING DOCS
    ===================== */
    $finalPdf = $this->mergePdfSupportingDocs($mpdf->Output('', 'S'), $supportingPdfs);

    if ($request->has('preview')) {
        return response($finalPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    return response($finalPdf, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}

/**
 * Kumpulkan supporting document (CA & PA) dan kelompokkan jadi:
 * - pdfs          : file pdf asli, siap di-merge
 * - docxContent   : konten docx yang sudah diextract (untuk ditampilkan via blade)
 * - fallbackLinks : format lain / docx gagal parse, ditampilkan sebagai link
 */
private function collectSupportingDocuments(string $basePath, array $actions): array
{
    $pdfs = [];
    $docxContent = [];
    $fallbackLinks = [];

    foreach ($actions as $label => $action) {

        if (!$action || !$action->supporting_document) {
            continue;
        }

        $filePath = $basePath . $action->supporting_document;

        if (!file_exists($filePath)) {
            continue;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $labelText = strtoupper($label) . ' Supporting Document';

        if ($ext === 'pdf') {
            $pdfs[] = ['label' => $labelText, 'path' => $filePath];
            continue;
        }

        if ($ext === 'docx') {
            $extracted = $this->extractDocxContent($filePath);

            if ($extracted !== null) {
                $docxContent[] = [
                    'label'   => $labelText,
                    'name'    => $action->supporting_document,
                    'content' => $extracted,
                ];
                continue;
            }
        }

        // fallback: .doc lama, atau docx gagal diparse
        $fallbackLinks[] = [
            'label' => $labelText,
            'name'  => $action->supporting_document,
            'url'   => url('capa_document/' . basename(rtrim($basePath, '/')) . '/' . $action->supporting_document),
        ];
    }

    return [
        'pdfs'          => $pdfs,
        'docxContent'   => $docxContent,
        'fallbackLinks' => $fallbackLinks,
    ];
}

/**
 * Gabungkan PDF utama dengan daftar PDF supporting (CA/PA) jadi satu file,
 * menggunakan fitur import bawaan mPDF (tidak perlu FPDI terpisah).
 */
/**
 * Gabungkan PDF utama (hasil generate mPDF) dengan daftar PDF supporting (CA/PA).
 */
private function mergePdfSupportingDocs(string $mainPdfContent, array $supportingPdfs): string
{
    if (empty($supportingPdfs)) {
        return $mainPdfContent;
    }

    $tmpMainPdf = storage_path('app/tmp_capa_merge_' . uniqid() . '.pdf');
    file_put_contents($tmpMainPdf, $mainPdfContent);

    $pdf = new \setasign\Fpdi\Fpdi();

    $this->appendPdfPages($pdf, $tmpMainPdf);

    foreach ($supportingPdfs as $doc) {
        $this->appendPdfPages($pdf, $doc['path']);
    }

    $result = $pdf->Output('S');

    @unlink($tmpMainPdf);

    return $result;
}

/**
 * Import semua halaman dari satu file PDF ke dokumen FPDI yang sedang dibangun.
 */
private function appendPdfPages(\setasign\Fpdi\Fpdi $pdf, string $sourcePath): void
{
    $pageCount = $pdf->setSourceFile($sourcePath);

    for ($i = 1; $i <= $pageCount; $i++) {
        $tplId = $pdf->importPage($i);
        $size  = $pdf->getTemplateSize($tplId);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tplId);
    }
}

/**
 * Extract teks & tabel dari file docx (PHPWord). Return null kalau gagal parse.
 */
private function extractDocxContent(string $filePath): ?array
{
    try {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath, 'Word2007');
        $content = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $content[] = ['type' => 'text', 'value' => $element->getText()];

                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $line = '';
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $line .= $child->getText();
                        }
                    }
                    $content[] = ['type' => 'text', 'value' => $line];

                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $content[] = ['type' => 'table', 'value' => $this->extractDocxTable($element)];
                }
            }
        }

        return $content;

    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Ubah elemen Table PHPWord jadi array baris x kolom string.
 */
private function extractDocxTable(\PhpOffice\PhpWord\Element\Table $table): array
{
    $rows = [];

    foreach ($table->getRows() as $row) {
        $cells = [];

        foreach ($row->getCells() as $cell) {
            $cellText = '';

            foreach ($cell->getElements() as $cellElement) {
                if (method_exists($cellElement, 'getText')) {
                    $cellText .= $cellElement->getText() . ' ';
                } elseif (method_exists($cellElement, 'getElements')) {
                    foreach ($cellElement->getElements() as $sub) {
                        if (method_exists($sub, 'getText')) {
                            $cellText .= $sub->getText() . ' ';
                        }
                    }
                }
            }

            $cells[] = trim($cellText);
        }

        $rows[] = $cells;
    }

    return $rows;
}


public function print($id)
{
    $capa = CAPA::with([
        'user',
        'departemen',
        'representative',
        'rca',
        'ca',
        'pa',
        'comments.user',
        'evidences',
        'authorizedBy',
        'verifiedBy'
    ])->findOrFail($id);

    /* LOGO */
    $logoPath = public_path('img/logo-2.jpg');

    $logo = null;

    if (file_exists($logoPath)) {
        $logo = 'data:image/jpeg;base64,' . base64_encode(
            file_get_contents($logoPath)
        );
    }

    /* EVIDENCE */
    $evidenceImages = [];

    foreach ($capa->evidences as $evidence) {

        $path = public_path(
            'evidence_capa/'.$capa->id.'/'.$evidence->file_name
        );

        if (file_exists($path)) {

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            $mime = match ($ext) {
                'jpg','jpeg' => 'image/jpeg',
                'png'        => 'image/png',
                default      => null,
            };

            if ($mime) {

                $evidenceImages[] = [
                    'src' => 'data:'.$mime.';base64,'.base64_encode(
                        file_get_contents($path)
                    ),
                    'name' => $evidence->file_name
                ];
            }
        }
    }

    return view(
        'mr.capa-pdf',
        compact('capa','logo','evidenceImages')
    );
}




public function detail($id)
{
    $capa = CAPA::with([
        'user',               // created_by
        'departemen',
        'representative',
        'auditors.users',
        'rca',
        'ca',
         'comments.user', // 👈 penting: relasi user komentar
        'pa',
        'evidences'
    ])->findOrFail($id);

     $currentUser = [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'photo' => auth()->user()->avatar ?? 'https://via.placeholder.com/40',
    ];


    return view('mr.detail-capa', compact('capa','currentUser'));
}

public function destroy($id)
{
    $capa = CAPA::findOrFail($id);
    $capa->delete();

    return response()->json([
        'success' => true,
        'message' => 'CAPA berhasil dihapus.'
    ]);
}

public function edit($id)
{
    $capa = CAPA::with([
        'user',              
        'departemen',
        'representative',
        'auditors.users'
    ])->findOrFail($id);

     $departments = Department::orderBy('name')->get(); // ambil semua department
        $users = User::orderBy('name')->get(); // ambil semua department
    return view('mr.edit-capa', compact('capa','departments','users'));
}

 public function update(Request $request, $id)
{
    // Validasi
    $validated = $request->validate([
        'source' => 'required',
        'category' => 'required',
        'dept_id' => 'required|integer',
        'dept_representative' => 'required|integer',
        'detail_of_information' => 'required|string',
        'problem' => 'required|string',

        'auditors' => 'required|array|min:1',
        'auditors.*' => 'exists:users,id',
    ]);

    DB::beginTransaction();

    try {

        // Ambil data lama
        $capa = Capa::findOrFail($id);

        // UPDATE CAPA
        $capa->update([

            'source_of_finding'   => $request->source,
            'category'           => $request->category,
            'dept_id'             => $request->dept_id,
            'dept_representative' => $request->dept_representative,
            'detail_of_information' => $request->detail_of_information,
            'problem'             => $request->problem,
        ]);


        // HAPUS AUDITOR LAMA
        CAPAAuditor::where('capa_id', $capa->id)->delete();


        // INSERT AUDITOR BARU
        $auditorsData = collect($request->auditors)->map(function ($userId) use ($capa) {

            return [
                'capa_id'    => $capa->id,
                'user_id'    => $userId,
                'updated_at'=> now(),
            ];

        })->toArray();


        CAPAAuditor::insert($auditorsData);


        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'CAPA berhasil diupdate.',
            'data'    => $capa
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: '.$e->getMessage()
        ], 500);
    }
}

public function process($id)
{
    $capa = CAPA::with([
        'rca','ca','pa',
          'comments' => function ($q) {
        $q->where('type', 'Returned for Action')
          ->with('user')
          ->orderBy('created_at', 'desc');
    }, // 👈 penting: relasi user komentar // relasi
    ])->findOrFail($id);

  $users = User::where('status', '!=', 0)
    ->select('id', 'name')
    ->orderBy('name')
    ->get();


    return view('mr.process-capa', compact('capa', 'users'));
}

public function updateProcess(Request $request, $id)
{
    try {
        $capa = CAPA::findOrFail($id);

        // Validasi input untuk CAPA actions
        $validated = $request->validate([
            'rca' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'ca_pic' => 'nullable|integer',
            'ca_due_date' => 'nullable|date',
            'preventive_action' => 'nullable|string',
            'pa_pic' => 'nullable|integer',
            'pa_due_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        // Hanya update status CAPA
        $capa->update([
            'status' => 'In Progress',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Simpan RCA
        if (!empty($validated['rca'])) {
           CAPAAction::updateOrCreate(
                ['capa_id' => $capa->id, 'type' => 'RCA'],
                ['description' => $validated['rca']]
            );
        }

        // Simpan Corrective Action
        if (!empty($validated['corrective_action'])) {
            CAPAAction::updateOrCreate(
                ['capa_id' => $capa->id, 'type' => 'CA'],
                [
                    'description' => $validated['corrective_action'],
                    'pic' => $validated['ca_pic'] ?? null,
                    'due_date' => $validated['ca_due_date'] ?? null,
                ]
            );
        }

        // Simpan Preventive Action
        if (!empty($validated['preventive_action'])) {
            CAPAAction::updateOrCreate(
                ['capa_id' => $capa->id, 'type' => 'PA'],
                [
                    'description' => $validated['preventive_action'],
                    'pic' => $validated['pa_pic'] ?? null,
                    'due_date' => $validated['pa_due_date'] ?? null,
                ]
            );
        }

        DB::commit();

              // Setup WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => 'mailto:it2@asnusantara.co.id',
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
        'automaticPadding' => true
    ]);

    // Ambil user MR (department_id = 8)
    $targetUsers = User::join('department_user', 'users.id', '=', 'department_user.user_id')
        ->where('department_user.department_id', 8)
        ->select('users.*')
        ->distinct()
        ->get();

    // Loop user MR
    foreach ($targetUsers as $user) {

        // Ambil subscription per user
        $subscriptions = DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            continue; // skip kalau tidak ada subscription
        }

        foreach ($subscriptions as $subRow) {

            $subData = json_decode($subRow->subscription, true);
            if (!$subData) continue;

            $sub = Subscription::create($subData);

            $webPush->sendOneNotification(
                $sub,
                json_encode([
                    'title' => "✅ CAPA telah diproses | Abimanyu Live",
                    'body'  => "Auditee telah melengkapi action, segera review sebelum user submit evidence. Klik Disini!",
                    'url'   => url("/mr/capa/{$capa->id}/review")
                ])
            );
        }
    }

    // Flush push
    $webPush->flush();

        return response()->json([
            'success' => true,
            'message' => 'CAPA successfully process and actions saved.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to process CAPA: ' . $e->getMessage()
        ], 500);
    }
}

public function checkNumber(Request $request)
{
    $exists = CAPA::where('capa_number', $request->capa_number)->exists();

    return response()->json([
        'exists' => $exists
    ]);
}

public function deleteDocument($capaId, $type)
{
    $action = CapaAction::where('capa_id', $capaId)
                        ->where('type', $type)
                        ->firstOrFail();

    if ($action->supporting_document) {

        // Full path file
      $filePath = '/home/abimany3/public_html/' . ltrim($action->supporting_document, '/');


        // Delete file if exists
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    // Set DB NULL
    $action->supporting_document = null;
    $action->save();

    return back()->with('success', 'Document deleted successfully.');
}


}
