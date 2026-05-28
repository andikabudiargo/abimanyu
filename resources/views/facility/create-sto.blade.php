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
    @include('facility.create-sto-desktop-2')
  @endif
</form>

<style>
/* ── Spinner angka ─────────────────────── */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type=number] { -moz-appearance: textfield; }

/* ── Select2 sizing ────────────────────── */
.select2-container { width: 100% !important; min-width: 0 !important; }
.select2-container .select2-selection--single {
  height: 42px !important;
  display: flex !important;
  align-items: center !important;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0 0.75rem !important;
}
.select2-container .select2-selection__rendered {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 42px !important;
  font-size: 15px;
  color: #374151;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 42px !important;
  right: 0.75rem;
}
</style>

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════
// KONSTANTA GLOBAL
// ══════════════════════════════════════════════════════════
const IS_CHEM_CONS       = @json(in_array($warehouse ?? null, ['Chemical', 'Consumable']));
const IS_CHEM_ONLY       = @json(($warehouse ?? null) === 'Chemical');
const WAREHOUSE_VAL      = @json($warehouse ?? '');
const AREA_URL           = '/facility/sto/reference/areas';
const ITEMS_URL          = '/facility/sto/reference/items';
const ARTICLE_SELECT_URL = "{{ route('facility.article.select') }}";

let desktopRowCount = IS_CHEM_CONS ? 1 : 7;
let mobileRowCount  = IS_CHEM_CONS ? 1 : 7;
let shelvesLoaded = false;
// 'default' | 'area' | 'shelf'
let tableMode = 'default';
let lastArea = null;

$(document).on('change', '#area_desktop, #area_mobile', function () {
    lastArea = $(this).val();
});

// Cache area → shelves+items, hindari re-fetch
const areaCache = {};

// ══════════════════════════════════════════════════════════
// UTIL
// ══════════════════════════════════════════════════════════
$(document).on('wheel', 'input[type=number]', function (e) {
    e.preventDefault();
    $(this).blur();
});

function toggleUomByLocation($row) {
    const row      = $row.find('.part-select').data('row');
    const $uom     = $row.find(`input[name="articles[${row}][uom]"]`);
    const code     = ($row.find(`input[name="articles[${row}][article_code]"]`).val() || '').toUpperCase();
    const loc      = ($row.find('.location-input').val() || '').toLowerCase();
    const editable = loc.includes('Chemical') || loc.includes('Consumable');
    if (code === 'OTHER') { $uom.prop('readonly', false); return; }
    $uom.prop('readonly', !editable);
}



function autoKondisi($row) {
    if (!IS_CHEM_ONLY) return;

    const row    = $row.find('.part-select').data('row');
    const qty    = parseFloat($row.find(`input[name="articles[${row}][qty]"]`).val()) || 0;
    const minPkg = parseFloat($row.find('.part-min-package').val()) || 0;
    const $input = $row.find('.kondisi-input');
    const $label = $row.find('.kondisi-label');
    const $uom   = $row.find('.part-uom');

    if (minPkg <= 0 || qty <= 0) {
        $input.val('');
        // Mobile pakai div, desktop pakai span — handle keduanya
        $label.text('—').css('color', $label.is('div') ? '#9ca3af' : 'var(--sto-text-muted)');
        return;
    }

    let kondisi;
    if (qty < minPkg) {
        kondisi = 'Tidak Utuh';
    } else if (Math.abs(qty % minPkg) < 0.0001) {
        kondisi = 'Utuh';
    } else {
        kondisi = 'Tidak Utuh';
    }

    $input.val(kondisi);

    // Warna kondisi label — sama untuk desktop (span) & mobile (div)
    const colorUtuh     = $label.is('div') ? '#2E7D32' : 'var(--sto-green-mid)';
    const colorTidakUtuh = '#E53935';
    $label.text(kondisi).css('color', kondisi === 'Utuh' ? colorUtuh : colorTidakUtuh);

    // ── UOM logic ───────────────────────────────────────────
    if (kondisi === 'Tidak Utuh') {
        $uom.val('KG');
    } else {
        // Utuh → kembalikan ke original UOM
        const originalUom = $uom.data('original-uom') || $uom.attr('data-original-uom') || $uom.val();
        $uom.val(originalUom);
    }
}


// ══════════════════════════════════════════════════════════
// SELECT2
// ══════════════════════════════════════════════════════════
function getSelect2Config(warehouseSelector) {
    return {
        placeholder: '— Pilih Part —',
        width: '100%',
        allowClear: true,
        tags: true,
        selectOnClose: false,
        escapeMarkup: m => m,
        createTag(params) {
            const term = $.trim(params.term);
            if (!term) return null;
            return { id: '__OTHER__:' + term, text: term, isOther: true };
        },
        ajax: {
            url: ARTICLE_SELECT_URL,
            dataType: 'json',
            delay: 300,
            data(params) {
                return {
                    q        : params.term || null,
                    page     : params.page || 1,
                    warehouse: $(warehouseSelector).val() || WAREHOUSE_VAL || null,
                };
            },
            processResults(data) {
                return {
                    results: data.results.map(a => ({
                        id        : a.id,
                        text      : a.text,
                        code      : a.article_code,
                        uom       : a.unit,
                        minPackage: a.min_package,
                        isOther   : false,
                    })),
                   pagination: {
            more: data.pagination.more  // ← harus object { more: true/false }
        },
                };
            },
        },
    };
}

function initSelect2OnRows() {
    $('.part-select.sto-select').not('.select2-hidden-accessible')
        .select2(getSelect2Config('#warehouse-null-desktop, #warehouse-null-desktop-th'));
    $('.part-select:not(.sto-select)').not('.select2-hidden-accessible')
        .select2(getSelect2Config('#warehouse-null'));
}

// ══════════════════════════════════════════════════════════
// BUILD ROW HTML — dengan dropdown address untuk baris manual
// ══════════════════════════════════════════════════════════
function buildDesktopRowHtml(idx, opts = {}) {
    const { 
        articleCode = '', uom = '', minPackage = '', qty = '',
        isRef = false, location = WAREHOUSE_VAL, kondisi = '',
        shelvesOptions = ''
    } = opts;

    const showAddrDropdown = tableMode === 'area' && !isRef;

    // 🔥 DETEKSI: ini row baru (Add Row)
    const isNewRow = true;

    // 🔥 ADDRESS CELL (SELALU ADA TD)
    const addrCell = showAddrDropdown
        ? `<td class="center td-addr-desktop">
             <select class="addr-select sto-select" name="articles[${idx}][shelf_id]" 
                     data-row="${idx}" style="font-size:11px; height:32px;">
               <option value="">— pilih —</option>
               ${shelvesOptions}
             </select>
           </td>`
        : `<td class="center td-addr-desktop" style="display:none;">
             <span class="row-addr-label">—</span>
           </td>`;

    // 🔥 ACTION CELL (SELALU ADA TD, TAPI ISI CONDITIONAL)
    const actionCell = `
      <td class="center td-action-col" style="${showAddrDropdown ? '' : 'display:none'}">
        ${showAddrDropdown && isNewRow ? `
          <button type="button" class="btn-sync-item sto-btn sto-btn-add" data-row="${idx}"
                  style="font-size:10px; padding:4px 8px; height:26px;" title="Sync ke Address">
            <i data-feather="link" class="w-3 h-3"></i> Sync
          </button>
        ` : ''}
      </td>`;

    return `
    <tr class="sto-row" data-is-ref="${isRef ? 1 : 0}" data-is-manual="${!isRef ? 1 : 0}">
      <input type="hidden" name="articles[${idx}][other_name]" class="other-name-input">

      <td class="center"><span class="sto-row-num">${idx + 1}</span></td>

      <td>
        <input type="text" name="articles[${idx}][article_code]"
          value="${articleCode}" class="article-code sto-input readonly" readonly>
      </td>

      <td>
        <select class="part-select sto-select" name="articles[${idx}][article_id]" data-row="${idx}">
          <option value="">— pilih part —</option>
        </select>
      </td>

      <!-- QTY -->
      <td class="center td-qty-desktop" style="${tableMode === 'area' ? 'display:none' : ''}">
        <input type="number" min="0" name="articles[${idx}][qty]" value="${qty}"
          class="qty-input sto-input" style="text-align:center;">
      </td>

      <!-- PACKING -->
      <td class="center">
        <input type="text" name="articles[${idx}][min_package]" value="${minPackage}"
          class="part-min-package sto-input" style="text-align:center;">
      </td>

      <!-- UOM -->
      <td class="center">
       <input type="text" name="articles[${idx}][uom]" value="${uom}"
  class="part-uom sto-input ${IS_CHEM_CONS ? '' : 'readonly'}"
  ${IS_CHEM_CONS ? '' : 'readonly'}
  data-ref-uom="${isRef ? uom : ''}"
  data-original-uom="${uom}"
  style="text-align:center;">
      </td>

      <!-- KONDISI -->
      ${IS_CHEM_ONLY ? `
       <td class="center">
         <input type="hidden" name="articles[${idx}][kondisi]" class="kondisi-input" value="${kondisi}">
         <span class="kondisi-label sto-input readonly"
          style="display:block;text-align:center;line-height:32px;height:32px;
                 font-size:11px;border:1px solid var(--sto-border);
                 border-radius:var(--sto-radius-md);background:var(--sto-surface);
                 color:${kondisi === 'Utuh' ? 'var(--sto-green-mid)' : kondisi === 'Tidak Utuh' ? '#E53935' : 'var(--sto-text-muted)'};">
          ${kondisi || '—'}</span>
     </td>` : ''}

      <!-- ADDRESS -->
      ${addrCell}

      <!-- LOCATION -->
      <td class="center">
        <input type="text" name="articles[${idx}][location]" value="${location}"
          readonly class="location-input sto-input readonly" style="text-align:center;">
      </td>

      <!-- ACTION -->
      ${actionCell}
    </tr>`;
}



