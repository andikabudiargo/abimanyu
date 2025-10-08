@extends('layouts.app')

@section('title', 'Create New Purchase Order')
@section('page-title', 'Create New Purchase Order')
@section('breadcrumb-item', 'Purchase Order')
@section('breadcrumb-active', 'Create New Purchase Order')

@section('content')
<div class="grid grid-cols-3 gap-4 mb-4">
  
  <!-- Kolom kiri: Form Create PO -->
  <div class="col-span-2 bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Purchase Order Form</h2>
    
    <form id="po-form" enctype="multipart/form-data">
      @csrf

      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Order Date<small class="text-red-600"> *</small></label>
          <input type="date" name="order_date" id="order_date"
            class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required value="{{ date('Y-m-d') }}" />
        </div>

        <div>
          <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Delivery Date<small class="text-red-600"> *</small></label>
          <input type="date" name="delivery_date" id="delivery_date"
       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
       required
       min="{{ date('Y-m-d') }}" />

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
          <input type="checkbox" name="pkp" id="pkp" value="1" checked
            class="form-checkbox text-indigo-600 mr-2">
          <label for="pkp" class="text-sm text-gray-700">Taxable Person (PKP)</label>
        </div>
      </div>

      <div class="mb-4">
        <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
        <textarea id="note" name="note" rows="2"
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
    </form>
  </div>

  <!-- Kolom kanan: Waiting List Supplier -->
  <div class=" bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">List Purchase Request</h2>
    <div id="supplierAccordion" class="overflow-y-auto max-h-[600px]">
        <!-- Accordion supplier akan di-generate di sini -->
    </div>
</div>
</div>



<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
  <div class="flex items-center justify-between mb-4">
    <!-- Kiri -->
    <h2 class="text-lg font-semibold text-gray-700">Purchase Order Item</h2>
    
    <!-- Kanan -->
    <div id="selectedSupplierDisplay" 
         class="flex items-center gap-4 font-semibold text-lg text-gray-700">
      <span id="supplierNameDisplay">-</span>
      <i data-feather="truck"></i>
    </div>
  </div>


      <!-- 📋 Tabel Artikel yang Dipindahkan -->
      <div class="overflow-x-auto">
        <table  id="itemTable" class="min-w-full bg-white">
          <thead class="bg-blue-500 text-white border-separate border-spacing-0 rounded-lg">
            <tr>
           <th class="p-2 border w-40 text-center">Purchase Request Number</th>
<th class="p-2 border w-64 text-left">Article</th>
<th class="p-2 border w-24 text-center">Current Stock</th>
<th class="p-2 border w-20 text-center">Qty</th>
<th class="p-2 border w-32 text-center">Price</th>
<th class="p-2 border w-36 text-center">Total</th>
<th class="p-2 border w-20 text-center">Action</th>

            </tr>
          </thead>
          <tbody id="itemList">
            <!-- Diisi via JavaScript -->
          </tbody>
        </table>
      </div>

  <!-- 💰 Ringkasan Total & Pajak -->
<div class="flex justify-end mt-4">
  <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span class="font-semibold">Bruto</span>
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
      <div class="flex justify-start gap-2 mt-4 border-t pt-6">
        <a href="" class="text-center w-24 bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Back</a>
        <button id="submitBtn" class="w-24 bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal -->
<div id="priceHistoryModal" class="hidden fixed inset-0 z-50 bg-gray-800 bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-4 w-full max-w-3xl">
     <div class="flex justify-between items-center mb-2">
     <h2 class="text-xl font-semibold mb-2">PRICE COMPARISON</h2>
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

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Hilangkan border samping */
#itemTable td,
#itemTable th {
  border-left: none !important;
  border-right: none !important;
}

/* Tetap ada border bawah dan atas untuk baris */
#itemTable tr {
  border-bottom: 1px solid #ccc; /* misal garis bawah */
}

#itemTable tbody tr:nth-child(odd) {
  background-color: white;
}

