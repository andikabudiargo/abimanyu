@extends('layouts.app')

@section('title', 'Create Transfer Out')
@section('page-title', 'CREATE TRANSFER OUT')
@section('breadcrumb-item', 'Transfer Out')
@section('breadcrumb-active', 'Create Transfer Out')

@section('content')
<div class="flex gap-6">
  <!-- 📦 Main Transfer Panel -->
  <div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Create Transfer Out</h2>
    <form id="transfer-form">
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
        <input type="text" name="reference_number" id="reference_number"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukan Nomor WOS, Material Request atau yang setara. . ." />
        </div>
         <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Outgoing Date<small class="text-red-600"> *</small></label>
        <input type="date" name="date" id="date" value="<?= date('Y-m-d') ?>"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
      </div>

      
      <!-- 📦 Lokasi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
        <label for="transfer_type" class="block text-sm font-medium text-gray-700 mb-1">Transfer Type <small class="text-red-600"> *</small></label>
       <select name="transfer_type" id="transfer_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Type --</option>
            <option value="Transfer Loading">Transfer Loading</option>
            <option value="Customer Return">Customer Return</option>
            <option value="Mutasi">Mutasi</option>
            <option value="Sample">Sample</option>
            <option value="Trial">Trial</option>
          </select>
        </div>
      </div>

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
      Select Kode Transfer In / Receiving
    </label>
    <select id="selectArticle"
      class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
      <option value="">-- Choose Code --</option>
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
    <div class="flex justify-between items-start mb-2">
  <div class="flex flex-col">
    <h2 class="text-2xl font-bold text-gray-900">Scan QR Code</h2>
    <p class="text-xs text-gray-500 mt-1">Scan QR yang sudah diprint dari Transfer In/Receiving</p>
    <div class="w-14 h-1 bg-teal-600 rounded mt-2"></div>
  </div>
  <button id="closeQrModal" class="text-red-500 hover:text-red-700 font-bold">X</button>
</div>
    <div id="qr-reader" style="width:100%;"></div>
  </div>
</div>


      <!-- 📋 Tabel Artikel yang Dipindahkan -->
     <div class="datatable-container">
  <table id="itemTable" class="min-w-full bg-white border border-gray-200">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="p-2 border text-center">No.</th>
        <th class="p-2 border">Item</th>
        <th class="p-2 border text-center">Balance</th>
        <th class="p-2 border text-center">Qty Out</th>
        <th class="p-2 border text-center">UOM</th>
        <th class="p-2 border text-center">Min Package</th>
        <th class="p-2 border text-center">Expired Date</th>
        <th class="p-2 border">From</th>
        <th class="p-2 border">Destination</th>
      </tr>
    </thead>
    <tbody id="itemList">
     <td colspan="9" class="text-center">No Data Available</td>
    </tbody>
  </table>
</div>


      <!-- 🎯 Tombol Submit -->
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

<style>
  /* ✅ Perbaiki Header Tabel */
  #itemTable thead th {
    background-color: #1e3a8a !important;  /* Tailwind blue-800 */
    color: #ffffff !important;             /* Putih */
    font-weight: 600 !important;           /* Bold */
    padding: 10px !important;
    border: none !important;
  }

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
<script src="https://unpkg.com/html5-qrcode@2.3.7/minified/html5-qrcode.min.js"></script>
<script>
  
let scannedItems = {};  // ← WAJIB ADA
let activeSupplier = null;  // jika perlu untuk pengecekan supplier
let locations = [];
let warehouses = [];
let currentTransferType = '';
let itemTable = null;
let itemIndex = 1;
let barcodeBuffer = '';
let barcodeTimer = null;
let html5QrcodeScanner = null;
let isScannerRunning = false;

// === Inisialisasi ===
document.addEventListener('DOMContentLoaded', function () {
  initEventListeners();
  initDropdowns();
  startScannerPolling();
});

function initEventListeners() {
  document.getElementById('barcodeInput').addEventListener('keydown', barcodeInputHandler);
  document.getElementById('resetBtn').addEventListener('click', resetForm);
  document.getElementById('submitBtn').addEventListener('click', submitForm);
  document.getElementById('searchArticle').addEventListener('keyup', manualSearch);

  document.addEventListener('keydown', globalScannerListener);
}

