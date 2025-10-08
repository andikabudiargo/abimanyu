<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseRequest;
use App\Models\Department;
use App\Models\PurchaseRequestItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PRController extends Controller
{
     public function index() {
        $departments = Department::orderBy('name')->get();
        return view('purchasing.purchase-request', compact('departments'));
    }

     public function data(Request $request)
{
    $query = PurchaseRequest::with(['owner.departments', 'reject'])
    ->orderByRaw("FIELD(status, 'Draft', 'Revision', 'Approved', 'Authorized', 'Verified', 'Partially Order', 'Full Order', 'Closed', 'Rejected')")
    ->orderBy('created_at', 'asc');



    if ($request->request_number) {
        $query->where('request_number', 'like', '%' . $request->request_number . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
    }

   if ($request->filled('department')) {
    $query->whereHas('owner.departments', function ($q) use ($request) {
        $q->where('id', $request->department);
    });
    }

    if ($request->filled('order_type')) {
    $query->where('order_type', 'like', '%' . trim($request->order_type) . '%');
    }
    
    if ($request->request_date) {
    $dates = explode(' to ', $request->request_date);
    if (count($dates) === 2) {
        $start = $dates[0];
        $end = $dates[1];
        $query->whereBetween('request_date', [$start, $end]);
    } else {
        // Hanya satu tanggal
        $query->whereDate('request_date', $dates[0]);
    }
}

     // 🔹 Filter Department berdasarkan role
$user = Auth::user();
$userRoles = $user->roles->pluck('name');
$userDepts = $user->departments->pluck('name');
$userDeptIds = $user->departments->pluck('id');

// Jika bukan Superuser DAN bukan Purchasing
if (
    !$userDepts->contains('Purchasing') && 
    !$userRoles->contains('Superuser') && 
    !$userDepts->contains('Board of Director')
) {
    // Hanya tampilkan PR sesuai departemen user
    $userDeptIds = $user->departments->pluck('id');

    $query->whereHas('owner.departments', function ($q) use ($userDeptIds) {
        $q->whereIn('id', $userDeptIds);
    });
}


    return DataTables::of($query)
    ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $ownerDepartments = optional($row->owner?->departments)->pluck('name') ?? collect();
    $hasSameDepartment = $userDepartments->intersect($ownerDepartments)->isNotEmpty();
    $isOwner = $row->owner && $row->owner->id === Auth::id();
    $requestNumber = $row->request_number ?? 'Unknown';
    $detail_url = route('purchasing.pr.show', ['id' => $row->id]);

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
        <a href="#" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
        </a>
        <button onclick="confirmDelete(' . $row->id . ')" 
            class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
    </button>';
}


    if ($isOwner && $row->status !== 'Draft') {
    $actionButtons .= '
        <button onclick="revisionPO(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Revision
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
        <button onclick="approvePR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
        </button>
        <button onclick="rejectPR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

     // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
   $row->status === 'Approved' &&
    $hasSameDepartment &&
    $userRoles->contains('Manager Special Access')
) {

        $actionButtons .= '
        <button onclick="authorizedPR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Authorized
        </button>
        <button onclick="rejectPR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

    if (
   $row->status === 'Authorized' &&
    $userRoles->contains('BOD Special Access')
) {

        $actionButtons .= '
        <button onclick="verifiedPR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Verified
        </button>
        <button onclick="rejectPR(' . $id . ', \'' . $requestNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }



    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

     ->editColumn('status', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Draft') {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Draft</span>';
    } elseif ($row->status === 'Posted') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Approved</span>';
    } elseif ($row->status === 'Revision') {
        return '<span class="bg-blue-500 ' . $commonClasses . '">Authorized</span>';
    } elseif ($row->status === 'Verified') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Verified</span>';
    } elseif ($row->status === 'Partially Order') {
        return '<span class="bg-pink-400 ' . $commonClasses . '">Partially Order</span>';
    } elseif ($row->status === 'Full Order') {
        return '<span class="bg-purple-400 ' . $commonClasses . '">Full Order</span>';
    } elseif ($row->status === 'Closed') {
        return '<span class="bg-teal-400 ' . $commonClasses . '">Closed</span>';
    } elseif ($row->status === 'Rejected') {
        return '<span class="bg-red-600 ' . $commonClasses . '">Rejected</span>';
    }
})

 ->editColumn('order_type', function ($row) {
    $dot = '<span class="w-3 h-3 rounded-full inline-block mr-2"></span>';

    switch ($row->order_type) {
        case 'Standard':
            $color = 'bg-indigo-600';
            $text = 'Standard';
            break;
        case 'Target Sales Order':
            $color = 'bg-teal-600';
            $text = 'Target Sales Order';
            break;
        case 'GA Request':
            $color = 'bg-pink-600';
            $text = 'General Affair Request';
            break;
        default:
            $color = 'bg-gray-300';
            $text = $row->order_type;
    }

    return '<div class="flex items-center">' 
        . str_replace('inline-block', $color, $dot) 
        . '<span>' . $text . '</span></div>';
})



