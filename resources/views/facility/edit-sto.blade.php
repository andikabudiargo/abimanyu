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
  border: 1px solid var(--sto-border); border-radius: var(--sto-radius-md); padding: 0 0.5rem !important;
}
.select2-container .select2-selection__rendered { padding-left: 0 !important; padding-right: 0 !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 32px !important; font-size: 12px; color: var(--sto-text);
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px !important; right: 0.5rem; }
</style>

@push('scripts')
<script>
const IS_SUPER_USER  = @json($isSuperUser);
const IS_SECOND_USER = @json($isSecondUser);
const WAREHOUSE_VAL  = @json($warehouse ?? '');
const STO_ID         = @json($sto->id);
const ARTICLE_SELECT_URL = "{{ route('facility.article.select') }}";

// ── Scroll wheel jangan ubah angka ──────────────────────────
$(document).on('wheel', 'input[type=number]', function (e) {
    e.preventDefault(); $(this).blur();
});

// ── Select2 config ───────────────────────────────────────────
function getSelect2Config() {
    return {
        placeholder: '— Pilih Part —', width: '100%', allowClear: true,
        tags: true, selectOnClose: false, escapeMarkup: m => m,
        createTag(params) {
            const term = $.trim(params.term);
            if (!term) return null;
            return { id: '__OTHER__:' + term, text: term, isOther: true };
        },
        ajax: {
            url: ARTICLE_SELECT_URL, dataType: 'json', delay: 300,
            data(params) { return { q: params.term || null, page: params.page || 1, warehouse: WAREHOUSE_VAL || null }; },
            processResults(data) {
                return {
                    results: data.results.map(a => ({
                        id: a.id, text: a.text, code: a.article_code, uom: a.unit, minPackage: a.min_package, isOther: false,
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

// ── Part select events ───────────────────────────────────────
$(document).on('select2:select', '.part-select', function (e) {
    const data    = e.params.data;
    const $row    = $(this).closest('.sto-row');
    const row     = $(this).data('row');
    const isOther = data.isOther || String(data.id).startsWith('__OTHER__:');
    if (isOther) {
        $(`input[name="articles[${row}][article_code]"]`).val('OTHER');
        $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', false);
        $(`input[name="articles[${row}][min_package]"]`).val('');
        $(`input[name="articles[${row}][other_name]"]`).val(data.text);
    } else {
        $(`input[name="articles[${row}][article_code]"]`).val(data.code || '');
        $(`input[name="articles[${row}][uom]"]`).val(data.uom || '').prop('readonly', true);
        $(`input[name="articles[${row}][min_package]"]`).val(data.minPackage || '');
        $(`input[name="articles[${row}][other_name]"]`).val('');
    }
});

$(document).on('select2:clear', '.part-select', function () {
    const row = $(this).data('row');
    $(`input[name="articles[${row}][article_code]"]`).val('');
    $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', true);
    $(`input[name="articles[${row}][min_package]"]`).val('');
    $(`input[name="articles[${row}][other_name]"]`).val('');
});

// ── Add Row ──────────────────────────────────────────────────
let rowCount = {{ max($items->count(), 7) }};

function buildRowHtml(idx) {
    const loc = WAREHOUSE_VAL || '';
    let qtyCols = '';
    if (IS_SUPER_USER) {
        qtyCols = `
          <td class="center">
            <input type="number" min="0" name="articles[${idx}][qty]" value=""
              class="qty-input sto-input" style="text-align:center;">
          </td>
          <td class="center td-qty2">
            <input type="number" min="0" name="articles[${idx}][qty_2]" value=""
              class="qty2-input sto-input" style="text-align:center;">
          </td>`;
    } else if (IS_SECOND_USER) {
        qtyCols = `<td class="center">
            <input type="number" min="0" name="articles[${idx}][qty_2]" value=""
              class="qty2-input sto-input" style="text-align:center;"></td>`;
    } else {
        qtyCols = `<td class="center">
            <input type="number" min="0" name="articles[${idx}][qty]" value=""
              class="qty-input sto-input" style="text-align:center;"></td>`;
    }
    return `
    <tr class="sto-row" data-row="${idx}">
      <input type="hidden" name="articles[${idx}][item_id]" value="">
      <input type="hidden" name="articles[${idx}][other_name]" class="other-name-input" value="">
      <td class="center"><span class="sto-row-num">${idx + 1}</span></td>
      <td><input type="text" name="articles[${idx}][article_code]" value=""
            class="article-code sto-input readonly" readonly></td>
      <td><select class="part-select sto-select" name="articles[${idx}][article_id]" data-row="${idx}">
            <option value="">— Pilih Part —</option></select></td>
      ${qtyCols}
      <td class="center"><input type="text" name="articles[${idx}][min_package]" value=""
            class="part-min-package sto-input readonly" readonly style="text-align:center;"></td>
      <td class="center"><input type="text" name="articles[${idx}][uom]" value=""
            class="part-uom sto-input readonly" readonly style="text-align:center;"></td>
      <td class="center"><input type="text" name="articles[${idx}][location]" value="${loc}"
            readonly class="location-input sto-input readonly" style="text-align:center;"></td>
    </tr>`;
}

$('#btnAddRow').on('click', function () {
    document.getElementById('article-table').insertAdjacentHTML('beforeend', buildRowHtml(rowCount));
    rowCount++;
    initSelect2OnRows();
    if (window.feather) feather.replace();
});

// ── Document Ready ───────────────────────────────────────────
$(document).ready(function () {
    initSelect2OnRows();
    if (window.feather) feather.replace();

    // ── FORM SUBMIT ──────────────────────────────────────────
    $('#btnSave').on('click', function (e) {
        e.preventDefault();

        let articles = [], hasError = false, errorRow = 0;

        $('.sto-row').each(function (index) {
            const $row        = $(this);
            const row         = $row.data('row') ?? index;
            const itemId      = $row.find(`input[name="articles[${row}][item_id]"]`).val() || null;
            const articleCode = $row.find(`input[name="articles[${row}][article_code]"]`).val()?.trim();
            const uom         = $row.find(`input[name="articles[${row}][uom]"]`).val()?.trim();
            const location    = $row.find('.location-input').val();
            const otherName   = $row.find(`input[name="articles[${row}][other_name]"]`).val()?.trim() || null;

            // Baris kosong (tidak ada part dipilih & bukan row existing) → skip
            if (!articleCode && !itemId) return;

            const qtyRaw  = $row.find(`input[name="articles[${row}][qty]"]`).val();
            const qty2Raw = $row.find(`input[name="articles[${row}][qty_2]"]`).val();

            if (IS_SUPER_USER) {
                // Superuser: minimal salah satu harus diisi jika ada article
                if (articleCode) {
                    const bothEmpty = (qtyRaw === '' && qty2Raw === '');
                    if (bothEmpty) { hasError = true; errorRow = index + 1; return false; }
                }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : qtyRaw  !== '' ? parseFloat(qtyRaw)  : null,
                    qty_2       : qty2Raw !== '' ? parseFloat(qty2Raw) : null,
                    uom         : uom || null,
                    location,
                });
            } else if (IS_SECOND_USER) {
                if (articleCode && qty2Raw === '') { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty_2       : qty2Raw !== '' ? parseFloat(qty2Raw) : null,
                    uom         : uom || null,
                    location,
                });
            } else {
                if (articleCode && qtyRaw === '') { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : qtyRaw !== '' ? parseFloat(qtyRaw) : null,
                    uom         : uom || null,
                    location,
                });
            }
        });

        if (hasError) return Swal.fire({ icon: 'warning', title: 'Qty tidak valid', text: `Qty baris ke-${errorRow} harus diisi` });
        if (!articles.filter(a => a.article_code).length)
            return Swal.fire({ icon: 'warning', title: 'Data kosong', text: 'Minimal 1 item harus diisi' });

        const payload = {
            note    : $('#note').val() || '',
            articles,
            _token  : $('meta[name="csrf-token"]').attr('content'),
        };

        $.ajax({
            url   : `/facility/sto/update/${STO_ID}`,
            method: 'PUT',
            data  : JSON.stringify(payload),
            contentType: 'application/json',
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