function initQrScanner() {
    $('#scanQrBtn').click(function() {
        $('#qrModal').removeClass('hidden');

        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
        }

        const config = { fps: 10, qrbox: 250 };

        setTimeout(() => {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    // Panggil handleScannedCode agar langsung render tabel
                    handleScannedCode(decodedText);
                    stopQrScanner();
                },
                (errorMessage) => {
                    console.log("Scan error:", errorMessage);
                }
            ).then(() => isScannerRunning = true)
             .catch(err => {
                 console.error("QR Scan start failed:", err);
                 $('#qrModal').addClass('hidden');
             });
        }, 300);
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

// Inisialisasi scanner saat DOM siap
$(document).ready(function() {
    initQrScanner();
});

$(document).ready(function() {
    $('#selectArticle').select2({
        placeholder: '-- Choose Kode Transfer In / Receiving --',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '/ppic/logistic/transfer_out/transfer-in-search', // endpoint
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '',   // search term
                    page: params.page || 1  // page for lazy load
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;

                // data.results sesuai struktur JSON dari backend
                const results = (data.results || []).map(item => ({
                    id: item.code,
                    text: `${item.code} - ${item.transfer_type}`,
                    itemData: item
                }));

                return {
                    results: results,
                    pagination: {
                        more: data.pagination?.more || false
                    }
                };
            },
            cache: true
        }
    });

    // Saat pilih item, otomatis panggil handleScannedCode
    $('#selectArticle').on('select2:select', function(e) {
        const selectedItem = e.params.data.itemData;
        handleScannedCode(selectedItem.code);
        $(this).val(null).trigger('change'); // reset dropdown
    });
});