->addColumn('created_by', function ($row) {
    return $row->owner->name ?? '-';
})

->addColumn('approved_by', function ($row) {
    return $row->approve->name ?? '-';
})

->addColumn('authorized_by', function ($row) {
    return $row->authorized->name ?? '-';
})

->addColumn('verified_by', function ($row) {
    return $row->verified->name ?? '-';
})

->addColumn('rejected_by', function ($row) {
    return $row->reject->name ?? '-';
})

->addColumn('department', function ($row) {
    if ($row->owner && $row->owner->departments) {
        return $row->owner->departments->pluck('name')->join(', ');
    }
    return '-';
})



  ->editColumn('request_number', function ($row) {
    $colorClass = '';
    switch ($row->status) {
        case 'Draft':
            $colorClass = 'bg-gray-500';
            break;
        case 'Approved':
            $colorClass = 'bg-yellow-500';
            break;
        case 'Authorized':
            $colorClass = 'bg-blue-500';
            break;
        case 'Verified':
            $colorClass = 'bg-green-500';
            break;
        case 'Partially Order':
            $colorClass = 'bg-pink-400';
            break;
        case 'Full Order':
            $colorClass = 'bg-purple-400';
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
    return $row->created_at 
        ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') 
        : '-';
})
         ->editColumn('approved_at', function ($row) {
    return $row->approved_at 
        ? \Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i') 
        : '-';
})

->editColumn('verified_at', function ($row) {
    return $row->verified_at 
        ? \Carbon\Carbon::parse($row->verified_at)->format('d-m-Y H:i') 
        : '-';
})


        ->editColumn('authorized_at', function ($row) {
    return $row->authorized_at 
        ? \Carbon\Carbon::parse($row->authorized_at)->format('d-m-Y H:i') 
        : '-';
})


         ->editColumn('rejected_at', function ($row) {
            return \Carbon\Carbon::parse($row->rejected_at)->format('d-m-Y H:i');
        })
        
         ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action','status', 'request_number', 'order_type'])
        ->make(true);
}

  public function create()
{
    return view('purchasing.create-purchase-request');
}

