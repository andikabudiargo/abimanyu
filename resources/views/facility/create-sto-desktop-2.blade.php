{{-- ============================================================
     create-sto-desktop-2.blade.php
     Versi Chemical/Consumable: Area & Shelf dari STO Reference Master
     ============================================================ --}}

@php
  $isChemCons = in_array($warehouse ?? null, ['Chemical', 'Consumable']);

  $year  = 2026;
  $month = '05';
  $month = str_pad($month, 2, '0', STR_PAD_LEFT);

  $stoRange = [
    'Dead Stock CM1' => [1, 48],
    'Chemical'       => [1000, 1999],
    'Consumable'     => [2000, 2999],
    'Raw Material'   => [3000, 3999],
    'WIP Buffing'    => [5000, 5999],
    'WIP Sanding'    => [6000, 6999],
    'WIP Touch Up'   => [7000, 7999],
    'Finish Goods'   => [4000, 4999],
    'OT'             => [51, 999],
    'Werate'         => [8000, 8999],
  ];

  $ranges = [];
  if (is_array($allowedWarehouses)) {
    foreach ($allowedWarehouses as $wh) {
      if (isset($stoRange[$wh])) $ranges[] = $stoRange[$wh];
    }
  }
  if (empty($ranges)) $ranges = array_values($stoRange);
@endphp

@push('styles')
<style>
/* ── DESIGN TOKENS ─────────────────────────────────────────── */
:root {
  --sto-blue:        #1565C0;
  --sto-blue-mid:    #1976D2;
  --sto-blue-light:  #E3F0FC;
  --sto-blue-border: #BBDEFB;
  --sto-surface:     #F8FAFD;
  --sto-white:       #FFFFFF;
  --sto-border:      #DDE3EC;
  --sto-text:        #1A2332;
  --sto-text-muted:  #64748B;
  --sto-text-label:  #475569;
  --sto-accent:      #0D47A1;
  --sto-green:       #1B5E20;
  --sto-green-mid:   #2E7D32;
  --sto-green-light: #E8F5E9;
  --sto-row-hover:   #F0F6FF;
  --sto-radius-sm:   4px;
  --sto-radius-md:   6px;
  --sto-radius-lg:   8px;
  --sto-shadow-card: 0 1px 4px rgba(21,101,192,.08), 0 0 0 1px rgba(21,101,192,.07);
  --sto-shadow-sm:   0 1px 2px rgba(0,0,0,.06);
  --font-ui:         'DM Sans', 'Segoe UI', system-ui, sans-serif;
}

.sto-wrap { font-family: var(--font-ui); background: var(--sto-surface); min-height: 100vh; }

.sto-card {
  background: var(--sto-white);
  border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-lg);
  box-shadow: var(--sto-shadow-card);
  overflow: hidden;
}

/* ── HEADER BAR ─────────────────────────────────────────────── */
.sto-header {
  background: var(--sto-blue);
  padding: 20px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 3px solid var(--sto-accent);
}
.sto-header-left h1 { font-size: 17px; font-weight: 600; color: #fff; letter-spacing: .4px; margin: 0; }
.sto-header-left p  { font-size: 12px; color: rgba(255,255,255,.72); margin: 3px 0 0; }

.sto-badge-status {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
  border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #fff;
}
.sto-badge-status .dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #69F0AE; box-shadow: 0 0 0 2px rgba(105,240,174,.3);
}

.sto-logo-wrap img { height: 48px; width: auto; opacity: .92; }

/* ── TOOLBAR ─────────────────────────────────────────────────── */
.sto-toolbar {
  background: var(--sto-white);
  border-bottom: 1px solid var(--sto-border);
  padding: 0 32px;
  display: flex; align-items: center;
  min-height: 46px;
}
.sto-tab {
  padding: 12px 18px; font-size: 13px; font-weight: 500;
  color: var(--sto-text-muted); border-bottom: 2px solid transparent;
  cursor: pointer; white-space: nowrap; transition: color .15s, border-color .15s;
}
.sto-tab.active { color: var(--sto-blue); border-color: var(--sto-blue); }

