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
            vertical-align: middle;
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
            font-size: 8px;
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
        .h-50 { height: 50px; }
        .h-80 { height: 80px; }
        .h-120 { height: 120px; }
        .h-150 { height: 150px; }
    </style>
</head>
<body>

<table>
    <tr>
         <!-- Logo -->
         <td width="20%">
            @if($logo)
    <img src="{{ $logo }}" alt="Logo" style="width:140px;">
        @endif 
        </td>

        <td width="50%" class="title">
            FORM PENGAJUAN PEMBUATAN DAN PERUBAHAN DOKUMEN /<br>
            <i>SUBMISSION FORM MAKING AND CHANGING DOCUMENTS</i>
        </td>
        <td width="30%" style="padding:0;">
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

<table style="width:100%; border-collapse: collapse; table-layout: fixed;">

    {{-- Header --}}
    <tr>
        <td width="55%" class="section-title">
            ALASAN PENGAJUAN / <i>REASON FOR SUBMISSION</i>
        </td>

        <td width="45%" class="section-title">
            PERMOHONAN SALINAN / <i>APPLICATION FOR COPIES</i>
        </td>
    </tr>

    {{-- Content --}}
    <tr>
        <td width="55%" class="h-120" style="vertical-align: top;">
            {{ $document->reason ?? '-' }}
        </td>

        <td width="45%" style="padding:0; vertical-align: top;">
            <table style="width:100%; border-collapse: collapse; table-layout: fixed; font-size:10px;">

                <tr>
                    <td width="32%">PRODUKSI</td>
                    <td width="4%" class="center">:</td>
                    <td width="14%" class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Production'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td width="32%">MANAGEMENT SYSTEM</td>
                    <td width="4%" class="center">:</td>
                    <td width="14%" class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Management Representative & HSE'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>QUALITY</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Quality'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>MAINTENANCE</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Maintenance'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>ENGINEERING NEW PRODUCT</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Engineering New Product Development'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>HSE</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Health, Safety & Environment'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>ENGINEERING PRODUCTION</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Engineering Production'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>HRGA & IT</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Human Resource Development'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>IMPROVEMENT</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Improvement'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>MARKETING</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Marketing'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>LOGISTIC</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'PPIC - Logistik'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>PURCHASING</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Purchasing'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>DELIVERY</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'PPIC - Delivery'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td>FINANCE & ACCOUNTING</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'Finance & Accounting'
                                )
                            )->qty ?? ''
                        }}
                    </td>
                </tr>

                <tr>
                    <td>PLANNING</td>
                    <td class="center">:</td>
                    <td class="center">
                        {{
                            optional(
                                collect($document->copies)->first(fn($copy) =>
                                    ($copy->department->name ?? '') == 'PPIC - Planning'
                                )
                            )->qty ?? ''
                        }}
                    </td>

                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
<table>
    {{-- Header utama --}}
    <tr>
        <td width="55%" class="section-title">
            SEBELUM PERUBAHAN / <i>BEFORE CHANGE</i>
        </td>

        <td colspan="5" class="section-title">
            PERMOHONAN PENAMBAHAN SALINAN /
            <i>APPLICATION FOR ADDITIONAL COPIES</i>
        </td>
    </tr>

    {{-- Sub header kanan --}}
    <tr>
        <td rowspan="4" class="h-120" style="vertical-align: top;">
            {{ $document->revision->before_change ?? '-' }}
        </td>

        <td width="8%" class="center small">
            Number of<br>Copies
        </td>

        <td width="8%" class="center small">
            Dept.
        </td>

        <td width="8%" class="center small">
            Reason for addition
        </td>

        <td width="8%" class="center small">
            Date<br>Submission
        </td>

        <td width="8%" class="center small">
            Name &<br>approval
        </td>
    </tr>

    {{-- Row kosong BEFORE CHANGE --}}
    @for($i = 0; $i < 3; $i++)
        <tr>
            <td style="height:28px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endfor

    {{-- AFTER CHANGE TITLE --}}
    <tr>
        <td class="section-title">
            SETELAH PERUBAHAN / <i>AFTER CHANGES</i>
        </td>

         <td style="height:28px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
    </tr>

    {{-- AFTER CHANGE CONTENT --}}
    @for($i = 0; $i < 3; $i++)
        <tr>
            @if($i == 0)
                <td rowspan="4" class="h-150" style="vertical-align: top;">
                    {{ $document->revision->after_change ?? '-' }}
                </td>
            @endif

            <td style="height:28px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endfor
</table>

<table>
    {{-- Header --}}
    <tr>
        <td width="20%" class="center">
            <b>Fill By :<br>Management System</b>
        </td>

        {{-- Kolom pembatas vertikal 5% --}}
        <td width="5%" rowspan="5"></td>

        <td width="40%" colspan="2" class="center">
            <b>Fill By :<br>Related Dept</b>
        </td>

        <td width="35%" rowspan="5" class="small" style="vertical-align: top;">
            <b>Catatan:</b><br>
            * Draft Dokumen yang dibuat user dilampirkan Soft Copy Dokumen melalui Email ke DCC.<br>
            * Proses TTD Dokumen harus sudah selesai dan kembali ke DCC paling lambat H+3.<br><br>

            <b>Note:</b><br>
            * Draft Documents created by the user are attached with Soft Copy Documents via Email to DCC.<br>
            * Document approval process must be completed and returned to DCC not more than 3 day.
        </td>
    </tr>

    {{-- Sub Header --}}
    <tr>
        <td class="center">
            Accepted by
        </td>

        <td width="17.5%" class="center">
            Prepared by
        </td>

        <td width="17.5%" class="center">
            Approved by (SPV)
        </td>
    </tr>

    {{-- Area tanda tangan --}}
    <tr>
        <td class="h-50"></td>
        <td></td>
        <td></td>
    </tr>

    {{-- Date --}}
    <tr>
        <td class="center">
    Date : {{ $document->authorized_at ? \Carbon\Carbon::parse($document->authorized_at)->format('d-m-Y') : '' }}
</td>

<td class="center">
    Date : {{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d-m-Y') : '' }}
</td>

<td class="center">
    Date : {{ $document->approved_at ? \Carbon\Carbon::parse($document->approved_at)->format('d-m-Y') : '' }}
</td>
    </tr>

    {{-- Name --}}
    <tr>
        <td class="center">
            Name : {{ $document->authorizedBy->name ?? '-' }}
        </td>

        <td class="center">
            Name : {{ $document->createdBy->name ?? '-' }}
        </td>

        <td class="center">
            Name : {{ $document->approvedBy->name ?? '-' }}
        </td>
    </tr>
</table>

</body>
</html>
