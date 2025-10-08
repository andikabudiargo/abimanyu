@extends('layouts.app')

@section('title', 'Create Transfer In')
@section('page-title', 'CREATE TRANSFER IN')
@section('breadcrumb-item', 'Transfer In')
@section('breadcrumb-active', 'Create Transfer In')

@section('content')
<div class="flex flex-col md:flex-row gap-2">
  <!-- 📘 Sidebar Search Panel -->
  <div class="w-full md:w-1/4 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Search Article</h2>

   <div>
  <label for="selectArticle" class="block text-sm font-medium text-gray-700 mb-1">
    Article / Transfer Number
  </label>
  <select id="selectArticle" class="w-full border border-gray-300 rounded px-3 py-2 shadow-sm">
    <!-- Options akan di-load via AJAX -->
  </select>
</div>

<div id="selectResult" class="space-y-2 text-sm text-gray-600 mt-2">
  <p class="text-gray-400 italic">Belum ada pilihan...</p>
</div>

    <!-- Loader Spinner -->
    <div id="qrLoader" class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-2 hidden"></div>

    <div class="p-4 flex flex-col gap-4 w-full max-w-xl mt-4">
    <!-- QR Image -->
    <div class="rounded p-2 flex justify-center items-center">
        <img id="qrPreview" src="{{ asset('img/tf-in.png') }}" alt="QR Code" class="w-32 h-32 object-contain" />
    </div>
</div>

  </div>

  <!-- 📦 Main Transfer Panel -->
  <div class="w-full md:w-3/4 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Transfer In</h2>
    <form id="transfer-form">
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="relative group">
          <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number<small class="text-red-600"> *</small></label>
          <input type="text" name="reference_number" id="reference_number"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tuliskan Nomor SJ atau yang setara" required />
          <!-- Tooltip -->
          <div id="tooltip-reference"
            class="absolute z-10 hidden group-focus-within:block group-hover:block top-full left-0 mt-1 w-max bg-gray-800 text-white text-xs rounded py-1 px-2 shadow-md">
            Tulis nomor surat jalan dari supplier yang tertera pada surat jalan
          </div>
        </div>
        <div>
          <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Received Date<small class="text-red-600"> *</small></label>
          <input type="date" name="date" id="date" value="<?= date('Y-m-d') ?>"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
      </div>


      <!-- 📦 Lokasi -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="transfer_type" class="block text-sm font-medium text-gray-700 mb-1">Transfer Type <small class="text-red-600"> *</small></label>
          <select name="transfer_type" id="transfer_type"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Type --</option>
            <option value="Incoming">Incoming</option> <!-- Dari Supplier -->
            <option value="Material Return">Material Return</option>
            <option value="Temporary Saved">Temporary Saved</option> <!-- Dari Produksi -->
          </select>
        </div>
        <!-- 🏢 Supplier (untuk incoming) -->
        <div id="supplierWrapper">
          <label for="supplier_code" class="block text-sm font-medium text-gray-700 mb-1">Supplier <small class="text-red-600"> *</small></label>
          <input type="text" id="supplier_name" name="supplier_name"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm bg-gray-100 text-gray-700"
            readonly />
          <input type="hidden" id="supplier_code" name="supplier_code">
          <div id="tooltip-supplier"
            class="absolute z-10 hidden group-focus-within:block group-hover:block top-full left-0 mt-1 w-max bg-gray-800 text-white text-xs rounded py-1 px-2 shadow-md">
            Supplier dikunci otomatis berdasarkan artikel pertama yang discan, klik reset jika terjadi kesalahan
          </div>
        </div>

        <div id="fromLocationWrapper">
          <label for="from_location" class="block text-sm font-medium text-gray-700 mb-1">Location From <small class="text-red-600"> *</small></label>
          <select name="from_location" id="from_location"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Location From --</option>
          </select>
        </div>
      </div>

      <!-- 🔍 Input Barcode -->
      <div class="mb-4">
        <label for="barcodeInput" class="block text-sm font-medium text-gray-700">Input Manual</label>
        <input type="text" id="barcodeInput" placeholder="Pilih artikel untuk diinput"
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-base">
        <small class="text-gray-600">*Input manual apabila scanner bermasalah</small>
      </div>

      <!-- 📝 Catatan Tambahan -->
      <div class="mb-12">
        <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
        <textarea id="note" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>

      <!-- 📋 Tabel Artikel yang Dipindahkan -->
      <div class="overflow-x-auto">
        <table id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="p-2 border">No.</th>
              <th class="p-2 border">Article Code</th>
              <th class="p-2 border">Description</th>
              <th class="p-2 border material-return-col hidden">Qty Out</th>
              <th class="p-2 border">Qty</th>
              <th class="p-2 border">UOM</th>
              <th class="p-2 border">Min Package</th>
              <th class="p-2 border">Expired Date</th>
              <th class="p-2 border">Destination</th>
              <th class="p-2 border">Action</th>
            </tr>
          </thead>
          <tbody id="itemList">
            <td colspan="10" class="text-center">Data Not Found</td>
          </tbody>
        </table>
      </div>

      <hr class="mt-6">
      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start space-x-2 mt-4">
        <button id="resetBtn"
          class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Reset
        </button>

        <button id="submitBtn"
          class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded shadow">
          <i data-feather="save" class="h-4 w-4"></i>
          Save
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Pilihan Print -->
<div id="printButtonWrapper" class="mt-6 hidden">
  <button onclick="autoPrintLabel()" class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 9v12h12V9m-4-4H10m0 0V5a2 2 0 012-2h0a2 2 0 012 2v0m-4 0h4" />
    </svg>
    <span>Cetak Label</span>
  </button>
