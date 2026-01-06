@extends('layouts.app')

@section('title', 'Create STO')
@section('page-title', 'CREATE STO')
@section('breadcrumb-item', 'Stock Opname')
@section('breadcrumb-active', 'Create STO')
@section('content')
<form id="doc-form" enctype="multipart/form-data">
  @csrf
  <!-- DIV UTAMA CONTAINER -->
    
    <!-- Flex container kiri + kanan -->
      <!-- LEFT CONTENT (2/3) -->
      <div class="w-full bg-white p-6 space-y-4 border border-gray-800">
   <div class="w-full bg-white flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-2">


  <!-- Judul -->
  <div class="w-full text-center border border-gray-700 p-2">
    <h2 class="text-3xl sm:text-lg font-bold text-gray-700">
     ELECTRONIC STOCK OPNAME (e-STO)
    </h2>
  </div>
</div>
        <!-- INPUTS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-6">
            <!-- Logo -->
  <div class="flex-shrink-0">
    <img src="{{ asset('img/logo-2.jpg') }}" alt="Company Logo" class="h-12 sm:h-16 w-auto">
  </div>
<form id="stoForm">
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    STO Number
  </label>

 <select
  name="sto_number"
  id="sto_number"
  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm
         focus:ring-indigo-500 focus:border-indigo-500"
>
@php
  $year  = 2025;
  $month = '12';
@endphp

@for ($i = 1; $i <= 2000; $i++)
  @php
    $number = str_pad($i, 4, '0', STR_PAD_LEFT);
    $val = "{$year}/{$month}/{$number}";
  @endphp

  @if (!in_array($val, $usedStoNumbers))
    <option value="{{ $val }}">
      {{ $val }}
    </option>
  @endif
@endfor
</select>



</div>
  <!-- ARTICLE LIST -->
<div class="col-span-2">
  <div class="overflow-x-auto">

    <table class="w-full text-sm border-separate border-spacing-y-2">
      <!-- THEAD -->
      <thead class="bg-blue-500 text-white">
        <tr>
          <th class="border border-gray-300 px-3 py-2 text-left">PART-CODE</th>
          <th class="border border-gray-300 px-3 py-2 text-left">PART-NAME</th>
          <th class="border border-gray-300 px-3 py-2 text-center w-32">QTY</th>
          <th class="border border-gray-300 px-3 py-2 text-center w-32">UOM</th>
         <th class="border border-gray-300 px-3 py-2 text-center w-48">
  LOCATION

  @if(in_array(auth()->id(), [67, 53]))
    
    @if($warehouse === null)
      <select name="warehouse"
              id="warehouse-null"
              class="mt-1 w-full text-black text-sm rounded px-1 py-1">
        <option value="">-- Pilih Gudang --</option>

        @foreach($allowedWarehouses as $wh)
          <option value="{{ $wh }}">{{ $wh }}</option>
        @endforeach
      </select>
    @else
      <input type="text"
             class="mt-1 w-full bg-gray-100 text-sm rounded px-1 py-1"
             value="{{ $warehouse }}"
             readonly>
    @endif

  @endif
</th>



        </tr>
      </thead>

      <!-- TBODY -->
      <tbody id="article-table">
        @for ($i = 0; $i < 15; $i++)
        <tr class="bg-white sto-row">
          <!-- PART CODE -->
          <td class="border border-gray-300 px-2 py-2">
            <input type="text"
       name="articles[{{ $i }}][article_code]"
       class="article-code w-full border rounded px-2 py-1"
       readonly>

          </td>

          <!-- PART NAME -->
         <td class="border px-2 py-2">
  <select class="part-select w-full"
          name="articles[{{ $i }}][article_id]"
          data-row="{{ $i }}">
    <option value="">-- pilih part --</option>
    @foreach ($articles as $a)
      <option value="{{ $a->id }}"
              data-code="{{ $a->article_code }}"
              data-uom="{{ $a->unit }}">
        {{ $a->description }}
      </option>
    @endforeach
  </select>
</td>


          <!-- QTY -->
          <td class="border border-gray-300 px-2 py-2 text-center">
            <input type="number"
                   name="articles[{{ $i }}][qty]"
                   min="0"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-center">
          </td>

          <!-- UOM -->
          <td class="border border-gray-300 px-2 py-2 text-center">
           <input type="text"
       name="articles[{{ $i }}][uom]"
       class="part-uom w-full border rounded px-2 py-1 text-center"
       readonly>

          </td>

          <!-- LOCATION -->
          <td class="border border-gray-300 px-2 py-2 text-center">
           <input type="text"
       name="articles[{{ $i }}][location]"
       value="{{ $warehouse }}"
       readonly
       class="location-input w-full bg-gray-100 border border-gray-300 rounded px-2 py-1">


          </td>
        </tr>
        @endfor
      </tbody>
    </table>

  </div>
