@extends('layouts.app')

@section('title', 'Quality Inspection')
@section('page-title', 'QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Quality Inspection')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
  

  <!-- Main Panel -->
  <div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Quality Inspection</h2>
    <form id="inspection-form" class="space-y-4">

   <!-- Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="row-1">

  <!-- Inspection Post -->
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
    <select name="part_name" id="part_name" class="select2 w-full" required>
      <option value="">-- Select Part --</option>
    </select>
  </div>

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
    class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm"
  />
</div>
        <div class="w-full">
          <label class="block text-sm font-medium text-gray-700 mb-1">Total Check <span class="text-red-600">*</span></label>
          <input type="number" name="total_check" id="total_check" placeholder="Masukan Total Qty Part ..." class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
        </div>


</div>


     
      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400">
        <table id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-red-800 text-white">
            <tr>
             <th class="p-2 border text-center min-w-[40px]">No.</th>
        <th class="p-2 border min-w-[140px]">Defect</th>
        <th class="p-2 border min-w-[80px]">Qty</th>
        <th class="p-2 border min-w-[80px] ok-repair-wrapper">OK Repair</th>
        <th class="p-2 border min-w-[120px]">Note</th>
        <th class="p-2 border text-center min-w-[60px]">Action</th>
            </tr>
          </thead>
          <tbody id="defectTableBody"></tbody>
        </table>
        <button type="button" id="addRowBtn" class="mt-2 w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Row</button>
      </div>

      <!-- Buttons -->
      <div class="flex flex-col md:flex-row gap-2 mt-4">
        <button id="resetBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Reset
        </button>
        <button id="submitBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
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
  min-height: 2.4rem;
  line-height: 1rem;
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
  toggleOkRepair(); // 🔥 INI WAJIB
  const $checkMethod = $('#check_method');
  const $qtyReceiving = $('#qty_received');
  const $totalCheck = $('#total_check');
  const $totalCheckLabel = $('[data-info="total-check"]');
  const $totalNgLabel = $('[data-info="total-ng"]');
  const $totalOkLabel = $('[data-info="total-ok"]');
  const $totalOkRepairLabel = $('[data-info="total-ok-repair"]');
  const $passRate = $('[data-info="pass-rate"]');
  const $passTrough = $('[data-info="pass-trough"]');
  const $NGRate = $('[data-info="ng-rate"]');
  const $okRepairRate = $('[data-info="ok-repair-rate"]');
  let articleMap = {};
  let rowIndex = 1;

  

  // ================== Helper ==================
  function getSamplingCheck(qty) {
    if (qty >= 2 && qty <= 8) return 2;
    if (qty >= 9 && qty <= 15) return 3;
    if (qty >= 16 && qty <= 25) return 5;
    if (qty >= 26 && qty <= 50) return 8;
    if (qty >= 51 && qty <= 90) return 13;
    if (qty >= 91 && qty <= 150) return 20;
    if (qty >= 151 && qty <= 280) return 32;
    if (qty >= 281 && qty <= 500) return 50;
    if (qty >= 501 && qty <= 1200) return 80;
    if (qty >= 1201 && qty <= 3200) return 125;
    if (qty >= 3201 && qty <= 10000) return 200;
    if (qty >= 10001 && qty <= 35000) return 315;
    return 0;
  }

  function updateTotalCheck() {
    const method = $checkMethod.val();
    const qty = parseInt($qtyReceiving.val()) || 0;
    let val = '';

    if (method === '100%') val = qty;
    else if (method === 'Sampling') val = getSamplingCheck(qty) || '';

    $totalCheck.val(val).trigger('input');
  }

  function updateTotals() {
    let totalNg = 0, totalOkRepair = 0;

    $('input[name="qty[]"]').each(function () {
      totalNg += parseInt($(this).val()) || 0;
    });

    $('input[name="ok_repair[]"]').each(function () {
      totalOkRepair += parseInt($(this).val()) || 0;
    });

    const totalNgAfterRepair = Math.max(totalNg - totalOkRepair, 0);
    const totalCheck = parseInt($totalCheck.val()) || 0;
    const totalOk = Math.max(totalCheck - totalNg, 0);

    $totalCheckLabel.text(totalCheck || '-');
    $totalNgLabel.text(totalNgAfterRepair);
    $totalOkRepairLabel.text(totalOkRepair);
    $totalOkLabel.text(totalOk);

    if (totalNg > totalCheck) {
      Swal.fire('Peringatan', 'Jumlah defect melebihi total check!', 'warning');
    }

 // Hitung persentase
const passRate = totalCheck
  ? (((totalOk + totalOkRepair) / totalCheck) * 100).toFixed(0)
  : 0;

const passTrough = totalCheck
  ? ((totalOk / totalCheck) * 100).toFixed(0)
  : 0;

const ngRate = totalCheck
  ? (((totalNg - totalOkRepair) / totalCheck) * 100).toFixed(0)
  : 0;

const okRepairRate = totalCheck
  ? ((totalOkRepair / totalCheck) * 100).toFixed(0)
  : 0;


$passRate.text(passRate + '%');
$passTrough.text(passTrough + '%');
$NGRate.text(ngRate + '%');
$okRepairRate.text(okRepairRate + '%');

  }

  // ================== Event ==================
  $checkMethod.on('change', updateTotalCheck);
  $qtyReceiving.on('input', updateTotalCheck);
  $totalCheck.on('input', updateTotals);

  $(document).on('input', 'input[name="qty[]"], input[name="ok_repair[]"]', updateTotals);

  $(document).on('input', '.qty-ok-repair', function () {
    const qtyDefect = parseInt($(this).closest('tr').find('.qty-defect').val()) || 0;
    const qtyOkRepair = parseInt($(this).val()) || 0;
    if (qtyOkRepair > qtyDefect) {
      Swal.fire('Error', 'Qty OK Repair tidak boleh melebihi Qty Defect', 'error');
      $(this).val(qtyDefect);
    }
  });

  $(document).on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();
    updateTotals();
  });


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



  // ================== Initial ==================
  updateTotals();
  toggleOkRepair();
  feather.replace();
});





 let rowIndex = 1;

