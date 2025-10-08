@extends('layouts.app')

@section('title', 'Asset Loan')
@section('page-title', 'Asset Loan')
@section('breadcrumb-item', 'Facility')
@section('breadcrumb-active', 'Asset Loan')

@section('content')



@if($loans->isNotEmpty())
<div class="px-4 py-8 bg-white shadow-md rounded-lg mb-6">
<!-- Header E-Commerce Style -->
<div class="bg-white rounded-2xl mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
   <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 bg-white rounded-2xl p-6">
    <!-- Icon Section -->
    <div class="relative flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-500 shadow-md">
        <i data-feather="clipboard" class="w-7 h-7 text-white"></i>
    </div>

    <!-- Text Section -->
    <div class="text-center sm:text-left">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
            List Borrowed & Pending Assets
        </h2>
        <p class="text-gray-500 text-sm mt-1">
            View the assets you’ve borrowed or are currently waiting for approval
        </p>
        <div class="w-full md:w-20 h-1.5 bg-teal-500 rounded-full mt-4"></div>
    </div>
</div>
</div>


<div class="space-y-6 px-6 mb-6">
    @foreach($loans as $loan)
    <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-md border border-gray-200 overflow-hidden flex flex-col md:flex-row transition-transform duration-300 hover:shadow-lg">

        <!-- Status Badge -->
        @php
            $statusColor = match($loan->status) {
                'Pending' => 'bg-yellow-400 text-white',
                'Approved' => 'bg-green-400 text-white',
                 'Returned' => 'bg-purple-400 text-white',
                default => 'bg-gray-400 text-white',
            };
            if(!$isIT && $loan->status === 'Approved' && now() > $loan->return_estimation) {
                $statusColor = 'bg-red-500 text-white';
            }
        @endphp
        <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
            {{ (!$isIT && $loan->status === 'Approved' && now() > $loan->return_estimation) ? 'Overdue' : $loan->status }}
        </span>

        <!-- Gambar Asset -->
        <div class="w-full md:w-44 h-44 md:h-auto flex-shrink-0 overflow-hidden rounded-xl m-4">
            <img src="{{ $loan->asset->photo ? asset($loan->asset->photo) : asset('uploads/assets/no-image.png') }}" 
                 alt="{{ $loan->asset->asset_name }}" 
                 class="w-full h-full object-fit">
        </div>

        <!-- Detail Asset -->
        <div class="flex-1 p-4 flex flex-col justify-between space-y-4">
            <div>
                <h3 class="text-lg md:text-lg font-bold text-gray-800 mb-2">
                    {{ $loan->asset->asset_name }}
                </h3>

                <div class="text-xs text-gray-700 space-y-1">
                    <p><span class="font-semibold text-gray-900">Requested by:</span> {{ $loan->user->name }}</p>
                    <p><span class="font-semibold text-gray-900">Purpose:</span> {{ $loan->purpose }}</p>
                    <p><span class="font-semibold text-gray-900">Borrowed at:</span> {{ \Carbon\Carbon::parse($loan->date_loan)->format('Y-m-d H:i') }}</p>
                    <p><span class="font-semibold text-gray-900">Return Estimation:</span> {{ \Carbon\Carbon::parse($loan->return_estimation)->format('Y-m-d H:i') }}</p>
                </div>
            </div>

           <!-- Action Buttons -->
<div class="flex flex-wrap gap-3 mt-2">
    @if($isIT)
        @if($loan->status === 'Pending')
            <button class="flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 text-white py-2 px-4 rounded-lg btn-approve" data-loan-id="{{ $loan->id }}">
                <i data-feather="check" class="w-4 h-4"></i> Approve
            </button>
            <button class="flex items-center gap-2 bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600 text-white py-2 px-4 rounded-lg btn-reject" data-loan-id="{{ $loan->id }}">
                <i data-feather="x" class="w-4 h-4"></i> Reject
            </button>
            @if($loan->user_id === $user->id)
                <button onclick="cancelLoan('{{ route('facility.alo.cancel', $loan->id) }}')" class="flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded-lg">
                    <i data-feather="trash" class="w-4 h-4"></i> Cancel
                </button>
            @endif
        @elseif($loan->status === 'Approved' && $loan->user_id === $user->id)
            <button class="flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 text-white py-2 px-4 rounded-lg btn-return" data-loan-id="{{ $loan->id }}">
                <i data-feather="rotate-ccw" class="w-4 h-4"></i> Return
            </button>
        @elseif($loan->status === 'Returned')
            <button class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg btn-check-condition" data-loan-id="{{ $loan->id }}">
                <i data-feather="eye" class="w-4 h-4"></i> Confirm Condition After Return
            </button>
        @endif
    @else
        @if($loan->status === 'Pending')
            <button onclick="cancelLoan('{{ route('facility.alo.cancel', $loan->id) }}')" class="flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded-lg">
                <i data-feather="trash" class="w-4 h-4"></i> Cancel
            </button>
        @elseif($loan->status === 'Approved')
            <button class="flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 text-white py-2 px-4 rounded-lg btn-return" data-loan-id="{{ $loan->id }}">
                <i data-feather="rotate-ccw" class="w-4 h-4"></i> Return
            </button>
        @endif
    @endif
