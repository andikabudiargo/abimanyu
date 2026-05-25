<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Request ATK - {{ $atkRequest->request_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html, body {
            width: 210mm;
            background: #fff;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
        }

        /* ── Page ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 16mm 18mm 18mm 18mm;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 2.5px solid #1a1a1a;
            margin-bottom: 22px;
        }

        .header-logo {
            flex-shrink: 0;
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .header-logo-placeholder {
            flex-shrink: 0;
            width: 58px;
            height: 58px;
            border: 1.5px solid #ccc;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #aaa;
            text-align: center;
            line-height: 1.3;
        }

        .header-divider {
            width: 1.5px;
            height: 46px;
            background: #d0d0d0;
            flex-shrink: 0;
        }

        .header-text {
            flex: 1;
        }

        .header-form-title {
            font-family: 'Source Serif 4', serif;
            font-size: 17pt;
            font-weight: 700;
            color: #111;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .header-company {
            font-size: 9.5pt;
            font-weight: 400;
            color: #555;
            margin-top: 3px;
            letter-spacing: 0.1px;
        }

        .header-right {
            text-align: right;
            flex-shrink: 0;
        }

        .doc-number-label {
            font-size: 7.5pt;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
        }

        .doc-number {
            font-family: 'DM Sans', monospace;
            font-size: 10.5pt;
            font-weight: 600;
            color: #111;
            margin-top: 2px;
        }

        .doc-date {
            font-size: 8.5pt;
            color: #666;
            margin-top: 3px;
        }

        /* ── Meta Info ── */
        .meta-section {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .meta-box {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 9px 12px;
            background: #fafafa;
        }

        .meta-label {
            font-size: 7.5pt;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 10pt;
            font-weight: 500;
            color: #1a1a1a;
        }

        /* ── Status Badge ── */
        .status-badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 8.5pt;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-submitted  { background: #f1f1f1; color: #555; border: 1px solid #d5d5d5; }
        .status-approved   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-rejected   { background: #fff1f2; color: #9f1239; border: 1px solid #fda4af; }
        .status-distributed{ background: #f3e8ff; color: #5b21b6; border: 1px solid #c4b5fd; }
        .status-received   { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }

        /* ── Notes ── */
        .notes-section {
            margin-bottom: 16px;
            padding: 9px 13px;
            border-left: 3px solid #1a1a1a;
            background: #f8f8f8;
        }

        .notes-label {
            font-size: 7.5pt;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .notes-text {
            font-size: 9.5pt;
            color: #333;
            line-height: 1.5;
            font-style: italic;
        }

        /* ── Table ── */
        .section-title {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #777;
            margin-bottom: 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .items-table thead tr {
            background: #1a1a1a;
            color: #fff;
        }

        .items-table thead th {
            padding: 8px 11px;
            font-size: 8.5pt;
            font-weight: 600;
            text-align: left;
            letter-spacing: 0.4px;
        }

        .items-table thead th.center { text-align: center; }

        .items-table tbody tr {
            border-bottom: 1px solid #ececec;
        }

        .items-table tbody tr:last-child {
            border-bottom: 2px solid #1a1a1a;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table td {
            padding: 8px 11px;
            font-size: 10pt;
            vertical-align: middle;
        }

        .items-table td.center { text-align: center; }

        .td-no {
            width: 36px;
            color: #888;
            font-size: 9pt;
        }

        .td-name {
            font-weight: 500;
        }

        .td-qty {
            width: 80px;
            text-align: center;
            font-weight: 600;
        }

        .td-uom {
            width: 70px;
            color: #666;
            font-size: 9pt;
        }

        /* empty rows for manual fill */
        .items-table tr.empty-row td {
            color: #bbb;
            font-size: 9pt;
            font-style: italic;
        }

        /* ── Signature Section ── */
        .signature-section {
            margin-top: auto;
            padding-top: 20px;
        }

        .signature-row {
            display: flex;
            gap: 0;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            overflow: hidden;
        }

        .sig-col {
            flex: 1;
            padding: 14px 16px 0 16px;
            text-align: center;
            border-right: 1px solid #d0d0d0;
        }

        .sig-col:last-child {
            border-right: none;
        }

        .sig-role {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-weight: 700;
            color: #444;
            margin-bottom: 2px;
        }

        .sig-sublabel {
            font-size: 8pt;
            color: #aaa;
            margin-bottom: 48px;
        }

        .sig-line {
            border-top: 1px solid #aaa;
            margin: 0 12px;
        }

        .sig-name {
            font-size: 9.5pt;
            font-weight: 600;
            color: #111;
            padding: 7px 0 5px 0;
        }

        .sig-name-empty {
            font-size: 9.5pt;
            color: #ccc;
            padding: 7px 0 5px 0;
        }

        .sig-date {
            font-size: 8pt;
            color: #666;
            padding-bottom: 10px;
        }

        .sig-date-empty {
            font-size: 8pt;
            color: #ccc;
            padding-bottom: 10px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            font-size: 7.5pt;
            color: #bbb;
        }

        .footer-right {
            font-size: 7.5pt;
            color: #bbb;
            text-align: right;
        }

        /* ── Print ── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                page-break-after: avoid;
            }

            .no-print {
                display: none !important;
            }
        }

        /* ── Print Button (screen only) ── */
        .print-btn-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            gap: 8px;
        }

        .btn-print {
            background: #1a1a1a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10pt;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .btn-back {
            background: #fff;
            color: #333;
            border: 1px solid #ccc;
            padding: 10px 16px;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10pt;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        @media screen {
            body {
                background: #e5e5e5;
                display: flex;
                justify-content: center;
                padding: 40px 0 60px;
            }

            .page {
                box-shadow: 0 4px 32px rgba(0,0,0,0.15);
                background: #fff;
            }
        }
    </style>
</head>
<body>

{{-- ── Print / Back Buttons (screen only) ── --}}
<div class="print-btn-wrapper no-print">
    <a href="{{ url()->previous() }}" class="btn-back">
        ← Kembali
    </a>
    <button class="btn-print" onclick="window.print()">
        &#128438; Cetak Dokumen
    </button>
</div>

<div class="page">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="header">

      @if (file_exists(public_path('img/logo-2.jpg')))
    <img src="{{ asset('img/logo-2.jpg') }}" alt="Logo" class="header-logo">
@else
    <div class="header-logo-placeholder">LOGO</div>
@endif

        <div class="header-divider"></div>

        <div class="header-text">
            <div class="header-form-title">Form Request ATK</div>
            <div class="header-company">PT. Abimanyu Sekar Nusantara</div>
        </div>

        <div class="header-right">
            <div class="doc-number-label">No. Request</div>
            <div class="doc-number">{{ $atkRequest->request_number }}</div>
            <div class="doc-date">
                @php
                    $statusMap = [
                        'submitted'   => ['class' => 'status-submitted',   'label' => 'Submitted'],
                        'approved'    => ['class' => 'status-approved',    'label' => 'Approved'],
                        'rejected'    => ['class' => 'status-rejected',    'label' => 'Rejected'],
                        'distributed' => ['class' => 'status-distributed', 'label' => 'Distributed'],
                        'received'    => ['class' => 'status-received',    'label' => 'Received'],
                    ];
                    $s = $statusMap[strtolower($atkRequest->status)] ?? ['class' => 'status-submitted', 'label' => ucfirst($atkRequest->status)];
                @endphp
                 <span class="status-badge {{ $s['class'] }}">{{ $s['label'] }}</span>
            </div>
        </div>

    </div>


    {{-- ══════════════════════════════════════
         TABEL ITEM
    ══════════════════════════════════════ --}}
    <div class="section-title">Daftar Item ATK</div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="td-no center">No.</th>
                <th>Nama ATK</th>
                <th class="center">Qty</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($atkRequest->items as $i => $item)
            <tr>
                <td class="td-no center">{{ $i + 1 }}</td>
                <td class="td-name">{{ $item->atk?->name ?? '—' }}</td>
                <td class="td-qty">{{ $item->qty }}</td>
                <td class="td-uom">{{ $item->atk?->uom ?? '—' }}</td>
            </tr>
            @endforeach

            {{-- Baris kosong untuk padding visual jika item sedikit --}}
            @if ($atkRequest->items->count() < 5)
                @for ($e = 0; $e < (5 - $atkRequest->items->count()); $e++)
                <tr class="empty-row">
                    <td class="center">{{ $atkRequest->items->count() + $e + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>

    
    {{-- ══════════════════════════════════════
         CATATAN
    ══════════════════════════════════════ --}}
    @if ($atkRequest->note)
    <div class="notes-section">
        <div class="notes-label">Catatan</div>
        <div class="notes-text">{{ $atkRequest->note }}</div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         TANDA TANGAN
    ══════════════════════════════════════ --}}
    <div class="signature-section">
        <div class="section-title">Tanda Tangan</div>

        <div class="signature-row">

            {{-- Kolom 1: Pembuat --}}
            <div class="sig-col">
                <div class="sig-role">Pembuat</div>
                <div class="sig-sublabel">(Pemohon)</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $atkRequest->createdBy?->name ?? '—' }}</div>
                <div class="sig-date">
                    {{ \Carbon\Carbon::parse($atkRequest->created_at)->locale('id')->translatedFormat('d F Y') }}
                </div>
            </div>

            {{-- Kolom 2: Menyetujui --}}
            <div class="sig-col">
                <div class="sig-role">Menyetujui</div>
                <div class="sig-sublabel">(Admin GA)</div>
                <div class="sig-line"></div>

                @if ($atkRequest->approvedBy)
                    <div class="sig-name">{{ $atkRequest->approvedBy->name }}</div>
                    <div class="sig-date">
                        {{ \Carbon\Carbon::parse($atkRequest->approved_at)->locale('id')->translatedFormat('d F Y') }}
                    </div>
                @else
                    <div class="sig-name-empty">—</div>
                    <div class="sig-date-empty">Tanggal</div>
                @endif
            </div>

            {{-- Kolom 3: Diterima (kosong, isi manual) --}}
            <div class="sig-col">
                <div class="sig-role">Diterima</div>
                <div class="sig-sublabel">(Penerima)</div>
                <div class="sig-line"></div>
                <div class="sig-name-empty">&nbsp;</div>
                <div class="sig-date-empty">Tanggal</div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════
         FOOTER
    ══════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-left">
            Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} &mdash; {{ auth()->user()?->name }}
        </div>
        <div class="footer-right">
            {{ $atkRequest->request_number }} &bull; PT. Abimanyu Sekar Nusantara
        </div>
    </div>

</div>

</body>
</html>