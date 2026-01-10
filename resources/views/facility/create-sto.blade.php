@extends('layouts.app-sto')

@section('title', 'Create STO')
@section('page-title', 'CREATE STO')
@section('breadcrumb-item', 'Stock Opname')
@section('breadcrumb-active', 'Create STO')
@section('content')


  <!-- MOBILE VERSION -->
  <div class="lg:hidden relative z-20 -mt-20 overflow-hidden w-full bg-white p-6 space-y-4 rounded-t-md sm:rounded-2xl shadow-lg">
    <!-- MOBILE VERSION (REPLACE SELECT WITH HEADING) -->
    <div class="sm:hidden relative pb-4">
      <span class="text-lg font-semibold text-gray-900">🏷️ Electronic Stock Opname</span>
      <span class="text-xs text-gray-500 mt-1">(e-STO)</span>
    </div>
    <!-- MOBILE MODE (CARD STYLE) -->
  <div class="sm:hidden col-span-2 space-y-4">
  <form id="stoForm" class="space-y-6 w-full">
    @for ($i = 0; $i < 15; $i++)
    <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 overflow-hidden sto-row" data-row="{{ $i }}">

      <!-- HEADER CARD -->
      <div class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2">
        <span class="header-label text-sm font-semibold">❖ Item {{ $i + 1 }}</span>
      </div>

      <div class="p-4">

        <!-- PART NAME -->
        <label class="text-xs font-semibold text-gray-600 mb-1">Nama Part</label>
        <select class="part-select w-full mt-1"
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
  <input type="hidden"
        name="articles[{{ $i }}][other_name]"
        class="other-name-input">
        <!-- PART CODE -->
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Kode Part</label>
        <input type="text"
              name="articles[{{ $i }}][article_code]"
              class="article-code w-full border rounded px-2 py-1 bg-gray-100"
              readonly>

        <!-- QTY + UOM in one row -->
        <div class="grid grid-cols-2 gap-3 mt-3">
          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty</label>
            <input type="number"
                  min="0"
                  name="articles[{{ $i }}][qty]"
                  class="w-full border rounded px-2 py-1">
          </div>

          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">UOM</label>
            <input type="text"
                  name="articles[{{ $i }}][uom]"
                  class="part-uom w-full border rounded px-2 py-1 bg-gray-100"
                  readonly>
          </div>
        </div>

        <!-- LOCATION -->
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Location</label>
        <input type="text"
              name="articles[{{ $i }}][location]"
              value="{{ $warehouse }}"
              readonly
              class="location-input w-full bg-gray-100 border rounded px-2 py-1">

      </div>

    </div>
    @endfor

  </div>

  <!-- MOBILE VIEW -->
  <div class="sm:hidden mb-4">
    <h2 class="text-lg font-semibold text-gray-700 tracking-wide drop-shadow">
      Catatan
    </h2>

    <textarea
      id="note_mobile"
      name="note"
      rows="3"
      class="w-full mt-3 p-3 rounded-lg bg-gray/10 text-gray-700 placeholder-gray/50 border border-gray/20 backdrop-blur focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
      placeholder="Tambahkan catatan di sini..."
    ></textarea>
  </div>

  <!-- MOBILE BUTTONS -->
  <div class="sm:hidden w-full space-y-3">

    <button type="submit" id="btnSaveMobile"
            class="w-full bg-green-600 text-white py-3 rounded-lg text-lg font-semibold shadow-md">
      Save
    </button>

    <a href="{{ url()->previous() }}"
      class="w-full block text-center bg-gray-200 text-gray-700 py-3 rounded-lg text-lg font-semibold shadow-md">
      Back
    </a>

  </div>

  </div>


    <!-- DESKTOP VERSION -->
  <div class="hidden lg:block pc-container ml-[264px] p-6 min-h-screen">
    <!-- Page Header: hidden di mobile & tablet -->
  <div class="page-header">
    <div class="page-block flex items-center justify-start lg:justify-between gap-4">
      
      <!-- Page Title -->
      <div class="page-header-title hidden lg:block">
        <h5 class="mb-0 font-medium">@yield('page-title', 'Dashboard')</h5>
      </div>

      <!-- Breadcrumb -->
      <ul class="mb-0 text-xs text-gray-500 flex items-center">
        <li class="flex items-center">
          <a href="{{ url('/') }}" class="text-gray-600 hover:underline">
            <i data-feather="home" class="w-4 h-4"></i>
          </a>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="flex items-center">
          <span>@yield('breadcrumb-item')</span>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="text-gray-800 font-medium">@yield('breadcrumb-active')</li>
      </ul>
      
    </div>
  </div>

    <div class="w-full bg-white p-0 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

      <!-- HEADER BAR -->
      <div class="bg-blue-500 px-10 py-8 border-b border-blue-600">
        <h1 class="text-3xl font-semibold tracking-wide text-white">
          ELECTRONIC STOCK OPNAME (e-STO)
        </h1>
        <p class="text-sm text-white mt-1 opacity-80">
          Sistem Pencatatan Stock Opname Digital
        </p>
      </div>

      <!-- CONTENT WRAPPER -->
      <div class="p-10">

        <!-- TOP GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 mb-10 items-center">
          <div class="flex-shrink-0">
            <img src="{{ asset('img/logo-2.jpg') }}"
              alt="Company Logo"
              class="h-14 sm:h-20 w-auto opacity-90">
          </div>

        
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-1">STO Number</label>
              <select name="sto_number" id="sto_number"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#8b5cf6] focus:border-[#8b5cf6] bg-gray-50 text-gray-700">
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
                    <option value="{{ $val }}">{{ $val }}</option>
                  @endif
                @endfor
              </select>
            </div>
        
        </div>


        <!-- TABLE SECTION -->
        <div class="hidden sm:block mb-10">
          <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white shadow-xs">
            <table class="w-full text-sm">
              <thead class="bg-blue-500 text-white border-b border-blue-600">
                <tr>
                  <th class="px-4 py-3 text-left font-medium">PART CODE</th>
                  <th class="px-4 py-3 text-left font-medium">PART NAME</th>
                  <th class="px-4 py-3 text-center font-medium w-32">QTY</th>
                  <th class="px-4 py-3 text-center font-medium w-32">UOM</th>
                  <th class="px-4 py-3 text-center font-medium w-48">LOCATION

  @if(in_array(auth()->id(), [2, 67, 53, 92]))
    
    @if($warehouse === null)
      <select name="warehouse"
              id="warehouse-null-desktop"
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

              <tbody id="article-table" class="divide-y divide-gray-100">
                @for ($i = 0; $i < 15; $i++)
                  <tr class="sto-row">
                    <input type="hidden" name="articles[{{ $i }}][other_name]" class="other-name-input">

                    <td class="px-3 py-2">
                      <input type="text"
                        name="articles[{{ $i }}][article_code]"
                        class="article-code w-full border border-gray-300 rounded-lg px-2 py-1 bg-gray-50"
                        readonly>
                    </td>

                    <td class="px-3 py-2">
                      <select class="part-select w-full border-gray-300 rounded-lg focus:ring-[#a78bfa] focus:border-[#a78bfa]"
                          name="articles[{{ $i }}][article_id]"
                          data-row="{{ $i }}">
                        <option value=""> -- pilih part -- </option>
                        @foreach ($articles as $a)
                          <option value="{{ $a->id }}"
                            data-code="{{ $a->article_code }}"
                            data-uom="{{ $a->unit }}">
                            {{ $a->description }}
                          </option>
                        @endforeach
                      </select>
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="number" min="0"
                        name="articles[{{ $i }}][qty]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1 text-center">
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="text"
                        name="articles[{{ $i }}][uom]"
                        class="part-uom w-full border border-gray-300 rounded-lg px-2 py-1 text-center bg-gray-50"
                        readonly>
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="text"
                        name="articles[{{ $i }}][location]"
                        value="{{ $warehouse }}"
                        readonly
                        class="location-input w-full bg-gray-100 border border-gray-300 rounded-lg px-2 py-1">
                    </td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </div>



        <!-- NOTE + BUTTONS -->
        <div class="flex flex-col md:flex-row md:justify-between gap-10">

          <div class="w-full md:w-2/3">
            <label class="block text-sm font-medium text-gray-600 mb-2">
              Catatan / Note
            </label>
            <textarea id="note" name="note" rows="3"
              class="w-full resize-none border border-gray-300 rounded-xl p-3 bg-gray-50 focus:ring-[#a78bfa] focus:border-[#a78bfa]"></textarea>
          </div>

          <div class="flex justify-end gap-4 self-end">
            <a href="{{ url()->previous() }}"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">
              Back
            </a>

            <button type="submit" id="btnSave"
              class="px-6 py-2 bg-green-500 text-white rounded-lg shadow-sm hover:bg-green-600 transition">
              Save
            </button>
          </div>
    </form>
        </div>

      </div>
    </div>
  </div>


  

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
  escapeMarkup: m => m,

  createTag: function (params) {
    const term = $.trim(params.term);
    if (!term) return null;
    return {
      id: '__OTHER__:' + term,
      text: term,
      isOther: true
    };
  },

 ajax: {
  url: "{{ route('facility.article.select') }}",
  dataType: 'json',
  delay: 300,
  data: function (params) {
    return {
      q: params.term || null,
      page: params.page || 1,
    warehouse: $('#warehouse-null').val() || $('#warehouse-null-desktop').val() || null

    };
  },

  processResults: function (data) {
    return {
      results: data.results.map(a => ({
        id: a.id,
        text: a.text,
        code: a.article_code,
        uom: a.unit,
        isOther: false
      })),
      pagination: data.pagination
    };
  }
}
});


  // =====================
  // SELECT HANDLER
  // =====================
  $(document).on('select2:select', '.part-select', function (e) {
  const data = e.params.data;
  const $row = $(this).closest('.sto-row');
  const row  = $(this).data('row');
  const $header = $row.find('.header-label');
  const isOther = data.isOther || String(data.id).startsWith('__OTHER__:');
  const $codeInput  = $(`input[name="articles[${row}][article_code]"]`);
  const $uomInput   = $(`input[name="articles[${row}][uom]"]`);
  const $otherInput = $(`input[name="articles[${row}][other_name]"]`);

  if (isOther) {

    $codeInput.val('OTHER');

    $uomInput
      .val('')
      .prop('readonly', false);

    $otherInput.val(data.text);

     // 🔥 update header label
    $header.text(data.text);
  } else {

    $codeInput.val(data.code || data.id || '');
    $uomInput.val(data.uom || '').prop('readonly', true);
    $otherInput.val('');
     // 🔥 update header label
    $header.text(data.text);
  }

  $row.find('.qty-input').prop('disabled', false);
});

  // =====================
  // CLEAR HANDLER
  // =====================
