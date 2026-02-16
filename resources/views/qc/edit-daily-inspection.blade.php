@extends('layouts.app')

@section('title', 'Edit Daily Inspection')
@section('page-title', 'EDIT DAILY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Edit Quality Inspection')

@section('content')
<div class="space-y-4">
  <div class="w-full bg-white shadow-md rounded-xl px-8 space-y-4 pt-6 pb-12">
   <div class="flex items-center gap-2 border-b border-gray-200 pb-3 mb-6">
      <i class="fa-solid fa-pen-to-square text-indigo-700 text-sm"></i>
      <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
        Edit Daily Inspection
      </h2>
    </div>

    <form id="inspectionForm" class="space-y-4" method="POST" data-id="{{ $inspection->id ?? '' }}">
    @csrf
    @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-1">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Inspection Date<span class="text-red-600"> *</span>
                </label>
                <input type="date" name="inspection_date" id="inspection_date"
                       value="{{ $inspection->inspection_date }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
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
                    @foreach(['Incoming','Unloading','Buffing','Touch Up','Final','Outgoing'] as $post)
                        <option value="{{ $post }}" {{ $inspection->inspection_post == $post ? 'selected' : '' }}>
                            {{ $post }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="spraybooth-wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Spray Booth <span class="text-red-600">*</span>
                </label>
                <select name="spraybooth" id="spraybooth"
                        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm">
                    <option value="">-- Pilih Booth --</option>
                    @foreach([
                        'Spraybooth 1A','Spraybooth 1B','Spraybooth 1C',
                        'Spraybooth 2A','Spraybooth 2B','Spraybooth 2C',
                        'Spraybooth 3A','Spraybooth 3B','Spraybooth 3C',
                        'Spraybooth 4A','Spraybooth 4B','Spraybooth 4C',
                        'Spraybooth 5A','Spraybooth 5B','Spraybooth 5C'
                    ] as $booth)
                        <option value="{{ $booth }}" {{ $inspection->spraybooth == $booth ? 'selected' : '' }}>
                            {{ $booth }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-2">
    <div id="supplier-wrapper">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Supplier <span class="text-red-600">*</span>
        </label>
        <select name="supplier" id="supplier" class="select2 w-full">
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->code }}"
                    {{ $inspection->supplier_code == $supplier->code ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="customer-wrapper" class="{{ $inspection->inspection_post !== 'Incoming' ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Customer <span class="text-red-600">*</span>
        </label>
        <select name="customer" id="customer" class="select2 w-full">
            <option value="">-- Pilih Customer --</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->code }}"
                    {{ $inspection->supplier_code == $customer->code ? 'selected' : '' }}>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>


            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
                <select name="part_name" id="part_name" class="select2 w-full">
                    <option value="">-- Select Part --</option>
                   
                </select>
            </div>
        </div>

        <!-- Row 3 & Row 4 tetap sama, tinggal isi value dari $inspection -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-3">
            <div id="check_method_container" class="w-full {{ $inspection->check_method ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Method <span class="text-red-600">*</span></label>
                <select name="check_method" id="check_method" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Choose Method --</option>
                    <option value="100%" {{ $inspection->check_method == '100%' ? 'selected' : '' }}>100% (A)</option>
                    <option value="Sampling" {{ $inspection->check_method == 'Sampling' ? 'selected' : '' }}>Sampling (S)</option>
                </select>
            </div>

            <div class="w-full {{ $inspection->qty_received ? '' : 'hidden' }}" id="qty-received-wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Qty Received <span class="text-red-600">*</span>
                </label>
                <input type="number" name="qty_received" id="qty_received"
                       value="{{ $inspection->qty_received }}"
                       placeholder="Masukan Qty Total Kedatangan Barang ..."
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-4">
            <div class="col-span-2 flex items-center gap-6 bg-gray-50 border border-gray-200 rounded-lg px-5 py-3 transition focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-300">
                <label for="total_check" class="text-sm font-medium text-gray-700 min-w-[110px]">
                    Total Check <span class="text-red-500">*</span>
                </label>
                <input type="number" name="total_check" id="total_check"
                       value="{{ $inspection->total_check }}"
                       placeholder="Masukan Qty. . ."
                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-300 transition"/>
            </div>
        </div>

        <!-- Table defect -->
        <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
                <i class="fa-solid fa-circle-exclamation text-indigo-700 text-sm"></i>
                <h2 class="text-base font-semibold text-indigo-700 tracking-wide">Add List Defect</h2>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table id="itemTable" class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-center font-medium min-w-[20px]">No</th>
                            <th class="px-3 py-2 font-medium min-w-[160px]">Defect</th>
                            <th class="px-3 py-2 text-center font-medium min-w-[60px]">Qty</th>
                            <th class="px-3 py-2 text-center font-medium min-w-[60px] ok-repair-wrapper">OK Repair</th>
                            <th class="px-3 py-2 font-medium min-w-[180px]">Note</th>
                            <th class="px-3 py-2 text-center font-medium min-w-[72px]">Action</th>
                        </tr>
                    </thead>
                    <tbody id="defectTableBody" class="divide-y divide-gray-100">
                        @foreach($inspection->defects as $i => $defect)
                            <tr id="row-{{ $i+1 }}">
                                <td class="text-center px-3 py-2">{{ $i+1 }}</td>
                                <td>
                                    <select name="defect[]" class="w-full px-2 py-1 border border-gray-300 rounded">
                                        <option value="">-- Choose Defect --</option>
                                        @foreach($allDefects as $d)
                                            <option value="{{ $d->id }}" {{ $defect->defect_id == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center px-3 py-2">
                                    <input type="number" name="qty[]" value="{{ $defect->qty }}" class="w-full text-center border rounded px-1 py-1">
                                </td>
                                <td class="text-center px-3 py-2 ok-repair-wrapper">
                                    <input type="number" name="ok_repair[]" value="{{ $defect->ok_repair }}" class="w-full text-center border rounded px-1 py-1">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="note[]" value="{{ $defect->note }}" class="w-full border rounded px-2 py-1">
                                </td>
                                <td class="text-center px-3 py-2">
                                    <button type="button" class="removeRowBtn text-red-500 font-bold">×</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex justify-end">
                <button type="button" id="addRowBtn" class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <span class="text-lg leading-none">+</span> Add Row
                </button>
            </div>
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

  let rowIndex = 1;
  let articleMap = {};

  const $checkMethod       = $('#check_method');
  const $qtyReceiving      = $('#qty_received');
  const $totalCheck        = $('#total_check');
  const $totalCheckDisplay = $('#totalCheckDisplay');

  const $postSelect        = $('#inspection_post');

  const $totalNGDisplay    = $('#totalNGDisplay');
  const $totalNGPercent    = $('#totalNGPercent');

  const $totalOkDisplay    = $('#totalOkDisplay');
  const $totalOkPercent    = $('#totalOkPercent');

  const $totalNCLabel      = $('#totalNCLabel');
  const $totalNCDisplay    = $('#totalNCDisplay');
  const $totalNCPercent    = $('#totalNCPercent');

  const $totalPTWrapper    = $('#totalPTWrapper');
  const $totalPTDisplay    = $('#totalPTDisplay');

  const $passRate          = $('#passRate');
  const $passTroughLabel   = $('#passTroughLabel');
  const $passTroughDisplay = $('#passTroughDisplay');

  // ==================== SELECT2 INIT ====================
  $('#inspection_post, #supplier, #customer, #check_method, #spraybooth').select2({
      placeholder: "-- Pilih --",
      allowClear: true,
      width: '100%'
  });

  // ==================== HELPER ====================
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

  function updateTotalCheck() {
      const method = $checkMethod.val();
      const qty = parseInt($qtyReceiving.val()) || 0;
      let val = '';
      if (method === '100%') val = qty;
      if (method === 'Sampling') val = getSamplingCheck(qty) || '';
      $totalCheck.val(val).trigger('input');
      $totalCheckDisplay.text(val);
  }

  function calculateTotalQty() {
      let total = 0;
      $('.qty-defect').each(function () {
          total += parseInt($(this).val()) || 0;
      });
      return total;
  }

  function validateTotalQty(changedInput = null) {
      const totalCheck = parseInt($('#total_check').val()) || 0;
      const totalQty = calculateTotalQty();

      if (totalCheck === 0) return;

      if (totalQty > totalCheck && changedInput) {
          const currentVal = parseInt($(changedInput).val()) || 0;
          const selisih = totalQty - totalCheck;
          $(changedInput).val(Math.max(currentVal - selisih, 0));
          Swal.fire({
              icon: 'error',
              title: 'Qty Melebihi Total Check',
              text: 'Akumulasi Qty Defect tidak boleh lebih dari Total Check.',
              confirmButtonColor: '#2563eb'
          });
      }
      updateAllSummary();
  }

  function toggleOkRepair() {
      const post = $postSelect.val();
      if (post === 'Incoming') {
          $('.ok-repair-wrapper').removeClass('hidden').find('input').prop('disabled', false).prop('required', true);
          $('[data-info="total-ok-repair"]').closest('div.flex').removeClass('hidden');
          $('[data-info="ok-repair-rate"]').closest('div.flex').removeClass('hidden');
          $passTroughLabel.text('Performance');
      } else {
          $('.ok-repair-wrapper').addClass('hidden').find('input').prop('disabled', true).prop('required', false).val('');
          $('[data-info="total-ok-repair"]').closest('div.flex').addClass('hidden');
          $('[data-info="ok-repair-rate"]').closest('div.flex').addClass('hidden');
          $passTroughLabel.text(post === 'Unloading' ? 'Pass Through' : 'Performance');
      }
  }

  // ==================== SUMMARY ====================
  function updateTotalNG() {
      let totalNG = 0;
      const totalCheck = parseInt($totalCheck.val()) || 0;

      $('#defectTableBody tr').each(function () {
          const defectText = $(this).find('.defect-select option:selected').text().trim();
          const qty = parseInt($(this).find('.qty-defect').val()) || 0;
          if (defectText.startsWith('NG')) totalNG += qty;
      });

      $totalNGDisplay.text(totalNG);
      $totalNGPercent.text(totalCheck > 0 ? ((totalNG / totalCheck) * 100).toFixed(0) : 0);
  }

  function updateTotalOK() {
      const totalCheck = parseInt($totalCheck.val()) || 0;
      const totalNG = parseInt($totalNGDisplay.text()) || 0;
      const totalOK = Math.max(totalCheck - totalNG, 0);
      $totalOkDisplay.text(totalOK);
      $totalOkPercent.text(totalCheck > 0 ? ((totalOK / totalCheck) * 100).toFixed(0) : 0);
  }

  function updateNcOrOkRepair() {
      const post = $postSelect.val();
      const totalCheck = parseInt($totalCheck.val()) || 0;
      let totalValue = 0;

      if (post === 'Incoming') {
          $totalNCLabel.text('Total OK Repair');
          $('input[name="ok_repair[]"]').each(function () {
              totalValue += parseInt($(this).val()) || 0;
          });
      } else {
          $totalNCLabel.text('Total NC');
          $('#defectTableBody tr').each(function () {
              const defectText = $(this).find('.defect-select option:selected').text().trim();
              const qty = parseInt($(this).find('.qty-defect').val()) || 0;
              if (defectText.startsWith('NC')) totalValue += qty;
          });
      }

      $totalNCDisplay.text(totalValue);
      $totalNCPercent.text(totalCheck > 0 ? ((totalValue / totalCheck) * 100).toFixed(0) : 0);
  }

  function updateTotalPassThrough() {
      const post = $postSelect.val();
      if (post === 'Incoming') {
          $totalPTWrapper.addClass('hidden');
          return;
      }
      $totalPTWrapper.removeClass('hidden');
      const totalCheck = parseInt($totalCheck.val()) || 0;
      const totalNG = parseInt($totalNGDisplay.text()) || 0;
      const totalNC = parseInt($totalNCDisplay.text()) || 0;
      const passThrough = Math.max(totalCheck - totalNG - totalNC, 0);
      $totalPTDisplay.text(passThrough);
  }

  function updatePassRate() {
      const totalCheck = parseInt($totalCheck.val()) || 0;
      const totalOK = parseInt($totalOkDisplay.text()) || 0;
      $passRate.text(totalCheck > 0 ? ((totalOK / totalCheck) * 100).toFixed(0) + '%' : '0%');
  }

  function updatePassTrough() {
      const post = $postSelect.val();
      const totalCheck = parseInt($totalCheck.val()) || 0;
      if (totalCheck <= 0) { $passTroughDisplay.text('0%'); return; }

      let numerator = 0;
      if (post === 'Incoming') {
          const totalOK = parseInt($totalOkDisplay.text()) || 0;
          const totalOkRepair = parseInt($totalNCDisplay.text()) || 0;
          numerator = Math.max(totalOK - totalOkRepair, 0);
          $passTroughLabel.text('Performance');
      } else {
          numerator = parseInt($totalPTDisplay.text()) || 0;
          $passTroughLabel.text(post === 'Unloading' ? 'Pass Through' : 'Performance');
      }
      $passTroughDisplay.text(((numerator / totalCheck) * 100).toFixed(0) + '%');
  }

  function updateAllSummary() {
      updateTotalNG();
      updateTotalOK();
      updateNcOrOkRepair();
      updateTotalPassThrough();
      updatePassRate();
      updatePassTrough();
  }

  // ==================== CREATE ROW ====================
  function createRow(index, defects = [], existing = null) {
      const $row = $('<tr>');

      let defectOptions = '<option value="">-- Choose Defect --</option>';
      defects.forEach(defect => {
          defectOptions += `<option value="${defect.id}" data-defect="${defect.defect}">${defect.category} - ${defect.defect}</option>`;
      });

      $row.html(`
        <td class="border p-2 text-center w-[20px]">${index}</td>
        <td class="border p-2 min-w-[140px]">
            <select name="defect_id[]" class="w-full border rounded defect-select">${defectOptions}</select>
        </td>
        <td class="border p-2 w-[60px]">
          <div class="flex items-stretch">
            <input type="number" name="qty[]" min="1" class="flex-1 border border-gray-300 border-r-0 rounded-l-md px-3 py-2 text-sm qty-defect text-right" required>
            <span class="inline-flex items-center px-3 text-sm text-gray-600 bg-gray-100 border border-gray-300 border-l-0 rounded-r-md">PCS</span>
          </div>
        </td>
        <td class="border p-2 w-[60px] ok-repair-wrapper">
          <div class="flex items-stretch">
            <input type="number" name="ok_repair[]" class="flex-1 border border-gray-300 border-r-0 rounded-l-md px-3 py-2 text-sm qty-ok-repair text-right">
            <span class="inline-flex items-center px-3 text-sm text-gray-600 bg-gray-100 border border-gray-300 border-l-0 rounded-r-md">PCS</span>
          </div>
        </td>
        <td class="border p-2 min-w-[120px]">
          <input type="text" name="note_defect[]" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-sm">
        </td>
        <td class="border p-2 text-center min-w-[60px]">
          <button type="button" class="removeBtn text-red-600 hover:text-red-800">X</button>
        </td>
      `);

      const $defectSelect = $row.find('.defect-select');
      $defectSelect.select2({ placeholder: '-- Choose Defect --', allowClear: true, width: '100%' });

      if (existing) {
          $defectSelect.val(existing.defect_id).trigger('change');
          $row.find('.qty-defect').val(existing.qty);
          $row.find('.qty-ok-repair').val(existing.ok_repair);
          $row.find('input[name="note_defect[]"]').val(existing.note);
      }

      $defectSelect.on('select2:select', function (e) {
          const currentDefect = e.params.data.element.dataset.defect?.trim().toLowerCase();
          if (!currentDefect) return;
          let isDuplicate = false;
          $('.defect-select').not(this).each(function () {
              const data = $(this).select2('data');
              if (!data.length) return;
              const defect = data[0].element.dataset.defect?.trim().toLowerCase();
              if (defect === currentDefect) isDuplicate = true;
          });
          if (isDuplicate) { $(this).val(null).trigger('select2:clear'); Swal.fire({icon:'warning',title:'Duplikasi Defect!',text:'Defect sama sudah dipilih.',confirmButtonText:'OK'});}
      });

      $row.find('.qty-ok-repair').on('input', function () {
          const qtyDefect = parseInt($row.find('.qty-defect').val()) || 0;
          const qtyOkRepair = parseInt($(this).val()) || 0;
          if (qtyOkRepair > qtyDefect) $(this).val(qtyDefect);
      });

      return $row;
  }

  // ==================== LOAD EXISTING DATA ====================
  const existingRows = window.editData || []; // backend kirim JSON
  const post = $('#inspection_post').val();
  if (post) {
      $.getJSON(`/qc/get-defects/${post}`, function (defects) {
          $('#defectTableBody').empty();
          rowIndex = 1;
          if (existingRows.length) {
              existingRows.forEach(data => {
                  $('#defectTableBody').append(createRow(rowIndex++, defects, data));
              });
          } else {
              $('#defectTableBody').append(createRow(rowIndex, defects));
          }
          toggleOkRepair();
          updateAllSummary();
      });
  }

  // ==================== EVENTS ====================
  $('#addRowBtn').on('click', function () {
      if (!post) return alert('Select inspection post first!');
      $.getJSON(`/qc/get-defects/${post}`, function (defects) {
          rowIndex++;
          $('#defectTableBody').append(createRow(rowIndex, defects));
          toggleOkRepair();
          updateAllSummary();
      });
  });

  $('#defectTableBody').on('click', '.removeBtn', function () {
      $(this).closest('tr').remove();
      $('#defectTableBody tr').each(function (i) { $(this).find('td:first').text(i + 1); });
      rowIndex = $('#defectTableBody tr').length;
      toggleOkRepair();
      updateAllSummary();
  });

  $(document).on('input', '.qty-defect', function () { validateTotalQty(this); updateAllSummary(); });
  $('#total_check').on('input', function () { validateTotalQty(); updateAllSummary(); });
  $checkMethod.on('change', updateTotalCheck);
  $qtyReceiving.on('input', updateTotalCheck);
});