</div>





<style>
  /* ✅ Perbaiki Header Tabel */
  #itemTable thead th {
    background-color: #1e3a8a !important;
    /* Tailwind blue-800 */
    color: #ffffff !important;
    /* Putih */
    font-weight: 600 !important;
    /* Bold */
    padding: 10px !important;
    border: none !important;
  }

  /* ✅ Perbaiki Border dan Padding Kolom */
  #itemTable th,
  #itemTable td {
    border: 1px solid #e5e7eb !important;
    /* Tailwind gray-200 */
    padding: 8px 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    font-size: 0.875rem;
    /* Tailwind text-sm */
  }

  /* ✅ Baris Genap & Ganjil */
  #itemTable tbody tr:nth-child(even) {
    background-color: #f9fafb !important;
    /* Tailwind gray-50 */
  }

  #itemTable tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
  }

  /* ✅ Hover Warna */
  #itemTable tbody tr:hover {
    background-color: #e0f2fe !important;
    /* Tailwind blue-100 */
  }

  /* ✅ Hilangkan border horizontal agar tampak lebih modern */
  #itemTable td,
  #itemTable th {
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
    font-size: 0.75rem;
    /* Tailwind text-xs */
    color: #6b7280;
    /* Tailwind gray-500 */
  }

  #itemTable_wrapper .dataTables_paginate {
    font-size: 0.75rem;
  }

  /* ✅ Scroll wrapper */
  .datatable-container {
    overflow-x: auto;
  }
