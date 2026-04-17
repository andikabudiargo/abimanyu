@extends('layouts.app-op-qc')

@section('title', 'Create Daily Inspection')
@section('page-title', 'CREATE DAILY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Create Quality Inspection')

@section('content')
<div class="space-y-4">
  <div class="w-full bg-white shadow-md rounded-xl px-8 space-y-4 pt-6 pb-12">
   <div class="flex items-center gap-2 border-b border-gray-200 pb-3 mb-6">
  <i class="fa-solid fa-pen-to-square text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    Create Daily Inspection
  </h2>
</div>

    <form id="inspectionForm" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-1">
    <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
      Inspection Date<span class="text-red-600"> *</span>
    </label>

    <input type="date" name="inspection_date" id="inspection_date" max="{{ date('Y-m-d') }}"
  class="w-full px-3 py-2
         bg-gray-50
         border border-gray-300
         rounded-lg
         shadow-sm
         focus:outline-none
         focus:ring-2 focus:ring-blue-400
         focus:border-blue-400
         transition"
  required>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
      Inspection Post <span class="text-red-600">*</span>
    </label>

    <select name="inspection_post" id="inspection_post"
      class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm"
      required>

      <option value="">-- Choose Post --</option>
      <option value="Incoming">Incoming</option>
      <option value="Unloading">Unloading</option>
      <option value="Buffing">Buffing</option>
      <option value="Touch Up">Touch Up</option>
      <option value="Final">Final</option>
      <option value="Outgoing">Outgoing</option>

    </select>
  </div>

  <!-- Spray Booth -->
  <div id="spraybooth-wrapper">
    <label class="block text-sm font-medium text-gray-700 mb-1">
      Spray Booth <span class="text-red-600">*</span>
    </label>

    <select name="spraybooth" id="spraybooth"
      class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm">

      <option value="">-- Pilih Booth --</option>
      <option value="Spraybooth 1A">Spraybooth 1A</option>
      <option value="Spraybooth 1B">Spraybooth 1B</option>
      <option value="Spraybooth 1C">Spraybooth 1C</option>
      <option value="Spraybooth 2A">Spraybooth 2A</option>
      <option value="Spraybooth 2B">Spraybooth 2B</option>
      <option value="Spraybooth 2C">Spraybooth 2C</option>
      <option value="Spraybooth 3A">Spraybooth 3A</option>
      <option value="Spraybooth 3B">Spraybooth 3B</option>
      <option value="Spraybooth 3C">Spraybooth 3C</option>
      <option value="Spraybooth 4A">Spraybooth 4A</option>
      <option value="Spraybooth 4B">Spraybooth 4B</option>
      <option value="Spraybooth 4C">Spraybooth 4C</option>
      <option value="Spraybooth 5A">Spraybooth 5A</option>
      <option value="Spraybooth 5B">Spraybooth 5B</option>
      <option value="Spraybooth 5C">Spraybooth 5C</option>
    </select>
  </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-2">
  <!-- Supplier -->
  <div id="supplier-wrapper">
    <label class="block text-sm font-medium text-gray-700 mb-1">
      Supplier <span class="text-red-600">*</span>
    </label>

    <select name="supplier" id="supplier" class="select2 w-full">
      <option value="">-- Pilih Supplier --</option>

      @foreach ($suppliers as $supplier)
        <option value="{{ $supplier->code }}">
          {{ $supplier->name }}
        </option>
      @endforeach
    </select>
  </div>


  <!-- Customer -->
  <div id="customer-wrapper" class="hidden">
    <label class="block text-sm font-medium text-gray-700 mb-1">
      Customer <span class="text-red-600">*</span>
    </label>

    <select name="customer" id="customer" class="select2 w-full">
      <option value="">-- Pilih Customer --</option>

      @foreach ($customers as $customer)
        <option value="{{ $customer->code }}">
          {{ $customer->name }}
        </option>
      @endforeach
    </select>
  </div>

   <div class="w-full">
    <label class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
    <select name="part_name" id="part_name" class="select2 w-full">
      <option value="">-- Select Part --</option>
    </select>
  </div>
        </div>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-3">
  <!-- Check Method & Total Check -->
        <div id="check_method_container" class="w-full hidden">
          <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Method <span class="text-red-600">*</span></label>
          <select name="check_method" id="check_method" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Method --</option>
            <option value="100%">100% (A)</option>
            <option value="Sampling">Sampling (S)</option>
          </select>
        </div>
        
  <div class="w-full hidden" id="qty-received-wrapper">
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Qty Received <span class="text-red-600">*</span>
  </label>
  <input
    type="number"
    name="qty_received"
    id="qty_received"
    placeholder="Masukan Qty Total Kedatangan Barang ..."
    class="w-full px-3 py-2
         bg-gray-50
         border border-gray-300
         rounded-lg
         shadow-sm
         focus:outline-none
         focus:ring-2 focus:ring-blue-400
         focus:border-blue-400
         transition"
  required>
