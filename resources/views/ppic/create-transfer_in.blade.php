@extends('layouts.app')

@section('title', 'Create Transfer In')
@section('page-title', 'CREATE TRANSFER IN')
@section('breadcrumb-item', 'Transfer In')
@section('breadcrumb-active', 'Create Transfer In')

@section('content')
<div>
  <!-- 📦 Main Transfer Panel -->
  <div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Transfer In</h2>

    <form id="transfer-form">
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label for="transfer_type" class="block text-sm font-medium text-gray-700 mb-1">Transfer Type <small class="text-red-600"> *</small></label>
          <select name="transfer_type" id="transfer_type"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Type --</option>
            <option value="Incoming">Incoming</option>
            <option value="Material Return">Material Return</option>
            <option value="Temporary Saved">Temporary Saved</option>
          </select>
        </div>

        <div id="supplierWrapper">
          <label for="supplier_code" class="block text-sm font-medium text-gray-700 mb-1">Supplier <small class="text-red-600"> *</small></label>
          <input type="text" id="supplier_name" name="supplier_name"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm bg-gray-100 text-gray-700"
            readonly />
          <input type="hidden" id="supplier_code" name="supplier_code">
        </div>

        <div id="fromLocationWrapper">
          <label for="from_location" class="block text-sm font-medium text-gray-700 mb-1">Location From <small class="text-red-600"> *</small></label>
          <select name="from_location" id="from_location"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Location From --</option>
          </select>
        </div>
      </div>

      <!-- 📝 Catatan Tambahan -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
          <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
          <textarea id="note" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
        </div>
      </div>

      <!-- 🧾 Divider -->
      <hr class="my-6 border-gray-300">

   <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 mb-4">

  <!-- Select Article -->
  <div class="w-full md:w-1/2">
    <label for="article_select" class="block text-sm font-medium text-gray-700 mb-1">
      Select Article / Kode Transfer In
    </label>
    <select id="selectArticle"
      class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      <option value="">-- Choose Article --</option>
    </select>
  </div>

  <!-- Divider (mobile only) -->
  <div class="flex md:hidden items-center w-full my-2">
    <div class="flex-grow border-t border-dashed border-gray-300"></div>
    <span class="px-3 text-gray-400 text-sm font-medium">or</span>
    <div class="flex-grow border-t border-dashed border-gray-300"></div>
  </div>

  <!-- Button -->
  <div class="w-full md:w-auto flex justify-center md:justify-end">
    <button type="button" id="scanQrBtn"
      class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded shadow-md transition">
      <i data-feather="camera" class="h-4 w-4"></i> Scan QR Code
    </button>
  </div>
</div>


<!-- QR Scan Modal -->
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg shadow-lg p-4 w-11/12 md:w-1/2">
    <div class="flex justify-between items-center mb-2">
      <h3 class="text-lg font-bold">Scan QR Code</h3>
      <button id="closeQrModal" class="text-red-500 hover:text-red-700 font-bold">X</button>
    </div>
    <div id="qr-reader" style="width:100%;"></div>
  </div>
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
          <i data-feather="save" class="h-4 w-4"></i> Save
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

  .select2-container {
    width: 100% !important;
}


 .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 4px 10px !important;
        border: 1px solid #d1d5db !important; /* gray-300 */
        border-radius: 0.375rem !important; /* rounded-md */
        font-size: 1rem !important; /* text-base */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px;
    }
</style>
@push('scripts')
<script>
  
  // =====================
// === VARIABEL GLOBAL ===
// =====================
let scannedItems = {};
let itemIndex = 1;
let activeSupplier = null;
let warehouseNameToId = {};
let html5QrcodeScanner = null;
let isScannerRunning = false;

// =====================
// === INISIALISASI HALAMAN ===
// =====================
$(document).ready(function() {
    initPage();
});

function initPage() {
    initElements();
    initDropdowns();
    initEventListeners();
    initSelect2Search();
    initQrScanner();
}

// =====================
// === INIT ELEMENTS ===
// =====================
// === INISIALISASI ELEMENT ===
function initElements() {
    const $transferType = $('#transfer_type');
    if ($transferType.length) {
        updateFormByTransferType($transferType.val());
        $transferType.on('change', function() {
            updateFormByTransferType($(this).val());
        });
    }
}

// =====================
// === UPDATE FORM UI ===
// =====================
function updateFormByTransferType(type) {
    $('#supplierWrapper').toggle(type === 'Incoming');
    $('#fromLocationWrapper').toggle(type !== 'Incoming');

    // Sembunyikan kolom Material Return di tabel
    const isMaterialReturn = type === 'Material Return';
    $('.material-return-col').toggle(isMaterialReturn);
}