/* ── BODY ────────────────────────────────────────────────────── */
.sto-body { padding: 28px 32px; }

.sto-section-title {
  font-size: 11px; font-weight: 700; color: var(--sto-text-muted);
  text-transform: uppercase; letter-spacing: .8px;
  margin: 0 0 14px; padding-bottom: 8px;
  border-bottom: 1px solid var(--sto-border);
  display: flex; align-items: center; gap: 6px;
}

/* ── FIELD ───────────────────────────────────────────────────── */
.sto-field { display: flex; flex-direction: column; gap: 5px; }
.sto-field label {
  font-size: 11px; font-weight: 700; color: var(--sto-text-label);
  letter-spacing: .3px; text-transform: uppercase;
}
.sto-field label .req { color: #E53935; margin-left: 2px; }

.sto-input, .sto-select {
  width: 100%; height: 36px; padding: 0 10px;
  font-size: 13px; font-family: var(--font-ui);
  color: var(--sto-text); background: var(--sto-white);
  border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-md);
  outline: none; transition: border-color .15s, box-shadow .15s;
  box-shadow: var(--sto-shadow-sm);
  appearance: none; -webkit-appearance: none;
}
.sto-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center;
  padding-right: 30px;
}
.sto-input:focus, .sto-select:focus {
  border-color: var(--sto-blue-mid);
  box-shadow: 0 0 0 3px rgba(25,118,210,.12);
}
.sto-input[readonly], .sto-input.readonly {
  background: var(--sto-surface); color: var(--sto-text-muted); cursor: default;
}
.sto-select:disabled {
  background-color: var(--sto-surface); color: var(--sto-text-muted);
  cursor: not-allowed; opacity: .7;
}

/* ── REFERENCE INFO BANNER ───────────────────────────────────── */
.sto-ref-banner {
  display: flex; align-items: center; gap: 10px;
  background: var(--sto-blue-light);
  border: 1px solid var(--sto-blue-border);
  border-radius: var(--sto-radius-md);
  padding: 10px 14px;
  font-size: 12px; color: var(--sto-blue);
  margin-bottom: 20px;
}
.sto-ref-banner strong { font-weight: 700; }
.sto-ref-banner.hidden { display: none; }

/* ── TABLE ───────────────────────────────────────────────────── */
.sto-table-wrap {
  border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-lg);
  overflow: hidden;
  margin-bottom: 20px;
}

/* 🔥 FIX UTAMA */
.sto-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  table-layout: fixed; /* ⛔ WAJIB: kunci layout */
}

/* HEADER */
.sto-table thead tr {
  background: var(--sto-blue);
}

.sto-table thead th {
  padding: 10px 12px;
  text-align: left;
  font-size: 11px;
  font-weight: 700;
  color: rgba(255,255,255,.9);
  letter-spacing: .5px;
  text-transform: uppercase;
  white-space: nowrap;
  
}

.sto-table thead th:last-child {
  border-right: none;
}

.sto-table thead th.center {
  text-align: center;
}

/* BODY */
.sto-table tbody tr {
  border-bottom: 1px solid var(--sto-border);
  transition: background .12s;
}

.sto-table tbody tr:last-child {
  border-bottom: none;
}

.sto-table tbody tr:hover {
  background: var(--sto-row-hover);
}

.sto-table tbody tr:nth-child(even) {
  background: #FAFCFF;
}

.sto-table tbody tr:nth-child(even):hover {
  background: var(--sto-row-hover);
}

/* 🔥 FIX TAMBAHAN (biar tidak melar) */
.sto-table th,
.sto-table td {
  padding: 6px 8px;
  vertical-align: middle;

  overflow: hidden;             /* ⛔ cegah melar */
  white-space: nowrap;          /* jangan wrap */
}

.sto-table td.center {
  text-align: center;
}

/* 🔥 INPUT & SELECT HARUS PATUH KOLOM */
.sto-table input,
.sto-table select {
  width: 100%;
  box-sizing: border-box;
  min-width: 0; /* ⛔ penting untuk flex/overflow */
}

/* 🔥 SELECT2 FIX (ini sering bikin rusak layout) */
.select2-container {
  width: 100% !important;
  min-width: 0 !important;
}

