@extends('layouts.app')

@section('title', 'Document Detail - ' . $document->document_number)
@section('page-title', 'DOCUMENT DETAIL')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Document Detail')

@section('content')

@php
    $status = $document->status ?? 'Draft';

    $badgeClass = match($status) {
        'Submitted'            => 'bg-gray-100 text-gray-600 border border-gray-300',
        'Resubmitted'        => 'bg-blue-50 text-blue-700 border border-blue-200',
        'Approved'         => 'bg-green-50 text-green-700 border border-green-200',
        'Returned by SPV'  => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Returned by MR'   => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Rejected'         => 'bg-red-50 text-red-700 border border-red-200',
        'Published'        => 'bg-teal-50 text-teal-700 border border-teal-200',
        default            => 'bg-gray-100 text-gray-600 border border-gray-300',
    };

    $submissionBadge = match($document->submission_type ?? '') {
        'New Release' => 'bg-green-50 text-green-700 border border-green-200',
        'Revision'    => 'bg-purple-50 text-purple-700 border border-purple-200',
        'Obsolete'    => 'bg-red-50 text-red-700 border border-red-200',
        default       => 'bg-gray-100 text-gray-600',
    };

    // Role checks
    $isOwner      = $document->created_by === auth()->id();
   $isSPVTarget = auth()->user()
    ->departments->contains('id', $document->department_id)
    && auth()->user()->roles->contains('name', 'Supervisor Special Access');
    $isMR         = auth()->user()->departments->contains('name', 'Management Representative');

    // Flow steps & active index
    $flowSteps = ['Submitted', 'Approved', 'Published'];
    $flowIndex = match($status) {
        'Submitted', 'Resubmitted', 'Returned by SPV' => 0,
        'Approved', 'Returned by MR', 'Rejected' => 1,
        'Published'                 => 2,
        default                     => 0,
    };
@endphp

