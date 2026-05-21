// ══════════════════════════════════════════════════════════
// KONSTANTA GLOBAL
// ══════════════════════════════════════════════════════════
const IS_CHEM_CONS       = @json(in_array($warehouse ?? null, ['Chemical', 'Consumable']));
const IS_CHEM_ONLY       = @json(($warehouse ?? null) === 'Chemical');
const WAREHOUSE_VAL      = @json($warehouse ?? '');
const AREA_URL           = '/facility/sto/reference/areas';
const ITEMS_URL          = '/facility/sto/reference/items';
const ARTICLE_SELECT_URL = "{{ route('facility.article.select') }}";

let desktopRowCount = 7;
let mobileRowCount  = 8;

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
    const editable = loc.includes('chemical') || loc.includes('consumable');
    if (code === 'OTHER') { $uom.prop('readonly', false); return; }
    $uom.prop('readonly', !editable);
}

function autoKondisi($row) {
    if (!IS_CHEM_ONLY) return;
    const row      = $row.find('.part-select').data('row');
    const qty      = parseFloat($row.find(`input[name="articles[${row}][qty]"]`).val()) || 0;
    const minPkg   = parseFloat($row.find(`input[name="articles[${row}][min_package]"]`).val()) || 0;
    const $kondisi = $row.find('.kondisi-select');
    if (!$kondisi.length || minPkg <= 0) { $kondisi.val(''); return; }
    const isMultiple = qty > 0 && Math.abs(qty % minPkg) < 0.0001;
    $kondisi.val(isMultiple ? 'Utuh' : 'Tidak Utuh');
}