</div>

        </div>

    </div>
    @endforeach
</div>
</div>
@endif




  <!-- Title -->
   <div class="px-4 py-8 bg-white shadow-md rounded-lg">
<!-- Header E-Commerce Style -->
<div class="bg-white rounded-2xl mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
   <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 bg-white rounded-2xl p-6">
    <!-- Icon Section -->
    <div class="relative flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-500 shadow-md">
        <i data-feather="monitor" class="w-7 h-7 text-white"></i>
    </div>

    <!-- Text Section -->
    <div class="text-center sm:text-left">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
            Find the Asset You Need
        </h2>
        <p class="text-gray-500 text-sm mt-1">
            Browse available assets and borrow them for your needs
        </p>
        <div class="w-full md:w-20 h-1.5 bg-teal-500 rounded-full mt-4"></div>
    </div>
</div>
</div>

  <!-- Filter -->
<div class="flex flex-col sm:flex-row px-6 gap-4 mb-8 items-center">
    <!-- Search -->
    <div class="relative flex-1">
        <!-- Icon Feather dengan background -->
        <div class="absolute left-3 top-1/2 -translate-y-1/2 bg-white  p-2 rounded-full transition group-hover:bg-indigo-50">
            <i data-feather="search" class="text-gray-500 w-5 h-5"></i>
        </div>

        <!-- Input -->
        <input type="text" id="searchInput"
               placeholder="Cari barang..."
               class="w-full rounded-xl pl-14 pr-4 py-3 border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder-gray-400 text-gray-700">
    </div>

    <!-- Category -->
    <div class="w-full sm:w-64">
        <select id="categoryFilter"
                class="w-full p-3 rounded-xl border-2 border-gray-300 shadow-sm text-gray-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>
</div>

 <div id="assetList" class="grid grid-cols-1 px-6 sm:grid-cols-2 lg:grid-cols-4 gap-6">
 @foreach($assets as $asset) 
    @if($asset->assignment_type === 'Spare')
      @php
        // Ambil data pinjaman aktif jika ada
        $activeLoan = \App\Models\AssetLoan::with('user')
                        ->where('asset_id', $asset->id)
                        ->where('status', 'Approved') // atau 'Loaned' sesuai status
                        ->latest('date_loan')
                        ->first();
      @endphp

    <div class="asset-card relative bg-white rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 transition-all duration-500 transform hover:-translate-y-1 cursor-pointer group"
     data-name="{{ strtolower($asset->asset_name) }}"
     data-category="{{ strtolower($asset->asset_type) }}"
     @if($asset->status !== 'Loaned') 
         data-bs-toggle="modal" data-bs-target="#assetModal{{ $asset->id }}"
     @endif
>
    <!-- Gambar & Status -->
    <div class="aspect-[4/3] relative overflow-hidden rounded-t-2xl">
        <img src="{{ $asset->photo ? asset($asset->photo) : asset('uploads/assets/no-image.png') }}" 
             alt="{{ $asset->asset_name }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold rounded-full shadow-md
            {{ $asset->status === 'Loaned' ? 'bg-red-600 text-white' : 'bg-green-600 text-white' }}">
            {{ $asset->status ?? 'Available' }}
        </span>
    </div>

    <!-- Body -->
    <div class="p-4 space-y-3">
        <h5 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">{{ $asset->asset_name }}</h5>



        <!-- Card wrapper -->
<div class="bg-white shadow-md rounded-2xl p-4 border border-gray-100 hover:shadow-lg transition duration-300">