$(document).on('select2:clear', '.part-select', function () {
  const $select = $(this);
  const $row = $select.closest('.sto-row');
  const row     = $select.data('row');

  // reset select2
  $select.val(null).trigger('change');

  // hapus ONLY opsi OTHER (ketikan manual)
  $select.find('option').filter(function () {
    return this.value && this.value.startsWith('__OTHER__:' );
  }).remove();

  // reset inputs
  $(`input[name="articles[${row}][article_code]"]`).val('');
  $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', true);
  $(`input[name="articles[${row}][other_name]"]`).val('');

  $row.find('.qty-input').val('').prop('disabled', true);

  // 🔥 RESET HEADER LABEL ke default
  const $header = $row.find('.header-label');
  $header.text(`Item ${row + 1}`);
});
});

$(document).on('change', '#warehouse-null, #warehouse-null-desktop, #wip-master', function () {
  const selectedLocation = $(this).val();
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
              </option>`
            );
          });
          select.val(null).trigger('change');
      });
    });
  }

  loadArticles(null);

  $('#warehouse-null', 'warehouse-null-desktop').on('change', function () {
    const warehouse = $(this).val() || null;
      loadArticles(warehouse);
  });

});



$(document).ready(function () {
  $('#sto_number').select2({
    placeholder: '-- Pilih STO Number --',
    width: '100%'
  });
   $('#warehouse-null').select2({
    placeholder: '-- Pilih Lokasi --',
    width: '100%',
    allowClear: true,
  });
    $('#warehouse-null-desktop').select2({
    placeholder: '-- Pilih Lokasi --',
    width: '100%',
  });
});

$(document).ready(function () {

  $("#btnSave, #btnSaveMobile").on("click", function (e) {
    e.preventDefault();

    let articles = [];
    let hasError = false;
    let errorRow = 0;

 $('.sto-row').each(function (index) {

  const $row = $(this);

  const articleId   = $row.find('.part-select').val();
  const articleCode = $row.find('.article-code').val()?.trim();
  const qtyRaw      = $row.find('input[name$="[qty]"]').val();
  const qty         = parseFloat(qtyRaw);
  const location    = $row.find('.location-input').val();
  const uom         = $row.find('.part-uom').val()?.trim();
 const otherName = $row.find('input[name$="[other_name]"]').val()?.trim() || null;


  // BARIS DIISI jika user memilih part
  if (!articleId) return; // skip baris kosong

  // VALIDASI QTY
  if (isNaN(qty) || qty <= 0) {
    hasError = true;
    errorRow = index + 1;
    return false;
  }

  // PUSH DATA
  articles.push({
    article_code: articleCode,
    other_name  : articleCode === 'OTHER' ? otherName : null,
    qty         : qty,
    uom         : uom || null,
    location    : location
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
      note: $('#note, #note_mobile').val() || '',
      articles: articles,
      _token: $('meta[name="csrf-token"]').attr('content')
    };


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
const $header = $row.find('.header-label');
  $header.text(`Item ${row + 1}`);
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