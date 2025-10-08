<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index() {
    $suppliers = \App\Models\Supplier::orderBy('name')->get();
    $articleTypes = \App\Models\ArticleType::whereIn('name', [
    'Raw Material Purchase',
    'Raw Material Non Purchase',
    'Consumable',
    'Chemical',
    'Finish Good'
])->orderBy('name')->pluck('name');

  $warehouses = \App\Models\Warehouse::whereIn('name', [
    'Warehouse Chemical',
    'Warehouse Raw Material',
    'Warehouse Consumable',
    'Warehouse Finish Good'
  ])->orderBy('name')->pluck('name');

    $articles = \App\Models\Article::whereIn('article_type', ['RMP', 'RMNP', 'CM1', 'CM2', 'FG'])
    ->orderBy('description')
    ->get();


    return view('inventory.stock', compact('suppliers', 'articles','articleTypes', 'warehouses'));
}


    public function data(Request $request)
{
    $query = Stock::with([
        'article',
        'article.supplier',
        'article.type.warehouse',
    ]);

    if ($request->filled('article_code')) {
        $query->where('article_code', 'like', '%' . $request->article_code . '%');
    }
if ($request->filled('article_type')) {
    $articleTypeName = $request->article_type;

    $query->whereHas('article.type', function($q) use ($articleTypeName) {
        $q->where('name', $articleTypeName);
    });
}

if ($request->filled('location')) {
    $locationName = $request->location;

    $query->whereHas('location', function($q) use ($locationName) {
        $q->where('name', $locationName);
    });
}

   if ($request->filled('supplier')) {
    $supplierName = $request->supplier;

    $query->whereHas('article.supplier', function ($q) use ($supplierName) {
        $q->where('name', 'like', '%' . $supplierName . '%');
    });
}


    if ($request->filled('status')) {
    $status = $request->status;

    $query->whereHas('article', function ($q) use ($status) {
    $q->where(function ($sub) use ($status) {
        if ($status === 'Empty') {
            $sub->where('qty', 0);
        } elseif ($status === 'Critical') {
            $sub->whereColumn('qty', '<', 'safety_stock')
                ->where('qty', '>', 0); // hanya qty > 0
        } elseif ($status === 'Overload') {
            $sub->whereColumn('qty', '>', 'maximum_stock');
        } elseif ($status === 'Safe') {
            $sub->whereColumn('qty', '>=', 'safety_stock')
                ->whereColumn('qty', '<=', 'maximum_stock')
                ->where('qty', '>', 0); // pastikan 0 tidak termasuk
        }
    });
});

}


    return DataTables::of($query)

   ->addColumn('action', function ($row) {
    $dropdownId = 'dropdown-' . $row->id;

$actionButtons = '
<div class="relative inline-block text-left">
  <button type="button"
    data-dropdown-id="' . $dropdownId . '"
    onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
    class="inline-flex justify-center w-full rounded-md px-2 py-1 text-sm font-medium text-gray-700 hover:shadow-sm focus:outline-none">
    <i data-feather=\'align-justify\'></i>
  </button>
  <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">';


$actionButtons .= '
  <a href="#" class="block px-4 py-2 hover:bg-gray-100 movement-link" data-article-code="' . ($row->article->article_code ?? '') . '">
    <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Movement
  </a>';



             return $actionButtons;
})


        ->addColumn('article_type', function ($row) {
            return $row->article->type->name ?? '-';
        })
        ->addColumn('supplier', function ($row) {
            return $row->article->supplier->name ?? '-';
        })
        ->addColumn('article_name', function ($row) {
            return $row->article->description ?? '-';
        })
         ->addColumn('qty', function ($row) {
            $qty = (int) $row->qty;
            $uom = $row->article->unit ?? '';
            return "{$qty} {$uom}";
        })
         ->addColumn('min_stock', function ($row) {
            $min = (int) ($row->article->safety_stock ?? 0);
            $uom = $row->article->unit ?? '';
            return "{$min} {$uom}";
        })
        ->addColumn('max_stock', function ($row) {
            $max = (int) ($row->article->maximum_stock ?? 0);
            $uom = $row->article->unit ?? '';
            return "{$max} {$uom}";
        })
        ->addColumn('balance', function ($row) {
            $qty = (int) $row->qty;
            $min = (int) ($row->article->safety_stock ?? 0);
            $balance = $qty - $min;
            $uom = $row->article->unit ?? '';
            return "{$balance} {$uom}";
        })
        ->addColumn('min_package', function ($row) {
            return $row->article->min_package ?? '-';
        })
        ->addColumn('warehouse_id', function ($row) {
            return $row->article->type->warehouse->name ?? '-';
        })

         ->addColumn('status', function ($row) {
    $qty = (int)$row->qty;
    $min = (int)$row->article->safety_stock;
    $max = (int)$row->article->maximum_stock;
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($qty === 0) {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Empty</span>';
    } elseif ($qty < $min) {
        return '<span class="bg-red-600 ' . $commonClasses . '">Critical</span>';
    } elseif ($qty > $max) {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Overload</span>';
    } else {
        return '<span class="bg-teal-400 ' . $commonClasses . '">Safe</span>';
    }
    })

->addColumn('row_class', function ($row) {
    $qty = (int)$row->qty;
    $min = (int)$row->article->safety_stock;
    $max = (int)$row->article->maximum_stock;

    if ($qty < $min) {
        return 'row-critical';
    } elseif ($qty > $max) {
        return 'row-overload';
    } else {
        return '';
    }
})

->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })

        ->rawColumns(['action','status'])
        ->make(true);
}

