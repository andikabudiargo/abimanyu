<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class CalculatorBOMController extends Controller
{
    public function index()
    {
        return view('accounting.calculator-bom');
    }

     public function cekcm()
    {
        return view('accounting.chemical-check');
    }

     public function uploadCM(Request $request)
    {
         $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        // Load Excel dengan PhpSpreadsheet
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Simpan ke cache selama 1 jam
       Cache::forever('cm_data', $rows);

        return response()->json([
            'status' => 'success',
            'message' => 'File berhasil diupload dan disimpan di cache',
            'rows_count' => count($rows),
        ]);
    }

   public function getCM()
{
    $cmList = DB::table('boms')
        ->select('article_cm as code', 'article_cm_desc as name')
        ->whereNotNull('article_cm')
        ->where('article_cm', '!=', '')
        ->whereNotNull('article_cm_desc')
        ->where('article_cm_desc', '!=', '')
        ->groupBy('article_cm', 'article_cm_desc') // unik
        ->orderBy('article_cm')
        ->get();

    return response()->json($cmList);
}



    public function getFG(Request $request)
{
    $cmCode = $request->query('cm');

    if (!$cmCode) {
        return response()->json([]);
    }

    $fgList = DB::table('boms')
        ->select(
            'article_fg as code',
            'article_fg_desc as name'
        )
        ->where('article_cm', $cmCode)
        ->whereNotNull('article_fg')
        ->where('article_fg', '!=', '')
        ->whereNotNull('article_fg_desc')
        ->where('article_fg_desc', '!=', '')
        ->groupBy('article_fg', 'article_fg_desc') // pastikan unik
        ->orderBy('article_fg')
        ->get();

    return response()->json($fgList);
}


public function exportCMFG()
{
     $rows = DB::table('boms')
        ->select(
            'article_cm as cm_code',
            'article_cm_desc as cm_name',
            'qty as qty_bom',
            'uom',
            'article_fg as fg_code',
            'article_fg_desc as fg_name'
        )
        ->whereNotNull('article_cm')
        ->whereNotNull('article_fg')
        ->whereNotNull('article_fg_desc')
        ->get();

    $data = collect($rows)
        ->unique(function ($item) {
            // unik per kombinasi CM + FG
            return $item->cm_code . '|' . $item->fg_code;
        })
        ->values();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

   // Header
$sheet->setCellValue('A1', 'CM Code')
      ->setCellValue('B1', 'CM Name')
      ->setCellValue('C1', 'Qty BOM')
      ->setCellValue('D1', 'UOM')
      ->setCellValue('E1', 'FG Code')
      ->setCellValue('F1', 'FG Name');

// Styling header: background gelap dan teks cerah
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], // teks putih
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD'], // biru gelap
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Isi data
$rowNumber = 2;
foreach ($data as $item) {
    $sheet->setCellValue('A'.$rowNumber, $item->cm_code);
    $sheet->setCellValue('B'.$rowNumber, $item->cm_name);
    $sheet->setCellValue('C'.$rowNumber, $item->qty_bom);
    $sheet->setCellValue('D'.$rowNumber, $item->uom);
    $sheet->setCellValue('E'.$rowNumber, $item->fg_code);
    $sheet->setCellValue('F'.$rowNumber, $item->fg_name);
    $rowNumber++;
}


// Auto size kolom berdasarkan isi
foreach(range('A','F') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

    // Download file Excel
    $fileName = 'CM_FG_List.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. $fileName .'"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');

        // Load Excel
        $spreadsheet = IOFactory::load($file->getPathname());

        $cacheData = [];

        // Ambil semua sheet
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $sheetName = $worksheet->getTitle();
            $rows = $worksheet->toArray();
            $cacheData[$sheetName] = $rows;
        }

        // Simpan semua sheet ke cache selama 1 jam
    Cache::forever('bom_data', $cacheData);


        return response()->json([
            'status' => 'success',
            'message' => 'File berhasil diupload dan semua sheet disimpan di cache',
        ]);
    }


