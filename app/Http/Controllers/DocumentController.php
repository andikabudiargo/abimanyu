<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCopy;
use App\Models\Department;
use App\Models\DocumentRevision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DocumentController extends Controller
{
    public function index()
    {
         $departments = Department::orderBy('name')->get(); // ambil semua department
        return view('mr.archive-document', compact('departments'));
    }

    public function create()
    {
       
        $departments = Department::all(); // <- ini penting
        return view('mr.create-document', compact('departments'));
    }

    public function getLastDocumentNumber(Request $request)
{
    $userId = auth()->id();
    $departmentId = User::find($userId)->departments()->first()->id ?? null;

    if (!$departmentId) {
        return response()->json(['last' => null]);
    }

    $userIds = User::whereHas('departments', function ($q) use ($departmentId) {
        $q->where('departments.id', $departmentId);
    })->pluck('id');

    $lastDocument = Document::where('document_type', $request->document_type)
    ->whereHas('revisions', function ($q) use ($userIds) {
        $q->whereIn('created_by', $userIds);
    })
    ->with(['revisions' => function ($q) use ($userIds) {
        $q->whereIn('created_by', $userIds)
          ->orderByDesc('created_at') // urut berdasarkan revisi terbaru dari department
          ->limit(1);
    }])
    ->get()
    ->sortByDesc(function($doc) {
        return optional($doc->revisions->first())->created_at ?? $doc->created_at;
    })
    ->first();


if ($lastDocument) {
    $lastRevision = $lastDocument->revisions->first();
    $result = [
        'document_number' => $lastDocument->document_number,
        'version'         => optional($lastRevision)->version ?? $lastDocument->current_version,
        'created_by'      => optional($lastRevision)->created_by ?? $lastDocument->created_by,
    ];
} else {
    $result = null;
}

return response()->json(['last' => $result]);


}


     public function data(Request $request)
{
     $user = auth()->user();
     // Ambil semua nama departemen user dari relasi pivot
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name')->toArray(); // asumsi relasi `roles` tersedia


$query = Document::with([
    'copies.department',
    'revisions.requestor.departments',
    'revisions.approval',
    'revisions.reject',
    'revisions.authorized',
    'revisions'
])
->orderByRaw("FIELD(status, 'Draft', 'Revision', 'Approved', 'Under Review',  'Resubmitted', 'Published', 'Partially Socialized', 'Closed', 'Obsolete', 'Rejected')")
->orderByDesc(
    DocumentRevision::select('created_at')
        ->whereColumn('document_id', 'documents.id')
        ->latest() // order by created_at desc
        ->limit(1)
);

// Jika user bukan Management Representative → filter berdasarkan departemen
    if (!in_array('Management Representative', $userDepartments)) {
        $query->whereHas('revisions.requestor.departments', function ($q) use ($userDepartments) {
            $q->whereIn('name', $userDepartments);
        });
    }

// Filter document_number
if ($request->document_number) {
    $query->where('document_number', 'like', '%' . $request->document_number . '%');
}

// Filter status di master documents
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// Filter department melalui requestor di revisi terbaru
if ($request->department) {
    $query->whereHas('revisions', function ($q) use ($request) {
        $q->whereHas('requestor.departments', function ($qq) use ($request) {
            $qq->where('name', 'like','%' . $request->department . '%');
        })
        ->whereRaw('id IN (SELECT MAX(id) FROM document_revisions GROUP BY document_id)');
    });
}

// Filter remark di revisi terbaru
if ($request->filled('remark')) {
    $query->whereHas('revisions', function ($q) use ($request) {
        $q->where('remark', 'like', '%' . $request->remark . '%')
          ->whereRaw('id IN (SELECT MAX(id) FROM document_revisions GROUP BY document_id)');
    });
}

// Filter created_at di revisi terbaru
if ($request->date) {
    $dates = explode(' to ', $request->date);
    $start = Carbon::parse($dates[0])->startOfDay();
    $end   = count($dates) === 2 ? Carbon::parse($dates[1])->endOfDay() : Carbon::parse($dates[0])->endOfDay();

    $query->whereHas('revisions', function ($q) use ($start, $end) {
        $q->whereBetween('created_at', [$start, $end])
          ->whereRaw('id IN (SELECT MAX(id) FROM document_revisions GROUP BY document_id)');
    });
}


// Filter document_type di master documents
if ($request->filled('document_type')) {
    $query->where('document_type', $request->document_type);
}



    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;
    
    $user = Auth::user();
$userRoles = $user->roles->pluck('name');
$userDepartments = $user->departments->pluck('name');
// Ambil requestor dari revisions terbaru
$latestRevision = $row->revisions->sortByDesc('created_at')->first(); // ambil revisi terbaru
$requestor = $latestRevision?->requestor;

// Ambil departemen owner dari requestor revisi
$ownerDepartments = optional($requestor?->departments)->pluck('name') ?? collect();

// Cek intersect dengan departemen user saat ini
$hasSameDepartment = $userDepartments->intersect($ownerDepartments)->isNotEmpty();

// Cek apakah user login adalah requestor revisi
$isOwner = $requestor && $requestor->id === Auth::id();


$docNumber = $row->document_number ?? 'Unknown';
$detail_url = route('mr.doc.show', ['id' => $row->id]);
$revision_url = route('mr.doc.rev', ['id' => $row->id]);



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

            if ($isOwner && $row->status === 'Draft') {
    $actionButtons .= '
       
        <button onclick="confirmDelete(' . $row->id . ',  \'' . $docNumber . '\')" 
            class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
    </button>';
}

 if ($isOwner && $row->status === 'Closed') {
    $actionButtons .= '
       <a href="' . $revision_url . '" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Revision
        </a>
        <button onclick="obsoleteDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Obsolete
        </button>';
}

 if ($isOwner && $row->status === 'Under Review') {
    $actionButtons .= '
        <button onclick="resubmitDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Resubmit
        </button>';
}

   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Draft' && 
    $hasSameDepartment &&
    $userRoles->contains(function ($role) {
        return in_array($role, [
            'Supervisor Special Access',
            'Manager Special Access'
        ]);
    })
) {

        $actionButtons .= '
        <button onclick="approveDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
        </button>
        <button onclick="rejectDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

    if (
    $row->status === 'Revision' && 
    $hasSameDepartment &&
    $userRoles->contains(function ($role) {
        return in_array($role, [
            'Supervisor Special Access',
            'Manager Special Access'
        ]);
    })
) {

        $actionButtons .= '
        <button onclick="approveDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
        </button>';
    }

    if (
    ($row->status === 'Approved' || $row->status === 'Resubmitted') && // ✅ cek dua status
    $userDepartments->contains('Management Representative')
) {
    $actionButtons .= '
        <button onclick="reviewDOC(' . $id . ', \'' . $docNumber . '\')" 
            class="block w-full text-left px-4 py-2 text hover:bg-green-100 text-green-700">
            <i data-feather="zoom-in" class="w-4 h-4 inline mr-2"></i>Review
        </button>';
}

if (
    ($row->status === 'Under Review') && // ✅ cek dua status
    $userDepartments->contains('Management Representative')
) {
    $actionButtons .= '
        <button onclick="authorizedDOC(' . $id . ', \'' . $docNumber . '\')" 
            class="block w-full text-left px-4 py-2 text hover:bg-green-100 text-green-700">
            <i data-feather="edit-3" class="w-4 h-4 inline mr-2"></i>Authorized
        </button>';
}


if (
    ($row->status === 'Published' || $row->status === 'Partially Socialized') &&
    $userDepartments->contains('Management Representative')
) {

        $actionButtons .= '
          <button onclick="updateDOC(' . $id . ', \'' . $docNumber . '\')" class="block w-full text-left px-4 py-2 text hover:bg-purple-100 text-purple-700">
            <i data-feather="calendar" class="w-4 h-4 inline mr-2"></i>Socialize
        </button>';
}

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('created_by', function ($row) {
    $revision = $row->revisions->last(); // ambil revisi terakhir
    return $revision && $revision->requestor
        ? $revision->requestor->name
        : '-';
})

->addColumn('department', function ($row) {
    $revision = $row->revisions->last();
    return $revision && $revision->requestor && $revision->requestor->departments->first()
        ? $revision->requestor->departments->first()->name
        : '-';
})


->addColumn('approved_by', function ($row) {
    $revision = $row->revisions->last(); 
    // Approved by tetap dari master documents
   return $revision && $revision->approval
        ? $revision->approval->name
        : '-';
})

->addColumn('authorized_by', function ($row) {
    $revision = $row->revisions->last(); 
    // Approved by tetap dari master documents
   return $revision && $revision->authorized
        ? $revision->authorized->name
        : '-';
})




->editColumn('document_type', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-xs font-medium p-1 rounded-xl';

    if ($row->document_type === 'Standard') {
        return '<span class="text-indigo-600 ' . $commonClasses . '">Standard</span>';
    } elseif ($row->document_type === 'SOP') {
        return '<span class="text-orange-600 ' . $commonClasses . '">SOP</span>';
    } elseif ($row->document_type === 'Work Instructions') {
        return '<span class="text-lime-500 ' . $commonClasses . '">Work Instructions</span>';
    } elseif ($row->document_type === 'Form') {
        return '<span class="text-rose-600 ' . $commonClasses . '">Form</span>';
    }
})

->editColumn('remark', function ($row) {
    $commonClasses = 'inline-block text-center text-xs font-semibold p-1 rounded-lg';

    // Ambil remark dari revisi terbaru
    $latestRemark = $row->revisions->sortByDesc('created_at')->first()?->remark;

    if ($latestRemark === 'New Release') {
        return '<span class="text-green-600 ' . $commonClasses . '">New Release</span>';
    } elseif ($latestRemark === 'Revision') {
        return '<span class="text-blue-600 ' . $commonClasses . '">Revision</span>';
    } elseif ($latestRemark === 'Obsolete') {
        return '<span class="text-red-600 ' . $commonClasses . '">Obsolete</span>';
    } else {
        return '<span class="text-gray-600 ' . $commonClasses . '">' . ($latestRemark ?? '-') . '</span>';
    }
})


->editColumn('status', function ($row) {
    $status = $row->status ?? 'Draft'; // fallback Draft kalau null

    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    return match ($status) {
        'Draft'                => '<span class="bg-gray-500 '   . $commonClasses . '">Draft</span>',
        'Approved'             => '<span class="bg-yellow-500 ' . $commonClasses . '">Approved</span>',
        'Under Review'         => '<span class="bg-indigo-500 ' . $commonClasses . '">Under Review</span>',
        'Published'            => '<span class="bg-green-500 '  . $commonClasses . '">Published</span>',
        'Resubmitted'          => '<span class="bg-blue-500 '   . $commonClasses . '">Resubmitted</span>',
        'Partially Socialized' => '<span class="bg-purple-500 ' . $commonClasses . '">Partially Socialized</span>',
        'Closed'               => '<span class="bg-teal-500 '   . $commonClasses . '">Closed</span>',
        'Rejected'             => '<span class="bg-red-500 '    . $commonClasses . '">Rejected</span>',
        'Revision'             => '<span class="bg-lime-500 '   . $commonClasses . '">Revision</span>',
        'Obsolete'             => '<span class="bg-orange-500 ' . $commonClasses . '">Obsolete</span>',
        default                => '<span class="bg-gray-400 '   . $commonClasses . '">Unknown</span>',
    };
})

->addColumn('file', function ($row) {
    $revision = $row->revisions->last(); // ambil revisi terbaru
    if ($revision && $revision->file) {
        $fileUrl = asset('storage/' . $revision->file);
        $extension = strtolower(pathinfo($revision->file, PATHINFO_EXTENSION));

        // ✅ tambahkan current_version ke nama file download
        $downloadName = $row->document_number . '-' . $revision->version . '.' . $extension;

        $icon = match($extension) {
            'pdf' => '<i class="fas fa-file-pdf text-red-600 text-2xl"></i>',
            'doc', 'docx' => '<i class="fas fa-file-word text-blue-600 text-2xl"></i>',
            'xls', 'xlsx' => '<i class="fas fa-file-excel text-green-600 text-2xl"></i>',
            default => '<i class="fas fa-file text-gray-600 text-2xl"></i>',
        };

        return '
            <a href="' . $fileUrl . '" download="' . $downloadName . '" 
               class="flex items-center space-x-3 hover:underline">
                ' . $icon . '
                <div class="flex flex-col text-left">
                    <span class="text-sm font-semibold text-gray-800">'
                        . $row->document_number . '-' . $revision->version . '</span> 
                    <span class="text-xs text-gray-500">' . $row->document_type . '</span>
                </div>
            </a>
        ';
    }
    return '-';
})


->editColumn('created_at', function ($row) {
    $latestRevision = $row->revisions()->latest()->first();
    return $latestRevision && $latestRevision->created_at
        ? $latestRevision->created_at->format('d-m-Y H:i')
        : '-';
})

->editColumn('approved_at', function ($row) {
    $latestRevision = $row->revisions()->latest()->first();
    return $latestRevision && $latestRevision->approved_at
        ? \Carbon\Carbon::parse($latestRevision->approved_at)->format('d-m-Y H:i')
        : '-';
})

->editColumn('authorized_at', function ($row) {
    $latestRevision = $row->revisions()->latest()->first();
    return $latestRevision && $latestRevision->authorized_at
        ? \Carbon\Carbon::parse($latestRevision->authorized_at)->format('d-m-Y H:i')
        : '-';
})


        ->rawColumns(['action', 'status','file', 'remark', 'document_type'])
        ->make(true);
}

