@extends('layouts.app')

@section('title', 'Create Incoming Inspection')
@section('page-title', 'CREATE INCOMING')
@section('breadcrumb-item', 'Incoming Inspection')
@section('breadcrumb-active', 'Create Incoming Inspection')

@section('content')
<form id="incoming-form">
  <div class="flex gap-6">
    <!-- 📘 Sidebar Search Panel -->
    <div class="w-1/4 bg-white shadow-md rounded-xl p-4 space-y-4">
      <h2 class="text-lg font-semibold text-gray-700">Incoming Inspection</h2>

      {{-- Supplier Select2 --}}
      <div>
        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">
          Supplier<small class="text-red-600"> *</small>
        </label>
        <select name="supplier" id="supplier"
          class="supplier w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required>
          <option value="">-- Pilih Supplier --</option>
          @foreach ($suppliers as $supplier)
            <option value="{{ $supplier->code }}">{{ $supplier->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Part Name Select2 --}}
      <div>
        <label for="article_code" class="block text-sm font-medium text-gray-700 mb-1">
          Part Name<small class="text-red-600"> *</small>
        </label>
        <select name="article_code" id="article_code"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required>
          <option value="">-- Choose Part --</option>
          {{-- @foreach jika ada --}}
        </select>
      </div>

      {{-- Periode: Bulan dan Tahun --}}
      <div>
        <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">
          Periode<small class="text-red-600"> *</small>
        </label>
        <input type="month" name="periode" id="periode"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required />
      </div>
    </div>

    <!-- 📦 Main Transfer Panel -->
    <div class="w-3/4 bg-white shadow-md rounded-xl p-4 space-y-4">
      <h2 class="text-lg font-semibold text-gray-700">Incoming Data</h2>

      {{-- Tabel Total Inspection --}}
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Total Inspection</h2>
      <div class="overflow-x-auto">
        <table id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-blue-500 text-white">
            <tr id="headerRow">
              <th class="p-2 border">Item</th>
              <!-- Kolom tanggal akan diappend di sini -->
              <th class="p-2 border">Total</th>
            </tr>
          </thead>
          <tbody id="itemList">
            <!-- Baris data akan diappend via JS -->
          </tbody>
        </table>
      </div>

      {{-- Tabel Defect --}}
      <h2 class="text-lg font-semibold text-gray-700 mt-6 mb-4">Defect</h2>
      <div class="overflow-x-auto">
        <table id="defectTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-blue-500 text-white">
            <tr id="defectHeaderRow">
              <th class="p-2 border">Defect</th>
              <!-- Kolom tanggal dinamis -->
              <th class="p-2 border">Total</th>
            </tr>
          </thead>
          <tbody id="defectItemList">
            <!-- Baris defect dinamis di sini -->
          </tbody>
        </table>
      </div>

      {{-- Summary --}}
      <div class="flex justify-end mt-4">
        <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
          <div class="flex justify-between">
            <span class="font-semibold text-green-500">OK Percentage</span>
            <span class="text-green-500" id="summary-ok">0%</span>
          </div>
          <div class="flex justify-between">
            <span class="font-semibold text-yellow-500">OK Repair Percentage</span>
            <span class="text-yellow-500" id="summary-ok-repair">0%</span>
          </div>
            <div class="flex justify-between">
            <span class="font-semibold text-red-500">NG Percentage</span>
            <span class="text-red-500" id="summary-ng">0%</span>
          </div>
        </div>
      </div>

      {{-- Hidden inputs untuk inspection_ids --}}
      <div id="inspection-hidden-inputs">
        <!--
          Contoh isi akan dibuat via JS:
          <input type="hidden" name="inspection_ids[]" value="23" />
          <input type="hidden" name="inspection_ids[]" value="22" />
        -->
      </div>

      {{-- Tombol Submit dan Reset --}}
      <div class="flex justify-start gap-2 mt-4">
        <button type="button" id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Reset</button>
        <button type="submit" id="saveInspectionBtn" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
      </div>
    </div>
  </div>
</form>

  </div>
</div>


