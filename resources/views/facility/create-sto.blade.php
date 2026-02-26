@extends('layouts.app-sto')

@section('title', 'Create STO')
@section('page-title', 'CREATE STO')
@section('breadcrumb-item', 'Stock Opname')
@section('breadcrumb-active', 'Create STO')
@section('content')




<form id="stoForm" class="space-y-6 w-full">
   @if (Agent::isMobile())
    @include('facility.create-sto-mobile')
@else
    @include('facility.create-sto-desktop')
@endif
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
        minPackage: a.min_package, // tambahkan di JS
        isOther: false
      })),
      pagination: data.pagination
    };
  }
}
});


function toggleUomByLocation($row) {
  const locationValue =
  ($row.find('.location-input').val() || '').trim();
  const $uomInput = $row.find('.part-uom');

  // hanya unlock jika Chemical/Dead Stock CM1
 if (
    locationValue === "Chemical" ||
    locationValue === "Dead Stock CM1"
) {
    $uomInput.prop('readonly', false)
             .removeClass('bg-gray-50')
             .addClass('bg-white');
  } else {
    $uomInput.prop('readonly', true)
             .addClass('bg-gray-50')
             .removeClass('bg-white');
  }
}

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
  const $minPackageInput = $(`input[name="articles[${row}][min_package]"]`);
  const $otherInput = $(`input[name="articles[${row}][other_name]"]`);

  if (isOther) {

    $codeInput.val('OTHER');

    $uomInput
      .val('')
      .prop('readonly', false);

    $minPackageInput
      .val(data.minPackage || $(this).find(':selected').data('min-package') || '')
      .prop('readonly', true);

    $otherInput.val(data.text);
    $header.text(data.text);

  } else {

    $codeInput.val(data.code || data.id || '');
    $uomInput.val(data.uom || '').prop('readonly', true);

    $minPackageInput
      .val(data.minPackage || $(this).find(':selected').data('min-package') || '')
      .prop('readonly', true);

    $otherInput.val('');
    $header.text(data.text);
  }

  // ✅ CEK LOCATION → override readonly jika Chemical
  toggleUomByLocation($row);

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
  toggleUomByLocation($row);
});


$(document).on('change', '#warehouse-null, #warehouse-null-desktop, #wip-master', function () {

  const selectedLocation = $(this).val();

  if (!selectedLocation) {
    $('.location-input').val('');
    return;
  }

  $('.location-input').val(selectedLocation);

  // re-check semua row
  $('.sto-row').each(function () {
    toggleUomByLocation($(this));
  });

});
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
                data-uom="${a.unit}"
                data-min-package="${a.min_package}">
                ${a.article_code} - ${a.description}
              </option>`
            );
          });
          select.val(null).trigger('change');
      });
    });
  }

  loadArticles(null);

  $('#warehouse-null', '#warehouse-null-desktop').on('change', function () {
    const warehouse = $(this).val() || null;
      loadArticles(warehouse);
  });

});



$(document).ready(function () {
  $('#sto_number').select2({
    placeholder: '-- Pilih STO Number --',
    width: '100%'
  });
  $('#sto_number_mobile').select2({
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

function getStoNumber() {
  if ($(window).width() < 1024) {
    return $('#sto_number_mobile').val();
  } else {
    return $('#sto_number').val();
  }
}


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
       sto_number: getStoNumber(),
      note: $('#note, #note_mobile').val() || '',
      articles: articles,
      _token: $('meta[name="csrf-token"]').attr('content')
    };


    $.ajax({
      url: '/facility/sto/save',
      method: 'POST',
      data: payload,
      beforeSend: function () {
        $('#btnSave, #btnSaveMobile').prop('disabled', true).text('Saving...');
      },
     success: function (res) {

  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: res.message,
    timer: 2000,
    showConfirmButton: false
  });

  const usedValue = payload.sto_number;

  // Hapus option
  $('#sto_number_mobile option[value="'+usedValue+'"]').remove();
  $('#sto_number option[value="'+usedValue+'"]').remove();

  // Reset select STO
  $('#sto_number_mobile, #sto_number')
    .val('')
    .trigger('change');

  // Reset row
  $('.sto-row').each(function (i) {

    const $r = $(this);

    $r.find('.part-select')
      .val('')
      .trigger('change.select2')
      .trigger('change');

    $r.find('.article-code').val('');
    $r.find('.part-uom').val('');
    $r.find('input[name$="[qty]"]').val('');
    $r.find('.location-input').val('{{ $warehouse }}');

    $r.find('.header-label').text(`Item ${i + 1}`);
  });

  // Reset note
  $('#note, #note_mobile').val('');
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