$('#inspectionForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $('#submitBtn');
    const inspectionId = $form.data('id'); // penting untuk update

    // ===== Disable Button + Spinner =====
    const originalHtml = $btn.html();
    $btn.prop('disabled', true)
        .addClass('opacity-50 cursor-not-allowed')
        .html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

    const formData = new FormData(this);

    // ===== Method spoofing untuk PUT =====
    if (inspectionId) formData.set('_method', 'PUT');

    // ===== Kirim inspection ID =====
    formData.set('inspection_id', inspectionId);

    // ===== Incoming vs Non Incoming =====
    const post = $('#inspection_post').val();
    const supplierCode = (post === 'Incoming')
        ? $('#supplier').val()
        : $('#customer').val();
    formData.set('supplier_code', supplierCode);

    // ===== Summary =====
    formData.set('total_check', $('#totalCheckDisplay').text() || 0);
    formData.set('total_ok', $('#totalOkDisplay').text() || 0);
    formData.set('total_ng', $('#totalNGDisplay').text() || 0);
    formData.set('total_ok_repair', $('#totalNCDisplay').text() || 0);
    formData.set('pass_rate', $('#passRate').text() || 0);
    formData.set('pass_through', $('#passTroughDisplay').text() || 0);

    // ===== Bersihkan FormData defect =====
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

        if (!defectId) return;

        formData.append('defect_id[]', defectId);
        formData.append('qty[]', qty);
        formData.append('ok_repair[]', okRepair);
        formData.append('note_defect[]', note);
    });

    // ===== AJAX =====
    $.ajax({
        url: inspectionId ? `/qc/inspections/${inspectionId}` : '/qc/inspections/store',
        method: 'POST', // PUT dikirim via _method
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: inspectionId ? 'Updated' : 'Saved',
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

            $btn.prop('disabled', false)
                .removeClass('opacity-50 cursor-not-allowed')
                .html(originalHtml);
        }
    });
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

// ================== SET SELECTED UNTUK EDIT ==================
@if(isset($inspection))
const selectedArticle = {
    id: '{{ $inspection->part_name }}',
    text: '{{ $inspection->part_name }}' // bisa diganti description jika ada
};
const option = new Option(selectedArticle.text, selectedArticle.id, true, true);
$('#part_name').append(option).trigger('change');
@endif




</script>
@endpush
@endsection