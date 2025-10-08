@extends('layouts.app')

@section('title', 'Document Submission Detail')
@section('page-title', 'Document Submission Detail')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Document Submission Detail')

@section('content')
@php
$latest = $document->latestRevision;
$status = $document->status ?? '';
$colorClass = match($status) {
    'Draft'    => 'text-gray-600 bg-gray-200',
    'Approved' => 'text-white bg-yellow-600',
    'Under Review' => 'text-white bg-indigo-600',
    'Resubmitted' => 'text-white bg-blue-600',
    'Published' => 'text-white bg-green-600',
    'Partially Socialized' => 'text-white bg-purple-600',
    'Closed' => 'text-white bg-teal-600',
    'Revision' => 'text-white bg-lime-600',
    'Rejected' => 'text-white bg-red-600',
    'Obsolete' => 'text-white bg-orange-600',
    default    => 'text-gray-700 bg-gray-100',
};
$remarkClass = match($latest->remark ?? '') {
    'New Release' => 'text-green-600',
    'Revision' => 'text-purple-600',
    'Obsolete' => 'text-red-600',
    default => 'text-gray-800'
};
$latestRevision = $document->revisions->sortByDesc('version')->first();
@endphp

<div class="w-full bg-white shadow-md rounded-xl p-6 space-y-4 mb-2">
  <div class="mb-6">
    <!-- BARIS ATAS: Document Number + Status + Version -->
    <div class="flex items-center gap-4 justify-between mb-2">
        <div class="flex items-center gap-2">
            <h1 class="text-3xl font-extrabold text-gray-900 uppercase">
                {{ $document->document_number }}
            </h1>
            <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
                {{ strtoupper($document->status ?? '-') }}
            </span>
        </div>
<!-- Version dropdown -->
<div class="relative inline-block text-left">
    <button id="revisionDropdownButton" data-current-version="{{ $document->current_version }}" type="button"
        class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-1 hover:bg-blue-200 transition">
        Document Version {{ str_pad($document->current_version, 2, '0', STR_PAD_LEFT) }}
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

   <div id="revisionDropdownMenu"
    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 hidden">

   @php
// versi yang sedang aktif di halaman saat ini
$currentVersion = $document->current_version;

// Urutkan revisi ascending
$revisions = $document->revisions->sortBy('version');
@endphp

@foreach ($revisions as $rev)
    @if($rev->version)
        @php
            $copiesJson = $rev->copies->map(function($c) use ($rev) {
                return [
                    'department' => $c->department->name ?? '-',
                    'qty' => $c->qty,
                    'date' => $c->date ? \Carbon\Carbon::parse($c->date)->format('d-m-Y') : null,
                    'socialized' => $c->socialized->name ?? 'Not yet Socialized',
                    'document_revision_id' => $rev->id,
                ];
            });
        @endphp

        <a href="#"
           class="revision-item block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
           data-version="{{ $rev->version }}"
           data-remark="{{ $rev->remark }}"
           data-created-by="{{ $rev->created_by }}"
           data-created-name="{{ $rev->requestor?->name ?? '' }}"
           data-created-at="{{ $rev->created_at }}"
           data-approved-by="{{ $rev->approved_by }}"
           data-approved-name="{{ $rev->approval?->name ?? '' }}"
            data-approved-at="{{ $rev->approved_at }}"
   data-rejected-by="{{ $rev->rejected_by }}"
   data-rejected-name="{{ $rev->reject?->name ?? '' }}"
   data-rejected-at="{{ $rev->rejected_at }}"
   data-review-by="{{ $rev->review_by }}"
   data-review-name="{{ $rev->review?->name ?? '' }}"
   data-review-at="{{ $rev->review_at }}"
   data-authorized-by="{{ $rev->authorized_by }}"
   data-authorized-name="{{ $rev->authorized?->name ?? '' }}"
   data-authorized-at="{{ $rev->authorized_at }}"
           data-file="{{ $rev->file ? asset('storage/' . $rev->file) : '' }}"
           data-file-name="{{ $rev->file ? basename($rev->file) : '' }}"
           data-file-ext="{{ $rev->file ? strtolower(pathinfo($rev->file, PATHINFO_EXTENSION)) : '' }}"
           data-file-4m="{{ $rev->file_4m ? asset('storage/' . $rev->file_4m) : '' }}"
           data-file-4m-name="{{ $rev->file_4m ? basename($rev->file_4m) : '' }}"
           data-file-4m-ext="{{ $rev->file_4m ? strtolower(pathinfo($rev->file_4m, PATHINFO_EXTENSION)) : '' }}"
           data-reason="{{ $rev->reason_revision ?? '' }}"
           data-obsolete-reason="{{ $rev->obsolete_reason ?? '' }}"
           data-revision-id="{{ $rev->id }}"
           data-copies='@json($copiesJson)'>
            Document Version {{ str_pad($rev->version, 2, '0', STR_PAD_LEFT) }}
        </a>
    @endif
