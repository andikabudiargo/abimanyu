@extends('layouts.app-sto')

@section('title', 'Edit STO')
@section('page-title', 'EDIT STO')
@section('breadcrumb-item', 'Stock Opname')
@section('breadcrumb-active', 'Edit STO')

@section('content')

@php
    $authId       = auth()->id();
    $isSuperUser  = in_array($authId, [53, 2]);
    $isSecondUser = !$isSuperUser && ($sto->created_by_2 == $authId);
    $isFirstUser  = !$isSuperUser && ($sto->created_by   == $authId);
    // Superuser lihat dua kolom qty; user biasa hanya satu
@endphp

<form id="stoForm" class="space-y-6 w-full">
  @include('facility.edit-sto-desktop', compact('sto','items','articles','warehouse','allowedWarehouses','isSuperUser','isSecondUser','isFirstUser'))
</form>

<style>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type=number] { -moz-appearance: textfield; }

.select2-container { width: 100% !important; min-width: 0 !important; }
.select2-container .select2-selection--single {
  height: 32px !important; display: flex !important; align-items: center !important;
  border: 1px solid var(--sto-border); border-radius: var(--sto-radius-md);
  padding: 0 0.5rem !important;
}
.select2-container .select2-selection__rendered {
  padding-left: 0 !important; padding-right: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 32px !important; font-size: 12px; color: var(--sto-text);
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 32px !important; right: 0.5rem;
}
</style>

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════
// KONSTANTA GLOBAL
// ══════════════════════════════════════════════════════════
const IS_SUPER_USER  = @json($isSuperUser);
const IS_SECOND_USER = @json($isSecondUser);
const IS_FIRST_USER  = @json($isFirstUser);
const WAREHOUSE_VAL  = @json($warehouse ?? '');
const STO_ID         = @json($sto->id);
const ARTICLE_SELECT_URL = "{{ route('facility.article.select') }}";

// ══════════════════════════════════════════════════════════
// UTIL
// ══════════════════════════════════════════════════════════
$(document).on('wheel', 'input[type=number]', function (e) {
    e.preventDefault(); $(this).blur();
});

function getSelect2Config() {
    return {
        placeholder  : '— Pilih Part —',
        width        : '100%',
        allowClear   : true,
        tags         : true,
        selectOnClose: false,
        escapeMarkup : m => m,
        createTag(params) {
            const term = $.trim(params.term);
            if (!term) return null;
            return { id: '__OTHER__:' + term, text: term, isOther: true };
        },
        ajax: {
            url     : ARTICLE_SELECT_URL,
            dataType: 'json',
            delay   : 300,
            data(params) {
                return { q: params.term || null, page: params.page || 1, warehouse: WAREHOUSE_VAL || null };
            },
            processResults(data) {
                return {
                    results   : data.results.map(a => ({
                        id        : a.id,
                        text      : a.text,
                        code      : a.article_code,
                        uom       : a.unit,
                        minPackage: a.min_package,
                        isOther   : false,
                    })),
                    pagination: { more: data.pagination.more },
                };
            },
        },
    };
}

function initSelect2OnRows() {
    $('.part-select').not('.select2-hidden-accessible').select2(getSelect2Config());
}

// ══════════════════════════════════════════════════════════
// PART SELECT EVENTS
// ══════════════════════════════════════════════════════════
$(document).on('select2:select', '.part-select', function (e) {
    const data    = e.params.data;
    const $row    = $(this).closest('.sto-row');
    const row     = $(this).data('row');
    const isOther = data.isOther || String(data.id).startsWith('__OTHER__:');
    const $code   = $(`input[name="articles[${row}][article_code]"]`);
    const $uom    = $(`input[name="articles[${row}][uom]"]`);
    const $minPkg = $(`input[name="articles[${row}][min_package]"]`);
    const $other  = $(`input[name="articles[${row}][other_name]"]`);

    if (isOther) {
        $code.val('OTHER');
        $uom.val('').prop('readonly', false);
        $minPkg.val('');
        $other.val(data.text);
    } else {
        $code.val(data.code || '');
        $uom.val(data.uom || '');
        $minPkg.val(data.minPackage || '');
        $other.val('');
        $uom.prop('readonly', true);
    }
});

