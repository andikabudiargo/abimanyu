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
      <span class="ml-auto text-xs text-gray-400 font-mono">ID: #{{ $inspection->id }}</span>
    </div>

    <form id="inspectionForm" class="space-y-4">
      @csrf
      @method('PUT')
      <input type="hidden" name="inspection_id" value="{{ $inspection->id }}">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-1">
        <!-- Inspection Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Inspection Date<span class="text-red-600"> *</span>
          </label>
          <input type="date" name="inspection_date" id="inspection_date"
            max="{{ date('Y-m-d') }}"
            value="{{ \Carbon\Carbon::parse($inspection->inspection_date)->format('Y-m-d') }}"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
            required>
        </div>

        <!-- Inspection Post -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Inspection Post <span class="text-red-600">*</span>
          </label>
          <select name="inspection_post" id="inspection_post"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm"
            required>
            <option value="">-- Choose Post --</option>
            @foreach(['Incoming','Unloading','Buffing','Touch Up','Final','Outgoing'] as $post)
              <option value="{{ $post }}" {{ $inspection->inspection_post === $post ? 'selected' : '' }}>
                {{ $post }}
              </option>
            @endforeach
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
            @foreach(['1A','1B','1C','2A','2B','2C','3A','3B','3C','4A','4B','4C','5A','5B','5C'] as $booth)
              <option value="Spraybooth {{ $booth }}"
                {{ $inspection->spraybooth === "Spraybooth $booth" ? 'selected' : '' }}>
                Spraybooth {{ $booth }}
              </option>
            @endforeach
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
            @foreach ($suppliers as $sup)
              <option value="{{ $sup->code }}"
                {{ $inspection->inspection_post === 'Incoming' && $inspection->supplier_code === $sup->code ? 'selected' : '' }}>
                {{ $sup->name }}
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
            @foreach ($customers as $cust)
              <option value="{{ $cust->code }}"
                {{ $inspection->inspection_post !== 'Incoming' && $inspection->supplier_code === $cust->code ? 'selected' : '' }}>
                {{ $cust->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Part Name -->
        <div class="w-full">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Part Name <span class="text-red-600">*</span>
          </label>
          <select name="part_name" id="part_name" class="select2 w-full">
            {{-- Pre-fill selected option --}}
            @if($inspection->article_code && $inspection->article_description)
              <option value="{{ $inspection->article_code }}" selected>
                {{ $inspection->article_description }}
              </option>
            @else
              <option value="">-- Select Part --</option>
            @endif
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-3">
        <!-- Check Method -->
        <div id="check_method_container" class="w-full hidden">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Inspection Method <span class="text-red-600">*</span>
          </label>
          <select name="check_method" id="check_method"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm
                   focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Method --</option>
            <option value="100%" {{ $inspection->check_method === '100%' ? 'selected' : '' }}>100% (A)</option>
            <option value="Sampling" {{ $inspection->check_method === 'Sampling' ? 'selected' : '' }}>Sampling (S)</option>
          </select>
        </div>

        <!-- Qty Received -->
        <div class="w-full hidden" id="qty-received-wrapper">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Qty Received <span class="text-red-600">*</span>
          </label>
          <input type="number" name="qty_received" id="qty_received"
            value="{{ $inspection->qty_received }}"
            placeholder="Masukan Qty Total Kedatangan Barang ..."
            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="row-4">
        <div class="col-span-2 flex items-center gap-6 bg-gray-50 border border-gray-200 rounded-lg
                    px-5 py-3 transition focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-300">
          <label for="total_check" class="text-sm font-medium text-gray-700 min-w-[110px]">
            Total Check <span class="text-red-500">*</span>
          </label>
          <input type="number" name="total_check" id="total_check"
            value="{{ $inspection->total_check }}"
            placeholder="Masukan Qty. . ."
            class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm
                   focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-300 transition"/>
        </div>
      </div>
    </div>

    <!-- ==================== DEFECT SECTION ==================== -->
    <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">
      <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
        <i class="fa-solid fa-circle-exclamation text-indigo-700 text-sm"></i>
        <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
          Edit List Defect
        </h2>
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
            {{-- Rows diisi via JS (lihat existingDefects di bawah) --}}
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex justify-end">
        <button type="button" id="addRowBtn"
          class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-medium
                 px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
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
                <span class="text-gray-500">(<span id="totalOkPercent">0</span>%)</span>
              </span>
            </div>

            <div class="flex justify-between px-4 py-2">
              <span class="text-sm text-gray-600">Total NG</span>
              <span class="text-sm font-semibold text-gray-900">
                <span id="totalNGDisplay">0</span>
                <span class="text-gray-500">(<span id="totalNGPercent">0</span>%)</span>
              </span>
            </div>

            <div class="flex justify-between px-4 py-2">
              <span id="totalNCLabel" class="text-sm text-gray-600">Total NC / OK Repair</span>
              <span class="text-sm font-semibold text-gray-900">
                <span id="totalNCDisplay">0</span>
                <span class="text-gray-500">(<span id="totalNCPercent">0</span>%)</span>
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
        <a href="{{ url()->previous() }}"
          class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2
                 bg-gray-500 hover:bg-gray-600 text-white rounded shadow">
          <i data-feather="arrow-left" class="h-4 w-4"></i> Back
        </a>
        <button id="resetBtn" type="button"
          class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2
                 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Reset
        </button>
        <button type="submit" id="submitBtn"
          class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2
                 bg-green-700 hover:bg-green-800 text-white rounded shadow">
          <i data-feather="save" class="h-4 w-4"></i> Update
        </button>
      </div>
    </form>
  </div>
</div>


{{-- ===== DATA EXISTING DEFECTS (JSON untuk JS) ===== --}}
<script>
  // Data existing dari controller
  const existingDefects = @json($inspection->details ?? []);
  // Contoh struktur: [{defect_id, qty, ok_repair, note_defect, defect_category, defect_name}, ...]

  const inspectionPost  = @json($inspection->inspection_post ?? '');
  const savedTotalCheck = @json($inspection->total_check ?? 0);
</script>


<style>
  #itemTable th, #itemTable td {
    border: 1px solid #e5e7eb !important;
    padding: 8px 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    font-size: 0.875rem;
  }
  #itemTable tbody tr:nth-child(even) { background-color: #f9fafb !important; }
  #itemTable tbody tr:nth-child(odd)  { background-color: #ffffff !important; }
  #itemTable tbody tr:hover           { background-color: #e0f2fe !important; }
  #itemTable td, #itemTable th {
    border-left: none !important;
    border-right: none !important;
  }
  #itemTable {
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
  }
  .select2-container .select2-selection--single {
    height: 42px;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
  }
  .select2-container--default .select2-selection--single:focus,
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #60a5fa;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.4);
  }
  .defect-select { min-height: 1.8rem; line-height: 0.8rem; }
  select:disabled { background-color: #f3f4f6; color: #9ca3af; }
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
  const $passRate           = $('#passRate');
  const $passTroughLabel    = $('#passTroughLabel');
  const $passTroughDisplay  = $('#passTroughDisplay');

  let rowIndex = 0;
  let articleMap = {};


  /* =====================================================
   * HELPER
   * ===================================================== */
  function getSamplingCheck(qty) {
    if (qty >= 2 && qty <= 8)    return 2;
    if (qty <= 15)  return 3;
    if (qty <= 25)  return 5;
    if (qty <= 50)  return 8;
    if (qty <= 90)  return 13;
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
    if (method === '100%')      val = qty;
    if (method === 'Sampling')  val = getSamplingCheck(qty) || '';
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
      Swal.fire({ icon: 'warning', title: 'Validasi Gagal',
        text: 'Total Defect Qty melebihi Total Check. Nilai akan direset.' });
      $('input[name="qty[]"]').val(0);
      totalDefect = 0;
    }
    $('#totalDefectQty').text(totalDefect);
    const percent = totalCheck > 0 ? ((totalDefect / totalCheck) * 100).toFixed(0) : 0;
    $('#totalDefectPercent').text(percent);
  }


  /* =====================================================
   * TOTAL NG
   * ===================================================== */
  function updateTotalNG() {
    let totalNG = 0;
    const totalCheck = parseInt($totalCheck.val()) || 0;
    $('#defectTableBody tr').each(function () {
      const defectText = $(this).find('.defect-select option:selected').text().trim();
      const qty = parseInt($(this).find('.qty-defect').val()) || 0;
      if (defectText.startsWith('NG')) totalNG += qty;
    });
    $totalNGDisplay.text(totalNG);
    const percent = totalCheck > 0 ? ((totalNG / totalCheck) * 100).toFixed(0) : 0;
    $totalNGPercent.text(percent);
  }


  /* =====================================================
   * TOTAL OK
   * ===================================================== */
  function updateTotalOK() {
    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalNG    = parseInt($totalNGDisplay.text()) || 0;
    const totalOK    = Math.max(totalCheck - totalNG, 0);
    $totalOkDisplay.text(totalOK);
    const percent = totalCheck > 0 ? ((totalOK / totalCheck) * 100).toFixed(0) : 0;
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
    const percent = totalCheck > 0 ? ((totalValue / totalCheck) * 100).toFixed(0) : 0;
    $totalNCPercent.text(percent);
  }


  /* =====================================================
   * TOTAL PASS THROUGH
   * ===================================================== */
  function updateTotalPassThrough() {
    const post = $postSelect.val();
    if (post === 'Incoming') { $totalPTWrapper.addClass('hidden'); return; }
    $totalPTWrapper.removeClass('hidden');
    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalNG    = parseInt($totalNGDisplay.text()) || 0;
    const totalNC    = parseInt($totalNCDisplay.text()) || 0;
    const passThrough = Math.max(totalCheck - totalNG - totalNC, 0);
    $totalPTDisplay.text(passThrough);
  }


  /* =====================================================
   * PASS RATE
   * ===================================================== */
  function updatePassRate() {
    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalOK    = parseInt($totalOkDisplay.text()) || 0;
    const passRate   = totalCheck > 0 ? ((totalOK / totalCheck) * 100).toFixed(0) : 0;
    $passRate.text(`${passRate}%`);
  }


  /* =====================================================
   * PASS THROUGH / PERFORMANCE
   * ===================================================== */
  function updatePassTrough() {
    const post       = $postSelect.val();
    const totalCheck = parseInt($totalCheck.val()) || 0;
    let numerator = 0;
    if (totalCheck <= 0) { $passTroughDisplay.text('0%'); return; }
    if (post === 'Incoming') {
      $passTroughLabel.text('Performa');
      const totalOK       = parseInt($totalOkDisplay.text()) || 0;
      const totalOkRepair = parseInt($totalNCDisplay.text()) || 0;
      numerator = totalOK - totalOkRepair;
    } else {
      $passTroughLabel.text('Pass Through');
      numerator = parseInt($totalPTDisplay.text()) || 0;
    }
    if (numerator < 0) numerator = 0;
    const percent = ((numerator / totalCheck) * 100).toFixed(0);
    $passTroughDisplay.text(`${percent}%`);
  }


  /* =====================================================
   * RESET SUMMARY
   * ===================================================== */
  function resetSummary() {
    $totalNGDisplay.text(0);   $totalNGPercent.text(0);
    $totalOkDisplay.text(0);   $totalOkPercent.text(0);
    $totalNCDisplay.text(0);   $totalNCPercent.text(0);
    $totalPTDisplay.text(0);   $passTroughDisplay.text('0%');
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
   * TOGGLE OK REPAIR
   * ===================================================== */
  function toggleOkRepair() {
    const post = $('#inspection_post').val();
    if (post === 'Incoming') {
      $('.ok-repair-wrapper').removeClass('hidden')
        .find('input').prop('required', true).prop('disabled', false);
      $passTroughLabel.text('Performance');
    } else {
      $('.ok-repair-wrapper').addClass('hidden')
        .find('input').prop('required', false).prop('disabled', true).val('');
      if (post === 'Unloading') {
        $passTroughLabel.text('Pass Through');
      } else {
        $passTroughLabel.text('Performance');
      }
    }
  }


  /* =====================================================
   * VALIDASI QTY DEFECT
   * ===================================================== */
  function calculateTotalQty() {
    let total = 0;
    $('.qty-defect').each(function () { total += parseInt($(this).val()) || 0; });
    return total;
  }

  function validateTotalQty(changedInput = null) {
    const totalCheck = parseInt($('#total_check').val()) || 0;
    const totalQty   = calculateTotalQty();
    if (totalCheck === 0) return;
    if (totalQty > totalCheck) {
      if (changedInput) {
        const currentVal = parseInt($(changedInput).val()) || 0;
        const selisih    = totalQty - totalCheck;
        const corrected  = currentVal - selisih;
        $(changedInput).val(corrected > 0 ? corrected : 0);
      }
      Swal.fire({ icon: 'error', title: 'Qty Melebihi Total Check',
        text: 'Akumulasi Qty Defect tidak boleh lebih dari Total Check.',
        confirmButtonColor: '#2563eb' });
    }
  }


  /* =====================================================
   * CREATE ROW  ← sama persis dengan Create, +param prefill
   * ===================================================== */
  function createRow(index, defects = [], prefill = {}) {
    const $row = $('<tr>');

    let defectOptions = '<option value="">-- Choose Defect --</option>';
    defects.forEach(defect => {
      const selected = prefill.defect_id && parseInt(prefill.defect_id) === parseInt(defect.id)
        ? 'selected' : '';
      defectOptions += `
        <option value="${defect.id}"
          data-defect="${defect.defect}"
          ${selected}>
          ${defect.category} - ${defect.defect}
        </option>`;
    });

    // Jika prefill ada tapi defect tidak ada di list (misal defect beda post),
    // tambahkan sebagai option sementara agar nilai terbaca
    if (prefill.defect_id && defects.length === 0) {
      defectOptions += `
        <option value="${prefill.defect_id}" selected
          data-defect="${prefill.defect_name ?? ''}">
          ${prefill.defect_category ?? ''} - ${prefill.defect_name ?? ''}
        </option>`;
    }

    $row.html(`
      <td class="border p-2 text-center w-[20px]">${index}</td>
      <td class="border p-2 min-w-[140px]">
        <select name="defect_id[]" class="w-full border rounded defect-select">
          ${defectOptions}
        </select>
      </td>
      <td class="border p-2 w-[60px]">
        <div class="flex items-stretch">
          <input type="number" name="qty[]" min="1"
            value="${prefill.qty ?? ''}"
            class="flex-1 border border-gray-300 border-r-0 rounded-l-md px-3 py-2
                   text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-300
                   qty-defect text-right" required>
          <span class="inline-flex items-center px-3 text-sm text-gray-600 bg-gray-100
                       border border-gray-300 border-l-0 rounded-r-md">PCS</span>
        </div>
      </td>
      <td class="border p-2 w-[60px] ok-repair-wrapper">
        <div class="flex items-stretch">
          <input type="number" name="ok_repair[]"
            value="${prefill.ok_repair ?? ''}"
            class="flex-1 border border-gray-300 border-r-0 rounded-l-md px-3 py-2
                   text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-300
                   qty-ok-repair text-right">
          <span class="inline-flex items-center px-3 text-sm text-gray-600 bg-gray-100
                       border border-gray-300 border-l-0 rounded-r-md">PCS</span>
        </div>
      </td>
      <td class="border p-2 min-w-[120px]">
        <input type="text" name="note_defect[]"
          value="${prefill.note_defect ?? ''}"
          class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm
                 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
      </td>
      <td class="border p-2 text-center min-w-[60px]">
        <button type="button" class="removeBtn text-red-600 hover:text-red-800">X</button>
      </td>
    `);

    // Init Select2 pada row ini
    const $defectSelect = $row.find('.defect-select');
    $defectSelect.select2({ placeholder: '-- Choose Defect --', allowClear: true, width: '100%' });

    // Duplicate check
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
      if (isDuplicate) {
        Swal.fire({ icon: 'warning', title: 'Duplikasi Defect!',
          text: 'Defect yang sama sudah dipilih di baris lain.', confirmButtonText: 'OK' });
        $(this).val(null).trigger('select2:clear');
      }
    });

    // Validasi OK Repair <= Qty
    $row.find('.qty-ok-repair').on('input', function () {
      const qtyDefect   = parseInt($row.find('.qty-defect').val()) || 0;
      const qtyOkRepair = parseInt($(this).val()) || 0;
      if (qtyOkRepair > qtyDefect) {
        Swal.fire({ icon: 'error', title: 'Input tidak valid',
          text: 'Qty OK Repair tidak boleh melebihi Qty Defect di baris ini.',
          confirmButtonText: 'OK' });
        $(this).val(qtyDefect);
      }
    });

    toggleOkRepair();
    return $row;
  }


  /* =====================================================
   * INIT SELECT2 (header fields)
   * ===================================================== */
  $('#inspection_post').select2({ placeholder: "-- Pilih Inspection Post --", allowClear: true, width: '100%' });
  $('#supplier').select2({ placeholder: "-- Pilih Supplier --", allowClear: true, width: '100%' });
  $('#customer').select2({ placeholder: "-- Pilih Customer --", allowClear: true, width: '100%' });
  $('#check_method').select2({ placeholder: "-- Pilih Metode Inspection --", allowClear: true, width: '100%' });
  $('#spraybooth').select2({ placeholder: "-- Pilih Booth --", allowClear: true, width: '100%' });

  feather.replace();


  /* =====================================================
   * INSPECTION POST CHANGE (logic tampil/sembunyi field)
   * ===================================================== */
  function applyPostVisibility(post) {
    const supplierWrap   = $('#supplier-wrapper');
    const customerWrap   = $('#customer-wrapper');
    const qtyWrap        = $('#qty-received-wrapper');
    const checkMethod    = $('#check_method_container');
    const sprayboothWrap = $('#spraybooth-wrapper');

    supplierWrap.addClass('hidden');
    customerWrap.addClass('hidden');
    qtyWrap.addClass('hidden');
    checkMethod.addClass('hidden');
    sprayboothWrap.addClass('hidden');

    $('#supplier, #customer').prop('required', false);
    $('#qty_received').prop('required', false);
    $('#check_method').prop('required', false);
    $('#spraybooth').prop('required', false);

    if (post === 'Incoming') {
      supplierWrap.removeClass('hidden');
      $('#supplier').prop('required', true);
      qtyWrap.removeClass('hidden');
      $('#qty_received').prop('required', true);
      checkMethod.removeClass('hidden');
      $('#check_method').prop('required', true);
    } else if (post) {
      customerWrap.removeClass('hidden');
      $('#customer').prop('required', true);
      sprayboothWrap.removeClass('hidden');
      $('#spraybooth').prop('required', true);
    }
  }

  // Terapkan visibility berdasarkan nilai yang sudah tersimpan
  applyPostVisibility(inspectionPost);

  // Sinkronkan total check display
  syncTotalCheckDisplay();
  updateAllSummary();

  // Event change post
  $postSelect.on('change', function () {
    const post = $(this).val();
    applyPostVisibility(post);
    resetSummary();
    updateAllSummary();
    toggleOkRepair();

    // Reset Part & supplier/customer pilihan lama
    $('#part_name').val(null).trigger('change');
    if (post === 'Incoming') {
      $('#customer').val(null).trigger('change');
    } else {
      $('#supplier').val(null).trigger('change');
    }

    // Reload defects sesuai post
    if (!post) return;
    $.getJSON(`/qc/get-defects/${post}`, function (defects) {
      $('#defectTableBody').empty();
      rowIndex = 1;
      $('#defectTableBody').append(createRow(rowIndex, defects));
      toggleOkRepair();
      feather.replace();
    });
  });


  /* =====================================================
   * LOAD EXISTING DEFECTS (saat halaman pertama dibuka)
   * ===================================================== */
  if (inspectionPost) {
    $.getJSON(`/qc/get-defects/${inspectionPost}`, function (defects) {
      $('#defectTableBody').empty();
      rowIndex = 0;

      if (existingDefects && existingDefects.length > 0) {
        existingDefects.forEach(function (detail) {
          rowIndex++;
          const prefill = {
            defect_id:       detail.defect_id,
            defect_name:     detail.defect_name   ?? detail.defect?.defect   ?? '',
            defect_category: detail.defect_category ?? detail.defect?.category ?? '',
            qty:             detail.qty,
            ok_repair:       detail.ok_repair,
            note_defect:     detail.note_defect
          };
          $('#defectTableBody').append(createRow(rowIndex, defects, prefill));
        });
      } else {
        rowIndex = 1;
        $('#defectTableBody').append(createRow(rowIndex, defects));
      }

      toggleOkRepair();
      feather.replace();
      updateAllSummary();
    });
  } else {
    rowIndex = 1;
    $('#defectTableBody').append(createRow(rowIndex, []));
    toggleOkRepair();
  }


  /* =====================================================
   * ADD ROW
   * ===================================================== */
  $('#addRowBtn').on('click', function () {
    const post = $('#inspection_post').val();
    if (!post) return alert('Select inspection post first!');
    $.getJSON(`/qc/get-defects/${post}`, function (defects) {
      rowIndex++;
      $('#defectTableBody').append(createRow(rowIndex, defects));
      toggleOkRepair();
      feather.replace();
    });
  });


  /* =====================================================
   * REMOVE ROW
   * ===================================================== */
  $('#defectTableBody').on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();
    $('#defectTableBody tr').each(function (i) {
      $(this).find('td:first').text(i + 1);
    });
    rowIndex = $('#defectTableBody tr').length;
    toggleOkRepair();
    updateAllSummary();
  });