@endforeach





</div>

</div>


    </div>

   <!-- BARIS BAWAH: Title, Requestor, Created At -->
<div id="revisionInfo" class="flex flex-col gap-1 text-gray-600 mt-2">
    <div class="text-sm flex gap-4 items-center">
        <span class="flex items-center gap-1" id="revisionUser">
            <i data-feather="user" class="w-4 h-4"></i>
            {{ $latestRevision->requestor->name ?? 'Unknown' }}
        </span>
        <span class="flex items-center gap-1" id="revisionDate">
            <i data-feather="calendar" class="w-4 h-4"></i>
            {{ $latestRevision->created_at->format('d M Y H:i') }}
        </span>
    </div>
</div>

</div>

<hr>

<div class="flex gap-6 mb-2">
  <!-- MAIN CONTENT: PO Info + Items -->
  <div class="w-2/3 flex flex-col space-y-6">
    
    <!-- Purchase Order Information -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
       <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-semibold text-gray-700">Document Information</h3>
       @php
    $remarkClasses = [
        'New Release' => 'underline text-green-600',
        'Revision' => 'underline text-purple-600',
        'Obsolete' => 'underline text-red-600',
    ];

    $remarkClass = $remarkClasses[$document->remark] ?? 'text-gray-800';
@endphp

<span id="remark" class="inline-block {{ $remarkClass }} px-2 py-1 rounded-lg text-sm">
    {{ $rev->remark }}
</span>

      </div>

      <div class="text-sm mb-8">
         <div class="grid grid-cols-2 gap-x-12 gap-y-6 text-sm mb-8">
            <div class="col-span-2">
          <div class="text-gray-500 font-medium mb-1">Document Title</div>
          <div class="text-gray-800 font-semibold">{{ $document->title ?? 'No Title' }}</div>
            </div>
            <div>
          <div class="text-gray-500 font-medium mb-1">Document Type</div>
          <div class="text-gray-800">{{ $document->document_type ?? 'No Type' }}</div>
            </div>
               <div>
          <div class="text-gray-500 font-medium mb-1">Document Version</div>
          <div id="documentVersion" class="text-gray-800">{{ $document->current_version ?? '00' }}</div>
            </div>
        </div>
        <div class="text-gray-500 font-medium mb-2">Document File</div>

@php
    $filename = $latest->file ? basename($latest->file) : basename($document->file);
    $extension = strtolower(pathinfo($latest->file ?? $document->file, PATHINFO_EXTENSION));

    $filename4m = $latest->file_4m ? basename($latest->file_4m) : ($document->file_4m ? basename($document->file_4m) : null);
    $extension4m = $latest->file_4m ? strtolower(pathinfo($latest->file_4m, PATHINFO_EXTENSION)) : ($document->file_4m ? strtolower(pathinfo($document->file_4m, PATHINFO_EXTENSION)) : null);
@endphp

{{-- File Utama --}}
<div id="fileSection" class="flex items-center justify-between bg-gray-100 p-3 rounded shadow-sm mb-4">
    <div>
        <p id="documentFileName" class="text-sm font-medium text-gray-800">{{ $filename }}</p>
        <p class="text-xs text-gray-500">Format: .<span id="documentFileExt">{{ $extension }}</span></p>
    </div>
    <div class="flex gap-2">
        <a id="documentFileLink" href="{{ asset('storage/' . ($latest->file ?? $document->file)) }}" download class="inline-flex items-center text-green-600 hover:underline">
            <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
        </a>
    </div>
</div>

