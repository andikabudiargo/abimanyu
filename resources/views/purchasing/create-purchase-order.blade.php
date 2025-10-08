@extends('layouts.app')

@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')
@section('breadcrumb-item', 'Purchase Order')
@section('breadcrumb-active', 'Create Purchase Order')

@section('content')
<div class="w-full bg-white shadow rounded-xl mb-4">
  <!-- Accordion Header -->
  <button id="toggleAccordion" class="w-full text-left px-6 py-4 text-lg font-semibold flex justify-between text-gray-600 items-center">
    Upload Multiple Purchase Order
    <svg id="accordionIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  <!-- Accordion Content -->
  <div id="accordionContent" class="px-6 pb-6 hidden">
    @if(session('success'))
    <div class="text-green-600 mt-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="text-red-600 mt-2">{{ session('error') }}</div>
    @endif

    <div class="bg-gray-50 border border-dashed border-gray-300 p-4 rounded mt-4">
      <h3 class="text-lg font-medium mb-2">Upload via Excel (.xlsx)</h3>
      <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4">
        @csrf
        <input type="file" name="csv_file" accept=".xlsx" class="flex-1 border border-gray-300 rounded p-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Upload</button>
      </form>
      <p class="text-sm text-gray-500 mt-2">
        Download template Excel: <a href="#" class="text-blue-600 underline" download>Download Template</a>
      </p>
    </div>
  </div>
</div>


<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New Purchase Order</h2>
    <form id="po-form" enctype="multipart/form-data">
      @csrf
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Order Number<small class="text-red-600"> *</small></label>
        <input type="text" name="order_number" id="reference_number"
               class="w-full px-3 py-2 bg-gray-200 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Automatic" readonly />
        </div>
         <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Order Date<small class="text-red-600"> *</small></label>
        <input type="date" name="order_date" id="order_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required  value="{{ date('Y-m-d') }}" />
        </div>
         <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date<small class="text-red-600"> *</small></label>
        <input type="date" name="delivery_date" id="delivery_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
         <div id="supplierWrapper">
          <label for="supplier_code" class="block text-sm font-medium text-gray-700 mb-1">Supplier <small class="text-red-600"> *</small></label>
          <select id="supplierSelect" class="form-control w-full">
    <option value="">-- Choose Supplier --</option>
    @foreach($suppliers as $supplier)
        <option value="{{ $supplier->code }}">{{ $supplier->code }} - {{ $supplier->name }}</option>
    @endforeach
</select>
       <input type="hidden" id="supplier_code" name="supplier_code">
        </div>
         <div>
  <label for="top" class="block text-sm font-medium text-gray-700 mb-1">Term of Payment (TOP)</label>
  <div class="relative">
    <input type="number" name="top" id="top"
           class="w-full px-3 py-2 pr-16 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
      Days
    </div>
  </div>
</div>
 <div class="flex items-center h-full pt-6">
        <input type="checkbox" name="pkp" id="pkp" value="1" checked class="form-checkbox text-indigo-600 mr-2">
        <label for="pkp" class="text-sm text-gray-700">Taxable Person (PKP)</label>
        </div>
      </div>
      <div class="mb-12">
        <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
        <textarea id="note" name="note" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
</div>

<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Purchase Order Item</h2>
      <!-- 🔢 Nomor Referensi -->
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="purchase_request_id" class="block text-sm font-medium text-gray-700 mb-1">
  Purchase Request<small class="text-red-600"> *</small>
</label>

<!-- Dropdown Container -->
<div class="relative" id="prDropdownWrapper">
  <button type="button" id="prDropdownToggle" class="w-full border border-gray-300 rounded p-2 bg-white flex justify-start gap-2 items-center">
    <div id="prDropdownLabel" class="flex flex-wrap gap-2">
      <span class="text-gray-400">-- Choose Purchase Request --</span>
    </div>
    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
      <path d="M19 9l-7 7-7-7"></path>
    </svg>
  </button>

  <!-- Dropdown Menu -->
  <div id="prDropdownMenu" class="absolute z-10 bg-white w-full border border-gray-300 rounded mt-1 shadow max-h-64 overflow-y-auto hidden">
    <div class="p-2 sticky top-0 bg-white border-b border-gray-200 z-10">
      <input type="text" id="prSearchInput" placeholder="Search PR..." class="w-full border px-2 py-1 rounded">
    </div>
    <div id="prCheckboxList" class="max-h-48 overflow-y-auto">
      <!-- Checkboxes injected via JS -->
    </div>
  </div>
