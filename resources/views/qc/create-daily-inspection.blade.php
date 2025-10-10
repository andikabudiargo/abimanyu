@extends('layouts.app-op-qc')

@section('title', 'Quality Inspection')
@section('page-title', 'QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Quality Inspection')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
  <!-- Sidebar -->
  <div class="w-full md:w-1/4 bg-white shadow-lg rounded-2xl p-4 md:p-6 space-y-6">
    <div>
      <h2 class="text-lg font-semibold text-gray-700">Inspection Overview</h2>
    </div>

    <!-- Operator Info -->
    <div class="space-y-2">
      <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Operator Information</h3>
      <div class="flex justify-between text-sm text-gray-600">
        <span class="font-medium text-gray-500">Operator Name:</span>
        <span class="text-gray-800">{{ Auth::user()->name }}</span>
      </div>
      <div class="flex justify-between text-sm text-gray-600">
        <span class="font-medium text-gray-500">Shift:</span>
        <span id="shift-label" class="text-gray-800">-</span>
      </div>
      <div class="flex justify-between text-sm text-gray-600">
        <span class="font-medium text-gray-500">Inspection Date:</span>
        <span id="inspection-date" class="text-gray-800">-</span>
      </div>
    </div>

    <!-- Part Info -->
    <div class="space-y-2">
      <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Part Information</h3>
      <div class="flex justify-between items-start text-sm gap-2 text-gray-600">
        <span class="font-medium text-gray-500 whitespace-nowrap">Part Name:</span>
        <span data-info="part-name" class="text-gray-800 text-right max-w-[65%] break-words">-</span>
      </div>
      <div class="flex justify-between items-start text-sm gap-2 text-gray-600">
        <span class="font-medium text-gray-500 whitespace-nowrap">Supplier:</span>
        <span data-info="supplier" class="text-gray-800 text-right max-w-[65%] break-words">-</span>
      </div>
      <input type="hidden" id="supplier_code" name="supplier_code">
    </div>

    <!-- Summary & Percentage Inspection -->
    <div class="space-y-3">
      <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Summary Inspection</h3>
      <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-2 text-sm">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-gray-500">
            <i data-feather="search" class="w-4 h-4"></i>
            <span class="font-medium">Total Check</span>
          </div>
          <span data-info="total-check" class="text-gray-800 font-semibold">0</span>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-green-600">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            <span class="font-medium">Total OK</span>
          </div>
          <span data-info="total-ok" class="text-green-600 font-semibold">-</span>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-yellow-500">
            <i data-feather="tool" class="w-4 h-4"></i>
            <span class="font-medium">Total OK Repair</span>
          </div>
          <span data-info="total-ok-repair" class="text-yellow-500 font-semibold">0</span>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-red-600">
            <i data-feather="x-circle" class="w-4 h-4"></i>
            <span class="font-medium">Total NG</span>
          </div>
          <span data-info="total-ng" class="text-red-600 font-semibold">-</span>
        </div>
      </div>

      <h3 class="text-md font-semibold text-gray-700 border-b pb-1 mt-4">Percentage Inspection</h3>
      <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-2 text-sm">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-green-500">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            <span class="font-medium">Pass Rate</span>
          </div>
          <span data-info="pass-rate" class="text-green-500 font-semibold">0</span>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-yellow-500">
            <i data-feather="tool" class="w-4 h-4"></i>
            <span class="font-medium">OK Repair</span>
          </div>
          <span data-info="ok-repair-rate" class="text-yellow-500 font-semibold">-</span>
        </div>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2 text-red-600">
            <i data-feather="x-circle" class="w-4 h-4"></i>
            <span class="font-medium">NG Rate</span>
          </div>
          <span data-info="ng-rate" class="text-red-600 font-semibold">-</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Panel -->
  <div class="w-full md:w-3/4 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Quality Inspection</h2>
    <form id="inspection-form" class="space-y-4">

     <!-- Row 1 -->
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Post <span class="text-red-600">*</span></label>
    <select name="inspection_post" id="inspection_post" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
      <option value="">-- Choose Post --</option>
      <option value="Incoming">Incoming</option>
      <option value="Unloading">Unloading</option>
      <option value="Buffing">Buffing</option>
      <option value="Touch Up">Touch Up</option>
      <option value="Final">Final</option>
    </select>
  </div>

  <div class="w-full md:w-1/2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-600">*</span></label>
    <select name="supplier" id="supplier" class="select2 w-full" required>
      <option value="">-- Pilih Supplier --</option>
      @foreach ($suppliers as $supplier)
        <option value="{{ $supplier->code }}">{{ $supplier->name }}</option>
      @endforeach
    </select>
  </div>
</div>

<!-- Row 2 -->
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Part Name <span class="text-red-600">*</span></label>
    <select name="part_name" id="part_name" class="select2 w-full" required>
      <option value="">-- Select Part --</option>
    </select>
  </div>

  <div class="w-full md:w-1/2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Qty Received <span class="text-red-600">*</span></label>
    <input type="number" name="qty_received" id="qty_received" placeholder="Masukan Qty Total Kedatangan Barang ..." class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required/>
  </div>
