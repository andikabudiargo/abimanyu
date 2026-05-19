<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Article;
use App\Models\TransferChemical;
use App\Models\TransferChemicalItem;

class TransferChemicalController extends Controller
{

public function index() {

  return view('ppic.transfer-chemical');

}

public function data(Request $request)
{
  $query = TransferChemical::with(['createdBy'])
    ->when($request->date, function ($q) use ($request) {
        if (str_contains($request->date, ' to ')) {
            [$start, $end] = explode(' to ', $request->date);
            $q->whereBetween('transfer_date', [$start, $end]);
        } else {
            $q->whereDate('transfer_date', $request->date);
        }
    })
    ->when($request->from, fn($q) =>
        $q->where('location_from', $request->from)
    )
    ->when($request->to, fn($q) =>
        $q->where('location_to', $request->to)
    )
    ->when($request->status, function ($q) use ($request) {
        if ($request->status === 'Supply') {
            $q->where('location_from', 'Warehouse Chemical');
        } elseif ($request->status === 'Return') {
            $q->where('location_from', '!=', 'Warehouse Chemical');
        }
    });

    $query->orderBy('created_at', 'desc');

    return DataTables::of($query)
   ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;
    $detail_url = route('ppic.tfcm1.show', ['id' => $row->id]);
    $delete_url = route('qc.inspections.destroy', $row->id);
    $edit_url = route('qc.inspection.edit', ['id' => $row->id]);

    $actionButtons = '
    <div class="relative inline-block text-left">
      <button type="button"
        data-dropdown-id="' . $dropdownId . '"
        onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
        class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
        <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
    ';

    $actionButtons .= '
        <a href="'. $detail_url .'" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
        </a>
        <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
        </a>
         <button onclick="modalExport(' . $row->id .')" class="w-full text-left px-4 py-2 text-green-600 hover:bg-green-100">
            <i data-feather="download" class="w-4 h-4 inline mr-2"></i>Export
        </button>
        <button onclick="confirmDelete(' . $row->id . ')" 
            class="w-full text-left text-red-600 px-4 py-2 hover:bg-red-500 hover:text-white">
            <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
        </button>
    ';

    $actionButtons .= '</div></div>';

    return $actionButtons;
})

        // ✅ TRANSFER DATE + ICON
        ->editColumn('transfer_date', function ($row) {
            return '
            <div class="flex items-center gap-1">
                
                ' . \Carbon\Carbon::parse($row->transfer_date)->format('d-m-Y') . '
            </div>';
        })

       ->addColumn('status', function ($row) {
    $isSupply = $row->location_from === 'Warehouse Chemical';

    return $isSupply
        ? '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
            <i data-feather="arrow-up-circle" class="w-3.5 h-3.5"></i>
            Supply
        </span>'
        : '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
            <i data-feather="rotate-ccw" class="w-3.5 h-3.5"></i>
            Return
        </span>';
})

       // ✅ FROM
->addColumn('from', function ($row) {
    return '
    <div class="flex items-center gap-1">
        <i data-feather="home" class="w-4 h-4"></i>
        ' . $row->location_from . '
    </div>';
})