// ══════════════════════════════════════════════════════════
// HELPER: Build shelf options HTML dari cache
// ══════════════════════════════════════════════════════════
function buildShelfOptionsHtml(warehouse, area) {
    const cacheKey = `${warehouse}|${area}`;
    const cached = areaCache[cacheKey];
    if (!cached || !cached.shelves) return '';
    
    return cached.shelves
        .filter(s => !s.all_saved)
        .map(s => `<option value="${s.id}" data-shelves="${s.shelves}">${s.shelves}</option>`)
        .join('');
}

// ══════════════════════════════════════════════════════════
// ADD ROW — dengan dropdown address saat mode area
// ══════════════════════════════════════════════════════════
$(document)
  .off('click', '#btnAddRow')
  .on('click', '#btnAddRow', function (e) {
    e.preventDefault();
 
    const tbody = document.getElementById('article-table');
    if (!tbody) return;
 
    const area = document.getElementById('area_desktop')?.value || '';
    let shelvesOptions = '';
    if (tableMode === 'area' && area) {
        shelvesOptions = buildShelfOptionsHtml(WAREHOUSE_VAL, area);
    }
 
    tbody.insertAdjacentHTML(
        'beforeend',
        buildDesktopRowHtml(desktopRowCount, {
            isRef: false,
            shelvesOptions
        })
    );
 
    const newIdx = desktopRowCount;
    desktopRowCount++;
 
    initSelect2OnRows();
    if (window.feather) feather.replace();
 
    // ── [PATCH 4] Copy artikel dari baris terakhir sebelum baris baru ──
    if (IS_CHEM_CONS) {
        const allRows = tbody.querySelectorAll('.sto-row');
        // baris ke-2 dari belakang = baris sebelum yang baru ditambah
        const prevRow = allRows[allRows.length - 2];
        if (prevRow) {
            const $prevRow  = $(prevRow);
            const prevRowIdx = $prevRow.find('.part-select').data('row');
            const $prevSel  = $prevRow.find(`select[name="articles[${prevRowIdx}][article_id]"]`);
 
            const prevVal     = $prevSel.val();
            const prevText    = $prevSel.find(':selected').text();
            const prevCode    = $prevRow.find(`input[name="articles[${prevRowIdx}][article_code]"]`).val();
            const prevUom     = $prevRow.find(`input[name="articles[${prevRowIdx}][uom]"]`).val();
            const prevMinPkg  = $prevRow.find(`input[name="articles[${prevRowIdx}][min_package]"]`).val();
            const prevKondisi = IS_CHEM_ONLY
                ? $prevRow.find('.kondisi-input').val()
                : '';
 
            if (prevVal && prevVal !== '__OTHER__:') {
                const $newRow = $(tbody.querySelectorAll('.sto-row')[allRows.length - 1]);
                const $newSel = $newRow.find(`select[name="articles[${newIdx}][article_id]"]`);
 
                // Tambah option + set value ke select2
                const opt = new Option(prevText, prevVal, true, true);
                $(opt).data('code',       prevCode);
                $(opt).data('uom',        prevUom);
                $(opt).data('minPackage', prevMinPkg);
 
                $newSel.append(opt).trigger({
                    type: 'select2:select',
                    params: {
                        data: {
                            id        : prevVal,
                            text      : prevText,
                            code      : prevCode,
                            uom       : prevUom,
                            minPackage: prevMinPkg,
                            isOther   : false,
                        }
                    }
                });
 
                // Isi field secara langsung juga (antisipasi trigger tidak fire)
                $newRow.find(`input[name="articles[${newIdx}][article_code]"]`).val(prevCode);
                $newRow.find(`input[name="articles[${newIdx}][uom]"]`).val(prevUom);
                $newRow.find(`input[name="articles[${newIdx}][min_package]"]`).val(prevMinPkg);
 
                // ── [PATCH 5] Flip kondisi untuk Chemical ──────────────────
                if (IS_CHEM_ONLY && prevKondisi) {
                    const flippedKondisi = prevKondisi === 'Utuh' ? 'Tidak Utuh' : 'Utuh';
                    $newRow.find('.kondisi-input').val(flippedKondisi);
                    $newRow.find('.kondisi-label')
                        .text(flippedKondisi)
                        .css('color', flippedKondisi === 'Utuh' ? 'var(--sto-green-mid)' : '#E53935');
 
                    // Set qty hint: jika Tidak Utuh, biarkan kosong (user isi manual)
                    // jika Utuh, biarkan kosong juga — user tetap isi qty
                }
            }
        }
    }
});

