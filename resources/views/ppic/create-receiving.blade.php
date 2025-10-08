@extends('layouts.app')

@section('title', 'Create Receiving')
@section('page-title', 'Create Receiving')
@section('breadcrumb-item', 'Receiving')
@section('breadcrumb-active', 'Create Receiving')

@section('content')
<div class="w-full bg-white shadow rounded-xl mb-4">
  <!-- Accordion Header -->
  <button id="toggleAccordion" class="w-full text-left px-6 py-4 text-lg font-semibold flex justify-between text-gray-600 items-center">
    Upload Multiple Receiving
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
  <h2 class="text-lg font-semibold text-gray-700">Create Receiving</h2>
  <form id="rec-form" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      
      <!-- Receiving Number -->
      <div>
        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
          Receiving Number<small class="text-red-600"> *</small>
        </label>
        <input type="text" name="receiving_number" id="reference_number"
          class="w-full px-3 py-2 bg-gray-200 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Automatic" readonly />
      </div>

      <!-- Received Date -->
      <div>
        <label for="received_date" class="block text-sm font-medium text-gray-700 mb-1">
          Received Date<small class="text-red-600"> *</small>
        </label>
        <input type="date" name="received_date" id="received_date"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required value="{{ date('Y-m-d') }}" />
      </div>

     <!-- Supplier Dropdown -->
<div id="supplierWrapper">
  <label for="supplierSelect" class="block text-sm font-medium text-gray-700 mb-1">Supplier <small class="text-red-600"> *</small></label>
  <select id="supplierSelect" class="form-control w-full">
    <option value="">-- Choose Supplier --</option>
    @foreach($suppliers as $supplier)
      <option value="{{ $supplier->code }}">{{ $supplier->code }} - {{ $supplier->name }}</option>
    @endforeach
  </select>
  <input type="hidden" id="supplier_code" name="supplier_code">
</div>

  <!-- PO Dropdown akan berubah berdasarkan Supplier -->
<div id="poWrapper">
  <label for="poSelect" class="block text-sm font-medium text-gray-700 mb-1">Purchase Order</label>
  <select id="poSelect" name="purchase_order_id" class="form-control w-full">
    <option value="">-- Choose Purchase Order --</option>
    <!-- Akan diisi via JS -->
  </select>
</div>

      <!-- Delivery Order Number -->
      <div>
        <label for="delivery_order_number" class="block text-sm font-medium text-gray-700 mb-1">
          Delivery Order Number<small class="text-red-600"> *</small>
        </label>
        <input type="text" name="delivery_order_number" id="delivery_order_number"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukan Nomor Surat Jalan, PO atau yang setara..."
          required />
      </div>

      <!-- Delivery Order Date -->
      <div>
        <label for="delivery_order_date" class="block text-sm font-medium text-gray-700 mb-1">
          Delivery Order Date<small class="text-red-600"> *</small>
        </label>
        <input type="date" name="delivery_order_date" id="delivery_order_date"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required value="{{ date('Y-m-d') }}" />
      </div>
    </div>

    <!-- Note -->
    <div class="mb-12">
      <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
      <textarea id="note" name="note" rows="2"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
    </div>
</div>


<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700">Receiving Item</h2>
      <!-- 📋 Tabel Artikel yang Dipindahkan -->
      <div class="overflow-x-auto">
        <table  id="itemTable" class="min-w-full bg-white border border-gray-200">
          <thead class="bg-blue-500 text-white">
            <tr>
<th class="p-2 border w-64 text-left">Article</th>
<th class="p-2 border w-20 text-center">Qty Order</th>
<th class="p-2 border w-20 text-center">Qty Received</th>
<th class="p-2 border w-20 text-center">Qty Free</th>
<th class="p-2 border w-20 text-center">UOM</th>
<th class="p-2 border w-20 text-center">Qty Total</th>
<th class="p-2 border w-20 text-center">Expired Date</th>
<th class="p-2 border w-20 text-center">Destination</th>
            </tr>
          </thead>
          <tbody id="itemList">
            <!-- Diisi via JavaScript -->
          </tbody>
        </table>
      </div>

      <hr>

  <!-- 💰 Ringkasan Total -->
