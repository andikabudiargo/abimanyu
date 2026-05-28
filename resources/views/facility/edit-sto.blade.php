@push('scripts')
<script>
const IS_CHEM_CONS   = @json(in_array($warehouse ?? null, ['Chemical', 'Consumable']));
const IS_CHEM_ONLY   = @json(($warehouse ?? null) === 'Chemical');
const IS_SUPER_USER  = @json($isSuperUser);
const IS_SECOND_USER = @json($isSecondUser);
const WAREHOUSE_VAL  = @json($warehouse ?? '');
const STO_ID         = @json($sto->id);
const ARTICLE_SELECT_URL = "{{ route('facility.article.select') }}";
const AREA_URL       = '/facility/sto/reference/areas';

let rowCount     = {{ max($items->count(), $isChemCons ? 1 : 7) }};
const areaCache  = {};

// ── Scroll wheel jangan ubah angka ──────────────────────────
$(document).on('wheel', 'input[type=number]', function (e) {
    e.preventDefault(); $(this).blur();
});

// ══════════════════════════════════════════════════════════
// AUTO KONDISI — readonly, logika sama dengan create
// ══════════════════════════════════════════════════════════
function autoKondisi($row) {
    if (!IS_CHEM_ONLY) return;
    const row    = $row.find('.part-select').data('row');
    const qty    = parseFloat($row.find(`input[name="articles[${row}][qty]"], input[name="articles[${row}][qty_2]"]`).first().val()) || 0;
    const minPkg = parseFloat($row.find('.part-min-package').val()) || 0;
    const $input = $row.find('.kondisi-input');
    const $label = $row.find('.kondisi-label');

    if (minPkg <= 0 || qty <= 0) {
        $input.val(''); $label.text('—').css('color', 'var(--sto-text-muted)');
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
    $label.text(kondisi).css('color', kondisi === 'Utuh' ? 'var(--sto-green-mid)' : '#E53935');
}

// ══════════════════════════════════════════════════════════
// VALIDASI QTY — kelipatan min_package atau di bawahnya
// ══════════════════════════════════════════════════════════
$(document).on('change blur', '.qty-input, .qty2-input', function () {
    if (!IS_CHEM_ONLY) return;
    const $row   = $(this).closest('.sto-row');
    const row    = $row.find('.part-select').data('row');
    const qty    = parseFloat($(this).val());
    const minPkg = parseFloat($row.find('.part-min-package').val()) || 0;

    if (!isNaN(qty) && qty > 0 && minPkg > 0 && qty >= minPkg) {
        if (Math.abs(qty % minPkg) > 0.0001) {
            Swal.fire({
                icon: 'warning', title: 'Qty tidak valid',
                html: `Qty harus kelipatan min packing (<strong>${minPkg}</strong>), atau di bawah ${minPkg}.<br>
                       Contoh: ${minPkg}, ${minPkg*2}, ${minPkg*3}...`,
                timer: 3000, showConfirmButton: false,
            });
            $(this).val('');
            autoKondisi($row);
            return;
        }
    }
    autoKondisi($row);
});

// ══════════════════════════════════════════════════════════
// MIN PACKAGE — editable + auto-save
// ══════════════════════════════════════════════════════════
$(document).on('change blur', '.part-min-package', function () {
    if (!IS_CHEM_CONS) return;
    const $this       = $(this);
    const $row        = $this.closest('.sto-row');
    const row         = $row.find('.part-select').data('row');
    const newVal      = parseFloat($this.val());
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
        },
        error(xhr) {
            console.warn('Gagal update min_package:', xhr.responseJSON?.message);
            $this.css('border-color', '#E53935');
            setTimeout(() => $this.css('border-color', ''), 1500);
        }
    });
});