{{-- ===================== HEADER ===================== --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 mb-4">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

        {{-- Title & meta --}}
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                    {{ $document->document_number }}
                </h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                    {{ $status }}
                </span>
                
            </div>

            <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                    <i data-feather="user" class="w-3.5 h-3.5"></i>
                    {{ $document->createdBy->name ?? 'Unknown' }}
                </span>
              <span class="flex items-center gap-1">
    <i data-feather="briefcase" class="w-3.5 h-3.5"></i>
    {{ optional($document->createdBy?->departments->first())->name ?? '-' }}
</span>
            </div>
        </div>

        {{-- Back button --}}
        <a href="{{ route('mr.doc.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition self-start">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back
        </a>
    </div>
</div>

{{-- ===================== FLOW BAR ===================== --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 mb-4">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Document Flow</p>

    <div class="flex items-center">
        @foreach($flowSteps as $i => $step)

            {{-- Connector line --}}
            @if($i > 0)
                <div class="flex-1 h-0.5 mx-1
                    {{ $i <= $flowIndex && !in_array($status, ['Returned by SPV','Returned by MR','Rejected'])
                        ? 'bg-green-500' : 'bg-gray-200' }}">
                </div>
            @endif

            {{-- Step --}}
            <div class="flex flex-col items-center gap-1 min-w-[64px]">

                {{-- Dot --}}
                @if($i < $flowIndex && !in_array($status, ['Returned by SPV','Returned by MR','Rejected']))
                    {{-- Done --}}
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold shadow">
                        <i data-feather="check" class="w-4 h-4"></i>
                    </div>
                @elseif($i === $flowIndex)
                    @if($status === 'Returned by SPV' || $status === 'Returned by MR')
                        <div class="w-8 h-8 rounded-full bg-amber-400 flex items-center justify-center text-white text-xs font-bold ring-2 ring-amber-200 shadow">
                            <i data-feather="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </div>
                    @elseif($status === 'Rejected')
                        <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white text-xs font-bold ring-2 ring-red-200 shadow">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-4 ring-blue-100 shadow">
                            {{ $i + 1 }}
                        </div>
                    @endif
                @else
                    <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-gray-400 text-xs font-medium">
                        {{ $i + 1 }}
                    </div>
                @endif

                {{-- Label --}}
                <span class="text-[11px] text-center font-medium leading-tight
                    @if($i === $flowIndex && in_array($status, ['Returned by SPV','Returned by MR'])) text-amber-600
                    @elseif($i === $flowIndex && $status === 'Rejected') text-red-600
                    @elseif($i === $flowIndex) text-blue-600
                    @elseif($i < $flowIndex && !in_array($status, ['Returned by SPV','Returned by MR','Rejected'])) text-green-600
                    @else text-gray-400 @endif">

                    @if($i === $flowIndex && in_array($status, ['Returned by SPV','Returned by MR','Rejected']))
                        {{ $status }}
                    @else
                        {{ $step }}
                    @endif
                </span>
            </div>

        @endforeach
    </div>
</div>

{{-- ===================== MAIN LAYOUT ===================== --}}
<div class="flex flex-col lg:flex-row gap-4">

    {{-- =========== LEFT: Document Info =========== --}}
    <div class="w-full lg:w-2/3 flex flex-col gap-4">

        {{-- Document Information --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            
           <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
    
    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
        Document Information
    </h2>

    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $submissionBadge }}">
        {{ $document->submission_type }}
    </span>

</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Document Title</p>
                    <p class="text-base font-semibold text-gray-900">{{ $document->document_title ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Document Type</p>
                    <p class="text-sm text-gray-800">{{ $document->document_type ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Revision No.</p>
                    <p class="text-sm text-gray-800">{{ $document->revision?->revision_number ?? '–' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Department Destination</p>
                    <p class="text-sm text-gray-800">{{ $document->department->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">4M Attachment</p>
                   @if($document->need_4m)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Yes</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">No</span>
                    @endif
                </div>
            </div>

            <div>
                <p class="text-xs text-gray-400 mb-1">Reason for Submission</p>
                <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-sm text-gray-700">
                    {{ $document->reason ?? 'No reason provided.' }}
                </div>
            </div>

            @if($document->submission_type === 'Revision')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Before Changes</p>
                    <div class="bg-red-50 border border-red-100 rounded-lg px-4 py-3 text-sm text-gray-700">
                       {{ $document->revision?->before_change ?? '–' }}
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">After Changes</p>
                    <div class="bg-green-50 border border-green-100 rounded-lg px-4 py-3 text-sm text-gray-700">
                       {{ $document->revision?->after_change ?? '–' }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Files --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4 pb-3 border-b border-gray-100">
                Document Files
            </h2>

       @php
// =========================
// NORMALIZE DOC TYPE
// =========================
$docType = strtolower(str_replace(' ', '_', trim($document->document_type)));

// =========================
// AMBIL DEPT DARI CREATED_BY
// =========================
$deptFrom = optional($document->createdBy->departments->first())->id;

// fallback biar gak null
$deptFrom = $deptFrom ?? 0;

// =========================
// BUILD PATH
// =========================
$mainPath = $document->file_path 
    ? "documents/{$docType}/{$deptFrom}/{$document->file_path}" 
    : null;

$file4mPath = $document->file_4m_path 
    ? "documents/{$docType}/{$deptFrom}/{$document->file_4m_path}" 
    : null;
@endphp


{{-- ========================= --}}
{{-- Main file --}}
{{-- ========================= --}}
@if($document->file_path)
<p class="text-xs text-gray-400 mb-2">Main Document File</p>

<div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 mb-4">
    
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-bold">
            {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
        </div>

        <div>
            <p class="text-sm font-medium text-gray-800">
                {{ $document->file_path }}
            </p>
            <p class="text-xs text-gray-400">
                Format: .{{ pathinfo($document->file_path, PATHINFO_EXTENSION) }}
            </p>
        </div>
    </div>

    <a href="{{ asset($mainPath) }}" download="{{ $document->file_path }}"
       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
        <i data-feather="download" class="w-4 h-4"></i> Download
    </a>
</div>
@endif


{{-- ========================= --}}
{{-- 4M file --}}
{{-- ========================= --}}
@if($document->need_4m && $document->file_4m_path)
<p class="text-xs text-gray-400 mb-2">4M Attachment</p>

<div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
    
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center text-green-700 text-[10px] font-bold">
            {{ strtoupper(pathinfo($document->file_4m_path, PATHINFO_EXTENSION)) }}
        </div>

        <div>
            <p class="text-sm font-medium text-gray-800">
                {{ $document->file_4m_path }}
            </p>
            <p class="text-xs text-gray-400">
                Format: .{{ pathinfo($document->file_4m_path, PATHINFO_EXTENSION) }}
            </p>
        </div>
    </div>

    <a href="{{ asset($file4mPath) }}" download="{{ $document->file_4m_path }}"
       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
        <i data-feather="download" class="w-4 h-4"></i> Download
    </a>
</div>
@endif
        </div>

        {{-- Copy Distribution --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4 pb-3 border-b border-gray-100">
                Copy Distribution (Application for Copies)
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <th class="px-3 py-2 text-left border border-gray-200">Department</th>
                            <th class="px-3 py-2 text-center border border-gray-200">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="copiesTableBody">
                        @forelse($document->copies ?? [] as $copy)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border border-gray-200">{{ $copy->department->name ?? '-' }}</td>
                            <td class="px-3 py-2 border border-gray-200 text-center">{{ $copy->qty }} sheet</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-400 text-sm">No copy distribution recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- =========== RIGHT: Sidebar =========== --}}
    <div class="w-full lg:w-1/3 flex flex-col gap-4">

        {{-- ---- ACTIONS ---- --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4 pb-3 border-b border-gray-100">
                Actions
            </h2>

            <div class="flex flex-col gap-2">

                {{-- OWNER: Draft → Submit --}}
                @if($isOwner && $status === 'Draft')
                    <button onclick="submitDOC({{ $document->id }})"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="send" class="w-4 h-4"></i> Submit for Approval
                    </button>
                    <a href="#"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        <i data-feather="edit-2" class="w-4 h-4"></i> Edit Draft
                    </a>
                @endif

                {{-- OWNER: Returned → Resubmit --}}
                @if($isOwner && in_array($status, ['Returned by SPV', 'Returned by MR']))
                    <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs text-amber-700 mb-1 flex items-start gap-2">
                        <i data-feather="alert-circle" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0"></i>
                        <span>Document was returned. Please revise and resubmit.</span>
                    </div>
                    <a href="{{ route('mr.doc.edit', $document->id) }}"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="refresh-ccw" class="w-4 h-4"></i> Resubmit Document
</a>
                @endif

                {{-- SPV: Submitted → Approve / Return --}}
                @if($isSPVTarget && in_array ($status, ['Submitted', 'Resubmitted']))
                    <button onclick="approveDOC({{ $document->id }})"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="check-circle" class="w-4 h-4"></i> Approve
                    </button>
                    <button onclick="openReturnModal({{ $document->id }}, 'spv')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="rotate-ccw" class="w-4 h-4"></i> Return to Requestor
                    </button>
                @endif

                {{-- MR: Approved → Authorize / Return / Reject --}}
                @if($isMR && $status === 'Approved')
                    <button onclick="authorizedDOC({{ $document->id }})"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="shield" class="w-4 h-4"></i> Authorize & Publish
                    </button>
                    <button onclick="openReturnModal({{ $document->id }}, 'mr')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="rotate-ccw" class="w-4 h-4"></i> Return to Owner
                    </button>
                    <button onclick="rejectDOC({{ $document->id }})"
                        class="w-full flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        <i data-feather="x-circle" class="w-4 h-4"></i> Reject Document
                    </button>
                @endif

                {{-- No action available --}}
                @if(
                    !($isOwner && in_array($status, ['Draft', 'Returned by SPV', 'Returned by MR'])) &&
                    !($isSPVTarget && in_array($status, ['Submitted','Resubmitted'])) &&
                    !($isMR && $status === 'Approved')
                )
                    <p class="text-sm text-gray-400 text-center py-2">No actions available for your role at this stage.</p>
                @endif

            </div>
        </div>

      

        {{-- ---- INTERNAL NOTES ---- --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4 pb-3 border-b border-gray-100">
                Internal Notes
            </h2>

            <div class="flex flex-col gap-0 max-h-80 overflow-y-auto pr-1" id="noteList">
                @forelse($document->notes ?? [] as $note)
                <div class="flex gap-3 py-3 border-b border-gray-100 last:border-0">
                    <img src="{{ $note->user->avatar ? asset('storage/'.$note->user->avatar) : asset('img/avatar-dummy.png') }}"
                         alt="{{ $note->user->name }}"
                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-800">{{ $note->user->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($note->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="text-sm text-gray-700 mt-1 prose prose-sm max-w-none">
                            {!! $note->note !!}
                        </div>
                        @if($note->image)
                            <img src="{{ asset('document/notes/'.$note->image) }}" alt="note image" class="mt-2 rounded-lg max-w-full max-h-32 object-cover border border-gray-200">
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No notes yet.</p>
                @endforelse
            </div>
        </div>

          {{-- ---- APPROVAL TRACK ---- --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4 pb-3 border-b border-gray-100">
                Approval Track
            </h2>

            <div class="flex flex-col gap-4">

                {{-- Submitted --}}
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                        {{ $document->created_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                        <i data-feather="{{ $document->created_at ? 'check' : 'clock' }}" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Submitted by</p>
                        <p class="text-sm font-medium text-gray-800">{{ $document->createdBy->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400">{{ $document->created_at?->format('d M Y, H:i') ?? '–' }}</p>
                    </div>
                </div>

                @php
    $icon = 'clock';

    if ($status === 'Returned by SPV') {
        $icon = 'rotate-ccw';
    } elseif (in_array($status, ['Approved','Returned by MR','Rejected','Published'])) {
        $icon = 'check';
    }
@endphp

               {{-- SPV Approval --}}
<div class="flex items-start gap-3">
    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
        @if($status === 'Returned by SPV') bg-amber-100 text-amber-600
        @elseif(in_array($status, ['Approved','Returned by MR','Rejected','Published'])) bg-green-100 text-green-600
        @else bg-gray-100 text-gray-400 @endif">

        <i data-feather="{{ $icon }}" class="w-3.5 h-3.5"></i>
    </div>

    <div>
        {{-- LABEL --}}
        <p class="text-xs font-semibold text-gray-500">
            @if($status === 'Returned by SPV')
                Returned by SPV
            @elseif(in_array($status, ['Approved','Returned by MR','Rejected','Published']))
                Approved by SPV
            @else
                SPV Approval
            @endif
        </p>

        {{-- CONTENT --}}
        @if($status === 'Returned by SPV' && $document->returnedBy)
            <p class="text-sm font-medium text-gray-800">
                {{ $document->returnedBy->name }}
            </p>
            <p class="text-xs text-gray-400">
                {{ \Carbon\Carbon::parse($document->returned_at)->format('d M Y, H:i') }}
            </p>

        @elseif($document->approvedBy)
            <p class="text-sm font-medium text-gray-800">
                {{ $document->approvedBy->name }}
            </p>
            <p class="text-xs text-gray-400">
                {{ \Carbon\Carbon::parse($document->approved_at)->format('d M Y, H:i') }}
            </p>

        @else
            <p class="text-xs text-gray-400">Pending</p>
        @endif
    </div>
</div>

     @php
    $iconMR = 'clock';

    if ($status === 'Returned by MR') {
        $iconMR = 'rotate-ccw';
    } elseif ($status === 'Published') {
        $iconMR = 'check';
    }
     elseif ($status === 'Rejected') {
        $iconMR = 'x';
    }
@endphp

                {{-- MR Authorization --}}
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                        @if($status === 'Rejected') bg-red-100 text-red-600
                        @elseif($status === 'Returned by MR') bg-amber-100 text-amber-600
                        @elseif($status === 'Published') bg-green-100 text-green-600
                        @else bg-gray-100 text-gray-400 @endif">
                        <i data-feather="{{ $iconMR }}" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">
                            @if($status === 'Rejected') Rejected by MR
                            @elseif($status === 'Returned by MR') Returned by MR
                            @elseif($status === 'Published') Authorized by MR
                            @else MR Authorization @endif
                        </p>
                        @if($document->authorizedBy ?? false)
                            <p class="text-sm font-medium text-gray-800">{{ $document->authorizedBy->name }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($document->authorized_at)->format('d M Y, H:i') }}</p>
                        @elseif($document->rejectedBy ?? false)
                            <p class="text-sm font-medium text-red-700">{{ $document->rejectedBy->name }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($document->rejected_at)->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-red-500 mt-1">{{ $document->rejected_reason }}</p>
                        @else
                            <p class="text-xs text-gray-400">Pending</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- ======================== MODAL: Return ======================== --}}
<div id="returnModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i data-feather="rotate-ccw" class="w-4 h-4"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Return Document</h2>
        </div>
        <form id="returnForm">
            @csrf
            <input type="hidden" name="registration_id" id="return_document_id">
            <input type="hidden" name="return_role" id="return_role">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Return</label>
                <textarea name="return_reason" id="return_reason" rows="4" required
                    placeholder="Please explain what needs to be revised..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeReturnModal()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-medium">Return</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: Reject ======================== --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <i data-feather="x-circle" class="w-4 h-4"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Reject Document</h2>
        </div>
        <form id="rejectForm">
            @csrf
            <input type="hidden" name="document_id" id="reject_document_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection</label>
                <textarea name="rejected_reason" id="rejected_reason" rows="4" required
                    placeholder="e.g. Duplicate request, invalid document, does not meet standards..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1">This reason will be visible to the document owner.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium">Reject</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: Resubmit ======================== --}}
<div id="resubmitModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-feather="refresh-ccw" class="w-4 h-4"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Resubmit Document</h2>
        </div>
        <form id="resubmitForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Revised File</label>
                <input type="file" name="file" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeResubmitModal()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">Submit</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        icon: icon,
        title: title
    });
}

// ========================
// SUBMIT (Draft → Submitted)
// ========================
function submitDOC(id) {
    Swal.fire({
        title: 'Submit this document?',
        text: 'Document will be sent to your Supervisor for approval.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        confirmButtonText: 'Yes, Submit'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/submit`, { _token: '{{ csrf_token() }}' }, function(res) {
                showToast('success', res.message || 'Document submitted!');
                setTimeout(() => location.reload(), 1500);
            }).fail(() => showToast('error', 'Failed to submit document.'));
        }
    });
}

// ========================
// APPROVE (SPV)
// ========================
function approveDOC(id) {
    Swal.fire({
        title: 'Approve this document?',
        text: 'Document will be forwarded to Management Representative.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Yes, Approve'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/approve`, { _token: '{{ csrf_token() }}' }, function(res) {
                showToast('success', res.message || 'Document approved!');
                setTimeout(() => location.reload(), 1500);
            }).fail(() => showToast('error', 'Failed to approve document.'));
        }
    });
}

// ========================
// AUTHORIZE (MR)
// ========================
function authorizedDOC(id) {
    Swal.fire({
        title: 'Authorize & Publish?',
        text: 'Document will be published and distributed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f766e',
        confirmButtonText: 'Yes, Authorize'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/authorized`, { _token: '{{ csrf_token() }}' }, function(res) {
                showToast('success', res.message || 'Document authorized!');
                setTimeout(() => location.reload(), 1500);
            }).fail(() => showToast('error', 'Failed to authorize document.'));
        }
    });
}

// ========================
// RETURN MODAL
// ========================
function openReturnModal(id, role) {
    $('#return_document_id').val(id);
    $('#return_role').val(role);
    $('#return_reason').val('');
    $('#returnModal').removeClass('hidden').addClass('flex');
}

function closeReturnModal() {
    $('#returnModal').addClass('hidden').removeClass('flex');
}

$('#returnForm').on('submit', function(e) {
    e.preventDefault();

    const id   = $('#return_document_id').val();
    const role = $('#return_role').val();
    const note = $('#return_reason').val();

    if (!note.trim()) {
        showToast('error', 'Note wajib diisi!');
        return;
    }

    $.ajax({
        url: `/mr/document/return/${id}`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            note: note,
            role: role
        },
        success: function(res) {
            if (res.success) {
                showToast('success', res.message);
                closeReturnModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', res.message);
            }
        },
        error: function() {
            showToast('error', 'Server error.');
        }
    });
});

// ========================
// REJECT MODAL
// ========================
function rejectDOC(id) {
    $('#reject_document_id').val(id);
    $('#rejected_reason').val('');
    $('#rejectModal').removeClass('hidden').addClass('flex');
}

function closeRejectModal() {
    $('#rejectModal').addClass('hidden').removeClass('flex');
}

$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#reject_document_id').val();

    $.post(`/mr/document/${id}/reject`, $(this).serialize(), function(res) {
        if (res.success) {
            showToast('success', res.message || 'Document rejected.');
            closeRejectModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', res.message || 'Failed to reject document.');
        }
    }).fail(() => showToast('error', 'Server error.'));
});

// ========================
// RESUBMIT MODAL
// ========================
function resubmitDOC(id) {
    window._resubmitId = id;
    $('#resubmitModal').removeClass('hidden').addClass('flex');
}

function closeResubmitModal() {
    $('#resubmitModal').addClass('hidden').removeClass('flex');
    $('#resubmitForm')[0].reset();
}

$('#resubmitForm').on('submit', function(e) {
    e.preventDefault();
    const id = window._resubmitId;
    const formData = new FormData(this);

    $.ajax({
        url: `/mr/document/${id}/resubmit`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                showToast('success', res.message || 'Document resubmitted!');
                closeResubmitModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', res.message || 'Failed to resubmit.');
            }
        },
        error: () => showToast('error', 'Server error.')
    });
});

// ========================
// NOTE FORM
// ========================
$('#noteForm').on('submit', function(e) {
    e.preventDefault();
    const url     = $(this).attr('action');
    const formData = new FormData(this);

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                showToast('success', 'Note added successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', 'Failed to add note.');
            }
        },
        error: () => showToast('error', 'Server error.')
    });
});

// Close modals on backdrop click
['returnModal','rejectModal','resubmitModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});

feather.replace();
</script>
@endpush

@endsection