public function getFinishGoods(Request $request)
{
    $term  = trim($request->query('term', ''));
    $page  = max(1, (int) $request->query('page', 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // Query untuk mengambil FG unik
    $query = DB::table('boms')
        ->select(
            'article_fg as id',
            DB::raw("CONCAT(article_fg, ' - ', article_fg_desc) as text")
        )
        ->whereNotNull('article_fg')
        ->where('article_fg', '!=', '');

    // Filter search
    if ($term !== '') {
        $query->where(function($q) use ($term) {
            $q->where('article_fg', 'LIKE', "%{$term}%")
              ->orWhere('article_fg_desc', 'LIKE', "%{$term}%");
        });
    }

    // === FIX: Hitung total FG unik tanpa limit, tanpa offset ===
    $total = DB::table('boms')
        ->select('article_fg')
        ->whereNotNull('article_fg')
        ->where('article_fg', '!=', '')
        ->when($term !== '', function($q) use ($term) {
            $q->where(function($q2) use ($term) {
                $q2->where('article_fg', 'LIKE', "%{$term}%")
                   ->orWhere('article_fg_desc', 'LIKE', "%{$term}%");
            });
        })
        ->distinct()
        ->count('article_fg');

    // Ambil data unik per halaman
    $results = $query
        ->groupBy('article_fg', 'article_fg_desc')
        ->orderBy('article_fg')
        ->offset($offset)
        ->limit($limit)
        ->get();

    return response()->json([
        'results' => $results,
        'pagination' => [
            'more' => ($offset + $limit) < $total
        ]
    ]);
}




public function getChemical(Request $request)
{
    $term  = trim($request->query('term', ''));
    $page  = max(1, (int) $request->query('page', 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // Query dasar: ambil CM unik
    $query = DB::table('boms')
        ->select(
            'article_cm as id',
            DB::raw("CONCAT(article_cm, ' - ', article_cm_desc) as text")
        )
        ->whereNotNull('article_cm')
        ->where('article_cm', '!=', '')
        ->whereNotNull('article_cm_desc')
        ->where('article_cm_desc', '!=', '')
        ->groupBy('article_cm', 'article_cm_desc');

    // Filter by term
    if ($term !== '') {
        $query->where(function($q) use ($term) {
            $q->where('article_cm', 'LIKE', "%{$term}%")
              ->orWhere('article_cm_desc', 'LIKE', "%{$term}%");
        });
    }

    // Hitung total untuk pagination
    $total = $query->count();

    // Ambil data berdasarkan limit & offset
    $results = $query
        ->orderBy('article_cm')
        ->offset($offset)
        ->limit($limit)
        ->get();

    // Return response format Select2
    return response()->json([
        'results' => $results,
        'pagination' => [
            'more' => ($offset + $limit) < $total
        ]
    ]);
}


public function getRMByFG(Request $request)
{
    $fgCode = $request->query('article_fg');
    $periodeSelected = $request->query('periode'); // bulan 1–12

    if (!$fgCode) {
        return response()->json(['data' => []]);
    }

    // ================================
    // 1. AMBIL RM DARI TABEL BOM
    // ================================
    $bomRows = DB::table('boms')
        ->where('article_fg', $fgCode)
        ->get();

    if ($bomRows->isEmpty()) {
        return response()->json(['data' => []]);
    }

    $rmList = [];
    foreach ($bomRows as $row) {
        $rmCode = trim($row->rm_code);

        if (!isset($rmList[$rmCode])) {
            $rmList[$rmCode] = [
                'article_rm' => $rmCode,
                'name_rm'    => $row->rm_name,
                'qty_bom'    => (float)$row->qty_bom,
                'uom'        => $row->uom ?? 'PCS',
                'price'      => 0,
                'consumption'=> 0,
                'qty_sales'  => 0,
                'total'      => 0
            ];
        }
    }

    // ================================
    // 2. AMBIL PRICE DARI LPB
    // ================================
    $lpbQuery = DB::table('lpb_temporary')
        ->whereIn('rm_code', array_keys($rmList));

    if ($periodeSelected) {
        $lpbQuery->whereMonth('tanggal', $periodeSelected);
    }

    // Ambil harga terakhir dari LPB
    $lpbRows = $lpbQuery
        ->orderBy('tanggal', 'desc')
        ->get();

    $lpbData = [];
    foreach ($lpbRows as $row) {
        if (!isset($lpbData[$row->rm_code])) {
            $lpbData[$row->rm_code] = (float)$row->price;
        }
    }

    // Hitung consumption
    foreach ($rmList as &$rm) {
        $price = $lpbData[$rm['article_rm']] ?? 0;
        $rm['price'] = $price;

        $rm['consumption'] = round($rm['qty_bom'] * $price, 2);
        $rm['consumption'] = number_format($rm['consumption'], 2, ',', '.'); // tampilan 2 digit koma
    }
    unset($rm);

    // ================================
    // 3. AMBIL QTY SALES DARI SJ
    // ================================
    $sjQuery = DB::table('sj_temporary')
        ->where('article_code', $fgCode);

    if ($periodeSelected) {
        $sjQuery->whereMonth('tanggal', $periodeSelected);
    }

    $sjRows = $sjQuery->get();

    foreach ($sjRows as $row) {
        $qty = (float)$row->qty;

        foreach ($rmList as &$rm) {
            $rm['qty_sales'] += $qty;
            $total = $rm['qty_sales'] * (float)str_replace(',', '.', $rm['consumption']);
            $rm['total'] = number_format($total, 2, ',', '.');
        }
        unset($rm);
    }

    // ================================
    // FORMAT PRICE & QTY JUGA 2 ANGKA DI BELAKANG KOMA
    // ================================
    foreach ($rmList as &$rm) {
        $rm['price'] = number_format($rm['price'], 2, ',', '.');
        $rm['qty_sales'] = number_format($rm['qty_sales'], 2, ',', '.');
    }

    return response()->json(['data' => array_values($rmList)]);
}





public function getChemicalByFG(Request $request)
{
    $fgCode = $request->query('fg_code');
    $periodeSelected = $request->query('periode'); // bulan 1–12

    if (!$fgCode) {
        return response()->json(['data' => []]);
    }

    // ========================================
    // 1. Ambil chemical dari BOM berdasarkan FG
    // ========================================
    $bomRows = DB::table('boms')
        ->where('article_fg', $fgCode)
        ->whereNotNull('cm_code')
        ->get();

    if ($bomRows->isEmpty()) {
        return response()->json(['data' => []]);
    }

    $chemList = [];
    foreach ($bomRows as $row) {
        $chemList[$row->cm_code] = [
            'article_cm' => $row->cm_code,
            'name_cm'    => $row->cm_name,
            'qty_bom'    => (float)$row->qty_bom,
            'uom'        => $row->uom,
            'price'      => 0,
            'consumption'=> 0,
            'qty_sales'  => 0,
            'total'      => 0
        ];
    }

    $cmCodes = array_keys($chemList);

    // ========================================
    // 2. Ambil price dari LPB (last price)
    // ========================================
    $lpbQuery = DB::table('lpb_temporary')
        ->whereIn('rm_code', $cmCodes);

    if ($periodeSelected) {
        $lpbQuery->whereMonth('tanggal', $periodeSelected);
    }

    $lpbRows = $lpbQuery
        ->orderBy('tanggal', 'desc')
        ->get();

    $lpbData = [];
    foreach ($lpbRows as $row) {
        if (!isset($lpbData[$row->rm_code])) {
            $lpbData[$row->rm_code] = (float)$row->price;
        }
    }

    // Hitung consumption
    foreach ($chemList as &$chem) {
        $code  = $chem['article_cm'];
        $price = $lpbData[$code] ?? 0;

        $chem['price'] = $price;
        $chem['consumption'] = $chem['qty_bom'] * $price;

        // format 2 digit
        $chem['price']       = number_format($chem['price'], 2, ',', '.');
        $chem['consumption'] = number_format($chem['consumption'], 2, ',', '.');
    }
    unset($chem);

    // ========================================
    // 3. Ambil qty sales dari SJ
    // ========================================
    $sjQuery = DB::table('sj_temporary')
        ->where('article_code', $fgCode);

    if ($periodeSelected) {
        $sjQuery->whereMonth('tanggal', $periodeSelected);
    }

    $sjRows = $sjQuery->get();

    foreach ($sjRows as $row) {
        $qty = (float)$row->qty;

        foreach ($chemList as &$chem) {
            $chem['qty_sales'] += $qty;

            // hitung total (consumption × qty_sales)
            $cons = (float) str_replace(',', '.', str_replace('.', '', $chem['consumption']));
            $chem['total'] = $cons * $chem['qty_sales'];

            // Format 2 digit koma
            $chem['qty_sales'] = number_format($chem['qty_sales'], 2, ',', '.');
            $chem['total']     = number_format($chem['total'], 2, ',', '.');
        }
        unset($chem);
    }

    return response()->json(['data' => array_values($chemList)]);
}


public function getFGbyChemical(Request $request)
{
    $cmCodeSelected   = $request->query('cm_code');
    $periodeAwal      = $request->query('periode_awal');
    $periodeAkhir     = $request->query('periode_akhir');
    $tahunSelected    = $request->query('tahun');

    if (!$cmCodeSelected) {
        return response()->json(['data' => []]);
    }

    // ================================
    // 1. AMBIL DATA BOM (FG yang memakai CM ini)
    // ================================
    $bomRows = DB::table('boms')
        ->where('article_cm', $cmCodeSelected)
        ->get();

    $chemList = [];

    foreach ($bomRows as $row) {
        $fgCode = trim($row->article_fg);

        if (isset($chemList[$fgCode])) {
            $chemList[$fgCode]['qty_bom'] += (float)$row->qty;
        } else {
            $chemList[$fgCode] = [
                'fg_code'     => $fgCode,
                'fg_name'     => trim($row->article_fg_desc),
                'cm_code'     => trim($row->article_cm),
                'cm_name'     => trim($row->article_cm_desc),
                'qty_bom'     => (float)$row->qty,
                'uom'         => trim($row->uom),
                'price'       => 0,
                'consumption' => 0,
                'qty_sales'   => 0,
                'total'       => 0
            ];
        }
    }

    foreach ($chemList as &$c) {
        $c['qty_bom'] = round($c['qty_bom'], 4);
    }
    unset($c);

    if (empty($chemList)) {
        return response()->json(['data' => []]);
    }

    // ================================
    // RANGE BULAN
    // ================================
    $bulanAwal  = $periodeAwal ? (int)$periodeAwal : 1;
    $bulanAkhir = $periodeAkhir ? (int)$periodeAkhir : 12;
    if ($bulanAwal > $bulanAkhir) [$bulanAwal, $bulanAkhir] = [$bulanAkhir, $bulanAwal];

    // ================================
    // 2. AMBIL DATA LPB UNTUK CM INI
    // ================================
    $cmCodes = array_map(fn($x) => $x['cm_code'], $chemList);

    $lpbRows = DB::table('lpb_temporary')
        ->whereIn('article_code', $cmCodes)
        ->get();

    $cmPrices = [];
    foreach ($lpbRows as $row) {
        $date = Carbon::parse($row->do_date);

        $cmPrices[$row->article_code][] = [
            'date'  => $date,
            'price' => (float)$row->price
        ];
    }

    // ================================
    // 3. HITUNG AVG PRICE
    // ================================
    $avgPrices = [];

    foreach ($chemList as $chem) {
        $cm = $chem['cm_code'];
        $avgPrices[$cm] = 0;

        if (!isset($cmPrices[$cm])) continue;

        $pricesInRange = [];

        foreach ($cmPrices[$cm] as $item) {
            $bulan = (int)$item['date']->format('n');
            $tahun = (int)$item['date']->format('Y');

            if ($tahunSelected && $tahun != $tahunSelected) continue;
            if ($bulan < $bulanAwal || $bulan > $bulanAkhir) continue;

            $pricesInRange[] = $item['price'];
        }

        if (!empty($pricesInRange)) {
            $avgPrices[$cm] = array_sum($pricesInRange) / count($pricesInRange);
        } else {
            // fallback nearest price
            $targetDate = Carbon::createFromDate($tahunSelected ?? now()->year, $bulanAwal, 1);

            $closestPrice = null;
            $closestDiff = PHP_INT_MAX;

            foreach ($cmPrices[$cm] as $item) {
                $diff = abs($targetDate->diffInDays($item['date'], false));
                if ($diff < $closestDiff) {
                    $closestDiff = $diff;
                    $closestPrice = $item['price'];
                }
            }

            $avgPrices[$cm] = $closestPrice ?? 0;
        }
    }

    // ================================
    // 4. HITUNG CONSUMPTION
    // ================================
    foreach ($chemList as &$chem) {
        $cm = $chem['cm_code'];
        $chem['price'] = $avgPrices[$cm] ?? 0;
        $chem['consumption'] = round($chem['qty_bom'] * $chem['price'], 2);
    }
    unset($chem);

    // ================================
    // 5. HITUNG SALES (SJ)
    // ================================
    $sjRows = DB::table('sj_temporary')
        ->when($tahunSelected, fn($q) => $q->whereYear('delivery_date', $tahunSelected))
        ->whereBetween(DB::raw('MONTH(delivery_date)'), [$bulanAwal, $bulanAkhir])
        ->get();

    foreach ($sjRows as $sj) {
        $fg = trim($sj->article_code);

        if (!isset($chemList[$fg])) continue;

        $qty = (float)$sj->delivery_qty;
        $chemList[$fg]['qty_sales'] += $qty;

        $chemList[$fg]['total'] =
            round($chemList[$fg]['qty_sales'] * $chemList[$fg]['consumption'], 2);
    }

    ksort($chemList);

    return response()->json([
        'data' => array_values($chemList)
    ]);
}




public function getFGInfo(Request $request)
{
    $fgCode = $request->query('fg_code');
    if (!$fgCode) {
        return response()->json(['data' => null]);
    }

    // =========================================
    // 1️⃣ AMBIL BOM NUMBER DARI TABEL BOM
    // =========================================
    $bomRow = DB::table('boms')
        ->where('article_fg', $fgCode)
        ->first();

    $bomNumber = $bomRow->code ?? null;

    // =========================================
    // 2️⃣ AMBIL DATA SJ UNTUK FG INI
    // =========================================
    $sjRows = DB::table('sj_temporary')
        ->where('article_code', $fgCode)
        ->orderBy('delivery_date')
        ->get();

    $customer    = null;
    $latestPrice = 0;
    $avgPrice    = 0;
    $prices      = [];

    foreach ($sjRows as $row) {

        // Ambil customer dari SJ pertama
        if (!$customer) {
            $customer = $row->customer;
        }

        // Hitung total price per row
        $price = (float) $row->price + (float) $row->service_price;
        $prices[] = $price;
    }

    if (!empty($prices)) {
        $latestPrice = end($prices);
        $avgPrice    = array_sum($prices) / count($prices);
    }

    return response()->json([
        'bom_number'  => $bomNumber,
        'customer'    => $customer,
        'latest_price'=> number_format($latestPrice, 2, '.', ''),
        'avg_price'   => number_format($avgPrice, 2, '.', '')
    ]);
}



public function getCMInfo(Request $request)
{
    $cmCodeSelected = $request->query('cm_code');
    if (!$cmCodeSelected) {
        return response()->json(['data' => null]);
    }

    // 1️⃣ AMBIL CM NAME DARI TABEL BOM
    $bom = DB::table('boms')
        ->select('article_cm', 'article_cm_desc')
        ->where('article_cm', $cmCodeSelected)
        ->first();

    $cmCode = $bom->article_cm ?? null;
    $cmName = $bom->article_cm_desc ?? null;

    // 2️⃣ AMBIL HARGA TERBARU & AVERAGE DARI LPB
    $lpbData = DB::table('lpb_temporary')
        ->select('price', 'supplier_name')
        ->where('article_code', $cmCodeSelected)
        ->orderBy('do_date', 'desc')
        ->get();

    $latestPrice = 0;
    $avgPrice    = 0;
    $customer    = null;

    if ($lpbData->count() > 0) {

        // harga terbaru
        $latestPrice = (float) $lpbData->first()->price;

        // customer dari baris terbaru
        $customer = $lpbData->first()->supplier_name;

        // list harga
        $prices = $lpbData->pluck('price')->map(fn($p) => (float)$p)->toArray();

        // average price
        $avgPrice = array_sum($prices) / count($prices);
    }

    return response()->json([
        'cm_code'      => $cmCode,
        'cm_name'      => $cmName,
        'customer'     => $customer,
        'latest_price' => number_format((float)$latestPrice, 2, '.', ''), // selalu 2 decimal
        'avg_price'    => number_format((float)$avgPrice, 2, '.', '')     // selalu 2 decimal
    ]);
}

public function getCmTotalBuy(Request $request)
{
    $cmCodeSelected  = strtolower(trim($request->get('cm_code')));
    $periodeSelected = $request->get('periode'); // 1 - 12
    $tahunSelected   = $request->get('tahun');   // contoh: 2025

    if (!$cmCodeSelected) {
        return response()->json([
            'cm_code'    => null,
            'total_buy'  => number_format(0, 2, '.', ''),
            'rows_count' => 0
        ]);
    }

    // 1️⃣ Query dasar
    $query = DB::table('lpb_temporary')
        ->whereRaw('LOWER(article_code) = ?', [$cmCodeSelected]);

    // 2️⃣ Filter bulan (periode)
    if (!empty($periodeSelected)) {
        $query->whereMonth('do_date', (int)$periodeSelected);
    }

    // 3️⃣ Filter tahun
    if (!empty($tahunSelected)) {
        $query->whereYear('do_date', (int)$tahunSelected);
    }

    // 4️⃣ Ambil data
    $lpbData = $query->select('total_tanpa_ppn')->get();

    $matchedRows = $lpbData->count();

    // 5️⃣ Hitung total buy
    $totalBuy = $lpbData->sum(function ($row) {
        return (float)$row->total_tanpa_ppn;
    });

    return response()->json([
        'cm_code'    => $cmCodeSelected,
        'total_buy'  => number_format($totalBuy, 2, '.', ''), // selalu 2 decimal
        'rows_count' => $matchedRows
    ]);
}



public function getRMTable(Request $request)
{
    $filename = $request->query('filename');
    $fgCode   = $request->query('fg_code');

    if (!$filename || !$fgCode) {
        return response()->json(['data' => [], 'subtotal' => 0]);
    }

    $path = storage_path("app/private/private/excels/{$filename}");
    if (!file_exists($path)) {
        return response()->json(['data' => [], 'subtotal' => 0]);
    }

    $spreadsheet = IOFactory::load($path);

    // === Sheet BOM ===
    $bomSheet = $spreadsheet->getSheetByName('BOM') ?: $spreadsheet->getActiveSheet();
    $bomRows  = $bomSheet->toArray();

    // === Sheet LPB ===
    $lpbSheet = $spreadsheet->getSheetByName('LPB') ?: $spreadsheet->getActiveSheet();
    $lpbRows  = $lpbSheet->toArray();

    // === Sheet SJ ===
    $sjSheet  = $spreadsheet->getSheetByName('SJ') ?: $spreadsheet->getActiveSheet();
    $sjRows   = $sjSheet->toArray();

    // Filter BOM untuk FG Code yang dipilih
    $rmData = [];
    foreach ($bomRows as $index => $row) {
        if ($index === 0) continue; // skip header
        $bomFG = $row[2] ?? null;
        if ($bomFG !== $fgCode) continue;

        $rmCode = $row[4] ?? null; // kolom 5 = kode RM
        $rmName = $row[5] ?? null; // kolom 6 = nama RM
        $rmData[$rmCode] = [
            'article_rm'   => $rmCode,
            'name_rm'      => $rmName,
            'qty_bom'      => 1, // hardcode
            'uom'          => null,
            'price'        => 0,
            'consumption'  => 0,
            'qty_sales'    => 0,
            'total'        => 0,
        ];
    }

    if (empty($rmData)) {
        return response()->json(['data' => [], 'subtotal' => 0]);
    }

    // Ambil UOM dan Price dari LPB
    $lpbMap = [];
    foreach ($lpbRows as $index => $row) {
        if ($index === 0) continue;
        $lpbRM = $row[9] ?? null; // kolom 10
        if (isset($rmData[$lpbRM])) {
            $lpbMap[$lpbRM] = [
                'uom'   => $row[14] ?? null, // kolom 15
                'price' => $row[15] ?? 0,    // kolom 16
            ];
        }
    }

    foreach ($rmData as $rmCode => &$rm) {
        if (isset($lpbMap[$rmCode])) {
            $rm['uom'] = $lpbMap[$rmCode]['uom'];
            $rm['price'] = $lpbMap[$rmCode]['price'];
            $rm['consumption'] = $rm['qty_bom'] * $rm['price'];
        }
    }

    // Ambil Qty Sales dari SJ
    $sjMap = [];
    foreach ($sjRows as $index => $row) {
        if ($index === 0) continue;
        $sjRM = $row[5] ?? null; // kolom 6
        $sjQty = $row[7] ?? 0;   // kolom 8
        if (isset($rmData[$sjRM])) {
            if (!isset($sjMap[$sjRM])) $sjMap[$sjRM] = 0;
            $sjMap[$sjRM] += $sjQty;
        }
    }

    foreach ($rmData as $rmCode => &$rm) {
        $rm['qty_sales'] = $sjMap[$rmCode] ?? 0;
        $rm['total'] = $rm['qty_sales'] * $rm['consumption'];
    }

    // Hitung subtotal (jumlah total)
    $subtotal = array_sum(array_column($rmData, 'total'));

    return response()->json([
        'data' => array_values($rmData),
        'subtotal' => $subtotal,
    ]);
}


public function exportChemicalSummaryFull(Request $request)
{
    $cacheData = Cache::get('bom_data', []);
    $bomSheet = $cacheData['BOM'] ?? [];
    $lpbSheet = $cacheData['LPB'] ?? [];

    $chemList = [];
    $fgCmQty = [];

    // ===============================
    // 1. Ambil semua CM per FG dan jumlahkan jika CM muncul lebih dari sekali
    // ===============================
    foreach ($bomSheet as $i => $row) {
        if ($i === 0) continue;

        $fgCode = trim($row[1] ?? '');
        $cmCode = trim($row[5] ?? '');
        if (!$cmCode) continue;

        $cmName = trim($row[6] ?? '');
        $qtyBOMRaw = trim($row[7] ?? '0');

        // Bersihkan quantity: hilangkan titik ribuan & spasi, ubah koma desimal menjadi titik
        $qtyClean = str_replace(['.', ' '], '', $qtyBOMRaw);
        $qtyClean = str_replace(',', '.', $qtyClean);
        $qtyBOM = (float)$qtyClean;

        // Simpan per FG per CM, jumlahkan jika muncul lebih dari sekali
        if (!isset($fgCmQty[$fgCode])) $fgCmQty[$fgCode] = [];
        if (!isset($fgCmQty[$fgCode][$cmCode])) $fgCmQty[$fgCode][$cmCode] = 0;
        $fgCmQty[$fgCode][$cmCode] += $qtyBOM;

        // Simpan nama CM
        if (!isset($chemList[$cmCode])) {
            $chemList[$cmCode] = [
                'cm_name' => $cmName,
                'total_consumption' => 0,
            ];
        }
    }

    // ===============================
    // 2. Ambil latest price per CM dari LPB (row terakhir per CM)
    // ===============================
    $latestPrice = [];
    for ($i = count($lpbSheet) - 1; $i >= 1; $i--) {
        $row = $lpbSheet[$i];
        $cmCode = trim($row[1] ?? '');
        if (!$cmCode || !isset($chemList[$cmCode]) || isset($latestPrice[$cmCode])) continue;

        $priceStr = trim($row[3] ?? '0'); // kolom harga untuk consumption
        $priceClean = str_replace(['.', ' '], '', $priceStr);
        $priceClean = str_replace(',', '.', $priceClean);
        $latestPrice[$cmCode] = (float)$priceClean;

        if (count($latestPrice) === count($chemList)) break;
    }

    // ===============================
    // 3. Hitung total consumption per CM
    // ===============================
    foreach ($fgCmQty as $fgCode => $cmData) {
        foreach ($cmData as $cmCode => $qtyBOM) {
            $price = $latestPrice[$cmCode] ?? 0;
            $chemList[$cmCode]['total_consumption'] += $qtyBOM * $price;
        }
    }

    // Round total consumption sekali per CM
    foreach ($chemList as $cmCode => &$chem) {
        $chem['total_consumption'] = round($chem['total_consumption'], 2);
    }
    unset($chem);

    // ===============================
    // 4. Hitung total buy per CM dari LPB
    // ===============================
    $totalBuyPerCM = [];
    foreach ($lpbSheet as $i => $row) {
        if ($i === 0) continue;
        $cmCode = trim($row[1] ?? '');
        if (!$cmCode) continue;

        $priceRaw = trim($row[6] ?? '0'); // kolom harga actual buy
        $priceClean = str_replace(['Rp',' ',' '], '', $priceRaw);

        if (preg_match('/,\d{2}$/', $priceClean)) {
            $priceClean = str_replace('.', '', $priceClean);
            $priceClean = str_replace(',', '.', $priceClean);
        } else {
            $priceClean = str_replace(',', '', $priceClean);
        }

        $priceParsed = (float)$priceClean;

        if (!isset($totalBuyPerCM[$cmCode])) $totalBuyPerCM[$cmCode] = 0;
        $totalBuyPerCM[$cmCode] += $priceParsed;
    }

    // ===============================
    // 5. Urutkan CM berdasarkan kode
    // ===============================
    ksort($chemList);

    // ===============================
    // 6. Buat spreadsheet
    // ===============================
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'CODE');
    $sheet->setCellValue('B1', 'BOM');
    $sheet->setCellValue('C1', 'BUY');
    $sheet->setCellValue('D1', 'CONTROL');
    $sheet->setCellValue('E1', '(%)');
    $sheet->setCellValue('F1', 'CM NAME');

    $rowNum = 2;
    foreach ($chemList as $cmCode => $chem) {
        $totalConsumption = $chem['total_consumption'] ?? 0;
        $totalBuy = $totalBuyPerCM[$cmCode] ?? 0;
        $control = round($totalConsumption - $totalBuy, 2);
        $percentage = $totalConsumption != 0 ? round(($totalBuy / $totalConsumption) * 100, 2) : 0;

        $sheet->setCellValue("A{$rowNum}", $cmCode);
        $sheet->setCellValue("B{$rowNum}", $totalConsumption);
        $sheet->setCellValue("C{$rowNum}", $totalBuy);
        $sheet->setCellValue("D{$rowNum}", $control);
        $sheet->setCellValue("E{$rowNum}", $percentage . '%');
        $sheet->setCellValue("F{$rowNum}", $chem['cm_name'] ?? '');

        // format accounting untuk kolom B, C, D
        foreach (['B','C','D'] as $col) {
            $sheet->getStyle("{$col}{$rowNum}")
                  ->getNumberFormat()
                  ->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
        }

        $rowNum++;
    }

    // Auto size kolom
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Download Excel
    $filename = 'consumption_summary.xlsx';
    header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}






}