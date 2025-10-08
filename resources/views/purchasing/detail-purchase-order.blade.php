@extends('layouts.app')

@section('title', 'Detail Purchase Order')
@section('page-title', 'Detail Purchase Order')
@section('breadcrumb-item', 'Purchase Order')
@section('breadcrumb-active', 'Detail Purchase Order')

@section('content')

@php
          $status = $po->status ?? '';
$colorClass = match($status) {
    'Draft'    => 'text-gray-600 bg-gray-200',
    'Approved' => 'text-white bg-yellow-600',
    'Rejected'    => 'text-white bg-red-600',
    'Authorized' => 'text-white bg-blue-600',
    'Verified'    => 'text-white bg-green-600',
    'Revision' => 'text-white bg-gray-600',
    'Partially Order' => 'text-white bg-pink-600',
    'Full Order' => 'text-white bg-purple-600',
    'Closed' => 'text-white bg-teal-600',
    default    => 'text-gray-700 bg-gray-100',
};
        @endphp
        
<div class="w-full bg-white shadow-md rounded-xl p-6 space-y-4 mb-2">
   <div class="flex justify-between items-center mb-4">
     <div class="flex items-center gap-4">
    <h1 class="text-3xl font-extrabold text-gray-900">{{ $po->order_number }}</h1>

    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
      {{ strtoupper($po->status ?? '-') }}
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
            @if ($po->revisions && $po->revisions->count())
                @foreach ($po->revisions as $rev)
                    <a href="{{ route('po.revision.view', $rev->id) }}"
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
        <i data-feather="user" class="inline w-4 h-4 mr-1"></i> {{ $po->createdBy->name ?? '-' }}
    </span>
    <span class="inline-block">
        <i data-feather="calendar" class="inline w-4 h-4 mr-1"></i> {{ $po->created_at->format('d M Y H:i') }}
    </span>
        </div>