<div class="flex justify-end mt-4">
  <div class="w-full md:w-1/3 bg-gray-100 p-4 rounded shadow space-y-3">
    <!-- Subtotal -->
    <div class="flex justify-between">
      <span class="font-semibold">Total Qty Received</span>
      <span id="summary-qty-received">0</span>
    </div>

    <div class="flex justify-between">
      <span class="font-semibold">Total Qty Free</span>
      <span id="summary-qty-free">0</span>
    </div>
    <hr class="border-gray-400">

    <!-- Netto -->
    <div class="flex justify-between text-lg font-bold text-green-700">
      <span>Grand Total Qty</span>
      <span id="summary-grand-total">0</span>
    </div>
  </div>
</div>

<input type="hidden" name="qty_po[]" value="${item.qty_po}">
<input type="hidden" name="uom[]" value="${item.uom}">

      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start gap-2 mt-4">
        <a href="" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Back</a>
        <button id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
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
  $(document).ready(function () {
    $('#supplierSelect').select2({
      placeholder: "-- Choose Supplier --",
      width: '100%',
      allowClear: true
    });
    $('#poSelect').select2({
      placeholder: "-- Choose Purchase Order --",
      width: '100%',
      allowClear: true
    });
  });

  function updateSummary() {
    let totalReceived = 0;
    let totalFree = 0;

    document.querySelectorAll('input[name="qty_received[]"]').forEach(input => {
      totalReceived += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('input[name="qty_free[]"]').forEach(input => {
      totalFree += parseFloat(input.value) || 0;
    });

    const grandTotal = totalReceived + totalFree;

    document.getElementById('summary-qty-received').textContent = totalReceived.toFixed(0);
    document.getElementById('summary-qty-free').textContent = totalFree.toFixed(0);
    document.getElementById('summary-grand-total').textContent = grandTotal.toFixed(0);
  }

  const supplierSelect = document.getElementById('supplierSelect');
  const supplierCodeInput = document.getElementById('supplier_code');
  const poSelect = document.getElementById('poSelect');
  const itemList = document.getElementById("itemList");

  $('#supplierSelect').on('change', function () {
    const selectedCode = $(this).val();
    $('#supplier_code').val(selectedCode);

    const $poSelect = $('#poSelect');
    $poSelect.html('<option value="">-- Loading Purchase Orders --</option>');

    fetch(`/ppic/api/get-po-by-supplier/${selectedCode}`)
      .then(response => response.json())
      .then(data => {
        $poSelect.html('<option value="">-- Choose Purchase Order --</option>');
        data.forEach(po => {
          $poSelect.append(`<option value="${po.id}">${po.order_number}</option>`);
        });
      })
      .catch(error => {
        $poSelect.html('<option value="">-- Failed to load PO --</option>');
        console.error('Error:', error);
      });
  });

  $('#poSelect').on('change', function () {
    const poId = $(this).val();
    const $itemList = $('#itemList');

    $itemList.html('<tr><td colspan="7" class="text-center p-4">Loading...</td></tr>');

    fetch(`/ppic/api/get-po-items/${poId}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          $itemList.html('<tr><td colspan="7" class="text-center p-4 text-gray-500">No items found.</td></tr>');
          return;
        }

      let rows = '';
data.forEach((item, index) => {
  const sisaQty = item.qty_po - item.qty_received;
  const warehouseName = item.destination ?? ''; // Ambil dari backend sesuai article_type
  const articleUOM = item.uom ?? ''; // Ambil dari backend sesuai article_type

  rows += `
    <tr data-index="${index}">
      <td class="p-2 border bg-gray-100">${item.article_code} - ${item.article_name}
        <input type="hidden" name="article_code[]" value="${item.article_code}">
        <input type="hidden" name="po_item_ids[]" value="${item.id}">
      </td>
      <td class="p-2 border bg-gray-100 text-center">${sisaQty}
        <input type="hidden" name="qty_po[]" value="${sisaQty}">
      </td>
      <td class="p-2 border text-center">
        <input type="number" name="qty_received[]" 
               class="qty-received w-full text-center border rounded px-2 py-1" 
               data-index="${index}" value="0" min="0" max="${sisaQty}">
      </td>
      <td class="p-2 border text-center">
        <input type="number" name="qty_free[]" 
               class="qty-free w-full text-center border rounded px-2 py-1" 
               data-index="${index}" value="0" min="0">
      </td>
      <td class="p-2 border text-center">${articleUOM}</td>
      <td class="p-2 border text-center total-cell" id="total-${index}">
        <span class="total-display">0</span>
        <input type="hidden" name="qty_total[]" class="qty-total" value="0">
      </td>
      <td class="p-2 border text-center">
        <input type="date" name="expired_date[]" 
               class="w-full text-center border rounded px-2 py-1"
               value="">
      </td>
      <td class="p-2 border text-center">${warehouseName}
        <input type="hidden" name="destination_id[]" value="${item.destination_id}">
      </td>
    </tr>
  `;
});

$itemList.html(rows);



        // Pasang listener validasi dan total update
        $itemList.on('input', '.qty-received, .qty-free', function () {
          const index = $(this).data('index');
          const $row = $(`tr[data-index="${index}"]`);
          const qtyPo = parseFloat($row.find('td:eq(1)').text()) || 0;
          const qtyReceived = parseFloat($row.find('.qty-received').val()) || 0;
          const qtyFree = parseFloat($row.find('.qty-free').val()) || 0;

          // Validasi qty_received vs qty_po
          const $inputReceived = $row.find('.qty-received');
          if (qtyReceived > qtyPo) {
            $inputReceived.addClass('border-red-500 bg-red-600 text-white');
            $inputReceived.attr('title', 'Qty Received melebihi Qty PO');
          } else {
            $inputReceived.removeClass('border-red-500 bg-red-600 text-white');
            $inputReceived.removeAttr('title');
          }

          const total = qtyReceived + qtyFree;
         $row.find('.total-display').text(total.toFixed(0));
         $row.find('.qty-total').val(total.toFixed(0));


          updateSummary();
        });

        updateSummary();
      })
      .catch(error => {
        $itemList.html('<tr><td colspan="7" class="text-center p-4 text-red-500">Failed to load items.</td></tr>');
        console.error('Error:', error);
      });
  });
});

$('#submitBtn').off('click').on('click', function (e) {
  e.preventDefault();

  const submitBtn = this;
  submitBtn.disabled = true;
  submitBtn.innerHTML = 'Saving...';
  const form = $('#rec-form');

  // Cek minimal satu qty > 0
  let valid = false;
  $('input[name="qty_received[]"], input[name="qty_free[]"]').each(function () {
    if (parseFloat($(this).val()) > 0) {
      valid = true;
    }
  });

  if (!valid) {
    alert('Minimal satu item harus memiliki Qty Received atau Qty Free yang lebih dari 0.');
    return;
  }

  const formData = new FormData();

  // Tambahkan field-field manual
  form.find('input, select, textarea').each(function () {
    const name = $(this).attr('name');
    if (name) {
      if ($(this).is(':checkbox') || $(this).is(':radio')) {
        if ($(this).is(':checked')) {
          formData.append(name, $(this).val());
        }
      } else {
        formData.append(name, $(this).val());
      }
    }
  });
$('input[name="qty_po[]"]').each(function () {
    const val = parseFloat($(this).val());
    formData.append('qty_po[]', isNaN(val) ? 0 : val);
});


  // Tambahkan array qty_received, qty_free, po_item_ids jika diperlukan
  $('input[name="qty_received[]"]').each(function () {
    formData.append('qty_received[]', $(this).val());
  });

  $('input[name="qty_free[]"]').each(function () {
    formData.append('qty_free[]', $(this).val());
  });

  $('input[name="qty_total[]"]').each(function () {
    formData.append('qty_total[]', $(this).val());
  });

  $('input[name="po_item_ids[]"]').each(function () {
    formData.append('po_item_ids[]', $(this).val());
  });
$('input[name="destination_id[]"]').each(function () {
  formData.append('destination_id[]',  $(this).val());
});
$('input[name="expired_date[]"]').each(function () {
  formData.append('expired_date[]',  $(this).val());
});
$('input[name="article_code[]"]').each(function () {
  formData.append('article_code[]',  $(this).val());
});


  $.ajax({
    url: "{{ route('ppic.rec.store') }}",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
     Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Receiving succesfully saved!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });

      setTimeout(function () {
        window.location.href = "{{ route('ppic.rec.index') }}";
      }, 2000);
    },
    error: function (xhr) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'Simpan';

      if (xhr.status === 422) {
        const errors = xhr.responseJSON.errors;
        let firstError = Object.values(errors)[0][0]; // hanya ambil error pertama

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: firstError,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Error while saving.',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      }
    }
  });
});

</script>

@endpush

@endsection

