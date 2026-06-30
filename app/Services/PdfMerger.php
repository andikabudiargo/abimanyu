<?php

namespace App\Services;

use Mpdf\Mpdf;
use setasign\Fpdi\FpdiTrait;

class PdfMerger extends Mpdf
{
    use FpdiTrait;
}   