#itemTable tbody tr:nth-child(even) {
  background-color: #f3f4f6; /* warna abu-abu terang */
}
</style>
<script>
  let selectedSupplier = null;
  let supplierNames = {}; // key = supplierCode, value = supplierName

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

  // Kirim supplier_code dari selectedSupplier
  formData.append('supplier_code', selectedSupplier || '');

  formData.append('top', $('#top').val() || '');
  formData.append('pkp', $('#pkp').is(':checked') ? 1 : 0);
  formData.append('note', $('#note').val());

  // Data item PR
  document.querySelectorAll('tr[data-prid]').forEach(function (row) {
    console.log(row);
    const prId = row.dataset.prid; 
    const article = row.querySelector('input[name="article_code[]"]').value;
    const qty = row.querySelector('input[name="qty[]"]').value;
    const price = row.querySelector('input[name="price[]"]').value;

    formData.append('purchase_request_id[]', prId);
    formData.append('article_code[]', article);
    formData.append('qty[]', qty);
    formData.append('price[]', price);
  });

  // Ringkasan
  formData.append('discount', $('#discount').val() || 0);
  formData.append('use_ppn', $('#use_ppn').is(':checked') ? 1 : 0);
  formData.append('use_pph', $('#use_pph').is(':checked') ? 1 : 0);
  formData.append('subtotal', $('#input-subtotal').val() || 0);
  formData.append('ppn', $('#input-ppn').val() || 0);
  formData.append('pph', $('#input-pph').val() || 0);
  formData.append('netto', $('#input-netto').val() || 0);

  // Kirim via AJAX
  $.ajax({
    url: '/purchasing/purchase-order/store',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Purchase Order succesfully saved!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });
      setTimeout(() => {
        window.location.href = '/purchasing/purchase-order/index';
      }, 1500);
    },
    error: function (xhr) {
      console.error(xhr);
      if (xhr.status === 422) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Validasi gagal. Pastikan semua data sudah benar.',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Terjadi kesalahan saat menyimpan Purchase Order.',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      }
    }
  });
});




    
});
// Simpan status terbuka
let openSuppliers = [];

$(document).on('click', '.accordion-header', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const supplierId = $(this).data('supplier'); // nanti tambahkan data-supplier di HTML
    const $content = $(this).next('.accordion-content');
    $content.slideToggle(200);
    $(this).find('.accordion-icon').toggleClass('rotate-180');

    if ($content.is(':visible')) {
        openSuppliers.push(supplierId);
    } else {
        openSuppliers = openSuppliers.filter(id => id !== supplierId);
    }
});

let prItemDetails = {}; // Simpan semua item details per purchase_item_id

function loadSuppliers() {
  supplierNames = {}; // reset mapping
  prItemDetails = {}; // reset detail item

  $.getJSON('/purchasing/purchase-order/supplier', function(data) {
    const $accordion = $('#supplierAccordion').empty();

    if ($.isEmptyObject(data)) {
      $accordion.html('<p class="text-gray-500 text-sm">Tidak ada PR yang menunggu PO.</p>');
      return;
    }

    // Data dari server diasumsikan objek: { supplierCode: [arrayPRs] }
    $.each(data, function(supplierCode, prArray) {
      // Ambil nama supplier dari PR pertama, jika tidak ada pakai kode supplier
      const supplierName = prArray[0]?.supplier_name || supplierCode;
      supplierNames[supplierCode] = supplierName;

      // Group PR berdasarkan request_number
      const groupedPR = {};

      prArray.forEach(pr => {
        if (!pr.pr_item_id) {
          console.warn('pr_item_id kosong untuk:', pr);
          return;
        }

        // Simpan detail item lengkap per pr_item_id
        prItemDetails[pr.pr_item_id] = {
          supplier_code: supplierCode,
          request_number: pr.request_number,
          article_code: pr.article_code,
          article_name: pr.article_name,
          uom: pr.uom,
          current_stock: pr.current_stock || 0,
          qty_requested: pr.qty,
          qty_remaining: pr.remaining_qty,
          price: pr.price,
          total: pr.price * pr.qty
        };

        // Group per request_number
        if (!groupedPR[pr.request_number]) {
          groupedPR[pr.request_number] = {
            request_number: pr.request_number,
            date: pr.date,
            purchase_item_ids: []
          };
        }
        groupedPR[pr.request_number].purchase_item_ids.push(pr.pr_item_id);
      });

      const prCount = Object.keys(groupedPR).length;

      // Buat list checkbox PR per groupedPR
      const prItemsHtml = Object.values(groupedPR).map(prGroup => `
        <li class="flex items-center space-x-2 py-1 border-b last:border-0">
          <input type="checkbox" class="pr-checkbox" 
                 value="${prGroup.purchase_item_ids.join(',')}" 
                 data-request-number="${prGroup.request_number}">
          <span class="text-sm text-gray-700">${prGroup.request_number} (${prGroup.date})</span>
        </li>
      `).join('');

      // Accordion per supplier
      const accordionItem = `
        <div class="border border-gray-200 rounded mb-2">
          <button type="button" data-supplier="${supplierCode}" 
                  class="w-full flex justify-between items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 accordion-header">
            <span class="font-medium text-gray-800">
              ${supplierName} 
              <span class="text-sm text-gray-500">(${prCount} PR)</span>
            </span>
            <svg class="w-4 h-4 transform transition-transform accordion-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="accordion-content hidden px-4 pb-2">
            <ul>${prItemsHtml}</ul>
          </div>
        </div>
      `;

      $accordion.append(accordionItem);
    });
  });
}