public function store(Request $request)
{
    $request->validate([
        'document_number' => 'required|string|max:100|unique:documents,document_number',
        'document_type'   => 'required|string',
        'title'           => 'required|string|max:255',
        'file'            => 'required|file|mimes:pdf,doc,docx,xlsx|max:5120',
        '4m'              => 'nullable|file|mimes:pdf,doc,docx,xlsx|max:5120',
        'current_version' => 'nullable|string|max:2', // ✅ tambahin ini
        'reason'          => 'nullable|string',
        'copies'          => 'nullable|array',
        'copies.*.department_id' => 'nullable|integer',
        'copies.*.qty'    => 'nullable|integer|min:0'
    ]);

    DB::beginTransaction();
    try {
        // === Upload file utama ===
     $filePath = null;
if ($request->hasFile('file')) {
    $extension = $request->file('file')->getClientOriginalExtension(); 
    
    // pastikan ada current_version di request
    $version   = str_pad($request->current_version ?? '00', 2, '0', STR_PAD_LEFT);
    
    // gabungkan document_number + current_version
    $fileName  = $request->document_number . '-' . $version . '.' . $extension; 
    
    // simpan di storage/app/public/documents
    $filePath  = $request->file('file')->storeAs('documents', $fileName, 'public');
}

        // === Upload file 4M ===
        $file4mPath = null;
        if ($request->hasFile('4m')) {
            $extension4m = $request->file('4m')->getClientOriginalExtension();
            $fileName4m  = $request->document_number . '_4M_Attachment.' . $extension4m;
            $file4mPath  = $request->file('4m')->storeAs('documents/4m', $fileName4m, 'public');
        }

        // Tentukan versi awal
$initialVersion = $request->filled('current_version') 
    ? str_pad($request->current_version, 2, '0', STR_PAD_LEFT)  // pakai input user jika ada
    : '00';  // default

        // === Simpan ke documents (master) ===
        $document = Document::create([
            'document_number'  => $request->document_number,
            'document_type'    => $request->document_type,
            'title'            => $request->title,
            'reason'           => $request->reason,
            'current_version'  => $initialVersion,
        ]);

        // === Simpan ke document_revisions (initial version) ===
        $revision = $document->revisions()->create([
            'version'          => $initialVersion,
            'file'             => $filePath,
            'file_4m'          => $file4mPath,
            'created_by'       => Auth::id(),
            'created_at'       => now()
        ]);

        // === Simpan copies kalau ada ===
        if ($request->has('copies')) {
            foreach ($request->copies as $copy) {
                if ($copy['qty'] > 0) {
                    DocumentCopy::create([
                        'document_id'   => $document->id,
                        'department_id' => $copy['department_id'],
                        'qty'           => $copy['qty'],
                        'document_revision_id' => $revision->id, // default ke revision awal
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully.',
            'data'    => [
                'document_id'     => $document->id,
                'document_number' => $document->document_number,
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function storeRevision(Request $request, $id)
{
    $doc = Document::findOrFail($id);

    $request->validate([
        'file' => 'nullable|file|mimes:pdf,xlsx,doc,docx|max:5120',
        '4m' => 'nullable|file|mimes:pdf,xlsx,doc,docx|max:5120',
        'reason_revision' => 'required|string|max:1000',
        'copies.*.department_id' => 'required|exists:departments,id',
        'copies.*.qty' => 'required|integer|min:1',
    ]);

    // Hitung version baru otomatis
    $lastVersion = $doc->current_version ?? '00';
    $newVersionInt = intval($lastVersion) + 1;
    $newVersion = str_pad($newVersionInt, 2, '0', STR_PAD_LEFT);

    // Update master document
    $doc->status = 'Revision';
    $doc->current_version = $newVersion;
    $doc->save();

    $docNumber = $doc->document_number;

   // Upload file utama
$filePath = $request->hasFile('file')
    ? $request->file('file')->storeAs(
        'documents',
        $docNumber . '-' . $newVersion . '.' . $request->file('file')->getClientOriginalExtension(),
        'public'
    )
    : null;

    // Upload file 4M
$file4MPath = $request->hasFile('4m')
    ? $request->file('4m')->storeAs(
        'documents/4m',
        $docNumber . '_' . $newVersion . '_4M.' . $request->file('4m')->getClientOriginalExtension(),
        'public'
    )
    : null;

    // Buat revisi baru
    $revision = $doc->revisions()->create([
        'version' => $newVersion,
        'file' => $filePath,
        'file_4m' => $file4MPath,
        'remark' => 'Revision',
        'reason_revision' => $request->reason_revision,
        'created_by' => auth()->id(),
    ]);

    // Simpan copies jika ada
    if ($request->has('copies')) {
        foreach ($request->copies as $copy) {
            $revision->copies()->create([
                'document_id'   => $doc->id,   // simpan document_id juga
                'department_id' => $copy['department_id'],
                'qty' => $copy['qty'],
            ]);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Revision created successfully.',
        'revision_id' => $revision->id,
        'version' => $newVersion,
    ]);
}


public function getCopies($document_id)
{
    $copies = DB::table('document_copies as dc')
        ->join('departments as d', 'dc.department_id', '=', 'd.id')
        ->where('dc.document_id', $document_id)
        ->whereNull('dc.date') // hanya ambil yang date masih null
        ->select(
            'dc.id',
            'd.name as department_name', // ambil nama department
            'dc.qty',
            'dc.date'
        )
        ->get();

    return response()->json($copies);
}


public function saveSocialize(Request $request)
{
    $dates = $request->input('dates');
    $documentId = $request->input('document_id'); // pastikan dikirim dari frontend

    // Update semua tanggal yang dikirim
    foreach ($dates as $id => $date) {
        DB::table('document_copies')
            ->where('id', $id)
            ->update([
                'date' => $date,
                'socialized_by'  => Auth::id(), // simpan user ID yang login
                'updated_at' => now()
            ]);
    }

    // Cek status setelah update
    $totalCopies = DB::table('document_copies')
        ->where('document_id', $documentId)
        ->count();

    $filledCopies = DB::table('document_copies')
        ->where('document_id', $documentId)
        ->whereNotNull('date')
        ->count();

    if ($filledCopies < $totalCopies) {
        $status = 'Partially Socialized';
    } else {
        $status = 'Closed';
    }

    // Update status di tabel documents
    DB::table('documents')
        ->where('id', $documentId)
        ->update([
            'status' => $status,
            
            'updated_at' => now()
        ]);

    return response()->json(['success' => true, 'status' => $status]);
}



public function approve($id)
{
    $doc = Document::findOrFail($id);

    // 1️⃣ Update status di tabel documents
    $doc->status = 'Approved';
    $doc->save();
      $latestRevision = $doc->revisions()->latest('created_at')->first();

    if ($latestRevision) {
        $latestRevision->approved_by = auth()->id();
        $latestRevision->approved_at = now();
        $latestRevision->save();
    } else {
        // Jika belum ada revisi sama sekali
        $doc->revisions()->create([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Document Approved.',
        'document_number' => $doc->document_number
    ]);
}


public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $doc = Document::findOrFail($id);
    $doc->status = 'Rejected';
    $doc->rejected_reason = $request->input('rejected_reason', 'No reason provided.');
    $doc->rejected_by = auth()->id();
    $doc->rejected_at = now();
    $doc->save();

     return response()->json([
        'success' => true,
        'message' => 'Document rejected successfully.',
        'document_number' => $doc->document_number
    ]);
}

public function obsolete(Request $request, $id)
{
    $request->validate([
        'obsolete_reason' => 'required|string|max:1000'
    ]);

    $doc = Document::findOrFail($id);
    $doc->status = 'Obsolete';
    $doc->save();

     $latestRevision = $doc->revisions()->latest('created_at')->first();

    if ($latestRevision) {
        $latestRevision->remark = 'Obsolete';
        $latestRevision->created_by = auth()->id();
        $latestRevision->created_at = now();
        $latestRevision->save();
    } else {
        // Jika belum ada revisi sama sekali
        $doc->revisions()->create([
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }


     return response()->json([
        'success' => true,
        'message' => 'Document obsolete successfully.',
        'document_number' => $doc->document_number
    ]);
}

public function review($id)
{
    $doc = Document::findOrFail($id);
    // Update latest revision
    $doc->status = 'Under Review';
    $doc->save();
 $latestRevision = $doc->revisions()->latest('created_at')->first();

    if ($latestRevision) {
        $latestRevision->review_by = auth()->id();
        $latestRevision->review_at = now();
        $latestRevision->save();
    } else {
        // Jika belum ada revisi sama sekali
        $doc->revisions()->create([
            'review_by' => auth()->id(),
            'review_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Document set to be Under Review.',
        'document_number' => $doc->document_number
    ]);
}

public function authorized($id)
{
    $doc = Document::findOrFail($id);
    // Update latest revision
    $doc->status = 'Published';
     $doc->save();
    $latestRevision = $doc->revisions()->latest('created_at')->first();

    if ($latestRevision) {
        $latestRevision->authorized_by = auth()->id();
        $latestRevision->authorized_at = now();
        $latestRevision->save();
    } else {
        // Jika belum ada revisi sama sekali
        $doc->revisions()->create([
            'authorized_by' => auth()->id(),
            'authorized_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Document sucesfully Authorized and set to be Published.',
        'document_number' => $doc->document_number
    ]);
}

public function show($id)
{
    
    $document = Document::with(['revisions.requestor', 'revisions.review', 'revisions.authorized', 'revisions'])->findOrFail($id);
    $userDepartments = Auth::user()->departments->pluck('name')->toArray();
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
    $hasSameDepartment = $document->department_id == auth()->user()->department_id; // contoh logika
    return view('mr.detail-document', compact('document','hasSameDepartment','userRoles','userDepartments'));
}

public function addNote(Request $request, $id)
{
    $request->validate([
        'note' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $document = Document::findOrFail($id);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('notes', 'public');
    }

    $note = $document->notes()->create([
        'user_id' => auth()->id(),
       'note' => $request->content, // hanya teks note
    'image' => $imagePath,       // hanya simpan path image
        'created_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'note' => [
            'user' => $note->user->name,
            'avatar' => $note->user->avatar ? asset('storage/'.$note->user->avatar) : asset('img/avatar-dummy.png'),
             'content' => $note->note, // teks note
            'created_at' => $note->created_at->format('d M Y H:i')
        ]
    ]);
}

public function resubmit(Request $request, $id)
{
    $document = Document::findOrFail($id);

    $request->validate([
        'file' => 'required|mimes:pdf,xlsx,xls|max:5120',
    ]);

    // ambil revisi terakhir
    $lastRevision = $document->revisions()->orderByDesc('id')->first();

    if (!$lastRevision) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada revisi sebelumnya untuk dokumen ini',
        ], 400);
    }

    // versi tetap sama
    $version = str_pad($lastRevision->version, 2, '0', STR_PAD_LEFT);

    // ambil ekstensi asli
    $extension = $request->file('file')->getClientOriginalExtension();

    // bikin nama file sesuai nomor dokumen dan versi
    $fileName = $document->document_number . '-' . $version . '.' . $extension;

    // simpan file
    $filePath = $request->file('file')->storeAs('documents', $fileName, 'public');

    // update revisi terakhir (replace file lama dengan yang baru)
    $lastRevision->update([
        'file'       => $filePath,
        'created_by' => Auth::id(),
        'review_by'  => null,   // reset reviewer
        'review_at'  => null,   // reset tanggal review
    ]);

    // update status dokumen utama
    $document->status = 'Resubmitted';
    $document->save();

    return response()->json([
        'success' => true,
        'message' => 'Document resubmitted successfully',
         'document_number' => $document->document_number, // ✅ tambahkan ini
    ]);
}


public function destroy($id)
{
    try {
        $document = Document::findOrFail($id);
        $docNumber = $document->document_number;

        // hapus file utama
        if ($document->file && Storage::disk('public')->exists($document->file)) {
            Storage::disk('public')->delete($document->file);
        }

        // hapus file 4M
        if ($document->file_4m && Storage::disk('public')->exists($document->file_4m)) {
            Storage::disk('public')->delete($document->file_4m);
        }

        // hapus relasi copies
        $document->copies()->delete();

        // hapus dokumen
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => "Document {$docNumber} deleted successfully.",
            'doc_number' => $docNumber
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function revision($id)
    {
        $doc = Document::with('copies')->findOrFail($id);
        $departments = Department::all();

        return view('mr.revision-document', compact('doc', 'departments'));
    }

}