// =====================
// === INIT DROPDOWNS ===
// =====================
function initDropdowns() {
    fetch('{{ route("ppic.warehouse.list") }}')
        .then(res => res.json())
        .then(data => {
            const from = $('#from_location');
            data.forEach(wh => {
                warehouseNameToId[wh.name] = wh.id;
                from.append(`<option value="${wh.id}">${wh.name}</option>`);
            });
        })
        .catch(err => console.error('Gagal memuat warehouse:', err));
}

// =====================
// === EVENT LISTENERS ===
// =====================
function initEventListeners() {
    $('#barcodeInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = $(this).val().trim();
            if (code) {
                handleScannedCode(code);
                $(this).val('');
            }
        }
    });

    $('#resetBtn').on('click', resetForm);
    $('#submitBtn').on('click', submitForm);

    // Global keyboard scanner (opsional)
    let barcodeBuffer = '';
    let barcodeTimer = null;
    $(document).on('keydown', function(e) {
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
    });
}

// =====================
// === INIT SELECT2 SEARCH ===
// =====================
function initSelect2Search() {
    const types = ['article', 'transfer', 'receiving'];
    let currentTypeIndex = 0;
    let loadedItems = [];

    $('#selectArticle').select2({
        placeholder: "Pilih article atau Transfer Number...",
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
                    type: types[currentTypeIndex]
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                loadedItems = loadedItems.concat(data.results);

                if (!data.pagination.more && currentTypeIndex < types.length - 1) {
                    currentTypeIndex++;
                }

                return {
                    results: buildGroupedResults(loadedItems),
                    pagination: { more: data.pagination.more || currentTypeIndex < types.length - 1 }
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    $('#selectArticle').on('select2:select', function(e) {
        const data = e.params.data;
        if (!data || !data.code) {
            Swal.fire({ icon: 'error', title: 'Data tidak valid', text: 'Tidak ada kode yang dipilih.' });
            return;
        }
        handleScannedCode(data.code);
        $(this).val(null).trigger('change');
    });
}

function buildGroupedResults(items) {
    const grouped = {};
    items.forEach(item => {
        const type = item.type || 'Other';
        const code = item.code || item.article_code || item.transfer_number || item.receiving_number || item.id;
        const select2Item = { id: code, text: `${code} - ${item.description || ''}`, code: code, type: type, ...item };
        if (!grouped[type]) grouped[type] = [];
        grouped[type].push(select2Item);
    });
    return Object.keys(grouped).map(key => ({ text: key, children: grouped[key] }));
}

// =====================
// === QR SCANNER ===
// =====================
function initQrScanner() {
    $('#scanQrBtn').click(function() {
        $('#qrModal').removeClass('hidden');

        if (!html5QrcodeScanner) html5QrcodeScanner = new Html5Qrcode("qr-reader");

        const config = { fps: 10, qrbox: 250 };
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                handleScannedCode(decodedText);
                stopQrScanner();
            },
            (errorMessage) => { /* optional debug */ }
        ).then(() => isScannerRunning = true)
         .catch(err => { console.error("QR Scan start failed:", err); $('#qrModal').addClass('hidden'); });
    });

    $('#closeQrModal').click(stopQrScanner);
}

function stopQrScanner() {
    if (html5QrcodeScanner && isScannerRunning) {
        html5QrcodeScanner.stop().then(() => {
            $('#qrModal').addClass('hidden');
            isScannerRunning = false;
        }).catch(err => {
            console.error('Stop error:', err);
            $('#qrModal').addClass('hidden');
            isScannerRunning = false;
        });
    } else {
        $('#qrModal').addClass('hidden');
    }
}

// =====================
// === RENDER ITEM TABLE ===
// =====================
function renderItemTable() {
    const $itemList = $('#itemList');
    const transferType = $('#transfer_type').val();
    const isMaterialReturn = transferType === 'Material Return';
    $itemList.html('');
    itemIndex = 1;

    for (const code in scannedItems) {
        const item = scannedItems[code];
        let row = `<tr>
            <td class="border p-2 text-center">${itemIndex++}</td>
            <td class="border p-2">${item.code}</td>
            <td class="border p-2">${item.name}</td>`;
        if (isMaterialReturn) row += `<td class="border p-2 text-center">${item.qty_out}</td>`;
        row += `<td class="border p-2 text-center">
            <input type="number" min="1" value="${item.qty}" class="w-20 text-center border rounded px-2 py-1 qty-input" data-code="${item.code}" data-qty-out="${item.qty_out}">
        </td>
        <td class="border p-2 text-center">${item.uom}</td>
        <td class="border p-2 text-center">${item.min_package}</td>
        <td class="border p-2 text-center"><input type="date" class="w-full border rounded px-2 py-1" min="<?= date('Y-m-d') ?>" /></td>
        <td class="border p-2 text-center">
            ${item.destination_name}
            <input type="hidden" class="destination-id-hidden" value="${item.destination_id}" />
            <input type="hidden" class="origin-item-id" name="items[${itemIndex}][origin_item_id]" value="${item.origin_item_id ?? ''}">
            <input type="hidden" class="origin-type" name="items[${itemIndex}][origin_type]" value="${item.origin_type ?? ''}">
        </td>
        <td class="border p-2 text-center">
            <button type="button" onclick="removeItem('${item.code}')" class="text-red-500 hover:text-red-700 font-semibold">X</button>
        </td>
        </tr>`;

        $itemList.append(row);
    }

    // Validasi qty input
    $itemList.off('change', '.qty-input').on('change', '.qty-input', function() {
        const val = parseInt($(this).val(), 10);
        const maxQty = parseInt($(this).data('qty-out') || '0', 10);
        const code = $(this).data('code');

        if (isMaterialReturn && val > maxQty) {
            Swal.fire({ icon: 'warning', title: 'Qty Melebihi Batas!', text: `Qty tidak boleh lebih besar dari Qty Out (${maxQty})`, confirmButtonText: 'OK' });
            $(this).val(maxQty);
            scannedItems[code].qty = maxQty;
        } else if (scannedItems[code]) {
            scannedItems[code].qty = val;
        }
    });
}

// =====================
// === HANDLE SCANNED CODE ===
// =====================
function handleScannedCode(code) {
    fetch(`/ppic/logistic/transfer_in/find/${encodeURIComponent(code)}`)
        .then(res => res.json())
        .then(data => {
            const transferType = $('#transfer_type').val();
            if (!transferType) return Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih Transfer Type terlebih dahulu!' });

            // Cek tipe data
            if (data.message === 'Not Found') return Swal.fire({ icon: 'error', title: 'Kode Tidak ditemukan', text: `Kode: ${code}` });

            if (data.type === 'article' || data.type === 'transfer_in_item') {
                addItemToScanned(data, transferType);
            } else if (data.type === 'transfer_in' || data.type === 'lpb') {
                data.items.forEach(item => addItemToScanned({ ...item, type: 'article' }, transferType));
            }

            renderItemTable();
        })
        .catch(err => console.error('Fetch error:', err));
}

function addItemToScanned(item, transferType) {
    const code = item.code || item.article_code;
    if (!code) return;

    // === Supplier handling hanya untuk Incoming ===
    if (transferType === 'Incoming' && item.supplier_name && item.supplier_code) {
        const currentSupplier = $('#supplier_name').val().trim();

        if (!currentSupplier) {
            // Pertama kali scan, set supplier
            $('#supplier_name').val(item.supplier_name);
            $('#supplier_code').val(item.supplier_code);
            activeSupplier = item.supplier_name;
        } else if (currentSupplier !== item.supplier_name) {
            // Supplier berbeda → warning, jangan render
            Swal.fire({
                icon: 'warning',
                title: 'Supplier Berbeda',
                text: `Artikel ini berasal dari supplier berbeda (${item.supplier_name}). Hanya boleh scan dari supplier "${currentSupplier}".`
            });
            return; // hentikan proses, jangan tambah ke scannedItems
        }
    }

    const qty = item.qty || 1;

    // === Tambahkan item ke scannedItems ===
    scannedItems[code] = scannedItems[code]
        ? { ...scannedItems[code], qty: scannedItems[code].qty + qty }
        : {
            code: code,
            name: item.name || item.description,
            uom: item.uom,
            min_package: item.min_package,
            destination_id: item.destination_id,
            destination_name: item.destination_name,
            qty: qty,
            qty_out: item.qty_out || 0,
            origin_item_id: item.origin_item_id || null,
            origin_type: item.origin_type || null
        };

    Swal.fire({
        icon: 'success',
        title: 'Article Succesfully Scan!',
        html: `<b>${code}</b><br>${item.name || item.description}<br>Qty: ${scannedItems[code].qty}`,
        timer: 2000,
        showConfirmButton: false
    });

    renderItemTable();
}



// =====================
// === REMOVE ITEM ===
// =====================
function removeItem(code) {
    delete scannedItems[code];
    renderItemTable();
    if (Object.keys(scannedItems).length === 0) {
        activeSupplier = null;
        $('#supplier_name').val('');
    }
}

// =====================
// === RESET FORM ===
// =====================
function resetForm() {
    $('#itemList').html('');
    $('#barcodeInput').val('');
    $('#note').val('');
    $('#supplier_name').val('');
    $('#supplier_code').val('');
    scannedItems = {};
    itemIndex = 1;
    activeSupplier = null;
}

// =====================
// === SUBMIT FORM ===
// =====================
function submitForm(e) {
    e.preventDefault();
    const transferType = $('#transfer_type').val();
    let supplierCode = $('#supplier_code').val();

    if (transferType === 'Incoming' && !supplierCode) return Swal.fire({ icon: 'warning', title: 'Supplier Belum Dipilih', text: 'Supplier ID tidak ditemukan.' });

    const items = [];
    $('#itemList tr').each(function() {
        const tds = $(this).find('td');
        const qtyInput = tds.eq(3).find('input');
        const expInput = tds.eq(6).find('input');
        const destinationInput = tds.eq(7).find('input.destination-id-hidden');

        items.push({
            article_code: tds.eq(1).text().trim(),
            description: tds.eq(2).text().trim(),
            qty: qtyInput.val(),
            expired_date: expInput.val(),
            destination_id: destinationInput.val(),
            origin_item_id: $(this).find('input[name$="[origin_item_id]"]').val(),
            origin_type: $(this).find('input[name$="[origin_type]"]').val()
        });
    });

    const payload = {
        reference_number: $('#reference_number').val(),
        date: $('#date').val(),
        transfer_category: transferType,
        supplier_code: supplierCode || null,
        from_location: $('#from_location').val(),
        note: $('#note').val(),
        items: items
    };

    $.ajax({
        url: '/ppic/logistic/transfer_in/store',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Transfer In berhasil disimpan!', timer: 2000, showConfirmButton: false });
                resetForm();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Terjadi kesalahan saat menyimpan data.' });
            }
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: '❌ Gagal menyimpan data transfer.' });
        }
    });
}

