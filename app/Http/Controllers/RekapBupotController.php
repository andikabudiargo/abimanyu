<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use ZipArchive;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class RekapBupotController extends Controller
{
     public function index() {
        return view('accounting.rekap-bupot');
    }

    public function process(Request $request)
{
    $request->validate([
        'pdf_files' => 'required|mimes:zip|max:51200', // max 50MB
    ]);

    $zipFile = $request->file('pdf_files');

    // Folder sementara
    $extractPath = storage_path('app/tmp_zip_' . uniqid());

    File::makeDirectory($extractPath, 0755, true);

    // Ekstrak ZIP
    $zip = new ZipArchive();
    if ($zip->open($zipFile->path()) !== true) {
        return back()->withErrors('Gagal membuka file ZIP');
    }

    $zip->extractTo($extractPath);
    $zip->close();

    $parser = new Parser();
    $allData = [];

    // Ambil semua PDF (recursive)
    $pdfFiles = File::allFiles($extractPath);

    foreach ($pdfFiles as $file) {
        if (strtolower($file->getExtension()) !== 'pdf') {
            continue;
        }

        try {
            $text = $parser->parseFile($file->getPathname())->getText();
            $extracted = $this->extractData($text);

            // Skip PDF yang gagal terbaca
            if (empty($extracted['nomor'])) {
                continue;
            }

            $allData[] = $extracted;

        } catch (\Exception $e) {
            // skip PDF rusak
            continue;
        }
    }

    // Bersihkan folder sementara
    File::deleteDirectory($extractPath);

    if (empty($allData)) {
        return back()->withErrors('Tidak ada data PDF yang berhasil diekstrak');
    }

    return $this->exportExcel($allData);
}

    private function cleanNumber($value)
{
    // Hapus semua titik, spasi, koma, dan karakter non-digit
    return preg_replace('/[^0-9]/', '', $value);
}

private function convertToExcelDate($tanggal)
{
    if (!$tanggal) return null;

    $bulan = [
        'Januari'   => '01',
        'Februari'  => '02',
        'Maret'     => '03',
        'April'     => '04',
        'Mei'       => '05',
        'Juni'      => '06',
        'Juli'      => '07',
        'Agustus'   => '08',
        'September' => '09',
        'Oktober'   => '10',
        'November'  => '11',
        'Desember'  => '12'
    ];

    if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/', $tanggal, $m)) {
        $hari = str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $bln  = $bulan[$m[2]] ?? '01';
        $tahun = $m[3];

        return "$hari/$bln/$tahun";
    }

    return $tanggal; // fallback
}



 private function extractData($text)
{
    // Bersihkan whitespace menjadi satu baris untuk memudahkan regex
    $clean = preg_replace('/\s+/', ' ', trim($text));

    preg_match('/\b([0-9A-Z]{8,12})([0-9]{2}-[0-9]{4})/', $clean, $m);
$nomor = $m[1] ?? '';
$masaPajak = $m[2] ?? '';
preg_match('/\b[0-9A-Z]{8,12}[0-9]{2}-([0-9]{4})(?=[A-Z])/i', $clean, $y);
$tahun = $y[1] ?? '';


    // Ambil sifat dan status
    preg_match('/(TIDAK FINAL|FINAL)/i', $clean, $sf);
    preg_match('/(NORMAL|ISTIMEWA)/i', $clean, $st);

    // Ambil Kode Objek, Nama Objek, DPP, Tarif, PPh
    preg_match('/([0-9]{2}-[0-9]{3}-[0-9]{2})\s+(.+?)\s+([\d\.]+)\s+(\d+)\s+([\d\.]+)/', $clean, $k);

    // Ambil NITKU + Nama penerima
   preg_match('/A\.3.*?NITKU.*?:\s*([0-9]+)\s*-\s*([A-Z0-9\s\.]+?)(?=\s+B\.)/i', $clean, $niktuMatch);
    // Ambil NITKU + Nama penerima
   preg_match('/C\.2.*?NITKU.*?:\s*([0-9]+)\s*-\s*([A-Z0-9\s\.]+?)(?=\s+C\.3)/i', $clean, $niktuPemotong);
  $tanggal_pemotong = $this->match('/C\.4.*?TANGGAL\s*:\s*([A-Za-z0-9 ]+?)(?=\s+C\.5)/i', $clean);
$tanggal_raw = $this->match('/Tanggal\s*:\s*([A-Za-z0-9 ]+?)(?=\s+[A-Z]\.)/i', $clean);
$tanggal_excel = $this->convertToExcelDate($tanggal_raw);
$convert_tanggal_pemotong = $this->convertToExcelDate($tanggal_pemotong);



    return [
        'nomor'         => $nomor,
        'masa_pajak'    => $masaPajak,
        'tahun_pajak'   => $tahun,
        'sifat'         => strtoupper($sf[1] ?? ''),
        'status'        => strtoupper($st[1] ?? ''),

        'npwp'          => $this->match('/A\.1.*?([0-9]{16})/i', $clean),
        'nama_wp'       => $this->match('/A\.2\s*NAMA\s*:\s*([A-Z0-9\s\.]+?)(?=\s+A\.3)/i', $clean),

        'niktu_penerima' => isset($niktuMatch[1], $niktuMatch[2])
    ? trim($niktuMatch[1] . ' - ' . $niktuMatch[2])
    : '',
        'jenis_fasilitas' => $this->match('/B\.1\s*Jenis Fasilitas\s*:\s*([A-Za-z ]+?)(?=\s+B\.2)/i', $clean),
        'jenis_pph' => $this->match('/Jenis PPh\s*:\s*([A-Za-z0-9 ]+?)(?=\s+Kode Objek)/i', $clean),
        'kode_objek'    => $k[1] ?? '',
        'nama_objek'    => $k[2] ?? '',
        'dpp'           => isset($k[3]) ? str_replace('.', '', $k[3]) : '',
        'tarif'         => $k[4] ?? '',
        'pph'           => isset($k[5]) ? str_replace('.', '', $k[5]) : '',
        'jenis_dokumen' => $this->match('/Jenis Dokumen\s*:\s*([A-Za-z0-9 ]+?)(?=\s*Tanggal)/i', $clean),
        'tanggal_dokumen' => $tanggal_excel,
        'nomor_invoice' => $this->match('/Nomor Dokumen\s*:\s*([A-Z0-9\-]+)/i', $clean),
        'npwp_pemotong' => $this->match('/C\.1.*?([0-9]{16})/i', $clean),
        'niktu_pemotong' => isset($niktuPemotong[1], $niktuPemotong[2])
    ? trim($niktuPemotong[1] . ' - ' . $niktuPemotong[2])
    : '',
        'pemotong' => $this->match('/C\.3.*?NAMA PEMOTONG.*?:\s*([A-Z0-9\s\.]+?)(?=\s+C\.4)/i', $clean),
        'tanggal'       => $convert_tanggal_pemotong,
        'penandatangan' => $this->match('/C\.5.*?NAMA PENANDATANGAN\s*:\s*([A-Z0-9\s\.]+?)(?=\s+C\.6)/i', $clean),
    ];
}






    private function match($pattern, $text)
    {
        preg_match($pattern, $text, $m);
        return $m[1] ?? '';
    }

    private function exportExcel($rows)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            "Nomor Bukti Potong",
            "Masa Pajak",
            "Tahun Pajak",
            "Sifat",
            "Pembetulan",
            "NPWP Penerima",
            "Nama Penerima",
            "NIKTU Penerima",
            "Jenis Fasilitas",
            "Jenis PPh",
            "Kode Objek",
            "Objek Pajak",
            "Jumlah Penghasilan Bruto",
            "Tarif (%)",
            "PPh Dipotong",
            "Jenis Dokumen",
            "Tanggal Dokumen",
            "Nomor Dokumen",
            "NPWP Pemotong",
            "NIKTU Pemotong",
            "Nama Pemotong",
            "Tanggal Bukti Potong",
            "Penandatangan"
        ];

        $sheet->fromArray([$headers], null, 'A1');

        // Data
        $sheet->fromArray($rows, null, 'A2');

        // Buat file ke memori
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="rekap_bukti_potong.xlsx"',
        ]);
    }
}