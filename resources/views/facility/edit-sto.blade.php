@extends('layouts.app')

@section('title', 'Edit STO')
@section('page-title', 'EDIT STO')
@section('breadcrumb-item', 'Stock Opname')
@section('breadcrumb-active', 'Edit STO')
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

 <input
  type="text"
  value="{{ $sto->sto_number }}"
  disabled
  class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100">

<input type="hidden" name="sto_number" value="{{ $sto->sto_number }}">




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
  @if($warehouse === 'Work In Progress')
    <select id="wip-master"
            class="mt-1 w-full text-black text-sm rounded px-1 py-1">
      <option value="">-- pilih WIP --</option>
      <option value="WIP Sanding">WIP Sanding</option>
      <option value="WIP Buffing">WIP Buffing</option>
      <option value="WIP Stripping">WIP Stripping</option>
      <option value="WIP Touch Up">WIP Touchup</option>
    </select>
  @endif
</th>

        </tr>
      </thead>
@php
  $maxRow   = 7;
  $rowCount = $items->count();
@endphp

      <!-- TBODY -->
   <tbody id="article-table">

@foreach ($items as $i => $item)
<tr class="bg-white sto-row">
  <td class="border px-2 py-2">
    <input type="text"
           name="items[{{ $i }}][article_code]"
           class="article-code w-full border rounded px-2 py-1"
           value="{{ $item->article_code }}"
           readonly>
  </td>

  <td class="border px-2 py-2">
  <select name="items[{{ $i }}][article_id]"
        class="part-select w-full"
        data-row="{{ $i }}">

  <option value="">-- pilih --</option>

  {{-- 🔥 OPTION KHUSUS UNTUK OTHER (EDIT MODE) --}}
  @if ($item->article_code === 'OTHER')
    <option value="OTHER"
            data-code="OTHER"
            data-uom="{{ $item->uom }}"
            selected>
      {{ $item->other_name }}
    </option>
  @endif

  @foreach ($articles as $a)
    <option value="{{ $a->id }}"
            data-code="{{ $a->article_code }}"
            data-uom="{{ $a->unit }}"
            @selected($a->article_code === $item->article_code)>
      {{ $a->article_code }} - {{ $a->description }}
    </option>
  @endforeach

</select>

  </td>

  <td class="border px-2 py-2 text-center">
    <input type="number"
           name="items[{{ $i }}][qty]"
           value="{{ $item->qty }}"
           class="qty-input w-full border rounded text-center">
  </td>

  <td class="border px-2 py-2 text-center">
   <input type="text"
       name="items[{{ $i }}][uom]"
       class="part-uom w-full border rounded text-center"
       value="{{ $item->article_code === 'OTHER' ? $item->uom : ($item->article->unit ?? '') }}"
       @if($item->article_code !== 'OTHER') readonly @endif>


  </td>

 <td class="border px-2 py-2 text-center">

@if(auth()->id() == 53)

    {{-- ✅ Mode Admin (Dropdown) --}}
    <select class="location-input w-full border rounded p-1">

        @foreach($allowedWarehouses as $wh)
            <option value="{{ $wh }}"
                {{ $item->location == $wh ? 'selected' : '' }}>
                {{ $wh }}
            </option>
        @endforeach

    </select>

@else

    {{-- 🔒 Mode Readonly --}}
    <input type="text"
           class="location-input w-full border bg-gray-100 rounded"
           value="{{ $item->location }}"
           readonly>

@endif

</td>

  </td>
</tr>
@endforeach
@for ($i = $rowCount; $i < $maxRow; $i++)
<tr class="bg-gray-50 sto-row">
  <td class="border px-2 py-2">
    <input type="text"
           name="items[{{ $i }}][article_code]"
           class="article-code w-full border rounded px-2 py-1"
           readonly>
  </td>

  <td class="border px-2 py-2">
  <select name="items[{{ $i }}][article_id]"
            class="part-select w-full"
            data-row="{{ $i }}">
      <option value="">-- pilih --</option>
      @foreach ($articles as $a)
        <option value="{{ $a->id }}"
                data-code="{{ $a->article_code }}"
                data-uom="{{ $a->unit }}">
          {{ $a->article_code }} - {{ $a->description }}
        </option>
      @endforeach
    </select>

  </td>

  <td class="border px-2 py-2 text-center">
    <input type="number"
           name="items[{{ $i }}][qty]"
           class="qty-input w-full border rounded text-center">
  </td>

  <td class="border px-2 py-2 text-center">
  <input type="text"
           name="items[{{ $i }}][uom]"
           class="part-uom w-full border rounded text-center"
           >
  </td>

 <td class="border px-2 py-2 text-center">

@if(auth()->id() == 53)

    {{-- ✅ Mode Admin (Dropdown) --}}
    <select class="location-input w-full border rounded p-1">

        @foreach($allowedWarehouses as $wh)
            <option value="{{ $wh }}"
                {{ $item->location == $wh ? 'selected' : '' }}>
                {{ $wh }}
            </option>
        @endforeach

    </select>