<style>
  /* ✅ Perbaiki Header Tabel */

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


   #defectTable th, #defectTable td {
    border: 1px solid #e5e7eb !important;  /* Tailwind gray-200 */
    padding: 8px 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    font-size: 0.875rem;  /* Tailwind text-sm */
  }

  /* ✅ Baris Genap & Ganjil */
  #defectTable tbody tr:nth-child(even) {
    background-color: #f9fafb !important;  /* Tailwind gray-50 */
  }
  #defectTable tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
  }

  /* ✅ Hover Warna */
  #defectTable tbody tr:hover {
    background-color: #e0f2fe !important;  /* Tailwind blue-100 */
  }

  /* ✅ Hilangkan border horizontal agar tampak lebih modern */
  #defectTable td, #defectTable th {
    border-left: none !important;
    border-right: none !important;
  }

  /* ✅ Pagar kiri-kanan (opsional) */
  #defectTable {
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
  }

  /* ✅ Perbaiki Search, Length, Info, Pagination */
  #defectTable .dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.875rem;
  }

  #defectTable .dataTables_length select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.875rem;
  }

  #defectTable .dataTables_info {
    font-size: 0.75rem;  /* Tailwind text-xs */
    color: #6b7280;      /* Tailwind gray-500 */
  }

  #defectTable .dataTables_paginate {
    font-size: 0.75rem;
  }
  /* ✅ Scroll wrapper */
  .datatable-container {
    overflow-x: auto;
  }