// ══════════════════════════════════════════════════════════
// SYNC ITEM — assign item ke address yang dipilih
// ══════════════════════════════════════════════════════════
$(document).on('click', '.btn-sync-item', function () {
    const $btn  = $(this);
    const row   = $btn.data('row');
    const $row  = $btn.closest('.sto-row');
    
    const shelfId     = $row.find('.addr-select').val();
    const shelfName   = $row.find('.addr-select option:selected').data('shelves') || '';
    const articleCode = $row.find(`input[name="articles[${row}][article_code]"]`).val()?.trim();
    const uom         = $row.find(`input[name="articles[${row}][uom]"]`).val()?.trim() || '';
    
    if (!shelfId) {
        return Swal.fire({ icon: 'warning', title: 'Pilih Address', text: 'Pilih address tujuan terlebih dahulu' });
    }
    if (!articleCode) {
        return Swal.fire({ icon: 'warning', title: 'Pilih Part', text: 'Pilih part terlebih dahulu sebelum sync' });
    }
    
    Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Sync',
        html: `Assign <strong>${articleCode}</strong> ke address <strong>${shelfName}</strong>?<br>
               <small class="text-gray-500">Item akan muncul di referensi address tersebut.</small>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Sync',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (!result.isConfirmed) return;
        
        $btn.prop('disabled', true).html('<i data-feather="loader" class="w-3 h-3 animate-spin"></i>');
        
        $.ajax({
            url: '/facility/sto/sign-item',
            method: 'POST',
            data: {
                master_id   : shelfId,
                article_code: articleCode,
                unit        : uom,
                _token      : $('meta[name="csrf-token"]').attr('content'),
            },
            success(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false,
                });
                
                // Update cache lokal
                const area = document.getElementById('area_desktop')?.value
                          || document.getElementById('area_mobile')?.value
                          || '';
                const cacheKey = `${WAREHOUSE_VAL}|${area}`;
                if (areaCache[cacheKey]) {
                    const shelf = areaCache[cacheKey].shelves?.find(s => String(s.id) === String(shelfId));
                    if (shelf) {
                        shelf.items = shelf.items || [];
                        shelf.items.push({
                            article_code : articleCode,
                            description  : res.description ?? null,
                            unit         : uom,
                            already_saved: false,
                        });
                    }
                }

                // Deteksi desktop vs mobile
                const isMobileRow = $row.closest('#mobile-article-list').length > 0;

                if (isMobileRow) {
                    // ── MOBILE ──────────────────────────────────
                    $row.attr('data-is-ref', '1').attr('data-is-manual', '0');
                    $row.find('.mobile-addr-select-block').replaceWith(`
                        <div class="mobile-addr-block mt-3">
                          <label class="text-xs font-semibold text-gray-600 mb-1 block">Address</label>
                          <div class="mobile-addr-label w-full border rounded px-2 py-1 bg-green-50 text-green-700 text-sm font-semibold">
                            ✓ ${shelfName}
                          </div>
                        </div>
                    `);
                    const $selM = $row.find('.part-select');
                    if (res.description) {
                        $selM.find('option:selected').text(`${articleCode} - ${res.description}`);
                        $selM.trigger('change.select2');
                    }
                } else {
                    // ── DESKTOP ──────────────────────────────────
                    $row.attr('data-is-ref', '1').attr('data-is-manual', '0');
                    $row.find('.td-addr-desktop').html(
                        `<span class="row-addr-label" style="color:var(--sto-green); font-weight:700;">✓ ${shelfName}</span>`
                    );
                    const $selD = $row.find('.part-select');
                    if (res.description) {
                        $selD.find('option:selected').text(`${articleCode} - ${res.description}`);
                        $selD.trigger('change.select2');
                    }
                    $btn.closest('td').remove();
                }

                if (window.feather) feather.replace();
            },
            error(xhr) {
                const msg = xhr.responseJSON?.message || 'Gagal menyimpan';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            },
            complete() {
                $btn.prop('disabled', false).html('<i data-feather="link" class="w-3 h-3"></i> Sync');
                if (window.feather) feather.replace();
            }
        });
    });
});


function buildMobileRowHtml(idx, opts = {}) {
    const { 
        articleCode = '', uom = '', minPackage = '',
        qty = '', location = WAREHOUSE_VAL,
        isRef = false, shelvesOptions = '',
        kondisi = ''
    } = opts;
    
    const showAddrDropdown = tableMode === 'area' && !isRef;  // hanya manual + mode area

    let addrBlock;
    if (isRef) {
        // Baris referensi: label address saja, tampil hanya saat mode area
        addrBlock = `
            <div class="mobile-addr-block mt-3" style="${tableMode === 'area' ? '' : 'display:none'}">
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Address</label>
              <div class="mobile-addr-label w-full border rounded px-2 py-1 bg-blue-50 text-blue-700 text-sm font-semibold">—</div>
            </div>`;
    } else if (showAddrDropdown) {
        // Baris manual di mode area: dropdown + sync button
        addrBlock = `
            <div class="mobile-addr-select-block mt-3">
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Assign ke Address</label>
              <div class="flex gap-2">
                <select class="addr-select flex-1 border rounded px-2 py-1 text-sm" 
                        name="articles[${idx}][shelf_id]" data-row="${idx}">
                  <option value="">— pilih address —</option>
                  ${shelvesOptions}
                </select>
                <button type="button" class="btn-sync-item px-3 py-1 bg-blue-600 text-white rounded text-xs font-semibold" 
                        data-row="${idx}">
                  Sync
                </button>
              </div>
            </div>`;
    } else {
        // Baris manual di mode non-area: sembunyikan address block
        addrBlock = `
            <div class="mobile-addr-block mt-3" style="display:none;">
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Address</label>
              <div class="mobile-addr-label w-full border rounded px-2 py-1 bg-blue-50 text-blue-700 text-sm font-semibold">—</div>
            </div>`;
    }
    
    return `
    <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 overflow-hidden sto-row" 
         data-row="${idx}" data-is-ref="${isRef ? 1 : 0}" data-is-manual="${!isRef ? 1 : 0}">
      <div class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2">
        <span class="header-label text-sm font-semibold">❖ Item ${idx + 1}</span>
      </div>
      <div class="p-4">
        <label class="text-xs font-semibold text-gray-600 mb-1">Nama Part</label>
        <select class="part-select w-full mt-1" name="articles[${idx}][article_id]" data-row="${idx}">
          <option value="">-- pilih part --</option>
        </select>
        <input type="hidden" name="articles[${idx}][other_name]" class="other-name-input">
        
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Kode Part</label>
        <input type="text" name="articles[${idx}][article_code]" value="${articleCode}"
          class="article-code w-full border rounded px-2 py-1 bg-gray-100 text-sm" readonly>
        
        <div class="mobile-qty-block" style="${tableMode === 'area' ? 'display:none' : ''}">
          <div class="grid grid-cols-2 gap-3 mt-3">
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty</label>
              <input type="number" min="0" name="articles[${idx}][qty]" value="${qty}"
                class="qty-input w-full border rounded px-2 py-1 text-sm">
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Packing</label>
              <input type="number" min="0" name="articles[${idx}][min_package]" value="${minPackage}"
                class="part-min-package w-full border rounded px-2 py-1 text-sm">
            </div>
          </div>
        </div>
        
        ${addrBlock}
        
       <div class="mt-3">
  <label class="text-xs font-semibold text-gray-600 mb-1 block">UOM</label>
  <input type="text" name="articles[${idx}][uom]" value="${uom}"
    class="part-uom w-full border rounded px-2 py-1 text-sm ${IS_CHEM_CONS ? '' : 'bg-gray-100'}"
    ${IS_CHEM_CONS ? '' : 'readonly'}
    data-ref-uom="${isRef ? uom : ''}"
    data-original-uom="${uom}"
    style="background:${IS_CHEM_CONS ? '' : '#f3f4f6'};">
</div>

        ${IS_CHEM_ONLY ? `
       <div class="mt-3">
         <label class="text-xs font-semibold text-gray-600 mb-1 block">Kondisi</label>
         <input type="hidden" name="articles[${idx}][kondisi]" class="kondisi-input" value="${kondisi}">
         <div class="kondisi-label w-full border rounded px-2 py-1 text-sm text-center font-semibold"
           style="background:#f3f4f6;color:${kondisi === 'Utuh' ? '#2E7D32' : kondisi === 'Tidak Utuh' ? '#E53935' : '#9ca3af'};">
           ${kondisi || '—'}</div>
       </div>` : ''}
        
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Location</label>
        <input type="text" name="articles[${idx}][location]" value="${location}" readonly
          class="location-input w-full bg-gray-100 border rounded px-2 py-1 text-sm">
      </div>
    </div>`;
}


// ══════════════════════════════════════════════════════════
// RESET ROWS
// ══════════════════════════════════════════════════════════
function resetDesktopRows(n = IS_CHEM_CONS ? 1 : 7) {
    const tbody = document.getElementById('article-table');
    if (!tbody) return;
    tbody.innerHTML = '';
    for (let i = 0; i < n; i++) tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i));
    desktopRowCount = n;
    initSelect2OnRows();
    setQtyColumnMode(tableMode === 'area' ? 'address' : 'qty');
    if (window.feather) feather.replace();
}

function resetMobileRows(n = IS_CHEM_CONS ? 1 : 7) {
    const list = document.getElementById('mobile-article-list');
    if (!list) return;
    list.innerHTML = '';

    const area_val = document.getElementById('area_mobile')?.value || '';
    const shelvesOpts = (tableMode === 'area' && area_val)
        ? buildShelfOptionsHtml(WAREHOUSE_VAL, area_val)
        : '';

    for (let i = 0; i < n; i++) {
        list.insertAdjacentHTML('beforeend', buildMobileRowHtml(i, {
            isRef         : false,
            shelvesOptions: shelvesOpts,
        }));
    }
    mobileRowCount = n;
    initSelect2OnRows();
    setQtyColumnModeMobile(tableMode === 'area' ? 'address' : 'qty');
}

// ══════════════════════════════════════════════════════════
// LOAD AREAS — satu fungsi, tidak duplikat
// ══════════════════════════════════════════════════════════
function loadAreas(warehouse, selectEl) {
    if (!warehouse || !selectEl) return;
    fetch(`${AREA_URL}?warehouse=${encodeURIComponent(warehouse)}`)
        .then(r => r.json())
        .then(data => {
            selectEl.innerHTML = '<option value="">— Pilih Rack —</option>';
            (data.areas || []).forEach(a => {
                const opt       = document.createElement('option');
                opt.value       = a.area;

                // ✅ Jika semua shelf di area ini sudah tersimpan → disable + gembok
                if (a.all_saved) {
                    opt.textContent = `🔒 ${a.area} — Already Closed`;
                    opt.disabled    = true;
                } else {
                    opt.textContent = a.area;
                }

                selectEl.appendChild(opt);
            });
        })
        .catch(err => console.error('Gagal load area:', err));
}

// ══════════════════════════════════════════════════════════
// LOAD SHELVES — satu request, pakai cache
// ══════════════════════════════════════════════════════════
async function loadShelves(warehouse, area, shelfEl) {
    if (!warehouse || !area || !shelfEl) return;

    shelfEl.innerHTML = '<option value="">Memuat address...</option>';
    shelfEl.disabled  = true;

    try {
        const cacheKey = `${warehouse}|${area}`;
        if (!areaCache[cacheKey]) {
            const res = await fetch(
                `/facility/sto/reference/items-by-area?warehouse=${encodeURIComponent(warehouse)}&area=${encodeURIComponent(area)}`
            );
            areaCache[cacheKey] = await res.json();
        }

        const shelves = areaCache[cacheKey].shelves || [];
        shelfEl.innerHTML = '<option value="">— Pilih Address —</option>';

        if (!shelves.length) {
            shelfEl.innerHTML = '<option value="">Tidak ada address</option>';
            shelfEl.disabled  = true;
            return;
        }

        shelves.forEach(s => {
            const opt            = document.createElement('option');
            opt.value            = s.id;
            opt.dataset.masterId = s.id;
            opt.dataset.shelves  = s.shelves;
            if (s.all_saved) {
                opt.textContent = `🔒 ${s.shelves} — Already Closed`;
                opt.disabled    = true;
            } else {
                opt.textContent = s.shelves;
            }
            shelfEl.appendChild(opt);
        });

        shelfEl.disabled = false;


    } catch (err) {
        console.error('Gagal load shelf:', err);
        shelfEl.innerHTML = '<option value="">Gagal memuat</option>';
        shelfEl.disabled  = true;
    }
}

function toggleSaveButton(warehouse) {
    const btns = ['btnSave', 'btnSaveMobile'].map(id => document.getElementById(id));
    const isSpecialWH = ['chemical', 'consumable'].includes((warehouse || '').toLowerCase());
 
    btns.forEach(btn => {
        if (!btn) return;
        if (isSpecialWH) {
            // Tampil hanya saat mode shelf (address sudah dipilih)
            btn.style.display = tableMode === 'shelf' ? 'inline-flex' : 'none';
        } else {
            // Non-chem/cons: selalu tampil
            btn.style.display = 'inline-flex';
        }
    });
}



function onWarehouseChange(warehouse) {
    shelvesLoaded = false;
    tableMode = 'default';
    toggleSaveButton(warehouse);
}



// ── Switch kolom Qty ↔ Address (desktop) ─────────────────────
function setQtyColumnMode(mode) {
    const thQty    = document.getElementById('th-qty-desktop');
    const thAddr   = document.getElementById('th-addr-desktop');
    const thAction = document.getElementById('th-action-desktop');
    
    if (thQty)    thQty.style.display    = mode === 'address' ? 'none' : '';
    if (thAddr)   thAddr.style.display   = mode === 'address' ? '' : 'none';
    if (thAction) thAction.style.display = mode === 'address' ? '' : 'none';

    document.querySelectorAll('.sto-row').forEach(row => {
        const qtyTd  = row.querySelector('.td-qty-desktop');
        const addrTd = row.querySelector('.td-addr-desktop');
        if (!qtyTd || !addrTd) return;
        
        if (mode === 'address') {
            qtyTd.style.display  = 'none';
            addrTd.style.display = '';
        } else {
            qtyTd.style.display  = '';
            addrTd.style.display = 'none';
        }
    });
}


// ── Switch kolom Qty ↔ Address (mobile) ──────────────────────
function setQtyColumnModeMobile(mode) {
    document.querySelectorAll('.sto-row').forEach(row => {
        const qtyDiv  = row.querySelector('.mobile-qty-block');
        const addrDiv = row.querySelector('.mobile-addr-block');
        if (!qtyDiv || !addrDiv) return;
        if (mode === 'address') {
            qtyDiv.style.display  = 'none';
            addrDiv.style.display = '';
        } else {
            qtyDiv.style.display  = '';
            addrDiv.style.display = 'none';
        }
    });
}

// Fungsi baru — populate dari semua shelf di area
async function populateAllItemsFromArea(warehouse, area, mode = 'desktop') {
    const cacheKey = `${warehouse}|${area}`;

    if (!areaCache[cacheKey]) {
        const res = await fetch(
            `/facility/sto/reference/items-by-area?warehouse=${encodeURIComponent(warehouse)}&area=${encodeURIComponent(area)}`
        );
        areaCache[cacheKey] = await res.json();
    }

    const shelves   = areaCache[cacheKey].shelves || [];
    const allItems  = [];
    const seenCodes = new Set();

    // ✅ Kumpulkan semua article_code yang already_saved di area ini
    //    (dari shelf manapun)
    const savedCodes = new Set();
    shelves.forEach(shelf => {
        (shelf.items || []).forEach(item => {
            if (item.already_saved) savedCodes.add(item.article_code);
        });
    });

    shelves.forEach(shelf => {
        (shelf.items || []).forEach(item => {
            // ✅ Skip jika sudah tersimpan di shelf manapun dalam area ini
            if (savedCodes.has(item.article_code)) return;
            if (!seenCodes.has(item.article_code)) {
                seenCodes.add(item.article_code);
                allItems.push(item);
            }
        });
    });
if (!allItems.length) {
        // Semua item sudah tersimpan — reset ke baris kosong
        if (mode === 'desktop') resetDesktopRows();
        else resetMobileRows();
        return;
    }

    // Set mode area sebelum populate
    tableMode = 'area';
    toggleSaveButton(WAREHOUSE_VAL);
    populateFromItems(allItems, area, '— semua address —', mode, shelves);
}

// ══════════════════════════════════════════════════════════
// POPULATE FROM ITEMS (dari cache)
// ══════════════════════════════════════════════════════════
function populateFromItems(items, area, shelf, mode = 'desktop', shelvesData = null) {
    const isMobile  = mode === 'mobile';
    const overlayEl = document.getElementById('rowsLoadingOverlay');
    if (overlayEl) overlayEl.classList.add('active');

    if (!isMobile) {
        const tbody = document.getElementById('article-table');
        if (!tbody) return;
        tbody.innerHTML = '';
        const defaultMin = IS_CHEM_CONS ? 1 : 7;
const total = Math.max(defaultMin, items.length);
        items.forEach((item, i) => {
            tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i, {
                articleCode: item.article_code || '',
                uom        : item.unit         || '',
                minPackage : item.min_package  || '',
                isRef      : true,
                location   : WAREHOUSE_VAL,
            }));
        });
        for (let i = items.length; i < total; i++) {
            tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i));
        }
        desktopRowCount = total;
    } else {
        // ── MOBILE: build rows manual, bukan pakai resetMobileRows ──
        const list = document.getElementById('mobile-article-list');
        if (!list) return;
        list.innerHTML = '';

        const defaultMin = IS_CHEM_CONS ? 1 : 7;
        const total = Math.max(defaultMin, items.length);

        // Baris referensi (isRef=true) — TIDAK dapat dropdown/sync
        items.forEach((item, i) => {
            list.insertAdjacentHTML('beforeend', buildMobileRowHtml(i, {
                articleCode: item.article_code || '',
                uom        : item.unit         || '',
                minPackage : item.min_package  || '',
                isRef      : true,              // ← KUNCI: baris ref tidak dapat sync
                location   : WAREHOUSE_VAL,
            }));
        });

        // Baris kosong tambahan (isRef=false) — boleh dapat dropdown jika mode area
        const area_val = document.getElementById('area_mobile')?.value || '';
        const shelvesOpts = (tableMode === 'area' && area_val)
            ? buildShelfOptionsHtml(WAREHOUSE_VAL, area_val)
            : '';

        for (let i = items.length; i < total; i++) {
            list.insertAdjacentHTML('beforeend', buildMobileRowHtml(i, {
                isRef         : false,
                shelvesOptions: shelvesOpts,
            }));
        }

        mobileRowCount = total;
    }

    initSelect2OnRows();
   // Build map: article_code → nama shelf (untuk mode area)
    const codeToShelf = {};
    if (shelvesData && tableMode === 'area') {
        shelvesData.forEach(shelf => {
            (shelf.items || []).forEach(item => {
                if (!codeToShelf[item.article_code]) {
                    codeToShelf[item.article_code] = shelf.shelves || '—';
                }
            });
        });
    }

  // Di dalam forEach items setelah $sel.append(...)
items.forEach((item, i) => {
    if (!item.article_code) return;

    const text = item.description
        ? `${item.article_code} - ${item.description}`
        : item.article_code;

    const $sel = $(`select[name="articles[${i}][article_id]"]`);
    const optionValue = item.article_id || item.article_code;

    $sel.append(new Option(text, optionValue, true, true)).trigger('change');

    // Set fields eksplisit
    $(`input[name="articles[${i}][article_code]"]`).val(item.article_code);
    $(`input[name="articles[${i}][min_package]"]`).val(item.min_package || '');

    // ── [BARU] Simpan UOM dari referensi ke data attribute ──
    const $uomField = $(`input[name="articles[${i}][uom]"]`);
    const refUom    = item.unit || '';          // ← dari sto_reference_items.uom
    $uomField
        .val(refUom)
        .data('ref-uom', refUom)               // ← simpan untuk dipakai saat select2:select
        .data('original-uom', refUom);         // ← simpan untuk dikembalikan saat Utuh

    const $sel = $(`select[name="articles[${i}][article_id]"]`);
    
    // Gunakan article_id sebagai value (konsisten dengan populateFromReference)
    const optionValue = item.article_id || item.article_code;
    $sel.append(new Option(text, optionValue, true, true)).trigger('change');
    
    // Set fields secara eksplisit karena trigger('change') tidak fire select2:select
    $(`input[name="articles[${i}][article_code]"]`).val(item.article_code);
    $(`input[name="articles[${i}][uom]"]`).val(item.unit || '');
    $(`input[name="articles[${i}][min_package]"]`).val(item.min_package || '');
    // ...

        // Set address label per row
        const shelfName = codeToShelf[item.article_code] || '—';
        if (!isMobile) {
            const rows = document.querySelectorAll('#article-table .sto-row');
            if (rows[i]) {
                const addrSpan = rows[i].querySelector('.row-addr-label');
                if (addrSpan) addrSpan.textContent = shelfName;
            }
        } else {
            const mobileRow = document.querySelector(`#mobile-article-list .sto-row[data-row="${i}"]`);
            if (mobileRow) {
                const addrDiv = mobileRow.querySelector('.mobile-addr-label');
                if (addrDiv) addrDiv.textContent = shelfName;
            }
            $(`div[data-row="${i}"] .header-label`).text(item.description || `❖ Item ${i + 1}`);
        }
    });

    // Apply kolom mode setelah rows populated
    if (!isMobile) {
        setQtyColumnMode(tableMode === 'area' ? 'address' : 'qty');
    } else {
        setQtyColumnModeMobile(tableMode === 'area' ? 'address' : 'qty');
    }

    const sfx      = isMobile ? 'Mobile' : '';
    const banner   = document.getElementById(`refBanner${sfx}`);
    const bannerTxt = document.getElementById(`refBannerText${sfx}`);
    const itemCount = document.getElementById(`refItemCount${sfx}`);
    const btnClear  = document.getElementById(`btnClearRef${sfx}`);
    if (banner)    banner.classList.remove('hidden');
    if (bannerTxt) bannerTxt.textContent = `${area} / ${shelf}`;
    if (itemCount) itemCount.textContent = items.length;
    if (btnClear)  btnClear.style.display = '';

    if (overlayEl) overlayEl.classList.remove('active');
    if (window.feather) feather.replace();
}

