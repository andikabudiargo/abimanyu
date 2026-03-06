@extends('layouts.app')

@section('title', 'Price Management')
@section('page-title', 'PRICE MANAGEMENT')
@section('breadcrumb-item', 'Conversion')
@section('breadcrumb-active', 'Price Management')

@section('content')


{{-- 📄 TABEL --}}
 <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Price Management</h2>
<!-- Toast Container — z-index lebih tinggi dari SweetAlert (1060) -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    <form id="filter-form">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Customer</label>
            <select id="filter-customer" name="customer" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                  <option value="">-- All Customer --</option>
                @foreach ($customers as $customer)
                <option value="{{ $customer->name }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Article</label>
            <select id="filter-article" name="article" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="">-- All Article --</option>
                @foreach ($articles as $article)
                <option value="{{ $article->article_code }}">{{ $article->article_code }} - {{ $article->description }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg shadow transition">
            Search
        </button>

         <button type="button"
                id="btn-sync"
                class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
            <i id="sync-icon" class="ri-refresh-line"></i>
            <span id="sync-label">Sync Pricing</span>
        </button>

      
    </div>
</form>
</div>

<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Price & Conversion List</h2>
    <div class="bg-white rounded-xl">
    <table id="conversion-table" class="w-max text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Article Code</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">UoM</th>
                    <th class="px-4 py-2">Selling Price</th>
                    <th class="px-4 py-2">RM Purchase Price</th>
                    <th class="px-4 py-2">FG Conversion</th>
                    <th class="px-4 py-2">RM Conversion</th>
                    <th class="px-4 py-2">Fixed Conversion</th>
                    <th class="px-4 py-2">Last Sync</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>
        </div>


        <div id="priceModal"
class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm
       hidden items-center justify-center z-50">

  <div class="bg-white w-full max-w-xl rounded-2xl
              shadow-2xl border border-slate-200
              p-7 relative animate-scaleIn">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h3 class="text-xl font-semibold text-slate-800">
          Basic Price Setup
        </h3>
        <p class="text-sm text-slate-500">
          Create new effective pricing record
        </p>
      </div>

      <button id="closePriceModal"
        class="text-slate-400 hover:text-red-500 text-xl">
        ✕
      </button>
    </div>

    <form id="basicPriceForm">
@csrf
      <!-- Article -->
      <div class="mb-5">
        <label class="text-xs mb-4 font-semibold text-slate-500 uppercase">
          Article
        </label>

        <select id="articleSelect" name="article_code"
          class="w-full mt-2 rounded-xl border border-slate-300
                 px-4 py-3 bg-slate-50
                 focus:bg-white focus:ring-4 focus:ring-indigo-100
                 focus:border-indigo-500 transition">
        </select>
      </div>

      <!-- Prices -->
      <div class="grid grid-cols-2 gap-4">

       <div>
  <label class="text-xs font-semibold text-slate-500 uppercase">
    Material Price
  </label>

  <div class="relative mt-2">

    <!-- Rp Prefix -->
    <span class="absolute left-4 top-1/2 -translate-y-1/2
                 text-slate-500 font-semibold">
      Rp
    </span>

    <input type="number"
      step="0.01"
      id="materialPrice"
      name="material_price"
      class="price-input w-full pl-12 pr-4 py-3 rounded-xl
             border border-slate-300 bg-slate-50
             focus:bg-white focus:ring-4 focus:ring-indigo-100
             focus:border-indigo-500 transition text-right">
  </div>
</div>

      <div>
  <label class="text-xs font-semibold text-slate-500 uppercase">
    Service Price
  </label>

  <div class="relative mt-2">

    <!-- Rp Prefix -->
    <span class="absolute left-4 top-1/2 -translate-y-1/2
                 text-slate-500 font-semibold">
      Rp
    </span>

    <input type="number"
      step="0.01"
      id="servicePrice"
      name="service_price"
      class="price-input w-full pl-12 pr-4 py-3 rounded-xl
             border border-slate-300 bg-slate-50
             focus:bg-white focus:ring-4 focus:ring-indigo-100
             focus:border-indigo-500 transition text-right">
  </div>
</div>

      </div>

      <!-- Effective Date -->
      <div class="mt-5 col-span-2">
        <label class="text-xs font-semibold text-slate-500 uppercase">
          Effective Date
        </label>

        <input type="date" name="effective_date"
          min="{{ date('Y-m-d') }}"
          class="w-full mt-2 rounded-xl border border-slate-300
                 px-4 py-3 bg-slate-50
                 focus:bg-white focus:ring-4 focus:ring-indigo-100
                 focus:border-indigo-500 transition"
          required>
      </div>

      <!-- Action -->
      <div class="flex justify-end gap-3 mt-8">
        <button type="button" id="cancelPrice"
          class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300">
          Cancel
        </button>

        <button type="submit"
          class="px-6 py-2.5 rounded-xl text-white
                 bg-gradient-to-r from-indigo-600 to-blue-600
                 hover:from-indigo-700 hover:to-blue-700
                 shadow-lg">
          Save
        </button>
      </div>

    </form>
  </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#conversion-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#conversion-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    margin-left: 10px;
}