{{-- Opsional: File 4M --}}
@if($filename4m)
<div id="fileSection4m" class="flex items-center justify-between bg-gray-100 p-3 rounded shadow-sm mb-4">
    <div>
        <p id="documentFile4MName" class="text-sm font-medium text-gray-800">{{ $filename4m }}</p>
        <p class="text-xs text-gray-500">Format: .<span id="documentFile4MExt">{{ $extension4m }}</span></p>
    </div>
    <div class="flex gap-2">
        <a id="documentFile4MLink" href="{{ asset('storage/' . ($latest->file_4m ?? $document->file_4m)) }}" download class="inline-flex items-center text-green-600 hover:underline">
            <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
        </a>
    </div>
</div>
@endif




<div class="text-gray-500 font-medium mb-2">Reason for Submission</div>
<div class="flex items-center justify-between bg-blue-50 p-3 text-gray-800 rounded shadow-sm mb-4">
    {{ $document->reason ?? 'No Reason' }}
</div>

  @if($document->revisions->count() > 0)
    @php
        $latestRevision = $document->revisions->last();
    @endphp
    <div id="reasonRevisionLabel" class="text-gray-500 font-medium mb-2 hidden">Reason for Revision</div>
    <div id="reasonRevisionContainer" 
         class="{{ $latestRevision->reason_revision ? '' : 'hidden' }} flex items-center justify-between bg-blue-50 p-3 text-gray-800 rounded shadow-sm mb-4">
        <span class="reason-text">
            {{ $latestRevision->reason_revision ?? '' }}
        </span>
    </div>
@endif



    </div>
            <div class="flex justify-between items-center mb-2">
    <h3 class="text-xl font-semibold text-gray-700">Application For Copies</h3>
    <i data-feather="users" class="text-gray-700 w-5 h-5"></i>
  </div>
  <hr class="my-4">
   
@if($document && $document->copies->count())
  @php
    // Ambil ID revisi terbaru, jika ada
    $latestRevisionId = $document->revisions->sortByDesc('version')->first()->id ?? null;

    // Filter copies hanya untuk revisi terbaru
    $copiesToShow = $document->copies->filter(function($copy) use ($latestRevisionId) {
        return $copy->document_revision_id == $latestRevisionId;
    });
  @endphp

  <table id="copiesTable" class="w-full text-left border-collapse">
    <thead>
      <tr class="bg-blue-500 text-white">
        <th class="p-2 border">Department</th>
        <th class="p-2 border text-center">Qty</th>
        <th class="p-2 border">Socialization Date</th>
        <th class="p-2 border">Socialize by</th>
      </tr>
    </thead>
    <tbody>
      @forelse($copiesToShow as $copy)
        <tr class="copy-row" data-version="{{ $copy->document_revision_id ?? 'original' }}">
          <td class="p-2 border">{{ $copy->department->name ?? '-' }}</td>
          <td class="p-2 border text-center">{{ $copy->qty }} Sheet</td>
          <td class="p-2 border">
            {{ optional($copy->date ? \Carbon\Carbon::parse($copy->date) : null)->format('d-m-Y') ?? 'Not yet socialized' }}
          </td>
          <td class="p-2 border">{{ $copy->socialized->name ?? 'Not yet Socialized' }}</td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-gray-500 p-2 text-center">No copies distributed.</td></tr>
      @endforelse
    </tbody>
  </table>
@else
  <p class="text-gray-500 mt-3">No copies distributed.</p>
@endif



    </div>
  </div>

  <!-- SIDEBAR: Order History + Summary -->
  <div class="w-1/3 flex flex-col space-y-6">
    <!-- Order History -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
  <div class="flex justify-between items-center mb-2">
    <h3 class="text-xl font-semibold text-gray-700">Internal Notes</h3>
    <i data-feather="file" class="text-gray-700 w-5 h-5"></i>
