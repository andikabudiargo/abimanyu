<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Supplier;
use App\Models\Inspection;
use App\Models\Article;
use App\Models\IncomingInspection;
use App\Models\IncomingInspectionItem;
use Yajra\DataTables\Facades\DataTables;

class IncomingInspectionController extends Controller
{
       public function index() {
        return view('qc.incoming');
    }

   public function show($id) {
    $inspection = IncomingInspection::with('items.inspection.inspection_defects.defect')->findOrFail($id);

    $defectTotals = [];
    $totalCheck = 0;
    $totalDefectQty = 0;

    // Ambil tanggal unik dari inspection items
    $dates = $inspection->items->map(function($item) {
        return \Carbon\Carbon::parse($item->inspection->inspection_date)->format('d');
    })->unique()->sort()->values()->all();

    // Ambil tahun dan bulan dari salah satu tanggal inspeksi, misal inspeksi pertama
    $firstInspectionDate = $inspection->items->first()
        ? \Carbon\Carbon::parse($inspection->items->first()->inspection->inspection_date)
        : now();
    $year = $firstInspectionDate->year;
    $month = $firstInspectionDate->month;

    // Buat array tanggal dari 1 sampai jumlah hari dalam bulan tersebut
    $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
    $allDates = [];
    for ($i = 1; $i <= $daysInMonth; $i++) {
        $allDates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
    }

    // Hitung defectTotals, totalCheck, totalDefectQty seperti kode awal
    foreach ($inspection->items as $item) {
        $inspectionData = $item->inspection;
        $totalCheck += $inspectionData->total_check ?? 0;

        $date = \Carbon\Carbon::parse($inspectionData->inspection_date)->format('d');

        foreach ($inspectionData->inspection_defects as $defectItem) {
            $defectName = $defectItem->defect->defect ?? 'Unknown';
            $qty = $defectItem->qty;

            $totalDefectQty += $qty;

            if (!isset($defectTotals[$defectName])) {
                $defectTotals[$defectName] = [];
            }
            if (!isset($defectTotals[$defectName][$date])) {
                $defectTotals[$defectName][$date] = 0;
            }
            $defectTotals[$defectName][$date] += $qty;
        }
    }

    $totalOk = max($totalCheck - $totalDefectQty, 0);
    $totalNg = $totalDefectQty;

    // Labels untuk chart: nama defect
    $defectLabels = array_keys($defectTotals);

    // Hitung total qty defect untuk chart dari semua tanggal
    $defectValues = [];
    foreach ($defectTotals as $defectName => $qtyPerDate) {
        $totalQty = array_sum($qtyPerDate);
        $defectValues[] = $totalCheck > 0 ? round(($totalQty / $totalCheck) * 100, 0) : 0;
    }

    $defects = [];
foreach ($defectLabels as $index => $label) {
    $defects[] = [
        'label' => $label,
        'value' => $defectValues[$index] ?? 0,
    ];
}

// Urutkan ascending berdasarkan nilai value
usort($defects, function($a, $b) {
    return $a['value'] <=> $b['value'];
});

// Pisahkan kembali label dan value setelah terurut
$defectLabels = array_column($defects, 'label');
$defectValues = array_column($defects, 'value');

    $itemLabels = [
        'Total Received',
        'Total Check',
        'Total OK',
        'Total NG',
        'Total OK Repair',
        'Pass Rate (%)',
        'Performa (%)',
        '100% (A)/Sampling (S)'
    ];

    // --- TAMBAHKAN PERHITUNGAN $totals ---
    $totals = [];
    foreach ($itemLabels as $label) {
        $totals[$label] = [];
        foreach ($dates as $date) {
            $totals[$label][$date] = 0;
        }
        $totals[$label]['total'] = 0;
    }

    // Isi data per tanggal dan total
    foreach ($inspection->items as $item) {
        $inspectionData = $item->inspection;
        $date = \Carbon\Carbon::parse($inspectionData->inspection_date)->format('d');

        // Total Received (misal qty_received di item, sesuaikan jika beda)
        $received = $inspectionData->qty_received ?? 0;
        $totals['Total Received'][$date] += $received;
        $totals['Total Received']['total'] += $received;

        // Total Check
        $totalCheckItem = $inspectionData->total_check ?? 0;
        $totals['Total Check'][$date] += $totalCheckItem;
        $totals['Total Check']['total'] += $totalCheckItem;

        // Total defect qty per item
        $defectQty = 0;
        $defectOkRepairQty = 0; // jika ada data OK Repair, sesuaikan logika

        foreach ($inspectionData->inspection_defects as $defect) {
            $defectQty += $defect->qty;

            // Contoh cek untuk OK Repair (ubah sesuai logika dan data)
            // if ($defect->type == 'repair') {
            //    $defectOkRepairQty += $defect->qty;
            // }
        }

        // Total OK = Total Check - defectQty
        $okQty = max($totalCheckItem - $defectQty, 0);
        $totals['Total OK'][$date] += $okQty;
        $totals['Total OK']['total'] += $okQty;

        // Total NG = defectQty
        $totals['Total NG'][$date] += $defectQty;
        $totals['Total NG']['total'] += $defectQty;

        // Total OK Repair
       $totalOkRepair = $inspectionData->total_ok_repair ?? 0;
    $totals['Total OK Repair'][$date] += $totalOkRepair;
    $totals['Total OK Repair']['total'] += $totalOkRepair;

        // Pass Rate (%) per tanggal
        $passRate = $totalCheckItem > 0 ? ($okQty / $totalCheckItem) * 100 : 0;
        $totals['Pass Rate (%)'][$date] = round($passRate, 0). '%';

      $ok = $inspectionData->total_ok ?? 0;
$okRepair = $inspectionData->total_ok_repair ?? 0;
$totalCheckItem = $inspectionData->total_check ?? 0;

if ($totalCheckItem > 0) {
    $performa = (($ok - $okRepair) / $totalCheckItem) * 100;
} else {
    $performa = 0;
}
$totals['Performa (%)'][$date] = round($performa, 0). '%';



        // 100% (A)/Sampling (S) - sesuaikan logika sampling jika ada
        $totals['100% (A)/Sampling (S)'][$date] = 0;
    }

    // Hitung total Pass Rate (%) keseluruhan
    $totalCheckAll = $totals['Total Check']['total'];
    $totalOkAll = $totals['Total OK']['total'];
    $totals['Pass Rate (%)']['total'] = $totalCheckAll > 0 ? round(($totalOkAll / $totalCheckAll) * 100, 0). '%' : '0%';

    // Total Performa (%) dan Sampling (sesuaikan jika ada)
    $totalOkAll = $totals['Total OK']['total'];
$totalOkRepairAll = $totals['Total OK Repair']['total'];
$totalCheckAll = $totals['Total Check']['total'];

if ($totalCheckAll > 0) {
    $totals['Performa (%)']['total'] = round((($totalOkAll - $totalOkRepairAll) / $totalCheckAll) * 100, 0). '%';

} else {
    $totals['Performa (%)']['total'] = '0%';
}

    $totals['100% (A)/Sampling (S)']['total'] = 0;
    // --- END OF TAMBAHAN ---

    $passRateByDate = [];
foreach ($allDates as $date) {
    // Ambil pass rate tiap tanggal, jika tidak ada isi 0
    $passRateByDate[] = $totals['Pass Rate (%)'][$date] ?? 0;
}

$summary = [
    'ok_percentage' => '0%',
    'ng_percentage' => '0%',
    'ok_repair_percentage' => '0%',
];

// Pastikan total check tidak nol agar tidak error pembagian
$totalCheckAll = $totals['Total Check']['total'] ?? 0;
$totalOkAll = $totals['Total OK']['total'] ?? 0;
$totalNgAll = $totals['Total NG']['total'] ?? 0;
$totalOkRepairAll = $totals['Total OK Repair']['total'] ?? 0;

if ($totalCheckAll > 0) {
    $summary['ok_percentage'] = round(($totalOkAll / $totalCheckAll) * 100) . '%';
    $summary['ng_percentage'] = round(($totalNgAll / $totalCheckAll) * 100) . '%';
    $summary['ok_repair_percentage'] = round(($totalOkRepairAll / $totalCheckAll) * 100) . '%';
}


    return view('qc.detail-incoming', compact(
        'inspection',
        'itemLabels',
        'defectTotals',
        'dates',
         'allDates',       // semua tanggal dari 1 sampai akhir bulan
        'totalCheck',
        'defectLabels',
        'defectValues',
        'totalOk',
        'totalNg',
        'passRateByDate', // tambahkan ini
        'totals',  // jangan lupa lempar ke view
         'summary'
    ));
}