// ══════════════════════════════════════════════════════════
// POPULATE FROM REFERENCE (fallback, fetch via master_id)
// ══════════════════════════════════════════════════════════
function populateFromReference(masterId, area, shelf, mode = 'desktop') {
    const isMobile  = mode === 'mobile';
    const overlayEl = document.getElementById('rowsLoadingOverlay');
    if (overlayEl) overlayEl.classList.add('active');

    fetch(`${ITEMS_URL}?master_id=${masterId}`)
        .then(r => r.json())
        .then(data => {
            const items = data.items || [];

            if (!isMobile) {
                const tbody = document.getElementById('article-table');
                if (!tbody) return;
                tbody.innerHTML = '';
                const defaultMobile = IS_CHEM_CONS ? 1 : 7;
resetMobileRows(Math.max(defaultMobile, items.length));
                items.forEach((item, i) => {
                    tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i, {
                        articleCode: item.article_code || '',
                        uom        : item.unit         || '',
                        minPackage : item.min_package  || '',
                        qty        : item.qty          || '',
                        isRef      : true,
                        location   : WAREHOUSE_VAL,
                    }));
                });
                for (let i = items.length; i < total; i++) {
                    tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i));
                }
                desktopRowCount = total;
            } else {
                const list = document.getElementById('mobile-article-list');
                if (!list) return;
                list.querySelectorAll('.sto-row').forEach((rowEl, i) => {
                    const $row = $(rowEl);
                    const idx  = $row.data('row') ?? i;
                    const $sel = $row.find('.part-select');
                    if ($sel.hasClass('select2-hidden-accessible')) $sel.val(null).trigger('change');
                    else $sel.val('');
                    $row.find(`input[name="articles[${idx}][article_code]"]`).val('');
                    $row.find(`input[name="articles[${idx}][qty]"]`).val('');
                    $row.find(`input[name="articles[${idx}][min_package]"]`).val('');
                    $row.find(`input[name="articles[${idx}][uom]"]`).val('');
                    $row.find(`input[name="articles[${idx}][other_name]"]`).val('');
                    $row.find(`input[name="articles[${idx}][location]"]`).val(WAREHOUSE_VAL);
                    $row.find('.header-label').text(`❖ Item ${idx + 1}`);
                });
                mobileRowCount = list.querySelectorAll('.sto-row').length;
            }

            initSelect2OnRows();

            items.forEach((item, i) => {
                if (!item.article_id) return;
                const $sel = $(`select[name="articles[${i}][article_id]"]`);
                $sel.append(new Option(
                    `${item.article_code} - ${item.description}`,
                    item.article_id, true, true
                )).trigger('change');
                $(`input[name="articles[${i}][article_code]"]`).val(item.article_code || '');
                $(`input[name="articles[${i}][uom]"]`).val(item.unit        || '');
                $(`input[name="articles[${i}][min_package]"]`).val(item.min_package || '');
                if (isMobile) {
                    $(`div[data-row="${i}"] .header-label`).text(item.description || `❖ Item ${i + 1}`);
                }
            });

            const sfx      = isMobile ? 'Mobile' : '';
            const banner   = document.getElementById(`refBanner${sfx}`);
            const bannerTxt = document.getElementById(`refBannerText${sfx}`);
            const itemCount = document.getElementById(`refItemCount${sfx}`);
            const btnClear  = document.getElementById(`btnClearRef${sfx}`);
            if (banner)    banner.classList.remove('hidden');
            if (bannerTxt) bannerTxt.textContent = `${area} / ${shelf}`;
            if (itemCount) itemCount.textContent = items.length;
            if (btnClear)  btnClear.style.display = '';

          // Mode shelf → tampilkan Qty
            if (!isMobile) setQtyColumnMode('qty');
            else setQtyColumnModeMobile('qty');

            if (window.feather) feather.replace();
        })
        .catch(err => console.error('Gagal load referensi items:', err))
        .finally(() => { if (overlayEl) overlayEl.classList.remove('active'); });
}