</div>
<hr class="my-4">
<div class="{{ $document->status === 'Approved' ? 'max-h-[30vh]' : 'max-h-[60vh]' }} overflow-y-auto pr-2">
    {{-- Reject --}}
    @if($document->status === 'Rejected' && $document->rejected_reason)
        <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
            <img src="{{ $document->reject->avatar ? asset('storage/' . $document->approval->avatar) : asset('img/avatar-dummy.png') }}" alt="Avatar" class="w-8 h-8 rounded-full">
            <div>
                <div class="font-semibold">{{ $document->reject->name ?? 'Unknown' }}</div>
                <div class="text-xs text-gray-500">Rejected • {{ \Carbon\Carbon::parse($document->rejected_at)->format('d M Y H:i') }}</div>
                <div class="mt-1 text-sm text-red-600"><strong>Reason:</strong> {{ $document->rejected_reason }}</div>
            </div>
        </div>
    @endif

    {{-- List Notes --}}
    <div id="notes-list">
        @forelse($document->notes as $note)
            <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
                <img src="{{ $note->user->avatar ? asset('storage/'.$note->user->avatar) : asset('img/avatar-dummy.png') }}" class="w-8 h-8 rounded-full border border-gray-800">
                <div>
                    <div class="font-semibold">{{ $note->user->name }}</div>
                    <div class="text-xs text-gray-500">Note • {{ \Carbon\Carbon::parse($note->created_at)->format('d M Y H:i') }}</div>
                    <div class="mt-1 text-sm">{!! $note->note !!}</div>
                    @if($note->image)
                        <a href="{{ asset('storage/'.$note->image) }}" target="_blank">
                            <img src="{{ asset('storage/'.$note->image) }}" class="mt-2 max-w-full h-auto rounded object-contain cursor-pointer">
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-gray-500 text-center py-4 italic">No notes added.</div>
        @endforelse
    </div>
</div>


   @if($document->status === 'Under Review' && auth()->user()->departments->contains('name', 'Management Representative'))
    <form id="noteForm" action="{{ route('mr.add.note', $document->id) }}"  method="POST" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="document_id" value="{{ $document->id }}">
        <textarea id="note" name="note" class="w-full"></textarea>

        <div class="mt-3">
            <input type="file" name="image" accept="image/*" class="border rounded p-1">
        </div>

        <button type="submit" class="mt-3 bg-purple-600 text-white px-4 py-2 rounded-lg">Add Note</button>
    </form>

@endif
</div>
    </div>
  </div>
    <!-- Requestor 
    <div class="flex items-center space-x-2">
        <i data-feather="check-circle" class="w-6 h-6 text-green-500"></i>
        <div>
            <div class="text-sm font-semibold text-gray-800">Submitted by {{ $rev->requestor->name }}</div>
            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rev->created_at)->format('d M Y H:i') }}</div>
        </div>
    </div>-->

  @php
// Jika belum ada versi yang dipilih, pakai current_version
$currentRevision = $document->revisions->where('version', $document->current_version)->first();
@endphp

<div id="approval-status" class="flex items-center justify-start space-x-12 p-6 bg-gray-200 rounded-lg">
    {{-- Approved / Rejected --}}
    <div class="flex items-center space-x-2 mb-2">
        @php
            $isApproved = $currentRevision->approved_by !== null;
            $isRejected = $currentRevision->rejected_by !== null;
        @endphp
        <i data-feather="{{ $isApproved ? 'check-circle' : ($isRejected ? 'x-circle' : 'x-circle') }}" 
           class="w-6 h-6 text-{{ $isApproved ? 'green-500' : ($isRejected ? 'red-500' : 'gray-400') }}"></i>
        <div>
            <div class="text-sm font-semibold text-gray-800">
                @if($isApproved)
                    Approved by {{ $currentRevision->approval?->name ?? 'Unknown' }}
                @elseif($isRejected)
                    Rejected by {{ $currentRevision->reject?->name ?? 'Unknown' }}
                @else
                    Not yet Approved
                @endif
            </div>
            <div class="text-xs text-gray-500">
                @if($isApproved)
                    {{ \Carbon\Carbon::parse($currentRevision->approved_at)->format('d M Y H:i') }}
                @elseif($isRejected)
                    {{ \Carbon\Carbon::parse($currentRevision->rejected_at)->format('d M Y H:i') }}
                @else
                    Pending
                @endif
            </div>
        </div>
    </div>

    {{-- Review --}}
    <div class="flex items-center space-x-2 mb-2">
        <i data-feather="{{ $currentRevision->review_by ? 'check-circle' : 'x-circle' }}" 
           class="w-6 h-6 text-{{ $currentRevision->review_by ? 'green-500' : 'gray-400' }}"></i>
        <div>
            <div class="text-sm font-semibold text-gray-800">
                @if($currentRevision->review_by)
                    Review by {{ $currentRevision->review?->name ?? 'Unknown' }}
                @else
                    Not yet Reviewed
                @endif
            </div>
            <div class="text-xs text-gray-500">
                {{ $currentRevision->review_at ? \Carbon\Carbon::parse($currentRevision->review_at)->format('d M Y H:i') : 'Pending' }}
            </div>
        </div>
    </div>

    {{-- Authorized --}}
    <div class="flex items-center space-x-2">
        <i data-feather="{{ $currentRevision->authorized_by ? 'check-circle' : 'x-circle' }}" 
           class="w-6 h-6 text-{{ $currentRevision->authorized_by ? 'green-500' : 'gray-400' }}"></i>
        <div>
            <div class="text-sm font-semibold text-gray-800">
                @if($currentRevision->authorized_by)
                    Authorized by {{ $currentRevision->authorized?->name ?? 'Unknown' }}
                @else
                    Not yet Authorized
                @endif
            </div>
            <div class="text-xs text-gray-500">
                {{ $currentRevision->authorized_at ? \Carbon\Carbon::parse($currentRevision->authorized_at)->format('d M Y H:i') : 'Pending' }}
            </div>
        </div>
    </div>
