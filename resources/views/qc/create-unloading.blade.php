@extends('layouts.app')

@section('title', 'Create Unloading')
@section('page-title', 'CREATE UNLOADING')
@section('breadcrumb-item', 'Unloading Inspection')
@section('breadcrumb-active', 'Create Unloading')

@section('content')
<div class="flex gap-6">
  <!-- 📘 Sidebar Search Panel -->
  <div class="w-1/4 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Unloading Check</h2>
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Periode<small class="text-red-600"> *</small></label>
        <input type="date" name="date" id="date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
         <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Customer<small class="text-red-600"> *</small></label>
       <select name="transfer_type" id="transfer_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Pilih Customer --</option>
            <option value="Incoming">Incoming</option> <!-- Dari Supplier -->
            <option value="Material Return">Material Return</option> <!-- Dari Produksi -->
          </select>
        </div>
        <div>
        <label for="transfer_type" class="block text-sm font-medium text-gray-700 mb-1">Daily Report<small class="text-red-600"> *</small></label>
       <select name="transfer_type" id="transfer_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Pilih Type --</option>
            <option value="Incoming">Incoming</option> <!-- Dari Supplier -->
            <option value="Material Return">Material Return</option> <!-- Dari Produksi -->
          </select>
        </div>
    
  </div>

  <!-- 📦 Main Transfer Panel -->
  <div class="w-3/4 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Data Unloading</h2>
    <form id="transfer-form">

      <!-- 📋 Tabel Artikel yang Dipindahkan -->
        <h2 class="text-lg font-semibold text-gray-700">Total Inspection</h2>
      <div class="overflow-x-auto">
        <table id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="p-2 border">Item</th>
              <th class="p-2 border">1</th>
              <th class="p-2 border">5</th>
              <th class="p-2 border">7</th>
              <th class="p-2 border">9</th>
              <th class="p-2 border">15</th>
              <th class="p-2 border">27</th>
              <th class="p-2 border">Total</th>
            </tr>
          </thead>
          <tbody id="itemList">
            <tr>
              <th class="p-2 border">Total Check</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">100</th>
              <th class="p-2 border">100</th>
            </tr>
             <tr>
              <th class="p-2 border">Total OK</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">500</th>
              <th class="p-2 border">100</th>
            </tr>
             <tr>
              <th class="p-2 border">Total NG</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">200</th>
              <th class="p-2 border">200</th>
            </tr>
             <tr>
              <th class="p-2 border">Total NC</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">300</th>
              <th class="p-2 border">200</th>
            </tr>
             <tr>
              <th class="p-2 border">Total Pass Trough</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">170</th>
              <th class="p-2 border">200</th>
            </tr>

             <tr>
              <th class="p-2 border">Pass Rate</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">140</th>
              <th class="p-2 border">200</th>
            </tr>

             <tr>
              <th class="p-2 border">Pass Trough</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">50</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">40</th>
              <th class="p-2 border">80</th>
              <th class="p-2 border">200</th>
              <th class="p-2 border">200</th>
            </tr>
          </tbody>
        </table>
      </div>

       <!-- 📋 Tabel Artikel yang Dipindahkan -->
        <h2 class="text-lg font-semibold text-gray-700 mt-6">Defect</h2>
      <div class="overflow-x-auto">
        <table  id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-gray-100 text-gray-700">
              <th class="p-2 border">Defect</th>
              <th class="p-2 border">12</th>
              <th class="p-2 border">17</th>
              <th class="p-2 border">16</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">5</th>
              <th class="p-2 border">1060</th>
          </thead>
          <tbody id="itemList">
            <tr>
              <th class="p-2 border">Blister</th>
              <th class="p-2 border">12</th>
              <th class="p-2 border">17</th>
              <th class="p-2 border">16</th>
              <th class="p-2 border">10</th>
              <th class="p-2 border">5</th>
              <th class="p-2 border">1060</th>
            </tr>
          </tbody>
        </table>
      </div>

       <!-- 💰 Ringkasan Total & Pajak -->
<div class="flex justify-end mt-4">
  <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span class="font-semibold">Total OK</span>
      <span id="summary-subtotal">80%</span>
    </div>
    <div class="flex justify-between">
      <span class="font-semibold">Total NG</span>
      <span id="summary-subtotal">80%</span>
    </div>
    <div class="flex justify-between">
      <span class="font-semibold">Total NC</span>
      <span id="summary-subtotal">80%</span>
    </div>
<div class="flex justify-between">
      <span class="font-semibold">Total Pass Trough</span>
      <span id="summary-subtotal">80%</span>
    </div>
    <div class="flex justify-between">
      <span class="font-semibold">Pass Rate</span>
      <span id="summary-subtotal">80%</span>
    </div>

     <div class="flex justify-between">
      <span class="font-semibold">Pass Trough</span>
      <span id="summary-subtotal">80%</span>
    </div>
  </div>
</div>

      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start gap-2 mt-4">
        <button id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Reset</button>
        <button id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
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

