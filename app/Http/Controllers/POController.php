<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;


class POController extends Controller
{
     public function index() {
        $suppliers = Supplier::orderBy('name')->get();
        return view('purchasing.purchase-order', compact('suppliers'));
    }

     public function data(Request $request)
{
    $user = auth()->user();
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name')->toArray(); // asumsi relasi `roles` tersedia

  $query = PurchaseOrder::with(['createdBy', 'approved', 'authorized', 'verified', 'supplier'])
   ->orderByRaw("FIELD(status, 'Draft', 'Revision', 'Approved', 'Authorized', 'Verified', 'Partially Received', 'Closed', 'Rejected')")
    ->orderBy('created_at', 'asc');



    if ($request->order_number) {
        $query->where('order_number', 'like', '%' . $request->order_number . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
}

if ($request->supplier) {
    $query->where('supplier_code', $request->supplier);
}


    if ($request->order_date) {
    $dates = explode(' to ', $request->order_date);
    if (count($dates) === 2) {
        $start = $dates[0];
        $end = $dates[1];
        $query->whereBetween('order_date', [$start, $end]);
    } else {
        // Hanya satu tanggal
        $query->whereDate('order_date', $dates[0]);
    }
}


    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->createdBy && $row->createdBy->id === Auth::id();
    $orderNumber = $row->order_number ?? 'Unknown';
    $detail_url = route('purchasing.po.show', ['id' => $row->id]);

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
        <button onclick="revisionPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Revision
        </button>';
}

   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Draft' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Supervisor Special Access']);
    }) &&
    $userDepartments->contains('Purchasing')
) {

        $actionButtons .= '
        <button onclick="approvePO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approve
        </button>
        <button onclick="rejectPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

     // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Approved' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Manager Special Access']);
    }) &&
    $userDepartments->contains('Purchasing')
) {

        $actionButtons .= '
        <button onclick="authorizedPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Authorized
        </button>
        <button onclick="rejectPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }

    if (
    $row->status === 'Authorized' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['BOD Special Access']);
    })
) {

        $actionButtons .= '
        <button onclick="verifiedPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Verified
        </button>
        <button onclick="rejectPO(' . $id . ', \'' . $orderNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
            <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Reject
        </button>';
    }



    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('created_by', function ($row) {
    return $row->createdBy ? $row->createdBy->name : '-';
})
->addColumn('approved_by', function ($row) {
    return $row->approved ? $row->approved->name : '-';
})
->addColumn('authorized_by', function ($row) {
    return $row->authorized ? $row->authorized->name : '-';
})
->addColumn('verified_by', function ($row) {
    return $row->verified ? $row->verified->name : '-';
})

 ->editColumn('status', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Draft') {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Draft</span>';
    } elseif ($row->status === 'Approved') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Approved</span>';
    } elseif ($row->status === 'Authorized') {
        return '<span class="bg-blue-500 ' . $commonClasses . '">Authorized</span>';
    } elseif ($row->status === 'Verified') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Verified</span>';
    } elseif ($row->status === 'Partially Received') {
        return '<span class="bg-purple-400 ' . $commonClasses . '">Partially Received</span>';
    } elseif ($row->status === 'Closed') {
        return '<span class="bg-teal-400 ' . $commonClasses . '">Closed</span>';
    } elseif ($row->status === 'Rejected') {
        return '<span class="bg-red-600 ' . $commonClasses . '">Rejected</span>';
    }
})