public function getArticles(Request $request)
{
    $perPage = 50;
    $page = $request->get('page', 1);
    $search = $request->get('q', '');

    $query = DB::table('articles')
        ->leftJoin('stocks', 'articles.article_code', '=', 'stocks.article_code')
        ->select(
            'articles.article_code',
            'articles.description',
            'articles.unit as uom',
            DB::raw('COALESCE(stocks.qty, 0) as stock')
        );

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('articles.article_code', 'like', "%{$search}%")
              ->orWhere('articles.description', 'like', "%{$search}%");
        });
    }

    $articles = $query->orderBy('articles.article_code')
        ->skip(($page - 1) * $perPage)
        ->take($perPage + 1) // ambil 1 ekstra buat cek "more"
        ->get();

    $more = $articles->count() > $perPage;

    return response()->json([
        'items' => $articles->take($perPage),
        'pagination' => ['more' => $more]
    ]);
}




  public function store(Request $request)
{
    $request->validate([
        'request_date' => 'required|date',
        'order_type' => 'required|string',
        'article_code' => 'required|array|min:1',
        'article_code.*' => 'required|string',
        'qty.*' => 'required|numeric|min:1',
        'sales_order_id' => 'required_if:order_type,Target Sales Order',
        'ga_request_id' => 'required_if:order_type,GA Procurement',
    ]);

    DB::beginTransaction();
    try {
        $requestNumber = $this->generateRequestNumber($request->order_type);

        $purchaseRequest = PurchaseRequest::create([
            'request_number' => $requestNumber,
            'created_by' => Auth::id(),
            'request_date' => $request->request_date,
            'order_type' => $request->order_type,
            'stock_needed_at' => $request->stock_needed_at,
            'sales_order_id' => $request->sales_order_id,
            'ga_request_id' => $request->ga_request_id,
            'pr_note' => $request->pr_note,
        ]);

        foreach ($request->article_code as $i => $code) {
            PurchaseRequestItem::create([
                'purchase_request_id' => $purchaseRequest->id,
                'article_code' => $code,
                'qty' => $request->qty[$i],
                'note' => $request->note[$i] ?? null,
            ]);
        }

        DB::commit();

        return response()->json(['message' => 'Purchase Request saved successfully.'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

   private function generateRequestNumber($orderType)
{
    // Tentukan prefix berdasarkan tipe order
    if ($orderType === 'sales_order') {
        $prefix = 'PRTSO';
    } elseif ($orderType === 'ga_request') {
        $prefix = 'PRGA';
    } else {
        $prefix = 'PR';
    }

    // Daftar bulan romawi
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];

    $bulan = (int) date('n');
    $tahun = date('Y');
    $formatPrefix = $prefix . '-ASN-' . $tahun . '-' . $bulanRomawi[$bulan];

    // Ambil nomor urut terakhir berdasarkan prefix
    $last = PurchaseRequest::where('request_number', 'like', "$formatPrefix-%")
        ->orderBy('request_number', 'desc')
        ->first();

    if ($last) {
        // Ambil nomor urut terakhir dari string request_number
        $lastNumber = (int) substr($last->request_number, strrpos($last->request_number, '-') + 1);
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '00001';
    }

    return $formatPrefix . '-' . $newNumber;
}

public function show($id)
{
  $pr = PurchaseRequest::with('items.article','items.poItems', 'purchaseOrders.createdBy')->findOrFail($id);
return view('purchasing.detail-purchase-request', compact('pr'));

}

public function bySupplier()
{
    $suppliers = Supplier::all();
    return view('purchase_requests.by_supplier', compact('suppliers'));
}

public function getBySupplier($supplierCode)
{
    Log::info("Fetching PR for supplierCode: $supplierCode");

    // Ambil article codes, trim & uppercase
    $articleCodes = Article::where('supplier_code', $supplierCode)
        ->pluck('article_code')
        ->map(fn($code) => strtoupper(trim($code)))
        ->toArray();

    Log::info("Normalized article codes: ", $articleCodes);

    if (empty($articleCodes)) {
        return response()->json([]);
    }

    // Ambil PR items dengan artikel yang sudah dinormalisasi, pakai raw untuk trim & upper pada kolom DB
    $prItems = PurchaseRequestItem::whereRaw('UPPER(TRIM(article_code)) IN (' . implode(',', array_map(fn($c) => "'$c'", $articleCodes)) . ')')
        ->whereRaw('qty > IFNULL(qty_po, 0)')
        ->get();

    Log::info("Filtered PR Items: ", $prItems->toArray());

    if ($prItems->isEmpty()) {
        return response()->json([]);
    }

    // Ambil ID PR unik
    $purchaseRequestIds = $prItems->pluck('purchase_request_id')->unique()->toArray();

    Log::info("Filtered PR IDs: ", $purchaseRequestIds);

    // Ambil data PR sesuai ID
    $purchaseRequests = PurchaseRequest::whereIn('id', $purchaseRequestIds)
        ->select('id', 'request_number', 'created_at')
        ->get();

    return response()->json($purchaseRequests);
}




public function getByIds(Request $request)
{
    $ids = explode(',', $request->input('ids'));
    $supplierCode = $request->input('supplier_code');

    $items = \App\Models\PurchaseRequestItem::with(['request', 'article'])
        ->whereIn('purchase_request_id', $ids)
        ->whereHas('article', function ($query) use ($supplierCode) {
            $query->where('supplier_code', $supplierCode);
        })
        ->get()
        ->filter(function ($item) {
            // Hitung qty PO yang sudah dibuat dari PR dan article yang sama
            $poQty = DB::table('purchase_order_items')
                ->where('purchase_request_id', $item->purchase_request_id)
                ->where('article_code', $item->article_code)
                ->sum('qty');

            return $poQty < $item->qty;
        })
        ->map(function ($item) {
            $poQty = DB::table('purchase_order_items')
                ->where('purchase_request_id', $item->purchase_request_id)
                ->where('article_code', $item->article_code)
                ->sum('qty');

            return [
                'purchase_request_id' => $item->purchase_request_id,
                'request_number' => $item->request->request_number,
                'article_code' => $item->article_code,
                'uom' => $item->uom,
                'article_description' => optional($item->article)->description ?? '-',
                'qty' => $item->qty - $poQty, // sisa qty
            ];
        })
        ->groupBy('purchase_request_id')
        ->filter(function ($itemsPerPR) {
            // Hanya tampilkan PR yang masih punya item sisa
            return $itemsPerPR->isNotEmpty();
        })
        ->flatten(1)
        ->values();

    return response()->json($items);
}

public function supplier()
{
    $prItems = DB::table('purchase_request_items')
        ->join('purchase_requests', 'purchase_request_items.purchase_request_id', '=', 'purchase_requests.id')
        ->join('articles', 'purchase_request_items.article_code', '=', 'articles.article_code')
        ->join('suppliers', 'articles.supplier_code', '=', 'suppliers.code')
        ->leftJoin('stocks', 'stocks.article_code', '=', 'articles.article_code') // join ke stock
        ->whereRaw('(purchase_request_items.qty - COALESCE(purchase_request_items.qty_po, 0)) > 0') // masih ada sisa qty
        ->whereIn('purchase_requests.status', ['Verified', 'Partially Order'])
        ->select(
            'suppliers.code as supplier_code',
            'suppliers.name as supplier_name',
            'purchase_requests.id as pr_id',
            'purchase_requests.request_number',
            'purchase_request_items.id as pr_item_id',
            'articles.article_code as article_code',
            'articles.description as article_name',
            'purchase_request_items.qty as requested_qty',
            'purchase_request_items.qty_po as qty_po', // ambil langsung dari kolom
            DB::raw('(purchase_request_items.qty - COALESCE(purchase_request_items.qty_po, 0)) as remaining_qty'),
            'articles.unit as uom',
            'purchase_requests.request_date as date',
            'stocks.qty as current_stock'
        )
        ->orderBy('suppliers.name')
        ->get()
        ->groupBy('supplier_code'); 

    return response()->json($prItems);
}


public function approve($id)
{
    $pr = PurchaseRequest::findOrFail($id);
    $pr->status = 'Approved';
    $pr->approved_by = auth()->id();
    $pr->approved_at = now();
    $pr->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Request Approved.',
        'request_number' => $pr->request_number
    ]);
}

public function authorized($id)
{
    $pr = PurchaseRequest::findOrFail($id);
    $pr->status = 'Authorized';
    $pr->authorized_by = auth()->id();
    $pr->authorized_at = now();
    $pr->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Request Authorized.',
        'request_number' => $pr->request_number
    ]);
}

public function verified($id)
{
    $pr = PurchaseRequest::findOrFail($id);
    $pr->status = 'Verified';
    $pr->verified_by = auth()->id();
    $pr->verified_at = now();
    $pr->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Request Verified.',
        'request_number' => $pr->request_number
    ]);
}

public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $pr = PurchaseRequest::findOrFail($id);
    $pr->status = 'Rejected';
    $pr->rejected_reason = $request->input('rejected_reason', 'No reason provided.');
    $pr->rejected_by = auth()->id();
    $pr->rejected_at = now();
    $pr->save();

     return response()->json([
        'success' => true,
        'message' => 'Purchase Request rejected successfully.',
        'request_number' => $pr->request_number
    ]);
}












}