</div>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-4">
  <div
    class="col-span-2
           flex items-center gap-6
           bg-gray-50
           border border-gray-200
           rounded-lg
           px-5 py-3
           transition
           focus-within:border-blue-400
           focus-within:ring-1 focus-within:ring-blue-300">

    <!-- Label -->
    <label for="total_check"
      class="text-sm font-medium text-gray-700 min-w-[110px]">
      Total Check <span class="text-red-500">*</span>
    </label>

    <!-- Input -->
    <input
      type="number"
      name="total_check"
      id="total_check"
      placeholder="Masukan Qty. . ."
      class="flex-1
             px-3 py-2
             bg-white
             border border-gray-300
             rounded-md
             text-sm
             focus:outline-none
             focus:border-blue-400
             focus:ring-1 focus:ring-blue-300
             transition"/>
  </div>
</div>


</div>



      <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">
       <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
  <i class="fa-solid fa-circle-exclamation text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    Add List Defect
  </h2>
</div>


      <!-- Table -->
     <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
  <table id="itemTable" class="min-w-full text-sm text-gray-700">
    <thead class="bg-gray-100 border-b border-gray-200">
      <tr>
        <th class="px-3 py-2 text-center font-medium min-w-[20px]">No</th>
        <th class="px-3 py-2 text-center font-medium min-w-[80px]">Type</th> <!-- NEW -->
        <th class="px-3 py-2 font-medium min-w-[160px]">Defect</th>
        <th class="px-3 py-2 text-center font-medium min-w-[60px]">Qty</th>
        <th class="px-3 py-2 text-center font-medium min-w-[60px] ok-repair-wrapper">
          OK Repair
        </th>
        <th class="px-3 py-2 font-medium min-w-[180px]">Note</th>
        <th class="px-3 py-2 text-center font-medium min-w-[72px]">Action</th>
      </tr>
    </thead>

    <tbody id="defectTableBody"
      class="divide-y divide-gray-100">
      <!-- rows injected here -->
    </tbody>
  </table>
</div>

<div class="mt-3 flex justify-end">
  <button
    type="button"
    id="addRowBtn"
    class="inline-flex items-center gap-2
           bg-blue-600 text-white
           text-sm font-medium
           px-4 py-2
           rounded-md
           hover:bg-blue-700
           focus:outline-none focus:ring-2 focus:ring-blue-400">
    <span class="text-lg leading-none">+</span>
    Add Row
  </button>
</div>

<!-- Inspection Summary -->
<div class="mt-6 flex justify-start">
  <div class="w-full md:w-96 border border-gray-200 bg-white px-2 rounded-md pb-8">

   <div class="flex items-center gap-2 border-b border-gray-200 py-3 px-2 mb-6">
  <i class="fa-solid fa-file text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    Inspection Summary
  </h2>
   </div>

    <div class="divide-y divide-gray-100 text-sm">

 <div class="flex justify-between px-4 py-2 hidden">
  <span class="text-gray-600">Total Receiving</span>
  <span class="font-medium text-gray-900">
    <span id="totalReceiving">0</span>
  </span>
</div>

      <div class="flex justify-between px-4 py-2 hidden">
  <span class="text-gray-600">Total Defect Qty</span>
  <span class="font-medium text-gray-900">
    <span id="totalDefectQty">0</span>
    <span class="text-gray-500">(<span id="totalDefectPercent">0</span>%)</span>
  </span>
</div>

 <div class="flex justify-between px-4 py-2 border-t border-gray-200 mt-4">
  <span class="text-sm text-gray-600">Total Check</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalCheckDisplay">0</span>
  </span>
</div>

<div class="flex justify-between px-4 py-2">
  <span class="text-sm text-gray-600">Total OK</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalOkDisplay">0</span>
    <span class="text-gray-500">
      (<span id="totalOkPercent">0</span>%)
    </span>
  </span>
</div>


      <div class="flex justify-between px-4 py-2">
  <span class="text-sm text-gray-600">Total NG</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalNGDisplay">0</span>
    <span class="text-gray-500">
      (<span id="totalNGPercent">0</span>%)
    </span>
  </span>
</div>


      <div class="flex justify-between px-4 py-2">
  <span id="totalNCLabel" class="text-sm text-gray-600">Total NC / OK Repair</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalNCDisplay">0</span>
    <span class="text-gray-500">
      (<span id="totalNCPercent">0</span>%)
    </span>
  </span>