// ══════════════════════════════════════════════════════════
// getStoNumber
// ══════════════════════════════════════════════════════════
function getStoNumber() {
    if ($(window).width() < 1024) {
        return document.getElementById('sto_number_mobile')?.value || '';
    }
    return document.getElementById('sto_number_desktop')?.value
        || $('#sto_number_desktop').val() || '';
}

// ══════════════════════════════════════════════════════════
// FULL RESET
// ══════════════════════════════════════════════════════════
function fullReset(usedStoNumber) {
    const isMobile = $(window).width() < 1024;

    // Reset hidden fields STO number & ref master
    ['desktop', 'mobile'].forEach(pfx => {
        const stoHid = document.getElementById(pfx === 'desktop' ? 'sto_number_desktop' : 'sto_number_mobile');
        const refHid = document.getElementById(`ref_master_id_${pfx}`);
        if (stoHid) stoHid.value = '';
        if (refHid) refHid.value = '';
    });

    // Reset note
    $('#note, #note_mobile').val('');

    if (IS_CHEM_CONS) {
        // ── Bersihkan cache agar data fresh (item yang baru save sudah masuk DB)
        Object.keys(areaCache).forEach(k => delete areaCache[k]);

        const areaDesktop = document.getElementById('area_desktop');
        const areaMobile  = document.getElementById('area_mobile');
        const currentArea = areaDesktop?.value || areaMobile?.value || lastArea || '';

        // Reload shelf dropdown (tanpa reset area UI)
        const shelfDesktop = document.getElementById('shelf_desktop');
        const shelfMobile  = document.getElementById('shelf_mobile');

        if (shelfDesktop) {
            shelfDesktop.innerHTML = '<option value="">— Pilih Address —</option>';
            shelfDesktop.disabled  = true;
        }
        if (shelfMobile) {
            shelfMobile.innerHTML = '<option value="">— Pilih Address —</option>';
            shelfMobile.disabled  = true;
        }

        // Sembunyikan banner & clear button
        ['refBanner', 'refBannerMobile'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });
        ['btnClearRef', 'btnClearRefMobile'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });

        // Reset tableMode ke area (bukan shelf, karena shelf direset)
        tableMode = 'area';
        shelvesLoaded = false;
        toggleSaveButton(WAREHOUSE_VAL);

        if (currentArea) {
            // Reload shelves + re-populate items dari area yang sama
            // Area UI TETAP terselect, hanya data di-refresh
            const reloadArea = async (areaEl, shelfEl, mode) => {
                if (!areaEl || !areaEl.value) return;
                await loadShelves(WAREHOUSE_VAL, currentArea, shelfEl);
                await populateAllItemsFromArea(WAREHOUSE_VAL, currentArea, mode);
            };

            reloadArea(areaDesktop, shelfDesktop, 'desktop');
            if (isMobile) reloadArea(areaMobile, shelfMobile, 'mobile');
        } else {
            // Tidak ada area tersimpan, reset rows saja
            resetDesktopRows();
            if (isMobile) resetMobileRows();
            // Reload area list supaya lock status terupdate
            loadAreas(WAREHOUSE_VAL, document.getElementById('area_desktop'));
            loadAreas(WAREHOUSE_VAL, document.getElementById('area_mobile'));
        }

    } else {
        // Non chem/cons: flow lama
        if (usedStoNumber) {
            $(`#sto_number_mobile option[value="${usedStoNumber}"],
               #sto_number_desktop option[value="${usedStoNumber}"]`).remove();
        }
        $('#sto_number_mobile, #sto_number_desktop').val(null).trigger('change');

        tableMode = 'default';
        toggleSaveButton(WAREHOUSE_VAL);
        setQtyColumnMode('qty');
        setQtyColumnModeMobile('qty');
        resetDesktopRows(7);
        $('#warehouse-null, #warehouse-null-desktop').val(null).trigger('change');
        $('.location-input').val(WAREHOUSE_VAL);
    }
}