function manualSearch() {
  const query = this.value.trim();
  const resultBox = document.getElementById('searchResult');
  const qrPreview = document.getElementById('qrPreview');
  const qrLoader = document.getElementById('qrLoader');

  if (query.length < 2) {
    resultBox.innerHTML = '<p class="text-gray-400 italic">Ketik minimal 2 huruf untuk mencari...</p>';
    qrPreview.src = "{{ asset('img/tf-in.png') }}";
    return;
  }

  fetch(`/ppic/logistic/transfer_out/transfer-in-search?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      console.log("Hasil Fetch:", data);
      if (data.length === 0) {
        resultBox.innerHTML = '<p class="text-red-500 italic">Data tidak ditemukan.</p>';
        qrPreview.src = "{{ asset('img/tf-in.png') }}";
        return;
      }

      resultBox.innerHTML = data.map(item => {
        // Tentukan infoLine berdasarkan transfer_type
        let infoLine = '';
        if (item.transfer_type === 'Transfer In') {
          infoLine = `From: ${item.warehouse_name}`;
        } else if (item.transfer_type === 'Receiving') {
          infoLine = `From: ${item.warehouse_name}`;
        }  else if (item.transfer_type === 'Transfer In Items') {
          infoLine = `From: ${item.warehouse_name}`;
        }

        return `
          <div class="border p-2 rounded hover:bg-gray-100 cursor-pointer"
               onclick="selectTransferIn('${item.code}', '${item.supplier_name}', '${item.supplier_code}', '${item.warehouse_name}', '${item.transfer_type}')">
            <div class="font-semibold">${item.code}</div>
            <div class="text-sm text-gray-600">Type: ${item.transfer_type}</div>
          </div>
        `;
      }).join('');
    })
    .catch(err => {
      console.error(err);
      resultBox.innerHTML = '<p class="text-red-500 italic">Gagal memuat data.</p>';
      qrPreview.src = "{{ asset('img/tf-in.png') }}";
    });
}


function renderItemTable() {
  const itemList = document.getElementById('itemList');
  itemList.innerHTML = '';

  let index = 1;

   console.log("🔍 Data yang akan dirender:", scannedItems); // DEBUG

  for (const key in scannedItems) {
    const item = scannedItems[key];
    if (!item) return; // kalau datanya null/undefined skip

    let destinationOptions = '';

    if (currentTransferType === 'Trial' || currentTransferType === 'Sample') {
      destinationOptions = `<option value="Engineering" selected>Engineering</option>`;
      item.destination_id = 'Engineering';
    } else if (currentTransferType === 'Transfer Loading') {
      destinationOptions = `<option value="Produksi" selected>Produksi</option>`;
      item.destination_id = 'Produksi';
    } else if (currentTransferType === 'Mutasi') {
      destinationOptions = `<option value="">-- Pilih Destination --</option>`;
      warehouses.forEach(w => {
        const selected = item.destination_id === w.name ? 'selected' : '';
        destinationOptions += `<option value="${w.name}" ${selected}>${w.name}</option>`;
      });
    } else if (currentTransferType === 'Customer Return') {
      const destinationName = item.supplier_name ?? 'Customer';
      destinationOptions = `<option value="${destinationName}" selected>${destinationName}</option>`;
      item.destination_id = destinationName;
    } else {
      destinationOptions = `<option value="">-- Select --</option>`;
    }

    const rowHTML = `
      <tr>
        <td class="border p-2 text-center">${index++}</td>
        <td class="border p-2">${item.code} - ${item.name}</td>
        <td class="border p-2 text-center">${item.balance}</td>
        <td class="border p-2 text-center">
          <input type="number" value="${item.balance}" class="w-20 text-center border rounded px-2 py-1" onchange="updateQty('${key}', this.value)">
        </td>
        <td class="border p-2 text-center">${item.uom}</td>
        <td class="border p-2 text-center">${item.min_package}</td>
        <td class="border p-2 text-center">${item.expired_date || '-'}</td>
        <td class="border p-2">${item.from_name}</td>
        <td class="border p-2">${item.destination_id || '-'}</td>
      </tr>
    `;

    itemList.insertAdjacentHTML('beforeend', rowHTML);
  }
}


function updateQty(code, value) {
    if (!scannedItems[code]) return;

    const item = scannedItems[code];
    const inputQty = parseFloat(value) || 0;

    if (inputQty > item.balance) {
        // Reset input ke qty maksimal (balance)
        scannedItems[code].qty = item.balance;

        // Update input field supaya terlihat
        const inputField = document.querySelector(`input[onchange="updateQty('${code}', this.value)"]`);
        if (inputField) inputField.value = item.balance;

        // SweetAlert peringatan
        Swal.fire({
            icon: 'warning',
            title: 'Qty Melebihi Stok!',
            text: `Qty yang dimasukkan (${inputQty}) melebihi Qty In (${item.balance})`,
            confirmButtonText: 'OK'
        });
    } else {
        scannedItems[code].qty = inputQty;
    }
}


function updateExpired(code, value) {
  if (scannedItems[code]) scannedItems[code].expired_date = value;
}

function updateDestination(code, value) {
  if (scannedItems[code]) scannedItems[code].destination_id = value;
}



function selectTransferIn(code, supplierName = '-', supplierCode = '', warehouseName = '-', transferType = '-') {
  const searchInput = document.getElementById('searchTFIn');
  const searchResult = document.getElementById('searchResult');
  const qrPreview = document.getElementById('qrPreview');
  const qrLoader = document.getElementById('qrLoader');

  searchInput.value = code;

  const warehouseInput = document.getElementById('warehouse_name');
  const transferTypeInput = document.getElementById('transfer_type_display');

  if (warehouseInput) warehouseInput.value = warehouseName;
  if (transferTypeInput) transferTypeInput.textContent = transferType;


  searchResult.innerHTML = `
    <p class="text-sm">
      ✅ Transfer In Selected:<br>
      <strong>${code}</strong><br>
      Type: ${transferType}<br>
    </p>
  `;

  qrLoader.classList.remove('hidden');
  qrPreview.classList.add('invisible');
  qrPreview.onload = () => {
    qrLoader.classList.add('hidden');
    qrPreview.classList.remove('invisible');
  };
  qrPreview.src = `/storage/qrcodes/${code}.png`;
}

function handleScannedCode(code) {
  const transferType = document.getElementById('transfer_type').value;
  if (!transferType) {
    Swal.fire({
      icon: 'warning',
      title: 'Pilih Transfer Type',
      text: 'Pilih Transfer Type terlebih dahulu sebelum scan!'
    });
    return;
  }

  if (scannedItems[code]) {
    Swal.fire({
      icon: 'warning',
      title: 'Item Sudah Discan',
      text: 'QR ini sudah discan dan ada di tabel!'
    });
    return;
  }

  fetch(`/ppic/logistic/transfer_out/scan-lookup/${encodeURIComponent(code)}`)
    .then(res => res.json())
    .then(data => {
      console.log('✅ Hasil data scan:', data);

      // === HANDLE TRANSFER ===
      if (data.type === 'transfer') {
  const transfer = data.transfer;
  const items = data.items;
  let hasStock = false; // ✅ Tambahkan flag

  items.forEach(item => {
    const itemKey = `${transfer.code}-${item.article_code}`;
   const balance = ((parseFloat(item.qty) || 0) + (parseFloat(item.qty_return) || 0)) 
              - (parseFloat(item.qty_used) || 0);
               const inputElement = document.querySelector(`#input-${itemKey}`);
  const inputQty = parseFloat(inputElement?.value) || 0; // ambil dari input


    if (balance > 0 && !scannedItems[itemKey]) {
      scannedItems[itemKey] = {
        type: 'transfer',
        transfer_in_code: transfer?.code ?? null,
        transfer_in_item_code: null,
        code: item.article_code,
        name: item.description,
       qty: parseFloat(item.balance) || 0, // ambil dari balance, bukan query selector
        uom: item.uom,
        min_package: item.min_package,
        expired_date: item.expired_date ?? '',
        from_name: item.warehouse_name ?? '-',
        destination_id: '',
        supplier_name: transfer?.supplier_name ?? null,
        balance: balance
      };
      hasStock = true; // ✅ Set true jika ada stok
    }
  });

  if (hasStock) {
    Swal.fire({
      icon: 'success',
      title: 'Kode Transfer Berhasil Discan',
      html: `<p><strong>Kode Transfer:</strong> ${transfer.code}</p>`,
      confirmButtonText: 'OK'
    });
    renderItemTable();
    document.getElementById('transfer_type').disabled = true;
  } else {
    Swal.fire({
      icon: 'error',
      title: 'Stok Habis',
      text: 'Tidak ada stok tersisa untuk kode transfer ini.'
    });
  }
}


      // === HANDLE RECEIVING ===
     else if (data.type === 'receiving') {
  const receiving = data.receiving;
  const items = data.items;
  let hasStock = false; // ✅ Flag untuk cek stok tersisa

  items.forEach(item => {
    const itemKey = `${receiving.receiving_number}-${item.article_code}`;
    const totalQty = parseFloat(item.total_qty) || 0;
    const totalUsed = parseFloat(item.total_used) || 0;
    const balance = totalQty - totalUsed;

    if (balance > 0 && !scannedItems[itemKey]) {
      scannedItems[itemKey] = {
        type: 'receiving',
        transfer_in_code: receiving?.receiving_number ?? null,
        code: item.article_code,
        name: item.description,
       qty: parseFloat(item.balance) || 0, // ambil dari balance, bukan query selector
        uom: item.uom,
        min_package: item.min_package,
        expired_date: item.expired_date ?? '',
        from_name: item.warehouse_name ?? '-',
        destination_id: '-',
        supplier_name: receiving?.supplier_name ?? null,
        balance: balance
      };
      hasStock = true; // ✅ Stok ada
    }
  });

  // ✅ Cek apakah ada stok
  if (hasStock) {
    Swal.fire({
      icon: 'success',
      title: 'Kode Receiving Berhasil Discan',
      html: `<p><strong>Kode Receiving:</strong> ${receiving.receiving_number}</p>`,
      confirmButtonText: 'OK'
    });

    console.log('Items:', items);
    console.log('📦 scannedItems setelah receiving:', JSON.stringify(scannedItems, null, 2));

    renderItemTable();
    document.getElementById('transfer_type').disabled = true;
  } else {
    Swal.fire({
      icon: 'error',
      title: 'Stok Habis',
      text: 'Tidak ada stok tersisa untuk kode receiving ini.'
    });
  }
}


      // === HANDLE ITEM ===
      else if (data.type === 'item') {
        const transfer = data.transfer;
        const item = data.item;
        const balance = ((parseFloat(item.qty) || 0) + (parseFloat(item.qty_return) || 0)) 
              - (parseFloat(item.qty_used) || 0);
                const inputElement = document.querySelector(`#input-${itemKey}`);
  const inputQty = parseFloat(inputElement?.value) || 0; // ambil dari input


        Swal.fire({
          icon: balance > 0 ? 'success' : 'error',
          title: balance > 0 ? 'Item Ditemukan' : 'Stok Habis',
          html: `
            <p><strong>Item:</strong> ${item.article_code} - ${item.description}</p>
            <p><strong>Sisa Stok:</strong> ${balance}</p>
            <p><strong>Lokasi:</strong> ${item.warehouse_name ?? '-'}</p>
          `,
          confirmButtonText: 'OK'
        });

        if (balance > 0) {
          const itemKey = item.code;
          if (!scannedItems[itemKey]) {
            scannedItems[itemKey] = {
              type: 'item', // ✅ Tambahkan type di sini
              transfer_in_code: transfer?.code ?? null,
              transfer_in_item_code: item.code ?? null,
              code: item.article_code,
              name: item.description,
             qty: parseFloat(item.balance) || 0, // ambil dari balance, bukan query selector
              uom: item.uom,
              min_package: item.min_package,
              expired_date: item.expired_date ?? '',
              from_name: item.warehouse_name ?? '-',
              destination_id: '',
              supplier_name: transfer?.supplier_name ?? null,
              balance: balance
            };
            renderItemTable();
          }
        }
      }

      // === HANDLE INVALID ===
      else {
        Swal.fire({
          icon: 'error',
          title: 'QR Tidak Valid',
          text: 'Data tidak ditemukan atau QR tidak valid.'
        });
      }
    })
    .catch(err => {
      console.error('❌ Fetch error:', err);
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Gagal memuat data.\n' + err.message
      });
    });
}



