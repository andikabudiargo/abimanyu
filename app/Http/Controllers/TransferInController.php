<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article; // Pastikan model ini sesuai
use App\Models\TransferIn;
use App\Models\TransferInItems;
use App\Models\Receiving;
use App\Models\Warehouse;
use App\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class TransferInController extends Controller
{
  public function index()
{
    // Ambil data dari database
    $warehouses = Warehouse::orderBy('name')->get();
    $suppliers = Supplier::orderBy('name')->get();

    // Kirimkan ke view
    return view('ppic.transfer_in', [
        'warehouses' => $warehouses,
        'suppliers' => $suppliers
    ]);
}


      public function data(Request $request)
{
    $query = TransferIn::with(['fromLocation', 'toLocation', 'supplier', 'warehouse'])
    ->orderByRaw("FIELD(status, 'Draft', 'Revision', 'Posted')")
    ->orderBy('created_at', 'asc');

    if ($request->code) {
        $query->where('code', 'like', '%' . $request->code . '%');
    }

    if ($request->filled('status')) {
    $query->where('status', $request->status);
    }

   if ($request->filled('from')) {
    $query->where(function ($q) use ($request) {
        $q->whereHas('warehouse', function ($w) use ($request) {
            $w->where('id', $request->from);
        })->orWhereHas('supplier', function ($s) use ($request) {
            $s->where('id', $request->from);
        });
    });
}


    if ($request->filled('transfer_category')) {
    $query->where('transfer_category', 'like', '%' . trim($request->transfer_category) . '%');
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
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->owner && $row->owner->id === Auth::id();
    $code = $row->code ?? 'Unknown';
    $detail_url = route('ppic.transfer-in.show', ['id' => $row->id]);
    $delete_url = route('ppic.transfer-in.destroy', ['id' => $row->id]);

    
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
        <button onclick="revisionIN(' . $id . ', \'' . $code . '\')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
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
        <button onclick="approveIN(' . $id . ', \'' . $code . '\')" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-green-700">
            <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Posted
        </button>';
    }

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

->editColumn('transfer_category', function ($row) {
    if ($row->transfer_category === 'Incoming') {
        return '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Incoming</span>';
    } elseif ($row->transfer_category === 'Material Return') {
        return '<span class="bg-teal-100 text-teal-800 text-xs font-medium px-2.5 py-0.5 rounded">Material Return</span>';
    }
    return $row->transfer_category;
})


         ->addColumn('from_location', function ($row) {
            if ($row->from_location && $row->fromLocation) {
                return $row->fromLocation->name;
            } elseif ($row->supplier && $row->supplier->name) {
                return $row->supplier->name;
            } else {
                return '-';
            }
        })
        ->addColumn('to_location', function ($row) {
            return $row->toLocation ? $row->toLocation->name : '-';
        })
         ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })

        ->editColumn('transfer_category', function ($row) {
    $dot = '<span class="w-3 h-3 rounded-full inline-block mr-2"></span>';

    switch ($row->transfer_category) {
        case 'Incoming':
            $color = 'bg-yellow-600';
            $text = 'Incoming';
            break;
        case 'Material Return':
            $color = 'bg-blue-600';
            $text = 'Material Return';
            break;
        case 'Temporary Saved':
            $color = 'bg-red-600';
            $text = 'Temporary Saved';
            break;
        default:
            $color = 'bg-gray-300';
            $text = $row->transfer_category;
    }

    return '<div class="flex items-center">' 
        . str_replace('inline-block', $color, $dot) 
        . '<span>' . $text . '</span></div>';
})