</div>






        </div>
      
</div>
<hr>
      

      <!-- 📋 Tabel Artikel yang Dipindahkan -->
      <div class="overflow-x-auto">
        <table  id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-blue-500 text-white">
            <tr>
           <th class="p-2 border w-40 text-center">Purchase Request Number</th>
<th class="p-2 border w-64 text-left">Article</th>
<th class="p-2 border w-24 text-center">Current Stock</th>
<th class="p-2 border w-20 text-center">Qty</th>
<th class="p-2 border w-32 text-right">Price</th>
<th class="p-2 border w-36 text-right">Total</th>
<th class="p-2 border w-20 text-center">Action</th>

            </tr>
          </thead>
          <tbody id="itemList">
            <!-- Diisi via JavaScript -->
          </tbody>
        </table>
      </div>

      <hr>

  <!-- 💰 Ringkasan Total & Pajak -->
<div class="flex justify-end mt-4">
  <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span class="font-semibold">Subtotal</span>
      <span id="summary-subtotal">Rp 0</span>
    </div>

    <!-- Discount Manual -->
    <div class="flex justify-between items-center">
      <label for="discount" class="font-semibold">Discount (Rp)</label>
      <input type="number" id="discount" value="0"
             class="w-24 px-2 py-1 border border-gray-300 rounded text-right focus:ring-indigo-500 focus:border-indigo-500" />
    </div>

    <!-- PPN -->
    <div class="flex justify-between items-center">
      <label class="flex items-center gap-2 font-semibold">
        <input type="checkbox" id="use_ppn" checked class="w-4 h-4" />
        PPN 10%
      </label>
      <span id="summary-ppn">Rp 0</span>
    </div>

    <!-- PPh -->
    <div class="flex justify-between items-center">
      <label class="flex items-center gap-2 font-semibold">
        <input type="checkbox" id="use_pph" class="w-4 h-4" />
        PPh 2%
      </label>
      <span id="summary-pph">Rp 0</span>
    </div>

    <hr class="border-gray-400">

    <!-- Netto -->
    <div class="flex justify-between text-lg font-bold text-green-700">
      <span>Netto</span>
      <span id="summary-netto">Rp 0</span>
    </div>
  </div>
</div>

 <!-- Hidden Inputs -->
    <input type="hidden" name="subtotal" id="input-subtotal" value="0">
    <input type="hidden" name="ppn" id="input-ppn" value="0">
    <input type="hidden" name="pph" id="input-pph" value="0">
    <input type="hidden" name="netto" id="input-netto" value="0">

      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start gap-2 mt-4">
        <a href="" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Back</a>
        <button id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal -->
<div id="priceHistoryModal" class="hidden fixed inset-0 z-50 bg-gray-800 bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-4 w-full max-w-3xl">
     <div class="flex justify-between items-center mb-2">
     <h2 class="text-xl font-semibold mb-2">Price Comparison</h2>
  <button id="closeHistoryModal" class="text-red-500 hover:text-red-800">x</button>
    </div>
    <hr class="mb-2"><b>Article :</b>
  <span id="articleNameInModal" class="text-sm text-gray-600"></span>
    <div class="overflow-x-auto mt-2">
      <table class="min-w-full text-sm border">
        <thead class="bg-blue-500 text-white">
          <tr>
            <th class="border px-3 py-2 text-center">No.</th>
            <th class="border px-3 py-2">Purchase Order Number</th>
            <th class="border px-3 py-2">Supplier</th>
            <th class="border px-3 py-2">Order Date</th>
            <th class="border px-3 py-2 text-center">Price</th>
          </tr>
        </thead>
        <tbody id="priceHistoryBody"></tbody>
      </table>
    </div>
  </div>
