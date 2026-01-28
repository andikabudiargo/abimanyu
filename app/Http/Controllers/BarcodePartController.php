<?php

namespace App\Http\Controllers;

use BaconQrCode\Common\ErrorCorrectionLevel as CommonErrorCorrectionLevel;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Logo\Logo;




class BarcodePartController extends Controller
{
     public function index() {
        return view('ppic.part-barcode');
    }

   public function process(Request $request)
{
    /* ============================
     | 1. VALIDATION
     ============================ */
    $request->validate([
        'zip_files' => 'required|mimes:zip|max:51200', // 50MB
    ]);


    /* ============================
     | 2. PREPARE FOLDER
     ============================ */
    $zipFile = $request->file('zip_files');

    $tempDir = storage_path('app/tmp_zip_' . uniqid());

    // Path absolut di server
$photoDir = '/home/abimany3/public_html/barcode_part';
$qrDir    = '/home/abimany3/public_html/barcode_part/qrcodes';

    File::ensureDirectoryExists($tempDir);
    File::ensureDirectoryExists($photoDir);
    File::ensureDirectoryExists($qrDir);


    /* ============================
     | 3. EXTRACT ZIP
     ============================ */
    $zip = new ZipArchive();

    if ($zip->open($zipFile->path()) !== true) {
        File::deleteDirectory($tempDir);
        return back()->withErrors('Gagal membuka file ZIP');
    }

    $zip->extractTo($tempDir);
    $zip->close();


    /* ============================
     | 4. PROCESS IMAGES
     ============================ */
    $results = [];

    $files = File::allFiles($tempDir);

    foreach ($files as $file) {

        // Hanya image
        if (!in_array(
            strtolower($file->getExtension()),
            ['jpg','jpeg','png','webp']
        )) {
            continue;
        }


        /* ============================
         | FILE NAME
         ============================ */
        $filename = $file->getFilename();

        // Cegah overwrite
        $finalName = $this->resolveDuplicate(
            $photoDir,
            $filename
        );


        /* ============================
         | SAVE IMAGE
         ============================ */
        $photoPath = $photoDir.'/'.$finalName;

        File::copy($file->getPathname(), $photoPath);


        $photoUrl = 'https://abimanyulive.cloud/barcode_part/'.$finalName;





/* ============================
 | GENERATE QR (ENDROID)
 ============================ */
$labelText = pathinfo($finalName, PATHINFO_FILENAME);

$tmpQr = $tempDir.'/tmp_qr_'.uniqid().'.png';

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($photoUrl)
    ->encoding(new Encoding('UTF-8'))
    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->size(400)
    ->margin(2)
    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    ->logoPath(public_path('img/asn-logo-bulat.png'))
    ->logoResizeToWidth(80)
    ->labelText($labelText)
    ->labelAlignment(new LabelAlignmentCenter())
    ->build();

$result->saveToFile($tmpQr);

/* ============================
 | MOVE TO FINAL DIR
 ============================ */
$qrName = $labelText.'.png'; // gunakan string

$finalPath = $qrDir.'/'.$qrName;

File::move($tmpQr, $finalPath);

/* ============================
 | RESULT
 ============================ */
$results[] = [
    'name'    => $labelText, // nama foto
    'qr_file' => $qrName,    // nama file QR
];

    }


    /* ============================
     | 5. CLEANUP
     ============================ */
    File::deleteDirectory($tempDir);


    if (empty($results)) {
        return back()->withErrors('Tidak ada gambar valid di ZIP');
    }


    /* ============================
     | 6. RESPONSE
     ============================ */
   return $this->downloadQrZip($results);

}

private function downloadQrZip($results)
{
    $zipName = 'barcode_result_'.date('Ymd_His').'.zip';

    // ZIP hasil langsung di public_html/tickets
    $zipPath = '/home/abimany3/public_html/barcode_part/'.$zipName;
    File::ensureDirectoryExists('/home/abimany3/public_html/barcode_part');

    $zip = new \ZipArchive();

    if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
        abort(500, 'Gagal membuat ZIP hasil');
    }

    foreach ($results as $item) {

        // Path file QR di server
        // Path file QR di public_html
$qrFile = '/home/abimany3/public_html/barcode_part/qrcodes/'.$item['qr_file'];

        if (file_exists($qrFile)) {

            // Nama di ZIP = nama foto
            $zip->addFile(
                $qrFile,
                $item['name'].'.png'
            );
        }
    }

    $zip->close();


    return response()->download($zipPath)->deleteFileAfterSend(true);
}



/* =====================================================
 | HELPER: Cegah Duplicate Filename
 ===================================================== */
private function resolveDuplicate($dir, $filename)
{
    $path = $dir.'/'.$filename;

    if (!file_exists($path)) {
        return $filename;
    }

    $name = pathinfo($filename, PATHINFO_FILENAME);
    $ext  = pathinfo($filename, PATHINFO_EXTENSION);

    $i = 1;

    while (file_exists($dir.'/'.$name.'_'.$i.'.'.$ext)) {
        $i++;
    }

    return $name.'_'.$i.'.'.$ext;
}


/* =====================================================
 | HELPER: Generate QR + Label
 ===================================================== */
private function buildQrWithLabel($tmpQr, $label, $qrDir, $filename)
{
    $qrImg = imagecreatefrompng($tmpQr);

    $w = imagesx($qrImg);
    $h = imagesy($qrImg);

    $textHeight = 40;


    // Canvas baru
    $canvas = imagecreatetruecolor(
        $w,
        $h + $textHeight
    );

    $white = imagecolorallocate($canvas, 255,255,255);
    $black = imagecolorallocate($canvas, 0,0,0);

    imagefill($canvas, 0, 0, $white);


    // Tempel QR
    imagecopy(
        $canvas,
        $qrImg,
        0,
        0,
        0,
        0,
        $w,
        $h
    );


    // Text center
    $font = 3;

    $textWidth = imagefontwidth($font) * strlen($label);

    $x = ($w - $textWidth) / 2;
    $y = $h + 12;


    imagestring(
        $canvas,
        $font,
        $x,
        $y,
        $label,
        $black
    );


    // Save
    $qrName = 'qr_'.$filename.'.png';

    $path = $qrDir.'/'.$qrName;

    imagepng($canvas, $path);


    // Cleanup
    imagedestroy($qrImg);
    imagedestroy($canvas);


    return $qrName;
}

/* =====================================================
 | HELPER: Add Logo to QR
 ===================================================== */
private function addLogoToQR($qrPath)
{
    $logoPath = public_path('img/asn-logo-bulat.png');

    if (!file_exists($logoPath)) {
        return; // fallback: skip logo
    }

    $qr   = imagecreatefrompng($qrPath);
    $logo = imagecreatefrompng($logoPath);

    $qrW = imagesx($qr);
    $qrH = imagesy($qr);

    $logoW = imagesx($logo);
    $logoH = imagesy($logo);

    // Logo = 20% QR
    $logoSize = $qrW * 0.2;

    $scale = $logoW / $logoSize;

    $newW = $logoW / $scale;
    $newH = $logoH / $scale;

    // Center
    $x = ($qrW - $newW) / 2;
    $y = ($qrH - $newH) / 2;

    imagecopyresampled(
        $qr,
        $logo,
        $x,
        $y,
        0,
        0,
        $newW,
        $newH,
        $logoW,
        $logoH
    );

    imagepng($qr, $qrPath);

    imagedestroy($qr);
    imagedestroy($logo);
}

}