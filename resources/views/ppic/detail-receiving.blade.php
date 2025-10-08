@extends('layouts.app')

@section('title', 'Detail Receiving')
@section('page-title', 'DETAIL RECEIVING')
@section('breadcrumb-item', 'Receiving')
@section('breadcrumb-active', 'Detail Receiving')

@section('content')
@php
          $status = $receiving->status ?? '';
$colorClass = match($status) {
    'Draft'    => 'text-gray-600 bg-gray-200',
    'Posted' => 'text-white bg-orange-600',
    'Revision'    => 'text-white bg-blue-600',
    default    => 'text-gray-700 bg-gray-100',
};
        @endphp
        
<div class="w-full bg-white shadow-md rounded-xl p-6 space-y-4 mb-2">
   <div class="flex justify-between items-center mb-4">
     <div class="flex items-center gap-4">
    <h1 class="text-3xl font-extrabold text-gray-900">{{ $receiving->receiving_number }}</h1>

    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
      {{ strtoupper($receiving->status ?? '-') }}
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
            @if ($receiving->revisions && $receiving->revisions->count())
                @foreach ($receiving->revisions as $rev)
                    <a href="{{ route('receiving.revision.view', $rev->id) }}"
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
        <i data-feather="user" class="inline w-4 h-4 mr-1"></i> {{ $receiving->creator->name ?? '-' }}
    </span>
    <span class="inline-block">
        <i data-feather="calendar" class="inline w-4 h-4 mr-1"></i> {{ $receiving->created_at->format('d M Y H:i') }}
    </span>
        </div>
<hr>
<div class="flex gap-6 mb-2">
  <div class="w-full flex flex-col space-y-6">
   
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
     <div class="flex items-center mb-4 gap-2">
      <i data-feather="info" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Receiving Information</h3>
  </div>

      <div class="grid grid-cols-2 gap-x-12 gap-y-6 text-sm mb-8">
        <div>
          <div class="text-gray-500 font-medium mb-1">Purchase Order Number</div>
          <div class="text-gray-800">{{ $receiving->purchaseOrder->order_number }}</div>
        </div>
         <div>
          <div class="text-gray-500 font-medium mb-1">Supplier</div>
          <div class="text-gray-800">{{ $receiving->supplier->name ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Delivery Order Number</div>
          <div class="text-gray-800">{{ $receiving->delivery_order_number ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Delivery Order Date</div>
          <div class="text-gray-800">{{ $receiving->delivery_order_date ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Received Date</div>
          <div class="text-gray-800">{{ $receiving->received_date }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Account Payable</div>
          <div class="text-gray-800">{{ $receiving->acoount_payable_id ?? '-' }}</div>
        </div>
       
         
      </div>
  <div class="flex items-center mb-4 mt-8 gap-2">
      <i data-feather="package" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Receiving Items</h3>
  </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
          <thead class="bg-blue-500 text-white uppercase text-xs">
            <tr>
              <th class="p-3 border">No.</th>
              <th class="p-3 border">Article</th>
              <th class="p-3 border">Qty PO</th>
              <th class="p-3 border">Qty Received</th>
              <th class="p-3 border">Qty Free</th>
              <th class="p-3 border">Qty Total</th>
                <th class="p-3 border">Standard Package</th>
              <th class="p-3 border">UOM</th>
              <th class="p-3 border">Expired Date</th>
              <th class="p-3 border">Destination</th>
            </tr>
          </thead>
          <tbody>
           @foreach($items as $index => $item)
            <tr>
              <td class="p-3 border text-center">{{ $index + 1 }}</td>
              <td class="p-3 border">{{ $item->article_code }} - {{ $item->article->description }}</td>
              <td class="p-3 border text-center">{{ $item->qty_po }}</td>
              <td class="p-3 border text-center">{{ $item->qty_received }}</td>
              <td class="p-3 border text-center">{{ $item->qty_free }}</td>
              <td class="p-3 border text-center">{{ $item->qty_total }}</td>
               <td class="p-3 border text-center">{{ $item->article->min_package }}</td>
              <td class="p-3 border text-center">{{ $item->article->unit }}</td>
              <td class="p-3 border text-center">{{ $item->expired_date ?? '-' }}</td>
              <td class="p-3 border text-center">{{ $item->destination->name ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex items-center mb-4 mt-8 gap-2">
        <i data-feather="file-text" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Internal Notes</h3>
      </div>
      <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
        <img src="{{ $receiving->creator->avatar ? asset('storage/' . $receiving->creator->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
        <div>
          <div class="text-sm font-semibold">{{ $receiving->creator->name ?? '-' }}</div>
          <div class="text-xs text-gray-500">Note • {{ \Carbon\Carbon::parse($receiving->created_at)->format('d M Y H:i') }}</div>
          <div class="mt-1 text-sm">
            <div class="mt-2 text-sm text-gray-700">{{ $receiving->note ?? 'No remarks were added' }}</div>
          </div>
        </div>
      </div>

  
   <hr>
   <div class="flex justify-start space-x-2 mt-4">
  <a href="{{ route('ppic.rec.index') }}" 
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
    $recNumber = $receiving->receiving_number ?? 'Unknown';
@endphp

{{-- Draft --}}
@if(
    $receiving->status == 'Draft' &&
    $user->hasRole('Supervisor Special Access') &&
    $userDepartments->contains('Production Planning & Inventory Control')
)
    <button onclick="approveRec({{ $receiving->id }}, '{{ $recNumber }}')"
        class="w-28 flex gap-2 items-center justify-center px-4 py-2 bg-green-600 text-white rounded">
        <i data-feather="check-circle" class="w-4 h-4 inline"></i>
        <span>Approve</span>
    </button>
@endif
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

   function approveIN(id, trinNumber) {

    Swal.fire({
        title: 'Approve Transfer In',
        html: `Approve this Transfer In: <strong>${trinNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/ppic/logistic/transfer_in/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Transfer In has been Approved: ' + res.code);
                  setTimeout(() => {
                    location.reload();
                }, 2000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui Transfer In.');
            });
        }
    });
}
</script>
@endpush
@endsection