// ══════════════════════════════════════════════════════════
// SELECT2
// ══════════════════════════════════════════════════════════
function getSelect2Config(warehouseSelector) {
    return {
        placeholder: '— pilih part —',
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
                    pagination: data.pagination,
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
// BUILD ROW HTML
// ══════════════════════════════════════════════════════════
function buildDesktopRowHtml(idx, opts = {}) {
    const { articleCode = '', uom = '', minPackage = '', qty = '',
            isRef = false, location = WAREHOUSE_VAL, kondisi = '' } = opts;

    return `
    <tr class="sto-row" data-is-ref="${isRef ? 1 : 0}">
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
      <td class="center">
        <input type="number" min="0" name="articles[${idx}][qty]" value="${qty}"
          class="qty-input sto-input" style="text-align:center;">
      </td>
      <td class="center">
        <input type="text" name="articles[${idx}][min_package]" value="${minPackage}"
          class="part-min-package sto-input readonly" readonly style="text-align:center;">
      </td>
      <td class="center">
        <input type="text" name="articles[${idx}][uom]" value="${uom}"
          class="part-uom sto-input ${IS_CHEM_CONS ? '' : 'readonly'}"
          ${IS_CHEM_CONS ? '' : 'readonly'} style="text-align:center;">
      </td>
      ${IS_CHEM_ONLY ? `
      <td class="center">
        <select name="articles[${idx}][kondisi]" class="kondisi-select sto-select"
          style="text-align:center; font-size:11px; height:32px;">
          <option value="">—</option>
          <option value="Utuh" ${kondisi === 'Utuh' ? 'selected' : ''}>Utuh</option>
          <option value="Tidak Utuh" ${kondisi === 'Tidak Utuh' ? 'selected' : ''}>Tidak Utuh</option>
        </select>
      </td>` : ''}
      <td class="center">
        <input type="text" name="articles[${idx}][location]" value="${location}"
          readonly class="location-input sto-input readonly" style="text-align:center;">
      </td>
    </tr>`;
}

function buildMobileRowHtml(idx, opts = {}) {
    const { articleCode = '', uom = '', minPackage = '',
            qty = '', location = WAREHOUSE_VAL } = opts;
    return `
    <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 overflow-hidden sto-row" data-row="${idx}">
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
        <div class="grid grid-cols-2 gap-3 mt-3">
          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty</label>
            <input type="number" min="0" name="articles[${idx}][qty]" value="${qty}"
              class="qty-input w-full border rounded px-2 py-1 text-sm">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty Box</label>
            <input type="number" min="0" name="articles[${idx}][min_package]" value="${minPackage}"
              class="w-full border rounded px-2 py-1 bg-gray-100 text-sm" readonly>
          </div>
        </div>
        <div class="mt-3">
          <label class="text-xs font-semibold text-gray-600 mb-1 block">UOM</label>
          <input type="text" name="articles[${idx}][uom]" value="${uom}"
            class="part-uom w-full border rounded px-2 py-1 bg-gray-100 text-sm" readonly>
        </div>
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Location</label>
        <input type="text" name="articles[${idx}][location]" value="${location}" readonly
          class="location-input w-full bg-gray-100 border rounded px-2 py-1 text-sm">
      </div>
    </div>`;
}

// ══════════════════════════════════════════════════════════
// RESET ROWS
// ══════════════════════════════════════════════════════════
function resetDesktopRows(n = 7) {
    const tbody = document.getElementById('article-table');
    if (!tbody) return;
    tbody.innerHTML = '';
    for (let i = 0; i < n; i++) tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(i));
    desktopRowCount = n;
    initSelect2OnRows();
    if (window.feather) feather.replace();
}

function resetMobileRows(n = 8) {
    const list = document.getElementById('mobile-article-list');
    if (!list) return;
    list.innerHTML = '';
    for (let i = 0; i < n; i++) list.insertAdjacentHTML('beforeend', buildMobileRowHtml(i));
    mobileRowCount = n;
    initSelect2OnRows();
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
                opt.textContent = a.area;
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

// ══════════════════════════════════════════════════════════
// POPULATE FROM ITEMS (dari cache)
// ══════════════════════════════════════════════════════════
function populateFromItems(items, area, shelf, mode = 'desktop') {
    const isMobile  = mode === 'mobile';
    const overlayEl = document.getElementById('rowsLoadingOverlay');
    if (overlayEl) overlayEl.classList.add('active');

    if (!isMobile) {
        const tbody = document.getElementById('article-table');
        if (!tbody) return;
        tbody.innerHTML = '';
        const total = Math.max(7, items.length);
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
        resetMobileRows(Math.max(8, items.length));
    }

    initSelect2OnRows();

    items.forEach((item, i) => {
        if (!item.article_code) return;
        const text = item.description
            ? `${item.article_code} - ${item.description}`
            : item.article_code;
        const $sel = $(`select[name="articles[${i}][article_id]"]`);
        $sel.append(new Option(text, item.article_code, true, true)).trigger('change');
        $(`input[name="articles[${i}][article_code]"]`).val(item.article_code);
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
                const total = Math.max(7, items.length);
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

    if (usedStoNumber) {
        $(`#sto_number_mobile option[value="${usedStoNumber}"],
           #sto_number_desktop option[value="${usedStoNumber}"]`).remove();
    }
    $('#sto_number_mobile, #sto_number_desktop').val(null).trigger('change');

    ['desktop', 'mobile'].forEach(pfx => {
        const areaEl  = document.getElementById(`area_${pfx}`);
        const shelfEl = document.getElementById(`shelf_${pfx}`);
        const stoHid  = document.getElementById(pfx === 'desktop' ? 'sto_number_desktop' : 'sto_number_mobile');
        const refHid  = document.getElementById(`ref_master_id_${pfx}`);
        if (areaEl)  areaEl.value = '';
        if (shelfEl) { shelfEl.innerHTML = '<option value="">— Pilih rack dulu —</option>'; shelfEl.disabled = true; }
        if (stoHid)  stoHid.value = '';
        if (refHid)  refHid.value = '';
        const areaValEl  = document.getElementById(`area_value_${pfx}`);
        const shelfValEl = document.getElementById(`shelf_value_${pfx}`);
        if (areaValEl)  areaValEl.value  = '';
        if (shelfValEl) shelfValEl.value = '';
    });

    // Bersihkan cache agar data fresh setelah save
    Object.keys(areaCache).forEach(k => delete areaCache[k]);

    if (IS_CHEM_CONS) {
        loadAreas(WAREHOUSE_VAL, document.getElementById('area_desktop'));
        loadAreas(WAREHOUSE_VAL, document.getElementById('area_mobile'));
    }

    ['refBanner', 'refBannerMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
    ['btnClearRef', 'btnClearRefMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    if (isMobile) {
        $('.sto-row').each(function () {
            const $row = $(this);
            const idx  = $row.data('row');
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
    } else {
        resetDesktopRows(7);
    }

    $('#note, #note_mobile').val('');
    $('#warehouse-null, #warehouse-null-desktop').val(null).trigger('change');
    $('.location-input').val(WAREHOUSE_VAL);
}

// ══════════════════════════════════════════════════════════
// DOCUMENT READY — hanya event handler, tidak ada fungsi
// ══════════════════════════════════════════════════════════
$(document).ready(function () {

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
    $(document).on('change', '#area_desktop', function () {
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
        resetDesktopRows(7);
        if (area) loadShelves(wh, area, shelfEl);
    });

    $(document).on('change', '#area_mobile', function () {
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
        resetMobileRows(8);
        if (area) loadShelves(wh, area, shelfEl);
    });

    // ── SHELF CHANGE ─────────────────────────────────────
    $(document).on('change', '#shelf_desktop', function () {
        const masterId    = $(this).val();
        const area        = document.getElementById('area_desktop')?.value || '';
        const shelvesName = $(this).find(':selected').data('shelves') || '';
        const cacheKey    = `${WAREHOUSE_VAL}|${area}`;
        document.getElementById('ref_master_id_desktop').value = masterId || '';
        if (!masterId) { resetDesktopRows(7); return; }

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
        resetMobileRows(8);
    });

    // ── ADD ROW ───────────────────────────────────────────
    $('#btnAddRow').on('click', function () {
        const tbody = document.getElementById('article-table');
        if (!tbody) return;
        tbody.insertAdjacentHTML('beforeend', buildDesktopRowHtml(desktopRowCount));
        desktopRowCount++;
        initSelect2OnRows();
        if (window.feather) feather.replace();
    });

    $('#btnAddRowMobile').on('click', function () {
        const list = document.getElementById('mobile-article-list');
        if (!list) return;
        list.insertAdjacentHTML('beforeend', buildMobileRowHtml(mobileRowCount));
        mobileRowCount++;
        initSelect2OnRows();
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
        const $code   = $(`input[name="articles[${row}][article_code]"]`);
        const $uom    = $(`input[name="articles[${row}][uom]"]`);
        const $minPkg = $(`input[name="articles[${row}][min_package]"]`);
        const $other  = $(`input[name="articles[${row}][other_name]"]`);
        const $header = $row.find('.header-label');
        if (isOther) {
            $code.val('OTHER');
            $uom.val('').prop('readonly', false);
            $minPkg.val('').prop('readonly', true);
            $other.val(data.text);
            $header.text(data.text);
        } else {
            $code.val(data.code || data.id || '');
            $uom.val(data.uom || '');
            $minPkg.val(data.minPackage || $(this).find(':selected').data('min-package') || '').prop('readonly', true);
            $other.val('');
            $header.text(data.text);
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
            const qty         = parseFloat($row.find('input[name$="[qty]"]').val());
            const location    = $row.find('.location-input').val();
            const uom         = $row.find('.part-uom').val()?.trim();
            const otherName   = $row.find('input[name$="[other_name]"]').val()?.trim() || null;
            if (!articleId) return;
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
            shelf : $('#shelf_desktop').val() || $('#shelf_value_mobile').val() || '',
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

}); // end document.ready