</style>
@push('scripts')
<script>
  
  // === PENGAMBILAN DATA ARTIKEL, TRANSFER IN DAN RECEIVING (DROPDOWN) ===
  $(document).ready(function() {
    const supplierCodeToName = {};
    const types = ['article','transfer','receiving'];
    let currentTypeIndex = 0;
    let loadedItems = []; // menampung semua item yang sudah di-load

    function buildGroupedResults(items) {
        const grouped = {};
        items.forEach(item => {
            const type = item.type || 'Other';
            if (!grouped[type]) grouped[type] = [];
            grouped[type].push(item);
        });
        return Object.keys(grouped).map(key => ({
            text: key,
            children: grouped[key]
        }));
    }

    $('#selectArticle').select2({
        placeholder: "Pilih article atau transfer number...",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '/ppic/logistic/transfer_in/search_all',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '',
                    page: params.page || 1,
                    type: types[currentTypeIndex]  // tipe saat ini
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;

                // simpan semua item ke loadedItems
                loadedItems = loadedItems.concat(data.results);

                // jika page habis, pindah ke tipe berikutnya
                if(!data.pagination.more && currentTypeIndex < types.length-1){
                    currentTypeIndex++;
                }

                return {
                    results: buildGroupedResults(loadedItems), // buat optgroup per tipe
                    pagination: { more: data.pagination.more || currentTypeIndex < types.length-1 }
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // === INFORMASI & BARCODE YANG MUNCUL KETIKA KLIK DATA DARI DROPDOWN ===
    $('#selectArticle').on('select2:select', function(e) {
        const item = e.params.data.data;
        selectArticle(item);
    });

    function selectArticle(item) {
        const searchResult = $('#selectResult');
    const qrPreview = $('#qrPreview');
    const qrLoader = $('#qrLoader');

    let code, description, supplierName, typeLabel;

    // Tentukan berdasarkan tipe
    if(item.article_code) { // Article
        code = item.article_code;
        description = item.description || '';
        supplierName = item.supplier?.name ?? '-';
        typeLabel = 'Article';
    } else if(item.code) { // Transfer In
        code = item.code;
        description = ''; // biasanya Transfer In tidak punya description
        supplierName = item.supplier?.name ?? '-';
        typeLabel = 'Transfer In';
    } else if(item.receiving_number) { // Receiving
        code = item.receiving_number;
        description = ''; // bisa ditambahkan jika ada
        supplierName = item.supplier?.name ?? '-';
        typeLabel = 'Receiving';
    }

   searchResult.html(`
<div class="w-full max-w-md p-6 bg-white flex flex-col items-center gap-4">

    <!-- Info Section -->
    <div class="flex flex-col gap-3 w-full">
        <!-- Header -->
        <div class="flex items-center gap-2 text-gray-600 text-sm font-semibold mb-2 border-b border-gray-200 pb-2">
            <i data-feather="check-circle" class="w-5 h-5 text-green-500"></i>
            <span>Selected Code Information</span>
        </div>

        <!-- Code -->
       <div class="flex items-center gap-2">
    <span class="text-gray-900 bg-green-100 px-3 py-1 font-semibold text-base tracking-wide shadow-sm">
        ${code}
    </span>
</div>


        <!-- Description -->
        <div class="flex items-center font-semibold gap-2 text-gray-700 text-base">
            <span>${description || ''}</span>
        </div>

        <!-- Supplier -->
        <div class="flex items-center gap-2 text-gray-500 italic text-sm">
            <i data-feather="user" class="w-3 h-3"></i>
            <span>${supplierName || '-'}</span>
        </div>

        <!-- Type -->
        <div class="flex items-center gap-2 text-gray-500 italic text-sm">
            <i data-feather="tag" class="w-3 h-3"></i>
            <span>${typeLabel || '-'}</span>
        </div>
    </div>

</div>
`);
feather.replace();



    qrLoader.removeClass('hidden');
    qrPreview.addClass('invisible');
    qrPreview.off('load').on('load', function() {
        qrLoader.addClass('hidden');
        qrPreview.removeClass('invisible');
    });

    // Hanya buat QR code jika ada code
    if(code) {
        qrPreview.attr('src', `/storage/qrcodes/${code}.png`);
    } else {
        qrPreview.attr('src', '');
    }
}
});



  // === Variabel Global ===
  let scannedItems = {};
  let itemIndex = 1;
  let barcodeBuffer = '';
  let barcodeTimer = null;
  let activeSupplier = null;
  const warehouseNameToId = {};
  const supplierCodeToName = {}; // mapping supplier_code => supplier_name


  // === Inisialisasi ===
  document.addEventListener('DOMContentLoaded', function() {
    initElements();
    initEventListeners();
    initDropdowns();
    startScannerPolling();
  });

  function initElements() {
    const transferTypeSelect = document.getElementById('transfer_type');
    if (transferTypeSelect) {
      updateFormByTransferType(transferTypeSelect.value);
      transferTypeSelect.addEventListener('change', () => updateFormByTransferType(transferTypeSelect.value));
    }
  }

  function initDropdowns() {
    fetch('{{ route("ppic.warehouse.list") }}')
      .then(res => res.json())
      .then(data => {
        const from = document.getElementById('from_location');
        data.forEach(wh => {
          warehouseNameToId[wh.name] = wh.id;
          const opt = `<option value="${wh.id}">${wh.name}</option>`;
          from.insertAdjacentHTML('beforeend', opt);
        });
      })
      .catch(err => console.error('Gagal memuat warehouse:', err));
  }

  function initEventListeners() {
    document.getElementById('barcodeInput').addEventListener('keydown', barcodeInputHandler);
    document.getElementById('resetBtn').addEventListener('click', resetForm);
    document.getElementById('submitBtn').addEventListener('click', submitForm);
    document.getElementById('searchArticle').addEventListener('keyup', manualSearch);

    document.addEventListener('keydown', globalScannerListener);
  }

  // === UI Handlers ===
  function updateFormByTransferType(type) {
    document.getElementById('supplierWrapper').classList.toggle('hidden', type !== 'Incoming');
    document.getElementById('fromLocationWrapper').classList.toggle('hidden', type === 'Incoming');
  }

  function renderItemTable() {
    const itemList = document.getElementById('itemList');
    const transferType = document.getElementById('transfer_type').value;
    const isMaterialReturn = transferType === 'Material Return';
    itemList.innerHTML = '';
    itemIndex = 1;
    for (const code in scannedItems) {
      const item = scannedItems[code];
      let row = `
      <tr>
        <td class="border p-2 text-center">${itemIndex++}</td>
        <td class="border p-2">${item.code}</td>
        <td class="border p-2">${item.name}</td>`;
      if (isMaterialReturn) {
        row += `
        <td class="border p-2 text-center">
          ${item.qty_out}
        </td>`;
      }
      row += `
        <td class="border p-2 text-center">
        <input type="number" min="1" value="${item.qty}"
  class="w-20 text-center border rounded px-2 py-1 qty-input"
  data-code="${item.code}" data-qty-out="${item.qty_out}">

        </td>
        <td class="border p-2 text-center">${item.uom}</td>
        <td class="border p-2 text-center">${item.min_package}</td>
        <td class="border p-2 text-center">
  <input type="date" class="w-full border rounded px-2 py-1"
         min="<?= date('Y-m-d') ?>" />
</td>

        <td class="border p-2 text-center">
          ${item.destination_name}
          <input type="hidden" class="destination-id-hidden" value="${item.destination_id}" />
         <input type="hidden" class="origin-item-id" name="items[${itemIndex}][origin_item_id]" value="${item.origin_item_id ?? ''}">
<input type="hidden" class="origin-type" name="items[${itemIndex}][origin_type]" value="${item.origin_type ?? ''}">

        </td>
        <td class="border p-2 text-center">
          <button type="button" onclick="removeItem('${item.code}')" 
            class="text-red-500 hover:text-red-700 font-semibold">X</button>
        </td>
      </tr>`;

      itemList.insertAdjacentHTML('beforeend', row);
    }
    // ✅ Validasi qty_input supaya tidak lebih besar dari qty_out
    // satu kali di inisialisasi
    const itemListEl = document.getElementById('itemList');
    itemListEl.addEventListener('change', function(e) {
      const el = e.target;
      if (!el.classList.contains('qty-input')) return;

      const isMaterialReturn = document.getElementById('transfer_type').value === 'Material Return';
      const qtyOut = parseInt(el.dataset.qtyOut || '0', 10);
      let val = parseInt(el.value || '0', 10);

      if (isMaterialReturn && val > qtyOut) {
        Swal.fire({
          icon: 'warning',
          title: 'Qty Melebihi Batas!',
          text: `Qty tidak boleh lebih besar dari Qty Out (${qtyOut})`,
          confirmButtonText: 'OK'
        });
        el.value = qtyOut;
        val = qtyOut;
      }

      const code = el.dataset.code;
      if (scannedItems[code]) scannedItems[code].qty = val;
    });
    // ✅ Tampilkan kolom Qty Out di header hanya jika Material Return
    document.querySelectorAll('.material-return-col')
      .forEach(col => col.classList.toggle('hidden', !isMaterialReturn));
  }


  function removeItem(code) {
    delete scannedItems[code];
    renderItemTable();
    if (Object.keys(scannedItems).length === 0) {
      activeSupplier = null;
      document.getElementById('supplier_name').value = '';
    }
  }

  function handleScannedCode(code) {
    fetch(`/ppic/logistic/transfer_in/find/${encodeURIComponent(code)}`)
      .then(res => res.json())
      .then(data => {
        const transferType = document.getElementById('transfer_type').value;

        if (!transferType) {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih Transfer Type terlebih dahulu sebelum scan!'
          });
          return;
        }

        // 🚨 Batasi kalau yang discan itu Transfer In (TRIN)
        if (data.type === 'transfer_in' && transferType !== 'Material Return') {
          Swal.fire({
            icon: 'error',
            title: 'Tidak Valid',
            text: 'Kode Transfer In hanya bisa discan saat Transfer Type = Material Return'
          });
          return;
        }

        if (data.message === 'Not Found') {
          Swal.fire({
            icon: 'error',
            title: 'Kode Tidak ditemukan',
            text: `Kode: ${code}`
          });
          return;
        }

        if (transferType === 'Incoming') {
          const articleSupplierName = data.supplier_name?.trim() ?? '-';
          const articleSupplierCode = data.supplier_code ?? null;
          const supplierInput = document.getElementById('supplier_name');
          const hiddenSupplierIdInput = document.getElementById('supplier_code');
          const currentSupplier = supplierInput.value.trim();
          if (!articleSupplierCode) {
            Swal.fire({
              icon: 'error',
              title: 'Data Supplier Tidak Lengkap',
              text: 'Artikel ini tidak memiliki informasi supplier.'
            });
            return;
          }
          if (!currentSupplier) {
            supplierInput.value = articleSupplierName;
            hiddenSupplierIdInput.value = articleSupplierCode;
            activeSupplier = articleSupplierName;
          } else if (articleSupplierName !== currentSupplier) {
            Swal.fire({
              icon: 'warning',
              title: 'Supplier Berbeda',
              text: `Artikel ini berasal dari supplier berbeda (${articleSupplierName}). 
Hanya boleh scan dari supplier "${currentSupplier}".`
            });
            return;
          }
        }

        // === Kalau hasilnya dokumen (Transfer In / LPB) ===
        if (data.type === 'transfer_in' || data.type === 'lpb') {
          const transferType = document.getElementById('transfer_type').value;

          // ✅ Kalau Material Return, isi Reference Number dengan kode Transfer In
          if (transferType === 'Material Return' && data.type === 'transfer_in') {
            const refNumberInput = document.getElementById('reference_number');
            if (refNumberInput) {
              refNumberInput.value = data.code; // isi otomatis dengan kode TRIN
            }
          }
          data.items.forEach(item => {
            scannedItems[item.article_code] = scannedItems[item.article_code] ?
              {
                ...scannedItems[item.article_code],
                qty: scannedItems[item.article_code].qty + item.qty
              } :
              {
                code: item.article_code,
                name: item.description,
                uom: item.uom,
                min_package: item.min_package,
                destination_id: item.destination_id,
                destination_name: item.destination_name,
                qty: item.qty,
                qty_out: item.qty_used,
                // Tambahan untuk keperluan RETURN
                origin_item_id: item.id, // id asli dari DB
                origin_type: data.type === 'transfer_in' ?
                  'transfer_in' :
                  'lpb'
              };
          });

          Swal.fire({
            icon: 'success',
            title: `${data.type.toUpperCase()} ${data.code} dimuat!`,
            text: `${data.items.length} Article Succesfully Scan!`
          });
          renderItemTable();
          return;
        }

        // === Kalau hasilnya artikel ===
        if (data.type === 'article') {
          const item = {
            code: data.article_code,
            name: data.description,
            uom: data.uom,
            min_package: data.min_package,
            destination_id: data.destination_id,
            destination_name: data.destination_name,
            qty: 1
          };

          scannedItems[item.code] = scannedItems[item.code] ?
            {
              ...scannedItems[item.code],
              qty: scannedItems[item.code].qty + 1
            } :
            item;

          Swal.fire({
            icon: 'success',
            title: 'Article Succesfully Scan!',
            html: `<b>${item.code}</b><br>${item.name}<br>Qty: ${scannedItems[item.code].qty}`,
            timer: 2000,
            showConfirmButton: false
          });

          renderItemTable();
        }
        // === Kalau hasilnya ITEM (Transfer In Item langsung) ===
        if (data.type === 'transfer_in_item') {
          // Kalau Material Return, isi Reference Number dengan kode Transfer In dari relasi
          if (transferType === 'Material Return' && data.transfer_in_code) {
            const refNumberInput = document.getElementById('reference_number');
            if (refNumberInput) {
              refNumberInput.value = data.transfer_in_code;
            }
          }

          const item = {
            code: data.article_code,
            name: data.description,
            uom: data.uom,
            min_package: data.min_package,
            destination_id: data.destination_id,
            destination_name: data.destination_name,
            qty: data.qty,
            qty_out: data.qty_used,
            origin_item_id: data.id, // id item transfer_in
            origin_type: 'transfer_in_item' // untuk pembeda
          };

          // gabung kalau artikel sudah ada
          scannedItems[item.code] = scannedItems[item.code] ?
            {
              ...scannedItems[item.code],
              qty: scannedItems[item.code].qty + item.qty
            } :
            item;

          Swal.fire({
            icon: 'success',
            title: 'Article Succesfully Scan!',
            html: `<b>${item.code}</b><br>${item.name}<br>Qty: ${scannedItems[item.code].qty}`,
            timer: 2000,
            showConfirmButton: false
          });

          renderItemTable();
          return;
        }

      })
      .catch(err => {
        console.error('❌ Fetch error:', err);
        Swal.fire({
          icon: 'error',
          title: 'Gagal Memuat',
          text: err.message
        });
      });
  }



  function globalScannerListener(e) {
    if (e.ctrlKey || e.altKey || e.metaKey) return;
    if (e.key === 'Enter') {
      const code = barcodeBuffer.trim();
      if (code) handleScannedCode(code);
      barcodeBuffer = '';
      clearTimeout(barcodeTimer);
    } else {
      barcodeBuffer += e.key;
      clearTimeout(barcodeTimer);
      barcodeTimer = setTimeout(() => barcodeBuffer = '', 300);
    }
  }

  function barcodeInputHandler(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const code = this.value.trim();
      if (code) {
        handleScannedCode(code);
        this.value = '';
      }
    }
  }

  function startScannerPolling() {
    setInterval(() => {
      fetch('/api/scanner/latest')
        .then(res => res.json())
        .then(data => {
          if (data.code) {
            handleScannedCode(data.code);
            fetch('/api/scanner/reset', {
              method: 'POST'
            });
          }
        })
        .catch(err => console.error('Polling scanner error:', err));
    }, 2000);
  }

  




  // === Form Actions ===
  function resetForm() {
    document.getElementById('itemList').innerHTML = '';
    document.getElementById('barcodeInput').value = '';
    document.getElementById('note').value = '';
    document.getElementById('supplier_name').value = '';
    scannedItems = {};
    itemIndex = 1;
    activeSupplier = null;
  }
  // === Submit Form dengan jQuery ===
  function submitForm(e) {
    e.preventDefault();

    const transferType = $('#transfer_type').val();
    let supplierCode = $('#supplier_code').val();

    // ✅ Validasi hanya untuk Incoming
    if (transferType === 'Incoming' && !supplierCode) {
      Swal.fire({
        icon: 'warning',
        title: 'Supplier Belum Dipilih',
        text: 'Supplier ID tidak ditemukan. Pastikan artikel sudah discan.'
      });
      return;
    }

    // Jika Material Return, biarkan supplier_code null
    if (transferType === 'Material Return') {
      supplierCode = supplierCode || null;
    }

    const items = [];
    $('#itemList tr').each(function() {
      const tds = $(this).find('td');

      let qtyInput, destinationInput, expInput;
      if (transferType === 'Material Return') {
        qtyInput = tds.eq(4).find('input'); // kolom Qty
        destinationInput = tds.eq(8).find('input.destination-id-hidden');
        expInput = tds.eq(7).find('input'); // expired date
      } else {
        qtyInput = tds.eq(3).find('input');
        destinationInput = tds.eq(7).find('input.destination-id-hidden');
        expInput = tds.eq(6).find('input');
      }

      items.push({
        article_code: tds.eq(1).text().trim(),
        description: tds.eq(2).text().trim(),
        qty: qtyInput.length ? qtyInput.val() : 0,
        expired_date: expInput.length ? expInput.val() : '',
        destination_id: destinationInput.length ? destinationInput.val() : null,

        // ✅ tambahan untuk Material Return
        origin_item_id: $(this).find('input[name^="items"][name$="[origin_item_id]"]').val() || null,
        origin_type: $(this).find('input[name^="items"][name$="[origin_type]"]').val() || null


      });
    });

    const payload = {
      reference_number: $('#reference_number').val(),
      date: $('#date').val(),
      transfer_category: transferType,
      supplier_code: supplierCode,
      from_location: $('#from_location').val(),
      note: $('#note').val(),
      items: items
    };

    $.ajax({
      url: '/ppic/logistic/transfer_in/store',
      method: 'POST',
      data: JSON.stringify(payload),
      contentType: 'application/json',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json', // pastikan response JSON
      success: function(response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Transfer In berhasil disimpan!',
            timer: 2000,
            showConfirmButton: false
          });

          if (transferType !== 'Material Return' && response.labels) {
            printLabelsDirect(response.labels);
          }

          // Reset form
          resetForm();
          $('#reference_number').val('');
          $('#note').val('');
          $('#transfer_type').val('');
          $('#supplier_name').val('');
          $('#supplier_code').val('');
          $('#searchArticle').val('');
          $('#searchResult').html('<p class="text-gray-400 italic">Belum ada pencarian...</p>');
          $('#qrPreview').attr('src', "{{ asset('img/tf-in.png') }}");

        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message || 'Terjadi kesalahan saat menyimpan data.'
          });
        }
      },
      error: function(xhr, status, error) {
        let message = '❌ Gagal menyimpan data transfer.';
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.message) message += '\n' + res.message;
        } catch (e) {
          message += '\nServer tidak merespon JSON.';
        }
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: message
        });
        console.error('Error:', xhr, status, error);
      }
    });
  }



  // === Generate dan Print Langsung === 
  function printLabelsDirect(labels) {
    const html = generateLabelHTML(labels); // cuma qr_transfer
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.onload = () => {
      printWindow.focus();
      setTimeout(() => printWindow.print(), 500);
    };
  }

  // === Template HTML Label ===
  function generateLabelHTML(labels, options = ['qr_transfer', 'qr_item']) {
    if (!labels || !Array.isArray(labels)) {
      return `<html><body><h3>Tidak ada label untuk dicetak</h3></body></html>`;
    }
    let html = `<html><head><title>Cetak Label</title>
    <style>
     body { font-family: Arial; padding: 0; margin:0; }
  .label-container { 
    width: 15mm; 
    height: 15mm; 
    page-break-after: always; 
    text-align: center; 
    box-sizing: border-box; 
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  .label-container img { 
    width: 11mm;   /* QR sedikit lebih kecil biar ada ruang teks */
    height: 11mm; 
  }
  .label-container div { 
    font-size: 3pt;   /* font sangat kecil */
    line-height: 1;   /* hemat ruang */
    margin-top: 0.5mm;
  }
  @page {
    size: 15mm 15mm; /* sesuai ukuran label fisik */
    margin: 0;
  }
    </style>
  </head><body>`;

    labels.forEach(label => {
      // === Cetak QR Transfer ===
      if (label.type === 'qr_transfer' && options.includes('qr_transfer')) {
        html += `<div class="label-container">
        <img src="${label.qr_path}" />
        <div>${label.reference_number}</div>
      </div>`;
      }

      // === Cetak QR Item (duplikasi sesuai min_package) ===
      if (label.type === 'qr_item' && options.includes('qr_item')) {
        let minPackage = parseInt(label.min_package || 1, 10);
        let qtyIn = parseInt(label.qty || 0, 10);

        if (minPackage > 0 && qtyIn > 0) {
          let numLabels = Math.ceil(qtyIn / minPackage);
console.log(label.code, 'qtyIn:', qtyIn, 'minPackage:', minPackage, 'numLabels:', numLabels);
          for (let i = 0; i < numLabels; i++) {
            html += `<div class="label-container">
            <img src="${label.qr_path}" />
            <div>${label.code}</div>
          </div>`;
          }
        }
      }
    });

    html += `</body></html>`;
    return html;
  }
</script>
@endpush
@endsection