<h5 class="text-l font-bold text-gray-900 mb-4">Description</h5>

    <!-- Deskripsi 2 kolom -->
    <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-xs text-gray-800">
        <span class="font-semibold text-gray-500">Location:</span>
        <span>{{ $asset->location }}</span>

        <span class="font-semibold text-gray-500">Condition:</span>
        <span class="@switch($asset->conditions)
    @case('New') text-green-600 @break
    @case('Good') text-blue-600 @break
    @case('Broken') text-yellow-500 @break
    @case('Lost') text-red-600 @break
    @case('Disposed') text-purple-600 @break
    @default text-gray-800
@endswitch font-semibold">
    {{ $asset->conditions ?? '-' }}
</span>


        @if($activeLoan)
            <span class="font-semibold text-gray-500">Borrowed by:</span>
            <span>{{ $activeLoan->user->name }}</span>

            <span class="font-semibold text-gray-500">Purpose:</span>
            <span>{{ $activeLoan->purpose }}</span>

            <span class="font-semibold text-gray-500">Return Estimation:</span>
            <span>{{ \Carbon\Carbon::parse($activeLoan->return_estimation)->format('Y-m-d H:i') }}</span>
        @endif
    </div>

   <!-- Catatan -->
@if(!$activeLoan && $asset->note)
    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-3 text-sm text-yellow-800">
        <!-- Icon Information -->
        <i data-feather="info" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <!-- Teks Catatan -->
        <p>{{ $asset->note }}</p>
    </div>
@endif

</div>
    </div>

    <!-- Footer Button -->
    @if($asset->status !== 'Loaned')
        <div class="px-4 py-3 bg-white border-t border-gray-200 rounded-b-2xl text-center">
            <button 
                class="w-full py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-700 shadow-md hover:from-blue-600 hover:to-blue-800 transition-all duration-300 btn-loan"
                data-id="{{ $asset->id }}"
                data-name="{{ $asset->asset_name }}"
                data-location="{{ $asset->location }}"
                data-status="{{ $asset->status ?? 'Available' }}"
                data-condition="{{ $asset->conditions ?? '-' }}"
                data-note="{{ $asset->note ?? '-' }}"
                data-photo="{{ $asset->photo ? asset($asset->photo) : asset('uploads/assets/no-image.png') }}">
                Borrow Now
            </button>
        </div>
    @endif
</div>



    @endif
@endforeach

</div>


<div id="loanModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden z-50 transition-all duration-300">
  <div class="bg-white backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl w-full max-w-3xl mx-4 md:mx-0 overflow-hidden animate-fadeIn max-h-[70vh] flex flex-col">
    
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b text-gray-600">
      <h2 class="text-xl font-semibold flex text-gray-600 items-center gap-2">
        <i data-feather="clipboard" class="w-5 h-5"></i>
        Form Peminjaman Asset
      </h2>
      <button type="button" id="btnCancel" class="text-gray-600 hover:text-red-500 transition">
        <i data-feather="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Scrollable Content -->
    <form id="loanForm" method="POST" class="px-6 overflow-y-auto">
      @csrf
      <input type="hidden" name="asset_id" id="modalAssetId">
<label class="block text-sm font-medium text-gray-800 mb-1 mt-2">Base Information</label>
     <!-- Asset Info -->
<div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-2">
  <div class="flex flex-col md:flex-row items-stretch">
    
    <!-- Gambar -->
    <div class="md:w-1/3 bg-white flex items-center justify-center p-4 border-b md:border-b-0 md:border-r border-gray-100">
      <img 
        id="modalAssetPhoto" 
        src="{{ asset('uploads/assets/no-image.png') }}" 
        alt="Asset Image" 
        class="w-full max-h-48 object-contain rounded-xl transition-transform duration-300 hover:scale-105"
      >
    </div>

    <!-- Deskripsi -->
    <div class="md:w-2/3 p-5 flex flex-col justify-between mb-2">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <div class="bg-indigo-100 p-2 rounded-full">
            <i data-feather="info" class="w-4 h-4 text-indigo-600"></i>
          </div>
          Description
        </h3>

        <div class="space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-medium">Name</span>
            <span id="modalAssetName" class="text-gray-900 font-semibold text-right"></span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-medium">Location</span>
            <span id="modalAssetLocation" class="text-gray-900 font-semibold text-right"></span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-medium">Status</span>
            <span id="modalAssetStatus" class="text-gray-900 font-semibold text-right"></span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-medium">Condition</span>
            <span id="modalAssetCondition" class="text-gray-900 font-semibold text-right"></span>
          </div>
        </div>
      </div>

      <!-- Catatan sebagai alert -->
      <div class="mt-5">
        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg px-4 py-3">
          <div class="flex-shrink-0 mt-0.5">
            <i data-feather="alert-circle" class="w-5 h-5 text-yellow-600"></i>
          </div>
          <div class="text-sm leading-relaxed">
            <span id="modalAssetNote">Tidak ada catatan khusus untuk aset ini.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


      <!-- Form Fields -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-800 mb-1">Purpose</label>
          <textarea name="purpose" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" rows="2" required></textarea>
        </div>

       <div>
  <label class="block text-sm font-medium text-gray-800 mb-1">Tanggal Peminjaman</label>
  <input type="text" name="date_loan" id="date_loan" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="Select date & time" required>