</div>
<hr>
   <div class="flex justify-start space-x-2 mt-4">
    <a href="{{ route('mr.doc.index') }}" class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">← Back</a>
@if($document->status == 'Draft' && $hasSameDepartment && collect($userRoles)->intersect(['Supervisor Special Access', 'Manager Special Access'])->isNotEmpty())
    <button onclick="approveDOC({{ $document->id }})" class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="check-circle" class="w-4 h-4 inline"></i> <span>Approve</span>
    </button>
    <button onclick="rejectDOC({{ $document->id }})" class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-red-600 text-white rounded">
        <i data-feather="x-circle" class="w-4 h-4 inline"></i> <span>Reject</span>
    </button>
@endif



   @if(in_array('Management Representative', $userDepartments) && 
    ($document->status === 'Approved' || $document->status === 'Resubmitted'))
    <button onclick="reviewDOC({{ $document->id }})" 
        class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="zoom-in" class="w-4 h-4 inline"></i>
        <span>Review</span>
    </button>
@endif



  @if(in_array('Management Representative', $userDepartments) && $document->status === 'Under Review')
    <button onclick="authorizedDOC({{ $document->id }})"
        class="w-32 text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="edit-3" class="w-4 h-4 inline"></i>
        <span>Authorized</span>
    </button>
@endif

@if($rev->created_by === Auth::id() && $document->status === 'Under Review')
    <button onclick="resubmitDOC({{ $document->id }})"
        class="w-32 text-center flex gap-2 items-center px-4 py-2 bg-blue-600 text-white rounded">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i>
        <span>Resubmit</span>
    </button>
@endif


@if(in_array('Management Representative', $userDepartments) && $document->status === 'Closed')
   <a href="{{ route('mr.doc.rev', ['id' => $document->id]) }}" 
    class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-purple-600 text-white rounded">
    <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i>
    <span>Revision</span>
</a>

@endif

@if(in_array('Management Representative', $userDepartments) && $document->status === 'Published')
    <button onclick="updateDOC({{ $document->id }})" 
        class="w-28 text-center flex gap-2 items-center px-4 py-2 bg-teal-600 text-white rounded">
        <i data-feather="calendar" class="w-4 h-4 inline"></i>
        <span>Socialize</span>
    </button>
@endif
   </div>
</div>
 </div>
<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-2xl transform transition-all scale-95">
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-red-100 text-red-600 rounded-full">
               <i data-feather="alert-triangle"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Reject Document</h2>
        </div>

        <form id="rejectForm" class="space-y-4">
            @csrf
             <input type="hidden" name="document_id" id="reject_document_id">
            <!-- Reason -->
            <div>
                <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Reason for Rejection
                </label>
                <textarea 
                    name="rejected_reason" 
                    id="rejected_reason" 
                    rows="4" 
                    required
                   placeholder="e.g. Duplicate request, issue already resolved, invalid request details..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve future requests.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="closeRejectModal()"
                    class="px-4 py-2 rounded-lg bg-gray-300 border border-gray-300 text-white hover:bg-gray-400 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition"
                >
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="socializeModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
    <h2 class="text-lg font-bold mb-4">Socialize Document</h2>
    <form id="socializeForm">
         @csrf
      <input type="hidden" name="document_id" id="document_id">

      <div id="docCopiesContainer" class="mb-4">
        <!-- Data department & qty akan muncul di sini -->
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Close</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="socializeModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 " id="modalContent">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4 border-b pb-2">
      <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
        <i data-feather="calendar"></i>
        Socialize Document
      </h2>
      <button type="button" onclick="closeModal()" class="text-gray-500 hover:text-gray-700 transition">
        ✕
      </button>
    </div>

    <!-- Form -->
    <form id="socializeForm" class="space-y-4">
         @csrf
      <input type="hidden" name="document_id" id="document_id">

      <!-- Container untuk data department & qty -->
      <div id="docCopiesContainer" class="space-y-3">
        <!-- Data akan di-render di sini -->
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-3 pt-4 border-t">
        <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Cancel</button>
        <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Save</button>
      </div>
    </form>
  </div>
