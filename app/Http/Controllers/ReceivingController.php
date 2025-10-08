<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;


class ReceivingController extends Controller
{
     public function index()
    {
         $suppliers = Supplier::all();
        return view('ppic.receiving', compact('suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all(); // Atau query lain sesuai kebutuhan
        return view('ppic.create-receiving', compact('suppliers'));
    }

     public function data(Request $request)
{
    $user = auth()->user();
    $userDepartments = $user->departments->pluck('name')->toArray();
    $userRoles = $user->roles->pluck('name')->toArray(); // asumsi relasi `roles` tersedia

  $query = Receiving::with(['creator', 'checker', 'verifier', 'supplier', 'purchaseOrder']);


    if ($request->receiving_number) {
        $query->where('receiving_number', 'like', '%' . $request->receiving_number . '%');
    }

    
  if ($request->order_number) {
    $query->whereHas('purchaseOrder', function ($q) use ($request) {
        $q->where('order_number', 'like', '%' . $request->order_number . '%');
    });
}


    if ($request->ap_number) {
        $query->where('ap_number', 'like', '%' . $request->ap_number . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
    }

     if ($request->received_date) {
    $dates = explode(' to ', $request->received_date);
    if (count($dates) === 2) {
        $start = $dates[0];
        $end = $dates[1];
        $query->whereBetween('received_date', [$start, $end]);
    } else {
        // Hanya satu tanggal
        $query->whereDate('received_date', $dates[0]);
    }
}

    if ($request->supplier) {
    $query->where('supplier_code', $request->supplier);
    }


    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->creator && $row->creator->id === Auth::id();
    $recNumber = $row->receiving_number ?? 'Unknown';
    $detail_url = route('ppic.receiving.show', ['id' => $row->id]);

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
        <button onclick="revisionPO(' . $id . ', \'' . $recNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Revision
        </button>';
}

   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Draft' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Supervisor Special Access']);
    }) &&
    $userDepartments->contains('PPIC - Logistic')
) {

        $actionButtons .= '
        <button onclick="approvePO(' . $id . ', \'' . $recNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Checked
        </button>';
    }

     // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Validate' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Manager Special Access']);
    }) &&
    $userDepartments->contains('PPIC')
) {

        $actionButtons .= '
        <button onclick="authorizedPO(' . $id . ', \'' . $recNumber . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Authorized
        </button>';
    }

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->addColumn('created_by', function ($row) {
    return $row->creator ? $row->creator->name : '-';
})
->addColumn('checked_by', function ($row) {
    return $row->checker ? $row->checker->name : '-';
})
->addColumn('verified_by', function ($row) {
    return $row->verifier ? $row->verifier->name : '-';
})
->addColumn('order_number', function ($row) {
            return $row->purchaseOrder ? $row->purchaseOrder->order_number : '-';
        })

 ->editColumn('status', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Draft') {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Draft</span>';
    } elseif ($row->status === 'Validated') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Validated</span>';
    } elseif ($row->status === 'Posted') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Posted</span>';
    }
})

 ->editColumn('receiving_number', function ($row) {
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
        default:
            $colorClass = 'bg-gray-300';
    }
    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->receiving_number . '</span>';
    })

        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })

        ->rawColumns(['action', 'status', 'receiving_number'])
        ->make(true);
}

   public function store(Request $request)
{
    $validated = $request->validate([
        'purchase_order_id'     => 'required|integer|exists:purchase_orders,id',
        'received_date'         => 'required|date',
        'delivery_order_number' => 'nullable|string|max:100',
        'delivery_order_date'   => 'nullable|date',
        'supplier_code'         => 'required|string|exists:suppliers,code',
        'note'                  => 'nullable|string|max:255',

        'po_item_ids'   => 'required|array',
        'po_item_ids.*' => 'required|integer|exists:purchase_order_items,id',

        'article_code'   => 'required|array',
        'article_code.*' => 'required|string|max:100',

        'qty_po'       => 'required|array',
        'qty_po.*'     => 'nullable|numeric|min:0',

        'qty_received'   => 'required|array',
        'qty_received.*' => 'required|numeric|min:0',

        'qty_free'       => 'required|array',
        'qty_free.*'     => 'nullable|numeric|min:0',

        'qty_total'       => 'required|array',
        'qty_total.*'     => 'nullable|numeric|min:0',

        'destination_id'       => 'required|array',
        'destination_id.*'     => 'nullable|numeric|min:0',

        'expired_date'       => 'required|array',
        'expired_date.*'     => 'nullable|date',
    ]);

    DB::beginTransaction();

    try {
        $receiving = \App\Models\Receiving::create([
            'receiving_number'      => $this->generateReceivingNumber(),
            'purchase_order_id'     => $validated['purchase_order_id'],
            'supplier_code'         => $validated['supplier_code'],
            'received_date'         => $validated['received_date'],
            'delivery_order_number' => $validated['delivery_order_number'],
            'delivery_order_date'   => $validated['delivery_order_date'],
            'note'                  => $validated['note'],
            'created_by'            => auth()->id(),
        ]);

        $poItems = \App\Models\PurchaseOrderItem::whereIn('id', $validated['po_item_ids'])->get()->keyBy('id');

      // Setelah $receiving dibuat
$receivingCode = $receiving->receiving_number;
$receivingQrFileName = $receivingCode . '.png';
$receivingQrPath = 'qrcodes/' . $receivingQrFileName;

// Generate QR sekali saja
$receivingQr = Builder::create()
    ->writer(new PngWriter())
    ->data($receivingCode)
    ->encoding(new Encoding('UTF-8'))
    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->size(300)
    ->margin(10)
    ->build();

// Simpan ke storage
Storage::disk('public')->put($receivingQrPath, $receivingQr->getString());
$receivingQrUrl = asset('storage/' . $receivingQrPath);

// Simpan QR path ke receiving
$receiving->qr_code = $receivingQrUrl;
$receiving->save();

// Lalu looping item
foreach ($validated['po_item_ids'] as $i => $poItemId) {
    $articleCode = $validated['article_code'][$i];
    $destinationId = $validated['destination_id'][$i];
    $expiredDate = $validated['expired_date'][$i];
    $qtyReceived = floatval($validated['qty_received'][$i]);
    $qtyFree     = floatval($validated['qty_free'][$i] ?? 0);
    $qtyTotal    = floatval($validated['qty_total'][$i] ?? 0);

   $article = \App\Models\Article::with('type.warehouse')
    ->where('article_code', $articleCode)
    ->first();
    $warehouseId = $article->warehouse_id ?? null;

    $poItem = $poItems[$poItemId];

    \App\Models\ReceivingItem::create([
        'receiving_id' => $receiving->id,
        'po_item_id'   => $poItemId,
        'destination_id'   => $destinationId,
        'article_code' => $articleCode,
        'qty_po'       => $poItem->qty,
        'qty_received' => $qtyReceived,
        'qty_free'     => $qtyFree,
        'qty_total'    => $qtyTotal,
        'qr_code'      => $receivingQrUrl, // Sama untuk semua item
        'expired_date' => $expiredDate
    ]);

    $poItem->qty_received += $qtyReceived;
    $poItem->save();

    // Update stok jika perlu
    if (in_array($article->article_type ?? '', ['RMP', 'CM1', 'CM2'])) {
        $existingStock = \App\Models\Stock::where('article_code', $articleCode)
            ->where('warehouse_id', $destinationId)
            ->first();

        if ($existingStock) {
            $existingStock->qty += $qtyTotal;
            $existingStock->save();
        } else {
            \App\Models\Stock::create([
                'article_code' => $articleCode,
                'qty'          => $qtyTotal,
                'warehouse_id' => $destinationId,
            ]);
        }
    }
}

$po = PurchaseOrder::find($request->purchase_order_id);
$allItems = $po->items()->get(); 

$fullyReceived = $allItems->every(function ($item) {
    return $item->qty_received >= $item->qty;
});
$po->status = $fullyReceived ? 'Closed' : 'Partially Received';
$po->save();


        DB::commit();
        return response()->json(['success' => true, 'message' => 'Receiving saved.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

   private function generateReceivingNumber()
{
   
        $prefix = 'LPB';

    // Daftar bulan romawi
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];

    $bulan = (int) date('n');
    $tahun = date('Y');
    $formatPrefix = $prefix . '-ASN-' .  $tahun . '-' . $bulanRomawi[$bulan] ;

    // Ambil nomor urut terakhir berdasarkan prefix
    $last = Receiving::where('receiving_number', 'like', "$formatPrefix-%")
        ->orderBy('receiving_number', 'desc')
        ->first();

    if ($last) {
        // Ambil nomor urut terakhir dari string request_number
        $lastNumber = (int) substr($last->receiving_number, strrpos($last->receiving_number, '-') + 1);
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '00001';
    }

    return $formatPrefix . '-' . $newNumber;
}

public function show($id)
{
    // Ambil Receiving + Items + Article + Destination (jika ada relasi)
    $receiving = Receiving::with(['items.article','items.destination'])->findOrFail($id);

    // Kirim langsung items tanpa diubah ke object biasa
    $items = $receiving->items;

    return view('ppic.detail-receiving', compact('receiving', 'items'));
}

}