// Inisiasi Select2 AJAX dulu
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
          supplier: post === 'Incoming' ? $('#supplier').val() : $('#customer').val()
        };
      },
      processResults: data => {
        articleMap = {};
        data.forEach(item => { articleMap[item.article_code] = item; });
        return { results: data.map(item => ({ id: item.article_code, text: item.description })) };
      },
      cache: true
    }
  });

  // ✅ Pre-select dengan fetch data dulu ke endpoint baru
  @if(!empty($inspection->part_name))
  (function() {
    const articleCode = "{{ $inspection->part_name }}";  // ← kolom di tabel inspections
    const post        = "{{ $inspection->inspection_post }}";

    $.getJSON('/qc/get-article-by-code', { code: articleCode, post: post })
      .done(function(data) {
        if (!data) return;

        // Simpan ke articleMap agar handler onChange bisa baca
        articleMap[data.article_code] = data;

        // Append option & set sebagai selected
        const option = new Option(data.description, data.article_code, true, true);
        $('#part_name').append(option).trigger('change');
      });
  })();
  @endif

  $('#inspection_post, #supplier, #customer').on('change', function () {
    if ($(this).data('initialized')) {
      $('#part_name').val(null).trigger('change');
    }
    $(this).data('initialized', true);
  });

  $('#part_name').on('change', function () {
    const data = articleMap[$(this).val()];
    if (!data) return;
    $('[data-info="part-name"]').text(data.description || '-');
    $('[data-info="supplier"]').text(data.partner_name || '-');
    $('#supplier_code').val(data.partner_code || '');
  });

  /* =====================================================
   * EVENTS (total check, qty, dll)
   * ===================================================== */
  $checkMethod.on('change', updateTotalCheck);
  $qtyReceiving.on('input', updateTotalCheck);

  $totalCheck.on('input', function () {
    syncTotalCheckDisplay();
    updateAllSummary();
  });

  $(document).on('input change', '.qty-defect, .defect-select, .qty-ok-repair', updateAllSummary);
  $(document).on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();
    updateAllSummary();
  });

  $(document).on('input', '.qty-defect', function () { validateTotalQty(this); });
  $('#total_check').on('input', function () { validateTotalQty(); });


  /* =====================================================
   * RESET BUTTON
   * ===================================================== */
  $('#resetBtn').on('click', function () {
    Swal.fire({
      icon: 'question',
      title: 'Reset Form?',
      text: 'Data yang belum disimpan akan direset ke nilai awal.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Reset',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#374151'
    }).then(result => {
      if (result.isConfirmed) location.reload();
    });
  });


  /* =====================================================
   * SUBMIT → PUT ke /qc/inspections/{id}
   * ===================================================== */
  $('#inspectionForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $btn = $('#submitBtn');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true)
      .addClass('opacity-50 cursor-not-allowed')
      .html('<i class="fa fa-spinner fa-spin mr-1"></i> Updating...');

    const formData = new FormData(this);
    const post = $('#inspection_post').val();
    const supplierCode = (post === 'Incoming') ? $('#supplier').val() : $('#customer').val();
    formData.set('supplier_code', supplierCode);

    formData.set('total_check',    $('#totalCheckDisplay').text() || 0);
    formData.set('total_ok',       $('#totalOkDisplay').text()    || 0);
    formData.set('total_ng',       $('#totalNGDisplay').text()    || 0);
    formData.set('total_ok_repair', $('#totalNCDisplay').text()   || 0);

    // Override method → PUT
    formData.set('_method', 'PUT');

    formData.delete('defect_id[]');
    formData.delete('qty[]');
    formData.delete('ok_repair[]');
    formData.delete('note_defect[]');

    $('#defectTableBody tr').each(function (i, tr) {
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

    const inspectionId = $('input[name="inspection_id"]').val();

    $.ajax({
      url: `/qc/inspections/${inspectionId}`,
      method: 'POST',         // FormData pakai POST + _method=PUT
      data: formData,
      processData: false,
      contentType: false,

      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Updated',
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          window.location.href = res.redirect ?? `/qc/inspections/${inspectionId}`;
        });
      },

      error: function (xhr) {
        let msg = 'Something went wrong';
        if (xhr.status === 422 && xhr.responseJSON?.errors) {
          msg = Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join('<br>');
        }
        Swal.fire('Error', msg, 'error');
        $btn.prop('disabled', false)
          .removeClass('opacity-50 cursor-not-allowed')
          .html(originalHtml);
      }
    });
  });

});
</script>
@endpush
@endsection