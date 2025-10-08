@extends('layouts.app')
@section('title', 'Preview Label')

@section('content')
<style>
  .label-container {
    width: 100mm;
    height: 60mm;
    padding: 4mm;
    box-sizing: border-box;
    background: white;
    page-break-after: always;
  }

  table {
    width: 100%;
    height: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    font-size: 10px;
    table-layout: fixed;
    color: #000;

  }

  td {
    border: 1px solid #000;
    padding: 2px 4px;
    vertical-align: top;
     color: #000; /* pastikan teks hitam */
    white-space: normal;       /* agar teks tidak dipotong */
    overflow: visible;         /* agar teks tidak tersembunyi */
    line-height: 1.2;          /* agar spasi antar baris cukup */
    word-wrap: break-word;     /* pisahkan kata jika terlalu panjang */
  }

  @media print {
    @page {
      size: 100mm 60mm;
      margin: 0;
    }

    body {
      margin: 0;
    }
  }
</style>

<div class="label-container">
  <table>
    <tr>
      <td colspan="2" style="text-align:center; vertical-align: middle;"><b>PT. Abimanyu Sekar Nusantara</b></td>
      <td style="text-align:center; vertical-align: middle;"><b>Reference Number</b></td>
      <td colspan="2" style="text-align:center; vertical-align: middle;">01928827384234</td>
    </tr>
    <tr>
      <td rowspan="3" colspan="2"><!-- QR / Logo bisa diletakkan di sini --></td>
      <td colspan="3" style="text-align:center; vertical-align: middle;">PT Autoplastik Indonesia</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center; vertical-align: middle;"><b>Article Name</b></td>
      <td style="text-align:center; vertical-align: middle;"><b>Exp. Date</b></td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center; vertical-align: middle;">20L-THINNER CLEANING FOR CED</td>
      <td style="text-align:center; vertical-align: middle;">23 Juni 2025</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center; vertical-align: middle;">TRIN-ASN-2025-VI-0001-B1-P1</td>
      <td colspan="2" style="text-align:center; vertical-align: middle;"><b>Article Code</b></td>
      <td style="text-align:center; vertical-align: middle;"><b>Date</b></td>
    </tr>
    <tr>
      <td rowspan="2" style="text-align:center; vertical-align: middle; font-size:20px;"><b>Qty</b></td>
      <td rowspan="2" style="text-align:center; vertical-align: middle; font-size:20px;">12</td>
      <td colspan="2" style="text-align:center; vertical-align: middle;">CM10000395</td>
      <td style="text-align:center; vertical-align: middle;">20 Juni 2025</td>
    </tr>
    <tr>
      <td><b>Operator</b></td>
      <td>Salvo</td>
      <td style="text-align:center; vertical-align: middle;">Shift A</td>
    </tr>
  </table>
</div>
@endsection
