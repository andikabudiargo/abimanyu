@extends('layouts.app')

@section('title', 'Create Document')
@section('page-title', 'CREATE DOCUMENT')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Create Document')

@section('content')

<style>
/* ── Reset & Base ── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Wizard Step Indicator ── */
.wiz-bar {
    display: flex;
    align-items: center;
    padding: 0 4px;
}
.wiz-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    min-width: 72px;
}
.wiz-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    font-size: 11px; font-weight: 600;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid #d1d5db;
    background: #fff;
    color: #9ca3af;
    transition: all .25s ease;
    letter-spacing: 0;
}
.wiz-num.is-active {
    background: #1e3a5f;
    border-color: #1e3a5f;
    color: #fff;
}
.wiz-num.is-done {
    background: #166534;
    border-color: #166534;
    color: #fff;
}
.wiz-num.is-done::after { content: '✓'; }
.wiz-title {
    font-size: 10px;
    font-weight: 500;
    color: #9ca3af;
    text-align: center;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    transition: color .25s;
}
.wiz-title.is-active { color: #1e3a5f; }
.wiz-title.is-done   { color: #166534; }

.wiz-line {
    flex: 1;
    height: 1px;
    background: #e5e7eb;
    margin-bottom: 18px;
    transition: background .25s;
    min-width: 20px;
}
.wiz-line.is-done { background: #166534; }

/* ── Step Panels ── */
.step-panel { display: none; }
.step-panel.is-active {
    display: block;
    animation: panelIn .22s ease;
}
@keyframes panelIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Card Shell ── */
.c-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
}
.c-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}
.c-card-body { padding: 20px; }
.c-card-footer {
    padding: 14px 20px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.c-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6b7280;
}

/* ── Form Controls ── */
.f-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 5px;
}
.f-label sup { color: #dc2626; }

.f-input {
    width: 100%;
    padding: 8px 11px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 13px;
    color: #111827;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    line-height: 1.4;
}
.f-input:focus {
    border-color: #1e3a5f;
    box-shadow: 0 0 0 2px rgba(30,58,95,.10);
}
.f-input::placeholder { color: #9ca3af; }
.f-input.is-error { border-color: #dc2626; box-shadow: 0 0 0 2px rgba(220,38,38,.08); }

textarea.f-input { resize: vertical; }

/* ── Type Selector Tiles ── */
.type-tile {
    position: relative;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    padding: 10px 12px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    user-select: none;
}
.type-tile:hover { border-color: #9ca3af; background: #f9fafb; }
.type-tile.is-selected {
    border-color: #1e3a5f;
    background: #f0f4f9;
}
.type-tile input[type="radio"] { display: none; }
.type-tile-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    border: 1.5px solid #d1d5db;
    flex-shrink: 0;
    transition: all .15s;
}
.type-tile.is-selected .type-tile-dot {
    background: #1e3a5f;
    border-color: #1e3a5f;
}
.type-tile-label {
    font-size: 12px;
    font-weight: 500;
    color: #374151;
}
.type-tile.is-selected .type-tile-label { color: #1e3a5f; }

/* ── Submission Pills ── */
.sub-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    background: #fff;
    transition: all .15s;
    user-select: none;
}
.sub-pill:hover { background: #f9fafb; border-color: #9ca3af; }
.sub-pill input[type="radio"] { display: none; }

.sub-pill.new.is-selected  { border-color: #166534; background: #f0fdf4; color: #14532d; }
.sub-pill.rev.is-selected  { border-color: #92400e; background: #fffbeb; color: #78350f; }
.sub-pill.obs.is-selected  { border-color: #991b1b; background: #fef2f2; color: #7f1d1d; }

.sub-pill-indicator {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #d1d5db;
    flex-shrink: 0;
    transition: background .15s;
}
.sub-pill.new.is-selected .sub-pill-indicator { background: #166534; }
.sub-pill.rev.is-selected .sub-pill-indicator { background: #92400e; }
.sub-pill.obs.is-selected .sub-pill-indicator { background: #991b1b; }

/* ── File Drop Zone ── */
.drop-zone {
    border: 1px dashed #d1d5db;
    border-radius: 4px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #fafafa;
    position: relative;
}
.drop-zone:hover, .drop-zone.is-over { border-color: #1e3a5f; background: #f0f4f9; }
.drop-zone input[type="file"] { display: none; }
.drop-zone-icon {
    width: 36px; height: 36px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    margin: 0 auto 10px;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    color: #6b7280;
}

/* ── Progress Sidebar ── */
.prog-step {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
}
.prog-dot {
    width: 20px; height: 20px;
    border-radius: 50%;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700;
    color: #9ca3af;
    flex-shrink: 0;
    margin-top: 1px;
    transition: all .2s;
}
.prog-dot.is-active { border-color: #1e3a5f; background: #1e3a5f; color: #fff; }
.prog-dot.is-done   { border-color: #166534; background: #166534; color: #fff; }
.prog-dot.is-done::after { content: '✓'; }
.prog-title { font-size: 12px; font-weight: 500; color: #6b7280; line-height: 1.3; }
.prog-title.is-active { color: #1e3a5f; font-weight: 600; }
.prog-title.is-done   { color: #166534; }
.prog-sub { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.prog-connector {
    width: 1px; height: 18px;
    background: #e5e7eb;
    margin-left: 9px;
    transition: background .2s;
}
.prog-connector.is-done { background: #166534; }

/* ── Review Table ── */
.review-table { width: 100%; }
.review-table td { padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
.review-table tr:last-child td { border-bottom: none; }
.review-table .r-key { color: #6b7280; width: 160px; vertical-align: top; padding-right: 12px; }
.review-table .r-val { color: #111827; font-weight: 500; }

/* ── Buttons ── */
.btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 16px;
    border-radius: 4px;
    font-size: 12px; font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.btn-primary   { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
.btn-primary:hover { background: #162d4a; }
.btn-secondary { background: #fff; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #f9fafb; }
.btn-success   { background: #166534; color: #fff; border-color: #166534; }
.btn-success:hover { background: #14532d; }
.btn-success:disabled { opacity: .55; cursor: not-allowed; }
.btn-danger    { background: #fff; color: #991b1b; border-color: #fca5a5; }
.btn-danger:hover { background: #fef2f2; }
.btn svg { width: 13px; height: 13px; flex-shrink: 0; }

/* ── Info Banner ── */
.info-banner {
    border-radius: 4px;
    padding: 9px 12px;
    font-size: 12px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}
.info-banner svg { width: 13px; height: 13px; flex-shrink: 0; margin-top: 1px; }
.info-banner.info  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.info-banner.warn  { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; }
.info-banner.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; }
.info-banner.danger{ background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }

/* ── File Preview ── */
.file-preview-row {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 11px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    background: #fafafa;
}
.file-ext-badge {
    font-size: 9px; font-weight: 700; letter-spacing: .04em;
    padding: 3px 6px;
    border-radius: 3px;
    background: #e5e7eb; color: #374151;
    flex-shrink: 0;
    text-transform: uppercase;
}
.file-ext-badge.xlsx { background: #d1fae5; color: #065f46; }
.file-ext-badge.pdf  { background: #fee2e2; color: #7f1d1d; }
.file-ext-badge.docx { background: #dbeafe; color: #1e3a8a; }

/* ── Distribution Table ── */
.dist-table { width: 100%; border-collapse: collapse; }
.dist-table th {
    text-align: left;
    font-size: 10px; font-weight: 600;
    letter-spacing: .06em; text-transform: uppercase;
    color: #6b7280;
    padding: 8px 10px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}
.dist-table td {
    padding: 6px 10px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 12px;
    vertical-align: middle;
}
.dist-table tr:last-child td { border-bottom: none; }
.dist-table tr:hover td { background: #f9fafb; }

/* ── Select2 ── */
.select2-container { width: 100% !important; }
.select2-container .select2-selection--single {
    height: 34px !important;
    display: flex !important; align-items: center !important;
    border: 1px solid #d1d5db !important;
    border-radius: 4px !important;
    padding: 0 10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 0 !important; line-height: 34px !important;
    font-size: 13px; color: #111827;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 34px !important; right: 8px;
}
.select2-container--focus .select2-selection--single,
.select2-container--open .select2-selection--single {
    border-color: #1e3a5f !important;
    box-shadow: 0 0 0 2px rgba(30,58,95,.10) !important;
}

/* ── Status badge ── */
.status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 3px;
    font-size: 11px; font-weight: 500;
    border: 1px solid #e5e7eb;
    background: #f9fafb; color: #374151;
}
.status-dot {
    width: 5px; height: 5px;
    border-radius: 50%; background: #9ca3af;
}

@media (max-width: 767px) {
    .wiz-title { display: none; }
    .r-key { width: 110px; }
}
</style>

<form id="doc-form" enctype="multipart/form-data">
@csrf

<div class="flex flex-col lg:flex-row gap-4">

    {{-- ═══════ SIDEBAR ═══════ --}}
    <div class="w-full lg:w-64 xl:w-72 flex-shrink-0 flex flex-col gap-4">

        {{-- Progress Tracker --}}
        <div class="c-card">
            <div class="c-card-header">
                <span class="c-section-label">Submission Progress</span>
            </div>
            <div class="c-card-body" style="padding: 16px 20px;">
                <div id="progressTracker" class="flex flex-col gap-0">

                    <div class="prog-step">
                        <div class="prog-dot is-active" id="pd1">1</div>
                        <div>
                            <p class="prog-title is-active" id="pt1">Type &amp; Submission</p>
                            <p class="prog-sub">Document category</p>
                        </div>
                    </div>
                    <div class="prog-connector" id="pc1"></div>

                    <div class="prog-step">
                        <div class="prog-dot" id="pd2">2</div>
                        <div>
                            <p class="prog-title" id="pt2">Document Details</p>
                            <p class="prog-sub">Number, title, reason</p>
                        </div>
                    </div>
                    <div class="prog-connector" id="pc2"></div>

                    <div class="prog-step">
                        <div class="prog-dot" id="pd3">3</div>
                        <div>
                            <p class="prog-title" id="pt3">File Upload</p>
                            <p class="prog-sub">Attach document files</p>
                        </div>
                    </div>
                    <div class="prog-connector" id="pc3"></div>

                    <div class="prog-step">
                        <div class="prog-dot" id="pd4">4</div>
                        <div>
                            <p class="prog-title" id="pt4">Distribution &amp; Review</p>
                            <p class="prog-sub">Copy recipients, confirm</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Department Destination --}}
        <div class="c-card">
            <div class="c-card-header">
                <span class="c-section-label">Department Destination</span>
            </div>
            <div class="c-card-body">
                <label class="f-label">Department <sup>*</sup></label>
                <select id="department" name="department_id" required class="f-input" style="padding:7px 10px;">
                    <option value="">— Select —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                <div id="deptInfo" class="hidden mt-3 p-2 border border-gray-200 rounded bg-gray-50 flex items-center gap-2">
                    <div id="deptAvatar" class="w-7 h-7 rounded-sm flex items-center justify-center text-xs font-bold flex-shrink-0 bg-blue-900 text-white"></div>
                    <span id="deptLabel" class="text-xs font-medium text-gray-700 truncate"></span>
                </div>
            </div>
        </div>

        {{-- Template Downloads --}}
        <div class="c-card">
            <div class="c-card-header">
                <span class="c-section-label">Document Templates</span>
            </div>
            <div class="c-card-body" style="padding: 12px 16px;">
                <p class="text-xs text-gray-500 mb-3">Download the correct template before uploading.</p>
                @foreach([
                    ['BLANK FORM.xlsx',                            'Form Template'],
                    ['BLANK SOP.xlsx',                             'SOP Template'],
                    ['BLANK IK.xlsx',                              'Work Instr. (General)'],
                    ['BLANK IK FOR PRODUKSI DAN QUALITY.xlsx',     'Work Instr. (Prod/QC)'],
                ] as [$file, $label])
                <a href="{{ asset('blank/' . $file) }}" download
                   class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:text-blue-900 group transition">
                    <span class="text-xs text-gray-600 group-hover:text-blue-900">{{ $label }}</span>
                    <svg class="w-3 h-3 text-gray-400 group-hover:text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ═══════ MAIN PANEL ═══════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-4">

        {{-- Page Header --}}
        <div class="c-card">
            <div style="padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h1 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">Document Registration</h1>
                    <p style="font-size: 12px; color: #6b7280; margin: 3px 0 0;">Register new document or update an existing document</p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-badge">
                        <span class="status-dot"></span>
                        Draft
                    </span>
                    <a href="{{ route('mr.doc.index') }}" class="btn btn-secondary" style="padding:5px 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Wizard Bar --}}
        <div class="c-card" style="padding: 14px 20px;">
            <div class="wiz-bar">
                <div class="wiz-step">
                    <div class="wiz-num is-active" id="wn1">1</div>
                    <span class="wiz-title is-active" id="wt1">Type</span>
                </div>
                <div class="wiz-line" id="wl1"></div>
                <div class="wiz-step">
                    <div class="wiz-num" id="wn2">2</div>
                    <span class="wiz-title" id="wt2">Details</span>
                </div>
                <div class="wiz-line" id="wl2"></div>
                <div class="wiz-step">
                    <div class="wiz-num" id="wn3">3</div>
                    <span class="wiz-title" id="wt3">Files</span>
                </div>
                <div class="wiz-line" id="wl3"></div>
                <div class="wiz-step">
                    <div class="wiz-num" id="wn4">4</div>
                    <span class="wiz-title" id="wt4">Review</span>
                </div>
            </div>
        </div>

        {{-- ─── STEP 1: Type & Submission ─── --}}
        <div class="step-panel is-active" id="step1">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="c-section-label">Document Type</span>
                </div>
                
                <div class="c-card-body">
                    <label class="f-label mb-3">Select document category <sup>*</sup></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="typeTiles">
                        @foreach([
                            ['Form',             'FM'],
                            ['Work Instructions','WI'],
                            ['Standard',         'STD'],
                            ['SOP',              'SOP'],
                            ['other',            '...'],
                        ] as [$val, $abbr])
                        <label class="type-tile {{ $loop->first ? 'is-selected' : '' }}">
                            <input type="radio" name="document_type" value="{{ $val }}" class="docType" {{ $loop->first ? 'checked' : '' }}>
                            <span class="type-tile-dot"></span>
                            <span class="type-tile-label">{{ $val === 'other' ? 'Other' : $val }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div id="otherTypeWrap" class="hidden mt-3">
                        <label class="f-label">Specify Type <sup>*</sup></label>
                        <input type="text" name="document_type_other" id="otherInput"
                               placeholder="e.g. Manual Instruction, Module, Guideline..."
                               class="f-input">
                    </div>
                </div>

                <div style="border-top: 1px solid #f3f4f6; padding: 16px 20px;">
                    <p class="c-section-label mb-3">Submission Type <sup style="color:#dc2626">*</sup></p>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <label class="sub-pill new is-selected">
                            <input type="radio" name="submission_type" value="New Release" checked>
                            <span class="sub-pill-indicator"></span>
                            New Release
                        </label>
                        <label class="sub-pill rev">
                            <input type="radio" name="submission_type" value="Revision">
                            <span class="sub-pill-indicator"></span>
                            Revision
                        </label>
                        <label class="sub-pill obs">
                            <input type="radio" name="submission_type" value="Obsolete">
                            <span class="sub-pill-indicator"></span>
                            Obsolete
                        </label>
                    </div>
                    <div id="subInfoBanner" class="info-banner success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="subInfoText">A new document will be registered for the first time in the system.</span>
                    </div>
                </div>

                <div class="c-card-footer">
                    <span class="text-xs text-gray-400">Step 1 of 4</span>
                    <button type="button" class="btn btn-primary" onclick="goStep(2)">
                        Continue
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── STEP 2: Document Details ─── --}}
        <div class="step-panel" id="step2">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="c-section-label">Document Details</span>
                </div>
                <div class="c-card-body space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="f-label">Document Number <sup>*</sup></label>
                            <input type="text" id="doc_number_input" name="document_number"
                                   placeholder="e.g. FM-HRD-001" class="f-input">
                            <select id="doc_number_select" name="document_number_select"
                                    class="f-input hidden" style="padding:7px 10px; margin-top:0;">
                                <option value="">— Select Document —</option>
                            </select>
                            <p id="last_doc_info" class="hidden text-xs text-gray-400 mt-1.5">
                                Last registered: <span id="last_doc_value" class="font-medium text-gray-600">—</span>
                            </p>
                        </div>

                        <div id="revision_group" class="hidden">
                            <label class="f-label">Revision No.</label>
                            <input type="text" name="revision_number" placeholder="e.g. 02" class="f-input">
                        </div>
                    </div>

                    <div>
                        <label class="f-label">Document Title <sup>*</sup></label>
                        <input type="text" name="document_title" id="titleInput"
                               placeholder="Enter a clear, descriptive title" class="f-input" maxlength="120">
                        <p class="text-xs text-gray-400 mt-1"><span id="titleCount">0</span> / 120 characters</p>
                    </div>

                    <div>
                        <label class="f-label">Reason for Submission</label>
                        <textarea name="reason" rows="3" placeholder="Describe the purpose or justification for this submission..." class="f-input"></textarea>
                    </div>

                    <div id="changes_group" class="hidden">
                        <div class="info-banner warn mb-3">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Revision submissions require documentation of what changed.</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="f-label" style="color:#991b1b;">Before Changes</label>
                                <textarea name="before_change" rows="4"
                                          placeholder="Describe the previous content or procedure..."
                                          class="f-input" style="background:#fef2f2;"></textarea>
                            </div>
                            <div>
                                <label class="f-label" style="color:#166534;">After Changes</label>
                                <textarea name="after_change" rows="4"
                                          placeholder="Describe the new content or procedure..."
                                          class="f-input" style="background:#f0fdf4;"></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 4px; background: #fafafa; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <p class="text-xs font-medium text-gray-700">4M Attachment Required?</p>
                            <p class="text-xs text-gray-400 mt-0.5">Man, Machine, Material, Method analysis</p>
                        </div>
                        <div style="display: flex; gap: 16px;">
                            <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                                <input type="radio" name="need_4m" value=0 checked> No
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                                <input type="radio" name="need_4m" value=1> Yes
                            </label>
                        </div>
                    </div>

                </div>
                <div class="c-card-footer">
                    <button type="button" class="btn btn-secondary" onclick="goStep(1)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goStep(3)">
                        Continue
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── STEP 3: File Upload ─── --}}
        <div class="step-panel" id="step3">
            <div class="c-card">
                <div class="c-card-header">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="c-section-label">File Upload</span>
                </div>
                <div class="c-card-body space-y-4">

                    <div>
                        <label class="f-label">Document File <sup>*</sup></label>
                        <div class="drop-zone" id="mainZone" onclick="document.getElementById('file').click()">
                            <input type="file" name="file_path" id="file" accept=".xlsx,.pdf,.docx">
                            <div id="mainPlaceholder">
                                <div class="drop-zone-icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium text-gray-600">Click to select file or drag and drop</p>
                                <p class="text-xs text-gray-400 mt-1">PDF, XLSX, DOCX — maximum 5 MB</p>
                            </div>
                            <div id="mainPreview" class="hidden file-preview-row" style="text-align:left;" onclick="event.stopPropagation()">
                                <span class="file-ext-badge" id="mainExt"></span>
                                <div style="flex:1; min-width:0;">
                                    <p class="text-xs font-medium text-gray-800 truncate" id="mainName"></p>
                                    <p class="text-xs text-gray-400" id="mainSize"></p>
                                </div>
                                <button type="button" onclick="clearFile('main')"
                                        class="text-xs text-gray-400 hover:text-red-600 transition border-0 bg-transparent cursor-pointer">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div id="file_4m_group" class="hidden">
                        <label class="f-label">4M Attachment File <sup>*</sup></label>
                        <div class="drop-zone" id="fm4Zone" onclick="document.getElementById('file_4m').click()">
                            <input type="file" name="file_4m_path" id="file_4m" accept=".xlsx,.pdf,.docx">
                            <div id="fm4Placeholder">
                                <div class="drop-zone-icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium text-gray-600">Click to select 4M attachment</p>
                                <p class="text-xs text-gray-400 mt-1">PDF, XLSX, DOCX — maximum 5 MB</p>
                            </div>
                            <div id="fm4Preview" class="hidden file-preview-row" style="text-align:left;" onclick="event.stopPropagation()">
                                <span class="file-ext-badge" id="fm4Ext"></span>
                                <div style="flex:1; min-width:0;">
                                    <p class="text-xs font-medium text-gray-800 truncate" id="fm4Name"></p>
                                    <p class="text-xs text-gray-400" id="fm4Size"></p>
                                </div>
                                <button type="button" onclick="clearFile('fm4')"
                                        class="text-xs text-gray-400 hover:text-red-600 transition border-0 bg-transparent cursor-pointer">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="info-banner info">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Ensure the file follows the correct template structure. Download templates from the sidebar before uploading.</span>
                    </div>

                </div>
                <div class="c-card-footer">
                    <button type="button" class="btn btn-secondary" onclick="goStep(2)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goStep(4)">
                        Continue
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── STEP 4: Distribution & Review ─── --}}
        <div class="step-panel" id="step4">

            {{-- Copy Distribution --}}
            <div class="c-card mb-4">
                <div class="c-card-header" style="justify-content: space-between;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="c-section-label">Copy Distribution</span>
                    </div>
                    <button type="button" id="addDeptBtn"
                            class="btn btn-secondary" style="padding: 4px 10px; font-size: 11px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Row
                    </button>
                </div>
                <div class="c-card-body" style="padding: 0;">
                    <table class="dist-table">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Department</th>
                                <th style="width: 25%;">Qty (sheets)</th>
                                <th style="width: 25%; text-align:right; padding-right: 16px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="distTableBody">
                            <tr class="dist-row">
                                <td>
                                    <select name="share_dept[]" class="f-input" style="padding:6px 10px;">
                                        <option value="">— Select Department —</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="share_qty[]" min="1" value="1"
                                           class="f-input" style="padding:6px 10px; max-width: 80px;">
                                </td>
                                <td style="text-align: right; padding-right: 16px;">
                                    <button type="button" class="removeDistRow btn btn-danger"
                                            style="padding: 4px 10px; font-size: 11px; opacity: .4; pointer-events: none;">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Review Summary --}}
            <div class="c-card mb-4">
                <div class="c-card-header">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="c-section-label">Review &amp; Confirm</span>
                </div>
                <div class="c-card-body">
                    <table class="review-table" id="reviewTable">
                        {{-- Populated by JS --}}
                    </table>
                </div>
            </div>

            <div style="display:flex; justify-content: space-between; align-items: center;">
                <button type="button" class="btn btn-secondary" onclick="goStep(3)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <button type="submit" id="submitBtn" class="btn btn-success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save &amp; Submit Draft
                </button>
            </div>
        </div>

    </div>{{-- end main --}}
</div>{{-- end flex --}}
</form>

{{-- ═══════ MODAL: Copy Distribution (legacy compat) ═══════ --}}
<div id="deptModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white w-full max-w-xl rounded-md shadow-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Copy Distribution</h2>
            <button id="cancelModal" class="text-gray-400 hover:text-gray-700 text-base leading-none">✕</button>
        </div>
        <div id="deptContainer" class="space-y-2 max-h-60 overflow-auto pr-1">
            <div class="flex gap-2 items-center dept-row">
                <select name="share_dept[]" class="select-dept flex-1 f-input" style="padding:7px 10px;">
                    <option value="">— Select Department —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                <input type="number" name="share_qty[]" min="1" value="1"
                       class="f-input w-20 text-center" style="padding:7px 8px;">
                <button type="button" class="removeRow text-gray-300 hover:text-red-600 transition text-sm">✕</button>
            </div>
        </div>
        <button id="addRow" class="mt-3 text-xs text-blue-700 hover:underline font-medium">+ Add Department</button>
        <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-gray-100">
            <button id="cancelModal2" class="btn btn-secondary">Cancel</button>
            <button id="confirmSave" class="btn btn-primary">Confirm &amp; Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ═══════════════════════════════════════════════
// WIZARD NAVIGATION
// ═══════════════════════════════════════════════
let currentStep = 1;
const STEPS = 4;

function goStep(n) {
    if (n > currentStep) {
        if (currentStep === 1 && !validateStep1()) return;
        if (currentStep === 2 && !validateStep2()) return;
        if (currentStep === 3 && !validateStep3()) return;
    }

    document.getElementById('step' + currentStep).classList.remove('is-active');
    document.getElementById('step' + n).classList.add('is-active');

    updateWizard(n);
    updateSidebar(n);
    if (n === 4) buildReview();

    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateWizard(active) {
    for (let i = 1; i <= STEPS; i++) {
        const num = document.getElementById('wn' + i);
        const lbl = document.getElementById('wt' + i);
        num.className = 'wiz-num';
        lbl.className = 'wiz-title';

        if (i < active)      { num.classList.add('is-done'); num.textContent = ''; }
        else if (i === active){ num.classList.add('is-active'); num.textContent = i; lbl.classList.add('is-active'); }
        else                  { num.textContent = i; }

        if (i < STEPS) {
            const line = document.getElementById('wl' + i);
            line.className = 'wiz-line' + (i < active ? ' is-done' : '');
        }
    }
}

function updateSidebar(active) {
    for (let i = 1; i <= STEPS; i++) {
        const dot = document.getElementById('pd' + i);
        const ttl = document.getElementById('pt' + i);
        dot.className = 'prog-dot';
        ttl.className = 'prog-title';

        if (i < active)      { dot.classList.add('is-done'); dot.textContent = ''; }
        else if (i === active){ dot.classList.add('is-active'); dot.textContent = i; ttl.classList.add('is-active'); }
        else                  { dot.textContent = i; }

        if (i < STEPS) {
            const conn = document.getElementById('pc' + i);
            conn.className = 'prog-connector' + (i < active ? ' is-done' : '');
        }
    }
}

// ═══════════════════════════════════════════════
// VALIDATIONS
// ═══════════════════════════════════════════════
function validateStep1() {
    if (!$('#department').val()) {
        showToast('warning', 'Please select a department destination.');
        return false;
    }
    const type = $('input[name="document_type"]:checked').val();
    if (type === 'other' && !$('#otherInput').val().trim()) {
        showToast('warning', 'Please specify the document type.');
        return false;
    }
    return true;
}
function validateStep2() {
    const num = !$('#doc_number_input').hasClass('hidden')
        ? $('#doc_number_input').val().trim()
        : $('#doc_number_select').val();
    if (!num) { showToast('warning', 'Document number is required.'); return false; }
    if (!$('input[name="document_title"]').val().trim()) {
        showToast('warning', 'Document title is required.'); return false;
    }
    return true;
}
function validateStep3() {
    if (!document.getElementById('file').files[0]) {
        showToast('warning', 'Please upload the document file.'); return false;
    }
    const need4m = $('input[name="need_4m"]:checked').val();
    if (need4m === 'yes' && !document.getElementById('file_4m').files[0]) {
        showToast('warning', '4M attachment is required.'); return false;
    }
    return true;
}

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
$(document).ready(function () {

    // Select2 — Department
    $('#department').select2({ width: '100%', placeholder: '— Select —', allowClear: true });

    $('#department').on('change', function () {
        const txt = $(this).find('option:selected').text().trim();
        if (txt && this.value) {
            const init = txt.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
            $('#deptAvatar').text(init);
            $('#deptLabel').text(txt);
            $('#deptInfo').removeClass('hidden');
        } else {
            $('#deptInfo').addClass('hidden');
        }
    });

    // Type Tiles
    $(document).on('change', 'input[name="document_type"]', function () {
        $('.type-tile').removeClass('is-selected');
        $(this).closest('.type-tile').addClass('is-selected');
        const isOther = $(this).val() === 'other';
        $('#otherTypeWrap').toggleClass('hidden', !isOther);
        if (!isOther) loadDocNumbers($(this).val());
    });

    // Submission pills
    $(document).on('change', 'input[name="submission_type"]', function () {
        $('.sub-pill').removeClass('is-selected');
        $(this).closest('.sub-pill').addClass('is-selected');

        const val = $(this).val();
        const map = {
            'New Release': ['success', 'A new document will be registered for the first time in the system.'],
            'Revision':    ['warn',    'An existing document will be updated. Document the changes below.'],
            'Obsolete':    ['danger',  'The selected document will be marked as no longer in use.'],
        };
        const [cls, txt] = map[val] || ['info', ''];
        const banner = document.getElementById('subInfoBanner');
        banner.className = 'info-banner ' + cls;
        document.getElementById('subInfoText').textContent = txt;

        if (val === 'Revision') {
            $('#doc_number_input').addClass('hidden');
            $('#doc_number_select').removeClass('hidden');
            $('#revision_group, #changes_group').removeClass('hidden');
        } else if (val === 'Obsolete') {
            $('#doc_number_input').addClass('hidden');
            $('#doc_number_select').removeClass('hidden');
            $('#revision_group, #changes_group').addClass('hidden');
        } else {
            $('#doc_number_input').removeClass('hidden');
            $('#doc_number_select').addClass('hidden');
            $('#revision_group, #changes_group').addClass('hidden');
        }
    });

    // 4M toggle
    $("input[name='need_4m']").on('change', function () {
        $('#file_4m_group').toggleClass('hidden', $(this).val() !== 1);
    });

    // Title char count
    $('#titleInput').on('input', function () {
        $('#titleCount').text($(this).val().length);
    });

    // Doc number
    function loadDocNumbers(type) {
    $.ajax({
        url: '/mr/get-document-number',
        type: 'GET',
        data: { document_type: type },
        success: function (data) {

            const $sel = $('#doc_number_select');
            $sel.html('<option value="">— Select Document —</option>');

            if (data.length > 0) {
                $.each(data, (i, doc) => {
                    $sel.append(`
                        <option value="${doc.document_number}" 
                                data-title="${doc.document_title}"
                                data-dept="${doc.dept_to}"
                                data-version="${doc.current_version}">
                            ${doc.document_number}
                        </option>
                    `);
                });

                $('#last_doc_info').removeClass('hidden');
                $('#last_doc_value').text(data[0].document_number);
            } else {
                $('#last_doc_info').addClass('hidden');
            }
        }
    });
}
    loadDocNumbers($('.docType:checked').val());
    $('.docType').on('change', function () { loadDocNumbers($(this).val()); });

   $('#doc_number_select').on('change', function () {
    const selected = $(this).find('option:selected');

    const title   = selected.data('title') || '';
    const dept    = selected.data('dept');
    const version = selected.data('version');

    // =========================
    // 1. TITLE
    // =========================
    $('#titleInput').val(title);
    $('#titleCount').text(title.length);

    // =========================
    // 2. DEPARTMENT DESTINATION
    // =========================
    if (dept) {
        $('#department').val(dept).trigger('change');

        // tampilkan info kecil (optional UI kamu)
        const label = $('#department option:selected').text();
        $('#deptLabel').text(label);
        $('#deptAvatar').text(label.substring(0,2).toUpperCase());
        $('#deptInfo').removeClass('hidden');
    }

    // =========================
    // 3. REVISION NUMBER
    // =========================
    if (version !== undefined) {
        const nextVersion = parseInt(version) + 1;

        $('input[name="revision_number"]').val(nextVersion);

        // tampilkan field revision
        $('#revision_group').removeClass('hidden');
    }
});

$('.docType').on('change', function () {
    loadDocNumbers($(this).val());

    $('#titleInput').val('');
    $('#titleCount').text(0);
    $('#department').val('');
    $('#deptInfo').addClass('hidden');
    $('input[name="revision_number"]').val('');
    $('#revision_group').addClass('hidden');
});

    // ── Distribution table ──
    $('#addDeptBtn').on('click', function () {
        const row = `<tr class="dist-row">
            <td>
                <select name="share_dept[]" class="f-input" style="padding:6px 10px;">
                    <option value="">— Select Department —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="share_qty[]" min="1" value="1"
                       class="f-input" style="padding:6px 10px; max-width: 80px;">
            </td>
            <td style="text-align: right; padding-right: 16px;">
                <button type="button" class="removeDistRow btn btn-danger" style="padding: 4px 10px; font-size: 11px;">Remove</button>
            </td>
        </tr>`;
        $('#distTableBody').append(row);
        updateDistRemoveBtns();
    });

    $(document).on('click', '.removeDistRow', function () {
        if ($('.dist-row').length > 1) {
            $(this).closest('tr').remove();
            updateDistRemoveBtns();
        }
    });

    function updateDistRemoveBtns() {
        const count = $('.dist-row').length;
        $('.removeDistRow').each(function () {
            if (count === 1) {
                $(this).css('opacity', '.4').css('pointer-events', 'none');
            } else {
                $(this).css('opacity', '1').css('pointer-events', 'auto');
            }
        });
    }

    // ── File Drop ──
    setupDrop('mainZone', 'file', 'main');
    setupDrop('fm4Zone', 'file_4m', 'fm4');

    // ── Form Submit ──
    $('#doc-form').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).html('<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h-1M5 12H4m13.66-6.66l-.7.7M6.34 17.66l-.7.7m0-12.02l.7.7m10.62 10.62l.7.7"/></svg> Saving…');

        const formData = new FormData(this);

        let docNum = !$('#doc_number_input').hasClass('hidden')
            ? $('#doc_number_input').val()
            : $('#doc_number_select').val();
        formData.delete('document_number');
        formData.append('document_number', docNum);

        let docType = $('input[name="document_type"]:checked').val();
        if (docType === 'other') docType = $('#otherInput').val().trim();
        formData.set('document_type', docType);

        let need4m = $('input[name="need_4m"]:checked').val() || 0;
        formData.set('need_4m', need4m);

        // Append distribution
        $('select[name="share_dept[]"]').each((i, el) => formData.append('share_dept['+i+']', el.value));
        $('input[name="share_qty[]"]').each((i, el) => formData.append('share_qty['+i+']', el.value));

        $.ajax({
            url: '{{ route("mr.doc.store") }}',
            method: 'POST', data: formData,
            processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    showToast('success', res.message || 'Document saved successfully.');
                    setTimeout(() => location.href = '{{ route("mr.doc.index") }}', 2000);
                } else {
                    showToast('error', res.message || 'Failed to save document.');
                    $btn.prop('disabled', false).html('<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save &amp; Submit Draft');
                }
            },
            error: function (err) {
                showToast('error', err.responseJSON?.message || 'Server error.');
                $btn.prop('disabled', false).html('<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save &amp; Submit Draft');
            }
        });
    });

    // ── Legacy modal (kept for compatibility) ──
    $('#cancelModal, #cancelModal2').on('click', () => $('#deptModal').addClass('hidden').removeClass('flex'));
    $('#addRow').on('click', function () {
        const row = `<div class="flex gap-2 items-center dept-row">
            <select name="share_dept[]" class="select-dept flex-1 f-input" style="padding:7px 10px;">
                <option value="">— Select Department —</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
            <input type="number" name="share_qty[]" min="1" value="1" class="f-input w-20 text-center" style="padding:7px 8px;">
            <button type="button" class="removeRow text-gray-300 hover:text-red-600 text-sm">✕</button>
        </div>`;
        $('#deptContainer').append(row);
    });
    $(document).on('click', '.removeRow', function () {
        if ($('.dept-row').length > 1) $(this).closest('.dept-row').remove();
    });
    $('#confirmSave').on('click', function () {
        $('#deptModal').addClass('hidden').removeClass('flex');
        $('#doc-form').submit();
    });
});

// ═══════════════════════════════════════════════
// FILE DROP ZONES
// ═══════════════════════════════════════════════
function setupDrop(zoneId, inputId, prefix) {
    const zone  = document.getElementById(zoneId);
    const input = document.getElementById(inputId);
    if (!zone || !input) return;

    ['dragenter','dragover'].forEach(ev => zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.add('is-over'); }));
    ['dragleave','drop'].forEach(ev => zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.remove('is-over'); }));
    zone.addEventListener('drop', e => { const f = e.dataTransfer.files[0]; if (f) showFilePreview(f, prefix); });
    input.addEventListener('change', function () { if (this.files[0]) showFilePreview(this.files[0], prefix); });
}

function showFilePreview(file, prefix) {
    const ext  = file.name.split('.').pop().toLowerCase();
    const size = file.size > 1024*1024
        ? (file.size / (1024*1024)).toFixed(1) + ' MB'
        : (file.size / 1024).toFixed(0) + ' KB';

    document.getElementById(prefix + 'Ext').textContent = ext.toUpperCase();
    document.getElementById(prefix + 'Ext').className   = 'file-ext-badge ' + ext;
    document.getElementById(prefix + 'Name').textContent = file.name;
    document.getElementById(prefix + 'Size').textContent = size;
    document.getElementById(prefix + 'Placeholder').classList.add('hidden');
    document.getElementById(prefix + 'Preview').classList.remove('hidden');
}

function clearFile(prefix) {
    const inputId = prefix === 'main' ? 'file' : 'file_4m';
    document.getElementById(inputId).value = '';
    document.getElementById(prefix + 'Placeholder').classList.remove('hidden');
    document.getElementById(prefix + 'Preview').classList.add('hidden');
}

// ═══════════════════════════════════════════════
// REVIEW TABLE
// ═══════════════════════════════════════════════
function buildReview() {
    const type     = $('input[name="document_type"]:checked').val();
    const typeFin  = type === 'other' ? ($('#otherInput').val() || '—') : type;
    const subType  = $('input[name="submission_type"]:checked').val() || '—';
    const dept     = $('#department option:selected').text().trim() || '—';
    const docNum   = !$('#doc_number_input').hasClass('hidden')
                       ? $('#doc_number_input').val() : $('#doc_number_select').val();
    const title    = $('input[name="document_title"]').val() || '—';
    const reason   = $('textarea[name="reason"]').val() || 'Not provided';
    const need4m   = $('input[name="need_4m"]:checked').val() === "1" ? 'Yes' : 'No';
    const file     = document.getElementById('file').files[0];
    const filename = file ? file.name : '— No file selected —';

    const subColorMap = {
        'New Release': 'color:#14532d; background:#f0fdf4; border:1px solid #bbf7d0;',
        'Revision':    'color:#78350f; background:#fffbeb; border:1px solid #fde68a;',
        'Obsolete':    'color:#7f1d1d; background:#fef2f2; border:1px solid #fecaca;',
    };
    const subStyle = subColorMap[subType] || '';

    const rows = [
        ['Department Dest.', dept],
        ['Document Type',    typeFin],
        ['Submission Type',  `<span style="display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; font-weight:600; ${subStyle}">${subType}</span>`],
        ['Document Number',  `<code style="font-family:monospace; font-size:12px; background:#f3f4f6; padding:2px 6px; border-radius:3px;">${docNum || '—'}</code>`],
        ['Document Title',   title],
        ['Reason',           reason],
        ['4M Required',      need4m],
        ['Document File',    filename],
    ];

    document.getElementById('reviewTable').innerHTML = rows.map(([k, v]) =>
        `<tr><td class="r-key">${k}</td><td class="r-val">${v}</td></tr>`
    ).join('');
}

// ═══════════════════════════════════════════════
// TOAST
// ═══════════════════════════════════════════════
function showToast(icon, title) {
    Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true, icon, title });
}

feather.replace();
</script>
@endpush

@endsection