</style>
@push('scripts')
<script>
    $(document).ready(function () {
      const $periode = $('#periode');
      const $supplier = $('#supplier');
      const $article = $('#article_code');

      // Inisialisasi Select2
      $supplier.select2({ placeholder: "-- Select Supplier --", allowClear: true, width: '100%' });
      $article.select2({ placeholder: "-- Choose Part --", allowClear: true, width: '100%' });

      // Event: Saat supplier dipilih
      $supplier.on('change', function () {
          const supplierCode = $(this).val();
          $article.empty().append('<option value="">-- Choose Part --</option>');

          if (supplierCode) {
              $.getJSON(`/qc/api/articles/by-supplier/${supplierCode}`, function (data) {
                  $.each(data, function (_, item) {
                      $article.append(
                          `<option value="${item.article_code}">${item.article_code} - ${item.description}</option>`
                      );
                  });
                  $article.trigger('change'); // langsung trigger filter setelah artikel dimuat
              });
          } else {
              triggerFetchFromFilters();
          }
      });

      // Event: Saat periode atau artikel dipilih
      $periode.on('change', triggerFetchFromFilters);
      $article.on('change', triggerFetchFromFilters);

      // Load pertama kali
      triggerFetchFromFilters();

      function triggerFetchFromFilters() {
          const periode = $periode.val();
          const supplierCode = $supplier.val();
          const articleCode = $article.val();

          if (!periode || !supplierCode) {
              console.warn('❗ Filter belum lengkap');
              renderInspectionTable([]); // kosongkan tabel
              return;
          }

          const [year, month] = periode.split('-');
          fetchInspectionData(supplierCode, year, month, articleCode);
      }

      function fetchInspectionData(supplierCode, year, month, articleCode) {
          const paddedMonth = String(month).padStart(2, '0');
          const url = `/qc/api/inspections/filter?supplier_code=${encodeURIComponent(supplierCode)}&year=${year}&month=${paddedMonth}&article_code=${articleCode || ''}`;

          console.log('Fetching:', url);

          $.getJSON(url, function (data) {
              console.log("Fetched inspection data:", data);

              if (!data.length) {
                  console.warn("🚫 Tidak ada data inspeksi untuk filter ini!");
                  renderInspectionTable([]);
                  renderDefectTable([]);
                  return;
              }

              // Simpan data di map untuk kebutuhan lain
              inspectionDataMap = {};
              $.each(data, function (_, item) {
                  inspectionDataMap[String(item.id)] = item;
              });
              updateInspectionHiddenInputs(data);
              renderInspectionTable(data);
              renderDefectTable(data);
          }).fail(function (err) {
              console.error("❌ Gagal mengambil data inspeksi:", err);
          });
      }
  });


  $(document).ready(function() {
   function updateInspectionHiddenInputs(data) {
  const container = $('#inspection-hidden-inputs');
  container.empty();

  data.forEach(item => {
    container.append(
      $('<input>').attr({
        type: 'hidden',
        name: 'inspection_ids[]',
        value: item.id
      })
    );
  });
}

// Ambil semua inspection ids dari input hidden
function getInspectionIds() {
  return $('#incoming-form input[name="inspection_ids[]"]').map(function() {
    return this.value;
  }).get();
}

  $(document).ready(function () {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });

    $('#incoming-form').on('submit', function (e) {
    e.preventDefault();

    const $submitBtn = $('#saveInspectionBtn');

    if ($submitBtn.prop('disabled')) {
      // Jika tombol sudah disable, abaikan submit berikutnya
      return;
    }

    // Matikan tombol submit (off)
    $submitBtn.prop('disabled', true);

    const inspectionIds = getInspectionIds();

    console.log('Submit intercepted, inspectionIds:', inspectionIds);

    $.ajax({
      url: '{{ url("qc/incoming/store") }}',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        supplier_code: $('#supplier').val(),
        periode: $('#periode').val(),
        article_code: $('#article_code').val(),
        inspection_ids: inspectionIds
      }),
      success: function (res) {
        Swal.fire({
          toast: true,
          icon: 'success',
          title: 'Data berhasil disimpan!',
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
          didClose: () => {
            window.location.href = '{{ route("qc.incoming.index") }}'; // Redirect ke halaman incoming.index
          }
        });
      },
      error: function (xhr) {
        let errorMsg = 'Gagal menyimpan data';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        Swal.fire({
          toast: true,
          icon: 'error',
          title: errorMsg,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
        $submitBtn.prop('disabled', false); // Enable tombol kembali
      }
    });
  });

  });

          $('.supplier').select2({
              placeholder: "-- Select Supplier --",
              allowClear: true,
              width: '100%'
          });
      });

      function updateInspectionHiddenInputs(data) {
  const container = $('#inspection-hidden-inputs');
  container.empty();

  data.forEach(item => {
    container.append(
      $('<input>').attr({
        type: 'hidden',
        name: 'inspection_ids[]',
        value: item.id
      })
    );
  });
}
      

    function renderInspectionTable(data) {
      const dates = [...new Set(data.map(item => {
          const d = new Date(item.inspection_date);
          return d.getDate(); 
      }))].sort((a, b) => a - b);

      const itemLabels = [
          'Total Received',
          'Total Check',
          'Total OK',
          'Total NG',
          'Total OK Repair',
          'Pass Rate (%)',
          'Performa (%)',
          '100% (A)/Sampling (S)'
      ];

      const grouped = {};
      dates.forEach(date => {
          grouped[date] = data.filter(item => {
              const d = new Date(item.inspection_date);
              return d.getDate() === date;
          });
      });

      const rows = {};
      itemLabels.forEach(label => {
          rows[label] = dates.map(date => {
              const items = grouped[date] || [];

              switch (label) {
                 case 'Total Received':
                      return items.reduce((sum, i) => sum + (i.qty_received || 0), 0);
                  case 'Total Check':
                      return items.reduce((sum, i) => sum + (i.total_check || 0), 0);
                  case 'Total OK':
                      return items.reduce((sum, i) => sum + (i.total_ok || 0), 0);
                  case 'Total NG':
                      return items.reduce((sum, i) => sum + (i.total_ng || 0), 0);
                  case 'Total OK Repair':
                      return items.reduce((sum, i) => sum + (i.total_ok_repair || 0), 0);
                  case 'Pass Rate (%)':
                      const ok = items.reduce((sum, i) => sum + (i.total_ok || 0), 0);
                      const check = items.reduce((sum, i) => sum + (i.total_check || 0), 0);
                      return check > 0 ? Math.round((ok / check) * 100) + '%' : '0%';
                  case 'Performa (%)':
                      const totalOk = items.reduce((sum, i) => sum + (i.total_ok || 0), 0);
                      const totalOkRepair = items.reduce((sum, i) => sum + (i.total_ok_repair || 0), 0);
                      const totalCheck = items.reduce((sum, i) => sum + (i.total_check || 0), 0);
                      return totalCheck > 0 ? Math.round(((totalOk - totalOkRepair) / totalCheck) * 100) + '%' : '-';
                  default:
                      return '-';
              }
          });
      });

      // Render header
      const headerRow = document.getElementById('headerRow');
      headerRow.innerHTML = `<th class="p-2 border text-left">Item</th>`;
      dates.forEach(date => {
          headerRow.innerHTML += `<th class="p-2 border">${date}</th>`;
      });
      headerRow.innerHTML += `<th class="p-2 border">Total</th>`;

      // Render body + hitung total summary
      const tbody = document.getElementById('itemList');
      tbody.innerHTML = '';

      let totalCheck = 0;
      let totalOk = 0;
      let totalNg = 0;
      let totalOkRepair = 0;

      itemLabels.forEach(label => {
          const values = rows[label];
          let rowHTML = `<tr><th class="p-2 border text-left">${label}</th>`;
          values.forEach(val => {
              rowHTML += `<td class="p-2 border text-center">${val}</td>`;
          });

          let total = 0;
          if (label.includes('Performa') || label.includes('Pass Rate')) {
              const numericValues = values
                  .map(v => parseFloat(v.toString().replace('%', '')))
                  .filter(v => !isNaN(v));
              total = numericValues.reduce((sum, v) => sum + v, 0);
              const avg = numericValues.length ? Math.round(total / numericValues.length) + '%' : '-';
              rowHTML += `<td class="p-2 border text-center">${avg}</td>`;
          } else if (label === '100% (A)/Sampling (S)') {
              rowHTML += `<td class="p-2 border text-center">-</td>`;
          } else {
              total = values.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
              rowHTML += `<td class="p-2 border text-center">${Math.round(total)}</td>`;

             if (label.includes('Total OK Repair')) totalOkRepair += total;
if (label.includes('Total OK')) totalOk += total - totalOkRepair;
if (label.includes('Total NG')) totalNg += total;
if (label.includes('Total Check')) totalCheck += total;

          }

          rowHTML += `</tr>`;
          tbody.innerHTML += rowHTML;
      });

      // Render ringkasan persentase
      const formatPercent = (value) => {
          return totalCheck ? `${Math.round((value / totalCheck) * 100)}%` : '0%';
      };

      document.getElementById('summary-ok').textContent = formatPercent(totalOk);
      document.getElementById('summary-ng').textContent = formatPercent(totalNg);
      document.getElementById('summary-ok-repair').textContent = formatPercent(totalOkRepair);
  }