</div>


<style>
    input::placeholder {
  font-size: 10px;
  color: #9ca3af; /* Tailwind gray-400 */
}
/* Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}

/* Samakan tinggi dan padding select2 dengan input lainnya */
.select2-container .select2-selection--single {
  height: 40px !important;
  padding: 6px 12px;
  border: 1px solid #d1d5db; /* Tailwind: border-gray-300 */
  border-radius: 0.375rem; /* Tailwind: rounded */
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); /* Tailwind: shadow-sm */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 26px;
  padding-left: 0;
  color: #374151; /* Tailwind: text-gray-700 */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 36px;
  right: 10px;
}


</style>
<script>
document.addEventListener('DOMContentLoaded', function () {


$('#submitBtn').on('click', function (e) {
  e.preventDefault();

    updateSummary(); // <-- penting!


  const formData = new FormData();

  // Data utama
  formData.append('_token', $('input[name="_token"]').val());
  formData.append('order_number', $('#reference_number').val());
  formData.append('order_date', $('#order_date').first().val());
  formData.append('delivery_date', $('#delivery_date').last().val());
  formData.append('supplier_code', $('#supplier_code').val());
  formData.append('top', $('#top').val() || '');
  formData.append('pkp', $('#pkp').is(':checked') ? 1 : 0);
  formData.append('note', $('#note').val());

  document.querySelectorAll('tr[data-prid]').forEach(function (row) {
  const prId = row.dataset.prid; // sesuai atribut data-prid
  const article = row.querySelector('input[name="article_code[]"]').value;
  const qty = row.querySelector('input[name="qty[]"]').value;
  const price = row.querySelector('input[name="price[]"]').value;
  const uom = row.querySelector('input[name="uom[]"]').value;

  formData.append('purchase_request_id[]', prId);
  formData.append('article_code[]', article);
  formData.append('qty[]', qty);
  formData.append('price[]', price);
  formData.append('uom[]', uom);
});


  // Ringkasan
  
  formData.append('discount', $('#discount').val() || 0);
  formData.append('use_ppn', $('#use_ppn').is(':checked') ? 1 : 0);
  formData.append('use_pph', $('#use_pph').is(':checked') ? 1 : 0);

  formData.append('subtotal', $('#input-subtotal').val() || 0);
  formData.append('ppn', $('#input-ppn').val() || 0);
  formData.append('pph', $('#input-pph').val() || 0);
  formData.append('netto', $('#input-netto').val() || 0);


  // Kirim via AJAX jQuery
  $.ajax({
    url: '/purchasing/purchase-order/store',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      alert('Purchase Order berhasil disimpan!');
      window.location.href = '/purchasing/purchase-order/index';
    },
    error: function (xhr) {
      console.error(xhr);
      if (xhr.status === 422) {
        alert('Validasi gagal. Pastikan semua data sudah benar.');
      } else {
        alert('Terjadi kesalahan saat menyimpan Purchase Order.');
      }
    }
  });
});


  

    const supplierSelect = document.getElementById('supplierSelect');
    const prDropdownMenu = document.getElementById('prDropdownMenu');
    const prDropdownToggle = document.getElementById('prDropdownToggle');
    const prDropdownLabel = document.getElementById('prDropdownLabel');
    const prCheckboxList = document.getElementById('prCheckboxList');
    const prSearchInput = document.getElementById('prSearchInput');
    const supplierHidden = document.getElementById('supplier_code');
    const itemList = document.getElementById('itemList');
    const addedPRs = new Set();
    
   function formatNumberWithThousands(numStr) {
  const parts = numStr.replace(/[^\d,]/g, '').split(',');
  let intPart = parts[0];
  const decimalPart = parts[1] || '';

  intPart = intPart.replace(/^0+(?=\d)/, ''); // remove leading zeros
  const formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  return decimalPart !== ''
    ? `${formattedInt},${decimalPart}`
    : formattedInt;
}

function formatOnBlur(value) {
  const num = parseFloat(value.replaceAll('.', '').replace(',', '.'));
  if (isNaN(num)) return '';
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(num);
}

function parseToRawNumber(value) {
  return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
}

    // Toggle Dropdown
    prDropdownToggle.addEventListener('click', () => {
        prDropdownMenu.classList.toggle('hidden');
    });

    // Klik luar = close
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#prDropdownWrapper')) {
            prDropdownMenu.classList.add('hidden');
        }
    });

   $(document).ready(function () {
    $('#supplierSelect').select2({
        placeholder: "-- Choose Supplier --",
        width: '100%',
        allowClear: true
    });

    $('#prSelect').select2({
  placeholder: "-- Choose Purchase Request --",
  width: '100%',
  closeOnSelect: false,
  templateSelection: function (data) {
    if (data.id === "") return "-- Choose Purchase Request --";
    return $('<span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-1 px-2.5 py-0.5 rounded">' + data.text + '</span>');
  }
});

    // Bind Select2 change event
    $('#supplierSelect').on('change', function () {
        const supplierCode = this.value;
        $('#supplier_code').val(supplierCode);
        prDropdownMenu.innerHTML = '';
        prDropdownLabel.textContent = '-- Choose Purchase Request --';
        itemList.innerHTML = '';
        addedPRs.clear();
        updateSummary();

        if (!supplierCode) return;

        fetch(`/purchasing/purchase-requests/by-supplier/${supplierCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    prDropdownMenu.innerHTML = `<div class="px-4 py-2 text-gray-500 text-sm">No Purchase Request Found</div>`;
                } else {
                    // Simpan data PR di variabel global agar bisa di-filter
window.allPRData = data;

// Render dropdown dengan search + scrollable
prDropdownMenu.innerHTML = `
  <div class="p-2 sticky top-0 bg-white z-10 border-b">
    <input type="text" id="prSearchInput" placeholder="Search PR..." class="w-full border px-2 py-1 rounded text-sm" />
  </div>
  <div id="prCheckboxList" class="max-h-52 overflow-y-auto">
    ${renderPRCheckboxList(data)}
  </div>
`;

// Bind search
document.getElementById('prSearchInput').addEventListener('input', function () {
  const keyword = this.value.toLowerCase();
  const filtered = window.allPRData.filter(pr => pr.request_number.toLowerCase().includes(keyword));
  document.getElementById('prCheckboxList').innerHTML = renderPRCheckboxList(filtered);
  bindPRCheckboxes(); // rebind setelah render ulang
});

bindPRCheckboxes();

                }
            });
    });
});

function renderPRCheckboxList(prArray) {
  return prArray.map(pr => `
    <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm">
        <input type="checkbox" value="${pr.id}" data-number="${pr.request_number}" class="pr-checkbox mr-2">
        ${pr.request_number}
    </label>
  `).join('');
}

    function bindPRCheckboxes() {
    const checkboxes = prDropdownMenu.querySelectorAll('.pr-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const selected = Array.from(checkboxes)
                .filter(c => c.checked)
                .map(c => ({
                    id: c.value,
                    number: c.dataset.number
                }));

            // Update label di dropdown
          if (selected.length > 0) {
    prDropdownLabel.innerHTML = selected.map(s =>
        `<span class="inline-block bg-blue-600 text-white text-xs rounded-full px-2 py-0.5 mr-1">${s.number}</span>`
    ).join('');
} else {
    prDropdownLabel.innerHTML = '-- Choose Purchase Request --';
}


            const checkedIds = selected.map(s => s.id);
            const newlyCheckedIds = checkedIds.filter(id => !addedPRs.has(id));
            const uncheckedIds = Array.from(checkboxes)
                .filter(c => !c.checked)
                .map(c => c.value);

            // ❌ Hapus baris dari itemList yang tidak dicentang
            uncheckedIds.forEach(id => {
                const rows = itemList.querySelectorAll(`tr[data-prid="${id}"]`);
                rows.forEach(row => row.remove());
                addedPRs.delete(id);
            });

            // ✅ Fetch hanya untuk ID yang baru ditambahkan
            if (newlyCheckedIds.length === 0) {
                bindCalculation();
                updateSummary();
                return;
            }

            fetch(`/purchasing/purchase-request-items/by-ids?ids=${newlyCheckedIds.join(',')}&supplier_code=${supplierHidden.value}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
    // Cegah duplikat artikel berdasarkan kode artikel
    const existingRow = itemList.querySelector(`tr[data-prid="${item.purchase_request_id}"] input[name="article_code[]"][value="${item.article_code}"]`);
if (existingRow) {
    return;
}

const index = document.querySelectorAll('.price-display').length;
    const row = document.createElement('tr');
    row.setAttribute('data-prid', item.purchase_request_id);
    row.innerHTML = `
        <td class="p-2 border">${item.request_number}</td>
        <td class="p-2 border max-w-xs truncate">
        <!-- Tambahkan hidden input untuk article_code dan purchase_request_id -->
        <input type="hidden" name="purchase_request_id[]" value="${item.purchase_request_id}">
        <input type="hidden" name="article_code[]" value="${item.article_code}">
        ${item.article_code} - ${item.article_description}
    </td>
        <td class="p-2 border text-center">-</td>
        <td class="p-2 border text-center">
        <div class="relative w-full">
  <input type="number" name="qty[]" value="${item.qty}" 
         class="qty-input w-full border rounded p-2 pr-16 text-center" />
  <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">
    ${item.uom}
  </span>
  <input type="hidden" name="uom[]" value="${item.uom}">
</div>
        </td>
        <td class="p-2 border">
         <div class="flex items-center space-x-2">
        <input type="text" 
       name="price_display[]" 
       class="price-display w-full border rounded p-1 text-right" 
       inputmode="decimal" 
       autocomplete="off" />

<input type="hidden" 
       name="price[]" 
       class="price-input" />
        <!-- Tombol History Price -->
    <button type="button" 
            class="history-btn text-yellow-500" 
            data-article="${item.article_code}" 
            data-name="${item.article_description}" 
            data-row-index="${index}" 
            title="Lihat histori harga">
      <i data-feather="alert-circle"></i>
    </button>
    </div>
</td>
        <td class="p-2 border total-cell text-right">Rp 0</td>
        <td class="p-2 border text-center">
            <button type="button" class="remove-row text-red-500 px-2 py-1 rounded"><i data-feather="trash-2"></i></button>
        </td>
    `;
    itemList.appendChild(row);
    feather.replace();
    //addedPRs.add(item.purchase_request_id);
     const priceInput = row.querySelector('.price-display');
  const priceHidden = row.querySelector('.price-input');
  bindSinglePriceFormat(priceInput, priceHidden, index);
  // Ambil harga terakhir dari server berdasarkan article_code
  const priceDisplayInput = row.querySelector('.price-display');
const priceHiddenInput = row.querySelector('.price-input');
fetch(`/purchasing/last-price/${item.article_code}`)
  .then(res => res.json())
  .then(data => {
    if (data.price !== null) {
      const price = parseFloat(data.price);
      
      // Format harga dan set ke input display
      priceDisplayInput.value = new Intl.NumberFormat('id-ID').format(price);
      priceHiddenInput.value = price;

      // Trigger kalkulasi ulang total row
      const index = document.querySelectorAll('.price-display').length - 1;
      updateTotal(index);
      updateSummary();
    }
  });
});
                    bindCalculation();
                   
                   // Panggil updateTotal agar total langsung muncul
requestAnimationFrame(() => {
  document.querySelectorAll('.qty-input').forEach((_, i) => updateTotal(i));
});
                    updateSummary();
                });
        });
    });
}
// Ambil elemen input price