@else

    {{-- 🔒 Mode Readonly --}}
    <input type="text"
           class="location-input w-full border bg-gray-100 rounded"
           value="{{ $item->location }}"
           readonly>

@endif

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
   <textarea id="note" name="note"
          rows="3"
          class="note-lines w-full resize-none">{{ $sto->note }}</textarea>


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

  $('.part-select').select2({
    placeholder: '-- Pilih Part --',
    width: '100%',
    allowClear: true,
    tags: true,
    selectOnClose: false,
    createTag: function (params) {
      const term = $.trim(params.term);
      if (!term) return null;

      return {
        id: 'OTHER',
        text: term,
        isOther: true
      };
    },
  });

  function handleSelect($select) {
    const $row = $select.closest('tr');
    const $opt = $select.find(':selected');
    let code = $opt.data('code') || '';
    let uom  = $opt.data('uom') || '';

    // deteksi OTHER
    const isOther = $opt.data('isOther') || $select.val() === 'OTHER';

    if (isOther) {
      code = 'OTHER';
      if (!$opt.val()) uom = $row.data('other-uom') || '';
      $row.find('.part-uom').prop('readonly', false).val(uom);
    } else {
      $row.find('.part-uom').prop('readonly', true).val(uom);
    }

    $row.find('.article-code').val(code);
    $row.find('.qty-input').prop('disabled', !$select.val());
  }

  $(document).on('select2:select', '.part-select', function () {
    handleSelect($(this));
  });

  $(document).on('change', '.part-select', function () {
    handleSelect($(this));
  });

  $(document).on('select2:clear', '.part-select', function () {
    const $row = $(this).closest('tr');
    $row.find('.article-code').val('');
    $row.find('.part-uom').val('').prop('readonly', true);
    $row.find('.qty-input').val('').prop('disabled', true);
  });

  // 🔥 edit mode: trigger select2:select sesuai option yang sudah ada
  $('.part-select').each(function () {
    const $select = $(this);
    const selectedVal = $select.val();
    if (selectedVal) {
      // jika OTHER, buat option dulu supaya Select2 punya reference
      if (selectedVal === 'OTHER') {
        const $row = $select.closest('tr');
        const otherName = $row.find('.article-code').val() || '';
        const uom = $row.find('.part-uom').val() || '';
        if (!$select.find('option[value="OTHER"]').length) {
          $select.append(`<option value="OTHER" data-uom="${uom}" selected>${otherName}</option>`);
        }
      }
      $select.trigger('select2:select', { data: $select.find(':selected').data() });
    }
  });

});




$(document).ready(function () {

  $('#wip-master').on('change', function () {
    const selectedWip = $(this).val();

    if (!selectedWip) return;

    console.log('WIP MASTER SELECTED:', selectedWip);

    $('.location-input').each(function (index) {
      // baris pertama ikut juga, biar konsisten
      $(this).val(selectedWip);
    });
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
      const articleCode = $row.find('.article-code').val();
      const qty = parseFloat($row.find('.qty-input').val());
      const location = $row.find('.location-input').val();
      const uom = $row.find('.part-uom').val();

      // Ambil OTHER name jika article_code = OTHER
      let other_name = '';
      if (articleCode === 'OTHER') {
        const $select = $row.find('.part-select');
        other_name = $select.find(':selected').text() || $row.data('other_name') || '';
      }

      // 🔴 Validasi qty
      if (articleCode && (!qty || qty <= 0)) {
        hasError = true;
        errorRow = index + 1;
        return false; // break each
      }

      if (articleCode && qty > 0) {
        articles.push({
          article_code: articleCode,
          qty: qty,
          uom: uom,
          location: location,
          other_name: other_name
        });
      }
    });

    // ❌ Validasi qty = 0
    if (hasError) {
      Swal.fire({
        icon: 'warning',
        title: 'Qty tidak valid',
        text: `Qty pada baris ke-${errorRow} harus lebih dari 0`,
        confirmButtonText: 'Oke'
      });
      return;
    }

    // ❌ Tidak ada item sama sekali
    if (articles.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Data belum lengkap',
        text: 'Minimal 1 item STO harus diisi',
        confirmButtonText: 'Oke'
      });
      return;
    }

    const payload = {
      sto_number: $('#sto_number').val(),
      note: $('#note').val(),
      articles: articles,
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    console.log('=== STO PAYLOAD ===', payload);

    $.ajax({
      url: '/facility/sto/update/{{ $sto->id }}',
      method: 'PUT',
      data: payload,
      beforeSend: function () {
        $('#btnSave').prop('disabled', true).text('Saving...');
      },
      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          window.location.href = '/facility/facility/sto/index';
        });
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          let msg = '';
          $.each(errors, function (key, value) {
            msg += `• ${value[0]}\n`;
          });
          Swal.fire({
            icon: 'error',
            title: 'Validasi gagal',
            text: msg,
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat menyimpan STO',
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