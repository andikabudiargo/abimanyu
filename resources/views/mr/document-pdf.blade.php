<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Submission Form PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            line-height: 1.5;
        }

        .section-title {
            font-weight: bold;
            text-align: center;
            background: #f5f5f5;
        }

        .h-80 { height: 80px; }
        .h-120 { height: 120px; }
        .h-150 { height: 150px; }
    </style>
</head>
<body>

<table>
    <tr>
        <td width="65%" class="title">
            FORM PENGAJUAN PEMBUATAN DAN PERUBAHAN DOKUMEN /<br>
            <i>SUBMISSION FORM MAKING AND CHANGING DOCUMENTS</i>
        </td>
        <td width="35%" style="padding:0;">
            <table>
                <tr><td>No. Doc</td><td>: MSS-04.01-FM</td></tr>
                <tr><td>Issue Date</td><td>: 02 Januari 2019</td></tr>
                <tr><td>Date Of Rev</td><td>: 16 Desember 2025</td></tr>
                <tr><td>No. Rev</td><td>: 05</td></tr>
                <tr><td>Page</td><td>: 1/1</td></tr>
            </table>
        </td>
    </tr>
</table>

<table>
    <tr><td width="35%"><b>Jenis Dokumen</b><br><i>Document Type</i></td><td>: {{ $document->document_type ?? '-' }}</td></tr>
    <tr><td><b>Jenis Pengajuan</b><br><i>Submission Type</i></td><td>: {{ $document->submission_type ?? '-' }}</td></tr>
    <tr><td><b>Lampiran 4M</b></td><td>: {{ $document->need_4m ? 'Diperlukan' : 'Tidak Diperlukan' }}</td></tr>
    <tr><td><b>Nomor Dokumen</b><br><i>Document Number</i></td><td>: {{ $document->document_number ?? '-' }}</td></tr>
    <tr><td><b>Judul Dokumen</b><br><i>Document Title</i></td><td>: {{ $document->document_title ?? '-' }}</td></tr>
    <tr><td><b>Nomor Revisi Sebelumnya</b><br><i>Number Previous Revision</i></td><td>: {{ $document->revision->revision_number ?? '-' }}</td></tr>
    <tr><td><b>Department</b></td><td>: {{ $document->department->name ?? '-' }}</td></tr>
</table>

<table>
    <tr>
        <td width="55%" class="section-title">ALASAN PENGAJUAN / <i>REASON FOR SUBMISSION</i></td>
        <td width="45%" class="section-title">PERMOHONAN SALINAN / <i>APPLICATION FOR COPIES</i></td>
    </tr>
    <tr>
        <td class="h-120">{{ $document->reason ?? '-' }}</td>
        <td>
            @foreach($document->copies ?? [] as $copy)
                {{ $copy->department->name ?? '-' }} : {{ $copy->qty ?? 0 }}<br>
            @endforeach
        </td>
    </tr>
</table>

<table>
    <tr>
        <td width="55%" class="section-title">SEBELUM PERUBAHAN / <i>BEFORE CHANGE</i></td>
        <td width="45%" class="section-title">PERMOHONAN PENAMBAHAN SALINAN / <i>APPLICATION FOR ADDITIONAL COPIES</i></td>
    </tr>
    <tr>
        <td class="h-80">{{ $document->revision->before_change ?? '-' }}</td>
        <td class="h-80"></td>
    </tr>
</table>

<table>
    <tr>
        <td class="section-title">SETELAH PERUBAHAN / <i>AFTER CHANGES</i></td>
    </tr>
    <tr>
        <td class="h-120">{{ $document->revision->after_change ?? '-' }}</td>
    </tr>
</table>

<table>
    <tr>
        <td width="20%" class="center">
            <b>Fill By :<br>Management System</b><br><br>
            Accepted by<br><br><br>
            Date : __________<br>
            Name : Dorojatus Salim
        </td>
        <td width="20%" class="center">
            <b>Fill by : Related Dept.</b><br><br>
            Prepared by<br><br><br>
            Date : __________<br>
            Name : __________
        </td>
        <td width="20%" class="center">
            <b>Approved by (SPV)</b><br><br><br><br>
            Date : __________<br>
            Name : __________
        </td>
        <td width="40%" class="small">
            <b>Catatan:</b><br>
            * Draft Dokumen yang dibuat user dilampirkan Soft Copy Dokumen melalui Email ke DCC.<br>
            * Proses TTD Dokumen harus sudah selesai dan kembali ke DCC paling lambat H+3.<br><br>

            <b>Note:</b><br>
            * Draft Documents created by the user are attached with Soft Copy Documents via Email to DCC.<br>
            * Document approval process must be completed and returned to DCC not more than 3 day.
        </td>
    </tr>
</table>

</body>
</html>