// =====================
// === PRINT / GENERATE LABEL ===
// =====================

// Cetak semua item yang sudah discan
function printLabels() {
    const items = Object.values(scannedItems);
    if (items.length === 0) return Swal.fire({ icon: 'warning', title: 'Belum ada item', text: 'Silakan scan atau pilih item terlebih dahulu.' });

    let htmlContent = `<html><head><title>Label Print</title><style>
        body { font-family: Arial, sans-serif; }
        .label { border: 1px solid #000; padding: 10px; margin: 5px; display: inline-block; width: 200px; }
        .label h4 { margin: 0 0 5px 0; font-size: 16px; }
        .label p { margin: 2px 0; font-size: 14px; }
    </style></head><body>`;

    items.forEach(item => {
        htmlContent += `<div class="label">
            <h4>${item.code}</h4>
            <p>${item.name}</p>
            <p>Qty: ${item.qty} ${item.uom}</p>
            ${item.destination_name ? `<p>To: ${item.destination_name}</p>` : ''}
        </div>`;
    });

    htmlContent += `</body></html>`;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Cetak label untuk item tertentu saja
function printLabelForItem(code) {
    const item = scannedItems[code];
    if (!item) return Swal.fire({ icon: 'error', title: 'Item Tidak Ditemukan' });

    let htmlContent = `<html><head><title>Label Print</title><style>
        body { font-family: Arial, sans-serif; }
        .label { border: 1px solid #000; padding: 10px; margin: 5px; display: inline-block; width: 200px; }
        .label h4 { margin: 0 0 5px 0; font-size: 16px; }
        .label p { margin: 2px 0; font-size: 14px; }
    </style></head><body>`;

    htmlContent += `<div class="label">
        <h4>${item.code}</h4>
        <p>${item.name}</p>
        <p>Qty: ${item.qty} ${item.uom}</p>
        ${item.destination_name ? `<p>To: ${item.destination_name}</p>` : ''}
    </div>`;

    htmlContent += `</body></html>`;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Contoh penggunaan tombol di HTML
// <button onclick="printLabels()">Print Semua Label</button>
// <button onclick="printLabelForItem('ITEM123')">Print ITEM123</button>

</script>
@endpush
@endsection