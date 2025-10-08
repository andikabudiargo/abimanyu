<?php

namespace App\Http\Controllers;

use App\Models\TransferOut;
use App\Models\TransferOutItem;
use App\Models\TransferIn;
use Yajra\DataTables\Facades\DataTables;
use App\Models\TransferInItems;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\ReceivingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransferOutController extends Controller
{
      public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('ppic.transfer_out');
    }

    public function data(Request $request)
{
    $query = TransferOut::with(['items', 'createdBy'])
    ->orderByRaw("FIELD(status, 'Draft', 'Revision', 'Posted')")
    ->orderBy('created_at', 'asc');

     if ($request->code) {
        $query->where('code', 'like', '%' . $request->code . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
    }


    if ($request->filled('transfer_type')) {
    $query->where('transfer_type', 'like', '%' . trim($request->transfer_type) . '%');
    }
    
    if ($request->date) {
    $dates = explode(' to ', $request->date);
    if (count($dates) === 2) {
        $start = $dates[0];
        $end = $dates[1];
        $query->whereBetween('date', [$start, $end]);
    } else {
        // Hanya satu tanggal
        $query->whereDate('date', $dates[0]);
    }
}

    return DataTables::of($query)
        ->addColumn('action', function ($row) {
            $id = $row->id;
            $dropdownId = 'dropdown-' . $id;

           $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->owner && $row->owner->id === Auth::id();
    $code = $row->code ?? 'Unknown';
    $detail_url = route('ppic.transfer-out.show', ['id' => $row->id]);
   
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
        <button onclick="revisionOUT(' . $id . ', \'' . $code . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i data-feather="repeat" class="w-4 h-4 inline mr-2"></i>Revision
        </button>';
}

   // Tampilkan tombol Approve/Reject jika status masih Pending dan role & dept cocok
if (
    $row->status === 'Draft' && // ✅ tambahkan ini
    $userRoles->contains(function ($role) {
        return in_array($role, ['Supervisor Special Access']);
    }) &&
    $userDepartments->contains('Production Planning & Inventory Control')
) {

        $actionButtons .= '
        <button onclick="approveOUT(' . $id . ', \'' . $code . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Approved
        </button>';
    }

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})
        ->editColumn('transfer_type', function ($row) {
    $dot = '<span class="w-3 h-3 rounded-full inline-block mr-2"></span>';

    switch ($row->transfer_type) {
        case 'Transfer Loading':
            $color = 'bg-red-600';
            $text = 'Transfer Loading';
            break;
        case 'Customer Return':
            $color = 'bg-yellow-600';
            $text = 'Material Return';
            break;
        case 'Sample':
            $color = 'bg-blue-600';
            $text = 'Sample';
            break;
            case 'Trial':
            $color = 'bg-purple-600';
            $text = 'Trial';
            break;
            case 'Mutasi':
            $color = 'bg-teal-600';
            $text = 'Mutasi';
            break;
        default:
            $color = 'bg-gray-300';
            $text = $row->transfer_type;
    }

    return '<div class="flex items-center">' 
        . str_replace('inline-block', $color, $dot) 
        . '<span>' . $text . '</span></div>';
})

  ->editColumn('status', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Draft') {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Draft</span>';
    } elseif ($row->status === 'Posted') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Posted</span>';
    } elseif ($row->status === 'Revision') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Revision</span>';
    }
})
->editColumn('code', function ($row) {
    $colorClass = '';
    switch ($row->status) {
        case 'Draft':
            $colorClass = 'bg-gray-500';
            break;
        case 'Posted':
            $colorClass = 'bg-green-500';
            break;
        case 'Revision':
            $colorClass = 'bg-yellow-500';
            break;
        default:
            $colorClass = 'bg-gray-300';
    }

    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->code . '</span>';
})

        ->editColumn('created_by', function ($row) {
           return $row->createdBy->name ?? '-';
        })

        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })

        ->rawColumns(['action', 'status', 'code', 'transfer_type'])

        ->make(true);
}


     public function create()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('ppic.create-transfer_out');
    }

      public function store(Request $request)
{
    $request->validate([
        'reference_number' => 'required',
        'date' => 'required|date',
        'transfer_type' => 'required',
        'items' => 'required|array|min:1',
    ]);

    DB::beginTransaction();
    try {
        $code = $this->generateTransferOutCode();

        // 1. Simpan Transfer Out Header
        $transferOut = TransferOut::create([
            'code' => $code,
            'reference_number' => $request->reference_number,
            'date' => $request->date,
            'transfer_type' => $request->transfer_type,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

       foreach ($request->items as $item) {
    Log::info("Processing item: ", $item);

    // Ambil transfer_in_code & transfer_in_item_code
    $transferInCode = $item['transfer_in_code']
        ?? TransferIn::where('id', TransferInItems::where('id', $item['transfer_in_item_id'] ?? null)->value('transfer_in_id'))->value('code');

    $transferInItemCode = $item['transfer_in_item_code']
        ?? TransferInItems::where('id', $item['transfer_in_item_id'] ?? null)->value('code');

      $qtyRequest = $item['qty'] ?? 0;
$qtyFinal = $qtyRequest; // default

if (!empty($item['transfer_in_item_id'])) {
    $inItem = TransferInItems::find($item['transfer_in_item_id']);
    if ($inItem) {
       $available = ($inItem->qty + $inItem->qty_return) - $inItem->qty_used;
        $qtyFinal = max(0, min($qtyRequest, $available)); // supaya tidak negatif
    }
}

    // Simpan detail Transfer Out
    $transferOutItem = new TransferOutItem([
        'transfer_in_code'       => $transferInCode,
        'transfer_in_item_code'  => $transferInItemCode,
        'article_code'           => $item['article_code'] ?? $item['code'] ?? null,
        'description'            => $item['description'] ?? $item['name'] ?? null,
        'qty'                    => $qtyFinal,
        'uom'                    => $item['uom'] ?? null,
        'min_package'            => $item['min_package'] ?? 1,
        'expired_date'           => $item['expired_date'] ?? null,
        'from_location'          => $item['from_location'] ?? null,
        'destination'            => $item['destination'] ?? null,
    ]);
    $transferOut->items()->save($transferOutItem);

    $remainingQty = $item['qty'];

    // === Case 1: TransferInItems berdasarkan ID ===
    if (!empty($item['transfer_in_item_id'])) {
        $inItem = TransferInItems::find($item['transfer_in_item_id']);
        if ($inItem) {
           $qtyReturned = $inItem->qty_return ?? 0;
$available = ($inItem->qty + $qtyReturned) - $inItem->qty_used;

$takeQty = min($available, $remainingQty);


            if ($takeQty > 0) {
                $inItem->qty_used += $takeQty;
                $inItem->save();

                $this->reduceStockByLocation($item['article_code'], $item['from_location'], $takeQty);
                Log::info("TransferInItem {$inItem->id} diupdate: qty_used +{$takeQty}");
                $remainingQty -= $takeQty;
            }
        }
    }

    // === Case 2: ReceivingItem berdasarkan ID ===
    if (!empty($item['receiving_item_id'])) {
        $receivingItems = ReceivingItem::find($item['receiving_item_id']);
        if ($receivingItems) {
            $available = $receivingItems->qty_received - $receivingItems->qty_used;
            $takeQty = min($available, $remainingQty);

            if ($takeQty > 0) {
                $receivingItems->qty_used += $takeQty;
                $receivingItems->save();

                $this->reduceStockByDestination($receivingItems->article_code, $receivingItems->destination_id, $takeQty);
                Log::info("ReceivingItem {$receivingItems->id} diupdate: qty_used +{$takeQty}");
                $remainingQty -= $takeQty;
            }
        }
    }

    // === Case 3: Fallback kalau tidak ada ID → update TransferInItems & ReceivingItem by article_code ===
    if ($remainingQty > 0 && !empty($item['transfer_in_code']) && !empty($item['article_code'])) {
        // Update TransferInItems
        $transferInId = TransferIn::where('code', $item['transfer_in_code'])->value('id');
        $inItems = TransferInItems::where('transfer_in_id', $transferInId)
            ->where('article_code', $item['article_code'])
            ->whereRaw('qty_used < (qty + qty_return)')
            ->orderBy('id')
            ->get();

        foreach ($inItems as $inItem) {
            if ($remainingQty <= 0) break;

            $available = ($inItem->qty + $inItem->qty_return) - $inItem->qty_used;
            $takeQty = min($available, $remainingQty);

            $inItem->qty_used += $takeQty;
            $inItem->save();

            $this->reduceStockByLocation($item['article_code'], $item['from_location'], $takeQty);
            Log::info("TransferInItem {$inItem->id} (fallback) diupdate: qty_used +{$takeQty}");

            $remainingQty -= $takeQty;
        }

        // Update ReceivingItem by article_code + from_location
        $warehouseId = Warehouse::where('name', $item['from_location'])->value('id');
        $receivingItems = ReceivingItem::where('article_code', $item['article_code'])
            ->where('destination_id', $warehouseId)
            ->whereRaw('qty_used < qty_received')
            ->orderBy('id')
            ->get();

        foreach ($receivingItems as $recv) {
            if ($remainingQty <= 0) break;

            $available = $recv->qty_received - $recv->qty_used;
            $takeQty = min($available, $remainingQty);

            $recv->qty_used += $takeQty;
            $recv->save();

            $this->reduceStockByDestination($recv->article_code, $recv->destination_id, $takeQty);
            Log::info("ReceivingItem {$recv->id} (fallback) diupdate: qty_used +{$takeQty}");

            $remainingQty -= $takeQty;
        }
    }
}


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Transfer Out berhasil disimpan.',
            'code'    => $code,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan: ' . $e->getMessage(),
        ], 500);
    }
}