</div>

     <div id="totalPTWrapper" class="flex justify-between px-4 py-2">
  <span class="text-gray-600">Total Pass Through</span>
  <span id="totalPTDisplay" class="font-medium text-gray-900">0</span>
</div>

      <div class="flex justify-between px-4 py-2">
        <span class="text-gray-600">Pass Rate</span>
        <span id="passRate" class="font-medium text-gray-900">0</span>
      </div>

        <div class="flex justify-between px-4 py-2">
        <span id="passTroughLabel" class="text-gray-600">Pass Trough / Performance</span>
        <span id="passTroughDisplay" class="font-medium text-gray-900">0</span>
      </div>
    </div>
  </div>
</div>

<hr class="mt-8">
      <!-- Buttons -->
      <div class="flex flex-col md:flex-row gap-2 mt-4">
        <button id="resetBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Reset
        </button>
        <button type="submit" id="submitBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
          <i data-feather="save" class="h-4 w-4"></i> Save
        </button>
      </div>

    </form>
  </div>
</div>


<style>

  /* ✅ Perbaiki Border dan Padding Kolom */
  #itemTable th, #itemTable td {
    border: 1px solid #e5e7eb !important;  /* Tailwind gray-200 */
    padding: 8px 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    font-size: 0.875rem;  /* Tailwind text-sm */
  }

  /* ✅ Baris Genap & Ganjil */
  #itemTable tbody tr:nth-child(even) {
    background-color: #f9fafb !important;  /* Tailwind gray-50 */
  }
  #itemTable tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
  }

  /* ✅ Hover Warna */
  #itemTable tbody tr:hover {
    background-color: #e0f2fe !important;  /* Tailwind blue-100 */
  }

  /* ✅ Hilangkan border horizontal agar tampak lebih modern */
  #itemTable td, #itemTable th {
    border-left: none !important;
    border-right: none !important;
  }

  /* ✅ Pagar kiri-kanan (opsional) */
  #itemTable {
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
  }

  /* ✅ Perbaiki Search, Length, Info, Pagination */
  #itemTable_wrapper .dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.875rem;
  }

  #itemTable_wrapper .dataTables_length select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.875rem;
  }

  #itemTable_wrapper .dataTables_info {
    font-size: 0.75rem;  /* Tailwind text-xs */
    color: #6b7280;      /* Tailwind gray-500 */
  }

  #itemTable_wrapper .dataTables_paginate {
    font-size: 0.75rem;
  }

  /* ✅ Scroll wrapper */
  .datatable-container {
    overflow-x: auto;
  }

 .select2-container .select2-selection--single {
  height: 42px;
  background-color: #f9fafb; /* gray-50 */
  border: 1px solid #d1d5db; /* gray-300 */
  border-radius: 0.5rem;
}

.select2-container--default
.select2-selection--single
.select2-selection__rendered {
  line-height: 40px;
}

.select2-container--default
.select2-selection--single:focus,
.select2-container--default.select2-container--focus
.select2-selection--single {
  border-color: #60a5fa; /* blue-400 */
  box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.4);
}


 .defect-select {
  min-height: 1.8rem;
  line-height: 0.8rem;
}

select:disabled {
  background-color: #f3f4f6; /* Tailwind gray-100 */
  color: #9ca3af; /* Tailwind gray-400 */
}



</style>
@push('scripts')
<script>
  