// ══════════════════════════════════════════════════════════
// LOAD AREAS
// ══════════════════════════════════════════════════════════
function loadAreas(warehouse, selectEl, currentVal) {
    if (!warehouse || !selectEl) return;
    fetch(`${AREA_URL}?warehouse=${encodeURIComponent(warehouse)}`)
        .then(r => r.json())
        .then(data => {
            selectEl.innerHTML = '<option value="">— Pilih Rack —</option>';
            (data.areas || []).forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.area;
                if (a.all_saved) {
                    opt.textContent = `🔒 ${a.area} — Already Closed`;
                    opt.disabled    = true;
                } else {
                    opt.textContent = a.area;
                }
                // Pre-select area saat ini
                if (a.area === currentVal) opt.selected = true;
                selectEl.appendChild(opt);
            });
            // Jika ada current value, trigger load shelf
           if (currentVal) {
    // Panggil langsung (tidak via trigger) agar bisa kirim currentShelf
    const shelfEl = document.getElementById('shelf_edit');
    const currentShelf = selectEl.dataset.currentShelf || '';
    loadShelves(warehouse, currentVal, shelfEl, currentShelf);
}
        })
        .catch(err => console.error('Gagal load area:', err));
}

// ══════════════════════════════════════════════════════════
// LOAD SHELVES
// ══════════════════════════════════════════════════════════
async function loadShelves(warehouse, area, shelfEl, currentShelf) {
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
            const opt = document.createElement('option');
            opt.value           = s.id;
            opt.dataset.shelves = s.shelves;
            if (s.all_saved && s.shelves !== currentShelf) {
                // Tetap tampilkan current shelf meski already_saved (untuk edit)
                opt.textContent = `🔒 ${s.shelves} — Already Closed`;
                opt.disabled    = true;
            } else {
                opt.textContent = s.shelves;
            }
            // Pre-select shelf saat ini
            if (s.shelves === currentShelf) {
                opt.selected  = true;
                opt.disabled  = false; // paksa enable meski locked
            }
            shelfEl.appendChild(opt);
        });

        shelfEl.disabled = false;

        // Update hidden fields
        document.getElementById('shelf_value_edit').value = currentShelf || '';

    } catch (err) {
        console.error('Gagal load shelf:', err);
        shelfEl.innerHTML = '<option value="">Gagal memuat</option>';
        shelfEl.disabled  = true;
    }
}