// ══════════════════════════════════════════════════════════
// DOCUMENT READY — hanya event handler, tidak ada fungsi
// ══════════════════════════════════════════════════════════
$(document).ready(function () {

toggleSaveButton(WAREHOUSE_VAL);

$(document).on('change blur', '.qty-input', function () {
    if (!IS_CHEM_ONLY) return;
     const $row   = $(this).closest('.sto-row');
     const row    = $row.find('.part-select').data('row');
     const qty    = parseFloat($(this).val());
     const minPkg = parseFloat($row.find(`input[name="articles[${row}][min_package]"]`).val()) || 0;

     if (!isNaN(qty) && qty > 0 && minPkg > 0 && qty >= minPkg) {
         const remainder = qty % minPkg;
         if (remainder > 0.0001) {
             // Bukan kelipatan dan >= minPkg → tolak
             Swal.fire({
                 icon: 'warning',
                 title: 'Qty tidak valid',
                 html: `Qty harus kelipatan min packing (<strong>${minPkg}</strong>), atau di bawah ${minPkg}.<br>
                        Contoh valid: ${minPkg}, ${minPkg * 2}, ${minPkg * 3}, ...`,
                 timer: 3000,
                 showConfirmButton: false,
             });
             $(this).val('');
             autoKondisi($row);
             return;
         }
     }
     autoKondisi($row);
 });

  $(document).on('change blur', '.part-min-package', function () {
    if (!IS_CHEM_CONS) return;

    const $this  = $(this);
    const $row   = $this.closest('.sto-row');
    const row    = $row.find('.part-select').data('row');
    const newVal = parseFloat($this.val());

    const articleCode = $row.find(`input[name="articles[${row}][article_code]"]`).val()?.trim();

    if (!articleCode || articleCode === 'OTHER' || isNaN(newVal) || newVal <= 0) return;

    autoKondisi($row);

    $.ajax({
        url   : '/facility/sto/update-min-package',
        method: 'POST',
        data  : {
            article_code: articleCode,
            min_package : newVal,
            _token      : $('meta[name="csrf-token"]').attr('content'),
        },
        success() {
            $this.css('border-color', 'var(--sto-green-mid)');
            setTimeout(() => $this.css('border-color', ''), 1500);

            // ✅ TOAST SUCCESS
            Toast.fire({
                icon: 'success',
                title: 'Min package berhasil diupdate'
            });
        },
        error(xhr) {
            console.warn('Gagal update min_package:', xhr.responseJSON?.message);

            $this.css('border-color', '#E53935');
            setTimeout(() => $this.css('border-color', ''), 1500);

            // ❌ TOAST ERROR
            Toast.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'Gagal update min package'
            });
        }
    });
});

    initSelect2OnRows();
    $('#warehouse-null').select2({ placeholder: '— Pilih Gudang —', width: '100%', allowClear: true });
    $('#warehouse-null-desktop').select2({ placeholder: '— Pilih Gudang —', width: '100%' });
    if (!IS_CHEM_CONS) {
        $('#sto_number_desktop').select2({ placeholder: '— Pilih STO Number —', width: '100%' });
        $('#sto_number_mobile').select2({ placeholder: '— Pilih STO Number —', width: '100%' });
    }
    if (window.feather) feather.replace();

    if (IS_CHEM_CONS) {
        loadAreas(WAREHOUSE_VAL, document.getElementById('area_desktop'));
        loadAreas(WAREHOUSE_VAL, document.getElementById('area_mobile'));
    }

   // ── AREA CHANGE ──────────────────────────────────────
