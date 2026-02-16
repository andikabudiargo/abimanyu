<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionDefect;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InspectionController extends Controller
{
      public function index(Request $request)
{
    // Ambil range tahun dari DB
    $range = Inspection::selectRaw('
            MIN(YEAR(inspection_date)) as min_year,
            MAX(YEAR(inspection_date)) as max_year
        ')->first();

    // Fallback kalau DB masih kosong
    $minYear = $range->min_year ?? now()->year;
    $maxYear = $range->max_year ?? now()->year;

    $years = range($minYear, $maxYear);

    // ================= DEFAULT FILTER =================
    $selectedYear = $request->year 
        ? (int) $request->year 
        : now()->year;

    // Kalau month tidak dikirim → default bulan sekarang
    // Tapi kalau dikirim kosong ("") → artinya All Month
    $selectedMonth = $request->has('month')
        ? ($request->month !== '' ? (int)$request->month : null)
        : now()->month;

    // ================= QUERY =================
    $query = Inspection::query()
        ->whereYear('inspection_date', $selectedYear);

    if (!is_null($selectedMonth)) {
        $query->whereMonth('inspection_date', $selectedMonth);
    }

    $data = $query->get();

    $suppliers = Supplier::orderBy('name')->get();
    $customers = Customer::orderBy('name')->get();

    $articles = Article::whereIn('article_type', ['RMP', 'RMNP', 'FG'])
        ->orderBy('description')
        ->get();

    return view(
        'qc.daily-inspection',
        compact(
            'suppliers',
            'customers',
            'articles',
            'years',
            'selectedYear',
            'selectedMonth',
            'data'
        )
    );
}



     public function data(Request $request)
{
    $query = Inspection::with(['user', 'article', 'supplier']);

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

 if ($request->supplier) {
        $query->where('supplier_code', $request->supplier);
    }

if ($request->spraybooth) {
        $query->where('spraybooth', $request->spraybooth);
    }

if ($request->part_name) {
        $query->where('part_name', $request->part_name);
    }

$query->orderBy('created_at', 'desc');

 return DataTables::of($query)
    // Tambahkan kolom berwarna untuk inspection_post
    ->editColumn('inspection_post', function ($row) {
        $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

        return match ($row->inspection_post) {
            'Incoming' => '<span class="bg-yellow-500 '.$commonClasses.'">Incoming</span>',
            'Unloading' => '<span class="bg-red-500 '.$commonClasses.'">Unloading</span>',
            'Buffing' => '<span class="bg-blue-500 '.$commonClasses.'">Buffing</span>',
            'Touch Up' => '<span class="bg-green-500 '.$commonClasses.'">Touch Up</span>',
            'Final' => '<span class="bg-teal-400 '.$commonClasses.'">Final</span>',
            'Outgoing' => '<span class="bg-purple-400 '.$commonClasses.'">Outgoing</span>',
            default => '-'
        };
    })

    ->editColumn('part_name', function ($row) {
        $code = $row->part_name;
        $name = optional($row->article)->description ?? '-';
        return "<span class='font-semibold text-gray-800'>{$code}</span><br><span class='text-sm text-gray-500'>{$name}</span>";
    })

    ->editColumn('user_id', function ($row) {
        return optional($row->user)->name ?? '-';
    })

    ->editColumn('total_check', fn($row) => '<span class="font-semibold text-gray-800">'.$row->total_check.'</span>')
    ->editColumn('total_ok', fn($row) => '<span class="text-green-600 font-semibold">'.$row->total_ok.'</span>')
    ->editColumn('total_ok_repair', fn($row) => '<span class="text-yellow-600 font-semibold">'.$row->total_ok_repair.'</span>')
    ->editColumn('total_ng', fn($row) => '<span class="text-red-600 font-semibold">'.$row->total_ng.'</span>')

  ->addColumn('pass_rate', function ($row) {

    // Total check minimal 1 supaya tidak division by zero
    $totalCheck = $row->total_check ?: 1;

    // Hitung pass rate sesuai rumus baru
    $totalPass = $row->total_ok;

    $passRate = ($totalPass / $totalCheck) * 100;

    return '<span class="text-green-600 font-semibold">' . number_format($passRate, 0) . '%</span>';
})


 // Tambahkan kolom persentase
->addColumn('pass_trough', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $totalPassTrough = $row->total_check - $row->total_ng - $row->total_ok_repair;
    $passTrough = ($totalPassTrough / $totalCheck) * 100;
    return '<span class="text-yellow-600 font-semibold">' . number_format($passTrough, 0) . '%</span>';
})

->addColumn('ok_repair_rate', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $okRepairRate = ($row->total_ok_repair / $totalCheck) * 100;
    return '<span class="text-yellow-600 font-semibold">' . number_format($okRepairRate, 0) . '%</span>';
})

->addColumn('ng_rate', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $ngRate = ($row->total_ng / $totalCheck) * 100;
    return '<span class="text-red-600 font-semibold">' . number_format($ngRate, 0) . '%</span>';
})