      public function data(Request $request)
{
    $query = IncomingInspection::with(['items']);

    if ($request->inspection_number) {
        $query->where('inspection_number', 'like', '%' . $request->inspection_number . '%');
    }

    if ($request->inspection_post) {
        $query->where('inspection_post', $request->inspection_post);
    }

    if ($request->inspection_date) {
        [$start, $end] = explode(' to ', $request->inspection_date);
        $query->whereBetween('inspection_date', [$start, $end]);
    }

   if ($request->supplier_code) {
    $query->whereHas('supplier', function ($q) use ($request) {
        $q->where('code', $request->supplier_code);
    });
}


if ($request->part_name) {
    $query->whereHas('article', function ($q) use ($request) {
        $q->where('description', 'like', '%' . $request->part_name . '%');
    });
}

 $query->orderByRaw("FIELD(status, 'DRAFT', 'VERIFIED', 'POSTED')")
          ->orderBy('created_at', 'desc');

   return DataTables::of($query)
   ->addColumn('total_check', function ($row) {
    return $row->items->sum(fn($item) => $item->inspection->total_check ?? 0);
})
->addColumn('total_ok', function ($row) {
    $val = $row->items->sum(fn($item) => $item->inspection->total_ok ?? 0);
    return '<span class="text-green-600 font-semibold">'.$val.'</span>';
})
->addColumn('total_ng', function ($row) {
    $val = $row->items->sum(fn($item) => $item->inspection->total_ng ?? 0);
    return '<span class="text-red-600 font-semibold">'.$val.'</span>';
})
->addColumn('total_ok_repair', function ($row) {
    $val = $row->items->sum(fn($item) => $item->inspection->total_ok_repair ?? 0);
    return '<span class="text-yellow-600 font-semibold">'.$val.'</span>';
})
->addColumn('pass_rate', function ($row) {
    $totalCheck = $row->items->sum(fn($item) => $item->inspection->total_check ?? 0);
    $totalOk = $row->items->sum(fn($item) => $item->inspection->total_ok ?? 0);
    if ($totalCheck > 0) {
        return round(($totalOk / $totalCheck) * 100, 0) . '%';
    }
    return '0%';
})
->addColumn('performa', function ($row) {
    $totalCheck = $row->items->sum(fn($item) => $item->inspection->total_check ?? 0);
    $totalOk = $row->items->sum(fn($item) => $item->inspection->total_ok ?? 0);
    $totalOkRepair = $row->items->sum(fn($item) => $item->inspection->total_ok_repair ?? 0);
    if ($totalCheck > 0) {
        return round((($totalOk - $totalOkRepair) / $totalCheck) * 100, 0) . '%';
    }
    return '0%';
})
    // Tambahkan kolom berwarna untuk inspection_post
    ->addColumn('status', function ($row) {
        switch ($row->status) {
    case 'DRAFT':
        $colorClass = 'bg-gray-400 text-white';
        break;
    case 'VERIFIED':
        $colorClass = 'bg-green-400 text-white';
        break;
    default:
        $colorClass = 'bg-gray-200 text-gray-800';
        break;
}


        return '<span class="px-2 py-1 rounded text-sm font-semibold '.$colorClass.'">'
            . ucfirst($row->status) . '</span>';
    })

    ->editColumn('part_name', function ($row) {
    $name = optional($row->article)->description ?? '-';
    return "<span class='text-sm text-gray-500'>{$name}</span>";
})


    ->editColumn('supplier', function ($row) {
    $name = optional($row->supplier)->name ?? '-';
    return "<span class='text-sm text-gray-500'>{$name}</span>";
})

->editColumn('periode', function ($row) {
    if (!$row->periode) return "<span class='text-sm text-gray-500'>-</span>";

    // $row->periode diasumsikan format "YYYY-MM"
    $months = [
        '01' => 'JANUARI',
        '02' => 'FEBRUARI',
        '03' => 'MARET',
        '04' => 'APRIL',
        '05' => 'MEI',
        '06' => 'JUNI',
        '07' => 'JULI',
        '08' => 'AGUSTUS',
        '09' => 'SEPTEMBER',
        '10' => 'OKTOBER',
        '11' => 'NOVEMBER',
        '12' => 'DESEMBER',
    ];

    [$year, $month] = explode('-', $row->periode);
    $monthName = $months[$month] ?? $month;

    return "<span class='text-sm text-gray-500'>{$year} {$monthName} </span>";
})


    ->editColumn('created_by', function ($row) {
    return optional($row->user)->name ?? '-';
})

    ->editColumn('created_at', function ($row) {
        return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
    })

  ->addColumn('action', function ($row) {
    $id         = $row->id;
    $number     = $row->inspection_number;
    $dropdownId = 'dropdown-' . $row->id;
    $detail_url = route('qc.incoming.show', ['id' => $row->id]);
    $delete_url = route('qc.inspections.destroy', $row->id); // route delete

    // Tombol verified default kosong
    $verifiedBtn = '';

    // Cek role & department + status draft
    if (
        auth()->user()->roles->contains('name', 'Supervisor Special Access') &&
        auth()->user()->departments->contains('name', 'Quality Control') &&
        strtoupper($row->status) === 'DRAFT'
    ) {
        $verifiedBtn = '
            <button 
                type="button" 
                class="btn-verified w-full text-left px-4 py-2 text-green-600 hover:bg-green-600 hover:text-white"
                data-id="' . $id . '">
                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>Verified
            </button>
        ';
    }

    return '
    <div class="relative inline-block text-left">
        <button type="button"
            data-dropdown-id="' . $dropdownId . '"
            onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
            class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
            <i data-feather="align-justify"></i>
        </button>
        <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
            <div class="py-1 text-sm text-gray-700">
                <a href="' . $detail_url . '" class="block px-4 py-2 hover:bg-gray-100">
                    <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
                </a>
                ' . $verifiedBtn . '
                <button 
                    type="button" 
                    class="btn-delete-inspection w-full text-left px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
                    data-url="' . $delete_url . '"
                    data-id="'. $id . '"
                    data-number="'. $number . '">
                    <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                </button>
            </div>
        </div>
    </div>';
})



    ->rawColumns(['status', 'total_ok', 'total_ng', 'total_ok_repair', 'part_name', 'supplier', 'created_by', 'periode', 'action'])
    ->make(true);

}

    
      public function create()
{
    
    $suppliers = Supplier::orderBy('name')->get();

    $dailyInspections = Inspection::where('inspection_post', 'Incoming')
        ->orderBy('created_at', 'desc')
        ->get();

    $articles = Article::whereIn('article_type', ['RMNP', 'RMP'])
        ->orderBy('description')
        ->get();

    return view('qc.create-incoming', compact('suppliers', 'dailyInspections', 'articles'));
}

public function store(Request $request)
{
    $request->validate([
        'supplier_code' => 'required|string',
        'article_code'  => 'required|string',
        'periode'       => 'required|date_format:Y-m',
        'inspection_ids'=> 'required|array|min:1',
    ]);

    DB::beginTransaction();
    try {
        // Cek apakah kombinasi sudah ada
        $exists = IncomingInspection::where('supplier_code', $request->supplier_code)
            ->where('article_code', $request->article_code)
            ->where('periode', $request->periode)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Data dengan Supplier, Artikel, dan Periode tersebut sudah dibuat sebelumnya!'
            ], 422);
        }

        // Generate nomor incoming
        $generateIncomingNumber = $this->generateIncomingNumber();

        // Simpan master inspection
        $inspection = IncomingInspection::create([
            'incoming_number' => $generateIncomingNumber,
            'supplier_code'   => $request->supplier_code,
            'periode'         => $request->periode,
            'article_code'    => $request->article_code,
            'created_by'      => auth()->id(),
            'status'          => 'DRAFT',
        ]);

        // Simpan detail inspection
        foreach ($request->inspection_ids as $id) {
            IncomingInspectionItem::create([
                'incoming_inspection_id' => $inspection->id,
                'inspection_id'          => $id,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!'
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

private function generateIncomingNumber()
{
    // Ambil tahun dan bulan sekarang
    $year = date('Y');
    $monthNumber = (int)date('m');

    // Konversi bulan ke angka romawi
    $monthRoman = $this->toRoman($monthNumber);

    // Prefix static
    $prefix = "QC-IN-ASN-{$year}-{$monthRoman}";

    // Cari record terakhir dengan prefix yang sama, order descending by incoming_number
    $lastRecord = IncomingInspection::where('incoming_number', 'like', "$prefix-%")
        ->orderBy('incoming_number', 'desc')
        ->first();

    if ($lastRecord) {
        // Ambil nomor urut dari incoming_number terakhir (4 digit terakhir)
        $lastNumber = intval(substr($lastRecord->incoming_number, -4));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    // Format nomor urut 4 digit dengan leading zero
    $numberFormatted = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    return "{$prefix}-{$numberFormatted}";
}

// Fungsi bantu untuk konversi angka ke angka romawi
private function toRoman($num)
{
    $map = [
        10 => 'X',
        9  => 'IX',
        8  => 'VIII',
        7  => 'VII',
        6  => 'VI',
        5  => 'V',
        4  => 'IV',
        3  => 'III',
        2  => 'II',
        1  => 'I',
        0  => ''
    ];

    foreach ($map as $value => $roman) {
        if ($num == $value) {
            return $roman;
        }
    }
    return '';
}

public function verified($id)
    {
        $item = IncomingInspection::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $item->status = 'VERIFIED';
        $item->save();

        return response()->json(['success' => true]);
    }

}