function updateItemTable() {
    // Kumpulkan semua purchase_item_id dari checkbox yang dicentang
    let selectedIds = [];
    $('.pr-checkbox:checked').each(function() {
        let ids = $(this).val().split(',');
        selectedIds = selectedIds.concat(ids);
    });

    
    console.log('Selected purchase_item_id:', selectedIds);
    // Buat baris tabel berdasarkan purchase_item_id yg dipilih
    let rowsHtml = selectedIds.map((id, index) => {
    let item = prItemDetails[id];
    if (!item) {
        console.warn(`Item dengan id ${id} tidak ditemukan di prItemDetails`);
        return ''; // Lewati jika item tidak ditemukan
    }
    return `
        <tr data-prid="${id}">
            <td class="p-2 border w-40 text-center">${item.request_number}</td>
             <td class="p-2 border max-w-xs truncate">
        <!-- Tambahkan hidden input untuk article_code dan purchase_request_id -->
        <input type="hidden" name="purchase_request_id[]" value="${item.purchase_request_id}">
        <input type="hidden" name="article_code[]" value="${item.article_code}">
        ${item.article_code} - ${item.article_name}
    </td>
            <td class="p-2 border w-24 text-center">${item.current_stock}</td>
            <td class="p-2 border w-32 text-center">
  <div class="relative w-full max-w-xs mx-auto">
    <input type="number" name="qty[]" value="${item.qty_remaining}" 
           class="qty-input w-full border rounded p-1 pr-16 text-right" />
    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500 select-none">
      ${item.uom}
    </span>
  </div>
</td>

            <td class="p-2 border w-32 text-right">
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
                        data-name="${item.article_name}" 
                        data-row-index="${index}">
                  <i data-feather="info"></i>
                </button>
                </div>
            </td>
            <td class="p-2 border w-36 text-right"> <span class="total-text"></span><input type="hidden" 
                       name="total[]" 
                       class="total-price"/></td>
           <td class="p-2 border text-center">
            <button type="button" class="remove-row text-red-500 px-2 py-1 rounded"><i data-feather="trash-2"></i></button>
        </td>
        </tr>
    `;
}).join('');


    console.log('HTML yang dibuat untuk tabel:', rowsHtml);

    $('#itemList').html(rowsHtml);
    feather.replace();
    updateSummary();
}

// Event listener checkbox PR
$(document).on('change', '.pr-checkbox', function() {
    const $this = $(this);
    const isChecked = $this.is(':checked');
    const purchaseItemIds = $this.val().split(',');
    if (purchaseItemIds.length === 0) return;

    // Ambil supplier dari salah satu purchase_item_id
    const firstId = purchaseItemIds[0];
    const currentSupplier = prItemDetails[firstId]?.supplier_code;

    if (!currentSupplier) {
        console.warn('Supplier untuk purchase_item_id', firstId, 'tidak ditemukan.');
        return;
    }

    if (isChecked) {
        if (selectedSupplier === null) {
            selectedSupplier = currentSupplier;
        } else if (selectedSupplier !== currentSupplier) {
             // Batalkan centang dan tampilkan SweetAlert
            $this.prop('checked', false);
            Swal.fire({
                icon: 'error',
                title: 'Supplier berbeda!',
                text: 'PR yang dipilih harus berasal dari supplier yang sama.',
                confirmButtonText: 'OK'
            });
            return;
        }
    } else {
        // Jika checkbox di-uncheck, cek apakah masih ada checkbox lain dari supplier yang sama
        let stillHasSameSupplier = false;
        $('.pr-checkbox:checked').each(function() {
            const ids = $(this).val().split(',');
            for (const id of ids) {
                if (prItemDetails[id]?.supplier_code === selectedSupplier) {
                    stillHasSameSupplier = true;
                    break;
                }
            }
            if (stillHasSameSupplier) return false; // keluar dari loop each
        });
        if (!stillHasSameSupplier) {
            selectedSupplier = null;
        }
    }
 updateSelectedSupplierDisplay(); // <-- update nama supplier
    updateItemTable();
    updateSummary();
});


$(document).on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
    updateSummary();
});



