@extends('layouts.app')

@section('title', 'Create Purchase Request')
@section('page-title', 'Create Purchase Request')
@section('breadcrumb-item', 'Purchase Request')
@section('breadcrumb-active', 'Create Purchase Request')

@section('content')

<!-- Accordion Header
<div class="w-full bg-white shadow rounded-xl mb-4">
  <button id="toggleAccordion" class="w-full text-left px-6 py-4 text-lg font-semibold flex justify-between text-gray-600 items-center">
    Upload Multiple Purchase Request
    <svg id="accordionIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

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
</div>-->


<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New Purchase Request</h2>
    <form id="pr-form">
        @csrf
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="request_number" class="block text-sm font-medium text-gray-700 mb-1">Request Number<small class="text-red-600"> *</small></label>
        <input type="text" name="request_number" id="request_number"
               class="w-full px-3 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Automatic" required />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
  <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
    Request Date<small class="text-red-600"> *</small>
  </label>
  <input type="date" name="request_date" id="request_date"
         value="{{ date('Y-m-d') }}"
         class="w-full p-2 text-xs border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
         required />
</div>

        <div>
    <label for="filter-order-type" class="block text-sm mb-1 font-medium text-gray-700">Order Type</label>
    <select id="filter-order-type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- Choose Type --</option>
        <option value="Standard">Standard</option>
        <option value="Target Sales Order">Target Sales Order</option>
        <option value="GA Request">General Affair Request</option>
        <option value="IT Request">Electronics & IT Request</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
</div>

      </div>
       <div id="salesOrderFields" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 hidden">
        <div>
  <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
    Stock Needed at<small class="text-red-600"> *</small>
  </label>
  <input type="date" name="date" id="date"
         class="w-full px-3 py-2 text-xs border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
         required />
</div>

         <div>
  <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">
    Target Sales Order<small class="text-red-600"> *</small>
  </label>
  <select name="order_type" id="order_type"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required>
    <option value="Standard">Standard</option>
    <option value="sales_order">Sales Order</option>
  </select>
</div>

      </div>
      <div id="gaRequestFields" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 hidden">
  <div>
  <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">
    General Affair Request<small class="text-red-600"> *</small>
  </label>
  <select name="order_type" id="order_type"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required>
    <option value="Standard">Standard</option>
    <option value="sales_order">Sales Order</option>
  </select>
</div>

</div>

       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div class="col-span-2 mb-4">
        <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
        <textarea id="note" name="pr_note" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
</div>
     
</div>

<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
  <h2 class="text-lg font-semibold text-gray-700">Purchase Request Item</h2>

  <div id="itemList" class="space-y-0.5">
    <!-- Baris Pertama (dengan label) -->
    <div class="item-row flex flex-col md:flex-row md:items-center md:gap-2 gap-1">
      <!-- Article -->
      <div class="flex-1 min-w-0">
        <label class="block text-sm font-medium text-gray-700 mb-1">Article</label>
        <select name="article_code[]" class="article-select w-full truncate border rounded p-2" id="article-select">
           <option value="">-- Choose Article --</option>
        </select>
      </div>

      <!-- Stock -->
      <div class="w-24">
        <label class="block text-sm font-medium text-gray-700 mb-1 text-center">Qty Stock</label>
        <input type="text" name="current_stock[]" class="stock-input w-full border rounded bg-gray-100 p-2 text-center" readonly>
      </div>

      <!-- Qty -->
      <div class="w-24">
        <label class="block text-sm font-medium text-gray-700 mb-1 text-center">Qty Request</label>
        <input type="number" name="qty[]" class="w-full border rounded p-2 text-center">
      </div>

      <!-- UOM -->
      <div class="w-24">
        <label class="block text-sm font-medium text-gray-700 mb-1 text-center">UOM</label>
        <input type="text" name="uom[]" class="uom-input w-full border rounded bg-gray-100 p-2 text-center" readonly>
      </div>

      <!-- Note -->
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <input type="text" name="note[]" class="w-full border rounded p-2">
      </div>

      <!-- Remove -->
      <div class="w-16 flex items-center pt-5">
        <button type="button" onclick="removeRow(this)" class="text-red-600 hover:underline"><i data-feather="trash-2"></i></button>
      </div>
    </div>
  </div>

  <!-- ➕ Add Button -->
  <div class="mt-4">
    <button type="button" onclick="addRow()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Add Article</button>
  </div>
   <hr>
      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start gap-2 mt-4">
        <a href="{{ route('purchasing.pr.index') }}" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   ← Back
</a>

<button type="button" id="submitBtn"
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-800 text-white rounded shadow">
   <i data-feather="save" class="h-4 w-4"></i>
   Save
</button>

      </div>
    </form>
</div>
     
  </div>
</div>

<template id="itemRowTemplate">
  <div class="item-row flex flex-col md:flex-row md:items-center md:gap-2 gap-1">
    <div class="flex-1 min-w-0">
        <select name="article_code[]" class="article-select w-full truncate border rounded p-2">
           <option value="">-- Choose Article --</option>
        </select>
      </div>
    <div class="w-24">
      <input type="text" name="current_stock[]" class="stock-input w-full border rounded bg-gray-100 p-2 text-center" readonly>
    </div>
    <div class="w-24">
      <input type="number" name="qty[]" class="w-full border rounded p-2 text-center">
    </div>
    <div class="w-24">
      <input type="text" class="uom-input w-full border rounded bg-gray-100 p-2 text-center" readonly>
    </div>
    <div class="flex-1">
      <input type="text" name="note[]" class="w-full border rounded p-2">
    </div>
    <div class="w-16 flex items-center pt-1">
      <button type="button" onclick="removeRow(this)" class="text-red-600 hover:underline"><i data-feather="trash-2"></i></button>
    </div>
  </div>
