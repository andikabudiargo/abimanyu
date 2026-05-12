{{-- resources/views/suggestion/create.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Create Suggestion System — ASN</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com"></script>
<style>
*, *::before, *::after { box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; background: #f3f4f6; color: #111827; min-height: 100vh; }

.topbar { background: #1e3a5f; height: 52px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
.topbar-brand { display: flex; align-items: center; gap: 10px; }
.topbar-brand img { width: 26px; height: 26px; border-radius: 50%; }
.topbar-brand-text { font-size: 12px; color: rgba(255,255,255,0.65); font-weight: 500; }
.topbar-back { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.65); text-decoration: none; padding: 5px 10px; border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; transition: all 0.15s; }
.topbar-back:hover { color: #fff; border-color: rgba(255,255,255,0.45); }

.c-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; }
.c-card-header { padding: 12px 18px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 8px; }
.c-card-body   { padding: 18px; }
.c-card-footer { padding: 12px 18px; border-top: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; }
.c-section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; }

.btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border-radius: 4px; font-size: 12px; font-weight: 500; border: 1px solid transparent; cursor: pointer; transition: all 0.15s; font-family: 'Inter', sans-serif; white-space: nowrap; text-decoration: none; }
.btn svg { width: 12px; height: 12px; flex-shrink: 0; }
.btn-primary   { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
.btn-primary:hover { background: #162d4a; }
.btn-secondary { background: #fff; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #f9fafb; }
.btn-success   { background: #166534; color: #fff; border-color: #166534; }
.btn-success:hover { background: #14532d; }

.f-label { display: block; font-size: 12px; font-weight: 500; color: #374151; margin-bottom: 5px; }
.f-label sup { color: #dc2626; }
.f-hint  { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.f-input { width: 100%; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; color: #111827; background: #fff; outline: none; transition: border-color 0.15s, box-shadow 0.15s; font-family: 'Inter', sans-serif; line-height: 1.5; }
.f-input:focus { border-color: #1e3a5f; box-shadow: 0 0 0 2px rgba(30,58,95,0.08); }
.f-input::placeholder { color: #9ca3af; }
.f-input.is-error { border-color: #dc2626; }
textarea.f-input { resize: vertical; }
.f-input[disabled] { background: #f9fafb; color: #6b7280; cursor: not-allowed; }

/* Wizard */
.wiz-bar { display: flex; align-items: center; }
.wiz-step { display: flex; flex-direction: column; align-items: center; gap: 5px; min-width: 60px; }
.wiz-num { width: 26px; height: 26px; border-radius: 50%; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; border: 1.5px solid #d1d5db; background: #fff; color: #9ca3af; transition: all 0.2s; }
.wiz-num.is-active { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
.wiz-num.is-done   { background: #166534; border-color: #166534; color: #fff; }
.wiz-title { font-size: 10px; font-weight: 500; color: #9ca3af; text-align: center; letter-spacing: 0.04em; text-transform: uppercase; }
.wiz-title.is-active { color: #1e3a5f; font-weight: 600; }
.wiz-title.is-done   { color: #166534; }
.wiz-line { flex: 1; height: 1px; background: #e5e7eb; margin-bottom: 18px; transition: background 0.2s; min-width: 16px; }
.wiz-line.is-done { background: #166534; }

/* Sidebar progress */
.prog-step { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; }
.prog-dot { width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #e5e7eb; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #9ca3af; flex-shrink: 0; margin-top: 1px; transition: all 0.2s; }
.prog-dot.is-active { border-color: #1e3a5f; background: #1e3a5f; color: #fff; }
.prog-dot.is-done   { border-color: #166534; background: #166534; color: #fff; }
.prog-title { font-size: 12px; font-weight: 500; color: #6b7280; line-height: 1.3; }
.prog-title.is-active { color: #1e3a5f; font-weight: 600; }
.prog-title.is-done   { color: #166534; }
.prog-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.prog-connector { width: 1px; height: 16px; background: #e5e7eb; margin-left: 9px; }
.prog-connector.is-done { background: #166534; }

/* Step panels */
.step-panel { display: none; }
.step-panel.is-active { display: block; animation: panelIn 0.18s ease; }
@keyframes panelIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

/* Category tiles — enterprise, no emoji */
.cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; }

.cat-tile { position: relative; border: 1px solid #e5e7eb; border-radius: 4px; padding: 9px 10px 8px; cursor: pointer; transition: border-color 0.15s, background 0.15s; background: #fff; user-select: none; overflow: hidden; }
.cat-tile input[type=checkbox] { position: absolute; opacity: 0; width: 0; height: 0; }
.cat-tile:hover { border-color: #9ca3af; background: #f9fafb; }
.cat-tile.is-checked { border-color: #1e3a5f; background: #f0f4f9; }

/* Color stripe left side */
.cat-tile::after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }
.cat-tile[data-cat="Safety"]::after      { background: #dc2626; }
.cat-tile[data-cat="Moral"]::after       { background: #7c3aed; }
.cat-tile[data-cat="Quality"]::after     { background: #2563eb; }
.cat-tile[data-cat="Productivity"]::after{ background: #d97706; }
.cat-tile[data-cat="Cost"]::after        { background: #16a34a; }
.cat-tile[data-cat="Environment"]::after { background: #059669; }
.cat-tile[data-cat="Delivery"]::after    { background: #ea580c; }

/* Unchecked stripe is subtle */
.cat-tile:not(.is-checked)::after { opacity: 0.25; }
.cat-tile.is-checked::after { opacity: 1; }

/* Checked background per category */
.cat-tile[data-cat="Safety"].is-checked    { border-color: #fca5a5; background: #fef2f2; }
.cat-tile[data-cat="Moral"].is-checked     { border-color: #c4b5fd; background: #f5f3ff; }
.cat-tile[data-cat="Quality"].is-checked   { border-color: #93c5fd; background: #eff6ff; }
.cat-tile[data-cat="Productivity"].is-checked { border-color: #fcd34d; background: #fffbeb; }
.cat-tile[data-cat="Cost"].is-checked      { border-color: #86efac; background: #f0fdf4; }
.cat-tile[data-cat="Environment"].is-checked { border-color: #6ee7b7; background: #ecfdf5; }
.cat-tile[data-cat="Delivery"].is-checked  { border-color: #fdba74; background: #fff7ed; }

.cat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; padding-left: 5px; }
.cat-abbr { font-size: 9px; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; padding: 2px 5px; border-radius: 3px; background: #f3f4f6; color: #6b7280; transition: all 0.15s; }
.cat-tile.is-checked .cat-abbr { background: rgba(0,0,0,0.07); }
.cat-tile[data-cat="Safety"].is-checked    .cat-abbr { background: #fee2e2; color: #991b1b; }
.cat-tile[data-cat="Moral"].is-checked     .cat-abbr { background: #ede9fe; color: #6d28d9; }
.cat-tile[data-cat="Quality"].is-checked   .cat-abbr { background: #dbeafe; color: #1d4ed8; }
.cat-tile[data-cat="Productivity"].is-checked .cat-abbr { background: #fef3c7; color: #92400e; }
.cat-tile[data-cat="Cost"].is-checked      .cat-abbr { background: #dcfce7; color: #166534; }
.cat-tile[data-cat="Environment"].is-checked .cat-abbr { background: #d1fae5; color: #065f46; }
.cat-tile[data-cat="Delivery"].is-checked  .cat-abbr { background: #ffedd5; color: #c2410c; }

.cat-chk { width: 14px; height: 14px; border-radius: 50%; border: 1px solid #d1d5db; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s; }
.cat-tile.is-checked .cat-chk { border-color: transparent; }
.cat-tile[data-cat="Safety"].is-checked    .cat-chk { background: #dc2626; }
.cat-tile[data-cat="Moral"].is-checked     .cat-chk { background: #7c3aed; }
.cat-tile[data-cat="Quality"].is-checked   .cat-chk { background: #2563eb; }
.cat-tile[data-cat="Productivity"].is-checked .cat-chk { background: #d97706; }
.cat-tile[data-cat="Cost"].is-checked      .cat-chk { background: #16a34a; }
.cat-tile[data-cat="Environment"].is-checked .cat-chk { background: #059669; }
.cat-tile[data-cat="Delivery"].is-checked  .cat-chk { background: #ea580c; }

.cat-name { font-size: 11px; font-weight: 500; color: #374151; padding-left: 5px; }
.cat-tile.is-checked .cat-name { font-weight: 600; }
.cat-desc { font-size: 10px; color: #9ca3af; padding-left: 5px; margin-top: 1px; }

/* Info banners */
.info-banner { border-radius: 4px; padding: 8px 11px; font-size: 11px; display: flex; gap: 7px; align-items: flex-start; }
.info-banner svg { width: 12px; height: 12px; flex-shrink: 0; margin-top: 1px; }
.info-banner.warn    { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; }
.info-banner.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; }

/* Drop zone */
.drop-zone { border: 1px dashed #d1d5db; border-radius: 4px; padding: 20px 16px; text-align: center; cursor: pointer; transition: border-color 0.15s, background 0.15s; background: #fafafa; position: relative; }
.drop-zone:hover, .drop-zone.is-over { border-color: #1e3a5f; background: #f0f4f9; }
.drop-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.dz-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 4px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; background: #fff; }
.dz-icon svg { width: 14px; height: 14px; color: #6b7280; }
.photo-thumbs { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
.photo-thumb { position: relative; width: 60px; height: 60px; border-radius: 4px; overflow: hidden; border: 1px solid #e5e7eb; flex-shrink: 0; }
.photo-thumb img { width: 100%; height: 100%; object-fit: cover; }
.photo-thumb-rm { position: absolute; top: 2px; right: 2px; width: 13px; height: 13px; background: rgba(0,0,0,0.6); border-radius: 2px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #fff; font-size: 7px; font-weight: 700; border: none; }

.lbl-before { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 2px 7px; border-radius: 3px; background: #fee2e2; color: #991b1b; margin-bottom: 6px; }
.lbl-after  { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 2px 7px; border-radius: 3px; background: #dcfce7; color: #166534; margin-bottom: 6px; }

.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 3px; font-size: 11px; font-weight: 500; border: 1px solid #e5e7eb; background: #f9fafb; color: #374151; }
.status-dot { width: 5px; height: 5px; border-radius: 50%; background: #9ca3af; }
.opt-tag { font-size: 10px; font-weight: 600; background: #f3f4f6; color: #6b7280; padding: 2px 7px; border-radius: 3px; }

.g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
.space-y > * + * { margin-top: 14px; }

.err-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 10px 14px; font-size: 12px; color: #7f1d1d; margin-bottom: 12px; }
.err-box ul { margin-top: 5px; padding-left: 14px; }
</style>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>

<nav class="bg-[#1e3a5f] sticky top-0 z-50 shadow-md">
    <div class="max-w-screen-xl mx-auto px-4 h-14 flex items-center justify-between gap-3">

        {{-- Brand --}}
        <a href="{{ route('suggestion.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
            <img src="{{ asset('img/asn-logo-bulat.png') }}" alt="ASN" class="w-7 h-7 rounded-full">
            <div class="">
                <div class="text-white text-xs font-semibold leading-tight">Suggestion System Portal</div>
                <div class="text-white/40 text-[10px] leading-tight">PT. Abimanyu Sekar Nusantara</div>
            </div>
        </a>

       <div class="flex items-center gap-2 shrink-0">

            <a href="{{ route('suggestion.dashboard') }}"
               class="
                    inline-flex items-center justify-center gap-2
                    h-9
                    px-3 sm:px-4
                    rounded-xl
                    bg-white/10
                    hover:bg-white/15
                    border border-white/10
                    text-white
                    text-xs
                    font-medium
                    transition-all
               ">

                <i class="fas fa-arrow-left text-[11px]"></i>

                {{-- Desktop --}}
                <span class="hidden sm:inline">
                    Kembali ke Dashboard
                </span>

            </a>

        </div>
    </div>
</nav>

<div class="flex flex-col lg:flex-row gap-4 px-6 py-4">

    {{-- ══ SIDEBAR ══ --}}
    <div class="w-full lg:w-56 xl:w-60 flex-shrink-0 flex flex-col gap-3">

        <div class="c-card hidden sm:block">
            <div class="c-card-header"><span class="c-section-label">ALUR PENGISIAN</span></div>
            <div class="c-card-body" style="padding:14px 16px;">
                @php
                $sideSteps = [
                    [1,'Identification',  'Kategori, tema, dan latar belakang'],
                    [2,'Root Cause',      'Analisa penyebab masalah'],
                    [3,'Improvement',     'Aktivitas perbaikan & ilustrasi'],
                    [4,'Evaluation',      'Evaluasi & standarisasi'],
                ];
                @endphp
                @foreach($sideSteps as [$n,$title,$sub])
                <div class="prog-step">
                    <div class="prog-dot {{ $n===1?'is-active':'' }}" id="pd{{ $n }}">{{ $n }}</div>
                    <div>
                        <p class="prog-title {{ $n===1?'is-active':'' }}" id="pt{{ $n }}">{{ $title }}</p>
                        <p class="prog-sub">{{ $sub }}</p>
                    </div>
                </div>
                @if($n<4)<div class="prog-connector" id="pc{{ $n }}"></div>@endif
                @endforeach
            </div>
        </div>

       <div class="c-card hidden sm:block" id="selSummary" style="display:none;">
    <div class="c-card-header">
        <span class="c-section-label">Ringkasan</span>
    </div>

    <div class="c-card-body" style="padding:10px 14px;">
        <table style="width:100%; font-size:11px; border-collapse:collapse;">
            
            <!-- Category -->
            <tr id="sumCatRow" style="display:none;">
                <td style="
                    color:#9ca3af;
                    padding:4px 0;
                    width:70px;
                    vertical-align:top;
                ">
                    Kategori
                </td>

                <td style="
                    width:10px;
                    padding:4px 0;
                    vertical-align:top;
                    color:#9ca3af;
                ">
                    :
                </td>

                <td 
                    id="sumCat"
                    style="
                        color:#111827;
                        font-weight:500;
                        padding:4px 0;
                        word-break:break-word;
                        vertical-align:top;
                    "
                >
                    —
                </td>
            </tr>

            <!-- Theme -->
            <tr id="sumThemeRow" style="display:none;">
                <td style="
                    color:#9ca3af;
                    padding:4px 0;
                    vertical-align:top;
                ">
                    Tema
                </td>

                <td style="
                    width:10px;
                    padding:4px 0;
                    vertical-align:top;
                    color:#9ca3af;
                ">
                    :
                </td>

                <td 
                    id="sumTheme"
                    style="
                        color:#111827;
                        font-weight:500;
                        padding:4px 0;
                        word-break:break-word;
                        vertical-align:top;
                    "
                >
                    —
                </td>
            </tr>

        </table>
    </div>
</div>

        @if($activePeriod)
        <div class="c-card">
            <div class="c-card-header"><span class="c-section-label">Periode Pengumpulan</span></div>
            <div class="c-card-body" style="padding:10px 14px;">
                <p style="font-size:12px;font-weight:600;color:#111827;">{{ $activePeriod->name }}</p>
                <p style="font-size:11px;color:#6b7280;margin-top:3px;">Batas Pengumpulan: <strong class="text-red-600">{{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') }}</strong></p>
                @if($activePeriod->max_submissions)
                <p style="font-size:11px;color:#6b7280;margin-top:2px;">Max {{ $activePeriod->max_submissions }} submissions</p>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ══ MAIN ══ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-3">

        {{-- Page header --}}
        <div class="c-card">
            <div style="padding:12px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div>
                    <h1 style="font-size:14px;font-weight:600;color:#111827;margin:0;">Pengajuan Suggestion System (SS)</h1>
                    <p style="font-size:11px;color:#6b7280;margin:3px 0 0;">Register an improvement suggestion for review and evaluation</p>
                </div>
                <span class="status-badge"><span class="status-dot"></span>Draft</span>
            </div>
        </div>

        {{-- Wizard bar --}}
        <div class="c-card hidden sm:block" style="padding:12px 18px;">
            <div class="wiz-bar">
                @php $wsteps=['Identification','Root Cause','Improvement','Evaluation']; @endphp
                @foreach($wsteps as $wi=>$wl)
                <div class="wiz-step">
                    <div class="wiz-num {{ $wi===0?'is-active':'' }}" id="wn{{ $wi+1 }}">{{ $wi+1 }}</div>
                    <span class="wiz-title {{ $wi===0?'is-active':'' }}" id="wt{{ $wi+1 }}">{{ $wl }}</span>
                </div>
                @if($wi<3)<div class="wiz-line" id="wl{{ $wi+1 }}"></div>@endif
                @endforeach
            </div>
        </div>

        @if($errors->any())
        <div class="err-box">
            <strong>Please correct the following errors:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

       <form method="POST"
      action="{{ route('suggestion.draft') }}"
      enctype="multipart/form-data"
      id="ss-form">
    @csrf

        {{-- ══ STEP 1 ══ --}}
        <div class="step-panel is-active" id="step1">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="c-section-label">Rencana Usulan</span>
                </div>
                <div class="c-card-body space-y">

            {{-- Category --}}
<div>

    <label class="f-label">
        Kategori <sup>*</sup>

        <span class="text-[10px] text-slate-400 font-normal normal-case">
            — pilih satu atau beberapa kategori yang sesuai dengan improvement Anda
        </span>
    </label>

    @php
    $cats = [
        ['Safety',       'fa-shield-halved',   'Keselamatan Kerja'],
        ['Moral',        'fa-scale-balanced',  'Etika & Budaya'],
        ['Quality',      'fa-gem',             'Kualitas & Mutu Produk'],
        ['Productivity', 'fa-bolt',            'Efisiensi & Produktivitas'],
        ['Cost',         'fa-wallet',          'Penghematan Biaya'],
        ['Environment',  'fa-leaf',            'Lingkungan Kerja & Relasi Karyawan'],
        ['Delivery',     'fa-truck-fast',      'Kualitas & Ketepatan Pengiriman'],
    ];

    $oldCats = old('categories', []);
    @endphp

    <div
        class="cat-grid grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-4 gap-2 mt-2">

        @foreach($cats as [$cat,$icon,$desc])

        <label
            class="cat-tile
                group
                relative
                overflow-visible
                
                border
                transition-all
                duration-200
                cursor-pointer
                bg-white

                {{ in_array($cat,$oldCats)
                    ? 'is-checked border-blue-500 bg-blue-50 shadow-sm'
                    : 'border-slate-200 hover:border-blue-300'
                }}"
            data-cat="{{ $cat }}">

            <input
                type="checkbox"
                name="categories[]"
                value="{{ $cat }}"
                class="hidden"
                {{ in_array($cat,$oldCats)?'checked':'' }}/>

            <div class="p-2">

                {{-- TOP --}}
                <div class="cat-top flex items-start justify-between gap-1">

                    <div class="min-w-0 flex-1">

                        {{-- MOBILE --}}
                        <div class="flex flex-col items-center justify-center text-center sm:hidden">

                            <div class="
                                w-8 h-8
                                rounded-lg
                                flex items-center justify-center
                                mb-1
                                transition-all

                                {{ in_array($cat,$oldCats)
                                    ? 'bg-blue-500 text-white'
                                    : 'bg-blue-50 text-blue-500'
                                }}
                            ">
                                <i class="fa-solid {{ $icon }} text-[12px]"></i>
                            </div>

                            <div class="
                                text-[9px]
                                font-semibold
                                text-slate-700
                                leading-tight
                            ">
                                {{ $cat }}
                            </div>

                        </div>

                        {{-- DESKTOP --}}
                        <div class="hidden sm:block">

                            <div class="flex items-center gap-2">

                                <div class="
                                    w-8 h-8
                                    rounded-lg
                                    flex items-center justify-center
                                    shrink-0

                                    {{ in_array($cat,$oldCats)
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-blue-50 text-blue-500'
                                    }}
                                ">
                                    <i class="fa-solid {{ $icon }} text-[12px]"></i>
                                </div>

                                <div class="min-w-0">

                                    <div class="
                                        text-[11px]
                                        font-semibold
                                        text-slate-700
                                        leading-tight
                                    ">
                                        {{ $cat }}
                                    </div>

                                    <div class="
                                        text-[9px]
                                        text-slate-400
                                        leading-tight
                                        mt-0.5
                                    ">
                                        {{ $desc }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="
                        cat-chk
                        absolute
                        top-2
                        right-2
                        w-4 h-4
                        rounded-full
                        flex items-center justify-center
                        shrink-0
                        transition-all

                        {{ in_array($cat,$oldCats)
                            ? 'bg-blue-500'
                            : 'border border-slate-300 bg-white'
                        }}
                    ">
                        <svg
                            width="8"
                            height="8"
                            viewBox="0 0 12 12"
                            fill="none"
                            stroke="#fff"
                            stroke-width="2.5">
                            <path d="M2 6l3 3 5-5"/>
                        </svg>
                    </div>

                </div>

            </div>

            {{-- MOBILE TOOLTIP --}}
            <div class="
                sm:hidden
                pointer-events-none
                absolute
                left-1/2
                -translate-x-1/2
                bottom-full
                mb-2
                opacity-0
                group-hover:opacity-100
                group-active:opacity-100
                transition-all
                duration-200
                z-30
            ">

                <div class="
                    px-2 py-1
                    rounded-lg
                    bg-slate-900
                    text-white
                    text-[9px]
                    whitespace-nowrap
                    shadow-lg
                ">
                    {{ $desc }}
                </div>

            </div>

        </label>

        @endforeach

    </div>

    @error('categories')
        <p class="f-hint mt-2 text-red-500">
            {{ $message }}
        </p>
    @enderror

</div>

                    <div class="g2">
                        <div>
                            <label class="f-label">Nama</label>
                            <input type="text" class="f-input" value="{{ $user->name }}" disabled/>
                        </div>
                        <div>
                            <label class="f-label">Departemen</label>
                            <input type="text" class="f-input" value="{{ $user->departments->first()?->name }}" disabled/>
                        </div>
                    </div>

                    <div>
                        <label class="f-label">Tema <sup>*</sup></label>
                        <input type="text" name="theme" id="f-theme" class="f-input {{ $errors->has('theme')?'is-error':'' }}"
                            placeholder="Brief, descriptive title for this improvement suggestion"
                            value="{{ old('theme') }}" maxlength="200"/>
                        @error('theme')<p class="f-hint" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                           <label class="f-label">Tanggal Penemuan <sup>*</sup></label>
<input 
    type="date" 
    name="discovery_date" 
    id="f-date" 
    class="f-input" 
    value="{{ old('discovery_date') }}"
    max="{{ date('Y-m-d') }}"
/>
@error('discovery_date')
    <p class="f-hint" style="color:#dc2626;">{{ $message }}</p>
@enderror
                        </div>
                        <div>
                            <label class="f-label">Lokasi <sup>*</sup></label>
                            <input type="text" name="location" id="f-loc" class="f-input" placeholder="e.g. Warehouse B, QC Line 3" value="{{ old('location') }}"/>
                            @error('location')<p class="f-hint" style="color:#dc2626;">{{ $message }}</p>@enderror
                        </div>
                      
                    </div>

                    <div>
                        <label class="f-label">Latar Belakang Penetapan Tema & Target<sup>*</sup></label>
                        <textarea name="background" id="f-background" rows="4" class="f-input"
                            placeholder="Describe the current situation and context that led to this suggestion...">{{ old('background') }}</textarea>
                        @error('background')<p class="f-hint" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>

                </div>
                <div class="c-card-footer">
                    <span style="font-size:11px;color:#9ca3af;">Step 1 of 4</span>
                    <div style="display:flex;gap:6px;">
                        <!--<button type="button" onclick="saveDraft()" class="btn btn-secondary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Draft
                        </button>-->
                        <button type="button" onclick="goStep(2)" class="btn btn-primary">
                            Continue
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ STEP 2 ══ --}}
        <div class="step-panel" id="step2">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span class="c-section-label">Root Cause Analysis</span>
                </div>
                <div class="c-card-body space-y">
                    <div>
                        <label class="f-label">Analisa Penyebab Masalah <sup>*</sup></label>
                        <p class="f-hint" style="margin-bottom:6px;">Use 5 Why, Fishbone diagram, or other structured analysis. Document each causal chain clearly.</p>
                        <textarea name="root_cause" id="f-rootcause" rows="9" class="f-input"
                            placeholder="Why 1: Reject rate increased &#10; → Because dimensions do not match spec&#10;Why 2: Dimensions off spec &#10; → Because machine calibration drifted&#10;Why 3: Calibration drifted &#10; → Because no periodic check schedule&#10;Why 4: No check schedule &#10; → Because SOP does not define interval&#10;&#10;Root Cause: SOP-PRD-003 lacks calibration frequency definition">{{ old('root_cause') }}</textarea>
                        @error('root_cause')<p class="f-hint" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="c-card-footer">
                    <button type="button" onclick="goStep(1)" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <div style="display:flex;gap:6px;">
                        <!--<button type="button" onclick="saveDraft()" class="btn btn-secondary">Save Draft</button>-->
                        <button type="button" onclick="goStep(3)" class="btn btn-primary">
                            Continue
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ STEP 3 ══ --}}
        <div class="step-panel" id="step3">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="c-section-label">Tindakan Perbaikan</span>
                    <span class="opt-tag" style="margin-left:auto;">Optional</span>
                </div>
                <div class="c-card-body space-y">
                    <div>
                        <label class="f-label">Aktivitas Perbaikan</label>
                        <p class="f-hint" style="margin-bottom:6px;">Detail the actions performed or planned. Include dates, responsible parties, and outcomes.</p>
                        <textarea name="improvement_activity" rows="6" class="f-input"
                            placeholder="1. [Date] Action taken — Person responsible — Outcome&#10;2. [Date] Second action...">{{ old('improvement_activity') }}</textarea>
                    </div>
                </div>

                <div style="border-top:1px solid #f3f4f6;padding:16px 18px;">
                    <p class="c-section-label" style="margin-bottom:12px;">Ilustrasi</p>
                    <div class="g2">
                        <div>
                            <span class="lbl-before">Sebelum Perbaikan</span>
                            <div class="drop-zone" id="zone-before"
                                ondragover="event.preventDefault();this.classList.add('is-over')"
                                ondragleave="this.classList.remove('is-over')"
                                ondrop="handleDrop(event,'before')">
                                <input type="file" name="photos_before[]" id="file-before" multiple accept="image/*" onchange="previewPhotos(this,'thumbs-before')"/>
                                <div class="dz-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></div>
                                <p style="font-size:11px;font-weight:500;color:#374151;">Click or drag to upload</p>
                                <p style="font-size:10px;color:#9ca3af;margin-top:3px;">JPG, PNG, WebP &mdash; max 5 MB each</p>
                            </div>
                            <div class="photo-thumbs" id="thumbs-before"></div>
                        </div>
                        <div>
                            <span class="lbl-after">Sesudah Perbaikan</span>
                            <div class="drop-zone" id="zone-after"
                                ondragover="event.preventDefault();this.classList.add('is-over')"
                                ondragleave="this.classList.remove('is-over')"
                                ondrop="handleDrop(event,'after')">
                                <input type="file" name="photos_after[]" id="file-after" multiple accept="image/*" onchange="previewPhotos(this,'thumbs-after')"/>
                                <div class="dz-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></div>
                                <p style="font-size:11px;font-weight:500;color:#374151;">Click or drag to upload</p>
                                <p style="font-size:10px;color:#9ca3af;margin-top:3px;">JPG, PNG, WebP &mdash; max 5 MB each</p>
                            </div>
                            <div class="photo-thumbs" id="thumbs-after"></div>
                        </div>
                    </div>
                </div>

                <div class="c-card-footer">
                    <button type="button" onclick="goStep(2)" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <div style="display:flex;gap:6px;">
                       <!-- <button type="button" onclick="saveDraft()" class="btn btn-secondary">Save Draft</button>-->
                        <button type="button" onclick="goStep(4)" class="btn btn-primary">
                            Continue
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ STEP 4 ══ --}}
        <div class="step-panel" id="step4">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="c-section-label">Evaluasi & Standarisasi</span>
                    <span class="opt-tag" style="margin-left:auto;">Optional</span>
                </div>
                <div class="c-card-body space-y">
                    <div class="info-banner success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Completing this section improves your evaluation score. Quantitative before/after comparisons are weighted heavily by managers.</span>
                    </div>
                    <div>
                        <label class="f-label">Evaluasi Hasil</label>
                        <textarea name="evaluation_result" rows="5" class="f-input"
                            placeholder="Compare before and after conditions with quantitative data where possible.&#10;&#10;Example: Reject rate decreased from 8.2% to 2.3% over 3 months post-implementation.">{{ old('evaluation_result') }}</textarea>
                    </div>
                    <div>
                        <label class="f-label">Standarisasi</label>
                        <input type="text" name="standardization" class="f-input"
                            placeholder="e.g. SOP-QC-001 Rev.2, IK-PRD-015, Form FM-QC-008"
                            value="{{ old('standardization') }}"/>
                        <p class="f-hint">Reference the SOP, work instruction, or form number created as a result of this improvement.</p>
                    </div>
                </div>
                <div class="c-card-footer">
                    <button type="button" onclick="goStep(3)" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <div style="display:flex;gap:6px;">
                        <!--<button type="button" onclick="saveDraft()" class="btn btn-secondary">Save Draft</button>-->
                        <button type="button"  onclick="submitSS()" id="btn-submit" class="btn btn-success">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Submit SS
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStep = 1;
const TOTAL = 4;


document.addEventListener('DOMContentLoaded', function () {

    // checkbox category
    document.querySelectorAll('.cat-tile input').forEach(cb => {
        cb.addEventListener('change', function () {
            const tile = this.closest('.cat-tile');
            tile.classList.toggle('is-checked', this.checked);
            updateSummary();
        });
    });

    // theme realtime
    const themeInput = document.getElementById('f-theme');

    if (themeInput) {
        themeInput.addEventListener('input', function () {
            updateSummary();
        });
    }

    // initial load (old value)
    updateSummary();

});

function goStep(n) {
    if (n > currentStep && !validateStep(currentStep)) return;
    document.getElementById('step' + currentStep).classList.remove('is-active');
    document.getElementById('step' + n).classList.add('is-active');
    updateWizard(n); updateSidebar(n); updateSummary();
    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const CHECKSVG = '<svg width="8" height="8" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5"><path d="M2 6l3 3 5-5"/></svg>';

function updateWizard(active) {
    for (let i = 1; i <= TOTAL; i++) {
        const num = document.getElementById('wn' + i);
        const lbl = document.getElementById('wt' + i);
        num.className = 'wiz-num'; lbl.className = 'wiz-title';
        if (i < active)       { num.classList.add('is-done'); num.innerHTML = CHECKSVG; }
        else if (i === active){ num.classList.add('is-active'); num.textContent = i; lbl.classList.add('is-active'); }
        else                  { num.textContent = i; }
        if (i < TOTAL) document.getElementById('wl' + i).className = 'wiz-line' + (i < active ? ' is-done' : '');
    }
}

function updateSidebar(active) {
    for (let i = 1; i <= TOTAL; i++) {
        const dot = document.getElementById('pd' + i);
        const ttl = document.getElementById('pt' + i);
        dot.className = 'prog-dot'; ttl.className = 'prog-title';
        if (i < active)       { dot.classList.add('is-done'); dot.innerHTML = CHECKSVG; }
        else if (i === active){ dot.classList.add('is-active'); dot.textContent = i; ttl.classList.add('is-active'); }
        else                  { dot.textContent = i; }
        if (i < TOTAL) document.getElementById('pc' + i).className = 'prog-connector' + (i < active ? ' is-done' : '');
    }
}

function updateSummary() {
    const cats  = [...document.querySelectorAll('input[name="categories[]"]:checked')].map(c => c.value);
    const theme = document.getElementById('f-theme')?.value?.trim();
    let any = false;
    if (cats.length) { document.getElementById('sumCat').textContent = cats.join(', '); document.getElementById('sumCatRow').style.display = 'table-row'; any = true; }
    else { document.getElementById('sumCatRow').style.display = 'none'; }
    if (theme) { document.getElementById('sumTheme').textContent = theme.length > 32 ? theme.slice(0,32)+'…' : theme; document.getElementById('sumThemeRow').style.display = 'table-row'; any = true; }
    else { document.getElementById('sumThemeRow').style.display = 'none'; }
    document.getElementById('selSummary').style.display = any ? 'block' : 'none';
}

function validateStep(step) {
    if (step === 1) {
        if (!document.querySelectorAll('input[name="categories[]"]:checked').length) {
            alert('Please select at least one category.'); return false;
        }
        const checks = [['f-theme','Theme / Title is required.'],['f-date','Discovery date is required.'],['f-loc','Location is required.'],['f-background','Background / Current condition is required.']];
        for (const [id, msg] of checks) {
            const el = document.getElementById(id);
            if (el && !el.value.trim()) { el.classList.add('is-error'); el.focus(); el.addEventListener('input', () => el.classList.remove('is-error'), {once:true}); alert(msg); return false; }
        }
    }
    if (step === 2) {
        const rc = document.getElementById('f-rootcause');
        if (!rc.value.trim()) { rc.classList.add('is-error'); rc.focus(); rc.addEventListener('input', () => rc.classList.remove('is-error'), {once:true}); alert('Root cause analysis is required.'); return false; }
    }
    return true;
}

function showToast(message, type = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type, // success | error | warning | info
        title: message,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });
}

  function saveDraft() {
    const form = document.getElementById('ss-form');
    form.action = "{{ route('suggestion.draft') }}";
    form.submit();
}

async function submitSS() {
    if (!validateStep(3)) {
        if (currentStep > 3) goStep(3);
        return;
    }

    const form = document.getElementById('ss-form');
    const btn  = document.getElementById('btn-submit');

    // 🔒 disable tombol + loading
    btn.disabled = true;
    btn.innerHTML = 'Submitting...';

    try {
        const formData = new FormData(form);

        const response = await fetch("{{ route('suggestion.submit') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        });

        const res = await response.json();

        if (response.ok && res.success) {

            showToast(res.message, 'success');

            setTimeout(() => {
                window.location.href = res.redirect;
            }, 1200);

        } else {
            showToast(res.message || 'Gagal submit', 'error');

            btn.disabled = false;
            btn.innerHTML = 'Submit';
        }

    } catch (err) {
        showToast('Terjadi kesalahan sistem', 'error');

        btn.disabled = false;
        btn.innerHTML = 'Submit';
    }
}

function previewPhotos(input, thumbsId) {
    const container = document.getElementById(thumbsId);
    Array.from(input.files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'photo-thumb';
            div.innerHTML = `<img src="${e.target.result}"/><button type="button" class="photo-thumb-rm" onclick="this.parentElement.remove()">✕</button>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function handleDrop(e, type) {
    e.preventDefault();
    document.getElementById('zone-' + type).classList.remove('is-over');
    const input = document.getElementById('file-' + type);
    const dt = new DataTransfer();
    Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
    input.files = dt.files;
    previewPhotos(input, 'thumbs-' + type);
}

updateSummary();
@if($errors->has('root_cause')) goStep(2); @endif
</script>
</body>
</html>