</style>
@push('scripts')
<script>
// === Variabel Global ===
let scannedItems = {};
let itemIndex = 1;
let barcodeBuffer = '';
let barcodeTimer = null;
let activeSupplier = null;
const warehouseNameToId = {};
const supplierCodeToName = {}; // mapping supplier_code => supplier_name


// === Inisialisasi ===
document.addEventListener('DOMContentLoaded', function () {
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
  itemList.innerHTML = '';
  itemIndex = 1;
  for (const code in scannedItems) {
    const item = scannedItems[code];
    const row = `
      <tr>
        <td class="border p-2 text-center">${itemIndex++}</td>
        <td class="border p-2">${item.code}</td>
        <td class="border p-2">${item.name}</td>
        <td class="border p-2 text-center">
          <input type="number" min="1" value="${item.qty}" class="w-20 text-center border rounded px-2 py-1">
        </td>
        <td class="border p-2 text-center">${item.uom}</td>
        <td class="border p-2 text-center">${item.min_package}</td>
        <td class="border p-2 text-center">
          <input type="date" class="w-full border rounded px-2 py-1" />
        </td>
        <td class="border p-2 text-center">
  ${item.destination_name}
  <input type="hidden" class="destination-id-hidden" value="${item.destination_id}" />
</td>
        <td class="border p-2 text-center">
          <button type="button" onclick="removeItem('${item.code}')" class="text-red-500 hover:text-red-700 font-semibold">X</button>
        </td>
      </tr>`;
    itemList.insertAdjacentHTML('beforeend', row);
  }
}


function removeItem(code) {
  delete scannedItems[code];
  renderItemTable();
  if (Object.keys(scannedItems).length === 0) {
    activeSupplier = null;
    document.getElementById('supplier_name').value = '';
  }
}

// === Scan Handler ===
function handleScannedCode(code) {
  fetch(`/inventory/article/find/${encodeURIComponent(code)}`)
    .then(res => res.json())
    .then(data => {
      const transferType = document.getElementById('transfer_type').value;
      if (!transferType) return alert('❌ Silakan pilih Transfer Type terlebih dahulu sebelum scan!');
      if (!data.article_code) return alert('❌ Tidak ditemukan: ' + code);

     if (transferType === 'Incoming') {
  const articleSupplierName = data.supplier_name?.trim() ?? '-';
const articleSupplierCode = data.supplier_code ?? null;

  const supplierInput = document.getElementById('supplier_name');
const hiddenSupplierIdInput = document.getElementById('supplier_code');
const currentSupplier = supplierInput.value.trim();

if (!articleSupplierCode) return alert("❌ Artikel ini tidak memiliki informasi supplier.");

if (!currentSupplier) {
  supplierInput.value = articleSupplierName;
  hiddenSupplierIdInput.value = articleSupplierCode;
  activeSupplier = articleSupplierName;
} else if (articleSupplierName !== currentSupplier) {
  return alert(`⚠️ Artikel ini berasal dari supplier berbeda (${articleSupplierName}).\nHanya boleh scan dari supplier "${currentSupplier}".`);
}

}

      const item = {
        code: data.article_code,
        name: data.description,
        unit: data.unit,
        min_package: data.min_package,
        article_type: data.type_name,
        destination_id: data.destination_warehouse_id,
        destination_name: data.destination_warehouse_name,
        qty: 1,
        uom: 'PCS'
      };

      scannedItems[item.code] = scannedItems[item.code]
        ? { ...scannedItems[item.code], qty: scannedItems[item.code].qty + 1 }
        : item;

      renderItemTable();
    })
    .catch(err => {
      console.error('❌ Fetch error:', err);
      alert('❌ Gagal memuat data artikel.\n' + err.message);
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
          fetch('/api/scanner/reset', { method: 'POST' });
        }
      })
      .catch(err => console.error('Polling scanner error:', err));
  }, 2000);
}