$(document).ready(function () {

  /* =====================================================
   * DOM REFERENCES
   * ===================================================== */
  const $checkMethod        = $('#check_method');
  const $qtyReceiving       = $('#qty_received');
  const $totalCheck         = $('#total_check');
  const $totalCheckDisplay  = $('#totalCheckDisplay');

  const $postSelect         = $('#inspection_post');

  const $totalNGDisplay     = $('#totalNGDisplay');
  const $totalNGPercent     = $('#totalNGPercent');

  const $totalOkDisplay     = $('#totalOkDisplay');
  const $totalOkPercent     = $('#totalOkPercent');

  const $totalNCLabel       = $('#totalNCLabel');
  const $totalNCDisplay     = $('#totalNCDisplay');
  const $totalNCPercent     = $('#totalNCPercent');

  const $totalPTWrapper     = $('#totalPTWrapper');
  const $totalPTDisplay     = $('#totalPTDisplay');

  const $passRate = $('#passRate');

  const $passTroughLabel = $('#passTroughLabel');
  const $passTroughDisplay = $('#passTroughDisplay');


  /* =====================================================
   * HELPER
   * ===================================================== */
  function getSamplingCheck(qty) {
    if (qty >= 2 && qty <= 8) return 2;
    if (qty <= 15) return 3;
    if (qty <= 25) return 5;
    if (qty <= 50) return 8;
    if (qty <= 90) return 13;
    if (qty <= 150) return 20;
    if (qty <= 280) return 32;
    if (qty <= 500) return 50;
    if (qty <= 1200) return 80;
    if (qty <= 3200) return 125;
    if (qty <= 10000) return 200;
    if (qty <= 35000) return 315;
    return 0;
  }


  /* =====================================================
   * TOTAL CHECK
   * ===================================================== */
  function updateTotalCheck() {
    const method = $checkMethod.val();
    const qty    = parseInt($qtyReceiving.val()) || 0;

    let val = '';
    if (method === '100%') val = qty;
    if (method === 'Sampling') val = getSamplingCheck(qty) || '';

    $totalCheck.val(val).trigger('input');
  }

  function syncTotalCheckDisplay() {
    $totalCheckDisplay.text(parseInt($totalCheck.val()) || 0);
  }


  /* =====================================================
   * TOTAL DEFECT (VALIDASI)
   * ===================================================== */
  function updateTotalDefectSummary() {
    let totalDefect = 0;

    $('input[name="qty[]"]').each(function () {
      totalDefect += parseInt($(this).val()) || 0;
    });

    const totalCheck = parseInt($totalCheck.val()) || 0;

    if (totalCheck > 0 && totalDefect > totalCheck) {
      Swal.fire({
        icon: 'warning',
        title: 'Validasi Gagal',
        text: 'Total Defect Qty melebihi Total Check. Nilai akan direset.'
      });

      $('input[name="qty[]"]').val(0);
      totalDefect = 0;
    }

    $('#totalDefectQty').text(totalDefect);

    const percent = totalCheck > 0
      ? ((totalDefect / totalCheck) * 100).toFixed(0)
      : 0;

    $('#totalDefectPercent').text(percent);
  }


  /* =====================================================
   * TOTAL NG
   * ===================================================== */
  function updateTotalNG() {
    let totalNG = 0;
    const totalCheck = parseInt($totalCheck.val()) || 0;

    $('#defectTableBody tr').each(function () {

        const type = $(this).find('.defect-type').val(); // 🔥 ambil dari dropdown
        const qty  = parseInt($(this).find('.qty-defect').val()) || 0;

        if (type === 'NG') {
            totalNG += qty;
        }
    });

    $totalNGDisplay.text(totalNG);

    const percent = totalCheck > 0
        ? ((totalNG / totalCheck) * 100).toFixed(0)
        : 0;

    $totalNGPercent.text(percent);
}


  /* =====================================================
   * TOTAL OK
   * ===================================================== */
  function updateTotalOK() {
    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalNG    = parseInt($totalNGDisplay.text()) || 0;

    const totalOK = Math.max(totalCheck - totalNG, 0);
    $totalOkDisplay.text(totalOK);

    const percent = totalCheck > 0
      ? ((totalOK / totalCheck) * 100).toFixed(0)
      : 0;

    $totalOkPercent.text(percent);
  }


  /* =====================================================
   * NC / OK REPAIR
   * ===================================================== */
  function updateNcOrOkRepair() {
    const post = $postSelect.val();
    const totalCheck = parseInt($totalCheck.val()) || 0;
    let totalValue = 0;

    if (post === 'Incoming') {
        // 🔵 Mode OK Repair
        $totalNCLabel.text('Total OK Repair');

        $('input[name="ok_repair[]"]').each(function () {
            totalValue += parseInt($(this).val()) || 0;
        });

    } else {
        // 🔴 Mode NC
        $totalNCLabel.text('Total NC');

        $('#defectTableBody tr').each(function () {

            const type = $(this).find('.defect-type').val(); // 🔥 pakai ini
            const qty  = parseInt($(this).find('.qty-defect').val()) || 0;

            if (type === 'NC') {
                totalValue += qty;
            }
        });
    }

    $totalNCDisplay.text(totalValue);

    const percent = totalCheck > 0
        ? ((totalValue / totalCheck) * 100).toFixed(0)
        : 0;

    $totalNCPercent.text(percent);
}


  /* =====================================================
   * TOTAL PASS THROUGH
   * ===================================================== */
  function updateTotalPassThrough() {
    const post = $postSelect.val();

    if (post === 'Incoming') {
      $totalPTWrapper.addClass('hidden');
      return;
    }

    $totalPTWrapper.removeClass('hidden');

    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalNG    = parseInt($totalNGDisplay.text()) || 0;
    const totalNC    = parseInt($totalNCDisplay.text()) || 0;

    const passThrough = Math.max(
      totalCheck - totalNG - totalNC,
      0
    );

    $totalPTDisplay.text(passThrough);
  }

  /*======================================================
   * PASS RATE
   * ===================================================== */
  function updatePassRate() {
    const totalCheck = parseInt($totalCheck.val()) || 0;
  const totalOK    = parseInt($totalOkDisplay.text()) || 0;

  let passRate = 0;

  if (totalCheck > 0) {
    passRate = ((totalOK / totalCheck) * 100).toFixed(0);
  }

  $passRate.text(`${passRate}%`);
  }

   /* =====================================================
   * PASS THROUGH
   * ===================================================== */

    function updatePassTrough() {
    const post       = $postSelect.val();
    const totalCheck = parseInt($totalCheck.val()) || 0;

    let numerator = 0;
    let percent   = 0;

  if (totalCheck <= 0) {
    $passTroughDisplay.text('0%');
    return;
  }

  if (post === 'Incoming') {
    // ================= PERFORMANCE =================
    $passTroughLabel.text('Performa');

    const totalOK       = parseInt($totalOkDisplay.text()) || 0;
    const totalOkRepair = parseInt($totalNCDisplay.text()) || 0;

    numerator = totalOK - totalOkRepair;

  } else {
    // ================= PASS THROUGH =================
    $passTroughLabel.text('Pass Through');

    const totalPassThrough =
      parseInt($totalPTDisplay.text()) || 0;

    numerator = totalPassThrough;
  }

  if (numerator < 0) numerator = 0;

  percent = ((numerator / totalCheck) * 100).toFixed(0);
  $passTroughDisplay.text(`${percent}%`);
}

/* =====================================================
   * RESET SUMMARY
   * ===================================================== */
function resetSummary() {
  $totalNGDisplay.text(0);
  $totalNGPercent.text(0);

  $totalOkDisplay.text(0);
  $totalOkPercent.text(0);

  $totalNCDisplay.text(0);
  $totalNCPercent.text(0);

  $totalPTDisplay.text(0);
  $passTroughDisplay.text('0%');
}


  /* =====================================================
   * MASTER UPDATE
   * ===================================================== */
  function updateAllSummary() {
    updateTotalNG();
    updateTotalOK();
    updateNcOrOkRepair();
    updateTotalPassThrough();
    updatePassRate();
    updatePassTrough();
  }



 /* =====================================================
 * EVENTS
 * ===================================================== */
$checkMethod.on('change', updateTotalCheck);
$qtyReceiving.on('input', updateTotalCheck);

$totalCheck.on('input', function () {
  syncTotalCheckDisplay();
  updateAllSummary();
});

$(document).on(
  'input change',
  '.qty-defect, .defect-select, .qty-ok-repair',
  updateAllSummary
);

$(document).on('click', '.removeBtn', function () {
  $(this).closest('tr').remove();
  updateAllSummary();
});

/* ===== POST CHANGE (FIXED) ===== */
$postSelect.on('change', function () {
  resetSummary();       // ⬅️ WAJIB
  updateAllSummary();   // ⬅️ HITUNG ULANG SESUAI POST BARU
});


   /* =====================================================
   * INIT SELECT 2
   * ===================================================== */

$('#inspection_post').select2({
  placeholder: "-- Pilih Inspection Post --",
  allowClear: true,
  width: '100%'
});

$('#supplier').select2({
  placeholder: "-- Pilih Supplier --",
  allowClear: true,
  width: '100%'
});

$('#customer').select2({
  placeholder: "-- Pilih Customer --",
  allowClear: true,
  width: '100%'
});


 $('#check_method').select2({
  placeholder: "-- Pilih Metode Inspection --",
  allowClear: true,
  width: '100%'
});

$('#spraybooth').select2({
  placeholder: "-- Pilih Booth --",
  allowClear: true,
  width: '100%'
});

 feather.replace();



 /* =====================================================
   * DEFECT TABLE HANDLE
   * ===================================================== */
 let rowIndex = 1;
// Function buat row
function createRow(index, defects = []) {
    const $row = $('<tr>');

   let defectOptions = '<option value="">-- Choose Defect --</option>';
defects.forEach(defect => {
    defectOptions += `
      <option 
        value="${defect.id}" 
        data-defect="${defect.defect}"> ${defect.defect}
      
      </option>`;
});


    $row.html(`
        <td class="border p-2 text-center w-[20px]">${index}</td>
         <!-- NEW DROPDOWN NC / NG -->
    <td class="border p-2 w-[120px]">
        <select name="defect_type[]" class="w-full border p-2 rounded defect-type" required>
            <option value="">--</option>
            <option value="NC">NC</option>
            <option value="NG">NG</option>
        </select>
    </td>
        <td class="border p-2 min-w-[140px]">
            <select name="defect_id[]" class="w-full border rounded defect-select">
                ${defectOptions}
            </select>
        </td>
       <td class="border p-2 w-[60px]">
  <div class="flex items-stretch">
    <input
      type="number"
      name="qty[]"
      min="1"
      class="flex-1
             border border-gray-300
             border-r-0
             rounded-l-md
             px-3 py-2
             text-sm
             focus:outline-none
             focus:border-blue-400
             focus:ring-1 focus:ring-blue-300
             qty-defect
             text-right"
      required
    >
    <span
      class="inline-flex
             items-center
             px-3
             text-sm
             text-gray-600
             bg-gray-100
             border border-gray-300
             border-l-0
             rounded-r-md">
      PCS
    </span>
  </div>
</td>

     <td class="border p-2 w-[60px] ok-repair-wrapper">
     <div class="flex items-stretch">
    <input type="number"
           name="ok_repair[]"
           class="flex-1
             border border-gray-300
             border-r-0
             rounded-l-md
             px-3 py-2
             text-sm
             focus:outline-none
             focus:border-blue-400
             focus:ring-1 focus:ring-blue-300
             qty-ok-repair
             text-right"
    >
     <span
      class="inline-flex
             items-center
             px-3
             text-sm
             text-gray-600
             bg-gray-100
             border border-gray-300
             border-l-0
             rounded-r-md">
      PCS
    </span>
  </div>
</td>


        <td class="border p-2 min-w-[120px]">
            <input type="text" name="note_defect[]" class="w-full px-3 py-2
         bg-gray-50
         border border-gray-300
         rounded-lg
         shadow-sm
         focus:outline-none
         focus:ring-2 focus:ring-blue-400
         focus:border-blue-400
         transition"">
        </td>
        <td class="border p-2 text-center min-w-[60px]">
            <button type="button" class="removeBtn text-red-600 hover:text-red-800">X</button>
        </td>
    `);

    $row.data('allDefects', defects);

    function filterDefectOptions($row, type) {
    const defects = $row.data('allDefects') || [];
    const $defectSelect = $row.find('.defect-select');

    let options = '<option value="">-- Choose Defect --</option>';

    defects.forEach(defect => {
        if (!type || defect.category === type) {
            options += `
                <option 
                    value="${defect.id}" 
                    data-defect="${defect.defect}">
                   ${defect.defect}
                </option>`;
        }
    });

    // destroy & re-init select2
    $defectSelect.html(options).val(null).trigger('change');

    $defectSelect.select2({
        placeholder: '-- Choose Defect --',
        allowClear: true,
        width: '100%'
    });
}

$row.find('.defect-type').on('change', function () {
    const type = $(this).val(); // NC / NG
    filterDefectOptions($row, type);
});

    // Init Select2
const $defectSelect = $row.find('.defect-select');

$defectSelect.select2({
    placeholder: '-- Choose Defect --',
    allowClear: true,
    width: '100%'
});



/// Saat defect dipilih
$defectSelect.on('select2:select', function (e) {
    const val = $(this).val();

    const currentDefect = e.params.data.element.dataset.defect
        ?.trim()
        .toLowerCase();

    if (!currentDefect) return;

    let isDuplicate = false;

    $('.defect-select').not(this).each(function () {

        const data = $(this).select2('data');
        if (!data.length) return;

        const defect = data[0].element.dataset.defect
            ?.trim()
            .toLowerCase();

        if (defect === currentDefect) {
            isDuplicate = true;
        }
    });

    if (isDuplicate) {
        Swal.fire({
            icon: 'warning',
            title: 'Duplikasi Defect!',
            text: 'Defect yang sama sudah dipilih di baris lain.',
            confirmButtonText: 'OK'
        });

        // reset TANPA trigger change
        $(this).val(null).trigger('select2:clear');
    }
});


    // Validasi OK Repair <= Qty Defect
    $row.find('.qty-ok-repair').on('input', function () {
        const qtyDefect = parseInt($row.find('.qty-defect').val()) || 0;
        const qtyOkRepair = parseInt($(this).val()) || 0;

        if (qtyOkRepair > qtyDefect) {
            Swal.fire({
                icon: 'error',
                title: 'Input tidak valid',
                text: 'Qty OK Repair tidak boleh melebihi Qty Defect di baris ini.',
                confirmButtonText: 'OK'
            });
            $(this).val(qtyDefect);
        }
    });
toggleOkRepair();

    return $row;
}

function toggleOkRepair() {
    const post = $('#inspection_post').val();

    // === OK Repair summary ===
    const $totalOkRepairRow = $('[data-info="total-ok-repair"]').closest('div.flex');
    const $okRepairRateRow  = $('[data-info="ok-repair-rate"]').closest('div.flex');

    // === KPI label ===
    const $passTroughLabel = $('[data-label="pass-trough-label"]');

    if (post === 'Incoming') {

        // OK Repair aktif
        $('.ok-repair-wrapper')
            .removeClass('hidden')
            .find('input')
            .prop('required', true)
            .prop('disabled', false);

        // summary OK Repair tampil
        $totalOkRepairRow.removeClass('hidden');
        $okRepairRateRow.removeClass('hidden');

        // KPI
        $passTroughLabel.text('Performance');

    } else {

        // OK Repair mati
        $('.ok-repair-wrapper')
            .addClass('hidden')
            .find('input')
            .prop('required', false)
            .prop('disabled', true)
            .val('');

        // summary OK Repair sembunyi
        $totalOkRepairRow.addClass('hidden');
        $okRepairRateRow.addClass('hidden');

        // KPI
        if (post === 'Unloading') {
            $passTroughLabel.text('Pass Through');
        } else {
            $passTroughLabel.text('Performance');
        }
    }
}




    $('#inspection_post').on('change', function () {

  const post = $(this).val();

  // === Element cache ===
  const supplierWrap = $('#supplier-wrapper');
  const customerWrap = $('#customer-wrapper');
  const qtyWrap      = $('#qty-received-wrapper');
  const checkMethod  = $('#check_method_container');
  const sprayboothWrap = $('#spraybooth-wrapper');

  // ================= RESET SEMUA =================
  supplierWrap.addClass('hidden');
  customerWrap.addClass('hidden');
  qtyWrap.addClass('hidden');
  checkMethod.addClass('hidden');
  sprayboothWrap.addClass('hidden');

  $('#supplier, #customer').prop('required', false);
  $('#qty_received').prop('required', false).val('');
  $('#check_method').prop('required', false).val('');
  $('#spraybooth').prop('required', false).val('');

  // ================= LOGIC =================
  if (post === 'Incoming') {

    // Supplier ON
    supplierWrap.removeClass('hidden');
    $('#supplier').prop('required', true);

    // Qty Received ON
    qtyWrap.removeClass('hidden');
    $('#qty_received').prop('required', true);

    // Check Method ON
    checkMethod.removeClass('hidden');
    $('#check_method').prop('required', true);

    sprayboothWrap.addClass('hidden');
    $('#spraybooth').val(null).trigger('change');

    // Customer OFF
    customerWrap.addClass('hidden');
    $('#customer').val(null).trigger('change');

    

  } else if (post) {

    // Customer ON
    customerWrap.removeClass('hidden');
    $('#customer').prop('required', true);

    // Supplier OFF
    supplierWrap.addClass('hidden');
    $('#supplier').val(null).trigger('change');

     sprayboothWrap.removeClass('hidden');
    $('spraybooth').prop('required', true);

  }

  // Reset Part setiap ganti post
  $('#part_name').val(null).trigger('change');

});


   // ===== DEFAULT ROW SAAT PAGE LOAD =====
const post = $('#inspection_post').val();

if (post) {
    $.getJSON(`/qc/get-defects/${post}`, function (defects) {
        $('#defectTableBody').append(createRow(rowIndex, defects));
        toggleOkRepair();              // 🔥 WAJIB
        feather.replace();
    });
} else {
    $('#defectTableBody').append(createRow(rowIndex, []));
    toggleOkRepair();                  // 🔥 WAJIB
    feather.replace();
}

// ===== UPDATE ROW SAAT INSPECTION POST BERUBAH =====
$('#inspection_post').on('change', function () {
    const post = $(this).val();
    if (!post) return;

    $.getJSON(`/qc/get-defects/${post}`, function (defects) {
        $('#defectTableBody').empty();
        rowIndex = 1;

        $('#defectTableBody').append(createRow(rowIndex, defects));

        toggleOkRepair();              // 🔥 INI YANG KURANG
        feather.replace();
        totalOkRepair();
    });
});

// ===== ADD ROW =====
$('#addRowBtn').on('click', function () {
    const post = $('#inspection_post').val();
    if (!post) return alert('Select inspection post first!');

    $.getJSON(`/qc/get-defects/${post}`, function (defects) {
        rowIndex++;
        $('#defectTableBody').append(createRow(rowIndex, defects));

        toggleOkRepair();              // aman
        feather.replace();
        totalOkRepair();
    });
});

// ===== REMOVE ROW =====
$('#defectTableBody').on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();

    $('#defectTableBody tr').each(function (i) {
        $(this).find('td:first').text(i + 1);
    });

    rowIndex = $('#defectTableBody tr').length;
    toggleOkRepair();                  // 🔥 WAJIB
});