/* OPTIONAL: kalau select2 masih bandel */
.select2-selection {
  overflow: hidden;
}

/* ROW NUMBER */
.sto-row-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--sto-blue-light);
  color: var(--sto-blue);
  font-size: 11px;
  font-weight: 700;
}

/* ── Ref badge di row ──────────────────────────────────────── */
.ref-row-badge {
  display: inline-flex; align-items: center; gap: 3px;
  background: #E8F5E9; border: 1px solid #A5D6A7;
  color: var(--sto-green); border-radius: 12px;
  padding: 1px 7px; font-size: 10px; font-weight: 700;
}

.sto-table .sto-input, .sto-table .sto-select {
  height: 32px; font-size: 12px; padding: 0 8px; box-shadow: none;
}
.sto-table .sto-select { padding-right: 26px; background-position: right 6px center; }

/* ── LOCATION TAG ─────────────────────────────────────────── */
.sto-location-tag {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: 20px;
  background: var(--sto-blue-light); border: 1px solid var(--sto-blue-border);
  color: var(--sto-blue); font-size: 11px; font-weight: 700;
}

/* ── WAREHOUSE SELECT IN TH ───────────────────────────────── */
.th-select {
  display: block; margin-top: 6px; width: 100%;
  padding: 4px 8px; font-size: 12px;
  background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
  border-radius: var(--sto-radius-sm); color: #fff; outline: none;
}
.th-select option { background: var(--sto-blue); color: #fff; }

/* ── FOOTER ───────────────────────────────────────────────── */
.sto-footer-row {
  display: grid; grid-template-columns: 1fr auto;
  gap: 24px; align-items: end;
  border-top: 1px solid var(--sto-border); padding-top: 24px;
}
.sto-textarea {
  width: 100%; padding: 10px 12px; font-size: 13px;
  font-family: var(--font-ui); color: var(--sto-text);
  background: var(--sto-white); border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-md); resize: vertical; outline: none;
  transition: border-color .15s, box-shadow .15s; box-shadow: var(--sto-shadow-sm);
}
.sto-textarea:focus {
  border-color: var(--sto-blue-mid); box-shadow: 0 0 0 3px rgba(25,118,210,.12);
}