// ✅ TO
->addColumn('to', function ($row) {
    return '
    <div class="flex items-center gap-1">
        <i data-feather="home" class="w-4 h-4"></i>
        ' . $row->location_to . '
    </div>';
})

        // ✅ CREATED BY + ICON
        ->addColumn('created_by', function ($row) {
            return '
            <div class="flex items-center gap-1">
                <i data-feather="user" class="w-4 h-4"></i>
                ' . ($row->createdBy->name ?? '-') . '
            </div>';
        })

        // ✅ CREATED AT + ICON
        ->editColumn('created_at', function ($row) {
            return '
            <div class="flex items-center gap-1">
               
                ' . \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') . '
            </div>';
        })

        ->rawColumns([
            'action',
            'transfer_date',
            'status',
            'from',
            'to',
            'created_by',
            'created_at'
        ])

        ->make(true);
}

 public function dataDetail(Request $request)
{
    $query = TransferChemicalItem::query()
        ->join('transfer_chemicals as tc', 'tc.id', '=', 'transfer_chemical_items.transfer_chemical_id')
        ->join('articles',                 'articles.article_code', '=', 'transfer_chemical_items.article_code')
        ->leftJoin('users',                'users.id', '=', 'tc.created_by')
        ->select([
            'tc.id                                          as transfer_id',
            'tc.transfer_date',
            'tc.location_from',
            'tc.location_to',
            'transfer_chemical_items.article_code',
            'articles.description',
            'articles.min_package',
            'articles.unit                                  as article_unit',   // unit dari articles (untuk min_package & UoM IMS)
            'articles.conversion_value',
            'transfer_chemical_items.condition',
            'transfer_chemical_items.qty',
            'transfer_chemical_items.unit                   as transfer_unit',  // unit dari items (KG / ikut article)
            'users.name                                     as creator_name',
            'tc.created_at',
        ])
        ->when($request->date, function ($q) use ($request) {
            if (str_contains($request->date, ' to ')) {
                [$start, $end] = explode(' to ', $request->date);
                $q->whereBetween('tc.transfer_date', [$start, $end]);
            } else {
                $q->whereDate('tc.transfer_date', $request->date);
            }
        })
        ->when($request->from,   fn($q) => $q->where('tc.location_from', $request->from))
        ->when($request->to,     fn($q) => $q->where('tc.location_to',   $request->to))
        ->when($request->status, function ($q) use ($request) {
            if ($request->status === 'Supply') {
                $q->where('tc.location_from', 'Warehouse Chemical');
            } elseif ($request->status === 'Return') {
                $q->where('tc.location_from', '!=', 'Warehouse Chemical');
            }
        })
        ->orderBy('tc.created_at', 'desc')
        ->orderBy('tc.id',         'desc');

    return DataTables::of($query)

        ->editColumn('transfer_date', fn($row) =>
            \Carbon\Carbon::parse($row->transfer_date)->format('d-m-Y')
        )

        ->addColumn('status', function ($row) {
    $isSupply = $row->location_from === 'Warehouse Chemical';

    return $isSupply
        ? '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
            <i data-feather="arrow-up-circle" class="w-3.5 h-3.5"></i>
            Supply
        </span>'
        : '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
            <i data-feather="rotate-ccw" class="w-3.5 h-3.5"></i>
            Return
        </span>';
})

        ->addColumn('from', fn($row) =>
            '<div class="flex items-center gap-1"><i data-feather="home" class="w-4 h-4"></i>' . e($row->location_from) . '</div>'
        )

        ->addColumn('to', fn($row) =>
            '<div class="flex items-center gap-1"><i data-feather="home" class="w-4 h-4"></i>' . e($row->location_to) . '</div>'
        )

        ->addColumn('created_by', fn($row) =>
            '<div class="flex items-center gap-1"><i data-feather="user" class="w-4 h-4"></i>' . e($row->creator_name ?? '-') . '</div>'
        )

        ->editColumn('condition', function ($row) {
    return $row->condition === 'Utuh'
        ? '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
            <i data-feather="check-circle" class="w-3.5 h-3.5"></i>
            Utuh
        </span>'
        : '
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
            <i data-feather="alert-triangle" class="w-3.5 h-3.5"></i>
            Tidak Utuh
        </span>';
})

        ->editColumn('qty', fn($row) =>
            '<span class="font-semibold text-green-700">' . $row->qty . '</span>'
        )

        ->editColumn('uom_con', fn($row) =>
            '<span class="text-xs font-medium">' . e($row->transfer_unit) . '</span>'
        )

        // Min Package: nilai + unit artikel
        ->addColumn('min_package_display', fn($row) =>
            $row->min_package
                ? $row->min_package . ' <span class="text-gray-400 text-xs">' . e($row->article_unit) . '</span>'
                : '-'
        )

        // Conversion value
        ->addColumn('conversion', fn($row) =>
            $row->conversion_value !== null
                ? number_format((float) $row->conversion_value, 2, '.', '')
                : '-'
        )

        // Qty IMS: qty * conversion_value (hanya jika Tidak Utuh, else qty as-is)
        ->addColumn('qty_ims', function ($row) {
            $qty        = (float) $row->qty;
            $conversion = (float) ($row->conversion_value ?? 1);

            $result = $row->condition === 'Tidak Utuh'
                ? round($qty / $conversion, 2)
                : $qty;

            return '<span class="font-semibold text-indigo-700">' . $result . '</span>';
        })

        // UoM IMS: selalu ikut unit artikel
        ->addColumn('uom_ims', fn($row) =>
            '<span class="text-xs font-medium">' . e($row->article_unit ?? '-') . '</span>'
        )

        ->rawColumns([
            'status', 'from', 'to', 'created_by',
            'condition', 'qty',
            'min_package_display', 'qty_ims', 'uom_ims', 'uom_con',
        ])

        ->make(true);
}