function renderDefectTable(data) {
    // 1. Ambil tanggal (hari) unik
    const dates = [...new Set(data.map(item => {
        const d = new Date(item.inspection_date);
        return d.getDate();
    }))].sort((a, b) => a - b);

    // 2. Kumpulkan semua nama defect unik
    const defectMap = {}; // { "Blister": { 1: 2, 5: 4 } }
    data.forEach(item => {
        const day = new Date(item.inspection_date).getDate();

        item.inspection_defects?.forEach(def => {
            const name = def.defect?.defect || 'Unknown';

            if (!defectMap[name]) defectMap[name] = {};
            if (!defectMap[name][day]) defectMap[name][day] = 0;

            defectMap[name][day] += def.qty;
        });
    });

    // 3. Render header
    const headerRow = document.getElementById('defectHeaderRow');
    headerRow.innerHTML = `<th class="p-2 border text-left">Defect</th>`;
    dates.forEach(day => {
        headerRow.innerHTML += `<th class="p-2 border">${day}</th>`;
    });
    headerRow.innerHTML += `<th class="p-2 border">Total</th>`;

    // 4. Render body
    const tbody = document.getElementById('defectItemList');
    tbody.innerHTML = '';

    Object.entries(defectMap).forEach(([defectName, daily]) => {
        let rowHTML = `<tr><th class="p-2 border text-left">${defectName}</th>`;
        let total = 0;
        dates.forEach(day => {
            const qty = daily[day] || 0;
            total += qty;
            rowHTML += `<td class="p-2 border text-center">${qty}</td>`;
        });
        rowHTML += `<td class="p-2 border text-center">${total}</td></tr>`;
        tbody.innerHTML += rowHTML;
    });
}
    periodeInput.addEventListener('change', handleFilterChange);
    supplierSelect.addEventListener('change', handleFilterChange);
    articleSelect.addEventListener('change', handleFilterChange);

// Contoh: tombol simpan
// Set default CSRF token untuk semua request AJAX


</script>
@endpush
@endsection