let articleMap = {};

// ================== PART SELECT2 ==================

$('#part_name').select2({
  placeholder: "-- Select Part --",
  allowClear: true,
  width: '100%',
  ajax: {
    url: '/qc/get-articles',
    dataType: 'json',
    data: params => {
      const post = $('#inspection_post').val();

      return {
        term: params.term,
        post: post,
        supplier: post === 'Incoming'
          ? $('#supplier').val()
          : $('#customer').val()
      };
    },
    processResults: data => {
      articleMap = {};

      data.forEach(item => {
        articleMap[item.article_code] = item;
      });

      return {
        results: data.map(item => ({
          id: item.article_code,
          text: item.description
        }))
      };
    },
    cache: true
  }
});

// ================== RESET PART ==================
$('#inspection_post, #supplier, #customer').on('change', function () {
  $('#part_name').val(null).trigger('change');
});

// ================== PART CHANGE ==================
$('#part_name').on('change', function () {
  const data = articleMap[$(this).val()];
  if (!data) return;

  $('[data-info="part-name"]').text(data.description || '-');

  // supplier / customer disatukan
  $('[data-info="supplier"]').text(data.partner_name || '-');
  $('#supplier_code').val(data.partner_code || '');
});


$('#inspectionForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $('#submitBtn');

    // ===== Disable Button + Spinner =====
    const originalHtml = $btn.html();
    $btn.prop('disabled', true)
        .addClass('opacity-50 cursor-not-allowed')
        .html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

    const formData = new FormData(this);

    // ===== Incoming vs Non Incoming =====
    const post = $('#inspection_post').val();
    const supplierCode = (post === 'Incoming')
        ? $('#supplier').val()
        : $('#customer').val();
    formData.set('supplier_code', supplierCode);

    // ===== Summary (text → backend) =====
    formData.set('total_check', $('#totalCheckDisplay').text() || 0);
    formData.set('total_ok', $('#totalOkDisplay').text() || 0);
    formData.set('total_ng', $('#totalNGDisplay').text() || 0);
    formData.set('total_ok_repair', $('#totalNCDisplay').text() || 0);

    // ===== Bersihkan dulu FormData defect =====
    formData.delete('defect_id[]');
    formData.delete('qty[]');
    formData.delete('ok_repair[]');
    formData.delete('note_defect[]');

    // ===== Loop semua row defect =====
    $('#defectTableBody tr').each(function(i, tr) {
        const defectId = $(tr).find('.defect-select').val();
        const qty      = $(tr).find('.qty-defect').val() || 0;
        const okRepair = $(tr).find('.qty-ok-repair').val() || 0;
        const note     = $(tr).find('input[name="note_defect[]"]').val() || null;

        // Safety check: skip row jika defectId kosong
        if (!defectId) return;

        formData.append('defect_id[]', defectId);
        formData.append('qty[]', qty);
        formData.append('ok_repair[]', okRepair);
        formData.append('note_defect[]', note);
    });

    // ===== Kirim ke backend =====
    $.ajax({
        url: '/qc/inspections/store',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'Saved',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        },

        error: function (xhr) {
            let msg = 'Something went wrong';

            if (xhr.status === 422 && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors)
                    .map(e => e.join(', '))
                    .join('<br>');
            }

            Swal.fire('Error', msg, 'error');

            // re-enable button
            $btn.prop('disabled', false)
                .removeClass('opacity-50 cursor-not-allowed')
                .html(originalHtml);
        }
    });
});