</div>

<!-- NOTE & ACTION -->
<div class="col-span-2 mt-6 flex flex-col md:flex-row md:justify-between gap-6">

  <!-- NOTE -->
  <div class="w-full md:w-2/3">
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Catatan / Note
    </label>

    <div class="space-y-3">
    <textarea
    id="note"
  name="note"
  rows="3"
  class="note-lines w-full resize-none focus:outline-none"
  placeholder=""
></textarea>

    </div>
  </div>

  <!-- BUTTONS -->
  <div class="w-full md:w-auto flex items-end justify-end gap-3">
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center px-5 py-2 border border-gray-400 rounded-md text-gray-700 hover:bg-gray-100">
      Back
    </a>

    <button type="submit" id="btnSave"
            class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">
      Save
    </button>
  </div>
</form>

</div>

        </div>
      </div>
  
</form>

<style>
  .note-lines {
  background-image: repeating-linear-gradient(
    to bottom,
    transparent,
    transparent 1.9rem,
    #9ca3af 2rem
  );
  background-size: 100% 2rem;
  line-height: 2rem;
  border: none;
  padding: 0.25rem 0;
}
.note-lines:focus {
  background-image: repeating-linear-gradient(
    to bottom,
    transparent,
    transparent 1.9rem,
    #4f46e5 2rem
  );
}
    /* Supaya select2 full width */
.select2-container {
  width: 100% !important;
}

/* Supaya tinggi sama dengan input Tailwind */
.select2-container .select2-selection--single {
  height: 42px !important; /* total tinggi */
  display: flex !important;
  align-items: center !important;
  border: 1px solid #d1d5db; /* border-gray-300 */
  border-radius: 0.375rem;   /* rounded-md */
  padding: 0 0.75rem !important; /* px-3 */
  line-height: normal !important;
}

/* Hilangkan padding default di dalam text */
.select2-container .select2-selection__rendered {
  padding-left: 0 !important;
  padding-right: 0 !important;
  line-height: 1.5rem !important; /* sama seperti input tailwind text-base */
}


/* Placeholder dan text select2 */
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 42px !important;
  font-size: 15px; /* tailwind text-base */
  color: #374151;  /* tailwind text-gray-700 */
}

/* Tombol dropdown */
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 42px !important;
  right: 0.75rem;
}
</style>
@push('scripts')
<script>
$(document).ready(function () {

  // =====================
  // SELECT2 INIT
  // =====================
  $('.part-select').select2({
    placeholder: '-- Pilih Part --',
    width: '100%',
    allowClear: true,
    tags: true,
    selectOnClose: false,

    // =====================
    // MODE OTHER (KETIK MANUAL)
    // =====================
    createTag: function (params) {
      const term = $.trim(params.term);
      if (!term) return null;

      return {
        id: '__OTHER__:' + term,   // 🔑 penanda aman & unik
        text: term,
        isOther: true
      };
    },

    ajax: {
      url: "{{ route('facility.article.select') }}",
      dataType: 'json',
      delay: 300,
      data: params => ({ q: params.term }),

      processResults: function (data) {
        return {
          results: data.results.map(a => ({
            id: a.id,
            text: a.text,
            code: a.article_code,
            uom: a.unit,
            isOther: false
          }))
        };
      }
    }
  });

  // =====================
  // SELECT HANDLER
  // =====================
  $(document).on('select2:select', '.part-select', function (e) {
    const data = e.params.data;
    const $row = $(this).closest('tr');
    const row  = $(this).data('row');

    const $codeInput = $(`input[name="articles[${row}][article_code]"]`);
    const $uomInput  = $(`input[name="articles[${row}][uom]"]`);

    const isOther = data.isOther || String(data.id).startsWith('__OTHER__:');

    // =====================
    // MODE OTHER
    // =====================
    if (isOther) {

      $codeInput.val('OTHER');

      $uomInput
        .val('')
        .prop('readonly', false)
        .focus();

      $row.data('other_name', data.text);

    }
    // =====================
    // MODE NORMAL
    // =====================
    else {

      $codeInput.val(data.code || data.id || '');

      $uomInput
        .val(data.uom || '')
        .prop('readonly', true);

      $row.data('other_name', null);
    }

    // qty aktif setelah pilih
    $row.find('.qty-input').prop('disabled', false);
  });

  // =====================
  // CLEAR HANDLER
  // =====================
 $(document).on('select2:clear', '.part-select', function () {
  const $select = $(this);
  const $row    = $select.closest('tr');
  const row     = $select.data('row');

  // 🔥 reset select2 dengan benar
  $select
    .val(null)
    .trigger('change');

  // 🔥 hapus option OTHER hasil ketikan manual
  $select.find('option').filter(function () {
    return this.value && this.value.startsWith('__OTHER__:');
  }).remove();

  // 🔥 reset input lain
  $(`input[name="articles[${row}][article_code]"]`).val('');
  $(`input[name="articles[${row}][uom]"]`)
    .val('')
    .prop('readonly', true);

  $row.data('other_name', null);
  $row.find('.qty-input').val('').prop('disabled', true);
});


});