document.getElementById('transfer_type').addEventListener('change', function() {
  if (Object.keys(scannedItems).length > 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Tidak Bisa Ubah',
      text: 'Transfer Type tidak bisa diubah setelah item discan. Klik Reset jika ingin mengganti.'
    });
    this.value = currentTransferType;
    return;
  }

  currentTransferType = this.value;
  renderItemTable();
});

document.getElementById('submitBtn').addEventListener('click', function(e) {
  e.preventDefault();

  if (Object.keys(scannedItems).length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Tidak Ada Item',
      text: 'Tidak ada item yang discan!'
    });
    return;
  }

  const transferType = document.getElementById('transfer_type').value;
  const referenceNumber = document.getElementById('reference_number').value;
  const date = document.getElementById('date').value;
  const note = document.getElementById('note').value;

  const items = Object.values(scannedItems).map(item => ({
    transfer_in_code: item.transfer_in_code || null,
    transfer_in_item_code: item.transfer_in_item_code ?? null,
    transfer_in_item_id: item.transfer_in_item_id || null,
    article_code: item.code,
    description: item.name,
    qty: item.qty,
    uom: item.uom,
    min_package: item.min_package,
    expired_date: item.expired_date,
    from_location: item.from_name,
    destination: item.destination_id,
  }));

  const payload = {
    transfer_type: transferType,
    reference_number: referenceNumber,
    date: date,
    note: note,
    items: items
  };

  fetch('/ppic/logistic/transfer_out/store', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(payload)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Transfer Out berhasil disimpan! Kode: ' + data.code,
        timer: 2000,
        showConfirmButton: false
      });

      scannedItems = {};
      renderItemTable();

      document.getElementById('reference_number').value = '';
      document.getElementById('note').value = '';
      document.getElementById('barcodeInput').value = '';
      document.getElementById('searchTFIn').value = '';
      document.getElementById('searchResult').innerHTML = '<p class="text-gray-400 italic">Belum ada pencarian...</p>';
      document.getElementById('qrPreview').src = "{{ asset('img/tf-in.png') }}";

      const transferTypeSelect = document.getElementById('transfer_type');
      transferTypeSelect.disabled = false;
      transferTypeSelect.value = '';
      currentTransferType = '';
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Terjadi kesalahan.'
      });
    }
  });
});

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




  const searchInput = document.getElementById('searchTFIn');
  if (searchInput) {
    searchInput.addEventListener('input', manualSearch);
  }
 const barcodeInput = document.getElementById('barcodeInput');
  if (barcodeInput) {
    barcodeInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const code = barcodeInput.value.trim();
        if (code) {
          handleScannedCode(code);
          barcodeInput.value = '';  // kosongkan setelah input
        }
      }
    });
  }


document.getElementById('resetBtn').addEventListener('click', function(e) {
  e.preventDefault();

  // Kosongkan semua item
  scannedItems = {};
  renderItemTable();

  // Reset Transfer Type
  const transferTypeSelect = document.getElementById('transfer_type');
  transferTypeSelect.disabled = false;
  transferTypeSelect.value = '';
  currentTransferType = '';

  // Reset field lainnya
  document.getElementById('reference_number').value = '';
  document.getElementById('searchTFIn').value = '';
  document.getElementById('searchResult').innerHTML = '<p class="text-gray-400 italic">Belum ada pencarian...</p>';
  document.getElementById('qrPreview').src = "{{ asset('img/tf-in.png') }}";
});


</script>
@endpush
@endsection