// ===============================
// VALIDASI TOTAL QTY DEFECT
// ===============================

function calculateTotalQty() {
    let total = 0;
    $('.qty-defect').each(function () {
        const val = parseInt($(this).val()) || 0;
        total += val;
    });
    return total;
}

function validateTotalQty(changedInput = null) {
    const totalCheck = parseInt($('#total_check').val()) || 0;
    const totalQty   = calculateTotalQty();

    if (totalCheck === 0) return; // kalau belum isi total_check, skip dulu

    if (totalQty > totalCheck) {

        if (changedInput) {
            const currentVal = parseInt($(changedInput).val()) || 0;
            const selisih = totalQty - totalCheck;
            const corrected = currentVal - selisih;

            $(changedInput).val(corrected > 0 ? corrected : 0);
        }

        Swal.fire({
            icon: 'error',
            title: 'Qty Melebihi Total Check',
            text: 'Akumulasi Qty Defect tidak boleh lebih dari Total Check.',
            confirmButtonColor: '#2563eb'
        });
    }
}

// Trigger saat qty berubah
$(document).on('input', '.qty-defect', function () {
    validateTotalQty(this);
});

// Trigger juga kalau total_check berubah
$('#total_check').on('input', function () {
    validateTotalQty();
});


});


</script>
@endpush
@endsection
 