/* ── BUTTONS ─────────────────────────────────────────────── */
.sto-btn-group { display: flex; gap: 8px; align-items: center; }
.sto-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 0 18px; height: 36px; border-radius: var(--sto-radius-md);
  font-size: 13px; font-weight: 600; font-family: var(--font-ui);
  cursor: pointer; border: 1px solid transparent;
  transition: background .15s, border-color .15s, box-shadow .15s, transform .1s;
  text-decoration: none; white-space: nowrap;
}
.sto-btn:active { transform: scale(.98); }
.sto-btn-ghost {
  background: var(--sto-white); border-color: var(--sto-border); color: var(--sto-text-muted);
}
.sto-btn-ghost:hover { background: var(--sto-surface); border-color: #B0BEC5; color: var(--sto-text); }
.sto-btn-save {
  background: var(--sto-green-mid); border-color: var(--sto-green); color: #fff;
  box-shadow: 0 1px 3px rgba(46,125,50,.3);
}
.sto-btn-save:hover { background: var(--sto-green); box-shadow: 0 2px 6px rgba(46,125,50,.35); }
.sto-btn-add {
  background: var(--sto-white); border-color: var(--sto-border);
  color: var(--sto-blue); font-size: 12px; height: 30px; padding: 0 12px;
}
.sto-btn-add:hover { background: var(--sto-blue-light); border-color: var(--sto-blue-border); }

/* ── HINT ─────────────────────────────────────────────────── */
.sto-hint { font-size: 11px; color: var(--sto-text-muted); margin-top: 2px; }

/* ── SKELETON SHIMMER (loading rows) ──────────────────────── */
@keyframes shimmer {
  0%   { background-position: -400px 0; }
  100% { background-position: 400px 0; }
}
.sto-skeleton {
  background: linear-gradient(90deg, #f0f4ff 25%, #e0eaff 50%, #f0f4ff 75%);
  background-size: 400px 100%;
  animation: shimmer 1.2s infinite;
  border-radius: 4px; height: 20px; display: block;
}

/* ── LOADING OVERLAY for rows ─────────────────────────────── */
#rowsLoadingOverlay {
  display: none; position: absolute; inset: 0;
  background: rgba(255,255,255,.75);
  border-radius: var(--sto-radius-lg);
  z-index: 10; align-items: center; justify-content: center;
}
#rowsLoadingOverlay.active { display: flex; }
.sto-spinner {
  width: 28px; height: 28px; border: 3px solid var(--sto-blue-border);
  border-top-color: var(--sto-blue); border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 639px) {
  .sto-footer-row { grid-template-columns: 1fr; }
  .sto-btn-group  { justify-content: flex-end; }
  .sto-body { padding: 20px 16px; }
  .sto-header { padding: 16px 20px; }
}

/* ── Mode Area: header Address kolom ──────────────────────── */
.sto-table thead .td-addr-col { display: none; }
.sto-table thead .td-qty-col  { }

/* Sinkronkan via JS — class .mode-area di table */
.sto-table.mode-area thead .td-addr-col { display: table-cell; }
.sto-table.mode-area thead .td-qty-col  { display: none; }

/* Address label styling di cell */
.sto-addr-badge {
  display: inline-flex; align-items: center;
  background: var(--sto-blue-light);
  border: 1px solid var(--sto-blue-border);
  border-radius: 12px;
  padding: 2px 8px;
  font-size: 10px;
  font-weight: 700;
  color: var(--sto-blue);
  white-space: nowrap;
}
</style>
@endpush


{{-- ══════════════════════════════════════════════════════════
     DESKTOP LAYOUT
     ══════════════════════════════════════════════════════════ --}}
<div class="pc-container ml-[264px] p-6 min-h-screen sto-wrap">

  {{-- Page Header --}}
  <div class="page-header">
    <div class="page-block flex items-center justify-start lg:justify-between gap-4">
      <div class="page-header-title">
        <h5 class="mb-0 font-medium">@yield('page-title', 'Dashboard')</h5>
      </div>
      <ul class="mb-0 text-xs text-gray-500 flex items-center">
        <li class="flex items-center">
          <a href="{{ url('/') }}" class="text-gray-600 hover:underline">
            <i data-feather="home" class="w-4 h-4"></i>
          </a>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="flex items-center">
          <span>@yield('breadcrumb-item')</span>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="text-gray-800 font-medium">@yield('breadcrumb-active')</li>
      </ul>
    </div>
  </div>

  {{-- ── CARD ── --}}
  <div class="sto-card">

    {{-- Header Bar --}}
    <div class="sto-header">
      <div class="sto-header-left">
        <h1>Electronic Stock Opname (e-STO)</h1>
        <p>Sistem Pencatatan Stock Opname Digital</p>
      </div>
      <div style="display:flex; align-items:center; gap:16px;">
        <div class="sto-logo-wrap">
          <img src="{{ asset('img/logo.png') }}" alt="Company Logo">
        </div>
      </div>
    </div>

    {{-- Body --}}
    <div class="sto-body">

      {{-- ── DOCUMENT REFERENCE SECTION ── --}}
      <p class="sto-section-title">
        <i data-feather="bookmark" class="w-3.5 h-3.5"></i>
        Document Reference
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">

        {{-- Warehouse badge --}}
        <div class="sto-field">
          <label>Warehouse</label>
          <div style="display:flex; align-items:center; height:36px;">
            <span class="sto-location-tag">
              <i data-feather="map-pin" class="w-3 h-3"></i>
              {{ $warehouse ?? 'All Warehouses' }}
            </span>
          </div>
        </div>

        {{-- ── USER INFO ── --}}
<div class="sto-field">
  <label>Verificator</label>
  <div style="display:flex; align-items:center; height:36px;">
    <span class="sto-location-tag">
      <i data-feather="user" class="w-3 h-3"></i>
      {{ auth()->user()->name }}
    </span>
  </div>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
        @if($isChemCons)
          {{-- ── AREA (dari STO Reference Master) ── --}}
          <div class="sto-field">
            <label>Rack</label>
            <select id="area_desktop" name="area" class="sto-select"
              data-warehouse="{{ $warehouse }}">
              <option value="">— Memuat rack... —</option>
            </select>
            <span class="sto-hint">Pilih rack terlebih dahulu</span>
          </div>

          {{-- ── SHELF (load setelah area dipilih) ── --}}
          <div class="sto-field">
            <label>Address</label>
            <select id="shelf_desktop" name="shelf" class="sto-select" disabled>
              <option value="">— Pilih rack dulu —</option>
            </select>
            <span class="sto-hint">Address akan muncul setelah rack dipilih</span>
          </div>

          {{-- STO Number hidden (di-generate dari area+shelf) --}}
          <input type="hidden" name="sto_number" id="sto_number_desktop">

          {{-- Reference ID hidden (untuk load items) --}}
          <input type="hidden" id="ref_master_id_desktop" value="">

        @else
          {{-- ── STO NUMBER (dropdown normal) ── --}}
          <div class="sto-field lg:col-span-2">
            <label>STO Number <span class="req">*</span></label>
            <select name="sto_number" id="sto_number_desktop" class="sto-select">
              @foreach ($ranges as [$start, $end])
                @for ($i = $start; $i <= $end; $i++)
                  @php
                    $number = str_pad($i, 4, '0', STR_PAD_LEFT);
                    $val    = "{$year}/{$month}/{$number}";
                  @endphp
                  @if (!in_array($val, $usedStoNumbers))
                    <option value="{{ $val }}">{{ $val }}</option>
                  @endif
                @endfor
              @endforeach
            </select>
          </div>
        @endif

        {{-- Warehouse selector untuk user tertentu --}}
        @if(in_array(auth()->id(), [53]) && $warehouse === null)
          <div class="sto-field">
            <label>Pilih Gudang</label>
            <select name="warehouse" id="warehouse-null-desktop" class="sto-select">
              <option value="">— Pilih Gudang —</option>
              @foreach($allowedWarehouses as $wh)
                <option value="{{ $wh }}">{{ $wh }}</option>
              @endforeach
            </select>
          </div>
        @endif

      </div>

      {{-- ── REFERENCE INFO BANNER ── --}}
      @if($isChemCons)
      <div id="refBanner" class="sto-ref-banner hidden mb-8">
        <i data-feather="info" class="w-4 h-4" style="flex-shrink:0;"></i>
        <span>
          Referensi: <strong id="refBannerText">—</strong>
          — <span id="refItemCount">0</span> article dari referensi akan dimuat ke tabel.
          Anda tetap bisa menambah baris manual untuk article yang tidak terinput di referensi.
        </span>
      </div>
      @endif

      {{-- ── STOCK ITEMS TABLE ── --}}
      <p class="sto-section-title">
        <i data-feather="list" class="w-3.5 h-3.5"></i>
        Stock Articles
      </p>

      <div style="position:relative;">
        {{-- Loading overlay --}}
        <div id="rowsLoadingOverlay">
          <div style="text-align:center;">
            <div class="sto-spinner" style="margin:0 auto 8px;"></div>
            <span style="font-size:12px; color:var(--sto-text-muted);">Memuat referensi item...</span>
          </div>
        </div>

        <div class="sto-table-wrap">
          <table class="sto-table">
            <thead>
              <tr>
                <th style="width:36px; text-align:center;">#</th>
                <th style="width:120px;">Part Code</th>
                <th>Part Name</th>
                <th class="center td-qty-col" id="th-qty-desktop" style="width:90px;">Qty</th>
                
                <th class="center" style="width:80px;">Packing</th>
                <th class="center" style="width:80px;">UOM</th>
                @if(($warehouse ?? null) === 'Chemical')
                <th class="center" style="width:110px;">Kondisi</th>
                @endif
                <th id="th-addr-desktop" class="center" style="width:90px;">
  Address
</th>
                <th class="center" style="width:160px;">
                  Location
                  
                    <div style="font-size:11px; font-weight:400; opacity:.75; margin-top:2px;">{{ $warehouse }}</div>
                
                </th>
               <th class="center td-action-col" id="th-action-desktop" style="width:70px; display:none;">Action</th>
              </tr>
            </thead>
           <tbody id="article-table">
  @php 
    $defaultRows = 7;
  @endphp

  @for ($i = 0; $i < $defaultRows; $i++)

    @php
      $articleCode = '';
      $articleId   = '';
      $qty         = '';
      $uom         = '';
      $minPackage  = '';
      $location    = ($warehouse ?? '');
      $isRef       = false;
    @endphp

    <tr class="sto-row" data-is-ref="0">
      <input type="hidden" name="articles[{{ $i }}][other_name]" class="other-name-input">

      <td class="center">
        <span class="sto-row-num">{{ $i + 1 }}</span>
      </td>

      <td>
        <input type="text"
          name="articles[{{ $i }}][article_code]"
          value=""
          class="article-code sto-input readonly"
          readonly>
      </td>

      <td>
        <select class="part-select sto-select"
          name="articles[{{ $i }}][article_id]"
          data-row="{{ $i }}">
          <option value="">— Pilih Part —</option>
        </select>
      </td>

     <td class="center td-qty-desktop">
        <input type="number" min="0"
          name="articles[{{ $i }}][qty]"
          value=""
          class="qty-input sto-input"
          style="text-align:center;">
      </td>

    
      <td class="center">
        <input type="text"
          name="articles[{{ $i }}][min_package]"
          value=""
          class="part-min-package sto-input readonly"
          readonly style="text-align:center;">
      </td>

      <td class="center">
        <input type="text"
          name="articles[{{ $i }}][uom]"
          value=""
          class="part-uom sto-input {{ $isChemCons ? '' : 'readonly' }}"
          {{ $isChemCons ? '' : 'readonly' }}
          style="text-align:center;">
      </td>

      {{-- SESUDAH --}}
     @if(($warehouse ?? null) === 'Chemical')
      <td class="center">
        <select name="articles[{{ $i }}][kondisi]"
          class="kondisi-select sto-select"
          style="text-align:center; font-size:11px; height:32px;">
          <option value="">—</option>
          <option value="Utuh">Utuh</option>
          <option value="Tidak Utuh">Tidak Utuh</option>
        </select>
      </td>
      @endif

        <td class="center td-addr-desktop" style=" color:var(--sto-blue); font-size:11px; font-weight:600;">
        <span class="row-addr-label">—</span>
      </td>


      <td class="center">
        <input type="text"
          name="articles[{{ $i }}][location]"
          value="{{ $location }}"
          readonly
          class="location-input sto-input readonly"
          style="text-align:center;">
      </td>

    </tr>

  @endfor
</tbody>
          </table>
        </div>
      </div>
  
      {{-- Add Row --}}
      <div style="margin-bottom:24px;">
        @if($isChemCons)
        <button type="button" id="btnAddRow" class="sto-btn sto-btn-add">
          <i data-feather="plus" class="w-3.5 h-3.5"></i>
          Add Row
        </button>
      
          <button type="button" id="btnClearRef" class="sto-btn sto-btn-ghost"
            style="margin-left:8px; font-size:12px; height:30px; padding:0 12px; display:none;">
            <i data-feather="x" class="w-3.5 h-3.5"></i>
            Reset ke Default
          </button>
        @endif
      </div>

      {{-- ── FOOTER ── --}}
      <div class="sto-footer-row">
        <div class="sto-field">
          <label>Catatan / Note</label>
          <textarea id="note" name="note" rows="3" class="sto-textarea"
            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>
        <div class="sto-btn-group">
          <a href="{{ url()->previous() }}" class="sto-btn sto-btn-ghost">
            <i data-feather="arrow-left" class="w-3.5 h-3.5"></i>
            Back
          </a>
        <button type="submit" id="btnSave" class="sto-btn sto-btn-save" style="display:none;">
    <i data-feather="save" class="w-3.5 h-3.5"></i>
    Save
</button>
        </div>
      </div>

    </div>{{-- /sto-body --}}
  </div>{{-- /sto-card --}}
</div>{{-- /pc-container --}}