/* 🧭 Spacing */
#conversion-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#conversion-table th, #conversion-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#conversion-table td, #conversion-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#conversion-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
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

<script>
$(document).ready(function(){

function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon,
        title: title
    });
}

  $('#filter-customer').select2({
        width: '100%',
        placeholder: '-- All Customer --',
        allowClear: true
    });

     $('#filter-article').select2({
        width: '100%',
        placeholder: '-- All Article --',
        allowClear: true
    });

  let onlyMatome = 0;

$('#conversion-table').DataTable({
    processing: true,
    serverSide: true,
    autowidth: false,
    drawCallback: function(settings) {
        if (!$('#conversion-table').parent().hasClass('scroll-wrapper')) {
            $('#conversion-table').wrap('<div class="scroll-wrapper overflow-x-auto"></div>');
        }
    },
    ajax: {
        url: '{{ route("marketing.price.data") }}',
        data: function (d) {
            d.customer    = $('#filter-customer').val();
            d.article     = $('#filter-article').val();
            d.only_matome = onlyMatome;
        }
    },
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: '<"dt-toolbar flex flex-wrap justify-between items-center gap-3 mb-4"l<"flex items-center gap-2"f<"matome-toggle">B>>rt<"dt-footer flex justify-between items-center mt-3"ip>',
    buttons: [
        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel text-xs mr-1"></i> Export Excel',
            className: 'inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium border border-emerald-500 rounded-lg bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition-all duration-150',
            filename: 'pricing-conversion-' + new Date().toISOString().slice(0,10),
            exportOptions: {
                columns: ':visible'
            }
        }
    ],
    initComplete: function() {
    // 1. Buat dulu elemennya
    $('div.matome-toggle').html(`
        <button id="filter-matome"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium border border-gray-200 rounded-lg bg-white text-gray-500 shadow-sm hover:border-blue-400 hover:text-blue-500 transition-all duration-150">
            <i class="fa-solid fa-filter text-xs"></i>
            <span>Fix Conversion</span>
        </button>
    `);

    // 2. Baru pasang event
    $('#filter-matome').on('click', function () {
        onlyMatome = onlyMatome === 1 ? 0 : 1;

        if (onlyMatome === 1) {
            $(this)
                .removeClass('border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500')
                .addClass('border-blue-500 bg-blue-500 text-white');
        } else {
            $(this)
                .removeClass('border-blue-500 bg-blue-500 text-white')
                .addClass('border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500');
        }

        $('#conversion-table').DataTable().ajax.reload();
    });
},
    columns: [
        { data: 'supplier_name',              searchable: false },
        { data: 'article_code',               searchable: true  },
        { data: 'description',                searchable: true  },
        { data: 'uom',                        searchable: false  },
        { data: 'selling_price',              searchable: false },
        { data: 'average_raw_material_price', searchable: false },
        
       
        { data: 'fg_conversion',              searchable: false },
         { data: 'rm_conversion',              searchable: false },
        { data: 'matome',                     searchable: false },
        { data: 'last_calculated_at',         searchable: false },
    ]
});

$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    $('#conversion-table').DataTable().ajax.reload();
});

    $('#addPrice').on('click', function(){
    $('#priceModal').removeClass('hidden').addClass('flex');
});

$('#closePriceModal, #cancelPrice').on('click', function(){
    $('#priceModal').addClass('hidden').removeClass('flex');
});

$('#articleSelect').select2({
        placeholder: '-- Choose Article --',
        width: '100%',
        dropdownParent: $('#priceModal') // penting kalau di dalam modal
    });

    // load article data
    $.get('/marketing/article/data', function (res) {

        let options = '<option value=""></option>';

        res.forEach(a => {
            options += `
                <option value="${a.article_code}">
                    ${a.article_code} - ${a.description}
                </option>`;
        });

        $('#articleSelect').html(options).trigger('change');
    });