</div>

<div id="resubmitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4" id="resubmitTitle">Resubmit Document</h2>

        <form id="resubmitForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Upload File -->
            <div class="mb-4">
                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload New File
                </label>
                <input type="file" name="file" id="document_file"
                       class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                       required>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelResubmit"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
   ClassicEditor
    .create(document.querySelector('#note'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
            'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'mediaEmbed', '|',
            'undo', 'redo', 'code'
        ]
    })
    .then(editor => {
        $('#noteForm').on('submit', function (e) {
            e.preventDefault();

            // Ambil konten dari CKEditor
            let noteContent = editor.getData();
            if (!noteContent.trim()) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Note tidak boleh kosong!',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }

            // Ambil URL dan FormData
            let url = $(this).attr('action');
            let formData = new FormData(this);
            formData.append('content', noteContent);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        // Tambah note ke list
                        let noteItem = `
                            <div class="note-item p-3 border-b">
                                <div class="flex items-center mb-2">
                                    <img src="${data.note.avatar}" class="w-8 h-8 rounded-full mr-2">
                                    <span class="font-bold">${data.note.user}</span>
                                    <span class="text-gray-500 text-sm ml-2">${data.note.created_at}</span>
                                </div>
                                <div class="note-content">${data.note.content}</div>
                            </div>
                        `;
                        $('#noteList').prepend(noteItem);

                        // Reset CKEditor dan Form
                        editor.setData('');
                        $('#noteForm')[0].reset();

                        // Tampilkan Toast Success
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Note berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Reload halaman setelah toast hilang
                            location.reload();
                        });

                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Gagal menambahkan note!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Terjadi kesalahan server!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        });
    })
    .catch(error => console.error(error));
});
    
    function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type, // success, error, info, warning
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

    function rejectDOC(id) {
        document.getElementById('reject_document_id').value = id;
        document.getElementById('reject_reason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

   // ✅ Approve Ticket
function approveDOC(docID) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Approve this Document?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${docID}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message || 'Document approved!',
                    showConfirmButton: false,
                    timer: 2000
                });
                setTimeout(() => location.reload(), 1200);
            }).fail(function () {
                Swal.fire('Error', 'Gagal approve.', 'error');
            });
        }
    });
}

// ✅ Reject Ticket
$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let docID = $('#reject_document_id').val();
    let data = form.serialize();

    $.post(`/mr/document/${docID}/reject`, data, function (res) {
        if (res.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.message,
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire('Failed', res.message, 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'An error occurred.', 'error');
    });
});