->addColumn('created_by', function ($row) {
    return $row->createdBy->name ?? '-';
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
        ->rawColumns(['transfer_category', 'action', 'status', 'category', 'code']) // ⚠️ penting agar HTML tidak di-escape
        ->make(true);
}

    public function create()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('ppic.create-transfer_in');
    }

  public function store(Request $request)
{
    $request->validate([
        'reference_number' => 'required|string|max:100',
        'date' => 'required|date',
        'transfer_category' => 'required|string',
        'supplier_code' => 'nullable|string',
        'from_location' => 'nullable|integer',
        'note' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.article_code' => 'required|string|max:100',
        'items.*.description' => 'nullable|string',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.expired_date' => 'nullable|date',
        'items.*.destination_id' => 'nullable|integer|exists:warehouses,id',
        'items.*.origin_item_id' => 'nullable|integer', // asal barang (dibutuhkan untuk update qty_return)
        'items.*.origin_type' => 'nullable|string|in:transfer_in,receiving', // asalnya TRIN / LPB
    ]);

    DB::beginTransaction();

    try {
        $code = null;
        $labelData = [];

        // ==========================
        // Jika Material Return
        // ==========================
        if ($request->transfer_category === 'Material Return') {
            $baseCode = $request->reference_number; // contoh: TRIN-ASN-2025-VIII-0001

            // hitung sudah ada berapa kali return dari kode ini
            $returnCount = DB::table('transfer_in')
                ->where('code', 'LIKE', $baseCode . '-R%')
                ->count();

            $nextReturn = $returnCount + 1;
            $code = $baseCode . '-R' . $nextReturn;

            // QR tidak digenerate lagi → ambil null
            $transferQrUrl = null;

        } else {
            // ==========================
            // Jalur Normal Transfer In
            // ==========================
            $bulanRomawi = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $tahun = now()->format('Y');
            $bulan = now()->format('n');
            $romawi = $bulanRomawi[$bulan];

            $lastCode = DB::table('transfer_in')
   ->where('code', 'LIKE', 'TRIN-ASN-' . $tahun . '-' . $romawi . '-%')
    ->where('code', 'NOT LIKE', '%-R%') // exclude Material Return
    ->orderBy('code', 'desc')
    ->value('code');


            if ($lastCode) {
                preg_match('/(\d{4})$/', $lastCode, $matches);
                $lastNumber = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
            } else {
                $lastNumber = 1;
            }

            $nomorUrut = str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
            $code = "TRIN-ASN-{$tahun}-{$romawi}-{$nomorUrut}";

            // === Generate QR Transfer In
            $transferQrFileName = $code . '.png';
            $transferQrPath = 'qrcodes/' . $transferQrFileName;
            $transferQr = Builder::create()
                ->writer(new PngWriter())
                ->data($code)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size(300)
                ->margin(10)
                ->build();

            Storage::disk('public')->put($transferQrPath, $transferQr->getString());
            $transferQrUrl = asset('storage/' . $transferQrPath);
        }

        // Ambil supplier name
        $supplierName = null;
        if ($request->supplier_code) {
            $supplierName = DB::table('suppliers')
                ->where('code', $request->supplier_code)
                ->value('name') ?? '-';
        }

        // ==========================
        // Simpan Transfer In Header
        // ==========================
        $transferId = DB::table('transfer_in')->insertGetId([
            'code' => $code,
            'reference_number' => $request->reference_number,
            'date' => $request->date,
            'transfer_category' => $request->transfer_category,
            'supplier_code' => $request->supplier_code,
            'from_location' => $request->from_location,
            'note' => $request->note,
            'qr_code_path' => $transferQrUrl, // null kalau Material Return
            'created_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // ==========================
        // Simpan Items
        // ==========================
        foreach ($request->items as $index => $item) {
            $itemCode = $code . '-ITEM' . ($index + 1);
            $qty = (int) $item['qty'];

            // Generate QR Item
            $itemQrFileName = $itemCode . '.png'; $itemQrPath = 'qrcodes/' . $itemQrFileName;
            $itemQr = Builder::create()
            ->writer(new PngWriter())
            ->data($itemCode)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->build();
            Storage::disk('public')
            ->put($itemQrPath, $itemQr->getString());
            $itemQrUrl = asset('storage/' . $itemQrPath);

            $article = Article::where('article_code', $item['article_code'])->first();

               // masukkan ke labelData
    $labelData[] = [
        'type' => 'qr_item',
        'qr_path' => $itemQrUrl,
        'code' => $itemCode,
        'article_code' => $item['article_code'],
        'description' => $item['description'],
        'qty' => $qty,
        'min_package' => $article->min_package ?? 1, // ambil dari DB
    ];

     if ($request->transfer_category === 'Material Return') {
        $qtyReturn = (int) $item['qty']; 
        $qty = $qtyReturn; // tetap pakai $qty supaya tidak pecah di insert
    } else {
        $qty = (int) $item['qty'];
    }

            DB::table('transfer_in_items')->insert([
                'transfer_in_id' => $transferId,
                'code' => $itemCode,
                'article_code' => $item['article_code'],
                'description' => $item['description'],
                'qty' => $qty,
                'qty_used' => 0,
                'balance' => $qty,
                'expired_date' => $item['expired_date'] ?? null,
                'destination_id' => $item['destination_id'] ?? null,
                'qr_path' => $itemQrUrl,
                'created_at' => now(),
            ]);

            // === Update Stock jika bukan Material Return ===
            if ($request->transfer_category !== 'Material Return') {
                $article = \App\Models\Article::where('article_code', $item['article_code'])->first();
                if ($article && in_array($article->article_type ?? '', ['RMP', 'RMNP', 'CM1', 'CM2'])) {
                    $warehouseId = $item['destination_id'] ?? null;
                    $articleCode = $item['article_code'];

                    if ($warehouseId) {
                        $existingStock = \App\Models\Stock::where('article_code', $articleCode)
                            ->where('warehouse_id', $warehouseId)
                            ->first();

                        if ($existingStock) {
                            $existingStock->qty += $qty;
                            $existingStock->save();
                        } else {
                            \App\Models\Stock::create([
                                'article_code' => $articleCode,
                                'qty' => $qty,
                                'warehouse_id' => $warehouseId,
                            ]);
                        }
                    }
                }
            }

       // === Update qty_return asal barang (khusus Material Return)
    if ($request->transfer_category === 'Material Return' && isset($item['origin_item_id'], $item['origin_type'])) {
        if ($item['origin_type'] === 'transfer_in') {
            DB::table('transfer_in_items')
                ->where('id', $item['origin_item_id'])
                ->increment('qty_return', $qtyReturn);
        } elseif ($item['origin_type'] === 'receiving') {
            DB::table('receiving_items')
                ->where('id', $item['origin_item_id'])
                ->increment('qty_return', $qtyReturn);
        }
        // === Update stock utama per warehouse
    $warehouseId = $item['destination_id'] ?? null;
    $articleCode = $item['article_code'];

    if ($warehouseId) {
        $existingStock = \App\Models\Stock::where('article_code', $articleCode)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if ($existingStock) {
            $existingStock->qty += $qtyReturn;
            $existingStock->save();
        } else {
            \App\Models\Stock::create([
                'article_code' => $articleCode,
                'qty'          => $qtyReturn,
                'warehouse_id' => $warehouseId,
            ]);
        }
    }
}
}

        // Label Transfer In QR
        $labelData[] = [
            'type' => 'qr_transfer',
            'qr_path' => $transferQrUrl,
            'reference_number' => $code,
            'supplier_name' => $supplierName ?? '-',
            'date' => $request->date,
        ];

        DB::commit();

        return response()->json([
            'status' => 'success',
            'code' => $code,
            'labels' => $labelData
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}



public function show($id)
{
    $transfer = TransferIn::with([
        'items.article',
        'fromLocation',
        'toLocation',
        'supplier',
        'warehouse',
        'items.destination'
    ])->findOrFail($id);

    // Ambil langsung semua items tanpa grouping
    $items = $transfer->items;

    return view('ppic.detail-transfer_in', compact('transfer', 'items'));
}




public function approve($id)
{
    $in = TransferIn::findOrFail($id);
    $in->status = 'Posted';
    $in->approved_by = auth()->id();
    $in->approved_at = now();
    $in->save();

    return response()->json([
        'success' => true,
        'message' => 'Transfer In Posted.',
        'code' => $in->code
    ]);
}

public function destroy($id)
{
    $transfer = TransferIn::findOrFail($id);
    $transfer->delete();

    return redirect()->route('ppic.transfer-in.index')->with('success', 'Transfer In berhasil dihapus');
}



     public function getArticleByCode(Request $request)
    {
        $code = $request->query('q');

        $article = Article::where('article_code', $code)->first();

        if (!$article) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json([
            'article_code' => $article->article_code,
            'description' => $article->description,
            'supplier' => $article->supplier ?? '-',
            'unit' => $article->unit,
            'article_type' => $article->article_type, // ✅ pastikan ini ada
        ]);
    }

  public function search(Request $request)
{
    $query = $request->query('q');

    if (!$query || strlen($query) < 2) {
        return response()->json([], 200);
    }

    // Ambil Transfer In + filter stok habis
    $transferInResults = TransferIn::with(['supplier', 'warehouse', 'items']) // pastikan ada relasi items
       ->where('code', 'LIKE', "%{$query}%")
    ->whereHas('items', function ($query) {
        $query->whereRaw('(qty + qty_return) > qty_used');
    })
    ->limit(10)
    ->get()
        ->map(function ($transfer) {
            return [
                'code' => $transfer->code,
                'supplier_code' => $transfer->supplier_code,
                'supplier_name' => optional($transfer->supplier)->name ?? '-',
                'warehouse_name' => optional($transfer->warehouse)->name ?? '-',
                'transfer_type' => $transfer->transfer_category ?? 'Transfer In',
            ];
        });

        // Ambil Transfer In Items + filter stok habis
    $transferInItemsResults = TransferInItems::with(['transferIn.supplier', 'transferIn.warehouse']) // pastikan ada relasi items
       ->where('code', 'LIKE', "%{$query}%")
        ->whereRaw('(qty + qty_return) > qty_used')

    ->limit(10)
    ->get()
        ->map(function ($items) {
            return [
                'code' => $items->code,
                'supplier_code'  => $items->transferIn->supplier_code,
                'supplier_name'  => optional($items->transferIn->supplier)->name ?? '-',
                'warehouse_name' => optional($items->transferIn->warehouse)->name ?? '-',
                'transfer_type' => $transfer->transfer_category ?? 'Transfer In Items',
            ];
        });

    // Ambil Receiving + filter stok habis
    $receivingResults = Receiving::with(['supplier', 'items']) // pastikan ada relasi items
        ->where('receiving_number', 'LIKE', "%{$query}%")
        ->limit(10)
        ->get()
        ->filter(function ($receive) {
            return $receive->items->contains(function ($item) {
                return $item->qty_received > $item->qty_used;
            });
        })
        ->map(function ($receive) {
            return [
                'code' => $receive->receiving_number,
                'supplier_code' => $receive->supplier_code,
                'supplier_name' => optional($receive->supplier)->name ?? '-',
                'transfer_type' => 'Receiving',
            ];
        });

    $merged = collect($transferInResults)->merge($receivingResults)->merge($transferInItemsResults);

    return response()->json($merged);
}

public function searchAll(Request $request)
{
    $page = (int) $request->query('page', 1);
    $perPage = 20;
    $q = $request->query('q', '');
    $type = $request->query('type', 'article'); // tipe untuk lazy load

    $mapResults = function($items, $idField, $textCallback, $typeLabel) {
        return $items->map(fn($item) => [
            'id' => $item->{$idField},
            'text' => $textCallback($item),
            'type' => $typeLabel, // tipe ini akan dipakai frontend buat optgroup
            'data' => $item
        ]);
    };

    $results = collect();
    $hasMore = false;

    switch($type) {
        case 'article':
            $query = Article::with('supplier')
                ->whereNotIn('article_type', ['FG','GA','PT']);
            if($q){
                $query->where(fn($sub)=>$sub
                    ->where('article_code','LIKE',"%{$q}%")
                    ->orWhere('description','LIKE',"%{$q}%")
                );
            }
            $total = $query->count();
            $items = $query->skip(($page-1)*$perPage)->take($perPage)->get();
            $results = $mapResults($items, 'article_code', fn($a)=> "{$a->article_code} - {$a->description} (" . (optional($a->supplier)->name ?? '-') . ")", 'Article');
            $hasMore = ($page * $perPage) < $total;
            break;

        case 'transfer':
            $query = TransferIn::with(['supplier','items'])
                ->whereHas('items', fn($q)=>$q->whereRaw('(qty_used - qty_return) > 0'));
            if($q) $query->where('code','LIKE',"%{$q}%");
            $total = $query->count();
            $items = $query->skip(($page-1)*$perPage)->take($perPage)->get();
            $results = $mapResults($items, 'code', fn($t)=> "{$t->code} (" . (optional($t->supplier)->name ?? '-') . ")", 'Transfer In');
            $hasMore = ($page * $perPage) < $total;
            break;

        case 'receiving':
            $query = Receiving::with(['supplier','items'])
                ->whereHas('items', fn($q)=>$q->whereRaw('(qty_used - qty_return) > 0'));
            if($q) $query->where('receiving_number','LIKE',"%{$q}%");
            $total = $query->count();
            $items = $query->skip(($page-1)*$perPage)->take($perPage)->get();
            $results = $mapResults($items, 'receiving_number', fn($r)=> "{$r->receiving_number} (" . (optional($r->supplier)->name ?? '-') . ")", 'Receiving');
            $hasMore = ($page * $perPage) < $total;
            break;
    }

    return response()->json([
        'results' => $results, // flat array, tidak ada header
        'pagination' => ['more' => $hasMore]
    ]);
}








}
