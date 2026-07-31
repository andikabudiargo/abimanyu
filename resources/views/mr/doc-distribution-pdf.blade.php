<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lembar Distribusi Dokumen PDF</title>
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
            table-layout: fixed;
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

        .left {
            text-align: left;
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

        .h-30 {
            height: 30px;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<table>
    <tr>
         <td width="20%">
            @if($logo)
    <img src="{{ $logo }}" alt="Logo" style="width:140px;">
        @endif 
        </td>
        <td width="50%" class="title">
            LEMBAR DISTRIBUSI DOKUMEN<br>
            <i>DOCUMENT DISTRIBUTION SHEET</i>
        </td>

        <td width="30%" style="padding:0;">
            <table>
                <tr>
                    <td>No. Doc</td>
                    <td>: MSS - 04.02 - FM</td>
                </tr>
                <tr>
                    <td>Issue Date</td>
                    <td>: 02 Jan 2019</td>
                </tr>
                <tr>
                    <td>Date Of Rev</td>
                    <td>: 14 Jan 2020</td>
                </tr>
                <tr>
                    <td>No. Rev</td>
                    <td>: 01</td>
                </tr>
                <tr>
                    <td>Page</td>
                    <td>: 1/1</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- FORM INFO --}}
<table>
    <tr>
        <td width="35%">
            <b>NOMOR DOKUMEN</b><br>
            <i>DOCUMENT NUMBER</i>
        </td>
        <td>: {{ $document->document_number ?? '' }}</td>
    </tr>

    <tr>
        <td>
            <b>JUDUL DOKUMEN</b><br>
            <i>DOCUMENT TITLE</i>
        </td>
        <td>: {{ $document->document_title ?? '' }}</td>
    </tr>

    <tr>
        <td>
            <b>NOMOR REVISI</b><br>
            <i>REVISION NUMBER</i>
        </td>
        <td>: {{ $document->revision->revision_number ?? '' }}</td>
    </tr>

    <tr>
        <td>
            <b>TANGGAL BERLAKU</b><br>
            <i>EFFECTIVE DATE</i>
        </td>
        <td>: {{ $document->effective_date ? \Carbon\Carbon::parse($document->effective_date)->format('d-m-Y') : '' }}</td>
    </tr>
</table>

{{-- DISTRIBUTION TABLE --}}
{{-- DISTRIBUTION TABLE --}}
<table>
    <tr class="section-title">
        <td width="5%" class="center">NO</td>

        <td width="13%" class="center">
            NO. COPY DOCUMENT
        </td>

        <td width="13%" class="center">
            RECEIVED DATE
        </td>

        <td width="16%" class="center">
            NO. COPY DOCUMENT WAS TAKEN
        </td>

        <td width="13%" class="center">
            DATE OF TAKEN
        </td>

        <td width="20%" class="center">
            DEPARTEMEN RECEIVER
        </td>

        <td width="20%" class="center">
            NAME & SIGN
        </td>
    </tr>

   @for($i = 0; $i < 12; $i++)
    @php
        $copy = $document->copies[$i] ?? null;
        $taken = $copy ? ($takenByDept->get($copy->department_id)) : null;
    @endphp

    <tr>
        <td class="center h-30">{{ $i + 1 }}</td>

        <td class="center">
            {{ $copy ? ($i + 1) : '' }}
        </td>

        <td class="center">
            {{ $copy && $copy->socialization_date
                ? \Carbon\Carbon::parse($copy->socialization_date)->format('d-m-Y')
                : '' }}
        </td>

        {{-- NO. COPY DOCUMENT WAS TAKEN --}}
        <td class="center">
            {{ $taken && $taken->copies_taken
                ? (($document->revision->revision_number ?? 1) - 1)
                : '' }}
        </td>

        {{-- DATE OF TAKEN --}}
        <td class="center">
            {{ $taken && $taken->copies_taken_at
                ? \Carbon\Carbon::parse($taken->copies_taken_at)->format('d-m-Y')
                : '' }}
        </td>

        <td>{{ $copy->department->name ?? '' }}</td>

        <td>
            {{ $taken->takenFrom->name ?? $copy->socialized->name ?? '' }}
        </td>
    </tr>
@endfor
</table>

</body>
</html>