->addColumn('partner_name', function ($row) {
    return optional($row->partner)->name ?? '-';
})




 ->editColumn('inspection_number', function ($row) {
    $colorClass = '';
    switch ($row->inspection_post) {
        case 'Incoming':
            $colorClass = 'bg-yellow-500';
            break;
        case 'Unloading':
            $colorClass = 'bg-red-500';
            break;
        case 'Buffing':
            $colorClass = 'bg-blue-500';
            break;
        case 'Touch Up':
            $colorClass = 'bg-green-500';
            break;
        case 'Final':
            $colorClass = 'bg-teal-400';
            break;
        case 'Outgoing':
            $colorClass = 'bg-purple-400';
            break;
        default:
            $colorClass = 'bg-gray-300';
    }

    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->inspection_number . '</span>';
})


    ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i'))

    ->addColumn('action', function ($row) {
        $id = $row->id;
        $number = $row->inspection_number;
        $dropdownId = 'dropdown-' . $row->id;
        $detail_url = route('qc.inspections.show', ['id' => $row->id]);
        $delete_url = route('qc.inspections.destroy', $row->id);

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

    ->rawColumns([
        'inspection_number', 'inspection_post', 'part_name', 'partner_name', 'user_id', 'total_check', 
        'total_ok', 'total_ok_repair', 'total_ng', 'pass_trough',
        'pass_rate', 'ng_rate', 'ok_repair_rate', 'action'
    ])
    ->make(true);


}

public function getSummary(Request $request)
{
    $positions = ['Incoming', 'Unloading', 'Buffing', 'Touch Up', 'Final', 'Outgoing'];

    /* ================= RANGE DATE ================= */
    $dateFilter = $request->inspection_date;

if (!empty($dateFilter)) {

    // jika ada format range: "YYYY-MM-DD to YYYY-MM-DD"
    if (str_contains($dateFilter, ' to ')) {

        [$start, $end] = explode(' to ', $dateFilter);

    } else {

        // ✅ hanya satu tanggal
        $start = $dateFilter;
        $end   = $dateFilter;
    }

} else {

    // default hari ini
    $start = now()->toDateString();
    $end   = now()->toDateString();
}

$isSingleDay = ($start === $end);


    $supplier = $request->supplier;
    $spraybooth = $request->spraybooth;
    $part_name = $request->part_name;

    $result = [];

    foreach ($positions as $pos) {

        $query = DB::table('inspections')
            ->selectRaw("
                SUM(total_check) AS total_part,
                SUM(total_ok) AS total_ok,
                SUM(total_ok_repair) AS total_ok_repair,
                SUM(total_ng) AS total_ng
            ")
            ->where('inspection_post', $pos);

        /* ================= DATE FILTER ================= */
        if ($isSingleDay) {
            // ✅ hanya 1 hari
            $query->whereDate('inspection_date', $start);
        } else {
            // ✅ range tanggal
            $query->whereBetween('inspection_date', [$start, $end]);
        }

        /* ================= SUPPLIER FILTER ================= */
        if (!empty($supplier)) {
            $query->where('supplier_code', $supplier);
        }
 /* ================= SPRAYBOOTH FILTER ================= */
        if (!empty($spraybooth)) {
            $query->where('spraybooth', $spraybooth);
        }
        /* ================= PART NAME FILTER ================= */
        if (!empty($part_name)) {
            $query->where('part_name', $part_name);
        }

        $data = $query->first();

        $result[$pos] = [
            'pos'       => $pos,
            'total'     => $data->total_part ?? 0,
            'ok'        => $data->total_ok ?? 0,
            'ok_repair' => $data->total_ok_repair ?? 0,
            'ng'        => $data->total_ng ?? 0,
        ];
    }

    return response()->json([
        'start_date' => $start,
        'end_date'   => $end,
        'summary'    => $result
    ]);
}


private function resolveInspectionDate(Request $request, string $pos)
{
    $dateFilter = trim($request->inspection_date ?? '');

    // 1️⃣ User pilih tanggal / range
    if ($dateFilter !== '') {
        if (str_contains($dateFilter, ' to ')) {
            [$start, $end] = array_map('trim', explode(' to ', $dateFilter));
        } else {
            $start = $dateFilter;
            $end   = $dateFilter;
        }
        return [$start, $end, 'user'];
    }

    // 2️⃣ Default hari ini
    $today = now()->toDateString();

    $hasTodayData = DB::table('inspections')
        ->where('inspection_post', $pos)
        ->whereDate('inspection_date', $today)
        ->exists();

    if ($hasTodayData) {
        return [$today, $today, 'today'];
    }

    // 3️⃣ Fallback ke tanggal TERAKHIR ada data
    $latestDate = DB::table('inspections')
        ->where('inspection_post', $pos)
        ->max('inspection_date');

    if ($latestDate) {
        return [$latestDate, $latestDate, 'latest'];
    }

    // 4️⃣ DB kosong total
    return [null, null, 'empty'];
}

public function getTopDefect(Request $request)
{
    $pos = $request->pos;

    [$start, $end, $mode] = $this->resolveInspectionDate($request, $pos);

    if (!$start || !$end) {
        return response()->json([
            'mode' => 'empty',
            'message' => 'Belum ada data inspection'
        ]);
    }

    $isSingleDay = ($start === $end);

  $dateFilter = function ($query) use ($start, $end, $isSingleDay) {
    if ($isSingleDay) {
        // ⛔ JANGAN whereDate
        $query->where('i.inspection_date', '=', $start);
    } else {
        $query->whereBetween('i.inspection_date', [$start, $end]);
    }
};

    /* ================= TOTAL DEFECT ================= */

    $totalDefect = DB::table('inspection_defects as d')
        ->join('inspections as i', 'i.id', '=', 'd.inspection_id')
        ->where('i.inspection_post', $pos)
        ->where($dateFilter)
        ->sum('d.qty');

    /* ================= TOP DEFECT ================= */

    $topDefect = DB::table('inspection_defects as d')
        ->join('inspections as i', 'i.id', '=', 'd.inspection_id')
        ->join('defects as f', 'f.id', '=', 'd.defect_id')
        ->select(
            'f.defect as defect_name',
            'f.category',
            DB::raw('SUM(d.qty) as total_qty')
        )
        ->where('i.inspection_post', $pos)
        ->where($dateFilter)
        ->groupBy('f.id', 'f.defect', 'f.category')
        ->orderByDesc('total_qty')
        ->limit(10)
        ->get();

    /* ================= PERCENTAGE ================= */

    $topDefect = $topDefect->map(function ($item) use ($totalDefect) {
        $item->percentage = $totalDefect > 0
            ? round(($item->total_qty / $totalDefect) * 100)
            : 0;
        return $item;
    });

    /* ================= TOP PART ================= */

    $topPart = DB::table('inspection_defects as d')
        ->join('inspections as i', 'i.id', '=', 'd.inspection_id')
        ->join('articles as a', 'a.article_code', '=', 'i.part_name')
        ->select(
            'a.description as part_name',
            DB::raw('SUM(d.qty) as total_qty')
        )
        ->where('i.inspection_post', $pos)
        ->where($dateFilter)
        ->groupBy('a.description')
        ->orderByDesc('total_qty')
        ->limit(10)
        ->get();

    /* ================= SUMMARY ================= */

    $summary = DB::table('inspection_defects as d')
        ->join('inspections as i', 'i.id', '=', 'd.inspection_id')
        ->selectRaw('
            SUM(d.qty) as total_defect,
            COUNT(DISTINCT i.part_name) as total_part_type
        ')
        ->where('i.inspection_post', $pos)
        ->where($dateFilter)
        ->first();

    return response()->json([
        'mode'        => $mode, // today | latest | user
        'start_date'  => $start,
        'end_date'    => $end,
        'summary' => [
            'total_defect'     => $summary->total_defect ?? 0,
            'total_part_type' => $summary->total_part_type ?? 0,
        ],
        'top_defect' => $topDefect,
        'top_part'   => $topPart
    ]);
}

 public function getDataChart(Request $request)
{
    $year  = $request->year ?? now()->year;

    $month = $request->filled('month')
    ? (int) $request->month
    : null;

    /* ================= QUERY BASE ================= */

    $query = DB::table('inspections')
        ->whereYear('inspection_date', $year);

    if (!is_null($month)) {
        $query->whereMonth('inspection_date', $month);
    }

    /* ================= FILTER DINAMIS ================= */

    if ($request->category) {
        $query->where('category', $request->category);
    }

    if ($request->part_name) {
        $query->where('part_name', $request->part_name);
    }

    if ($request->inspection_post) {
        $query->where('inspection_post', $request->inspection_post);
    }

    if ($request->spraybooth) {
        $query->where('spraybooth', $request->spraybooth);
    }

if ($request->supplier) {
        $query->where('supplier_code', $request->supplier);
    }

    /* ========================================================= */
    /* ================= MODE: PER BULAN ======================= */
    /* ========================================================= */

    if (is_null($month)) {

        $rows = $query
    ->selectRaw('
        MONTH(inspection_date) as month,
        SUM(total_check) as total_check,
        SUM(total_ok) as total_ok,
        SUM(total_ok_repair) as total_ok_repair
    ')
    ->groupByRaw('MONTH(inspection_date)')
    ->orderByRaw('MONTH(inspection_date)')
    ->get()
    ->keyBy('month');


        $labels = [];
        $passRate = [];
        $passTrough = [];

        for ($m = 1; $m <= 12; $m++) {

            if (isset($rows[$m])) {
                $r = $rows[$m];
                $totalCheck = $r->total_check ?: 1;

                $pr = ($r->total_ok / $totalCheck) * 100;
                $pt = ($r->total_ok - $r->total_ok_repair) / $totalCheck * 100;
            } else {
                $pr = 0;
                $pt = 0;
            }

            $labels[] = \Carbon\Carbon::create()->month($m)->format('M');
            $passRate[] = round($pr, 0);
            $passTrough[] = round($pt, 0);
        }

        return response()->json([
            'mode'         => 'year',
            'labels'       => $labels,
            'pass_rate'    => $passRate,
            'pass_trough'  => $passTrough,
        ]);
    }

    /* ========================================================= */
    /* ================= MODE: PER HARI ======================== */
    /* ========================================================= */

    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

   $rows = $query
    ->selectRaw('
        DATE(inspection_date) as inspection_date,
        SUM(total_check) as total_check,
        SUM(total_ok) as total_ok,
        SUM(total_ok_repair) as total_ok_repair
    ')
    ->groupByRaw('DATE(inspection_date)')
    ->orderBy('inspection_date')
    ->get()
    ->keyBy(function ($item) {
        return date('j', strtotime($item->inspection_date));
    });


    $labels = [];
    $passRate = [];
    $passTrough = [];

    for ($day = 1; $day <= $totalDays; $day++) {

        if (isset($rows[$day])) {
            $r = $rows[$day];
            $totalCheck = $r->total_check ?: 1;

            $pr = ($r->total_ok / $totalCheck) * 100;
            $pt = ($r->total_ok - $r->total_ok_repair) / $totalCheck * 100;
        } else {
            $pr = 0;
            $pt = 0;
        }

        $labels[] = $day;
        $passRate[] = round($pr, 0);
        $passTrough[] = round($pt, 0);
    }

    return response()->json([
        'mode'         => 'month',
        'labels'       => $labels,
        'pass_rate'    => $passRate,
        'pass_trough'  => $passTrough,
    ]);
}

public function getPerformanceChart(Request $request)
{
    /* ================= RANGE TANGGAL ================= */

    $monthParam = $request->date ?? date('Y-m');
    $year  = substr($monthParam, 0, 4);
    $month = substr($monthParam, 5, 2);

    $start = date('Y-m-01', strtotime("$year-$month-01"));
    $end   = date('Y-m-t', strtotime("$year-$month-01"));

    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    /* ================= QUERY ================= */

    $rows = DB::table('inspections')
        ->select(
            'inspection_date',
            'spraybooth',
            DB::raw('SUM(total_check) as total_check'),
            DB::raw('SUM(total_ok) as total_ok')
        )
        ->whereBetween('inspection_date', [$start, $end])
        ->groupBy('inspection_date', 'spraybooth')
        ->orderBy('inspection_date')
        ->get();

    /* ================= PREPARE DATA ================= */

    $sprayList = [];

    for ($i = 1; $i <= 5; $i++) {
        foreach (['A','B','C'] as $g) {
            $sprayList[] = "Spraybooth {$i}{$g}";
        }
    }

    $chartData = [];

    foreach ($sprayList as $spray) {
        $chartData[$spray] = array_fill(1, $totalDays, 0);
    }

    foreach ($rows as $r) {

        $day = date('j', strtotime($r->inspection_date));

        if ($r->total_check > 0) {
            $pt = ($r->total_ok / $r->total_check) * 100;
        } else {
            $pt = 0;
        }

        if (isset($chartData[$r->spraybooth])) {
            $chartData[$r->spraybooth][$day] = round($pt, 1);
        }
    }

    $days = range(1, $totalDays);

    return response()->json([
        'days' => $days,
        'datasets' => $chartData
    ]);
}


public function paretoDefect(Request $request)
{
    $month       = $request->filled('month') ? (int)$request->month : null;
    $year        = $request->year ?? now()->year;
    /*
    |--------------------------------------------------------------------------
    | AMBIL DEFECT CATEGORY = NG
    |--------------------------------------------------------------------------
    */
$query = DB::table('defects as d')

    ->leftJoin('inspection_defects as idf', 'd.id', '=', 'idf.defect_id')

    ->leftJoin('inspections as i', function ($join) use ($request, $month, $year) {

        $join->on('i.id', '=', 'idf.inspection_id');

        // ===== FILTER TANGGAL =====
        $join->whereYear('i.inspection_date', $year);

        if ($month) {
            $join->whereMonth('i.inspection_date', $month);
        }

        // ===== FILTER DINAMIS =====
        if ($request->inspection_post) {
            $join->where('i.inspection_post', $request->inspection_post);
        }

        if ($request->spraybooth) {
            $join->where('i.spraybooth', $request->spraybooth);
        }

        if ($request->part_name) {
            $join->where('i.part_name', $request->part_name);
        }

        if ($request->supplier) {
            $join->where('i.supplier_code', $request->supplier);
        }
    })

    ->select(
        'd.id',
        'd.defect',
        DB::raw('COALESCE(SUM(idf.qty),0) as total')
    )

    ->where('d.category', 'NG');


    /*
    |--------------------------------------------------------------------------
    | GROUPING
    |--------------------------------------------------------------------------
    */

    $data = $query
    ->groupBy('d.id','d.defect')
    ->havingRaw('SUM(idf.qty) IS NOT NULL')
    ->orderByDesc('total')
    ->get();


    /*
    |--------------------------------------------------------------------------
    | PISAHKAN YANG ADA KONTRIBUSI
    |--------------------------------------------------------------------------
    */


    $withContribution = [];
    $otherTotal = 0;

    foreach ($data as $row) {

        if ($row->total > 0) {
            $withContribution[] = $row;
        } else {
            $otherTotal += 0; // tetap dihitung struktur statistik
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAHKAN OTHER (JIKA ADA DEFECT 0)
    |--------------------------------------------------------------------------
    */

    if ($data->count() !== count($withContribution)) {
        $withContribution[] = (object)[
            'defect' => 'Other',
            'total'  => $otherTotal
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG PARETO %
    |--------------------------------------------------------------------------
    */

    $grandTotal = collect($withContribution)->sum('total');

   $labels = [];
$values = [];
$percentages = [];
$cumulativePercent = [];

$cumulative = 0;


    foreach ($withContribution as $row) {

    $labels[] = $row->defect;
    $values[] = (int)$row->total;

    // kontribusi %
    $percent = $grandTotal > 0
        ? ($row->total / $grandTotal) * 100
        : 0;

    $percent = round($percent, 0);

    $percentages[] = $percent;

    // kumulatif pareto
    $cumulative += $percent;
    $cumulativePercent[] = round($cumulative, 0);
}


    return response()->json([
    'labels'      => $labels,
    'values'      => $values,        // qty defect
    'percentages' => $percentages,   // tinggi bar
    'cumulative'  => $cumulativePercent
]);

}




public function monthlyTrend(Request $request)
{
    $month = $request->month ?? date('m');
    $year  = $request->year ?? date('Y');

    // Tentukan jumlah hari dalam bulan tertentu
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Ambil data inspeksi per tanggal
    $records = DB::table('inspections')
        ->selectRaw('
            DATE(inspection_date) as d,
            SUM(total_check) as total_check,
            SUM(total_ok) as total_ok,
            SUM(total_ok_repair) as total_ok_repair,
            SUM(total_ng) as total_ng
        ')
        ->whereMonth('inspection_date', $month)
        ->whereYear('inspection_date', $year)
        ->groupBy('d')
        ->orderBy('d')
        ->get()
        ->keyBy('d'); // memudahkan merk lookup tanggal

    // siapkan array final per tanggal
    $trend = [];

    for ($day = 1; $day <= $daysInMonth; $day++) {

        $date = sprintf("%04d-%02d-%02d", $year, $month, $day);

        if (isset($records[$date])) {

            $row = $records[$date];

            $total_check = $row->total_check;
            $total_ok = $row->total_ok;
            $total_ok_repair = $row->total_ok_repair;

            $pass_rate = $total_check ? round(($total_ok / $total_check) * 100, 2) : 0;
            $pass_trough = $total_check ? round((($total_ok + $total_ok_repair) / $total_check) * 100, 2) : 0;

            $trend[] = [
                "date" => $date,
                "total_check" => $total_check,
                "pass_rate" => $pass_rate,
                "pass_trough" => $pass_trough
            ];

        } else {
            // tanggal tanpa data inspection
            $trend[] = [
                "date" => $date,
                "total_check" => 0,
                "pass_rate" => 0,
                "pass_trough" => 0
            ];
        }
    }

    return response()->json($trend);
}



     public function unloading() {
        return view('qc.unloading');
    }

     public function create() {
         $suppliers = Supplier::orderBy('name')->get();
            $customers = Customer::orderBy('name')->get();
        return view('qc.staff-daily-inspection', compact('suppliers','customers'));
    }

     public function createOperator() {
         $suppliers = Supplier::orderBy('name')->get();
            $customers = Customer::orderBy('name')->get();
        return view('qc.create-daily-inspection', compact('suppliers','customers'));
    }

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'inspection_post' => 'required|string',
        'inspection_date' => 'required|date',
        'part_name'       => 'required|string',
        'supplier_code'   => 'required|string',

        'check_method' => 'nullable|string',
        'spraybooth'   => 'nullable|string',
        'qty_received' => 'nullable|integer|min:1',

        'total_check'      => 'required|integer|min:1',
        'total_ok'         => 'nullable|integer|min:0',
        'total_ok_repair'  => 'nullable|integer|min:0',
        'total_ng'         => 'nullable|integer|min:0',

        'defect_id'        => 'nullable|array',
        'defect_id.*'      => 'required|integer|exists:defects,id',

        'qty'              => 'nullable|array',
        'qty.*'            => 'required|integer|min:1',

        'ok_repair'        => 'nullable|array',
        'ok_repair.*'      => 'nullable|integer|min:0',

        'note_defect'      => 'nullable|array',
        'note_defect.*'    => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();

    try {

        $inspectionNumber = $this->generateInspectionNumber(
            $request->inspection_post,
            $request->inspection_date
        );

        $inspection = Inspection::create([
            'inspection_number' => $inspectionNumber,
            'user_id'           => Auth::id(),
            'inspection_date'   => $request->inspection_date,
            'inspection_post'   => $request->inspection_post,
            'supplier_code'     => $request->supplier_code,
            'part_name'         => $request->part_name,
            'check_method'      => $request->check_method,
            'spraybooth'        => $request->spraybooth,
            'qty_received'      => $request->qty_received,
            'total_check'       => $request->total_check,
            'total_ok'          => $request->total_ok ?? 0,
            'total_ok_repair'   => $request->total_ok_repair ?? 0,
            'total_ng'          => $request->total_ng ?? 0,
        ]);

        if ($request->filled('defect_id')) {
            foreach ($request->defect_id as $i => $defectId) {

                if (!isset($request->qty[$i])) {
                    continue; // safety net
                }

                InspectionDefect::create([
                    'inspection_id' => $inspection->id,
                    'defect_id'     => $defectId,
                    'qty'           => $request->qty[$i],
                    'ok_repair'     => $request->ok_repair[$i] ?? 0,
                    'note_defect'   => $request->note_defect[$i] ?? null,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Inspection saved successfully',
            'inspection_number' => $inspectionNumber
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to save inspection',
            'error'   => $e->getMessage()
        ], 500);
    }
}


   protected function generateInspectionNumber($inspection_post, $inspection_date)
{
    $prefix = 'QC';

    $romawiBulan = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];

    $postMapping = [
        'INCOMING'  => 'I',
        'UNLOADING' => 'U',
        'BUFFING'   => 'B',
        'TOUCH UP'  => 'T',
        'FINAL'     => 'F',
        'OUTGOING'  => 'O',
    ];

    $post = strtoupper(trim($inspection_post));
    $code = $postMapping[$post] ?? 'X';

    $bulan = (int) date('m', strtotime($inspection_date));
    $tahun = date('Y', strtotime($inspection_date));
    $bulanRomawi = $romawiBulan[$bulan];

    $count = Inspection::whereDate('inspection_date', $inspection_date)
        ->where('inspection_post', $inspection_post)
        ->count();

    $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return "{$prefix}{$code}-ASN-{$tahun}-{$bulanRomawi}-{$sequence}";
}


public function getInspectionNumbers(Request $request)
{
    $year = $request->input('year');
    $month = $request->input('month');
    $supplier = $request->input('supplier_code');
    $articleCode = $request->input('article_code');

   $inspections = Inspection::with(['inspection_defects.defect'])
    ->select('id', 'inspection_number', 'inspection_date', 'qty_received', 'total_ok', 'total_check', 'total_ng', 'total_ok_repair')
    ->where('inspection_post', 'Incoming')
    ->whereYear('inspection_date', $year)
    ->whereMonth('inspection_date', $month)
    ->where('supplier_code', $supplier)
    ->when($articleCode, function ($query) use ($articleCode) {
        $query->where('part_name', $articleCode); // hanya filter kalau diisi
    })
    ->orderByDesc('inspection_date')
    ->get();

    return response()->json($inspections);
}


 public function show($id)
{
   $inspection = Inspection::with('supplier','article', 'inspection_defects')->findOrFail($id);
    return view('qc.detail-daily-inspection', compact('inspection'));
}

public function destroy($id)
{
    $inspection = Inspection::findOrFail($id);
    $inspection->delete();

    return redirect()->route('qc.inspections.index')->with('success', 'Inspection berhasil dihapus');
}





}