public function movement(Request $request)
{
    $orderColumn = $request->input('columns.' . $request->input('order.0.column') . '.data', 'date');
    $orderDir    = $request->input('order.0.dir', 'desc');

    // Helper format data item
    $formatItem = function ($item, $source, $parent) {
        $date = match ($source) {
            'transfer_in', 'transfer_out' => $parent->date ?? $parent->created_at->format('Y-m-d'),
            'receiving'                   => $parent->received_date ?? $parent->created_at->format('Y-m-d'),
        };

        $status = match ($source) {
    'transfer_in', 'receiving' => '<span class="text-green-600 font-bold flex items-center justify-center gap-1">
                                        <i data-feather="arrow-down" width="14" height="14"></i> IN
                                    </span>',
    'transfer_out' => '<span class="text-red-600 font-bold flex items-center justify-center gap-1">
                                        <i data-feather="arrow-up" width="14" height="14"></i> OUT
                                    </span>',
};

$createdHour = (int) \Carbon\Carbon::parse($parent->created_at)->format('H');

$shift = match (true) {
    $createdHour >= 8 && $createdHour < 17 => "Shift 1",
    $createdHour >= 17 || $createdHour < 2 => "Shift 2",
    $createdHour >= 2 && $createdHour < 8 => "Shift 3",
};



        return [
            'source'           => $source,
            'date'             => $date,
            'status'           => $status,
            'reference_number' => $parent->code ?? $parent->receiving_number ?? '-',
            'cust_supp'        => $item->article->supplier->name ?? '-',
            'article_type'     => $item->article->article_type ?? '-',
            'article_code'     => $item->article_code,
            'description'      => $item->description ?? $item->article->description ?? '-',
            'qty'              => (float) ($item->qty ?? $item->qty_received ?? 0),
            'uom'              => $item->article->unit ?? '-',
            'from'             => strtoupper($parent->supplier->name ?? $parent->fromLocation->name ?? $item->from_location ?? '-'),
            'destination'      => strtoupper($item->destination->name ?? $item->destination ?? $parent->to_location ?? '-'),
            'created_by'       => strtoupper($parent->createdBy->name ?? $parent->creator->name ?? $parent->created_by ?? '-'),
            'shift'            => strtoupper($shift),
        ];
    };

    // Ambil & format data
    $inData = \App\Models\TransferIn::with('items.article.supplier', 'createdBy', 'supplier')
        ->get()
        ->flatMap(fn($t) => $t->items->map(fn($i) => $formatItem($i, 'transfer_in', $t)));

    $outData = \App\Models\TransferOut::with('items.article.supplier', 'createdBy')
        ->get()
        ->flatMap(fn($t) => $t->items->map(fn($i) => $formatItem($i, 'transfer_out', $t)));

    $receivingData = \App\Models\Receiving::with('items.article.supplier', 'creator')
        ->get()
        ->flatMap(fn($r) => $r->items->map(fn($i) => $formatItem($i, 'receiving', $r)));

    // Gabungkan semua data
    $allData = $inData->merge($outData)->merge($receivingData);

    // Sorting
    $allData = ($orderDir === 'desc')
        ? $allData->sortByDesc(fn($item) => $orderColumn === 'date' ? strtotime($item[$orderColumn]) : $item[$orderColumn])
        : $allData->sortBy(fn($item) => $orderColumn === 'date' ? strtotime($item[$orderColumn]) : $item[$orderColumn]);

    // Hanya return data tanpa recordsTotal & recordsFiltered
    return response()->json([
        'data' => $allData->values(),
    ]);
}