$(document).on('change', '#area_desktop', async function () {
    const area    = this.value;
    const wh      = this.dataset.warehouse || WAREHOUSE_VAL;
    const shelfEl = document.getElementById('shelf_desktop');

    shelfEl.innerHTML = '<option value="">— Pilih Address —</option>';
    shelfEl.disabled  = true;
    document.getElementById('sto_number_desktop').value    = '';
    document.getElementById('ref_master_id_desktop').value = '';

    const banner = document.getElementById('refBanner');
    if (banner) banner.classList.add('hidden');
    const btnC = document.getElementById('btnClearRef');
    if (btnC) btnC.style.display = 'none';

    resetDesktopRows(); // ← otomatis IS_CHEM_CONS ? 3 : 7

    if (!area) return;

    // ✅ await loadShelves dulu — cache terisi di sini
    await loadShelves(wh, area, shelfEl);

    // ✅ populateAllItemsFromArea pakai cache yang sudah ada, tidak double-fetch
   // ✅ populateAllItemsFromArea pakai cache yang sudah ada, tidak double-fetch
    if (IS_CHEM_CONS) {
        tableMode = 'area';
        toggleSaveButton(WAREHOUSE_VAL);
        populateAllItemsFromArea(wh, area, 'desktop');
    }
});

$(document).on('change', '#area_mobile', async function () {
    const area    = this.value;
    const wh      = this.dataset.warehouse || WAREHOUSE_VAL;
    const shelfEl = document.getElementById('shelf_mobile');
    if (!shelfEl) return;

    shelfEl.innerHTML = '<option value="">— Pilih Address —</option>';
    shelfEl.disabled  = true;
    document.getElementById('area_value_mobile').value  = area;
    document.getElementById('shelf_value_mobile').value = '';
    document.getElementById('sto_number_mobile').value  = '';

    const refHid = document.getElementById('ref_master_id_mobile');
    if (refHid) refHid.value = '';
    const banner = document.getElementById('refBannerMobile');
    if (banner) banner.classList.add('hidden');
    const btnC = document.getElementById('btnClearRefMobile');
    if (btnC) btnC.style.display = 'none';

    resetMobileRows(); // ← otomatis IS_CHEM_CONS ? 3 : 8

    if (!area) return;

    // ✅ await loadShelves dulu
    await loadShelves(wh, area, shelfEl);

    // ✅ pakai cache, tidak double-fetch
   // ✅ pakai cache, tidak double-fetch
    if (IS_CHEM_CONS) {
        tableMode = 'area';
        toggleSaveButton(WAREHOUSE_VAL);
        populateAllItemsFromArea(wh, area, 'mobile');
    }
});

    // ── SHELF CHANGE ─────────────────────────────────────
    $(document).on('change', '#shelf_desktop', function () {
        const masterId    = $(this).val();
        const area        = document.getElementById('area_desktop')?.value || '';
        const shelvesName = $(this).find(':selected').data('shelves') || '';
        const cacheKey    = `${WAREHOUSE_VAL}|${area}`;

        // 🔥 TAMBAHKAN INI
    shelvesLoaded = !!masterId;
        if (masterId) {
            tableMode = 'shelf';
        } else if (area && IS_CHEM_CONS) {
            tableMode = 'area';
        } else {
            tableMode = 'default';
        }
        toggleSaveButton(WAREHOUSE_VAL);

        document.getElementById('ref_master_id_desktop').value = masterId || '';
      if (!masterId) {
            if (area && IS_CHEM_CONS) {
                tableMode = 'area';
                toggleSaveButton(WAREHOUSE_VAL);
                populateAllItemsFromArea(WAREHOUSE_VAL, area, 'desktop');
            } else {
                tableMode = 'default';
                toggleSaveButton(WAREHOUSE_VAL);
                resetDesktopRows();
            }
            return;
        }

        const cached = areaCache[cacheKey];
        if (cached) {
            const shelf = (cached.shelves || []).find(s => String(s.id) === String(masterId));
            if (shelf) {
                populateFromItems((shelf.items || []).filter(i => !i.already_saved), area, shelvesName, 'desktop');
                return;
            }
        }
        populateFromReference(masterId, area, shelvesName, 'desktop');
    });

    $(document).on('change', '#shelf_mobile', function () {
        const masterId    = $(this).val();
        const area        = document.getElementById('area_mobile')?.value || '';
        const shelvesName = $(this).find(':selected').data('shelves') || '';
        const cacheKey    = `${WAREHOUSE_VAL}|${area}`;

// 🔥 TAMBAHKAN INI
   shelvesLoaded = !!masterId;
        if (masterId) {
            tableMode = 'shelf';
        } else if (document.getElementById('area_mobile')?.value) {
            tableMode = 'area';
        } else {
            tableMode = 'default';
        }
        toggleSaveButton(WAREHOUSE_VAL);

        document.getElementById('shelf_value_mobile').value = shelvesName;
        const refHid = document.getElementById('ref_master_id_mobile');
        if (refHid) refHid.value = masterId || '';
        if (!masterId) { resetMobileRows(8); return; }

        const cached = areaCache[cacheKey];
        if (cached) {
            const shelf = (cached.shelves || []).find(s => String(s.id) === String(masterId));
            if (shelf) {
                populateFromItems((shelf.items || []).filter(i => !i.already_saved), area, shelvesName, 'mobile');
                return;
            }
        }
        populateFromReference(masterId, area, shelvesName, 'mobile');
    });

    // ── RESET BUTTONS ────────────────────────────────────
    $('#btnClearRef').on('click', function () {
        document.getElementById('area_desktop').value          = '';
        const shelfEl = document.getElementById('shelf_desktop');
        shelfEl.innerHTML = '<option value="">— Pilih rack dulu —</option>';
        shelfEl.disabled  = true;
        document.getElementById('sto_number_desktop').value    = '';
        document.getElementById('ref_master_id_desktop').value = '';
        const banner = document.getElementById('refBanner');
        if (banner) banner.classList.add('hidden');
       $(this).hide();
        tableMode = 'default';
        shelvesLoaded = false;
        toggleSaveButton(WAREHOUSE_VAL);
        resetDesktopRows(7);
    });

    $('#btnClearRefMobile').on('click', function () {
        const areaEl  = document.getElementById('area_mobile');
        const shelfEl = document.getElementById('shelf_mobile');
        if (areaEl)  areaEl.value = '';
        if (shelfEl) { shelfEl.innerHTML = '<option value="">— Pilih rack dulu —</option>'; shelfEl.disabled = true; }
        const stoHid = document.getElementById('sto_number_mobile');
        const refHid = document.getElementById('ref_master_id_mobile');
        if (stoHid) stoHid.value = '';
        if (refHid) refHid.value = '';
        const banner = document.getElementById('refBannerMobile');
        if (banner) banner.classList.add('hidden');
       $(this).hide();
        tableMode = 'default';
        shelvesLoaded = false;
        toggleSaveButton(WAREHOUSE_VAL);
        resetMobileRows(8);
    });


   $('#btnAddRowMobile').off('click').on('click', function () {
    const list = document.getElementById('mobile-article-list');
    if (!list) return;
 
    const area = document.getElementById('area_mobile')?.value || '';
    let shelvesOptions = '';
    if (tableMode === 'area' && area) {
        shelvesOptions = buildShelfOptionsHtml(WAREHOUSE_VAL, area);
    }
 
    list.insertAdjacentHTML('beforeend', buildMobileRowHtml(mobileRowCount, {
        isRef: false,
        shelvesOptions
    }));
 
    const newIdx = mobileRowCount;
    mobileRowCount++;
    initSelect2OnRows();
 
    // ── [PATCH 4] Copy artikel dari baris mobile sebelumnya ──
    if (IS_CHEM_CONS) {
        const allRows = list.querySelectorAll('.sto-row');
        const prevRow = allRows[allRows.length - 2];
        if (prevRow) {
            const $prevRow   = $(prevRow);
            const prevRowIdx = $prevRow.data('row');
            const $prevSel   = $prevRow.find(`select[name="articles[${prevRowIdx}][article_id]"]`);
 
            const prevVal     = $prevSel.val();
            const prevText    = $prevSel.find(':selected').text();
            const prevCode    = $prevRow.find(`input[name="articles[${prevRowIdx}][article_code]"]`).val();
            const prevUom     = $prevRow.find(`input[name="articles[${prevRowIdx}][uom]"]`).val();
            const prevMinPkg  = $prevRow.find(`input[name="articles[${prevRowIdx}][min_package]"]`).val();
            const prevKondisi = IS_CHEM_ONLY
                ? $prevRow.find('.kondisi-input').val()
                : '';
 
            if (prevVal) {
                const $newRow = $(list.querySelectorAll('.sto-row')[allRows.length - 1]);
                const $newSel = $newRow.find(`select[name="articles[${newIdx}][article_id]"]`);
 
                const opt = new Option(prevText, prevVal, true, true);
                $newSel.append(opt).trigger({
                    type: 'select2:select',
                    params: {
                        data: {
                            id        : prevVal,
                            text      : prevText,
                            code      : prevCode,
                            uom       : prevUom,
                            minPackage: prevMinPkg,
                            isOther   : false,
                        }
                    }
                });
 
                $newRow.find(`input[name="articles[${newIdx}][article_code]"]`).val(prevCode);
                $newRow.find(`input[name="articles[${newIdx}][uom]"]`).val(prevUom);
                $newRow.find(`input[name="articles[${newIdx}][min_package]"]`).val(prevMinPkg);
                $newRow.find('.header-label').text(prevText || `❖ Item ${newIdx + 1}`);
 
                // ── [PATCH 5] Flip kondisi mobile ──
                if (IS_CHEM_ONLY && prevKondisi) {
                    const flippedKondisi = prevKondisi === 'Utuh' ? 'Tidak Utuh' : 'Utuh';
                    $newRow.find('.kondisi-input').val(flippedKondisi);
                    $newRow.find('.kondisi-label')
                        .text(flippedKondisi)
                        .css('color', flippedKondisi === 'Utuh' ? '#2E7D32' : '#E53935');
                }
            }
        }
    }
});

    // ── WAREHOUSE CHANGE ──────────────────────────────────
    $(document).on('change', '#warehouse-null, #warehouse-null-desktop, #warehouse-null-desktop-th', function () {
        const loc = $(this).val();
        if (!loc) { $('.location-input').val(''); return; }
        $('.location-input').val(loc);
        $('.sto-row').each(function () { toggleUomByLocation($(this)); });
    });

    // ── PART SELECT / CLEAR ───────────────────────────────
    $(document).on('select2:select', '.part-select', function (e) {
    const data    = e.params.data;
    const $row    = $(this).closest('.sto-row');
    const row     = $(this).data('row');
    const isOther = data.isOther || String(data.id).startsWith('__OTHER__:');
    const isRef   = $row.data('is-ref') == 1;  // baris referensi dari shelf

    const $code   = $(`input[name="articles[${row}][article_code]"]`);
    const $uom    = $(`input[name="articles[${row}][uom]"]`);
    const $minPkg = $(`input[name="articles[${row}][min_package]"]`);
    const $other  = $(`input[name="articles[${row}][other_name]"]`);
    const $header = $row.find('.header-label');

    if (isOther) {
        $code.val('OTHER');
        $uom.val('').removeData('original-uom').prop('readonly', false);
        $minPkg.val('');
        $other.val(data.text);
        $header.text(data.text);
    } else {
        $code.val(data.code || data.id || '');
        $minPkg.val(data.minPackage || $(this).find(':selected').data('min-package') || '');
        $other.val('');
        $header.text(data.text);

        // ── UOM: ref row pakai data-ref-uom jika ada, manual pakai article ──
        const refUom = $uom.data('ref-uom');  // diset saat populateFromItems
        const uomVal = (isRef && refUom) ? refUom : (data.uom || '');

        $uom.val(uomVal).data('original-uom', uomVal).prop('readonly', true);
    }

    toggleUomByLocation($row);
    $row.find('.qty-input').prop('disabled', false);
    autoKondisi($row);
});

    $(document).on('select2:clear', '.part-select', function () {
        const $sel = $(this);
        const $row = $sel.closest('.sto-row');
        const row  = $sel.data('row');
        $sel.val(null).trigger('change');
        $sel.find('option').filter((_, o) => o.value?.startsWith('__OTHER__:')).remove();
        $(`input[name="articles[${row}][article_code]"]`).val('');
        $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', true);
        $(`input[name="articles[${row}][min_package]"]`).val('').prop('readonly', true);
        $(`input[name="articles[${row}][other_name]"]`).val('');
        $row.find('.qty-input').val('').prop('disabled', false);
        $row.find('.header-label').text(`Item ${row + 1}`);
        toggleUomByLocation($row);
    });

    // ── QTY INPUT → AUTO KONDISI ──────────────────────────
    $(document).on('input change', '.qty-input', function () {
        if (!IS_CHEM_ONLY) return;
        autoKondisi($(this).closest('.sto-row'));
    });

    // ── FORM SUBMIT ───────────────────────────────────────
    $('#btnSave, #btnSaveMobile').on('click', function (e) {
        e.preventDefault();
        let articles = [], hasError = false, errorRow = 0;

        $('.sto-row').each(function (index) {
            const $row        = $(this);
            const articleId   = $row.find('.part-select').val();
            const articleCode = $row.find('.article-code').val()?.trim();
             const qtyRaw      = $row.find('input[name$="[qty]"]').val(); // ← deklarasi qtyRaw
            const qty         = parseFloat($row.find('input[name$="[qty]"]').val());
            const location    = $row.find('.location-input').val();
            const uom         = $row.find('.part-uom').val()?.trim();
            const otherName   = $row.find('input[name$="[other_name]"]').val()?.trim() || null;
           const isRef       = $row.data('is-ref') == 1; // ← dari data-is-ref attribute

    // Skip baris kosong (tidak ada part dipilih sama sekali)
    if (!articleId && !articleCode) return;

    const qtyKosong = qtyRaw === '' || qtyRaw === null || qtyRaw === undefined;

    if (qtyKosong) {
        if (isRef) return;           // ✅ baris referensi → skip saja
        hasError = true;             // ✅ baris manual → error
        errorRow = index + 1;
        return false;
    }

    if (isNaN(qty) || qty < 0) { hasError = true; errorRow = index + 1; return false; }
            const kondisi = IS_CHEM_ONLY ? ($row.find('.kondisi-select').val() || null) : null;
            articles.push({ article_code: articleCode, other_name: articleCode === 'OTHER' ? otherName : null,
                qty, uom: uom || null, kondisi, location });
        });

        if (hasError) return Swal.fire({ icon: 'warning', title: 'Qty tidak valid', text: `Qty baris ke-${errorRow} harus ≥ 0` });
        if (!articles.length) return Swal.fire({ icon: 'warning', title: 'Data kosong', text: 'Minimal 1 item STO harus diisi' });

        const payload = {
            sto_number: getStoNumber(),
            area  : $('#area_desktop').val() || $('#area_value_mobile').val() || '',
             // ✅ ambil data-shelves (nama), bukan val() yang berisi master_id
    shelf : $('#shelf_desktop').find(':selected').data('shelves')
            || $('#shelf_value_mobile').val()
            || '',
            note  : $('#note').val() || $('#note_mobile').val() || '',
            articles,
            _token: $('meta[name="csrf-token"]').attr('content'),
        };

        $.ajax({
            url   : '/facility/sto/save',
            method: 'POST',
            data  : payload,
            beforeSend() { $('#btnSave, #btnSaveMobile').prop('disabled', true).text('Saving...'); },
            success(res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 2000, showConfirmButton: false });
                fullReset(payload.sto_number);
            },
            error(xhr) {
                if (xhr.status === 422) {
                    let msg = '';
                    $.each(xhr.responseJSON.errors, (_, v) => { msg += `• ${v[0]}\n`; });
                    Swal.fire({ icon: 'error', title: 'Validasi gagal', text: msg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menyimpan STO' });
                }
            },
            complete() { $('#btnSave, #btnSaveMobile').prop('disabled', false).text('Save'); },
        });
    });
syncTableHeader();
    
}); // end document.ready
</script>
@endpush

@endsection