$(document).on('change', '#warehouse-null, #wip-master', function () {
  const selectedLocation = $(this).val();
  console.log('SELECTED:', selectedLocation);

  if (!selectedLocation) {
    $('.location-input').val('');
    return;
  }

  $('.location-input').val(selectedLocation);
});

$(document).ready(function () {

  function loadArticles(warehouse = null) {

    $.get('/facility/articles/by-warehouse', { warehouse }, function (articles) {

     $('.part-select').each(function () {
  const select = $(this);
  select.empty();
  select.append('<option value="">-- Pilih Artikel --</option>');

  articles.forEach(a => {
    select.append(`
      <option value="${a.article_code}"
              data-code="${a.article_code}"
              data-uom="${a.unit}">
        ${a.article_code} - ${a.description}
      </option>
    `);
  });

  // 🔥 INI KUNCI
  select.val(null).trigger('change');
});

    });
  }

  // 🔥 LOAD AWAL (warehouse null → semua artikel)
  loadArticles(null);

  // 🔁 SAAT GUDANG DIPILIH
  $('#warehouse-null').on('change', function () {
    const warehouse = $(this).val() || null;
    loadArticles(warehouse);
  });

});



$(document).ready(function () {
  $('#sto_number').select2({
    placeholder: '-- Pilih STO Number --',
    width: '100%'
  });
});

$(document).ready(function () {

  $('#btnSave').on('click', function (e) {
    e.preventDefault();

    let articles = [];
    let hasError = false;
    let errorRow = 0;

  $('.sto-row').each(function (index) {

  const $row = $(this);

  const articleCode = $row.find('.article-code').val()?.trim();
  const qty = parseFloat($row.find('input[name$="[qty]"]').val());
  const location = $row.find('.location-input').val();
const uom         = $(this).find('.part-uom').val()?.trim();
  const otherName = $row.data('other_name') || null; // ✅ FIX UTAMA

  const isFilled = articleCode || qty;

  if (!isFilled) return;

  if (!qty || qty <= 0) {
    hasError = true;
    errorRow = index + 1;
    return false;
  }

  articles.push({
    article_code: articleCode,
    other_name: articleCode === 'OTHER' ? otherName : null, // ✅ AMAN
    qty: qty,
     uom: uom || null,
    location: location
  });
});


    /* =========================
       VALIDATION
    ========================= */

    if (hasError) {
      Swal.fire({
        icon: 'warning',
        title: 'Qty tidak valid',
        text: `Qty pada baris ke-${errorRow} harus lebih dari 0`,
        confirmButtonText: 'Oke'
      });
      return;
    }

    if (articles.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Data belum lengkap',
        text: 'Minimal 1 item STO harus diisi',
        confirmButtonText: 'Oke'
      });
      return;
    }

    /* =========================
       PAYLOAD
    ========================= */

    const payload = {
      sto_number: $('#sto_number').val(),
      note: $('#note').val(),
      articles: articles,
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    console.log('=== STO PAYLOAD ===', payload);

    $.ajax({
      url: '/facility/sto/save',
      method: 'POST',
      data: payload,
      beforeSend: function () {
        $('#btnSave').prop('disabled', true).text('Saving...');
      },
      success: function (res) {

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: res.message,
          timer: 2000,
          showConfirmButton: false
        });

        /* =========================
           RESET STO NUMBER
        ========================= */
        const $stoSelect = $('#sto_number');
        const usedValue = $stoSelect.val();

        $stoSelect.find(`option[value="${usedValue}"]`).remove();
        $stoSelect.prop('selectedIndex', 0);

        /* =========================
           RESET TABLE ROWS
        ========================= */
        $('.sto-row').each(function () {
          $(this).find('.part-select').val(null).trigger('change');
          $(this).find('.article-code').val('');
          $(this).find('.part-uom').val('');
          $(this).find('input[name$="[qty]"]').val('');
          $(this).find('.location-input').val('{{ $warehouse }}');
        });

        $('textarea[name="note"]').val('');
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let msg = '';
          $.each(xhr.responseJSON.errors, function (_, value) {
            msg += `• ${value[0]}\n`;
          });

          Swal.fire({
            icon: 'error',
            title: 'Validasi gagal',
            text: msg
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat menyimpan STO'
          });
        }
      },
      complete: function () {
        $('#btnSave').prop('disabled', false).text('Save');
      }
    });
  });

});


</script>

@endpush

@endsection