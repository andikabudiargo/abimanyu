@extends('layouts.app')

@section('title', 'Detail Transfer Out')
@section('page-title', 'DETAIL TRANSFER OUT')
@section('breadcrumb-item', 'Transfer Out')
@section('breadcrumb-active', 'Detail Transfer Out')

@section('content')
@php
          $status = $transfer->status ?? '';
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
    <h1 class="text-3xl font-extrabold text-gray-900">{{ $transfer->code }}</h1>

    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
      {{ strtoupper($transfer->status ?? '-') }}
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
            @if ($transfer->revisions && $transfer->revisions->count())
                @foreach ($transfer->revisions as $rev)
                    <a href="{{ route('transfer.revision.view', $rev->id) }}"
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
        <i data-feather="user" class="inline w-4 h-4 mr-1"></i> {{ $transfer->createdBy->name ?? '-' }}
    </span>
    <span class="inline-block">
        <i data-feather="calendar" class="inline w-4 h-4 mr-1"></i> {{ $transfer->created_at->format('d M Y H:i') }}
    </span>
        </div>
<hr>
<div class="flex gap-6 mb-2">
  <div class="w-full flex flex-col space-y-6">
   
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-6">
     <div class="flex items-center mb-4 gap-2">
      <i data-feather="info" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Transfer Out Information</h3>
  </div>

      <div class="grid grid-cols-2 gap-x-12 gap-y-6 text-sm mb-8">
        <div>
          <div class="text-gray-500 font-medium mb-1">Reference Number</div>
          <div class="text-gray-800">{{ $transfer->reference_number }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Outgoing Date</div>
          <div class="text-gray-800">{{ $transfer->date }}</div>
        </div>
        <div>
          <div class="text-gray-500 font-medium mb-1">Transfer Type</div>
          <div class="text-gray-800">{{ $transfer->transfer_type ?? '-' }}</div>
        </div>
         
      </div>
  <div class="flex items-center mb-4 mt-8 gap-2">
      <i data-feather="package" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Transfer Out Items</h3>
  </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
          <thead class="bg-blue-500 text-white uppercase text-xs">
            <tr>
              <th class="p-3 border">No.</th>
              <th class="p-3 border">Date</th>
              <th class="p-3 border">Article</th>
              <th class="p-3 border">Qty</th>
              <th class="p-3 border">UOM</th>
              <th class="p-3 border">Min Package</th>
              <th class="p-3 border">Expired Date</th>
              <th class="p-3 border">From</th>
              <th class="p-3 border">Destination</th>
            </tr>
          </thead>
          <tbody>
            @foreach($groupedItems as $index => $item)
            <tr>
              <td class="p-3 border text-center">{{ $index + 1 }}</td>
              <td class="p-3 border">{{ $transfer->date }}</td>
              <td class="p-3 border">{{ $item->article_code }} - {{ $item->description }}</td>
              <td class="p-3 border text-center">{{ $item->qty }}</td>
              <td class="p-3 border text-center">{{ $item->uom }}</td>
              <td class="p-3 border text-center">{{ $item->min_package }}</td>
              <td class="p-3 border text-center">{{ $item->expired_date ?? '-' }}</td>
               <td class="p-3 border">{{ $item->from_location ?? '-' }}</td>
              <td class="p-3 border">{{ $item->destination ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex items-center mb-4 mt-8 gap-2">
        <i data-feather="file-text" class="text-gray-700 w-5 h-5"></i> <h3 class="text-xl font-semibold text-gray-700">Internal Notes</h3>
      </div>
      <div class="flex items-start space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
        <img src="{{ $transfer->createdBy->avatar ? asset('storage/' . $transfer->createdBy->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
        <div>
          <div class="text-sm font-semibold">{{ $transfer->createdBy->name ?? '-' }}</div>
          <div class="text-xs text-gray-500">Note • {{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y H:i') }}</div>
          <div class="mt-1 text-sm">
            <div class="mt-2 text-sm text-gray-700">{{ $transfer->note ?? 'No remarks were added' }}</div>
          </div>
        </div>
      </div>

  
   <hr>
   <div class="flex justify-start space-x-2 mt-4">
  <a href="{{ route('ppic.transfer-in.index') }}" 
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
    $trinNumber = $transfer->code ?? 'Unknown';
@endphp

{{-- Draft --}}
@if(
    $transfer->status == 'Draft' &&
    $user->hasRole('Supervisor Special Access') &&
    $userDepartments->contains('Production Planning & Inventory Control')
)
    <button onclick="approveIN({{ $transfer->id }}, '{{ $trinNumber }}')"
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