<hr>
<div class="flex gap-6 mb-2">
  <!-- MAIN CONTENT: PO Info + Items -->
  <div class="w-2/3 flex flex-col space-y-6">
    
    <!-- Purchase Order Information -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
      <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-semibold text-gray-700">Purchase Order Information</h3>

      </div>

      <div class="grid grid-cols-2 gap-x-12 gap-y-6 text-sm mb-8">
        <div>
          <div class="text-gray-500 font-medium mb-1">Purchase Order Date</div>
          <div class="text-gray-800">{{ $po->order_date }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Expected Delivery Date</div>
          <div class="text-gray-800">{{ $po->delivery_date }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Supplier</div>
          <div class="text-gray-800">{{ $po->supplier->name ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Payment Term</div>
          <div class="text-gray-800">{{ $po->top ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Tax (PKP)</div>
          <div class="text-gray-800">
            @if ($po->pkp === 1)
              <span class="text-green-600 font-semibold">Yes</span>
            @elseif ($po->pkp === 0)
              <span class="text-red-600 font-semibold">No</span>
            @else
              -
            @endif
          </div>
        </div>
      </div>
       <h3 class="text-xl font-semibold text-gray-700 mb-4">Purchase Order Items</h3>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
          <thead class="bg-blue-500 text-white uppercase text-xs">
            <tr>
              <th class="px-4 py-2 border-b">Purchase Request</th>
              <th class="px-4 py-2 border-b">Article</th>
              <th class="px-4 py-2 border-b">Qty PO</th>
              <th class="px-4 py-2 border-b">Qty Received</th>
              <th class="px-4 py-2 border-b">Price</th>
              <th class="px-4 py-2 border-b">Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($po->items as $item)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">{{ $item->request->request->request_number}}</td>
                <td class="px-4 py-2 border-b">{{ $item->article_code }} - {{ $item->article->description }}</td>
                <td class="px-4 py-2 border-b">{{ $item->qty }} {{ $item->uom }}</td>
                 <td class="px-4 py-2 border-b">{{ $item->qty_received }} {{ $item->uom }}</td>
                <td class="px-4 py-2 border-b">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="px-4 py-2 border-b">Rp{{ number_format($item->qty * $item->price, 0, ',', '.') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-4 text-center text-gray-500">No items found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
<div class="flex justify-end mt-4">
  <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
        <div class="flex justify-between">
          <span class="font-semibold">Bruto</span>
          <span>Rp{{ number_format($po->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-semibold">Discount</span>
          <span>Rp{{ number_format($po->discount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-semibold">PPN 10%</span>
          <span>Rp{{ number_format($po->ppn, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-semibold">PPh 2%</span>
          <span>Rp{{ number_format($po->pph, 0, ',', '.') }}</span>
        </div>
        <hr class="border-gray-400">
        <div class="flex justify-between text-lg font-bold text-green-700">
          <span>Netto</span>
          <span>Rp{{ number_format($po->netto, 0, ',', '.') }}</span>
        </div>
      </div>
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
            <img src="{{ $po->createdBy->avatar ? asset('storage/' . $po->createdBy->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
            <div>
                <div class="text-sm font-semibold">{{ $po->createdBy->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">Note • {{ \Carbon\Carbon::parse($po->created_at)->format('d M Y H:i') }}</div>
                    <div class="mt-1 text-sm">
                        <div class="mt-2 text-sm text-gray-700">{{ $po->note }}</div>
                    </div>
            </div>
        </div>
  <div class="flex justify-between items-center mt-6 mb-2">
    <h3 class="text-xl font-semibold text-gray-700">Order Timeline</h3>
    <i data-feather="clock" class="text-gray-700 w-5 h-5"></i>
  </div>
  <hr class="my-4">
   {{-- Approved --}}
    @if($po->approved)
        <div class="mb-4">
            <div class="text-xs text-yellow-400 uppercase font-semibold mb-1">Approved</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-yellow-500"></i>
                <span>Approved by {{ $po->approved->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($po->approved_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

     @if($po->authorized)
        <div class="mb-4">
            <div class="text-xs text-blue-400 uppercase font-semibold mb-1">Authorized</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-blue-500"></i>
                <span>Authorized by {{ $po->authorized->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($po->authorized_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

     @if($po->verified)
        <div class="mb-4">
            <div class="text-xs text-green-400 uppercase font-semibold mb-1">Verified</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                <span>Verified by {{ $po->verified->name }}</span>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($po->verified_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif
</div>
  </div>
</div>

  
   <hr>
   <div class="flex justify-start space-x-2 mt-4">
   <a href="{{ route('purchasing.po.index') }}" 
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
    $userDepartments = $user->departments->pluck('name'); 
    $orderNumber = $po->order_number ?? 'Unknown';
@endphp

{{-- Draft --}}
@if(
    $po->status == 'Draft' &&
    $user->hasRole('Supervisor Special Access') &&
    $userDepartments->contains('Purchasing')
)
    <button onclick="approvePO({{ $po->id }}, '{{ $orderNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="check-circle" class="w-4 h-4 inline"></i>
        <span>Approve</span>
    </button>

    <button onclick="rejectPO({{ $po->id }}, '{{ $orderNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-red-600 text-white rounded">
        <i data-feather="x-circle" class="w-4 h-4 inline"></i>
        <span>Reject</span>
    </button>
@endif

{{-- Approved --}}
@if(
    $po->status == 'Approved' &&
    $user->hasRole('Manager Special Access') &&
    $userDepartments->contains('Purchasing')
)
    <button onclick="authorizedPO({{ $po->id }}, '{{ $orderNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="check-circle" class="w-4 h-4 inline"></i>
        <span>Authorized</span>
    </button>

    <button onclick="rejectPO({{ $po->id }}, '{{ $orderNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-red-600 text-white rounded">
        <i data-feather="x-circle" class="w-4 h-4 inline"></i>
        <span>Reject</span>
    </button>
@endif

{{-- Authorized / BOD --}}
@if(
    $po->status == 'Authorized' &&
    $user->hasRole('BOD Special Access')
)
    <button onclick="verifiedPO({{ $po->id }}, '{{ $orderNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="check-circle" class="w-4 h-4 inline"></i>
        <span>Verified</span>
    </button>

    <button onclick="rejectPO({{ $po->id }}, '{{ $orderNumber }}')"
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

   function approvePO(id, orderNumber) {

    Swal.fire({
        title: 'Approve Purchase Order',
        html: `Approve this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Approved: ' + res.order_number);
                  setTimeout(() => {
                    location.reload();
                }, 2000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui PO.');
            });
        }
    });
}

function verifiedPO(id, orderNumber) {

    Swal.fire({
        title: 'Verified Purchase Order',
        html: `Verified this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verified it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/verified`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Verified: ' + res.order_number);
                 setTimeout(() => {
                    location.reload();
                }, 2000);
            }).fail(function() {
                showToast('error', 'Purchase Order Verification Failed');
            });
        }
    });
}

function authorizedPO(id, orderNumber) {

    Swal.fire({
        title: 'Authorize Purchase Order',
        html: `Authorize this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Authorized: ' + res.order_number);
                 setTimeout(() => {
                    location.reload();
                }, 1500);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan PO.');
            });
        }
    });
}

function rejectPO(id, orderNumber) {
    // Isi ID
    document.getElementById('reject_id').value = id;

    // Kalau mau tampilkan nomor PR di modal
    const prInfo = document.getElementById('pr_info');
    if (prInfo) {
        prInfo.textContent = orderNumber;
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

    $.post(`/purchasing/purchase-order/${id}/reject`, {
        _token: '{{ csrf_token() }}',
        rejected_reason: reason
    })
    .done(function(res) {
        showToast('success', 'Purchase Order rejected.');
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