function authorizedDOC(id, docNumber) {
feather.replace();
    Swal.fire({
        title: 'Authorize Document',
        html: `Authorize this Document?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document has been Authorized: ' + res.document_number);
                setTimeout(() => location.reload(), 3000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan Document.');
            });
        }
    });
}


$(function () {
    const $button = $('#revisionDropdownButton');
    const $menu = $('#revisionDropdownMenu');
    const $approvalStatus = $('#approval-status');

    // 🔄 Update tombol
    function updateButton(version) {
        $button.html(
            'Document Version ' + String(version).padStart(2, '0') +
            '<svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>' +
            '</svg>'
        );
        $button.data('current-version', version);
    }

     function updateApprovalStatus(data) {
        // Approved / Rejected
        const isApproved = data.approvedBy;
        const isRejected = data.rejectedBy;

        let approvedHtml = `
        <div class="flex items-center space-x-2 mb-2">
            <i data-feather="${isApproved ? 'check-circle' : (isRejected ? 'x-circle' : 'x-circle')}" 
               class="w-6 h-6 text-${isApproved ? 'green-500' : (isRejected ? 'red-500' : 'gray-400')}"></i>
            <div>
                <div class="text-sm font-semibold text-gray-800">
                    ${isApproved ? `Approved by ${data.approvedName}` : (isRejected ? `Rejected by ${data.rejectedName}` : 'Not yet Approved')}
                </div>
                <div class="text-xs text-gray-500">
                    ${isApproved ? data.approvedAtFormatted : (isRejected ? data.rejectedAtFormatted : 'Pending')}
                </div>
            </div>
        </div>
        `;

        let reviewHtml = `
        <div class="flex items-center space-x-2 mb-2">
            <i data-feather="${data.reviewBy ? 'check-circle' : 'x-circle'}" 
               class="w-6 h-6 text-${data.reviewBy ? 'green-500' : 'gray-400'}"></i>
            <div>
                <div class="text-sm font-semibold text-gray-800">
                    ${data.reviewBy ? `Review by ${data.reviewName}` : 'Not yet Reviewed'}
                </div>
                <div class="text-xs text-gray-500">
                    ${data.reviewAtFormatted}
                </div>
            </div>
        </div>
        `;

        let authorizedHtml = `
        <div class="flex items-center space-x-2">
            <i data-feather="${data.authorizedBy ? 'check-circle' : 'x-circle'}" 
               class="w-6 h-6 text-${data.authorizedBy ? 'green-500' : 'gray-400'}"></i>
            <div>
                <div class="text-sm font-semibold text-gray-800">
                    ${data.authorizedBy ? `Authorized by ${data.authorizedName}` : 'Not yet Authorized'}
                </div>
                <div class="text-xs text-gray-500">
                    ${data.authorizedAtFormatted}
                </div>
            </div>
        </div>
        `;

        $approvalStatus.html(approvedHtml + reviewHtml + authorizedHtml);

        // Re-init feather icons
        feather.replace();
    }

    // 🔄 Update tabel copies
    function updateCopies(copies, revisionId) {
        const $tbody = $('#copiesTable tbody').empty();

        if (!copies || copies.length === 0) {
            $tbody.append('<tr><td colspan="4" class="text-gray-500 p-2 text-center">No copies distributed.</td></tr>');
            return;
        }

        // Pastikan revisionId string
        revisionId = String(revisionId);

        // Filter berdasarkan revisionId
        const filtered = copies.filter(c => String(c.document_revision_id) === revisionId);

        if (filtered.length > 0) {
            filtered.forEach(c => {
                $tbody.append(
                    '<tr>' +
                        `<td class="p-2 border">${c.department}</td>` +
                        `<td class="p-2 border text-center">${c.qty} Sheet</td>` +
                        `<td class="p-2 border">${c.date ?? 'Not yet socialized'}</td>` +
                        `<td class="p-2 border">${c.socialized}</td>` +
                    '</tr>'
                );
            });
        } else {
            $tbody.append('<tr><td colspan="4" class="text-gray-500 p-2 text-center">No copies distributed.</td></tr>');
        }
    }

    // 🔄 Update seluruh document
    function updateDocument(data) {
        $('#documentVersion').text(String(data.version).padStart(2, '0'));
        $('#documentTitle').text(data.title);

        // File utama
        if (data.file) {
            $('#documentFileLink').attr('href', data.file).show();
            $('#documentFileName').text(data.fileName || 'Unknown File');
            $('#documentFileExt').text(data.fileExt || '');
        } else {
            $('#documentFileLink').hide();
            $('#documentFileName').text('');
            $('#documentFileExt').text('');
        }

        // File 4M
        if (data.file4m) {
            $('#documentFile4MLink').attr('href', data.file4m).show();
            $('#documentFile4MName').text(data.file4mName || 'Unknown File');
            $('#documentFile4MExt').text(data.file4mExt || '');
        } else {
            $('#documentFile4MLink').hide();
            $('#documentFile4MName').text('');
            $('#documentFile4MExt').text('');
        }

         // Reason revisi
        if (data.remark) {
            $('#remark').show().text(data.remark || 'Unknown');
        } else {
            $('#remark').hide();
        }

        // Reason revisi
      if (data.reason && data.reason.trim() !== '' && data.reason.toLowerCase() !== 'null') {
    $('#reasonRevisionLabel').show();
    $('#reasonRevisionContainer').show().find('.reason-text').text(data.reason);
} else {
    $('#reasonRevisionLabel').hide();
    $('#reasonRevisionContainer').hide().find('.reason-text').text('');
}

        // Update copies
        updateCopies(data.copies, data.revisionId ?? 'original');
    }

    // Toggle dropdown
    $button.on('click', function (e) {
        e.preventDefault();
        $menu.toggleClass('hidden');
    });

    // Klik di luar dropdown
    $(document).on('click', function (e) {
        if (!$button.is(e.target) && $button.has(e.target).length === 0 &&
            !$menu.is(e.target) && $menu.has(e.target).length === 0) {
            $menu.addClass('hidden');
        }
    });

    // Pilih versi
    $menu.on('click', '.revision-item', function (e) {
        e.preventDefault();
        const $selected = $(this);

        // Parse copies aman
        let copies = $selected.data('copies');
        if (typeof copies === 'string') copies = JSON.parse(copies);

        const newData = {
            version: $selected.data('version'),
            title: $selected.data('title'),
            file: $selected.data('file'),
            remark: $selected.data('remark'),
            fileName: $selected.data('file-name'),
            fileExt: $selected.data('file-ext'),
            file4m: $selected.data('file-4m'),
            file4mName: $selected.data('file-4m-name'),
            file4mExt: $selected.data('file-4m-ext'),
            reason: $selected.data('reason'),
            copies: copies,
            revisionId: String($selected.data('revision-id') ?? 'original'),
        };

        // Update tombol & document
        updateButton(newData.version);
        updateDocument(newData);

        $menu.addClass('hidden');
    });
});


function reviewDOC(id, docNumber) {

    Swal.fire({
        title: 'Review Document?',
        html: `Review this Document?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Review it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/review`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document has been Review: ' + res.document_number);
                 setTimeout(() => location.reload(), 1200);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat review document.');
            });
        }
    });
}

 // Animasi ketika modal dibuka
  function openModal() {
    document.getElementById('socializeModal').classList.remove('hidden');
    setTimeout(() => {
      document.getElementById('modalContent').classList.remove('scale-95', 'opacity-0');
      document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  // Animasi ketika modal ditutup
  function closeModal() {
    const modalContent = document.getElementById('modalContent');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
      document.getElementById('socializeModal').classList.add('hidden');
    }, 200);
  }

function updateDOC(documentId) {
    $('#document_id').val(documentId);
    $('#docCopiesContainer').html('<div class="text-center text-gray-500">Loading...</div>');

    $.get('/mr/document/copies/' + documentId, function (data) {
        if (!data || data.length === 0) {
            $('#docCopiesContainer').html('<p class="text-gray-500">No department copies found.</p>');
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `
                <div class="p-3 border rounded-lg bg-gray-50 mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-700">${item.department_name}</span>
                        <span class="text-sm text-gray-500">Qty: ${item.qty}</span>
                    </div>
                    <input type="date" name="dates[${item.id}]" value="${item.date ? item.date : ''}" 
                        class="border rounded-lg px-3 py-1 text-sm w-full focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>
            `;
        });

        $('#docCopiesContainer').html(html);
        openModal(); // gunakan animasi modal yang sebelumnya kita buat
    }).fail(() => {
        $('#docCopiesContainer').html('<p class="text-red-500">Failed to load data.</p>');
    });
}

// Close modal
function closeModal() {
    const modalContent = $('#modalContent');
    modalContent.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
    setTimeout(() => {
        $('#socializeModal').addClass('hidden');
    }, 200);
}

// Submit form untuk menyimpan tanggal socialize
$('#socializeForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.post('/mr/document/save-socialize', formData, function (res) {
        if (res.success) {
            showToast('success', 'Socialization dates saved successfully!');
            closeModal();
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('error', res.message || 'Failed to save socialize dates.');
        }
    }).fail(() => {
        showToast('error', 'Server error while saving dates.');
    });
});

function resubmitDOC(id, docNumber) {
    currentDocId = id;
    $("#resubmitTitle").text(`Resubmit Document`);
    $("#resubmitModal").removeClass("hidden");
}

// tutup modal
$("#cancelResubmit").on("click", function () {
    $("#resubmitModal").addClass("hidden");
    $("#resubmitForm")[0].reset();
});

// submit form via AJAX
$("#resubmitForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: `/mr/document/${currentDocId}/resubmit`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $("#resubmitModal").addClass("hidden");
                showToast('success', 'Document has been Resubmit');
                setTimeout(() => location.reload(), 1200);
            } else {
                 showToast('error', res.message || 'Failed to save socialize dates.');
            }
        }
    });
});

feather.replace();

</script>
@endpush

@endsection