</div>


      <!-- Note -->
      <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <textarea id="note" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>

      <!-- Check Method & Total Check -->
      <div class="flex flex-col md:flex-row gap-4">
        <div id="check_method_container" class="w-full md:w-1/2 hidden">
          <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Method <span class="text-red-600">*</span></label>
          <select name="check_method" id="check_method" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Method --</option>
            <option value="100%">100% (A)</option>
            <option value="Sampling">Sampling (S)</option>
          </select>
        </div>
        <div class="w-full md:w-1/2">
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
        <th class="p-2 border min-w-[80px]">OK Repair</th>
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
  const $checkMethod = $('#check_method');
  const $qtyReceiving = $('#qty_received');
  const $totalCheck = $('#total_check');
  const $totalCheckLabel = $('[data-info="total-check"]');
  const $totalNgLabel = $('[data-info="total-ng"]');
  const $totalOkLabel = $('[data-info="total-ok"]');
  const $totalOkRepairLabel = $('[data-info="total-ok-repair"]');
  const $passRate = $('[data-info="pass-rate"]');
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
const passRate = totalCheck ? ((totalOk / totalCheck) * 100).toFixed(0) : 0;
const ngRate = totalCheck ? (((totalNg - totalOkRepair) / totalCheck) * 100).toFixed(0) : 0;
const okRepairRate = totalCheck ? ((totalOkRepair / totalCheck) * 100).toFixed(0) : 0;

$passRate.text(passRate + '%');
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

  $(document).on('change', '.defect-select', function () {
    const selected = [];
    $('.defect-select').each(function () {
      const val = $(this).val();
      if (val) selected.push(val);
    });

    const hasDuplicate = new Set(selected).size !== selected.length;
    if (hasDuplicate) {
      Swal.fire('Peringatan', 'Defect duplikat tidak diizinkan', 'warning');
      $(this).val('').trigger('change');
    }
  });

  $(document).on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();
    updateTotals();
  });

  // ================== Select2 ==================
   $('#inspection_post').select2({ placeholder: "-- Pilih Inspection Post --", allowClear: true, width: '100%' });
  $('#supplier').select2({ placeholder: "-- Pilih Supplier --", allowClear: true, width: '100%' });

  $('#part_name').select2({
    placeholder: "-- Select Part --",
    allowClear: true,
    width: '100%',
    ajax: {
      url: '/qc/get-articles',
      dataType: 'json',
      data: params => ({ term: params.term, post: $('#inspection_post').val(), supplier: $('#supplier').val() }),
      processResults: data => {
        articleMap = {};
        data.forEach(item => { articleMap[item.article_code] = item; });
        return { results: data.map(item => ({ id: item.article_code, text: item.description })) };
      }
    }
  });

  $('#part_name').on('change', function () {
    const data = articleMap[$(this).val()];
    if (data) {
      $('[data-info="part-name"]').text(data.description || '-');
      $('[data-info="supplier"]').text(data.supplier?.name || '-');
      $('#supplier_code').val(data.supplier?.code || '');
    }
  });

  // ================== Initial ==================
  updateTotals();
  feather.replace();
});





 let rowIndex = 1;