private function reduceStockByDestination($articleCode, $warehouseId, $qty)
{
    if ($warehouseId && $articleCode && $qty > 0) {
        $stock = Stock::where('warehouse_id', $warehouseId)
            ->where('article_code', $articleCode)
            ->first();

        if ($stock) {
            $stock->qty = max(0, $stock->qty - $qty);
            $stock->save();
        }
    }
}

private function reduceStockByLocation($articleCode, $locationName, $qty)
{
    if ($locationName && $articleCode && $qty > 0) {
        $warehouse = Warehouse::where('name', $locationName)->first();
        if ($warehouse) {
            $this->reduceStockByDestination($articleCode, $warehouse->id, $qty);
        }
    }
}



        private function generateTransferOutCode()
        {
            // Generate Nomor Transfer In
       $tahun = now()->format('Y');
$bulan = now()->format('n');
$romawi = [
    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
][$bulan];

// Ambil kode terakhir yang mirip
$lastCode = DB::table('transfer_out')
    ->where('code', 'LIKE', "TROUT-ASN-{$tahun}-{$romawi}-%")
    ->orderBy('code', 'desc')
    ->value('code');

if ($lastCode) {
    preg_match('/(\d{4})$/', $lastCode, $matches);
    $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
} else {
    $lastNumber = 0;
}

$nextNumber = $lastNumber + 1;
$nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
$code = "TROUT-ASN-{$tahun}-{$romawi}-{$nomorUrut}";


         return $code;  // 🛑 HARUS return
        }

        public function show($id)
{
    $transfer = TransferOut::with('items')->findOrFail($id);

    $groupedItems = $transfer->items->map(function ($item) {
        return (object)[
            'article_code' => $item->article_code,
            'description' => $item->description,
            'qty' => $item->qty,
            'qty_used' => $item->qty_used ?? 0,
            'balance' => ($item->qty - ($item->qty_used ?? 0)),
            'uom' => $item->uom,
            'min_package' => $item->min_package,
            'expired_date' => $item->expired_date,
            'from_location' => $item->from_location ?? '-',
            'destination' => $item->destination ?? '-',
        ];
    });

    return view('ppic.detail-transfer_out', compact('transfer', 'groupedItems'));
}