function bindPriceFormat() {
  document.querySelectorAll('.price-display').forEach((input, index) => {
    const hidden = document.querySelectorAll('.price-input')[index];

    // Input realtime: hanya ribuan
    input.addEventListener('input', () => {
      const cursorPos = input.selectionStart;
      const originalLength = input.value.length;

      const formatted = formatNumberWithThousands(input.value);
      input.value = formatted;

      // Update cursor position agar tidak loncat
      const newLength = formatted.length;
      const diff = newLength - originalLength;
      input.setSelectionRange(cursorPos + diff, cursorPos + diff);

      hidden.value = parseToRawNumber(input.value).toFixed(2);

       // ✅ Panggil updateTotal juga di sini
  updateTotal(index);
  updateSummary();
    });

    // Blur: tambahkan koma dua digit desimal
    input.addEventListener('blur', () => {
      const formatted = formatOnBlur(input.value);
      input.value = formatted;
    });

    // Jika hidden sudah punya value saat load
    if (hidden?.value) {
      input.value = formatOnBlur(hidden.value);
    }
  });
}

function bindSinglePriceFormat(input, hidden, index) {
  input.addEventListener('input', () => {
    const cursorPos = input.selectionStart;
    const originalLength = input.value.length;

    const formatted = formatNumberWithThousands(input.value);
    input.value = formatted;

    const newLength = formatted.length;
    const diff = newLength - originalLength;
    input.setSelectionRange(cursorPos + diff, cursorPos + diff);

    hidden.value = parseToRawNumber(input.value).toFixed(2);
    updateTotal(index);
    updateSummary();
  });

  input.addEventListener('blur', () => {
    const formatted = formatOnBlur(input.value);
    input.value = formatted;
  });

  // Tampilkan ulang dari hidden jika ada
  if (hidden?.value) {
    input.value = formatOnBlur(hidden.value);
  }
}

    function bindCalculation() {
        const qtyInputs = document.querySelectorAll('.qty-input');
         const priceInputs = document.querySelectorAll('.price-display'); // ✅

        qtyInputs.forEach((input, index) => {
            input.addEventListener('input', () => updateTotal(index));
        });
        priceInputs.forEach((input, index) => {
            input.addEventListener('input', () => updateTotal(index));
        });
    }

   function updateTotal(index) {
  const qtyInput = document.querySelectorAll('.qty-input')[index];
  const priceInput = document.querySelectorAll('.price-input')[index];
  const totalCell = document.querySelectorAll('.total-cell')[index];

  const qty = parseFloat(qtyInput.value) || 0;
const price = parseFloat(priceInput.value) || 0;
  const total = qty * price;

  if (totalCell) {
    totalCell.textContent = 'Rp ' + formatOnBlur(total.toString());
  }
}



   function updateSummary() {
    let total = 0;
    document.querySelectorAll('.total-cell').forEach(cell => {
        const number = parseFloat(
    cell.innerText.replace(/\./g, '').replace(',', '.').replace(/[^0-9.]/g, '')
) || 0;

        total += number;
    });

    const discount = parseFloat(document.getElementById('discount')?.value || 0);
    const ppn = document.getElementById('use_ppn')?.checked ? 0.10 * total : 0;
    const pph = document.getElementById('use_pph')?.checked ? 0.02 * total : 0;
    const netto = total + ppn - pph - discount;

    // Tampilkan ke summary table
    document.getElementById('summary-subtotal').innerText = `Rp ${total.toLocaleString()}`;
    document.getElementById('summary-ppn').innerText = `Rp ${ppn.toLocaleString()}`;
    document.getElementById('summary-pph').innerText = `Rp ${pph.toLocaleString()}`;
    document.getElementById('summary-netto').innerText = `Rp ${netto.toLocaleString()}`;

    // Set ke input hidden
     document.getElementById('input-subtotal').value = total.toFixed(2);
    document.getElementById('input-ppn').value = ppn.toFixed(2);
    document.getElementById('input-pph').value = pph.toFixed(2);
    document.getElementById('input-netto').value = netto.toFixed(2);

    console.log('subtotal:', total);
console.log('ppn:', ppn);
console.log('pph:', pph);
console.log('netto:', netto);

}