</div>

<div>
  <label class="block text-sm font-medium text-gray-800 mb-1">Estimasi Pengembalian</label>
  <input type="text" name="return_estimation" id="return_estimation" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="Select date & time" required>
</div>
      </div>
    </form>

    <!-- Footer Buttons -->
    <div class="flex justify-end gap-3 p-6 border-t bg-white">
      <button type="button" id="btnCancel" 
        class="px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 transition">
        Batal
      </button>
      <button type="submit" form="loanForm"
        class="px-5 py-2 rounded-xl bg-green-600 text-white font-medium shadow-md hover:shadow-lg hover:scale-[1.02] transition">
        <i data-feather="send" class="inline-block w-4 h-4 mr-1"></i>
        Konfirmasi Pinjam
      </button>
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
            <h2 class="text-xl font-semibold text-gray-800">Reject Loan Request</h2>
        </div>

        <input type="hidden" id="rejectLoanId">

        <!-- Reason -->
        <div>
            <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-1">
                Reason for Rejection
            </label>
            <textarea  
                id="rejectReason"  
                rows="4" 
                required
                placeholder="Provide the reason for rejecting this loan (e.g., duplicate request, asset unavailable, invalid details, not under your department)..."
                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
            ></textarea>
            <p class="mt-1 text-xs text-gray-400">Be specific so the requester can understand and improve future requests.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <button 
                type="button" 
                id="btnRejectCancel"
                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg border border-gray-300 hover:bg-gray-300 transition"
            >
                Cancel
            </button>
            <button 
                id="btnRejectSubmit"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition"
            >
                Reject
            </button>
        </div>

    </div>
</div>

<!-- Modal Confirm Condition -->
<div id="conditionModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
  <div class="bg-white rounded-2xl shadow-2xl w-11/12 max-w-md p-6 relative animate-fadeIn">
    <button id="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 transition">
      <i data-feather="x" class="w-5 h-5"></i>
    </button>

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirm Condition After Return</h2>

    <form id="conditionForm" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <input type="hidden" name="loan_id" id="loan_id">

      <!-- Select Condition -->
      <div>
        <label class="block font-semibold text-gray-600 mb-1">Select Condition After Return</label>
        <p class="text-xs text-gray-400 mb-2">Don't forget to check condition after return</p>
        <select name="condition" id="condition" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none">
          <option value="">-- Choose Condition --</option>
          <option value="Same as Before">Same as Before</option>
          <option value="Broken">Broken</option>
        </select>
      </div>

      <!-- Upload Gambar (muncul jika Broken) -->
      <div id="brokenImageField" class="hidden">
        <label class="block font-semibold text-gray-600 mb-1">Broken Evidence</label>
        <input type="file" name="photo" id="photo"
               accept="image/*"
               class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-400 focus:border-blue-400 p-2 bg-gray-50">
        <p class="text-xs text-gray-400 mt-1">Upload a clear image showing the damage (max 2MB).</p>

        <!-- Preview -->
        <div id="photoPreview" class="mt-3 hidden">
          <img src="" alt="Preview" class="rounded-lg border border-gray-200 w-40 h-40 object-cover">
        </div>
      </div>

      <!-- Notes -->
      <div>
        <label class="block font-semibold text-gray-600 mb-1">Notes (optional)</label>
        <textarea name="notes" id="notes" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none resize-none"
                  placeholder="Add a note if necessary..."></textarea>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 mt-6">
        <button type="button" id="cancelModal"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition">
          Cancel
        </button>
        <button type="submit" id="submitCondition"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition flex items-center justify-center gap-2">
          <span>Confirm</span>
          <div id="loadingSpinner" class="hidden border-2 border-white border-t-transparent rounded-full w-4 h-4 animate-spin"></div>
        </button>
      </div>
    </form>
  </div>
