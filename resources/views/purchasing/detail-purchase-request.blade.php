@extends('layouts.app')

@section('title', 'Detail Purchase Request')
@section('page-title', 'Detail Purchase Request')
@section('breadcrumb-item', 'Purchase Request')
@section('breadcrumb-active', 'Detail Purchase Request')

@section('content')

 @php
          $status = $pr->status ?? '';
$colorClass = match($status) {
    'Draft'    => 'text-gray-600 bg-gray-200',
    'Approved' => 'text-white bg-yellow-600',
    'Rejected'    => 'text-gray-600 bg-red-600',
    'Authorized' => 'text-white bg-blue-600',
    'Verified'    => 'text-gray-600 bg-green-200',
    'Revision' => 'text-white bg-gray-600',
    'Partially Order' => 'text-white bg-pink-600',
    'Full Order' => 'text-white bg-purple-600',
    'Closed' => 'text-white bg-teal-600',
    default    => 'text-gray-700 bg-gray-100',
};
        @endphp
        
<div class="w-full bg-white shadow-md rounded-xl p-6 space-y-4 mb-2">
   <div class="flex justify-between items-center mb-4">
     <div class="flex items-center gap-2">
    <h1 class="text-3xl font-extrabold text-gray-900">{{ $pr->request_number }}</h1>
    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
      {{ strtoupper($pr->status ?? '-') }}
    </span>
  </div>

    <!-- Dropdown untuk revision history -->
    <div class="relative inline-block text-left">
        <button id="revisionDropdownButton" type="button"
            class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-1 hover:bg-blue-200 transition">
            Latest Version
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div id="revisionDropdownMenu"
            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 hidden">
            @if ($pr->revisions && $pr->revisions->count())
                @foreach ($pr->revisions as $rev)
                    <a href="{{ route('pr.revision.view', $rev->id) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Revision {{ $rev->revision }}
                    </a>
                @endforeach
            @else
                <span class="block px-4 py-2 text-sm text-gray-400">No revisions</span>
            @endif
        </div>
    </div>
</div>

     <div class="text-sm text-gray-600 mb-6">
 <span class="inline-block mr-2">
        <i data-feather="user" class="inline w-4 h-4 mr-1"></i> {{ $pr->owner->name ?? '-' }}
    </span>
    <span class="inline-block">
        <i data-feather="calendar" class="inline w-4 h-4 mr-1"></i> {{ $pr->created_at->format('d M Y H:i') }}
    </span>
        </div>
<hr>
<div class="flex gap-6 mb-2">
  <!-- MAIN CONTENT: PO Info + Items -->
  <div class="w-2/3 flex flex-col space-y-6">
    
    <!-- Purchase Order Information -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
      <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-semibold text-gray-700">Purchase Request Information</h3>
       @php
    $statusClasses = [
        'Standard' => 'bg-gray-600 text-white',
        'Target Sales Order' => 'bg-blue-600 text-white',
        'General Affair Request' => 'bg-yellow-600 text-white',
        'Electronics & IT Request' => 'bg-green-600 text-white',
    ];

    $statusClass = $statusClasses[$pr->order_type] ?? 'bg-gray-100 text-gray-800';
@endphp

<span class="inline-block {{ $statusClass }} px-2 py-1 rounded-lg text-sm">
    {{ $pr->order_type }}