itemList.addEventListener('click', function (e) {
    const removeButton = e.target.closest('.remove-row');
    if (removeButton) {
        const row = removeButton.closest('tr');
        const prId = row.getAttribute('data-prid');
        addedPRs.delete(prId);

        const cb = prDropdownMenu.querySelector(`input[value="${prId}"]`);
        if (cb) cb.checked = false;

        row.remove();
        updateSummary();

        const selected = Array.from(prDropdownMenu.querySelectorAll('.pr-checkbox:checked'))
            .map(c => `#${c.dataset.number}`);
        prDropdownLabel.textContent = selected.length ? selected.join(', ') : '-- Choose Purchase Request --';
    }
});


// Trigger update jika ada perubahan diskon / ppn / pph
document.getElementById('discount')?.addEventListener('input', updateSummary);
document.getElementById('use_ppn')?.addEventListener('change', updateSummary);
document.getElementById('use_pph')?.addEventListener('change', updateSummary);

// Inisialisasi awal
updateSummary();


    itemList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('tr');
            const prId = row.getAttribute('data-prid');
            addedPRs.delete(prId);

            // Uncheck PR in dropdown
            const cb = prDropdownMenu.querySelector(`input[value="${prId}"]`);
            if (cb) cb.checked = false;

            row.remove();
            updateSummary();

            // Update label
            const selected = Array.from(prDropdownMenu.querySelectorAll('.pr-checkbox:checked'))
                .map(c => `#${c.dataset.number}`);
            prDropdownLabel.textContent = selected.length ? selected.join(', ') : '-- Choose Purchase Request --';
        }
    });



 document.addEventListener('click', function (e) {
  const button = e.target.closest('.history-btn');
  if (!button) return;

  const articleCode = button.dataset.article;
  const articleName = button.dataset.name;
  const rowIndex = button.dataset.rowIndex;

  fetch(`/purchasing/price-history/${articleCode}`)
    .then(res => res.json())
    .then(data => {
    document.getElementById('articleNameInModal').textContent = `${articleCode} - ${articleName}`;
      document.getElementById('priceHistoryBody').innerHTML = '';

      data.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="border px-3 py-2 text-center">${index + 1}</td>
          <td class="border px-3 py-2">${item.order_number}</td>
          <td class="border px-3 py-2">${item.supplier_name}</td>
          <td class="border px-3 py-2">${item.order_date}</td>
          <td class="border px-3 py-2 text-center">
            <a href="#" 
               class="text-blue-600 font-semibold pick-price" 
               data-price="${item.price}" 
               data-index="${rowIndex}">
               <span class="border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white px-2 py-1 rounded">
      ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.price)}
    </span>
            </a>
          </td>
        `;
        document.getElementById('priceHistoryBody').appendChild(tr);
      });

      document.getElementById('priceHistoryModal').classList.remove('hidden');
    });

  // Tutup modal
  document.getElementById('closeHistoryModal')?.addEventListener('click', function () {
    document.getElementById('priceHistoryModal').classList.add('hidden');
  });

  // Klik harga di modal
  document.getElementById('priceHistoryBody')?.addEventListener('click', function (e) {
    const link = e.target.closest('.pick-price');
    if (!link) return;

    e.preventDefault();
    const price = link.dataset.price;
    const index = link.dataset.index;

    const priceInput = document.querySelector(`input[name="price[]"][data-index="${index}"]`);
    const display = document.querySelector(`.price-display[data-index="${index}"]`);

    if (priceInput && display) {
      priceInput.value = price;
      display.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
    }

    document.getElementById('priceHistoryModal').classList.add('hidden');

    if (typeof updateTotal === 'function') updateTotal();
    if (typeof updateSummary === 'function') updateSummary();
  });
});

    document.getElementById('discount')?.addEventListener('input', updateSummary);
    document.getElementById('use_ppn')?.addEventListener('change', updateSummary);
    document.getElementById('use_pph')?.addEventListener('change', updateSummary);
});
</script>

@endsection