->editColumn('order_number', function ($row) {
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
            case 'Partially Received':
            $colorClass = 'bg-purple-400';
            break;
        case 'Closed':
            $colorClass = 'bg-teal-400';
            break;
        default:
            $colorClass = 'bg-gray-300';
    }

    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->order_number . '</span>';
})

        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
       ->editColumn('pkp', function ($row) {
    return $row->pkp == 1 ? 'PKP' : '';
})

        ->rawColumns(['action', 'status','order_number', 'pkp'])
        ->make(true);
}

     public function create()
    {
         $purchaseRequests = PurchaseRequest::with('supplier')->get();
        $suppliers = Supplier::all(); // Atau query lain sesuai kebutuhan
        return view('purchasing.create-purchase-order-v2', compact('suppliers','purchaseRequests'));
    }

 public function store(Request $request)
{
    $request->validate([
        'purchase_request_id'    => 'required|array',
        'purchase_request_id.*'  => 'required|integer|exists:purchase_request_items,id', // cek disini

        'order_date'    => 'required|date',
        'delivery_date' => 'required|date',
        'supplier_code' => 'required|string|exists:suppliers,code',
        'top'           => 'nullable|string|max:50',
        'pkp'           => 'nullable|boolean',
        'note'          => 'nullable|string',

        'subtotal'      => 'nullable|numeric',
        'ppn'           => 'nullable|numeric',
        'pph'           => 'nullable|numeric',
        'netto'         => 'nullable|numeric',

        'article_code'  => 'required|array',
        'article_code.*'=> 'required|string',

        'qty'           => 'required|array',
        'qty.*'         => 'required|numeric|min:1',

        'price'         => 'required|array',
        'price.*'       => 'required|numeric|min:0',

        'discount'      => 'nullable|numeric|min:0',
        'use_ppn'       => 'nullable|boolean',
        'use_pph'       => 'nullable|boolean',
    ]);

    $itemCount = count($request->article_code);
    if (
        $itemCount !== count($request->qty) ||
        $itemCount !== count($request->price) ||
        $itemCount !== count($request->purchase_request_id)
    ) {
        return response()->json(['error' => 'Data item tidak konsisten.'], 422);
    }

    DB::beginTransaction();

    try {
        $orderNumber = $this->generateOrderNumber();

        $po = PurchaseOrder::create([
            'order_number'     => $orderNumber,
            'order_date'       => $request->order_date,
            'delivery_date'    => $request->delivery_date,
            'supplier_code'    => $request->supplier_code,
            'top'              => $request->top,
            'pkp'              => $request->pkp ? 1 : 0,
            'note'             => $request->note,
            'discount'         => $request->discount ?? 0,
            'subtotal'         => $request->subtotal ?? 0,
            'ppn'              => $request->ppn ?? 0,
            'pph'              => $request->pph ?? 0,
            'netto'            => $request->netto ?? 0,
            'use_ppn'          => $request->use_ppn ? 1 : 0,
            'use_pph'          => $request->use_pph ? 1 : 0,
            'created_by'       => Auth::id(),
        ]);

        for ($i = 0; $i < $itemCount; $i++) {
            $purchaseRequestItemId = $request->purchase_request_id[$i];
            $code = $request->article_code[$i];
            $qty = $request->qty[$i];
            $price = $request->price[$i];

            PurchaseOrderItem::create([
                'purchase_order_id'   => $po->id,
                'purchase_request_id' => $purchaseRequestItemId,
                'article_code'        => $code,
                'qty'                 => $qty,
                'price'               => $price,
                'total'               => $qty * $price,
            ]);

           PurchaseRequestItem::where('id', $purchaseRequestItemId)
    ->increment('qty_po', $qty);

// Ambil purchase_request_id dari purchase_request_items
$purchaseRequestId = PurchaseRequestItem::where('id', $purchaseRequestItemId)
    ->value('purchase_request_id');

// Cek semua item di purchase request
$allItems = PurchaseRequestItem::where('purchase_request_id', $purchaseRequestId)->get();

$isFullyOrdered = $allItems->every(function ($item) {
    return $item->qty_po >= $item->qty; // semua item terpenuhi
});

$isPartiallyOrdered = $allItems->contains(function ($item) {
    return $item->qty_po > 0 && $item->qty_po < $item->qty; // ada sebagian yg baru di-PO
});

$status = 'Draft';

if ($isFullyOrdered) {
    $status = 'Full Order';
} elseif ($isPartiallyOrdered) {
    $status = 'Partially Order';
}

PurchaseRequest::where('id', $purchaseRequestId)->update(['status' => $status]);

}

        DB::commit();
        return response()->json(['message' => 'PO created successfully', 'id' => $po->id], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Gagal menyimpan PO', 'details' => $e->getMessage()], 500);
    }
}




   private function generateOrderNumber()
{
   
        $prefix = 'PO';

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
    $last = PurchaseOrder::where('order_number', 'like', "$formatPrefix-%")
        ->orderBy('order_number', 'desc')
        ->first();

    if ($last) {
        // Ambil nomor urut terakhir dari string request_number
        $lastNumber = (int) substr($last->order_number, strrpos($last->order_number, '-') + 1);
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '00001';
    }

    return $formatPrefix . '-' . $newNumber;
}

// PurchaseOrderController.php
public function getLastPrice($article_code)
{
    $lastPO = DB::table('purchase_order_items')
        ->where('article_code', $article_code)
        ->orderByDesc('id') // atau 'created_at'
        ->first();

    return response()->json([
        'price' => $lastPO ? $lastPO->price : null,
    ]);
}

public function getPriceHistory($article_code)
{
    $rawHistory = DB::table('purchase_order_items')
        ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
        ->join('suppliers', 'suppliers.code', '=', 'purchase_orders.supplier_code')
        ->where('purchase_order_items.article_code', $article_code)
        ->select(
            'purchase_order_items.id',
            'purchase_orders.order_number',
            'purchase_orders.order_date',
            'suppliers.name as supplier_name',
            'purchase_order_items.price'
        )
        ->orderByDesc('purchase_orders.order_date')
        ->get();

    // Filter: harga sama dan supplier sama → hanya ambil yang order_date terbaru
    $filtered = collect();

    $seen = [];

    foreach ($rawHistory as $item) {
        $key = $item->supplier_name . '|' . $item->price;
        if (!isset($seen[$key])) {
            $filtered->push($item);
            $seen[$key] = true;
        }
    }

    return response()->json($filtered);
}

public function approve($id)
{
    $po = PurchaseOrder::findOrFail($id);
    $po->status = 'Approved';
    $po->approved_by = auth()->id();
    $po->approved_at = now();
    $po->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Order Approved.',
        'order_number' => $po->order_number
    ]);
}