$(document).on('select2:clear', '.part-select', function () {
    const $row = $(this).closest('.sto-row');
    const row  = $(this).data('row');
    $(`input[name="articles[${row}][article_code]"]`).val('');
    $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', true);
    $(`input[name="articles[${row}][min_package]"]`).val('');
    $(`input[name="articles[${row}][other_name]"]`).val('');
});

// ══════════════════════════════════════════════════════════
// ADD ROW
// ══════════════════════════════════════════════════════════
let rowCount = {{ $items->count() < 7 ? 7 : $items->count() }};

function buildRowHtml(idx) {
    const loc = WAREHOUSE_VAL || '';

    // Kolom qty sesuai role
    let qtyCols = '';
    if (IS_SUPER_USER) {
        qtyCols = `
          <td class="center">
            <input type="number" min="0" name="articles[${idx}][qty]" value=""
              class="qty-input sto-input" style="text-align:center;" placeholder="Qty 1">
          </td>
          <td class="center">
            <input type="number" min="0" name="articles[${idx}][qty_2]" value=""
              class="qty2-input sto-input" style="text-align:center; background:var(--sto-green-light);" placeholder="Qty 2">
          </td>`;
    } else if (IS_SECOND_USER) {
        qtyCols = `
          <td class="center">
            <input type="number" min="0" name="articles[${idx}][qty_2]" value=""
              class="qty2-input sto-input" style="text-align:center;" placeholder="Qty">
          </td>`;
    } else {
        qtyCols = `
          <td class="center">
            <input type="number" min="0" name="articles[${idx}][qty]" value=""
              class="qty-input sto-input" style="text-align:center;" placeholder="Qty">
          </td>`;
    }

    return `
    <tr class="sto-row" data-row="${idx}">
      <input type="hidden" name="articles[${idx}][other_name]" class="other-name-input">
      <td class="center"><span class="sto-row-num">${idx + 1}</span></td>
      <td>
        <input type="text" name="articles[${idx}][article_code]"
          value="" class="article-code sto-input readonly" readonly>
      </td>
      <td>
        <select class="part-select sto-select" name="articles[${idx}][article_id]" data-row="${idx}">
          <option value="">— Pilih Part —</option>
        </select>
      </td>
      ${qtyCols}
      <td class="center">
        <input type="text" name="articles[${idx}][min_package]" value=""
          class="part-min-package sto-input readonly" readonly style="text-align:center;">
      </td>
      <td class="center">
        <input type="text" name="articles[${idx}][uom]" value=""
          class="part-uom sto-input readonly" readonly style="text-align:center;">
      </td>
      <td class="center">
        <input type="text" name="articles[${idx}][location]" value="${loc}"
          readonly class="location-input sto-input readonly" style="text-align:center;">
      </td>
    </tr>`;
}

$('#btnAddRow').on('click', function () {
    const tbody = document.getElementById('article-table');
    if (!tbody) return;
    tbody.insertAdjacentHTML('beforeend', buildRowHtml(rowCount));
    rowCount++;
    initSelect2OnRows();
    if (window.feather) feather.replace();
});