</template>


<style>
    input::placeholder {
  font-size: 10px;
  color: #9ca3af; /* Tailwind gray-400 */
}
/* Tambahkan di style block */
.cloned-row select,
.cloned-row input {
  margin-top: 1.5rem; /* agar sejajar jika tanpa label */
}

.select2 {
  width: 100% !important;
}

/* Memastikan Select2 container selalu mengikuti full width parent */
.select2-container {
  width: 100% !important;
}

/* Jaga agar tampilan tetap stabil saat dipilih */
.select2-container--default .select2-selection--single {
  height: 38px; /* atau sesuai dengan input lainnya */
  padding: 6px 12px;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  box-sizing: border-box;
  width: 100%;
}


</style>
@push('scripts')
<script>
  let isSubmitting = false;

  function initSelect2($el) {
    $el.select2({
        placeholder: '-- Choose Article --',
        allowClear: true,
        ajax: {
            url: "{{ route('purchasing.pr.article') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                // 🔥 ambil semua article_code yang sudah dipilih di form
                let selectedArticles = $('.article-select').map(function () {
                    return $(this).val();
                }).get().filter(Boolean); // hapus null/empty

                return {
                    results: data.items
                        .filter(article => !selectedArticles.includes(article.article_code)) // 🚫 hide yang sudah dipilih
                        .map(function (article) {
                            return {
                                id: article.article_code,
                                text: article.article_code + ' - ' + article.description,
                                uom: article.uom,
                                stock: article.stock
                            };
                        }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Saat artikel dipilih → isi UOM dan Stock
    $el.on('select2:select', function (e) {
        var data = e.params.data;
        var row = $(this).closest('.item-row');

        row.find('.uom-input').val(data.uom || '');
        row.find('.stock-input').val(data.stock ?? 0);
    });

    // Kalau di-clear, kosongkan field
    $el.on('select2:clear', function () {
        var row = $(this).closest('.item-row');
        row.find('.uom-input').val('');
        row.find('.stock-input').val('');
    });
}


// Inisialisasi di baris pertama
$(document).ready(function () {
    initSelect2($('.article-select'));
});

function fetchStock(selectElement) {
  const selected = selectElement.options[selectElement.selectedIndex];
  const stock = selected.getAttribute('data-stock') || 0;
  const uom = selected.getAttribute('data-uom') || '';
  const row = selectElement.closest('tr');
  row.querySelector('.stock-input').value = stock;
  row.querySelector('.uom-input').value = uom;
}

// Saat tambah row baru
function addRow() {
    const container = document.getElementById('itemList');
    const template = document.getElementById('itemRowTemplate');
    const clone = template.content.cloneNode(true);

    container.appendChild(clone);

    // Inisialisasi hanya di select2 baru
    initSelect2($(container).find('.item-row:last .article-select'));

    feather.replace();
}


function removeRow(button) {
  const container = document.getElementById('itemList');
  const row = button.closest('.item-row');
  if (container.childElementCount > 1) {
    row.remove();
  } else {
    alert('Minimal satu item harus ada.');
  }
}

document.getElementById('filter-order-type').addEventListener('change', function () {
  const selectedType = this.value;
  const salesFields = document.getElementById('salesOrderFields');
  const gaFields = document.getElementById('gaRequestFields');

  // Reset tampilan
  salesFields.classList.add('hidden');
  gaFields.classList.add('hidden');

  // Reset required
  document.getElementById('stock_needed_at')?.removeAttribute('required');
  document.getElementById('sales_order_id')?.removeAttribute('required');
  document.getElementById('ga_purpose')?.removeAttribute('required');

  // Tampilkan sesuai pilihan
  if (selectedType === 'Target Sales Order') {
    salesFields.classList.remove('hidden');
    document.getElementById('stock_needed_at')?.setAttribute('required', 'required');
    document.getElementById('sales_order_id')?.setAttribute('required', 'required');
  } else if (selectedType === 'GA Request') {
    gaFields.classList.remove('hidden');
    document.getElementById('ga_purpose')?.setAttribute('required', 'required');
  }
});

$('#submitBtn').on('click', function (e) {
    e.preventDefault();

    if (isSubmitting) return; // cegah klik submit kedua
    isSubmitting = true;
    $('#submitBtn').prop('disabled', true);

    let form = $('#pr-form');
    let formData = new FormData(form[0]);

    $('.article-select').each(function (i) {
        formData.append('article_code[]', $(this).val());
    });
    $('input[name="qty[]"]').each(function (i) {
        formData.append('qty[]', $(this).val());
    });
    $('input[name="note[]"]').each(function (i) {
        formData.append('note[]', $(this).val());
    });

    $.ajax({
        url: "{{ route('purchasing.pr.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            setTimeout(() => {
                window.location.href = "/purchasing/purchase-request/index"; 
            }, 1500);
        },
        error: function (xhr) {
            let err = xhr.responseJSON?.message || 'Gagal menyimpan data';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: err,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        },
        complete: function() {
            isSubmitting = false;
            $('#submitBtn').prop('disabled', false);
        }
    });
});



</script>
@endpush
@endsection