public function authorized($id)
{
    $po = PurchaseOrder::findOrFail($id);
    $po->status = 'Authorized';
    $po->authorized_by = auth()->id();
    $po->authorized_at = now();
    $po->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Order Authorized.',
        'order_number' => $po->order_number
    ]);
}

public function verified($id)
{
    $po = PurchaseOrder::findOrFail($id);
    $po->status = 'Verified';
    $po->verified_by = auth()->id();
    $po->verified_at = now();
    $po->save();

    return response()->json([
        'success' => true,
        'message' => 'Purchase Order Verified.',
        'order_number' => $po->order_number
    ]);
}

public function reject(Request $request, $id)
{
    $request->validate([
        'rejected_reason' => 'required|string|max:1000'
    ]);

    $po = PurchaseOrder::findOrFail($id);
    $po->status = 'Rejected';
    $po->rejected_reason = $request->input('rejected_reason', 'No reason provided.');
    $po->reject_by = auth()->id();
    $po->reject_at = now();
    $po->save();

     return response()->json([
        'success' => true,
        'message' => 'Purchase Order rejected successfully.',
        'order_number' => $po->order_number
    ]);
}

 public function show($id)
{
  $po = PurchaseOrder::with('items.article','items.request')->findOrFail($id);
return view('purchasing.detail-purchase-order', compact('po'));

}

// PurchaseOrderController.php
public function getBySupplier($code)
{
    $pos = PurchaseOrder::where('supplier_code', $code)
        ->whereHas('items', function ($query) {
            $query->whereColumn('qty', '>', 'qty_received');
        })
        ->orderBy('order_date', 'desc')
        ->get(['id', 'order_number', 'order_date']);

    return response()->json($pos);
}


public function getPoItems(Request $request, $poId)
{
    $supplierCode = $request->get('supplier_code');

    // Ambil PO dan relasi item + artikel
    $po = PurchaseOrder::with(['items.article.type.warehouse'])->findOrFail($poId);

    // Filter item sesuai supplier dan qty
    $filteredItems = $po->items->filter(function ($item) use ($supplierCode) {
        return $item->supplier_code == $supplierCode && $item->qty > $item->qty_received;
    });

    if ($filteredItems->isEmpty()) {
        return response()->json([]);
    }

    // Mapping response
    $items = $filteredItems->map(function ($item) {
        return [
            'id' => $item->id,
            'article_code' => $item->article_code ?? '',
            'article_name' => $item->article->description ?? '',
            'qty_po' => $item->qty,
            'qty_received' => $item->qty_received,
            'uom' => $item->article->unit ?? '', // Ambil dari artikel
            'destination' => $item->article->type->warehouse->name ?? '', // Ambil dari relasi
            'destination_id' => $item->article->type->warehouse->id ?? '', // Ambil dari relasi
        ];
    });

    return response()->json($items);
}







}
