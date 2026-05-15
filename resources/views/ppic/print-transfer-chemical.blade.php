<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }} - Transfer #{{ $transfer->id }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      font-size: 10px;
      color: #1a1a2e;
      background: #fff;
    }

    /* ── PAGE ── */
    @page { size: A4 landscape; margin: 10mm 12mm; }

    .page {
      width: 100%;
      min-height: 190mm;
      padding: 0;
    }

    /* ── HEADER BLOCK ── */
    .header-wrap {
      display: flex;
      align-items: stretch;
      border: 1.5px solid #1a3a5c;
      border-radius: 6px;
      overflow: hidden;
      margin-bottom: 8px;
      background: linear-gradient(135deg, #1a3a5c 0%, #2563a8 100%);
    }

    .header-logo {
      background: #fff;
      padding: 8px 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-right: 2px solid #1a3a5c;
      min-width: 100px;
    }

    .header-logo img {
      max-height: 45px;
      max-width: 90px;
      object-fit: contain;
    }

    .header-center {
      flex: 1;
      padding: 8px 14px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .header-title {
      font-size: 14px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 2px;
    }

    .header-subtitle {
      font-size: 9px;
      color: #bfdbfe;
      letter-spacing: 0.3px;
    }

    .header-meta {
      padding: 8px 14px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 3px;
      border-left: 1px solid rgba(255,255,255,0.2);
      min-width: 160px;
    }

    .header-meta-row {
      display: flex;
      gap: 6px;
      align-items: baseline;
    }

    .header-meta-label {
      font-size: 8px;
      color: #93c5fd;
      width: 28px;
      flex-shrink: 0;
    }

    .header-meta-value {
      font-size: 9px;
      color: #fff;
      font-weight: 600;
    }

    .header-badge {
      padding: 8px 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-left: 1px solid rgba(255,255,255,0.2);
    }

    .badge-supply {
      background: #16a34a;
      color: #fff;
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .badge-return {
      background: #dc2626;
      color: #fff;
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    /* ── TABLE ── */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    thead tr {
      background: #1a3a5c;
      color: #fff;
    }

    thead th {
      padding: 5px 6px;
      text-align: center;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.3px;
      border: 1px solid #1a3a5c;
    }

    tbody tr {
      height: 20px;
    }

    tbody tr:nth-child(even) { background: #f0f7ff; }
    tbody tr:nth-child(odd)  { background: #fff; }

    tbody td {
      padding: 3px 6px;
      border: 1px solid #c8ddf0;
      font-size: 9px;
      vertical-align: middle;
    }

    .td-no     { text-align: center; width: 28px; }
    .td-code   { width: 90px; font-weight: 600; }
    .td-name   { }
    .td-cond   { text-align: center; width: 62px; }
    .td-satpkg { text-align: center; width: 68px; }
    .td-qty    { text-align: right;  width: 55px; font-weight: 600; }
    .td-sat    { text-align: center; width: 45px; }
    .td-ket    { width: 100px; }

    tbody tr.empty-row td { color: transparent; }

    /* ── SIGNATURE ── */
    .signature-wrap {
      display: flex;
      justify-content: flex-end;
      margin-top: 6px;
      gap: 20px;
    }

    .signature-box {
      border: 1px solid #1a3a5c;
      border-radius: 4px;
      width: 130px;
      overflow: hidden;
    }

    .signature-header {
      background: #1a3a5c;
      color: #fff;
      text-align: center;
      padding: 3px 6px;
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .signature-body {
      height: 55px;
      padding: 4px 6px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .signature-info {
      font-size: 7.5px;
      color: #374151;
      line-height: 1.4;
    }

    .signature-name {
      font-size: 8px;
      font-weight: 700;
      color: #1a3a5c;
      text-align: center;
      border-top: 1px solid #c8ddf0;
      padding-top: 2px;
    }

    /* ── PRINT ── */
    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .no-print { display: none !important; }
    }

    /* ── SCREEN PREVIEW ── */
    @media screen {
      body { background: #e5e7eb; padding: 20px; }
      .page {
        background: #fff;
        width: 277mm;
        margin: 0 auto;
        padding: 10mm 12mm;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        border-radius: 4px;
      }
      .print-btn {
        display: flex;
        justify-content: center;
        margin-bottom: 16px;
      }
      .print-btn button {
        background: #1a3a5c;
        color: #fff;
        border: none;
        padding: 8px 24px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        font-weight: 600;
      }
    }
  </style>
</head>
<body>

<div class="print-btn no-print">
  <button onclick="window.print()">🖨️ Print</button>
</div>

<div class="page">

  {{-- ── HEADER ── --}}
  <div class="header-wrap">

    <div class="header-logo">
      <img src="{{ asset('img/logo-2.jpg') }}" alt="Logo"
           onerror="this.style.display='none'">
    </div>

    <div class="header-center">
      <div class="header-title">Transfer Chemical</div>
      <div class="header-subtitle">
        {{ $isSupply ? 'Supply — Warehouse Chemical ke Booth' : 'Return — Booth ke Warehouse Chemical' }}
      </div>
    </div>

    <div class="header-meta">
      <div class="header-meta-row">
        <span class="header-meta-label">Tanggal</span>
        <span class="header-meta-value">{{ $transfer->transfer_date->format('d/m/Y') }}</span>
      </div>
      <div class="header-meta-row">
        <span class="header-meta-label">From</span>
        <span class="header-meta-value">{{ $transfer->location_from }}</span>
      </div>
      <div class="header-meta-row">
        <span class="header-meta-label">To</span>
        <span class="header-meta-value">{{ $transfer->location_to }}</span>
      </div>
    </div>

    <div class="header-badge">
      <span class="{{ $isSupply ? 'badge-supply' : 'badge-return' }}">
        {{ $isSupply ? 'Supply' : 'Return' }}
      </span>
    </div>

  </div>

  {{-- ── TABLE ── --}}
  <table>
    <thead>
      <tr>
        <th class="td-no">NO</th>
        <th class="td-code">CODE</th>
        <th class="td-name">NAME</th>
        <th class="td-cond">CONDITION</th>
        <th class="td-satpkg">SATUAN PACKING</th>
        <th class="td-qty">JUMLAH</th>
        <th class="td-sat">SATUAN</th>
        <th class="td-ket">KETERANGAN</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $i => $row)
        @if($row)
          <tr>
            <td class="td-no">{{ $i + 1 }}</td>
            <td class="td-code">{{ $row['article_code'] }}</td>
            <td class="td-name">{{ $row['description'] }}</td>
            <td class="td-cond">{{ $row['condition'] }}</td>
            <td class="td-satpkg">{{ $row['min_package'] }} {{ $row['min_package_unit'] }}</td>
            <td class="td-qty">{{ $row['qty'] }}</td>
            <td class="td-sat">{{ $row['unit'] }}</td>
            <td class="td-ket"></td>
          </tr>
        @else
          <tr class="empty-row">
            <td class="td-no">{{ $i + 1 }}</td>
            <td class="td-code">&nbsp;</td>
            <td class="td-name">&nbsp;</td>
            <td class="td-cond">&nbsp;</td>
            <td class="td-satpkg">&nbsp;</td>
            <td class="td-qty">&nbsp;</td>
            <td class="td-sat">&nbsp;</td>
            <td class="td-ket">&nbsp;</td>
          </tr>
        @endif
      @endforeach
    </tbody>
  </table>

  {{-- ── SIGNATURE ── --}}
  <div class="signature-wrap">

    <div class="signature-box">
      <div class="signature-header">Pengirim</div>
      <div class="signature-body">
        <div class="signature-info">
          
         
        </div>
        <div class="signature-name">
          ({{ optional($transfer->createdBy)->name ?? $transfer->created_by ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }})<br>
          {{ $transfer->created_at->format('d/m/Y H:i') }}
        </div>
      </div>
    </div>

    <div class="signature-box">
      <div class="signature-header">Penerima</div>
      <div class="signature-body">
        <div class="signature-info">&nbsp;</div>
        <div class="signature-name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
      </div>
    </div>

  </div>

</div>

<script>
  // Auto-print when opened in new tab
  window.addEventListener('load', () => {
    // small delay so styles render first
    setTimeout(() => window.print(), 400);
  });
</script>

</body>
</html>