// Panggil loadSuppliers sekali saat awal
loadSuppliers();

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
function formatRupiah(angka) {
    if (!angka) return '';
    // Buat jadi number dulu, lalu ke format lokal Indonesia
    let num = Number(String(angka).replace(/[^0-9.-]+/g,""));
    if (isNaN(num)) return '';
    return num.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
}

// Saat fokus di input price, hapus format supaya mudah edit
$(document).on('focus', '.price-display', function() {
    let val = $(this).val();
    if (!val) return;
    // Hilangkan titik ribuan dan ganti koma jadi titik (standar desimal JS)
    val = val.replace(/\./g, '').replace(',', '.');
    $(this).val(val);
});

// Saat blur (keluar) input price, format ke ribuan dan koma
$(document).on('blur', '.price-display', function() {
    let val = $(this).val();
    if (!val) return;

    // Ganti koma dengan titik agar parseFloat jalan
    val = val.replace(',', '.');

    let num = parseFloat(val);
    if (isNaN(num)) {
        $(this).val('');
        return;
    }
    let formatted = formatRupiah(num);

    $(this).val(formatted);

    // Trigger update total
    $(this).trigger('input');
});

// Update total saat harga atau qty berubah (input event)
// Update total saat harga atau qty berubah (input event)
$(document).on('input', '.price-display, .qty-input', function() {
    console.log('Input price/qty changed');  // Debug

    const $row = $(this).closest('tr');

    let qty = parseFloat($row.find('.qty-input').val()) || 0;
    let priceStr = $row.find('.price-display').val().replace(/\./g, '').replace(',', '.');
    let price = parseFloat(priceStr) || 0;

    let total = qty * price;
    let totalFormatted = total > 0 
        ? 'Rp. ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2}) 
        : '';

    $row.find('td').eq(5).find('.total-text').text(totalFormatted);
    $row.find('.total-price').val(total);
    $row.find('.price-input').val(price);

    updateSummary();
});


function updateSummary() {
  // Hitung subtotal dari total harga tiap baris
  let subtotal = 0;
  $('.total-price').each(function() {
    let val = parseFloat($(this).val());
    if (!isNaN(val)) subtotal += val;
  });

  // Ambil discount manual (Rp)
  let discount = parseFloat($('#discount').val()) || 0;

  // Pastikan discount tidak lebih besar dari subtotal
  if (discount > subtotal) discount = subtotal;

  // Hitung dasar pajak (setelah discount)
  let taxableAmount = subtotal - discount;

  // Cek PPN dan PPh dipakai atau tidak
  let usePPN = $('#use_ppn').is(':checked');
  let usePPH = $('#use_pph').is(':checked');

  // Hitung PPN 10%
  let ppn = usePPN ? taxableAmount * 0.10 : 0;

  // Hitung PPh 2%
  let pph = usePPH ? taxableAmount * 0.02 : 0;

  // Hitung Netto (subtotal - discount + ppn - pph)
  let netto = taxableAmount + ppn - pph;

  // Format ke Rupiah dengan Intl.NumberFormat
  const formatIDR = val => 'Rp ' + val.toLocaleString('id-ID', {minimumFractionDigits: 0});

  // Update tampilan ringkasan
  $('#summary-subtotal').text(formatIDR(subtotal));
  $('#summary-ppn').text(formatIDR(ppn));
  $('#summary-pph').text(formatIDR(pph));
  $('#summary-netto').text(formatIDR(netto));

  // Update nilai hidden input untuk submit form
  $('#input-subtotal').val(subtotal.toFixed(2));
  $('#input-ppn').val(ppn.toFixed(2));
  $('#input-pph').val(pph.toFixed(2));
  $('#input-netto').val(netto.toFixed(2));
}

// Panggil updateSummary saat ada perubahan:
// - harga atau qty di tabel berubah
// - discount diubah
// - checkbox PPN atau PPh berubah

$(document).on('input change', '.price-display, .qty-input, #discount, #use_ppn, #use_pph', function() {
  updateSummary();
});

// Juga panggil updateSummary sekali saat halaman siap, supaya nilai awal muncul benar
$(document).ready(function() {
  updateSummary();
});

function updateSelectedSupplierDisplay() {
  if (selectedSupplier && supplierNames[selectedSupplier]) {
    $('#supplierNameDisplay').text(supplierNames[selectedSupplier]);
  } else {
    $('#supplierNameDisplay').text('');
  }
}

// Di event change checkbox PR, setelah update selectedSupplier, panggil:
updateSelectedSupplierDisplay();


</script>
@endpush
@endsection