</div>

<style>
    @keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out; }
</style>


@push('scripts')
<script>
    $(document).ready(function() {
  const $searchInput = $('#searchInput');
  const $categoryFilter = $('#categoryFilter');
  const $cards = $('.asset-card');

  function filterAssets() {
    const search = $searchInput.val().toLowerCase().trim();
    const category = $categoryFilter.val().toLowerCase().trim();

    $cards.each(function() {
      const name = $(this).data('name')?.toLowerCase().trim() || '';
      const cat = $(this).data('category')?.toLowerCase().trim() || '';

      const matchSearch = name.includes(search);
      const matchCategory = !category || cat === category;

      $(this).toggle(matchSearch && matchCategory);
    });
  }

  $searchInput.on('keyup', filterAssets);
  $categoryFilter.on('change', filterAssets);

 //buka modal saat klik "Pinjam Sekarang"
  document.querySelectorAll(".btn-loan").forEach(btn => {
    btn.addEventListener("click", function () {
      document.getElementById("modalAssetId").value = this.dataset.id;
      document.getElementById("modalAssetName").textContent = this.dataset.name;
      document.getElementById("modalAssetLocation").textContent = this.dataset.location;
      document.getElementById("modalAssetStatus").textContent = this.dataset.status;
      document.getElementById("modalAssetCondition").textContent = this.dataset.condition;
      document.getElementById("modalAssetNote").textContent = this.dataset.note;
       document.getElementById("modalAssetPhoto").src = this.dataset.photo;

      document.getElementById("loanModal").classList.remove("hidden");
    });
  });

  // tutup modal
  document.getElementById("btnCancel").addEventListener("click", function () {
    document.getElementById("loanModal").classList.add("hidden");
  });

  // klik luar modal untuk tutup
  document.getElementById("loanModal").addEventListener("click", function (e) {
    if (e.target.id === "loanModal") {
      this.classList.add("hidden");
    }
  });

   // Submit form AJAX
    $('#loanForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('facility.alo.store') }}", // gunakan route Laravel
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === 'success') {
                // Sembunyikan modal
                 // Tutup modal sebelum toast
            const modal = document.getElementById('loanModal');
            if(modal) modal.classList.add('hidden');

                // Tampilkan toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                // Reload halaman setelah 2 detik
                setTimeout(() => location.reload(), 2000);
            }
        },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '';
                    $.each(errors, function(key, msgs) {
                        $.each(msgs, function(i, msg) {
                            errorHtml += '- ' + msg + '\n';
                        });
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: errorHtml.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#EF4444'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        });
    });

    $('.btn-approve').on('click', function() {
    let loanId = $(this).data('loan-id');

    Swal.fire({
        title: 'Approve this loan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: "{{ route('facility.alo.approve') }}",
                type: 'POST',
                data: { loan_id: loanId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    // Tampilkan toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    // Refresh list setelah 2 detik
                    setTimeout(() => location.reload(), 2000);
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.responseJSON.message || 'Something went wrong!'
                    });
                }
            });
        }
    });
});

// Buka modal ketika klik reject
document.querySelectorAll('.btn-reject').forEach(btn => {
    btn.addEventListener('click', function() {
        const loanId = this.dataset.loanId;
        document.getElementById('rejectLoanId').value = loanId;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    });
});

// Tutup modal
document.getElementById('btnRejectCancel').addEventListener('click', function() {
    document.getElementById('rejectModal').classList.add('hidden');
});

