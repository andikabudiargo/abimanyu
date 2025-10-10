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
    $rows = Cache::get('cm_data', []);

    $cmList = collect($rows)
        ->skip(1) // lewati header
        ->map(function($row) {
            return [
                'code' => $row[2] ?? null, // kolom kode CM
                'name' => $row[3] ?? null  // kolom nama CM
            ];
        })
        ->filter(function($item) {
            // buang yang kosong
            return $item['code'] && $item['name'];
        })
        ->unique(function($item) {
            // unik berdasarkan kode + nama
            return $item['code'] . '|' . $item['name'];
        })
        ->values();

    return response()->json($cmList);
}


     public function getFG(Request $request)
{
    $cmCode = $request->query('cm');

    $rows = Cache::get('cm_data', []);

    $fgList = collect($rows)
        ->skip(1)
        ->filter(function($row) use ($cmCode){
            return isset($row[2]) && $row[2] == $cmCode; // kolom kode CM
        })
        ->map(function($row){
            return [
                'code' => $row[0] ?? null, // kolom FG code
                'name' => $row[1] ?? null  // kolom FG name
            ];
        })
        ->filter(function($item){
            return $item['code'] && $item['name'];
        })
        ->unique(function($item){
            return $item['code'] . '|' . $item['name'];
        })
        ->values();

    return response()->json($fgList);
}