// Function buat row
function createRow(index, defects = []) {
    const $row = $('<tr>');

   let defectOptions = '<option value="">-- Choose Defect --</option>';
defects.forEach(defect => {
    defectOptions += `
      <option 
        value="${defect.id}" 
        data-defect="${defect.defect}">
        ${defect.category} - ${defect.defect}
      </option>`;
});


    $row.html(`
        <td class="border p-2 text-center min-w-[40px]">${index}</td>
        <td class="border p-2 min-w-[140px]">
            <select name="defect_id[]" class="w-full border rounded p-1 defect-select">
                ${defectOptions}
            </select>
        </td>
        <td class="border p-2 min-w-[80px]">
            <input type="number" name="qty[]" min="1" class="w-full border rounded p-1 qty-defect" required>
        </td>
     <td class="border p-2 min-w-[80px] ok-repair-wrapper">
    <input type="number"
           name="ok_repair[]"
           class="w-full border rounded p-1 qty-ok-repair">
</td>


        <td class="border p-2 min-w-[120px]">
            <input type="text" name="note_defect[]" class="w-full border rounded p-1">
        </td>
        <td class="border p-2 text-center min-w-[60px]">
            <button type="button" class="removeBtn text-red-600 hover:text-red-800"><i data-feather="trash-2"></i></button>
        </td>
    `);

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

// ================== Select2 Basic ==================
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
       console.log('📥 Response from backend →', data);
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


  const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // Format: YYYY-MM-DD
    document.getElementById("inspection-date").textContent = formattedDate;

   function getCurrentShift() {
    const now = new Date();
    const hour = now.getHours();

    if (hour >= 8 && hour < 17) {
      return 'Shift 1';
    }

    // Shift 2: 17.00 - 23.59 atau 00.00 - 02.59
    if (hour >= 17 || hour < 8) {
      return 'Shift 2';
    }

    return 'Unknown';
  }

  document.getElementById('shift-label').textContent = getCurrentShift();



$('#submitBtn').on('click', function (e) {
    e.preventDefault();

    const formData = new FormData();

    formData.append('_token', '{{ csrf_token() }}');
    formData.append('inspection_post', $('#inspection_post').val());
    formData.append('part_name', $('#part_name').val());
  const post = $('#inspection_post').val();

const supplierCode = (post === 'Incoming')
    ? $('#supplier').val()
    : $('#customer').val();

formData.append('supplier_code', supplierCode);

    formData.append('qty_received', $('#qty_received').val());
    formData.append('shift', $('#shift-label').text());
    formData.append('inspection_date', $('#inspection-date').text());
    formData.append('check_method', $('#check_method').val());
    formData.append('spraybooth', $('#spraybooth').val());
    formData.append('total_check', $('#total_check').val());
    formData.append('total_ok', $('[data-info="total-ok"]').text());
    formData.append('total_ok_repair', $('[data-info="total-ok-repair"]').text());
    formData.append('total_ng', $('[data-info="total-ng"]').text());
$('#defectTableBody tr').each(function () {
    const select = $(this).find('.defect-select');

    const defect = select.val(); // ✅ FIX
    const qty = $(this).find('input[name="qty[]"]').val();
    const ok_repair = $(this).find('input[name="ok_repair[]"]').val();
    const note = $(this).find('input[name="note_defect[]"]').val();

    if (!defect || !qty) return;

    formData.append('defect_id[]', defect);
    formData.append('qty[]', qty);
    formData.append('ok_repair[]', ok_repair || 0);
    formData.append('note_defect[]', note || '');
});



    console.log('Payload sending to server...');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: '/qc/inspections/store',
        method: 'POST',
        data: formData,
        processData: false, // ⬅️ penting agar FormData tidak diubah
        contentType: false, // ⬅️ penting agar boundary content dikirim otomatis
        success: function (res) {
            Swal.fire({
            title: 'Success',
            text: res.message,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            location.reload(); // ⬅️ Reload halaman setelah sukses
        });
    },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let msg = Object.values(errors).map(e => e.join(', ')).join('<br>');
                Swal.fire('Validation Error', msg, 'error');
            } else {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }
    });
});


    // saat load
    toggleOkRepair();

    
    

</script>
@endpush
@endsection
 