// === Search Manual ===
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

  fetch(`/ppic/logistic/transfer_in/article-search?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      console.log("Hasil Fetch Article:", data);
      if (data.length === 0) {
        resultBox.innerHTML = '<p class="text-red-500 italic">Artikel tidak ditemukan.</p>';
        qrPreview.src = "{{ asset('img/tf-in.png') }}";
        return;
      }

      // Simpan supplier_code => supplier_name untuk lookup nanti
      data.forEach(item => {
        if (item.supplier_code) {
          supplierCodeToName[item.supplier_code] = item.supplier_name ?? item.supplier_code;
        }
      });

     resultBox.innerHTML = data.map(item => `
  <div class="border p-2 rounded hover:bg-gray-100 cursor-pointer"
       onclick="selectArticle('${item.article_code}', '${item.description}', '${item.supplier_code}', '${item.supplier_name}')">
    <div class="font-semibold">${item.article_code}</div>
    <div class="text-sm text-gray-600">${item.description}</div>
    <div class="text-sm text-gray-600">${item.supplier_name}</div>
  </div>`).join('');

    })
    .catch(err => {
      console.error(err);
      resultBox.innerHTML = '<p class="text-red-500 italic">Gagal memuat data.</p>';
      qrPreview.src = "{{ asset('img/tf-in.png') }}";
    });
}


function selectArticle(code, name, supplierCode = null, supplierName = '-') {
  const searchInput = document.getElementById('searchArticle');
  const searchResult = document.getElementById('searchResult');
  const qrPreview = document.getElementById('qrPreview');
  const qrLoader = document.getElementById('qrLoader');

  searchInput.value = code;

  searchResult.innerHTML = `<p class="text-sm">✅ Artikel dipilih:<br><strong>${code}</strong> - ${name} (${supplierName})</p>`;
  qrLoader.classList.remove('hidden');
  qrPreview.classList.add('invisible');
  qrPreview.onload = () => {
    qrLoader.classList.add('hidden');
    qrPreview.classList.remove('invisible');
  };
  qrPreview.src = `/storage/qrcodes/${code}.png`;

  // Set supplier
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
// === Submit Form dan Cetak Otomatis ===
function submitForm(e) {
  e.preventDefault();
  const supplierCode = document.getElementById('supplier_code')?.value;

  if (!supplierCode) {
    Swal.fire({
      icon: 'warning',
      title: 'Supplier Belum Dipilih',
      text: 'Supplier ID tidak ditemukan. Pastikan artikel sudah discan.'
    });
    return;
  }

  const items = [];
  document.querySelectorAll('#itemList tr').forEach(row => {
    const tds = row.querySelectorAll('td');
    const qtyInput = tds[3].querySelector('input');
    const expInput = tds[6].querySelector('input');
    const destinationInput = tds[7].querySelector('input.destination-id-hidden');
    const destinationId = destinationInput ? destinationInput.value : null;

    items.push({
      article_code: tds[1].textContent.trim(),
      description: tds[2].textContent.trim(),
      qty: qtyInput.value,
      uom: tds[4].textContent.trim(),
      min_package: tds[5].textContent.trim(),
      expired_date: expInput.value,
      destination_id: destinationId
    });
  });

  const payload = {
    reference_number: document.getElementById('reference_number').value,
    date: document.getElementById('date').value,
    transfer_category: document.getElementById('transfer_type').value,
    supplier_code: supplierCode,
    from_location: document.getElementById('from_location').value,
    note: document.getElementById('note').value,
    items: items
  };

  fetch('/ppic/logistic/transfer_in/store', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(response => {
    if (response.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Transfer In berhasil disimpan!',
        timer: 2000,
        showConfirmButton: false
      });

      printLabelsDirect(response.labels);  // Cetak otomatis

      // Reset form agar bisa input lagi tanpa reload
      resetForm();
      document.getElementById('reference_number').value = '';
      document.getElementById('date').value = '';
      document.getElementById('note').value = '';
      document.getElementById('transfer_type').value = '';
      document.getElementById('supplier_name').value = '';
      document.getElementById('supplier_code').value = '';
      document.getElementById('searchArticle').value = '';
      document.getElementById('searchResult').innerHTML = '<p class="text-gray-400 italic">Belum ada pencarian...</p>';
      document.getElementById('qrPreview').src = "{{ asset('img/tf-in.png') }}";

    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: response.message || 'Terjadi kesalahan saat menyimpan data.'
      });
    }
  })
  .catch(err => {
    console.error(err);
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: '❌ Gagal menyimpan data transfer.\n' + err.message
    });
  });
}


// === Generate dan Print Langsung ===
function printLabelsDirect(labels) {
  const html = generateLabelHTML(labels, ['qr_item', 'qr_transfer']);
  const printWindow = window.open('', '_blank');

  if (!printWindow) {
   Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Gagal membuka jendela cetak.\n' + err.message
    });
    return;
  }

  printWindow.document.open();
  printWindow.document.write(html);
  printWindow.document.close();

  printWindow.onload = () => {
    printWindow.focus();
    setTimeout(() => printWindow.print(), 500);
  };
}


// === Template HTML Label ===
function generateLabelHTML(labels, options = ['qr_item', 'qr_transfer']) {
  let html = `<html><head><title>Cetak Label</title>
    <style>
      body { font-family: Arial; padding: 0px; margin:0px; }
      .label-container { width: 48mm; height: 48mm; page-break-after: always; border: 1px solid #ccc; padding: 4mm; text-align: center; box-sizing: border-box; }
      .label-container img { width: 30mm; height: 30mm; }
      .label-container div { font-size: 8pt; margin-top: 3mm; }
      @page {
    size: 48mm 48mm; /* atau 100mm 50mm tergantung label */
    margin: 0;
  }
    </style>
  </head><body>`;

  labels.forEach(label => {
    if (label.type === 'qr_item' && options.includes('qr_item')) {
      html += `<div class="label-container">
        <img src="${label.qr_path}" />
        <div>${label.code}</div>
      </div>`;
    }

    if (label.type === 'qr_transfer' && options.includes('qr_transfer')) {
      html += `<div class="label-container">
        <img src="${label.qr_path}" />
        <div>${label.reference_number}</div>
      </div>`;
    }
  });

  html += `</body></html>`;
  return html;
}





</script>
@endpush
@endsection

