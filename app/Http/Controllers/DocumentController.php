<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCopy;
use App\Models\Department;
use App\Models\DocumentRevision;
use App\Models\DocumentRegistration;
use App\Models\DocumentNote;
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
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class DocumentController extends Controller
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

    public function index()
    {
         $departments = Department::orderBy('name')->get(); // ambil semua department
        return view('mr.archive-document', compact('departments'));
    }

    public function create()
    {
        $departments = Department::whereNotIn('id', [2,3,5,11,15,18,20,21])
    ->orderBy('name')
    ->get();
        return view('mr.create-document', compact('departments'));
    }

    public function getDocumentNumber(Request $request)
{
    $user = auth()->user();

    // =========================
    // AMBIL & EXPAND DEPT GROUP
    // =========================
    $rawDeptIds = $user->departments->pluck('id')->toArray();

    $departmentIds = collect($rawDeptIds)
        ->flatMap(function ($deptId) {
            return $this->resolveDepartmentGroup($deptId);
        })
        ->unique()
        ->values();

    $query = Document::select(
        'id',
        'document_number',
        'document_title',
        'current_version',
        'dept_to'
    );

    // =========================
    // NORMALIZE TYPE
    // =========================
    $type = strtolower(trim($request->document_type));

    // =========================
    // FILTER DOCUMENT TYPE
    // =========================
    if ($type === 'other') {

        $query->whereRaw("
            TRIM(LOWER(document_type)) NOT IN (
                'sop',
                'form',
                'standard',
                'work instructions'
            )
        ");

    } else {

        $query->whereRaw(
            "TRIM(LOWER(document_type)) = ?",
            [$type]
        );
    }

    // =========================
    // FILTER BY DEPARTMENT (SUDAH GROUPED)
    // =========================
    $query->whereIn('dept_from', $departmentIds);

    // =========================
    // ONLY ACTIVE
    // =========================
    $query->where('is_active', 1);

    // =========================
    // ORDER
    // =========================
    $docs = $query->orderBy('document_number', 'desc')->get();

    return response()->json($docs);
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
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name');

   $query = DocumentRegistration::query();

// =========================
// CEK ROLE MR
// =========================
$isMR = $user->departments()
    ->where('name', 'Management Representative')
    ->exists();

// =========================
// FILTER (NON-MR SAJA)
// =========================
if (!$isMR) {

    $rawDeptIds = $user->departments->pluck('id');

    $departmentIds = collect($rawDeptIds)
        ->flatMap(fn($deptId) => $this->resolveDepartmentGroup($deptId))
        ->unique();

    $query->where(function ($q) use ($user, $departmentIds) {

        $q->where('created_by', $user->id)
          ->orWhereIn('department_id', $departmentIds);

    });
}

// =========================
// ORDER TERBARU
// =========================
$query->orderByDesc('created_at');

  // Filter document_number
if ($request->document_number) {
    $query->where('document_number', 'like', '%' . $request->document_number . '%');
}

// Filter status di master documents
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

if ($request->filled('dept_from')) {

    $deptIds = collect($this->resolveDepartmentGroup($request->dept_from));

    $query->whereIn('created_by', function ($q) use ($deptIds) {
        $q->select('user_id')
          ->from('department_user')
          ->whereIn('department_id', $deptIds);
    });
}

if ($request->filled('dept_to')) {

    $deptIds = collect($this->resolveDepartmentGroup($request->dept_to))
        ->map(fn($id) => (int) $id)
        ->unique();

    $query->whereIn('department_id', $deptIds);
}

if ($request->filled('document_type')) {

    $type = strtolower(trim($request->document_type));

    if ($type === 'other') {

        $query->whereRaw("
            TRIM(LOWER(document_type)) NOT IN (
                'sop',
                'form',
                'standard',
                'work instructions'
            )
        ");

    } else {

        $query->whereRaw(
            "TRIM(LOWER(document_type)) = ?",
            [$type]
        );
    }
}

// Filter status di master documents
if ($request->filled('submission_type')) {
    $query->where('submission_type', $request->submission_type);
}

if ($request->filled('registration_date')) {

    $dates = explode(' to ', $request->registration_date);

    if (count($dates) === 2) {
        $start = trim($dates[0]);
        $end   = trim($dates[1]);

        $query->whereDate('created_at', '>=', $start)
              ->whereDate('created_at', '<=', $end);
    }
}

    

    return DataTables::of($query)
  ->addColumn('action', function ($row) {
    $id = $row->id;
    $user = Auth::user();
    $dropdownId = 'dropdown-' . $id;
    $detail_url = route('mr.doc.detail', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $edit_url = route('mr.doc.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $approve_url = route('mr.doc.approve', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $isOwner = $row->created_by == Auth::id();
  $rawDeptIds = $user->departments->pluck('id')->toArray();

$departmentIds = collect($rawDeptIds)
    ->flatMap(fn($deptId) => $this->resolveDepartmentGroup($deptId))
    ->unique();

$isSPVTarget =
    $departmentIds->contains($row->department_id)
    && $user->roles->pluck('name')->intersect([
        'Supervisor Special Access',
        'Manager Special Access'
    ])->isNotEmpty();
     $isMR = $user->departments()
    ->where('name', 'Management Representative')
    ->exists();

    $actionButtons = '
    <div class="relative inline-block text-left">
      <button type="button"
        data-dropdown-id="' . $dropdownId . '"
        onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
        class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
        <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">';

$actionButtons .= '
            <a href="'. $detail_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
            </a>';

if ($isOwner && $row->status == 'Submitted') {
        $actionButtons .= '
            <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete(' . $row->id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>
            ';
}

if ($isOwner && in_array($row->status, ['Returned by SPV', 'Returned by MR'])) {
    $actionButtons .= '
        <a href="'. $detail_url .'" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="rotate-ccw" class="w-4 h-4 inline mr-2"></i>Resubmit
        </a>
    ';
}

if ($isSPVTarget && $row->status == 'Submitted') {
        $actionButtons .= '
            <button onclick="approveDOC(' . $id . ')" class="w-full text-left px-4 py-2 text-green-600 hover:bg-green-600 hover:text-white">
                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>Approve
            </button>
            <button onclick="openReturnModal(' . $row->id . ')" 
                class="w-full text-left text-amber-600 px-4 py-2 hover:bg-amber-500 hover:text-white">
                <i data-feather="rotate-ccw" class="w-4 h-4 inline mr-2"></i>Return
            </button>
            ';
}

if ($isMR && $row->status == 'Approved') {
        $actionButtons .= '
           <a href="'. $detail_url .'" class="w-full block text-left px-4 py-2 text-green-600 hover:bg-green-600 hover:text-white">
                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>Authorized
            </a>
            <a href="' . $detail_url . '" 
                class="w-full block text-left text-amber-600 px-4 py-2 hover:bg-amber-500 hover:text-white">
                <i data-feather="rotate-ccw" class="w-4 h-4 inline mr-2"></i>Return
            </a>
            <button onclick="rejectDOC(' . $row->id . ')" 
                class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Reject
            </button>
            ';
}

$actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('department_id', function ($row) {
    return $row->department->name
        ?? '-';
})

->addColumn('department', function ($row) {
    return $row->createdBy->departments->first()?->name
        ?? '-';
})

->addColumn('created_by', function ($row) {
    return $row->createdBy->name
        ?? '-';
})

->editColumn('approved_by', function ($row) {
    return $row->approvedBy->name
        ?? '-';
})

->editColumn('authorized_by', function ($row) {
    return $row->authorizedBy->name
        ?? '-';
})

->editColumn('created_at', function ($row) {
    return $row->created_at
        ?? '-';
})

->editColumn('approved_at', function ($row) {
    return $row->approved_at
        ?? '-';
})

->editColumn('authorized_at', function ($row) {
    return $row->authorized_at
        ?? '-';
})

->addColumn('document', function ($row) {

    if (!$row->file_path) {
        return '-';
    }

    // =========================
    // BUILD FILE URL
    // =========================
    $docType = strtolower(str_replace(' ', '_', $row->document_type));
    $deptFrom = $row->dept_from;

    $relativePath = "documents/{$docType}/{$deptFrom}/{$row->file_path}";
    $fileUrl = asset($relativePath);

    // =========================
    // FILE INFO
    // =========================
    $extension = strtolower(pathinfo($row->file_path, PATHINFO_EXTENSION));
    $downloadName = $row->file_path;

    $icon = match ($extension) {
        'pdf'        => '<i class="fas fa-file-pdf text-red-500 text-xl"></i>',
        'doc', 'docx'=> '<i class="fas fa-file-word text-blue-500 text-xl"></i>',
        'xls', 'xlsx'=> '<i class="fas fa-file-excel text-green-500 text-xl"></i>',
        default      => '<i class="fas fa-file text-gray-400 text-xl"></i>',
    };

    $type = strtoupper($row->document_type);

    $badgeColor = match ($type) {
        'SOP'               => 'bg-purple-100 text-purple-700',
        'STANDARD'          => 'bg-blue-100 text-blue-700',
        'WORK INSTRUCTIONS' => 'bg-amber-100 text-amber-700',
        'FORM'              => 'bg-emerald-100 text-emerald-700',
        default             => 'bg-gray-100 text-gray-600',
    };

    return '
        <a href="' . $fileUrl . '" download="' . e($downloadName) . '" 
           class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition group">

            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white shadow-sm flex-shrink-0">
                ' . $icon . '
            </div>

            <div class="flex flex-col justify-center min-w-0">

                <span class="inline-block w-fit px-2 py-0.5 text-[10px] font-semibold rounded ' . $badgeColor . '">
                    ' . e($type) . '
                </span>

                <span class="text-sm font-semibold text-gray-800 truncate">
                    ' . e($row->document_number) . '
                </span>

                <span class="text-xs text-gray-500 truncate">
                    ' . e($row->document_title) . '
                </span>

            </div>
        </a>
    ';
})

->editColumn('submission_type', function ($row) {
    $commonClasses = 'inline-block text-center text-xs font-semibold p-1 rounded-lg';

    if ($row->submission_type === 'New Release') {
        return '<span class="text-green-600 ' . $commonClasses . '">New Release</span>';
    } elseif ($row->submission_type === 'Revision') {
        return '<span class="text-yellow-600 ' . $commonClasses . '">Revision</span>';
    } elseif ($row->submission_type === 'Obsolete') {
        return '<span class="text-red-600 ' . $commonClasses . '">Obsolete</span>';
    } else {
        return '<span class="text-gray-600 ' . $commonClasses . '">' . ($row->submission_type ?? '-') . '</span>';
    }
})

->editColumn('status', function ($row) {
    $status = $row->status ?? 'Submitted'; // fallback Draft kalau null

    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    return match ($status) {
        'Submitted'            => '<span class="bg-gray-500 '   . $commonClasses . '">Submitted</span>',
        'Approved'             => '<span class="bg-yellow-500 ' . $commonClasses . '">Approved</span>',
        'Returned by SPV'      => '<span class="bg-orange-500 ' . $commonClasses . '">Returned by SPV</span>',
        'Under Review'         => '<span class="bg-teal-500 ' . $commonClasses . '">Under Review</span>',
        'Published'            => '<span class="bg-green-500 '  . $commonClasses . '">Published</span>',
        'Resubmitted'          => '<span class="bg-blue-500 '   . $commonClasses . '">Resubmitted</span>',
        'Partially Socialized' => '<span class="bg-purple-500 ' . $commonClasses . '">Partially Socialized</span>',
        'Rejected'             => '<span class="bg-red-500 '    . $commonClasses . '">Rejected</span>',
        default                => '<span class="bg-gray-400 '   . $commonClasses . '">Unknown</span>',
    };
})

        ->rawColumns(['action','document','submission_type', 'status', 'department'])
        ->make(true);
}

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // =========================
            // 1. NORMALIZE BOOLEAN
            // =========================
        $need4m = $request->boolean('need_4m');

            // =========================
            // 2. VALIDATION
            // =========================
            $validator = \Validator::make($request->all(), [
                'department_id'   => 'required',
                'document_type'   => 'required',
                'submission_type' => 'required',
                'document_title'  => 'required',
                'file_path'       => 'required|file|max:5120',
                'need_4m'         => 'required|boolean',
                'file_4m_path'    => $need4m
                    ? 'required|file|max:5120'
                    : 'nullable|file|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // =========================
            // 3. MAPPING TYPE
            // =========================
            $typeMap = [
                'Form' => 'FM',
                'Work Instructions' => 'IK',
                'Standard' => 'STD',
                'SOP' => 'SOP',
            ];

            $code = $typeMap[$request->document_type] ?? 'OTH';

            // =========================
            // 4. GENERATE REG NUMBER
            // =========================
            $year = date('Y');

            $last = DocumentRegistration::whereYear('created_at', $year)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $next = 1;

            if ($last) {
                preg_match('/REG'.$year.'(\d{5})/', $last->registration_number, $match);
                $next = isset($match[1]) ? ((int)$match[1] + 1) : 1;
            }

            $running = str_pad($next, 5, '0', STR_PAD_LEFT);
            $registrationNumber = "REG{$year}{$running}{$code}";

            // =========================
            // 5. UPLOAD FUNCTION (LOCAL)
            // =========================
        $storeFile = function ($file, $docType, $deptFrom, $docNumber, $docTitle) {

    // =========================
    // FORMAT FOLDER
    // =========================
    $docType  = strtolower(str_replace(' ', '_', $docType));
   $destinationPath = "/home/abimany3/public_html/documents/{$docType}/{$deptFrom}";

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    // =========================
    // EXTENSION
    // =========================
    $extension = strtolower($file->getClientOriginalExtension());

    // =========================
    // FORMAT NAMA FILE
    // =========================
    $baseName = $docNumber . '_' . $docTitle;

    $baseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
    $baseName = preg_replace('/_+/', '_', $baseName);
    $baseName = trim($baseName, '_');

    $filename = $baseName . '.' . $extension;

    // =========================
    // HANDLE DUPLICATE
    // =========================
    $i = 1;
    while (file_exists($destinationPath . '/' . $filename)) {
        $filename = $baseName . '(' . $i . ').' . $extension;
        $i++;
    }

    // =========================
    // MOVE FILE
    // =========================
    $file->move($destinationPath, $filename);

    // ❗ RETURN HANYA NAMA FILE
    return $filename;
};

            // =========================
            // 6. UPLOAD FILE
            // =========================
        $user = auth()->user();
$deptFrom = $user->departments->first()->id ?? 0;

$fileName = null;
if ($request->hasFile('file_path')) {
    $fileName = $storeFile(
        $request->file('file_path'),
        $request->document_type,
        $deptFrom,
        $request->document_number,
        $request->document_title
    );
}

$file4mName = null;
if ($need4m && $request->hasFile('file_4m_path')) {
    $file4mName = $storeFile(
        $request->file('file_4m_path'),
        $request->document_type,
        $deptFrom,
        $request->document_number,
        $request->document_title . '_4M'
    );
}

            // =========================
            // 7. INSERT
            // =========================
            $registration = DocumentRegistration::create([
                'registration_number' => $registrationNumber,
                'department_id'       => $request->department_id,
                'document_number'     => $request->document_number,
                'document_type'       => $request->document_type,
                'submission_type'     => $request->submission_type,
                'document_title'      => $request->document_title,
                'reason'              => $request->reason,
                'need_4m'             => $need4m,
                'file_path'           => $fileName,
                'file_4m_path'        => $file4mName,
                'created_by'          => auth()->id(),
                'status'              => 'Submitted',
            ]);

            // =========================
            // 8. REVISION
            // =========================
            if (in_array($request->submission_type, ['Revision', 'Obsolete'])) {
                DocumentRevision::create([
                    'registration_id' => $registration->id,
                    'revision_number' => $request->revision_number,
                    'file_path'       => $fileName,
                    'file_4m_path'    => $file4mName,
                    'before_change'   => $request->before_change,
                    'after_change'    => $request->after_change,
                ]);
            }

            // =========================
            // 9. SHARE DEPT
            // =========================
            if ($request->has('share_dept')) {
                foreach ($request->share_dept as $i => $deptId) {

                    $qty = $request->share_qty[$i] ?? 0;

                    if (!empty($deptId) && $qty > 0) {
                        DocumentCopy::create([
                            'registration_id' => $registration->id,
                            'department_id'   => $deptId,
                            'qty'             => $qty,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document successfully registered!',
                'data' => [
                    'id' => $registration->id,
                    'registration_number' => $registrationNumber
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $doc = DocumentRegistration::findOrFail($id);

        // =========================
        // 1. NORMALIZE
        // =========================
       $need4m     = $request->boolean('need_4m');
$isResubmit = $request->is_resubmit == 1;

        // =========================
        // 2. VALIDATION
        // =========================
        $validator = \Validator::make($request->all(), [
            'department_id'   => 'required',
            'document_type'   => 'required',
            'submission_type' => 'required',
            'document_title'  => 'required',
            'file_path'       => 'nullable|file|max:5120',
            'need_4m'         => 'required|in:0,1',
            'file_4m_path'    => $need4m
                ? 'nullable|file|max:5120'
                : 'nullable|file|max:5120',
            'is_resubmit'     => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // =========================
        // 3. UPLOAD FUNCTION
        // =========================
        $storeFile = function ($file, $folder) {

            $path = public_path($folder);

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $original = $file->getClientOriginalName();
            $name     = pathinfo($original, PATHINFO_FILENAME);
            $ext      = $file->getClientOriginalExtension();

            $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name);

            $filename = $name . '.' . $ext;
            $i = 1;

            while (file_exists($path.'/'.$filename)) {
                $i++;
                $filename = $name."($i).".$ext;
            }

            $file->move($path, $filename);

            return $folder.'/'.$filename;
        };

        // =========================
        // 4. FILE UPDATE
        // =========================
        if ($request->hasFile('file_path')) {
            $doc->file_path = $storeFile($request->file('file_path'), 'uploads/documents');
        }

        if ($need4m && $request->hasFile('file_4m_path')) {
            $doc->file_4m_path = $storeFile($request->file('file_4m_path'), 'uploads/documents/4m');
        }

        if (!$need4m) {
            $doc->file_4m_path = null;
        }

        // =========================
        // 5. UPDATE DATA
        // =========================
        $doc->update([
            'department_id'   => $request->department_id,
            'document_number' => $request->document_number,
            'document_type'   => $request->document_type,
            'submission_type' => $request->submission_type,
            'document_title'  => $request->document_title,
            'reason'          => $request->reason,
            'need_4m'         => $need4m,
            // ❌ status TIDAK DIUBAH
        ]);

        if ($isResubmit && in_array($doc->status, ['Returned by SPV', 'Returned by MR'])) {

        $doc->status = 'Resubmitted';

        $doc->save();
        }

        // =========================
        // 6. REFRESH COPY
        // =========================
        DocumentCopy::where('registration_id', $doc->id)->delete();

        if ($request->has('share_dept')) {
            foreach ($request->share_dept as $i => $deptId) {

                $qty = $request->share_qty[$i] ?? 0;

                if ($deptId && $qty > 0) {
                    DocumentCopy::create([
                        'registration_id' => $doc->id,
                        'department_id'   => $deptId,
                        'qty'             => $qty,
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Update failed',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function resubmit(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $doc = DocumentRegistration::findOrFail($id);

        // panggil logic update (biar tidak duplikat)
        $this->update($request, $id);

        // reload fresh data
        $doc->refresh();

        // =========================
        // UPDATE STATUS
        // =========================
        $doc->status = 'Resubmitted';
        $doc->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document resubmitted successfully'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Resubmit failed',
            'error'   => $e->getMessage()
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
        ->where('dc.document_id', $document_id)
        ->whereNull('dc.date') // hanya ambil yang date masih null
        ->select(
            'dc.id',
            'dc.department_name', // ambil nama department
            'dc.qty',
            'dc.date'
        )
        ->get();

    return response()->json($copies);
}


public function saveSocialize(Request $request)
{
    $dates = $request->input('dates', []);
    $documentId = $request->input('document_id');
    $photos = $request->file('photos', []);

    DB::beginTransaction();
    try {
        foreach ($dates as $id => $date) {
            $updateData = [
                'date' => $date,
                'socialized_by' => auth()->id(),
                'updated_at' => now()
            ];

            if (isset($photos[$id])) {
                $file = $photos[$id];

                // Buat nama file unik: socialize_{id}_{timestamp}.{ext}
                $extension = $file->getClientOriginalExtension();
                $filename = 'socialize_' . $id . '_' . time() . '.' . $extension;

                $destination = '/home/abimany3/public_html/socialized';
                if (!file_exists($destination)) {
                    mkdir($destination, 0775, true);
                }

                $file->move($destination, $filename);

                // Simpan hanya filename (dengan ekstensi) di tabel
                $updateData['photo'] = $filename;
            }

            DB::table('document_copies')->where('id', $id)->update($updateData);
        }

        // Update status dokumen
        $totalCopies = DB::table('document_copies')->where('document_id', $documentId)->count();
        $filledCopies = DB::table('document_copies')
            ->where('document_id', $documentId)
            ->whereNotNull('date')
            ->count();

        $status = $filledCopies < $totalCopies ? 'Partially Socialized' : 'Closed';

        DB::table('documents')->where('id', $documentId)->update([
            'status' => $status,
            'updated_at' => now()
        ]);

        DB::commit();

        return response()->json(['success' => true, 'status' => $status]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}




public function approve($id)
{
    $doc = DocumentRegistration::findOrFail($id);

    // 1️⃣ Update status di tabel documents
    $doc->status = 'Approved';
    $doc->approved_by = auth()->id();
    $doc->approved_at = now();
    $doc->save();

    return response()->json([
        'success' => true,
        'message' => 'Document Registration sucesfully Approved.',
    ]);
}


public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $doc = DocumentRegistration::findOrFail($id);
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

public function returnDocument(Request $request, $id)
{
    $request->validate([
        'note' => 'required|string',
        'role' => 'required|in:spv,mr'
    ]);

    $doc = DocumentRegistration::findOrFail($id);

    DB::beginTransaction();

    try {

        // simpan log
        DocumentNote::create([
            'user_id'         => auth()->id(),
            'registration_id' => $doc->id,
            'note'            => $request->note,
            'role'            => $request->role,
        ]);

        // update status dinamis
        $doc->status = $request->role === 'spv'
            ? 'Returned by SPV'
            : 'Returned by MR';

        $doc->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document berhasil di-return',
            
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal return document',
             'error'   => $e->getMessage() // 🔥 INI PENTING
        ], 500);
    }
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

     // Ambil semua subscription dari user yang ditargetkan
$subscriptions = DB::table('subscriptions')
    ->where('user_id', $doc->created_by)
    ->get();

$webPush = new WebPush([
    'VAPID' => [
        'subject' => 'mailto:it2@asnusantara.co.id',
        'publicKey' => env('VAPID_PUBLIC_KEY'),
        'privateKey' => env('VAPID_PRIVATE_KEY'),
    ],
    'automaticPadding' => true
]);

foreach ($subscriptions as $subRow) {
    $subData = json_decode($subRow->subscription, true);

    if (!$subData) continue; // skip jika subscription tidak valid

    $sub = Subscription::create($subData);

    $reviewName = $doc->review->name ?? 'User'; // fallback jika null

    $webPush->sendOneNotification($sub, json_encode([
        'title' => '📑 Dokumen Sedang Direview | Abimanyu Live',
        'body'  => "Dokumen Anda Dengan Nomor {$doc->document_number} sedang direview oleh {$reviewName}",
        'url'   => url("/mr/document/{$doc->id}/detail")
    ]));
}

// Kirim semua notifikasi
$webPush->flush();

    return response()->json([
        'success' => true,
        'message' => 'Document set to be Under Review.',
        'document_number' => $doc->document_number
    ]);
}

public function authorized($id)
{
    DB::beginTransaction();

    try {

        $doc = DocumentRegistration::findOrFail($id);

        // =========================
        // 1. UPDATE REGISTRATION
        // =========================
        $doc->update([
            'status'        => 'Published',
            'authorized_by' => auth()->id(),
            'authorized_at' => now(),
        ]);

        // =========================
        // 2. AMBIL REVISION NUMBER
        // =========================
        $revision = DocumentRevision::where('registration_id', $doc->id)
            ->latest()
            ->first();

        $revisionNumber = $revision->revision_number ?? 0;

        $submissionType = $doc->submission_type;

        // =========================
        // 3. REMARK
        // =========================
        $remark = match ($submissionType) {
            'New Release' => 'New Release',
            'Revision'    => 'Revision',
            'Obsolete'    => 'Obsolete',
            default       => '-',
        };

        // =========================
        // 4. AMBIL DEPARTMENT USER
        // =========================
        $user = User::with('departments')->find($doc->created_by);
        $deptFrom = optional($user->departments->first())->id;

        // =========================
        // 5. CHECK DOCUMENT EXIST
        // =========================
        $document = Document::where('document_number', $doc->document_number)
            ->lockForUpdate()
            ->first();

        if ($document) {

            // =========================
            // UPDATE DOCUMENT
            // =========================
            $document->update([
                'registration_id' => $doc->id,
                'document_type'   => $doc->document_type,
                'remark'          => $remark,
                'document_title'  => $doc->document_title,
                'current_version' => $revisionNumber,
                'file_path'       => $doc->file_path,
                'file_4m_path'    => $doc->file_4m_path,
                'is_active'       => $submissionType === 'Obsolete' ? 0 : 1,
                'dept_from'       => $deptFrom,
                'dept_to'         => $doc->department_id,
                'submitted_by'    => $doc->created_by,
                'submitted_at'    => $doc->created_at,
                'published_by'    => auth()->id(),
                'published_at'    => now(),
            ]);

        } else {

            // =========================
            // CREATE (FIRST TIME)
            // =========================
            Document::create([
                'registration_id' => $doc->id,
                'document_number' => $doc->document_number,
                'document_type'   => $doc->document_type,
                'remark'          => $remark,
                'document_title'  => $doc->document_title,
                'current_version' => $revisionNumber,
                'file_path'       => $doc->file_path,
                'file_4m_path'    => $doc->file_4m_path,
                'is_active'       => $submissionType === 'Obsolete' ? 0 : 1,
                'dept_from'       => $deptFrom,
                'dept_to'         => $doc->department_id,
                'submitted_by'    => $doc->created_by,
                'submitted_at'    => $doc->created_at,
                'published_by'    => auth()->id(),
                'published_at'    => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document updated (no versioning)'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to authorize document',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function edit($id)
    {
         $document = DocumentRegistration::findOrFail($id);
        $departments = Department::whereNotIn('id', [2,3,5,11,15,18,20,21])
    ->orderBy('name')
    ->get();
        return view('mr.edit-document', compact('departments','document'));
    }

public function show($id)
{
    
    $document = DocumentRegistration::findOrFail($id);
    return view('mr.detail-document', compact('document'));
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




public function destroy($id)
{
    try {
        $document = DocumentRegistration::findOrFail($id);
        $docNumber = $document->document_number;

        // =========================
        // HAPUS FILE UTAMA
        // =========================
        if ($document->file_path) {
            $filePath = public_path($document->file_path);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // =========================
        // HAPUS FILE 4M
        // =========================
        if ($document->file_4m_path) {
            $file4mPath = public_path($document->file_4m_path);

            if (file_exists($file4mPath)) {
                unlink($file4mPath);
            }
        }

        // =========================
        // HAPUS RELASI
        // =========================
        $document->copies()->delete();
        $document->revision()->delete(); // kalau ada relasi revision

        // =========================
        // HAPUS DATA
        // =========================
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