public function scanLookup($code)
{
    // 1️⃣ Cek: Transfer In Header
    $transfer = DB::table('transfer_in as t')
        ->leftJoin('suppliers as s', 's.code', '=', 't.supplier_code')
        ->select('t.*', 's.name as supplier_name')
        ->where('t.code', $code)
        ->first();

    if ($transfer) {
        $items = DB::table('transfer_in_items as ti')
            ->leftJoin('warehouses as w', 'w.id', '=', 'ti.destination_id')
            ->join('articles as a', 'ti.article_code', '=', 'a.article_code')
            ->select(
              'ti.id as transfer_in_item_id',
            'ti.code as transfer_in_item_code',
            'ti.article_code',
            'ti.description',
            'ti.qty',
            'ti.qty_used',
            'ti.qty_return',
            DB::raw('( (ti.qty + IFNULL(ti.qty_return,0)) - ti.qty_used ) as balance'),
            'a.unit as uom',
            'a.min_package',
            'ti.expired_date',
            'ti.destination_id',
            'w.name as warehouse_name'
        )
        ->where('ti.transfer_in_id', $transfer->id)
        ->get();

        return response()->json([
            'type'     => 'transfer',
            'transfer' => $transfer,
            'items'    => $items,
        ]);
    }

    // 2️⃣ Cek: Receiving Header
    $receiving = DB::table('receivings as r')
        ->leftJoin('suppliers as s', 's.code', '=', 'r.supplier_code')
        ->select('r.*', 's.name as supplier_name')
        ->where('r.receiving_number', $code)
        ->first();

    if ($receiving) {
        $items =DB::table('receiving_items as ri')
    ->leftJoin('receivings as r', 'r.id', '=', 'ri.receiving_id')
    ->leftJoin('warehouses as w', 'w.id', '=', 'ri.destination_id')
    ->leftJoin('articles as a', 'a.article_code', '=', 'ri.article_code') // join ke article
    ->select(
         DB::raw('MIN(ri.id) as receiving_item_id'), // ✅ perbaikan di sini
        'ri.article_code',
        DB::raw('MAX(a.description) as description'),
        DB::raw('SUM(ri.qty_received) as total_qty'),
        DB::raw('SUM(ri.qty_used) as total_used'),
        DB::raw('(SUM(ri.qty_received) - SUM(ri.qty_used)) as balance'),
        DB::raw('MAX(a.unit) as uom'),
        DB::raw('MAX(a.min_package) as min_package'),
        DB::raw('MAX(ri.expired_date) as expired_date'),
        'ri.destination_id',
        DB::raw('MAX(w.name) as warehouse_name')
    )
     ->where('ri.receiving_id', $receiving->id)
    ->groupBy('ri.article_code', 'ri.destination_id')
    ->get();


        return response()->json([
            'type'     => 'receiving',
            'receiving' => $receiving,
            'items'    => $items,
        ]);
    }

    // 3️⃣ Cek: Transfer In Item per kode QR
    $item = DB::table('transfer_in_items as ti')
    ->leftJoin('articles as a', 'a.article_code', '=', 'ti.article_code') // ✅ join article
        ->leftJoin('warehouses as w', 'w.id', '=', 'ti.destination_id')
        ->leftJoin('transfer_in as t', 't.id', '=', 'ti.transfer_in_id')
        ->leftJoin('suppliers as s', 's.code', '=', 't.supplier_code')
        ->select(
            'ti.id as transfer_in_item_id',
            'ti.code as transfer_in_item_code',
            'ti.article_code',
            'ti.description',
            'ti.qty',
            'ti.qty_used',
            'ti.qty_return',
            DB::raw('((ti.qty + IFNULL(ti.qty_return,0)) - ti.qty_used) as balance'),
            DB::raw('MAX(a.unit) as uom'),
             DB::raw('COALESCE(a.min_package) as min_package'), // ✅ ambil dari article, default 1
            'ti.expired_date',
            'ti.destination_id',
            'w.name as warehouse_name',
            's.name as supplier_name',
            't.code as transfer_in_code',
            't.reference_number',
            't.transfer_category',
            't.date',
            't.note',
            't.qr_code_path'
        )
        ->where('ti.code', $code)
        ->first();

    if ($item) {
        return response()->json([
            'type'     => 'item',
            'transfer' => [
                'code'              => $item->transfer_in_code,
                'reference_number'  => $item->reference_number,
                'transfer_category' => $item->transfer_category,
                'date'              => $item->date,
                'note'              => $item->note,
                'qr_code_path'      => $item->qr_code_path,
                'supplier_name'     => $item->supplier_name,
            ],
            'item' => $item,
        ]);
    }

    // 4️⃣ Not Found
    return response()->json([
        'status'  => 'error',
        'message' => 'Barcode tidak ditemukan atau tidak valid.',
    ], 404);
}



public function approve($id)
{
    $out = TransferOut::findOrFail($id);
    $out->status = 'Posted';
    $out->approved_by = auth()->id();
    $out->approved_at = now();
    $out->save();

    return response()->json([
        'success' => true,
        'message' => 'Transfer Out Approved.',
        'code' => $out->code
    ]);
}

public function destroy($id)
{
    $out = TransferOut::findOrFail($id);
    $out->delete();

    return redirect()->route('ppic.transfer-out.index')->with('success', 'Transfer Out berhasil dihapus');
}


public function getDestinations()
{
    $warehouses = DB::table('warehouses')
        ->where('status', 'active')
        ->select('code', 'name', 'type')
        ->get();

    return response()->json($warehouses);
}



}