public function single_movement(Request $request)
{
    $articleCodeFilter = $request->input('article_code');

    $orderColumn = $request->input('columns.' . $request->input('order.0.column') . '.data', 'date');
    $orderDir    = $request->input('order.0.dir', 'desc');

    $formatItem = function ($item, $source, $parent) {
        $date = match ($source) {
            'transfer_in', 'transfer_out' => $parent->date ?? $parent->created_at->format('Y-m-d'),
            'receiving'                   => $parent->received_date ?? $parent->created_at->format('Y-m-d'),
        };

        $status = match ($source) {
            'transfer_in', 'receiving' => '<span class="text-green-600 font-bold flex items-center justify-center gap-1"><i data-feather="arrow-down" width="14" height="14"></i> IN</span>',
            'transfer_out'             => '<span class="text-red-600 font-bold flex items-center justify-center gap-1"><i data-feather="arrow-up" width="14" height="14"></i> OUT</span>',
        };

        $createdHour = (int) \Carbon\Carbon::parse($parent->created_at)->format('H');

        $shift = match (true) {
            $createdHour >= 8 && $createdHour < 17 => "Shift 1",
            $createdHour >= 17 || $createdHour < 2 => "Shift 2",
            $createdHour >= 2 && $createdHour < 8 => "Shift 3",
        };

        return [
            'source'           => $source,
            'date'             => $date,
            'status'           => $status,
            'reference_number' => $parent->code ?? $parent->receiving_number ?? '-',
            'cust_supp'        => $item->article->supplier->name ?? '-',
            'article_type'     => $item->article->article_type ?? '-',
            'article_code'     => $item->article_code,
            'description'      => $item->description ?? $item->article->description ?? '-',
            'qty'              => (float) ($item->qty ?? $item->qty_received ?? 0),
            'uom'              => $item->article->unit ?? '-',
            'from'             => strtoupper($parent->supplier->name ?? $parent->from_location ?? $item->from_location ?? '-'),
            'destination'      => strtoupper($item->destination->name ?? $item->destination ?? $parent->to_location ?? '-'),
            'created_by'       => strtoupper($parent->createdBy->name ?? $parent->creator->name ?? $parent->created_by ?? '-'),
            'shift'            => strtoupper($shift),
        ];
    };

    // Ambil & format data dengan filter article_code
    $inData = \App\Models\TransferIn::with('items.article.supplier', 'createdBy', 'supplier')
        ->get()
        ->flatMap(function($t) use ($formatItem, $articleCodeFilter) {
            return $t->items
                ->filter(fn($i) => !$articleCodeFilter || $i->article_code === $articleCodeFilter)
                ->map(fn($i) => $formatItem($i, 'transfer_in', $t));
        });

    $outData = \App\Models\TransferOut::with('items.article.supplier', 'createdBy')
        ->get()
        ->flatMap(function($t) use ($formatItem, $articleCodeFilter) {
            return $t->items
                ->filter(fn($i) => !$articleCodeFilter || $i->article_code === $articleCodeFilter)
                ->map(fn($i) => $formatItem($i, 'transfer_out', $t));
        });

    $receivingData = \App\Models\Receiving::with('items.article.supplier', 'creator')
        ->get()
        ->flatMap(function($r) use ($formatItem, $articleCodeFilter) {
            return $r->items
                ->filter(fn($i) => !$articleCodeFilter || $i->article_code === $articleCodeFilter)
                ->map(fn($i) => $formatItem($i, 'receiving', $r));
        });

    $allData = $inData->merge($outData)->merge($receivingData);

    $allData = ($orderDir === 'desc')
        ? $allData->sortByDesc(fn($item) => $orderColumn === 'date' ? strtotime($item[$orderColumn]) : $item[$orderColumn])
        : $allData->sortBy(fn($item) => $orderColumn === 'date' ? strtotime($item[$orderColumn]) : $item[$orderColumn]);

    return response()->json([
        'data' => $allData->values(),
    ]);
}











