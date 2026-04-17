@extends('layouts.app')

@section('title', 'Edit Document - ' . $document->document_number)
@section('page-title', 'EDIT DOCUMENT')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Edit Document')

@section('content')

@php
    $isResubmit  = in_array($document->status, ['Returned by SPV', 'Returned by MR']);
    $status      = $document->status ?? 'Draft';
    $knownTypes  = ['Form', 'Work Instructions', 'Standard', 'SOP'];
    $currentType = $document->document_type ?? 'Form';
    $isOtherType = !in_array($currentType, $knownTypes);
    $subType     = $document->submission_type ?? 'New Release';
    $isRevOrObs  = in_array($subType, ['Revision', 'Obsolete']);
@endphp

<style>
*, *::before, *::after { box-sizing: border-box; }

.c-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; }
.c-card-header { padding: 14px 20px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }
.c-card-body { padding: 20px; }
.c-card-footer { padding: 14px 20px; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; gap: 8px; }
.c-section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; }

.f-label { display: block; font-size: 12px; font-weight: 500; color: #374151; margin-bottom: 5px; }
.f-label sup { color: #dc2626; }
.f-input {
    width: 100%; padding: 8px 11px; border: 1px solid #d1d5db; border-radius: 4px;
    font-size: 13px; color: #111827; background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s; line-height: 1.4;
}
.f-input:focus { border-color: #1e3a5f; box-shadow: 0 0 0 2px rgba(30,58,95,.10); }
.f-input::placeholder { color: #9ca3af; }
.f-input[readonly] { background: #f9fafb; color: #6b7280; cursor: not-allowed; }
textarea.f-input { resize: vertical; }

/* Type tiles */
.type-tile {
    position: relative; border: 1px solid #e5e7eb; border-radius: 4px;
    padding: 10px 12px; cursor: pointer; transition: border-color .15s, background .15s;
    display: flex; align-items: center; gap: 8px; background: #fff; user-select: none;
}
.type-tile:hover { border-color: #9ca3af; background: #f9fafb; }
.type-tile.is-selected { border-color: #1e3a5f; background: #f0f4f9; }
.type-tile input[type="radio"] { display: none; }
.type-tile-dot { width: 8px; height: 8px; border-radius: 50%; border: 1.5px solid #d1d5db; flex-shrink: 0; transition: all .15s; }
.type-tile.is-selected .type-tile-dot { background: #1e3a5f; border-color: #1e3a5f; }
.type-tile-label { font-size: 12px; font-weight: 500; color: #374151; }
.type-tile.is-selected .type-tile-label { color: #1e3a5f; }

/* Submission pills */
.sub-pill {
    display: flex; align-items: center; gap: 6px; padding: 7px 14px;
    border: 1px solid #e5e7eb; border-radius: 4px; cursor: pointer;
    font-size: 12px; font-weight: 500; color: #6b7280; background: #fff;
    transition: all .15s; user-select: none;
}
.sub-pill:hover { background: #f9fafb; border-color: #9ca3af; }
.sub-pill input[type="radio"] { display: none; }
.sub-pill.new.is-selected { border-color: #166534; background: #f0fdf4; color: #14532d; }
.sub-pill.rev.is-selected { border-color: #92400e; background: #fffbeb; color: #78350f; }
.sub-pill.obs.is-selected { border-color: #991b1b; background: #fef2f2; color: #7f1d1d; }
.sub-pill-indicator { width: 6px; height: 6px; border-radius: 50%; background: #d1d5db; flex-shrink: 0; transition: background .15s; }
.sub-pill.new.is-selected .sub-pill-indicator { background: #166534; }
.sub-pill.rev.is-selected .sub-pill-indicator { background: #92400e; }
.sub-pill.obs.is-selected .sub-pill-indicator { background: #991b1b; }

/* File zones */
.drop-zone {
    border: 1px dashed #d1d5db; border-radius: 4px; padding: 20px;
    text-align: center; cursor: pointer; transition: border-color .15s, background .15s; background: #fafafa; position: relative;
}
.drop-zone:hover, .drop-zone.is-over { border-color: #1e3a5f; background: #f0f4f9; }
.drop-zone input[type="file"] { display: none; }
.drop-zone-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 4px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; background: #fff; color: #6b7280; }

/* File preview */
.file-preview-row { display: flex; align-items: center; gap: 10px; padding: 9px 11px; border: 1px solid #e5e7eb; border-radius: 4px; background: #fafafa; }
.file-ext-badge { font-size: 9px; font-weight: 700; letter-spacing: .04em; padding: 3px 6px; border-radius: 3px; background: #e5e7eb; color: #374151; flex-shrink: 0; text-transform: uppercase; }
.file-ext-badge.xlsx { background: #d1fae5; color: #065f46; }
.file-ext-badge.pdf  { background: #fee2e2; color: #7f1d1d; }
.file-ext-badge.docx { background: #dbeafe; color: #1e3a8a; }

/* Existing file chip */
.existing-file {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 11px; border: 1px solid #e5e7eb; border-radius: 4px;
    background: #f9fafb; margin-bottom: 8px;
}
.existing-file-badge { font-size: 9px; font-weight: 700; padding: 3px 6px; border-radius: 3px; background: #dbeafe; color: #1e3a8a; flex-shrink: 0; text-transform: uppercase; }

/* Banners */
.info-banner { border-radius: 4px; padding: 9px 12px; font-size: 12px; display: flex; gap: 8px; align-items: flex-start; }
.info-banner svg { width: 13px; height: 13px; flex-shrink: 0; margin-top: 1px; }
.info-banner.info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.info-banner.warn    { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; }
.info-banner.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; }
.info-banner.danger  { background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }
.info-banner.amber   { background: #fffbeb; border: 1px solid #fcd34d; color: #78350f; }

/* Buttons */
.btn {
    display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px;
    border-radius: 4px; font-size: 12px; font-weight: 500;
    border: 1px solid transparent; cursor: pointer; transition: all .15s; white-space: nowrap;
}
.btn-primary   { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
.btn-primary:hover { background: #162d4a; }
.btn-secondary { background: #fff; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #f9fafb; }
.btn-success   { background: #166534; color: #fff; border-color: #166534; }
.btn-success:hover { background: #14532d; }
.btn-resubmit  { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
.btn-resubmit:hover { background: #1e40af; }
.btn-danger    { background: #fff; color: #991b1b; border-color: #fca5a5; }
.btn-danger:hover { background: #fef2f2; }
.btn:disabled  { opacity: .55; cursor: not-allowed; }
.btn svg { width: 13px; height: 13px; flex-shrink: 0; }

/* Status badge */
.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 3px; font-size: 11px; font-weight: 500; border: 1px solid #e5e7eb; background: #f9fafb; color: #374151; }
.status-dot   { width: 5px; height: 5px; border-radius: 50%; background: #9ca3af; }

/* Distribution table */
.dist-table { width: 100%; border-collapse: collapse; }
.dist-table th { text-align: left; font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: #6b7280; padding: 8px 10px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
.dist-table td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; font-size: 12px; vertical-align: middle; }
.dist-table tr:last-child td { border-bottom: none; }
.dist-table tr:hover td { background: #f9fafb; }

/* Select2 */
.select2-container { width: 100% !important; }
.select2-container .select2-selection--single {
    height: 34px !important; display: flex !important; align-items: center !important;
    border: 1px solid #d1d5db !important; border-radius: 4px !important; padding: 0 10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 0 !important; line-height: 34px !important; font-size: 13px; color: #111827; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 34px !important; right: 8px; }
.select2-container--focus .select2-selection--single,
.select2-container--open .select2-selection--single { border-color: #1e3a5f !important; box-shadow: 0 0 0 2px rgba(30,58,95,.10) !important; }

/* Sidebar progress */
.prog-step { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; }
.prog-dot {
    width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #e5e7eb;
    background: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700; color: #9ca3af; flex-shrink: 0; margin-top: 1px; transition: all .2s;
}
.prog-dot.is-done   { border-color: #166534; background: #166534; color: #fff; }
.prog-dot.is-active { border-color: #1e3a5f; background: #1e3a5f; color: #fff; }
.prog-dot.is-done::after { content: '✓'; }
.prog-title { font-size: 12px; font-weight: 500; color: #6b7280; line-height: 1.3; }
.prog-title.is-active { color: #1e3a5f; font-weight: 600; }
.prog-title.is-done   { color: #166534; }
.prog-sub   { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.prog-connector { width: 1px; height: 18px; background: #e5e7eb; margin-left: 9px; transition: background .2s; }
.prog-connector.is-done { background: #166534; }

/* Section separator */
.section-sep { border-top: 1px solid #f3f4f6; padding: 16px 20px; }

@media (max-width: 767px) { .r-key { width: 110px; } }
</style>

<form id="doc-form" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="flex flex-col lg:flex-row gap-4">

    {{-- ═══════ SIDEBAR ═══════ --}}
    <div class="w-full lg:w-64 xl:w-72 flex-shrink-0 flex flex-col gap-4">

        {{-- Status & Context --}}
        <div class="c-card">
            <div class="c-card-header">
                <span class="c-section-label">Document Status</span>
            </div>
            <div class="c-card-body" style="padding: 14px 16px;">
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Document No.</span>
                        <code class="font-mono text-xs font-semibold text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">{{ $document->document_number }}</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Status</span>
                        @php
                            $dotColor = match($status) {
                                'Returned by SPV', 'Returned by MR' => '#d97706',
                                'Submitted', 'Resubmitted' => '#2563eb',
                                default => '#9ca3af',
                            };
                        @endphp
                        <span class="status-badge" style="border-color: {{ $dotColor }}20;">
                            <span class="status-dot" style="background: {{ $dotColor }};"></span>
                            {{ $status }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Mode</span>
                        <span class="text-xs font-semibold {{ $isResubmit ? 'text-blue-700' : 'text-gray-700' }}">
                            {{ $isResubmit ? 'Resubmit' : 'Edit Draft' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Progress (static for edit) --}}
        <div class="c-card">
            <div class="c-card-header">
                <span class="c-section-label">Sections</span>
            </div>
            <div class="c-card-body" style="padding: 14px 20px;">
                <div class="flex flex-col gap-0">
                    <div class="prog-step">
                        <div class="prog-dot is-done">1</div>
                        <div>
                            <p class="prog-title is-done">Submission Type</p>
                            <p class="prog-sub">{{ $subType }}</p>
                        </div>
                    </div>
                    <div class="prog-connector is-done"></div>
                    <div class="prog-step">
                        <div class="prog-dot is-done">2</div>
                        <div>
                            <p class="prog-title is-done">Document Details</p>
                            <p class="prog-sub">{{ Str::limit($document->document_title, 22) }}</p>
                        </div>
                    </div>
                    <div class="prog-connector is-done"></div>
                    <div class="prog-step">
                        <div class="prog-dot is-active">3</div>
                        <div>
                            <p class="prog-title is-active">Dept &amp; Files</p>
                            <p class="prog-sub">{{ $document->department->name ?? 'Not set' }}</p>
                        </div>
                    </div>
                    <div class="prog-connector"></div>
                    <div class="prog-step">
                        <div class="prog-dot">4</div>
                        <div>
                            <p class="prog-title">Distribution</p>
                            <p class="prog-sub">{{ $document->copies->count() ?? 0 }} dept(s)</p>
                        </div>
                    </div>
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
                    ['BLANK FORM.xlsx',                        'Form Template'],
                    ['BLANK SOP.xlsx',                         'SOP Template'],
                    ['BLANK IK.xlsx',                          'Work Instr. (General)'],
                    ['BLANK IK FOR PRODUKSI DAN QUALITY.xlsx', 'Work Instr. (Prod/QC)'],
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
                    <h1 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">
                        {{ $isResubmit ? 'Resubmit Document' : 'Edit Document' }}
                    </h1>
                    <p style="font-size: 12px; color: #6b7280; margin: 3px 0 0;">
                        {{ $isResubmit ? 'Revise and resubmit the returned document' : 'Update your draft document before submission' }}
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-badge"><span class="status-dot" style="background: {{ $dotColor }};"></span>{{ $status }}</span>
                    <a href="{{ route('mr.doc.detail', $document->id) }}" class="btn btn-secondary" style="padding:5px 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Return notice --}}
        @if($isResubmit)
        <div class="c-card" style="border-color: #fcd34d;">
            <div class="c-card-body" style="padding: 14px 16px;">
                <div class="flex items-start gap-3">
                    <div style="width:28px; height:28px; border-radius:4px; background:#fffbeb; border:1px solid #fde68a; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg class="w-3.5 h-3.5" style="color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Document Returned — Action Required</p>
                        @if($document->returned_reason ?? false)
                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                            {{ collect($document->returned_reason)->last() }}
                        </p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">Revise accordingly and save to resubmit.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ─── SECTION 1: Submission Type + Document Type ─── --}}
        <div class="c-card">
            <div class="c-card-header">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="c-section-label">Submission Type</span>
            </div>
            <div class="c-card-body">
                <p class="f-label mb-3">Submission purpose <sup>*</sup></p>
                <div class="flex flex-wrap gap-2 mb-3">
                    <label class="sub-pill new {{ $subType === 'New Release' ? 'is-selected' : '' }}">
                        <input type="radio" name="submission_type" value="New Release" {{ $subType === 'New Release' ? 'checked' : '' }}>
                        <span class="sub-pill-indicator"></span>
                        New Release
                    </label>
                    <label class="sub-pill rev {{ $subType === 'Revision' ? 'is-selected' : '' }}">
                        <input type="radio" name="submission_type" value="Revision" {{ $subType === 'Revision' ? 'checked' : '' }}>
                        <span class="sub-pill-indicator"></span>
                        Revision
                    </label>
                    <label class="sub-pill obs {{ $subType === 'Obsolete' ? 'is-selected' : '' }}">
                        <input type="radio" name="submission_type" value="Obsolete" {{ $subType === 'Obsolete' ? 'checked' : '' }}>
                        <span class="sub-pill-indicator"></span>
                        Obsolete
                    </label>
                </div>

                @php
                    $bannerCls = match($subType) {
                        'New Release' => 'success', 'Revision' => 'warn', 'Obsolete' => 'danger', default => 'info'
                    };
                    $bannerMsg = match($subType) {
                        'New Release' => 'A new document will be registered for the first time in the system.',
                        'Revision'    => 'An existing document will be updated. Select the document to revise.',
                        'Obsolete'    => 'The selected document will be marked as no longer in use.',
                        default       => ''
                    };
                @endphp
                <div id="subInfoBanner" class="info-banner {{ $bannerCls }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="subInfoText">{{ $bannerMsg }}</span>
                </div>
            </div>

            {{-- Document Type --}}
            <div class="section-sep">
                <p class="c-section-label mb-3">Document Type <sup style="color:#dc2626;">*</sup></p>

                <div id="typeRevNotice" class="info-banner warn mb-3 {{ $isRevOrObs ? '' : 'hidden' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Select the document type to look up existing published documents.</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="typeTiles">
                    @foreach([['Form','FM'],['Work Instructions','WI'],['Standard','STD'],['SOP','SOP'],['other','...']] as $i => [$val, $abbr])
                    @php
                        $isSelected = $isOtherType
                            ? ($val === 'other')
                            : ($currentType === $val);
                    @endphp
                    <label class="type-tile {{ $isSelected ? 'is-selected' : '' }}" id="tile_{{ $i }}">
                        <input type="radio" name="document_type" value="{{ $val }}" class="docType"
                               {{ $isSelected ? 'checked' : '' }}>
                        <span class="type-tile-dot"></span>
                        <span class="type-tile-label">{{ $val === 'other' ? 'Other' : $val }}</span>
                    </label>
                    @endforeach
                </div>

                <div id="otherTypeWrap" class="{{ ($isOtherType && !$isRevOrObs) ? '' : 'hidden' }} mt-3">
                    <label class="f-label">Specify Type <sup>*</sup></label>
                    <input type="text" name="document_type_other" id="otherInput"
                           value="{{ $isOtherType ? $currentType : '' }}"
                           placeholder="e.g. Manual Instruction, Module, Guideline..."
                           class="f-input">
                </div>
            </div>
        </div>

        {{-- ─── SECTION 2: Document Details ─── --}}
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
                        {{-- New Release: readonly (can't change number on edit) --}}
                        <input type="text" id="doc_number_input" name="document_number"
                               value="{{ $document->document_number }}"
                               class="f-input {{ $isRevOrObs ? 'hidden' : '' }}" readonly>

                        {{-- Revision / Obsolete: dropdown --}}
                        <select id="doc_number_select" name="document_number_select"
                                class="f-input {{ $isRevOrObs ? '' : 'hidden' }}" style="padding:7px 10px;">
                            <option value="">— Select Published Document —</option>
                            {{-- Filled via AJAX, current value pre-selected --}}
                        </select>

                        <p id="last_doc_info" class="text-xs text-gray-400 mt-1.5 {{ $isRevOrObs ? '' : 'hidden' }}">
                            Current: <span id="last_doc_value" class="font-medium text-gray-600">{{ $document->document_number }}</span>
                        </p>
                    </div>

                    <div id="revision_group" class="{{ $subType === 'Revision' ? '' : 'hidden' }}">
                        <label class="f-label">Revision No.</label>
                        <input type="text" name="revision_number"
                               value="{{ $document->revision_number }}"
                               placeholder="e.g. 02" class="f-input">
                    </div>
                </div>

                <div>
                    <label class="f-label">Document Title <sup>*</sup></label>
                    <input type="text" name="document_title" id="titleInput"
                           value="{{ $document->document_title }}"
                           placeholder="Enter a clear, descriptive title" class="f-input" maxlength="120">
                    <p class="text-xs text-gray-400 mt-1">
                        <span id="titleCount">{{ strlen($document->document_title ?? '') }}</span> / 120 characters
                    </p>
                </div>

                <div>
                    <label class="f-label">Reason for Submission</label>
                    <textarea name="reason" rows="3" class="f-input">{{ $document->reason }}</textarea>
                </div>

                <div id="changes_group" class="{{ $subType === 'Revision' ? '' : 'hidden' }}">
                    <div class="info-banner warn mb-3">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Revision submissions require documentation of what changed.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="f-label" style="color:#991b1b;">Before Changes</label>
                            <textarea name="before_change" rows="4" class="f-input" style="background:#fef2f2;">{{ $document->before_change }}</textarea>
                        </div>
                        <div>
                            <label class="f-label" style="color:#166534;">After Changes</label>
                            <textarea name="after_change" rows="4" class="f-input" style="background:#f0fdf4;">{{ $document->after_change }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 4M --}}
                <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 4px; background: #fafafa; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <p class="text-xs font-medium text-gray-700">4M Attachment Required?</p>
                        <p class="text-xs text-gray-400 mt-0.5">Man, Machine, Material, Method analysis</p>
                    </div>
                    <div style="display: flex; gap: 16px;">
                        <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                            <input type="radio" name="need_4m" value="0" {{ !$document->need_4m ? 'checked' : '' }}> No
                        </label>
                        <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                            <input type="radio" name="need_4m" value="1" {{ $document->need_4m ? 'checked' : '' }}> Yes
                        </label>
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── SECTION 3: Department + File Upload ─── --}}
        <div class="c-card">
            <div class="c-card-header">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="c-section-label">Department Destination</span>
            </div>
            <div class="c-card-body" style="padding-bottom: 4px;">
                <label class="f-label">Department <sup>*</sup></label>
                <select id="department" name="department_id" required class="f-input" style="padding:7px 10px;">
                    <option value="">— Select Department —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $document->department_id == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                <div id="deptInfo" class="{{ $document->department_id ? '' : 'hidden' }} mt-3 mb-2 p-2 border border-gray-200 rounded bg-gray-50 flex items-center gap-2">
                    <div id="deptAvatar" class="w-7 h-7 rounded-sm flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:#1e3a5f; color:#fff;">
                        {{ strtoupper(substr($document->department->name ?? '', 0, 2)) }}
                    </div>
                    <span id="deptLabel" class="text-xs font-medium text-gray-700 truncate">{{ $document->department->name ?? '' }}</span>
                </div>
            </div>

            {{-- Files --}}
            <div class="section-sep space-y-4">
                <p class="c-section-label">
                    <svg class="w-4 h-4 text-gray-400 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    File Upload
                </p>

                {{-- Main document file --}}
                <div>
                    <label class="f-label">Document File
                        <sup>*</sup>
                        @if($document->file_path)
                        <span style="font-size:10px; font-weight:400; color:#6b7280; margin-left:4px;">Leave empty to keep current file</span>
                        @endif
                    </label>

                    {{-- Existing file --}}
                    @if($document->file_path)
                    <div class="existing-file mb-2">
                        <span class="existing-file-badge">{{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}</span>
                        <div style="flex:1; min-width:0;">
                            <p class="text-xs font-medium text-gray-700 truncate">{{ basename($document->file_path) }}</p>
                            <p class="text-xs text-gray-400">Current file</p>
                        </div>
                        <a href="{{ asset('document/' . $document->file_path) }}" download
                           class="text-xs text-blue-700 hover:underline flex-shrink-0 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                    </div>
                    @endif

                    <div class="drop-zone" id="mainZone" onclick="document.getElementById('fileMain').click()">
                        <input type="file" name="file_path" id="fileMain" accept=".xlsx,.pdf,.docx">
                        <div id="mainPlaceholder">
                            <div class="drop-zone-icon">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-600">{{ $document->file_path ? 'Click to replace file' : 'Click to select or drag and drop' }}</p>
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

                {{-- 4M file --}}
                <div id="file_4m_group" class="{{ $document->need_4m ? '' : 'hidden' }}">
                    <label class="f-label">4M Attachment File
                        <sup>*</sup>
                        @if($document->file_4m_path)
                        <span style="font-size:10px; font-weight:400; color:#6b7280; margin-left:4px;">Leave empty to keep current file</span>
                        @endif
                    </label>

                    {{-- Existing 4M --}}
                    @if($document->file_4m_path)
                    <div class="existing-file mb-2" style="border-color: #d1fae5; background: #f0fdf4;">
                        <span class="existing-file-badge" style="background:#d1fae5; color:#065f46;">{{ strtoupper(pathinfo($document->file_4m_path, PATHINFO_EXTENSION)) }}</span>
                        <div style="flex:1; min-width:0;">
                            <p class="text-xs font-medium text-gray-700 truncate">{{ basename($document->file_4m_path) }}</p>
                            <p class="text-xs text-gray-400">Current 4M file</p>
                        </div>
                        <a href="{{ asset('document/4m/' . $document->file_4m_path) }}" download
                           class="text-xs text-blue-700 hover:underline flex-shrink-0 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                    </div>
                    @endif

                    <div class="drop-zone" id="fm4Zone" onclick="document.getElementById('file4m').click()">
                        <input type="file" name="file_4m_path" id="file4m" accept=".xlsx,.pdf,.docx">
                        <div id="fm4Placeholder">
                            <div class="drop-zone-icon">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-600">{{ $document->file_4m_path ? 'Click to replace 4M file' : 'Click to select 4M attachment' }}</p>
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
                    <span>Ensure the file follows the correct template. Download templates from the sidebar.</span>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 4: Copy Distribution ─── --}}
        <div class="c-card">
            <div class="c-card-header" style="justify-content: space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="c-section-label">Copy Distribution</span>
                </div>
                <button type="button" id="addDeptBtn" class="btn btn-secondary" style="padding: 4px 10px; font-size: 11px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Row
                </button>
            </div>
            <div class="c-card-body" style="padding: 0;">
                <table class="dist-table">
                    <thead>
                        <tr>
                            <th style="width:55%;">Department</th>
                            <th style="width:20%;">Qty (sheets)</th>
                            <th style="width:25%; text-align:right; padding-right:16px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="distTableBody">
                        @forelse($document->copies ?? [] as $copy)
                        <tr class="dist-row">
                            <td>
                                <select name="share_dept[]" class="f-input" style="padding:6px 10px;">
                                    <option value="">— Select Department —</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ $copy->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="share_qty[]" min="1" value="{{ $copy->qty }}"
                                       class="f-input" style="padding:6px 10px; max-width:80px;">
                            </td>
                            <td style="text-align:right; padding-right:16px;">
                                <button type="button" class="removeDistRow btn btn-danger" style="padding:4px 10px; font-size:11px;">Remove</button>
                            </td>
                        </tr>
                        @empty
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
                                       class="f-input" style="padding:6px 10px; max-width:80px;">
                            </td>
                            <td style="text-align:right; padding-right:16px;">
                                <button type="button" class="removeDistRow btn btn-danger"
                                        style="padding:4px 10px; font-size:11px; opacity:.4; pointer-events:none;">Remove</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex; justify-content: space-between; align-items:center;">
            <a href="{{ route('mr.doc.detail', $document->id) }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <button type="submit" id="submitBtn" class="{{ $isResubmit ? 'btn btn-resubmit' : 'btn btn-success' }}">
                @if($isResubmit)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Resubmit Document
                @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
                @endif
            </button>
        </div>

    </div>{{-- end main --}}
</div>

</form>

@push('scripts')
<script>
$(document).ready(function () {

    // Select2 — Department
    $('#department').select2({ width: '100%', placeholder: '— Select Department —', allowClear: true });
    $('#department').on('change', function () {
        const txt = $(this).find('option:selected').text().trim();
        if (txt && this.value) {
            const init = txt.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
            $('#deptAvatar').text(init);
            $('#deptLabel').text(txt);
            $('#deptInfo').removeClass('hidden');
        } else {
            $('#deptInfo').addClass('hidden');
        }
    });

    // ── Submission type pills ──
    $(document).on('change', 'input[name="submission_type"]', function () {
        const val = $(this).val();
        $('.sub-pill').removeClass('is-selected');
        $(this).closest('.sub-pill').addClass('is-selected');

        const map = {
            'New Release': ['success', 'A new document will be registered for the first time in the system.'],
            'Revision':    ['warn',    'An existing document will be updated. Select the document to revise.'],
            'Obsolete':    ['danger',  'The selected document will be marked as no longer in use.'],
        };
        const [cls, txt] = map[val] || ['info',''];
        document.getElementById('subInfoBanner').className = 'info-banner ' + cls;
        document.getElementById('subInfoText').textContent = txt;

        applySubmissionLogic(val);
    });

    // ── Document type tiles ──
    $(document).on('change', 'input[name="document_type"]', function () {
        $('.type-tile').removeClass('is-selected');
        $(this).closest('.type-tile').addClass('is-selected');

        const val   = $(this).val();
        const isRev = !($('input[name="submission_type"]:checked').val() === 'New Release');

        $('#otherTypeWrap').toggleClass('hidden', !(val === 'other' && !isRev));

        if (isRev) loadDocNumbers(val);
    });

    // ── Doc number select auto-fill ──
    $('#doc_number_select').on('change', function () {
        const opt     = $(this).find('option:selected');
        const title   = opt.data('title') || '';
        const dept    = opt.data('dept');
        const version = opt.data('version');

        if (title) { $('#titleInput').val(title); $('#titleCount').text(title.length); }
        if (dept)  { $('#department').val(dept).trigger('change'); }
        if (version !== undefined && $('input[name="submission_type"]:checked').val() === 'Revision') {
            $('input[name="revision_number"]').val(parseInt(version) + 1);
        }
    });

    // ── Title char count ──
    $('#titleInput').on('input', function () { $('#titleCount').text($(this).val().length); });

    // ── 4M toggle ──
    $(document).on('change', "input[name='need_4m']", function () {
        const val = $(this).val();
        if (val === '1') {
            $('#file_4m_group').removeClass('hidden');
        } else {
            $('#file_4m_group').addClass('hidden');
            document.getElementById('file4m').value = '';
            $('#fm4Placeholder').removeClass('hidden');
            $('#fm4Preview').addClass('hidden');
        }
    });

    // ── Doc number loader ──
    function loadDocNumbers(type) {
        if (!type || type === 'other') return;
        $.ajax({
            url: '/mr/get-document-number', type: 'GET', data: { document_type: type },
            success: function (data) {
                const $sel = $('#doc_number_select');
                $sel.html('<option value="">— Select Published Document —</option>');
                if (data.length > 0) {
                    $.each(data, (i, doc) => {
                        const sel = doc.document_number === '{{ $document->document_number }}' ? 'selected' : '';
                        $sel.append(`<option value="${doc.document_number}" ${sel}
                            data-title="${doc.document_title}"
                            data-dept="${doc.dept_to || ''}"
                            data-version="${doc.current_version || 0}">
                            ${doc.document_number}
                        </option>`);
                    });
                    $('#last_doc_info').removeClass('hidden');
                    $('#last_doc_value').text(data[0].document_number);
                } else {
                    $('#last_doc_info').addClass('hidden');
                }
            }
        });
    }

    // ── Apply logic for Revision / Obsolete ──
    function applySubmissionLogic(subType) {
        const isRev = (subType === 'Revision' || subType === 'Obsolete');
        const currentDocType = $('input[name="document_type"]:checked').val();

        if (isRev) {
            $('#doc_number_input').addClass('hidden');
            $('#doc_number_select').removeClass('hidden');
            $('#last_doc_info').removeClass('hidden');
            if (subType === 'Revision') {
                $('#revision_group, #changes_group').removeClass('hidden');
            } else {
                $('#revision_group, #changes_group').addClass('hidden');
            }
            $('#typeRevNotice').removeClass('hidden');
            $('#otherTypeWrap').addClass('hidden');
            loadDocNumbers(currentDocType);
        } else {
            $('#doc_number_input').removeClass('hidden');
            $('#doc_number_select').addClass('hidden');
            $('#last_doc_info').addClass('hidden');
            $('#revision_group, #changes_group').addClass('hidden');
            $('#typeRevNotice').addClass('hidden');
            $('#otherTypeWrap').toggleClass('hidden', currentDocType !== 'other');
        }
    }

    // Init: load doc numbers if revision/obsolete already selected
    const initSub = $('input[name="submission_type"]:checked').val();
    if (initSub === 'Revision' || initSub === 'Obsolete') {
        loadDocNumbers($('input[name="document_type"]:checked').val());
    }

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
                       class="f-input" style="padding:6px 10px; max-width:80px;">
            </td>
            <td style="text-align:right; padding-right:16px;">
                <button type="button" class="removeDistRow btn btn-danger" style="padding:4px 10px; font-size:11px;">Remove</button>
            </td>
        </tr>`;
        $('#distTableBody').append(row);
        syncRemoveBtns();
    });

    $(document).on('click', '.removeDistRow', function () {
        if ($('.dist-row').length > 1) { $(this).closest('tr').remove(); syncRemoveBtns(); }
    });

    function syncRemoveBtns() {
        const count = $('.dist-row').length;
        $('.removeDistRow').each(function () {
            $(this).css({ opacity: count === 1 ? '.4' : '1', 'pointer-events': count === 1 ? 'none' : 'auto' });
        });
    }
    syncRemoveBtns();

    // ── File drop zones ──
    setupDrop('mainZone', 'fileMain', 'main');
    setupDrop('fm4Zone',  'file4m',   'fm4');

    // ── Form submit ──
    $('#doc-form').on('submit', function (e) {
        e.preventDefault();

        // Validation
        if (!$('#department').val()) {
            showToast('warning', 'Please select a department.'); return;
        }

        const need4m = $('input[name="need_4m"]:checked').val();
        const has4mFile = document.getElementById('file4m').files[0];
        const hasExisting4m = {{ $document->file_4m_path ? 'true' : 'false' }};
        if (need4m === '1' && !has4mFile && !hasExisting4m) {
            showToast('warning', '4M attachment file is required.'); return;
        }

        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).text('Saving…');

        const formData = new FormData(this);

        // Document number
        let docNum = !$('#doc_number_input').hasClass('hidden')
            ? $('#doc_number_input').val()
            : $('#doc_number_select').val();
        if (!docNum) { showToast('warning', 'Document number is required.'); $btn.prop('disabled', false); restoreBtn($btn); return; }
        formData.delete('document_number');
        formData.append('document_number', docNum);

        // Document type
        let docType = $('input[name="document_type"]:checked').val();
        if (docType === 'other') docType = $('#otherInput').val().trim();
        if (!docType) { showToast('warning', 'Please specify the document type.'); $btn.prop('disabled', false); restoreBtn($btn); return; }
        formData.set('document_type', docType);

        // 4M
        formData.set('need_4m', need4m || '0');
        if (need4m !== '1') formData.delete('file_4m_path');

        // Resubmit flag
        formData.set('is_resubmit', {{ $isResubmit ? '1' : '0' }});

        // Distribution
        formData.delete('share_dept[]');
        formData.delete('share_qty[]');
        $('select[name="share_dept[]"]').each((i, el) => formData.append('share_dept[' + i + ']', el.value));
        $('input[name="share_qty[]"]').each((i, el) => formData.append('share_qty[' + i + ']', el.value));

        $.ajax({
            url: '{{ route("mr.doc.update", $document->id) }}',
            method: 'POST', data: formData,
            processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    showToast('success', res.message || 'Document updated successfully.');
                    setTimeout(() => location.href = '{{ route("mr.doc.detail", $document->id) }}', 2000);
                } else {
                    showToast('error', res.message || 'Failed to update document.');
                    $btn.prop('disabled', false); restoreBtn($btn);
                }
            },
            error: function (err) {
                showToast('error', err.responseJSON?.message || 'Server error.');
                $btn.prop('disabled', false); restoreBtn($btn);
            }
        });
    });

    function restoreBtn($btn) {
        @if($isResubmit)
        $btn.html('<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Resubmit Document');
        @else
        $btn.html('<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Changes');
        @endif
    }
});

// ── File drop setup ──
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
    const size = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' MB' : (file.size/1024).toFixed(0)+' KB';
    document.getElementById(prefix+'Ext').textContent  = ext.toUpperCase();
    document.getElementById(prefix+'Ext').className    = 'file-ext-badge ' + ext;
    document.getElementById(prefix+'Name').textContent = file.name;
    document.getElementById(prefix+'Size').textContent = size;
    document.getElementById(prefix+'Placeholder').classList.add('hidden');
    document.getElementById(prefix+'Preview').classList.remove('hidden');
}

function clearFile(prefix) {
    const id = prefix === 'main' ? 'fileMain' : 'file4m';
    document.getElementById(id).value = '';
    document.getElementById(prefix+'Placeholder').classList.remove('hidden');
    document.getElementById(prefix+'Preview').classList.add('hidden');
}

function showToast(icon, title) {
    Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true, icon, title });
}

feather.replace();
</script>
@endpush

@endsection