</span>

      </div>

      <div class="grid grid-cols-2 gap-x-12 gap-y-6 text-sm mb-8">
        <div>
          <div class="text-gray-500 font-medium mb-1">Purchase Request Date</div>
          <div class="text-gray-800">{{ $pr->request_date }}</div>
        </div>
         <div>
          <div class="text-gray-500 font-medium mb-1">Department</div>
          <div class="text-gray-800">{{ $pr->owner->departments->pluck('name')->join(', ') }}</div>
        </div>
        <div>
        </div>
      </div>
       <h3 class="text-xl font-semibold text-gray-700 mb-4">Purchase Request Items</h3>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
          <thead class="bg-blue-500 text-white uppercase text-xs">
            <tr>
              <th class="px-4 py-2 border-b">No.</th>
              <th class="px-4 py-2 border-b">Article</th>
              <th class="px-4 py-2 border-b">Qty Request</th>
              <th class="px-4 py-2 border-b">Qty Order</th>
              <th class="px-4 py-2 border-b">Qty Received</th>
              <th class="px-4 py-2 border-b">Note</th>
            </tr>
          </thead>
         <tbody>
    @forelse ($pr->items as $index => $item)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b text-center">{{ $index + 1 }}</td>

            <td class="px-4 py-2 border-b">
                {{ $item->article_code }} - {{ $item->article->description }}
            </td>

            <td class="px-4 py-2 border-b text-center">
                {{ $item->qty }} {{ $item->article->unit }}
            </td>

            {{-- Qty Order --}}
            <td class="px-4 py-2 border-b text-center">
                {{ $item->poItems->sum('qty') }} {{ $item->article->unit }}
            </td>

            {{-- Qty Received --}}
            <td class="px-4 py-2 border-b text-center">
               
            </td>

            <td class="px-4 py-2 border-b">{{ $item->note }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                No items found.
            </td>
        </tr>
    @endforelse
</tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- SIDEBAR: Order History + Summary -->
  <div class="w-1/3 flex flex-col space-y-6">
    <!-- Order History -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
  <div class="flex justify-between items-center mb-2">
    <h3 class="text-xl font-semibold text-gray-700">Internal Notes</h3>
    <i data-feather="file-text" class="text-gray-700 w-5 h-5"></i>
  </div>
  <hr class="my-4">
   <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
            <img src="{{ $pr->owner->avatar ? asset('storage/' . $pr->owner->avatar) : asset('img/avatar-dummy.png') }}" alt="Avatar" class="w-8 h-8 rounded-full">
            <div>
                <div class="text-sm font-semibold">{{ $pr->owner->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">Note • {{ \Carbon\Carbon::parse($pr->created_at)->format('d M Y H:i') }}</div>
                    <div class="mt-1 text-sm">
                        <div class="mt-2 text-sm text-gray-700">{{ $pr->pr_note }}</div>
                    </div>
            </div>
        </div>

         {{-- Reject --}}
    @if($pr->status === 'Rejected' && $pr->rejected_reason)
        <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
            <img src="{{ $pr->reject->avatar ? asset('storage/' . $pr->process->avatar) : asset('img/avatar-dummy.png') }}" alt="Avatar" class="w-8 h-8 rounded-full">
            <div>
                <div class="font-semibold">{{ $pr->reject->name ?? 'Unknown' }}</div>
                <div class="text-xs text-gray-500">Rejected • {{ \Carbon\Carbon::parse($pr->rejected_at)->format('d M Y H:i') }}</div>
                <div class="mt-1 text-sm text-red-600"><strong>Reason:</strong> {{ $pr->rejected_reason }}</div>
            </div>
        </div>
    @endif
  <div class="flex justify-between items-center mt-6 mb-2">
    <h3 class="text-xl font-semibold text-gray-700">Order Timeline</h3>
    <i data-feather="clock" class="text-gray-700 w-5 h-5"></i>
  </div>
  <hr class="my-4">
   {{-- Approved --}}
    @if($pr->approve)
        <div class="mb-4">
            <div class="text-xs text-yellow-400 uppercase font-semibold mb-1">Approved</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-yellow-500"></i>
                <span>Approved by {{ $pr->approve->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($pr->approved_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

     @if($pr->authorized)
        <div class="mb-4">
            <div class="text-xs text-blue-400 uppercase font-semibold mb-1">Authorized</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-blue-500"></i>
                <span>Authorized by {{ $pr->authorized->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($pr->authorized_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

     @if($pr->verified)
        <div class="mb-4">
            <div class="text-xs text-green-400 uppercase font-semibold mb-1">Verified</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                <span>Verified by {{ $pr->verified->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($pr->verified_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Full Order --}}
@if($pr->purchaseOrders->isNotEmpty())
    <div class="mb-4">
        <div class="text-xs text-purple-400 uppercase font-semibold mb-1">
            {{ $pr->status === 'Full Order' }}
        </div>

        @foreach($pr->purchaseOrders as $po)
            <div class="flex items-center space-x-2">
                <i data-feather="file-text" class="w-4 h-4 text-purple-500"></i>
                <span>
                    PO Created by {{ $po->createdBy->name ?? '-' }}
                </span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-2">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($po->created_at)->format('d M Y H:i') }}</span>
            </div>
        @endforeach
    </div>
@endif
</div>
  </div>
</div>

  
   <hr>
   <div class="flex justify-start space-x-2 mt-4">
   <a href="{{ route('purchasing.pr.index') }}" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   ← Back
</a>

<button type="button" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded shadow">
   <i data-feather="printer" class="h-4 w-4"></i>
   Print
</button>

 @php
    $user = auth()->user();
    $userDepartments = $user->departments->pluck('name'); // koleksi nama departemen user
    $ownerDepartments = optional($pr->owner?->departments)->pluck('name') ?? collect();
    $hasSameDepartment = $userDepartments->intersect($ownerDepartments)->isNotEmpty();
    $requestNumber = $pr->request_number ?? 'Unknown';
@endphp

@if(
    $pr->status == 'Draft' &&
    $hasSameDepartment &&
    ($user->hasRole('Supervisor Special Access') || $user->hasRole('Manager Special Access'))
)
<button onclick="approvePR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
    <i data-feather="check-circle" class="w-4 h-4 inline"></i>
    <span>Approve</span>
</button>

<button onclick="rejectPR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-red-600 text-white rounded">
    <i data-feather="x-circle" class="w-4 h-4 inline"></i>
    <span>Reject</span>
</button>

@endif

@if(
    $pr->status == 'Approved' &&
    $hasSameDepartment &&
    $user->hasRole('Manager Special Access')
)
<button onclick="authorizedPR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
    <i data-feather="check-circle" class="w-4 h-4 inline"></i>
    <span>Authorized</span>
</button>

<button onclick="rejectPR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-red-600 text-white rounded">
    <i data-feather="x-circle" class="w-4 h-4 inline"></i>
    <span>Reject</span>
</button>

@endif

@if(
    $pr->status == 'Authorized' &&
    $user->hasRole('BOD Special Access')
)
<button onclick="verifiedPR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
    <i data-feather="check-circle" class="w-4 h-4 inline"></i>
    <span>Verified</span>
</button>

<button onclick="rejectPR({{ $pr->id }}, '{{ $requestNumber }}')"
    class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-red-600 text-white rounded">
    <i data-feather="x-circle" class="w-4 h-4 inline"></i>
    <span>Reject</span>
</button>

@endif
</div>
</div>

  








<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Reject Ticket</h2>
        <form id="rejectForm" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" id="reject_ticket_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Reject Reason</label>
                <textarea name="rejected_reason" id="reject_reason" rows="3" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
   function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type, // success, error, info, warning
        title: message,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

   function approvePR(id, requestNumber) {

    Swal.fire({
        title: 'Approve Purchase Request',
        html: `Approve this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Approved: ' + res.request_number);
                  setTimeout(() => {
                    location.reload();
                }, 2000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui PR.');
            });
        }
    });
}

function verifiedPR(id, requestNumber) {

    Swal.fire({
        title: 'Verified Purchase Request',
        html: `Verified this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verified it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/verified`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Verified: ' + res.request_number);
                 setTimeout(() => {
                    location.reload();
                }, 2000);
            }).fail(function() {
                showToast('error', 'Purchase Request Verification Failed');
            });
        }
    });
}

function authorizedPR(id, requestNumber) {

    Swal.fire({
        title: 'Authorize Purchase Request',
        html: `Authorize this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Authorized: ' + res.request_number);
                 setTimeout(() => {
                    location.reload();
                }, 1500);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan PR.');
            });
        }
    });
}

function rejectPR(id, requestNumber) {
    // Isi ID
    document.getElementById('reject_id').value = id;

    // Kalau mau tampilkan nomor PR di modal
    const prInfo = document.getElementById('pr_info');
    if (prInfo) {
        prInfo.textContent = requestNumber;
    }

    // Reset textarea
    document.getElementById('rejected_reason').value = '';

    // Show modal
    document.getElementById('rejectModal').classList.remove('hidden');
}


function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Submit form reject
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let id = document.getElementById('reject_id').value;
    let reason = document.getElementById('rejected_reason').value.trim();

    if (!reason) {
        showToast('error', 'Reject reason is required.');
        return;
    }

    $.post(`/purchasing/purchase-request/${id}/reject`, {
        _token: '{{ csrf_token() }}',
        rejected_reason: reason
    })
    .done(function(res) {
        showToast('success', 'Purchase Request rejected.');
        closeRejectModal();
        $('#request-table').DataTable().ajax.reload(null, false);
    })
    .fail(function() {
        showToast('error', 'Gagal reject PR.');
    });
});
</script>
@endpush

@endsection