public function create() {

  return view('ppic.create-transfer-chemical');

}

  // Tambahkan method ini ke controller yang sudah ada
    public function show($id)
    {
        $transfer = TransferChemical::with([
            'items.article'
        ])->findOrFail($id);

        return view('ppic.detail-transfer-chemical', compact('transfer'));
    }

public function chemicals(Request $request)
{
    $query = Article::query()
        ->select(['id', 'article_code', 'description', 'min_package', 'unit', 'conversion_value'])
        ->where('article_type', 'CM1')
        ->orderBy('id');

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('article_code', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
        });
    }

    $perPage = 20;
    $page    = (int) ($request->page ?? 1);
    $data    = $query->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'data'      => $data->items(),
        'more'      => $data->hasMorePages(),
    ]);
}

 public function store(Request $request)
    {
        // ── 1. Validate header ──────────────────────────────────────────────
        $request->validate([
            'transfer_date' => ['required', 'date', 'before_or_equal:today'],
            'location_from' => ['required', 'string'],
            'location_to'   => ['required', 'string', 'different:location_from'],
            'note'          => ['nullable', 'string', 'max:1000'],
 
            // items array
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.article_code'   => ['required', 'string', 'exists:articles,article_code'],
            'items.*.condition'     => ['required', 'in:Utuh,Tidak Utuh'],
            'items.*.qty'           => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit'          => ['required', 'string', 'max:20'],
        ], [
            'location_to.different'       => 'Location To tidak boleh sama dengan Location From.',
            'items.required'              => 'Minimal satu chemical harus diisi.',
            'items.*.article_code.exists'  => 'Chemical tidak ditemukan.',
            'items.*.condition.in'        => 'Kondisi hanya boleh Utuh atau Tidak Utuh.',
            'items.*.qty.min'             => 'Qty harus lebih dari 0.',
        ]);
 
 
        // ── 3. Simpan dalam satu transaksi ──────────────────────────────────
        DB::transaction(function () use ($request) {
 
            $transfer = TransferChemical::create([
                'transfer_date' => $request->transfer_date,
                'location_from' => $request->location_from,
                'location_to'   => $request->location_to,
                'note'          => $request->note,
                'created_by'    => Auth::id(),
                'created_at'    => now(),
            ]);
 
            $items = array_map(fn($item) => [
                'transfer_chemical_id' => $transfer->id,
                'article_code'          => $item['article_code'],
                'condition'            => $item['condition'],
                'qty'                  => $item['qty'],
                'unit'                 => $item['unit'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ], $request->items);
 
            TransferChemicalItem::insert($items);
        });
 
        return response()->json([
            'message' => 'Transfer chemical berhasil disimpan.',
        ], 201);
    }

     public function exportIMS(TransferChemical $transfer): StreamedResponse
    {
        $transfer->load('items.article');
 
        // ── Location mapping ────────────────────────────────────────────────
        $locationMap = [
            'Warehouse Chemical' => '1008',
            'Spraybooth 1A'      => '1002',
            'Spraybooth 1B'      => '1003',
            'Spraybooth 1C'      => '1004',
            'Spraybooth 2A'      => '1005',
            'Spraybooth 2B'      => '1006',
            'Spraybooth 2C'      => '1007',
            'Spraybooth 3A'      => '1014',
            'Spraybooth 3B'      => '1009',
            'Spraybooth 3C'      => '1010',
            'Spraybooth 4A'      => '1043',
            'Spraybooth 4B'      => '1044',
            'Spraybooth 4C'      => '1045',
            'Spraybooth 5A'      => '1046',
            'Spraybooth 5B'      => '1047',
            'Spraybooth 5C'      => '1047',
        ];
 
        $locationCode = $locationMap[$transfer->location_to] ?? '';
 
        // ── Build spreadsheet ───────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('article');
 
        // Header row
        $sheet->setCellValue('A1', 'article_code');
        $sheet->setCellValue('B1', 'location_code');
        $sheet->setCellValue('C1', 'qty');
 
        // Auto-width columns
        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
 
       // Data rows
$row = 2;

// Group items by article_code and sum qty
$groupedItems = [];
foreach ($transfer->items as $item) {
    $chemical    = $item->article;
    $articleCode = $chemical->article_code ?? '';

    // Qty conversion:
    // - Utuh      → use qty as-is
    // - Tidak Utuh → qty * conversion_value (from articles table)
    if ($item->condition === 'Tidak Utuh') {
        $conversionValue = floatval($chemical->conversion_value ?? 1);
        $qty             = round(floatval($item->qty) / $conversionValue, 2);
    } else {
        $qty = floatval($item->qty);
    }

    if (isset($groupedItems[$articleCode])) {
        $groupedItems[$articleCode] += $qty;
    } else {
        $groupedItems[$articleCode] = $qty;
    }
}

foreach ($groupedItems as $articleCode => $totalQty) {
    $sheet->setCellValue("A{$row}", $articleCode);
    $sheet->setCellValue("B{$row}", $locationCode);
    $sheet->setCellValue("C{$row}", round($totalQty, 2));

    // Right-align qty column
    $sheet->getStyle("C{$row}")->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $row++;
}

 
        // ── Stream as download ──────────────────────────────────────────────
        $filename = 'IMS_Transfer_' . $transfer->id . '_' . $transfer->transfer_date->format('Ymd') . '.xlsx';
 
        $writer = new Xlsx($spreadsheet);
 
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function print(TransferChemical $transfer)
    {
        $transfer->load('items.article');
 
        // ── Supply atau Return ──────────────────────────────────────────────
        // Supply  = from Warehouse Chemical
        // Return  = from selain Warehouse
        $isSupply = $transfer->location_from === 'Warehouse Chemical';
        $title    = $isSupply ? 'Transfer Chemical Supply' : 'Transfer Chemical Return';
 
        // ── Build rows ──────────────────────────────────────────────────────
        $items = $transfer->items->map(function ($item) {
            $chemical = $item->article;
 
            return [
                'article_code'    => $chemical->article_code    ?? '-',
                'description'     => $chemical->description     ?? '-',
                'condition'       => $item->condition,
                'min_package'     => $chemical->min_package      ?? '-',
                'min_package_unit'=> $chemical->unit             ?? '',   // unit dari tabel articles
                'qty'             => $item->qty,
                'unit'            => $item->condition === 'Tidak Utuh'
                                        ? 'KG'
                                        : ($chemical->unit ?? '-'),
            ];
        })->toArray();
 
        // ── Pad to minimum 10 rows ─────────────────────────────────────────
        $minRows   = max(10, count($items));
        $rows      = array_pad($items, $minRows, null); // null = empty row
 
        return view('ppic.print-transfer-chemical', compact(
            'transfer',
            'isSupply',
            'title',
            'rows',
        ));
    }


}