// ══════════════════════════════════════════════════════════
// DOCUMENT READY
// ══════════════════════════════════════════════════════════
$(document).ready(function () {
    initSelect2OnRows();

    // Init select2 on existing rows (edit mode)
    $('.part-select').each(function () {
        const $select = $(this);
        if ($select.hasClass('select2-hidden-accessible')) return;
        $select.select2(getSelect2Config());

        // Trigger ulang untuk row yang sudah punya value
        const val = $select.val();
        if (val) {
            if (val === 'OTHER') {
                const $row    = $select.closest('tr');
                const uom     = $row.find('.part-uom').val() || '';
                const othName = $row.find('.other-name-input').val() || $row.find('.article-code').val() || '';
                if (!$select.find('option[value="OTHER"]').length) {
                    $select.append(`<option value="OTHER" selected>${othName}</option>`);
                }
                $row.find('.part-uom').prop('readonly', false);
            }
        }
    });

    if (window.feather) feather.replace();

    // ── FORM SUBMIT ───────────────────────────────────────
    $('#btnSave').on('click', function (e) {
        e.preventDefault();

        let articles = [], hasError = false, errorRow = 0;

        $('.sto-row').each(function (index) {
            const $row        = $(this);
            const row         = $row.data('row') ?? index;
            const articleCode = $row.find(`input[name="articles[${row}][article_code]"]`).val()?.trim();
            const uom         = $row.find(`input[name="articles[${row}][uom]"]`).val()?.trim();
            const location    = $row.find(`.location-input`).val();
            const otherName   = $row.find(`input[name="articles[${row}][other_name]"]`).val()?.trim() || null;

            // Ambil qty sesuai kolom yang ada
            const qtyRaw  = $row.find(`input[name="articles[${row}][qty]"]`).val();
            const qty2Raw = $row.find(`input[name="articles[${row}][qty_2]"]`).val();

            // Skip baris benar-benar kosong
            if (!articleCode) return;

            // Validasi: superuser boleh salah satu diisi; user biasa wajib isi qtynya
            if (IS_SUPER_USER) {
                const qtyVal  = parseFloat(qtyRaw);
                const qty2Val = parseFloat(qty2Raw);
                const bothEmpty = (qtyRaw === '' || isNaN(qtyVal)) && (qty2Raw === '' || isNaN(qty2Val));
                if (bothEmpty) { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : isNaN(parseFloat(qtyRaw))  ? null : parseFloat(qtyRaw),
                    qty_2       : isNaN(parseFloat(qty2Raw)) ? null : parseFloat(qty2Raw),
                    uom         : uom || null,
                    location,
                });
            } else if (IS_SECOND_USER) {
                const qty2Val = parseFloat(qty2Raw);
                if (qty2Raw === '' || isNaN(qty2Val) || qty2Val < 0) { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty_2       : qty2Val,
                    uom         : uom || null,
                    location,
                });
            } else {
                const qtyVal = parseFloat(qtyRaw);
                if (qtyRaw === '' || isNaN(qtyVal) || qtyVal < 0) { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : qtyVal,
                    uom         : uom || null,
                    location,
                });
            }
        });

        if (hasError) return Swal.fire({ icon: 'warning', title: 'Qty tidak valid', text: `Qty baris ke-${errorRow} harus diisi` });
        if (!articles.length) return Swal.fire({ icon: 'warning', title: 'Data kosong', text: 'Minimal 1 item harus diisi' });

        const payload = {
            note    : $('#note').val() || '',
            articles,
            _token  : $('meta[name="csrf-token"]').attr('content'),
        };

        $.ajax({
            url   : `/facility/sto/update/${STO_ID}`,
            method: 'PUT',
            data  : payload,
            beforeSend() { $('#btnSave').prop('disabled', true).text('Saving...'); },
            success(res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false })
                    .then(() => window.location.href = '/facility/facility/sto/index');
            },
            error(xhr) {
                if (xhr.status === 422) {
                    let msg = '';
                    $.each(xhr.responseJSON.errors, (_, v) => { msg += `• ${v[0]}\n`; });
                    Swal.fire({ icon: 'error', title: 'Validasi gagal', text: msg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
                }
            },
            complete() { $('#btnSave').prop('disabled', false).text('Save'); },
        });
    });
});
</script>
@endpush

@endsection