public function periodic(Request $request)
{
   $stocks = Stock::with('article') // memuat relasi
    ->get();


    $data = $stocks->map(function ($stock) {
        // Initial stock = semua barang masuk - semua barang keluar
        $initialIn = \DB::table('transfer_in_items as ti')
            ->join('transfer_in as t', 'ti.transfer_in_id', '=', 't.id')
            ->where('ti.article_code', $stock->article_code)
            ->sum('ti.qty');

        $initialReceiving = \DB::table('receiving_items as ri')
            ->join('receivings as r', 'ri.receiving_id', '=', 'r.id')
            ->where('ri.article_code', $stock->article_code)
            ->sum('ri.qty_received');

        $initialOut = \DB::table('transfer_out_items as toi')
            ->join('transfer_out as t', 'toi.transfer_out_id', '=', 't.id')
            ->where('toi.article_code', $stock->article_code)
            ->sum('toi.qty');

        $initialStock = 0; // kalau belum pakai perhitungan awal periode
        // Incoming = semua barang masuk
        $incoming = $initialIn + $initialReceiving;
        // Outgoing = semua barang keluar
        $outgoing = $initialOut;
        $finalStock = ($initialStock + $incoming) - $outgoing;

        
        // Kriteria bisa disesuaikan
        if ($outgoing == 0 && $incoming == 0) {
            $status = '<span class="bg-red-500 inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl">
                                        Dead Stock
                                    </span>';
        } elseif ($outgoing <= 5) {
            $status = '<span class="bg-yellow-500 inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl">
                                        Slow Moving
                                    </span>';
        } elseif ($outgoing > 5 && $outgoing <= 20) {
            $status = '<span class="bg-blue-500 inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl">
                                        Medium Moving
                                    </span>';
        } else {
            $status = '<span class="bg-green-500 inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl">
                                        Fast Moving
                                    </span>';
        }

       $safety_stock  = $stock->article->safety_stock ?? 0;
$maximum_stock = $stock->article->maximum_stock ?? 0;

        // Remarks berdasarkan safety dan maximum stock
if ($finalStock < $safety_stock) {
    $remarks = '<span class="text-red-700 px-2 py-1 rounded-full text-xs font-medium">Restock Needed</span>';
     $rowClass = 'bg-red-100'; // row jadi merah muda
} elseif ($finalStock > $maximum_stock) {
    $remarks = '<span class="text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">Overstock</span>';
} else {
    $remarks = '<span class="text-green-700 px-2 py-1 rounded-full text-xs font-medium">Normal</span>';
}

       

        return [
            'cust_supp' => $stock->article->supplier->name,
            'article_type' => strtoupper($stock->article->type->name),
            'article_code' => $stock->article_code,
            'description' => $stock->article->description,
            'initial'      => $initialStock,
            'incoming'     => $incoming,
            'outgoing'     => $outgoing,
            'final'        => $finalStock,
            'status'       => $status,
            'min' => optional($stock->article)->safety_stock ?? 0,
'max' => optional($stock->article)->maximum_stock ?? 0,
            'remarks'      => $remarks,
        ];
    });

    return response()->json($data);
}



}