public function exportCMFG()
{
    $rows = Cache::get('cm_data', []);

    $data = collect($rows)
        ->skip(1) // lewati header
        ->map(function($row){
            return [
                'cm_code' => $row[2] ?? null,
                'cm_name' => $row[3] ?? null,
                'qty_bom' => $row[4] ?? null,
                'uom' => $row[5] ?? null,
                'fg_code' => $row[0] ?? null,
                'fg_name' => $row[1] ?? null,
            ];
        })
        ->filter(function($item){
            // buang baris yang penting kosong
            return $item['cm_code'] && $item['fg_code'] && $item['fg_name'];
        })
        ->unique(function($item){
            // unik per CM-FG
            return $item['cm_code'].'|'.$item['fg_code'];
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
foreach($data as $item){
    $sheet->setCellValue('A'.$rowNumber, $item['cm_code']);
    $sheet->setCellValue('B'.$rowNumber, $item['cm_name']);
    $sheet->setCellValue('C'.$rowNumber, $item['qty_bom']);
    $sheet->setCellValue('D'.$rowNumber, $item['uom']);
    $sheet->setCellValue('E'.$rowNumber, $item['fg_code']);
    $sheet->setCellValue('F'.$rowNumber, $item['fg_name']);
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
    $term  = $request->query('term', '');
    $page  = max(1, (int) $request->query('page', 1));
    $limit = 20;

    // Ambil semua sheet dari cache
    $cacheData = Cache::get('bom_data', []);
    $bomSheet  = $cacheData['BOM'] ?? [];

    // Gunakan associative array untuk key unik agar tidak duplikat
    $fgListAssoc = [];
    foreach ($bomSheet as $i => $row) {
        if ($i === 0) continue; // skip header
        $fgCode = trim($row[1] ?? '');
        $fgName = trim($row[2] ?? '');
        if ($fgCode && $fgName) {
            $fgListAssoc[$fgCode] = [
                'id'   => $fgCode,
                'text' => $fgCode . ' - ' . $fgName
            ];
        }
    }

    // Ubah ke numerik array
    $fgList = array_values($fgListAssoc);

    // Filter berdasarkan term
    if ($term) {
        $fgList = array_filter($fgList, function($item) use ($term) {
            return stripos($item['id'], $term) !== false || stripos($item['text'], $term) !== false;
        });
    }

    // Pagination
    $offset = ($page - 1) * $limit;
    $items  = array_slice($fgList, $offset, $limit);
    $more   = ($offset + $limit) < count($fgList);

    return response()->json([
        'results'    => array_values($items),
        'pagination' => ['more' => $more]
    ]);
}


public function getChemical(Request $request)
{
    $term  = $request->query('term', '');
    $page  = max(1, (int) $request->query('page', 1));
    $limit = 20;

    // Ambil semua sheet dari cache
    $cacheData = Cache::get('bom_data', []);
    $bomSheet  = $cacheData['BOM'] ?? [];

    // Gunakan associative array untuk key unik agar tidak duplikat
    $cmListAssoc = [];
    foreach ($bomSheet as $i => $row) {
        if ($i === 0) continue; // skip header
        $cmCode = trim($row[5] ?? '');
        $cmName = trim($row[6] ?? '');
        if ($cmCode && $cmName) {
            $cmListAssoc[$cmCode] = [
                'id'   => $cmCode,
                'text' => $cmCode . ' - ' . $cmName
            ];
        }
    }

    // Ubah ke numerik array
    $cmList = array_values($cmListAssoc);

    // Filter berdasarkan term
    if ($term) {
        $cmList = array_filter($cmList, function($item) use ($term) {
            return stripos($item['id'], $term) !== false || stripos($item['text'], $term) !== false;
        });
    }

    // Pagination
    $offset = ($page - 1) * $limit;
    $items  = array_slice($cmList, $offset, $limit);
    $more   = ($offset + $limit) < count($cmList);

    return response()->json([
        'results'    => array_values($items),
        'pagination' => ['more' => $more]
    ]);
}


public function getRMByFG(Request $request)
{
    $fgCode = $request->query('fg_code');
    $periodeSelected = $request->query('periode'); // misal 1-12
    if (!$fgCode) {
        return response()->json(['data' => []]);
    }

    $cacheData = Cache::get('bom_data', []);
    $bomSheet = $cacheData['BOM'] ?? [];
    $lpbSheet = $cacheData['LPB'] ?? [];
    $sjSheet  = $cacheData['SJ'] ?? [];

    $rmList = [];

    // Ambil RM dari BOM sesuai FG
    foreach ($bomSheet as $i => $row) {
        if ($i == 0) continue;
        $bomFGCode = trim($row[1] ?? '');
        if ($bomFGCode !== $fgCode) continue;

        $rmCode = trim($row[3] ?? '');
        if (!$rmCode) continue;

        if (!isset($rmList[$rmCode])) {
            $rmList[$rmCode] = [
                'article_rm' => $rmCode,
                'name_rm'    => trim($row[4] ?? ''),
                'qty_bom'    => 1,
                'uom'        => 'PCS',
                'price'      => 0,
                'consumption'=> 0,
                'qty_sales'  => 0,
                'total'      => 0
            ];
        }
    }

    if (empty($rmList)) return response()->json(['data' => []]);

    // Ambil price dari LPB (filter periode jika ada)
    $lpbData = [];
    for ($i = count($lpbSheet) - 1; $i >= 1; $i--) {
        $row = $lpbSheet[$i];
        $code = trim($row[1] ?? '');
        if (!isset($rmList[$code])) continue;

        $tanggal = trim($row[0] ?? '');
        $bulanData = null;

        if (preg_match('/\d{2}-\d{2}-\d{4}/', $tanggal)) {
            $parts = explode('-', $tanggal);
            $bulanData = (int) $parts[1];
        } elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        } elseif (preg_match('/\d{2}-[A-Za-z]{3}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        }

        if ($periodeSelected && $bulanData != (int)$periodeSelected) continue;

        $priceStr = trim($row[3] ?? '0');
        $priceNumeric = (float) str_replace(',', '.', str_replace('.', '', $priceStr));

        if (!isset($lpbData[$code])) {
            $lpbData[$code] = $priceNumeric;
        }
        if (count($lpbData) === count($rmList)) break;
    }

    // Hitung consumption
    foreach ($rmList as &$rm) {
        $code = $rm['article_rm'];
        $qtyBOMFloat = (float) str_replace(',', '.', $rm['qty_bom']);
        $price = $lpbData[$code] ?? 0;
        $rm['price'] = $price;
        $rm['consumption'] = round($qtyBOMFloat * $price, 2);
    }
    unset($rm);

    // Hitung qty sales dari SJ (filter periode)
    foreach ($sjSheet as $i => $row) {
        if ($i == 0) continue;
        if (trim($row[2] ?? '') !== $fgCode) continue;

        $tanggal = trim($row[0] ?? '');
        $bulanData = null;

        if (preg_match('/\d{2}-\d{2}-\d{4}/', $tanggal)) {
            $parts = explode('-', $tanggal);
            $bulanData = (int) $parts[1];
        } elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        } elseif (preg_match('/\d{2}-[A-Za-z]{3}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        }

        if ($periodeSelected && $bulanData != (int)$periodeSelected) continue;

        $qtyStrRaw = trim($row[4] ?? '0');
        $qty = (float) str_replace(',', '', $qtyStrRaw);

        foreach ($rmList as &$rm) {
            $rm['qty_sales'] += $qty;
            $rm['total'] = round($rm['consumption'] * $rm['qty_sales'], 2);
        }
        unset($rm);
    }

    return response()->json(['data' => array_values($rmList)]);
}




public function getChemicalByFG(Request $request)
{
    $fgCode = $request->query('fg_code');
    $periodeSelected = $request->query('periode'); // misal 1-12
    if (!$fgCode) return response()->json(['data' => []]);

    $cacheData = Cache::get('bom_data', []);
    $bomSheet = $cacheData['BOM'] ?? [];
    $lpbSheet = $cacheData['LPB'] ?? [];
    $sjSheet  = $cacheData['SJ'] ?? [];

    $chemList = [];

    // Ambil chemical dari BOM sesuai FG
    foreach ($bomSheet as $i => $row) {
        if ($i == 0) continue;
        if (trim($row[1] ?? '') !== $fgCode) continue;

        $cmCode = trim($row[5] ?? '');
        if (!$cmCode) continue;

        $chemList[] = [
            'article_cm' => $cmCode,
            'name_cm'    => trim($row[6] ?? ''),
            'qty_bom'    => trim($row[7] ?? 0),
            'uom'        => trim($row[8] ?? ''),
            'price'      => 0,
            'consumption'=> 0,
            'qty_sales'  => 0,
            'total'      => 0
        ];
    }

    // Ambil price dari LPB (filter periode)
    $lpbData = [];
    for ($i = count($lpbSheet) - 1; $i >= 1; $i--) {
        $row = $lpbSheet[$i];
        $code = trim($row[1] ?? '');
        if (!in_array($code, array_column($chemList, 'article_cm'))) continue;

        $tanggal = trim($row[0] ?? '');
        $bulanData = null;

        if (preg_match('/\d{2}-\d{2}-\d{4}/', $tanggal)) {
            $parts = explode('-', $tanggal);
            $bulanData = (int)$parts[1];
        } elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        } elseif (preg_match('/\d{2}-[A-Za-z]{3}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        }

        if ($periodeSelected && $bulanData != (int)$periodeSelected) continue;

        if (!isset($lpbData[$code])) {
            $priceStr = trim($row[3] ?? '0');
            $lpbData[$code] = (float) str_replace(',', '.', str_replace('.', '', $priceStr));
        }
        if (count($lpbData) === count($chemList)) break;
    }

    foreach ($chemList as &$chem) {
        $code = $chem['article_cm'];
        $qtyBOMFloat = (float) str_replace(',', '.', $chem['qty_bom']);
        $price = $lpbData[$code] ?? 0;
        $chem['price'] = $price;
        $chem['consumption'] = round($qtyBOMFloat * $price, 2);
    }
    unset($chem);

    // Hitung qty sales dari SJ (filter periode)
    foreach ($sjSheet as $i => $row) {
        if ($i == 0) continue;
        if (trim($row[2] ?? '') !== $fgCode) continue;

        $tanggal = trim($row[0] ?? '');
        $bulanData = null;

        if (preg_match('/\d{2}-\d{2}-\d{4}/', $tanggal)) {
            $parts = explode('-', $tanggal);
            $bulanData = (int)$parts[1];
        } elseif (preg_match('/\d{4}-\d{2}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        } elseif (preg_match('/\d{2}-[A-Za-z]{3}-\d{2}/', $tanggal)) {
            $bulanData = (int) date('n', strtotime($tanggal));
        }

        if ($periodeSelected && $bulanData != (int)$periodeSelected) continue;

        $qtyStrRaw = trim($row[4] ?? '0');
        $qty = (float) str_replace(',', '', $qtyStrRaw);

        foreach ($chemList as &$chem) {
            $chem['qty_sales'] += $qty;
            $chem['total'] = round($chem['consumption'] * $chem['qty_sales'], 2);
        }
        unset($chem);
    }

    return response()->json(['data' => $chemList]);
}


public function getFGbyChemical(Request $request)
{
    $cmCodeSelected = $request->query('cm_code');
    $periodeSelected = $request->query('periode'); // 1-12 atau kosong
    $tahunSelected = $request->query('tahun');     // Tahun, misal 2025

    if (!$cmCodeSelected) return response()->json(['data' => []]);

    $cacheData = Cache::get('bom_data', []);
    $bomSheet = $cacheData['BOM'] ?? [];
    $lpbSheet = $cacheData['LPB'] ?? [];
    $sjSheet  = $cacheData['SJ'] ?? [];

    $chemList = [];

    // --- Ambil semua FG yang pakai CM ---
    foreach ($bomSheet as $i => $row) {
        if ($i == 0) continue;
        $cmCode = trim($row[5] ?? '');
        if ($cmCode !== $cmCodeSelected) continue;

        $fgCode = trim($row[1] ?? '');
        $fgName = trim($row[2] ?? '');
        $cmName = trim($row[6] ?? '');
        $qtyBOM = floatval(str_replace(',', '.', $row[7] ?? 0));
        $uom = trim($row[8] ?? ''); 

        if (isset($chemList[$fgCode])) {
            $chemList[$fgCode]['qty_bom'] += $qtyBOM;
        } else {
            $chemList[$fgCode] = [
                'fg_code' => $fgCode,
                'fg_name' => $fgName,
                'cm_code' => $cmCode,
                'cm_name' => $cmName,
                'qty_bom' => $qtyBOM,
                'uom'        => $uom,
                'price' => 0,
                'consumption' => 0,
                'qty_sales' => 0,
                'total' => 0
            ];
        }
    }

    foreach ($chemList as &$chem) {
        $chem['qty_bom'] = round($chem['qty_bom'], 4);
    }
    unset($chem);

// --- Hitung average price per CM (filter periode & tahun) ---
$cmPrices = [];

for ($i = 1; $i < count($lpbSheet); $i++) {
    $row = $lpbSheet[$i];
    $tanggal = trim($row[0] ?? '');

    // --- Parse tanggal
    try {
        $tanggalObj = Carbon::createFromFormat('d/m/Y', $tanggal);
    } catch (\Exception $e) {
        $tanggalObj = Carbon::parse($tanggal); // otomatis baca "30 September 2025"
    }

    $bulanData = (int) $tanggalObj->format('n');
    $tahunData = (int) $tanggalObj->format('Y');

    // Filter periode & tahun
    if ($periodeSelected && $bulanData != (int) $periodeSelected) continue;
    if ($tahunSelected && $tahunData != (int) $tahunSelected) continue;

    $cmCode = trim($row[1] ?? '');
    if (!in_array($cmCode, array_column($chemList, 'cm_code'))) continue;

    // Ambil price langsung, sudah numeric
    $priceNumeric = isset($row[3]) ? (float)$row[3] : 0;
    $cmPrices[$cmCode][] = $priceNumeric;

    // Simpan tanggal formatted
    $row[0] = $tanggalObj->format('d F Y'); // misal "30 September 2025"
}

// Hitung average price per CM
$avgPrices = [];
foreach ($cmPrices as $cmCode => $prices) {
    $avgPrices[$cmCode] = count($prices) ? array_sum($prices) / count($prices) : 0;
}

// --- Hitung consumption pakai average price ---
foreach ($chemList as &$chem) {
    $cmCode = $chem['cm_code'];
    $qtyBOMFloat = (float) ($chem['qty_bom'] ?? 0); // langsung cast float

    $chem['price'] = $avgPrices[$cmCode] ?? 0;
    $chem['consumption'] = round($qtyBOMFloat * $chem['price'], 2);
}
unset($chem);


    // --- Hitung qty sales dari SJ (filter periode & tahun) ---
    foreach ($sjSheet as $i => $row) {
        if ($i == 0) continue;

        $tanggal = trim($row[0] ?? '');

        try {
            $tanggalObj = Carbon::createFromFormat('d/m/Y', $tanggal);
        } catch (\Exception $e) {
            $tanggalObj = Carbon::parse($tanggal);
        }

        $bulanData = (int) $tanggalObj->format('n');
        $tahunData = (int) $tanggalObj->format('Y');

        if ($periodeSelected && $bulanData != (int) $periodeSelected) continue;
        if ($tahunSelected && $tahunData != (int) $tahunSelected) continue;

        $sjFGCode = trim($row[2] ?? '');
        if (!isset($chemList[$sjFGCode])) continue;

        $qty = (float) str_replace(',', '', trim($row[4] ?? '0'));
        $chemList[$sjFGCode]['qty_sales'] += $qty;
        $chemList[$sjFGCode]['total'] = round($chemList[$sjFGCode]['consumption'] * $chemList[$sjFGCode]['qty_sales'], 2);
    }

    ksort($chemList);
    return response()->json(['data' => array_values($chemList)]);
}








public function getFGInfo(Request $request)
{
    $fgCode = $request->query('fg_code');
    if (!$fgCode) return response()->json(['data' => null]);

    $cacheData = Cache::get('bom_data', []);
    $bomSheet  = $cacheData['BOM'] ?? [];
    $sjSheet   = $cacheData['SJ'] ?? [];

    $bomNumber   = null;
    $customer    = null;
    $latestPrice = 0;
    $avgPrice    = 0;

    // 1️⃣ Ambil BOM number dari BOM sheet
    foreach ($bomSheet as $i => $row) {
        if ($i == 0) continue;
        if (trim($row[1] ?? '') === $fgCode) { // kolom 2 = FG code
            $bomNumber = trim($row[0] ?? ''); // kolom 1 = BOM number
            break;
        }
    }

    // 2️⃣ Ambil semua harga dari SJ untuk hitung rata-rata
    $prices = [];
    foreach ($sjSheet as $i => $row) {
        if ($i == 0) continue; // skip header
        if (trim($row[2] ?? '') === $fgCode) { // kolom 3 = FG code
            // customer dari SJ (ambil dari row pertama yang cocok)
            if (!$customer) {
                $customer = trim($row[1] ?? ''); // kolom 2 = customer
            }

            $val5 = (float) str_replace(',', '', trim($row[5] ?? '0'));
            $val6 = (float) str_replace(',', '', trim($row[6] ?? '0'));
            $prices[] = $val5 + $val6;
        }
    }

    if (!empty($prices)) {
        $latestPrice = end($prices); // price terbaru
        $avgPrice    = array_sum($prices) / count($prices);
    }

    return response()->json([
        'bom_number'  => $bomNumber,
        'customer'    => $customer,
        'latest_price'=> $latestPrice,
        'avg_price'   => $avgPrice
    ]);
}


public function getCMInfo(Request $request)
{
    $cmCodeSelected = $request->query('cm_code');
    if (!$cmCodeSelected) return response()->json(['data' => null]);

    $cacheData = Cache::get('bom_data', []);
    $bomSheet = $cacheData['BOM'] ?? [];
    $lpbSheet = $cacheData['LPB'] ?? [];
    $cmCode   = null;
    $cmName   = null;
    $customer = null;
    $latestPrice = 0;
    $avgPrice = 0;

    // 1️⃣ Ambil CM Name dari BOM
    foreach ($bomSheet as $i => $row) {
        if ($i == 0) continue; // skip header
        if (trim($row[5] ?? '') === $cmCodeSelected) { // kolom 6 = CM code
            $cmCode = trim($row[5] ?? '');
            $cmName = trim($row[6] ?? '');
            break; // cukup ambil 1
        }
    }

    // 2️⃣ Ambil semua harga dari LPB untuk hitung average
    $prices = [];
    $latestPriceSet = false;

    for ($i = 1; $i < count($lpbSheet); $i++) {
        $row = $lpbSheet[$i];
        $code = strtolower(trim($row[1] ?? '')); // kolom 2 = CM code
        if ($code === strtolower($cmCodeSelected)) {

            // Ambil harga langsung dari Excel, sudah numeric
            $priceNum = isset($row[3]) ? (float)$row[3] : 0;
            $prices[] = $priceNum;

            // Ambil price terbaru dari baris pertama yang ditemukan
            if (!$latestPriceSet) {
                $latestPrice = $priceNum;
                $latestPriceSet = true;
            }
        }
    }

    if (!empty($prices)) {
        $avgPrice = array_sum($prices) / count($prices); // hitung rata-rata
    }

    // 3️⃣ Ambil customer dari LPB
    for ($i = 1; $i < count($lpbSheet); $i++) {
        $row = $lpbSheet[$i];
        $code = strtolower(trim($row[1] ?? '')); 
        if ($code === strtolower($cmCodeSelected)) {
            $customer = trim($row[5] ?? ''); // kolom 6 = customer
            break;
        }
    }

    return response()->json([
        'cm_code'     => $cmCode,
        'cm_name'     => $cmName,
        'customer'    => $customer,
        'latest_price'=> $latestPrice,
        'avg_price'   => $avgPrice
    ]);
}



public function getCmTotalBuy(Request $request)
{
    $cmCodeSelected = strtolower(trim($request->get('cm_code')));
    $periodeSelected = $request->get('periode'); // 1-12 atau kosong
    $tahunSelected   = $request->get('tahun');   // Tahun, misal 2025

    $cacheData = Cache::get('bom_data', []);
    $lpbSheet = $cacheData['LPB'] ?? [];

    $totalBuy = 0;
    $matchedRows = 0;

    foreach ($lpbSheet as $i => $row) {
        if ($i === 0) continue; // skip header

        $code = strtolower(trim($row[1] ?? ''));
        if ($code !== $cmCodeSelected) continue;

        $tanggal = trim($row[0] ?? ''); // misal "30 September 2025"

        // --- Ambil bulan & tahun dari string Excel ---
        try {
            $tanggalObj = Carbon::parse($tanggal); // otomatis membaca "30 September 2025"
            $bulanData = (int)$tanggalObj->format('n'); // 1–12
            $tahunData = (int)$tanggalObj->format('Y');
        } catch (\Exception $e) {
            continue; // skip jika gagal parse
        }

        // Filter periode dan tahun dari dropdown
        if ($periodeSelected && $bulanData != (int)$periodeSelected) continue;
        if ($tahunSelected && $tahunData != (int)$tahunSelected) continue;

        $matchedRows++;

        // Ambil harga langsung, sudah numeric
        $price = (float) ($row[6] ?? 0);
        $totalBuy += $price;
    }

    return response()->json([
        'cm_code' => $cmCodeSelected,
        'total_buy' => $totalBuy,
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