// Submit reject via AJAX
document.getElementById('btnRejectSubmit').addEventListener('click', function() {
    const loanId = document.getElementById('rejectLoanId').value;
    const reason = document.getElementById('rejectReason').value;

    if(!reason.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan wajib diisi',
            confirmButtonColor: '#EF4444'
        });
        return;
    }

    $.ajax({
        url: "{{ route('facility.alo.reject') }}",
        type: 'POST',
        data: {
            loan_id: loanId,
            reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {

             // Tutup modal sebelum toast
            const modal = document.getElementById('rejectModal');
            if(modal) modal.classList.add('hidden');

            // Reset textarea
            document.getElementById('rejectReason').value = '';
           // Tampilkan toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: res.message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        // Reload setelah 2 detik
        setTimeout(() => location.reload(), 2000);
    },
        error: function(err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menolak loan. Silakan coba lagi.',
                confirmButtonColor: '#EF4444'
            });
        }
    });
});

$(document).on('click', '.btn-return', function() {
    let loanId = $(this).data('loan-id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You are returning this asset!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, return it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: "{{ route('facility.alo.return') }}",
                type: 'POST',
                data: { loan_id: loanId },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res) {
                  // Tampilkan toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    // Reload setelah 2 detik
                    setTimeout(() => location.reload(), 2000);
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: err.responseJSON?.message || 'Something went wrong.',
                        confirmButtonColor: '#EF4444'
                    });
                }
            });
        }
    });
});


    const modal = $('#conditionModal');
    const loanInput = $('#loan_id');
    const spinner = $('#loadingSpinner');
    const submitBtn = $('#submitCondition');
    const brokenField = $('#brokenImageField');
    const previewBox = $('#photoPreview img');

    // Buka modal
    $('.btn-check-condition').on('click', function() {
        const loanId = $(this).data('loan-id');
        loanInput.val(loanId);
        modal.removeClass('hidden');
    });

    // Tutup modal
    $('#closeModal, #cancelModal').on('click', function() {
        modal.addClass('hidden');
    });

    // Klik luar modal => tutup
    modal.on('click', function(e) {
        if (e.target === this) modal.addClass('hidden');
    });

     // Tampilkan field upload jika pilih "Broken"
    $('#condition').on('change', function() {
        if ($(this).val() === 'Broken') {
            brokenField.removeClass('hidden');
        } else {
            brokenField.addClass('hidden');
            $('#photo').val('');
            $('#photoPreview').addClass('hidden');
        }
    });

    // Preview gambar
    $('#photo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#photoPreview').removeClass('hidden');
                previewBox.attr('src', ev.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

     // Submit AJAX
    $('#conditionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        spinner.removeClass('hidden');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: "{{ route('facility.alo.condition') }}",
           type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                spinner.addClass('hidden');
                submitBtn.prop('disabled', false);
                modal.addClass('hidden');

                Swal.fire({
                    icon: 'success',
                    title: 'Condition confirmed!',
                    text: res.message || 'Asset condition updated successfully.',
                    showConfirmButton: false,
                    timer: 1800
                });

                setTimeout(() => location.reload(), 1500);
            },
            error: function(err) {
                spinner.addClass('hidden');
                submitBtn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON?.message || 'Failed to update condition.'
                });
            }
        });
    });



     });

     function cancelLoan(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to cancel this loan request!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:url,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        // Refresh list atau hapus row dari table/card
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                }
            });
        }
    });
}

// Fungsi bantu untuk format jam Flatpickr
function getCurrentTime() {
    const now = new Date();
    return `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
}

// Date Loan
flatpickr("#date_loan", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minuteIncrement: 5,
    minDate: "today", // tidak bisa memilih tanggal sebelum hari ini
    onReady: function(selectedDates, dateStr, instance) {
        // set minTime hanya jika pilih hari ini
        instance.set('minTime', getCurrentTime());
    },
    onChange: function(selectedDates, dateStr, instance) {
        const selectedDate = selectedDates[0];
        const today = new Date();
        if(selectedDate.toDateString() === today.toDateString()){
            instance.set('minTime', getCurrentTime()); // jika hari ini, jam tidak boleh mundur
        } else {
            instance.set('minTime', '00:00'); // hari lain bebas
        }
    }
});

// Return Estimation
flatpickr("#return_estimation", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minuteIncrement: 5,
    minDate: "today",
    onReady: function(selectedDates, dateStr, instance) {
        instance.set('minTime', getCurrentTime());
    },
    onChange: function(selectedDates, dateStr, instance) {
        const selectedDate = selectedDates[0];
        const today = new Date();
        if(selectedDate.toDateString() === today.toDateString()){
            instance.set('minTime', getCurrentTime());
        } else {
            instance.set('minTime', '00:00');
        }
    }
});
</script>
@endpush
@endsection