// Function buat row
function createRow(index, defects = []) {
    const $row = $('<tr>');

    let defectOptions = '<option value="">-- Choose Defect --</option>';
    defects.forEach(defect => {
        defectOptions += `<option value="${defect.id}">${defect.defect}</option>`;
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
        <td class="border p-2 min-w-[80px]">
            <input type="number" name="ok_repair[]" class="w-full border rounded p-1 qty-ok-repair" required>
        </td>
        <td class="border p-2 min-w-[120px]">
            <input type="text" name="note_defect[]" class="w-full border rounded p-1">
        </td>
        <td class="border p-2 text-center min-w-[60px]">
            <button type="button" class="removeBtn text-red-600 hover:text-red-800"><i data-feather="trash-2"></i></button>
        </td>
    `);

    // Init Select2
    $row.find('.defect-select').select2({
        placeholder: '-- Choose Defect --',
        allowClear: true,
        width: '100%'
    });

    // Validasi duplikat defect
    $row.find('.defect-select').on('change', function () {
        const selectedValue = $(this).val();
        let isDuplicate = false;

        $('.defect-select').not(this).each(function () {
            if ($(this).val() === selectedValue && selectedValue !== '') {
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
            $(this).val('').trigger('change');
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

    return $row;
}

// Tambahkan default row saat page load
$(document).ready(function () {
    const post = $('#inspection_post').val();
    if (post) {
        $.getJSON(`/qc/get-defects/${post}`, function(defects) {
            $('#defectTableBody').append(createRow(rowIndex, defects));
            feather.replace();
        });
    } else {
        $('#defectTableBody').append(createRow(rowIndex, []));
        feather.replace();
    }
});

// Update row ketika inspection_post berubah
$('#inspection_post').on('change', function () {
    const post = $(this).val();
    if (!post) return;

    $.getJSON(`/qc/get-defects/${post}`, function(defects) {
        $('#defectTableBody').empty();
        rowIndex = 1;
        $('#defectTableBody').append(createRow(rowIndex, defects));
        feather.replace();
    });
});

// Tombol Add Row
$('#addRowBtn').on('click', function () {
    const post = $('#inspection_post').val();
    if (!post) return alert('Select inspection post first!');

    $.getJSON(`/qc/get-defects/${post}`, function(defects) {
        rowIndex++;
        $('#defectTableBody').append(createRow(rowIndex, defects));
        feather.replace();
    });
});

// Tombol Remove Row & update nomor
$('#defectTableBody').on('click', '.removeBtn', function () {
    $(this).closest('tr').remove();
    $('#defectTableBody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
    rowIndex = $('#defectTableBody tr').length;
});


$('#supplier').select2({
  placeholder: "-- Pilih Supplier --",
  allowClear: true,
  width: 'resolve'
});

let articleMap = {}; // Global article map

$('#part_name').select2({
  placeholder: "-- Select Part --",
  allowClear: true,
  width: 'resolve',
  ajax: {
    url: function () {
      return `/qc/get-articles`;
    },
    dataType: 'json',
    data: function (params) {
      return {
        term: params.term, // keyword pencarian
        post: $('#inspection_post').val(),
        supplier: $('#supplier').val() // ambil kode supplier
      };
    },
    processResults: function (data) {
      articleMap = {};
      data.forEach(item => {
        articleMap[item.article_code] = item;
      });

      return {
        results: data.map(item => ({
          id: item.article_code,
          text: `${item.description}`
        }))
      };
    },
    cache: true
  }
});

// Kosongkan Select2 saat inspection_post berubah
 $('#supplier, #inspection_post').on('change', function () {
  $('#part_name').val(null).trigger('change'); // reset Select2 part
});


$('#part_name').on('change', function () {
  const code = $(this).val();
  const data = articleMap[code];

  if (!data) return;

  $('[data-info="part-name"]').text(data.description ?? '-');

  if (data && data.supplier) {
    $('[data-info="supplier"]').text(data.supplier.name); // tampilkan nama
    $('#supplier_code').val(data.supplier.code); // simpan code
}

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

   // Gunakan event 'change.select2' khusus
$('#inspection_post').on('change', function() {
    const typeValue = $(this).val();
    const methodField = document.getElementById('check_method_container');

    if (typeValue === 'Incoming') {
        methodField.classList.remove('hidden');
    } else {
        methodField.classList.add('hidden');
    }
});
  function attachDefectValidation() {
  const selects = document.querySelectorAll('.defect-select');

  selects.forEach(select => {
    select.removeEventListener('change', handleDefectChange); // Prevent multiple bindings
    select.addEventListener('change', handleDefectChange);
  });
}

function handleDefectChange() {
  const selectedValues = Array.from(document.querySelectorAll('.defect-select'))
    .map(s => s.value)
    .filter(v => v); // Remove empty

  const duplicates = selectedValues.filter((v, i, self) => self.indexOf(v) !== i);

  if (duplicates.length > 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Duplicate Defect',
      text: 'Tidak bisa pilih defect yang sama dua kali.',
    });

    this.value = ''; // Reset the current select
  }
}

$('#submitBtn').on('click', function (e) {
    e.preventDefault();

    const formData = new FormData();

    formData.append('_token', '{{ csrf_token() }}');
    formData.append('inspection_post', $('#inspection_post').val());
    formData.append('part_name', $('#part_name').val());
    formData.append('supplier_code', $('#supplier_code').val());
    formData.append('qty_received', $('#qty_received').val());
    formData.append('shift', $('#shift-label').text());
    formData.append('inspection_date', $('#inspection-date').text());
    formData.append('note', $('#note').val());
    formData.append('check_method', $('#check_method').val());
    formData.append('total_check', $('#total_check').val());
    formData.append('total_ok', $('[data-info="total-ok"]').text());
    formData.append('total_ok_repair', $('[data-info="total-ok-repair"]').text());
    formData.append('total_ng', $('[data-info="total-ng"]').text());

    $('#defectTableBody tr').each(function (index) {
        const defect = $(this).find('select[name="defect_id[]"]').val();
        const qty = $(this).find('input[name="qty[]"]').val();
        const ok_repair = $(this).find('input[name="ok_repair[]"]').val();
        const note = $(this).find('input[name="note_defect[]"]').val();

        if (defect && qty) {
            formData.append(`defect_id[${index}]`, defect);
            formData.append(`qty[${index}]`, qty);
            formData.append(`ok_repair[${index}]`, ok_repair);
            formData.append(`note_defect[${index}]`, note ?? '');
        }
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



</script>
@endpush
@endsection