// ══════════════════════════════════════════════════════════
// SELECT2 CONFIG
// ══════════════════════════════════════════════════════════
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
            data(params) {
                return { q: params.term || null, page: params.page || 1, warehouse: WAREHOUSE_VAL || null };
            },
            processResults(data) {
                return {
                    results: data.results.map(a => ({
                        id: a.id, text: a.text, code: a.article_code,
                        uom: a.unit, minPackage: a.min_package, isOther: false,
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
    autoKondisi($row);
});

$(document).on('select2:clear', '.part-select', function () {
    const $row = $(this).closest('.sto-row');
    const row  = $(this).data('row');
    $(`input[name="articles[${row}][article_code]"]`).val('');
    $(`input[name="articles[${row}][uom]"]`).val('').prop('readonly', true);
    $(`input[name="articles[${row}][min_package]"]`).val('');
    $(`input[name="articles[${row}][other_name]"]`).val('');
    $row.find('.kondisi-input').val('');
    $row.find('.kondisi-label').text('—').css('color', 'var(--sto-text-muted)');
});

// ══════════════════════════════════════════════════════════
// ADD ROW
// ══════════════════════════════════════════════════════════
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

    const kondisiCol = IS_CHEM_ONLY ? `
      <td class="center">
        <input type="hidden" name="articles[${idx}][kondisi]" class="kondisi-input" value="">
        <span class="kondisi-label sto-input readonly"
          style="display:block;text-align:center;line-height:32px;height:32px;
                 font-size:11px;background:var(--sto-surface);
                 border:1px solid var(--sto-border);border-radius:var(--sto-radius-md);
                 color:var(--sto-text-muted);">—</span>
      </td>` : '';

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
      <td class="center">
        <input type="text" name="articles[${idx}][min_package]" value=""
          class="part-min-package sto-input" style="text-align:center;">
      </td>
      <td class="center"><input type="text" name="articles[${idx}][uom]" value=""
            class="part-uom sto-input readonly" readonly style="text-align:center;"></td>
      ${kondisiCol}
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

// ══════════════════════════════════════════════════════════
// AREA CHANGE
// ══════════════════════════════════════════════════════════
$(document).on('change', '#area_edit', async function () {
    const area    = this.value;
    const wh      = this.dataset.warehouse || WAREHOUSE_VAL;
    const shelfEl = document.getElementById('shelf_edit');

    document.getElementById('area_value_edit').value  = area;
    document.getElementById('shelf_value_edit').value = '';
    shelfEl.innerHTML = '<option value="">— Pilih Address —</option>';
    shelfEl.disabled  = true;

    if (!area) return;
    await loadShelves(wh, area, shelfEl, '');
});

// ══════════════════════════════════════════════════════════
// SHELF CHANGE
// ══════════════════════════════════════════════════════════
$(document).on('change', '#shelf_edit', function () {
    const shelfName = $(this).find(':selected').data('shelves') || '';
    document.getElementById('shelf_value_edit').value = shelfName;
});

// ══════════════════════════════════════════════════════════
// DOCUMENT READY
// ══════════════════════════════════════════════════════════
$(document).ready(function () {
    initSelect2OnRows();
    if (window.feather) feather.replace();

    // Inisialisasi auto-kondisi untuk baris existing
    if (IS_CHEM_ONLY) {
        $('.sto-row').each(function () { autoKondisi($(this)); });
    }

    // Load area list + pre-select area & shelf saat ini
    if (IS_CHEM_CONS) {
        const currentArea  = @json($sto->area ?? '');
        const currentShelf = @json($sto->shelves ?? '');
        loadAreas(WAREHOUSE_VAL, document.getElementById('area_edit'), currentArea);
        // loadShelves dipanggil dari dalam loadAreas via trigger change
        // tapi kita perlu tahu currentShelf — simpan ke dataset sementara
        document.getElementById('area_edit').dataset.currentShelf = currentShelf;
    }

    // ══════════════════════════════════════════════════════
    // FORM SUBMIT
    // ══════════════════════════════════════════════════════
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
            const kondisi     = IS_CHEM_ONLY ? ($row.find('.kondisi-input').val() || null) : null;

            if (!articleCode && !itemId) return;

            const qtyRaw  = $row.find(`input[name="articles[${row}][qty]"]`).val();
            const qty2Raw = $row.find(`input[name="articles[${row}][qty_2]"]`).val();

            if (IS_SUPER_USER) {
                if (articleCode && qtyRaw === '' && qty2Raw === '') {
                    hasError = true; errorRow = index + 1; return false;
                }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : qtyRaw  !== '' ? parseFloat(qtyRaw)  : null,
                    qty_2       : qty2Raw !== '' ? parseFloat(qty2Raw) : null,
                    uom         : uom || null, location, kondisi,
                });
            } else if (IS_SECOND_USER) {
                if (articleCode && qty2Raw === '') { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty_2       : qty2Raw !== '' ? parseFloat(qty2Raw) : null,
                    uom         : uom || null, location, kondisi,
                });
            } else {
                if (articleCode && qtyRaw === '') { hasError = true; errorRow = index + 1; return false; }
                articles.push({
                    item_id     : itemId,
                    article_code: articleCode,
                    other_name  : articleCode === 'OTHER' ? otherName : null,
                    qty         : qtyRaw !== '' ? parseFloat(qtyRaw) : null,
                    uom         : uom || null, location, kondisi,
                });
            }
        });

        if (hasError) return Swal.fire({ icon: 'warning', title: 'Qty tidak valid', text: `Qty baris ke-${errorRow} harus diisi` });
        if (!articles.filter(a => a.article_code).length)
            return Swal.fire({ icon: 'warning', title: 'Data kosong', text: 'Minimal 1 item harus diisi' });

        const payload = {
            note       : $('#note').val() || '',
            area       : document.getElementById('area_value_edit')?.value  || '',
            shelf      : document.getElementById('shelf_value_edit')?.value || '',
            articles,
            _token     : $('meta[name="csrf-token"]').attr('content'),
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