$('#basicPriceForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn  = $form.find('button[type="submit"]');

    const originalText = $btn.html();

    // lock button
    $btn.prop('disabled', true)
        .addClass('opacity-50 cursor-not-allowed')
        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: '{{ route("marketing.price.store") }}',
        method: 'POST',
        data: $form.serialize(),

        success: function (res) {

            if (res.success) {

                showToast('success', res.message || 'Price saved successfully');

                // ========================
                // RESET FORM
                // ========================
                $form[0].reset();

                // reset select2
                $('#articleSelect')
                    .val(null)
                    .trigger('change');

                // reset price manual (biar pasti)
                $('#materialPrice').val('');
                $('#servicePrice').val('');

                // reload datatable tanpa reset paging
                $('#conversion-table')
                    .DataTable()
                    .ajax.reload(null,false);
            }
            else{
                showToast('error', res.message || 'Failed saving price');
            }

            // unlock button
            $btn.prop('disabled', false)
                .removeClass('opacity-50 cursor-not-allowed')
                .html(originalText);
        },

        error: function (err) {

            console.error(err.responseText);

            const msg = err.responseJSON?.message
                        || 'Terjadi kesalahan saat menyimpan.';

            showToast('error', msg);

            $btn.prop('disabled', false)
                .removeClass('opacity-50 cursor-not-allowed')
                .html(originalText);
        }
    });
});

  /*
    |------------------------------------------------------------------
    | Toast
    |------------------------------------------------------------------
    */
    function showToast(message, type) {
        var colors = {
            success : 'bg-emerald-500',
            error   : 'bg-red-500',
            loading : 'bg-gray-700',
        };
        var icons = {
            success : 'ri-checkbox-circle-line',
            error   : 'ri-error-warning-line',
            loading : 'ri-loader-4-line animate-spin',
        };

        var $toast = $(
            '<div id="toast-' + type + '" class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white text-sm opacity-0 translate-x-4 transition-all duration-300 ' + colors[type] + '">' +
                '<i class="' + icons[type] + ' text-lg shrink-0"></i>' +
                '<span>' + message + '</span>' +
            '</div>'
        );

        $('#toast-container').append($toast);

        // Animasi masuk
        setTimeout(function () {
            $toast.removeClass('opacity-0 translate-x-4');
        }, 10);

        // Auto remove kecuali loading
        if (type !== 'loading') {
            setTimeout(function () {
                removeToast($toast);
            }, 3500);
        }

        return $toast;
    }

    function removeToast($toast) {
        if (!$toast || $toast.length === 0) return;
        $toast.addClass('opacity-0 translate-x-4');
        setTimeout(function () {
            $toast.remove();
        }, 300);
    }

    /*
    |------------------------------------------------------------------
    | Sync Pricing
    |------------------------------------------------------------------
    */
    $('#btn-sync').on('click', function () {
        var $btn   = $('#btn-sync');
        var $icon  = $('#sync-icon');
        var $label = $('#sync-label');

        $btn.prop('disabled', true);
        $icon.attr('class', 'ri-loader-4-line animate-spin');
        $label.text('Syncing...');

        var $loadingToast = showToast('Sedang melakukan sync pricing...', 'loading');

        $.ajax({
            url    : '{{ route("marketing.pricing.sync") }}',
            method : 'POST',
            data   : { _token: '{{ csrf_token() }}' },
            success: function (res) {
    removeToast($loadingToast);

    // Toast success selalu muncul
    showToast(res.synced_count + ' price succesfully sync.', 'success');

    // Jika ada yang skip, tampilkan report di SweetAlert
    if (res.skip_count > 0) {
        var rows = '';
        $.each(res.skipped, function (i, item) {
            rows +=
                '<tr class="border-b border-gray-100">' +
                    '<td class="py-1.5 px-3 text-xs font-medium text-gray-700 whitespace-nowrap text-left">' + item.article_code + '</td>' +
                    '<td class="py-1.5 px-3 text-xs text-red-500 text-left">' + item.reasons.join('<br>') + '</td>' +
                '</tr>';
        });

        var html =
            '<div class="text-left mb-3 flex gap-2">' +
                '<span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full">' +
                    '<i class="ri-checkbox-circle-line"></i> ' + res.synced_count + ' berhasil' +
                '</span>' +
                '<span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">' +
                    '<i class="ri-close-circle-line"></i> ' + res.skip_count + ' diskip' +
                '</span>' +
            '</div>' +
            '<div class="overflow-y-auto max-h-64 rounded-lg border border-gray-200">' +
                '<table class="w-full">' +
                    '<thead class="bg-gray-50 sticky top-0">' +
                        '<tr>' +
                            '<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 whitespace-nowrap">Article Code</th>' +
                            '<th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Alasan</th>' +
                        '</tr>' +
                    '</thead>' +
                    '<tbody>' + rows + '</tbody>' +
                '</table>' +
            '</div>';

        Swal.fire({
            title              : 'Sync Report',
            html               : html,
            icon               : 'warning',
            confirmButtonText  : 'OK',
            confirmButtonColor : '#10b981',
            width              : '640px',
            customClass        : { popup: 'text-sm' },
        });
    }

    $('#conversion-table').DataTable().ajax.reload(null, false);
},

error: function (xhr) {
    removeToast($loadingToast);
    var msg = xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : 'Terjadi kesalahan.';

    showToast(msg, 'error');
},
            complete: function () {
                $btn.prop('disabled', false);
                $icon.attr('class', 'ri-refresh-line');
                $label.text('Sync Pricing');
            }
        });
    });
});
</script>
@endpush


@endsection