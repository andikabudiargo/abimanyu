@extends('layouts.app')

@section('title', 'Create ATK Request')
@section('page-title', 'CREATE ATK REQUEST')
@section('breadcrumb-item', 'GA Inventory Management')
@section('breadcrumb-active', 'Create ATK Request')

@section('content')

  <div class="bg-white shadow rounded-xl overflow-hidden">

    {{-- Card Header --}}
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
        <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-sm font-semibold text-gray-800">Form ATK Request</h2>
        <p class="text-xs text-gray-400">Form pengajuan alat tulis kantor</p>
      </div>
    </div>

    <div class="px-6 py-5 space-y-6">

      {{-- ── Informasi Pemohon ── --}}
      <div>
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-3">Informasi Pemohon</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1.5">Nama Pemohon</label>
            <div class="flex items-center gap-2 py-2 rounded-lg">
              <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span class="text-[9px] font-bold text-blue-600">
                  {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
              </div>
              <span class="text-xs font-medium text-gray-700 truncate">{{ Auth::user()->name }}</span>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1.5">Department</label>
            <div class="flex items-center gap-2 py-2  rounded-lg">
              <i data-feather="briefcase" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
              <span class="text-xs font-medium text-gray-700 truncate">
               {{ Auth::user()->departments->first()->name ?? '—' }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-medium text-gray-500 mb-1.5">Tanggal Request</label>
            <div class="flex items-center gap-2 py-2  rounded-lg">
              <i data-feather="calendar" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
              <span class="text-xs font-medium text-gray-700">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
          </div>

        </div>
      </div>

      

      {{-- ── Item ATK ── --}}
      <div>
        <div class="flex items-center justify-between mb-3">
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Item ATK</p>
          <span class="text-[11px] text-gray-400" id="itemCount">0 item</span>
        </div>

        {{-- Column Headers --}}
        <div class="hidden sm:grid sm:grid-cols-[2rem_1fr_7rem_6rem_6rem_7rem_2rem] gap-2 px-3 pb-2 border-b border-gray-100">
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider text-center">#</span>
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Pilih ATK</span>
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider text-center">Stock</span>
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider text-center">Qty</span>
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider text-center">UoM</span>
          <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider text-center">Status</span>
          <span></span>
        </div>

        {{-- Rows --}}
        <div id="requestRows" class="space-y-2 mt-3"></div>

        {{-- Add Row --}}
        <button type="button" onclick="addRequestRow()"
          class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 rounded-lg border border-dashed border-gray-300
                 text-xs text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50/50 transition">
          <i data-feather="plus" class="w-3.5 h-3.5"></i> Tambah Item
        </button>

        {{-- Warning Banner --}}
        <div id="stockWarningBanner" class="hidden mt-3 flex items-start gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
          <i data-feather="alert-triangle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
          <p class="text-xs text-red-600" id="stockWarningText"></p>
        </div>
      </div>

      <div class="border-t border-gray-100"></div>

      {{-- ── Catatan ── --}}
      <div>
        <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-3">
          Catatan <span class="text-gray-300 normal-case tracking-normal font-normal">(opsional)</span>
        </label>
        <textarea id="requestNotes" rows="3"
          placeholder="Tambahkan catatan atau keperluan khusus..."
          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none placeholder-gray-300 text-gray-700">
        </textarea>
      </div>

    </div>

    {{-- ── Card Footer / Actions ── --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50">
      <a href="{{ route('facility.atk.index') }}"
        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
        <i data-feather="arrow-left" class="w-3.5 h-3.5"></i> Kembali
      </a>
      <div class="flex items-center gap-2">
        <button type="button" onclick="resetForm()"
          class="px-4 py-2 rounded-lg border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 transition bg-white">
          Reset
        </button>
        <button type="button" onclick="submitRequest()" id="btnSubmitRequest"
          class="flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow transition">
          <i data-feather="send" class="w-3.5 h-3.5"></i> Submit Request
        </button>
      </div>
    </div>

  </div>

{{-- Row Template --}}
<template id="tmplRequestRow">
  <div class="request-row grid grid-cols-[2rem_1fr] sm:grid-cols-[2rem_1fr_7rem_6rem_6rem_7rem_2rem] gap-2 items-center
              bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5" data-request-row>
    <span class="text-[11px] font-medium text-gray-400 text-center row-number">1</span>
    <div class="min-w-0">
      <select class="req-select-atk w-full text-xs" name="items[__IDX__][atk_id]">
        <option value="">— Pilih ATK —</option>
      </select>
    </div>
    <div class="text-center">
      <span class="text-[10px] text-gray-400 sm:hidden">Stock: </span>
      <span class="text-xs font-semibold row-stock text-gray-600">—</span>
    </div>
    <div>
      <input type="number" name="items[__IDX__][qty]" min="1" value="" placeholder="0"
        class="req-qty w-full text-center text-xs font-semibold text-gray-800
               bg-white border border-gray-200 rounded-lg h-8
               focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300">
    </div>
    <div class="text-center">
      <span class="text-xs text-gray-500 row-uom">—</span>
    </div>
    <div class="text-center row-status-wrap">
      <span class="text-[10px] text-gray-300 italic">Pilih item</span>
    </div>
    <button type="button" onclick="removeRequestRow(this)"
      class="w-6 h-6 flex items-center justify-center rounded-lg mx-auto
             bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition flex-shrink-0">
      <i data-feather="trash-2" class="w-3 h-3"></i>
    </button>
  </div>
</template>

@push('scripts')
<style>
  .req-select-atk + .select2-container .select2-selection--single {
    height: 32px !important; display: flex; align-items: center;
    border-color: #e5e7eb; border-radius: 0.5rem;
  }
  .req-select-atk + .select2-container .select2-selection__rendered {
    line-height: 32px !important; font-size: 12px; padding-left: 10px;
  }
  .req-select-atk + .select2-container .select2-selection__arrow { height: 32px !important; }
  input[type="number"]::-webkit-outer-spin-button,
  input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }
  input[type="number"] { -moz-appearance: textfield; }
</style>

<script>
let reqRowIndex   = 0;
let reqAtkOptions = [];

$.ajax({
    url: '{{ route("facility.atk.data.stock") }}',
    method: 'GET',
    success: function (res) {
        reqAtkOptions = res.data ?? [];
        addRequestRow();
    }
});

function addRequestRow() {
    const idx  = reqRowIndex++;
    const tmpl = document.getElementById('tmplRequestRow').innerHTML.replace(/__IDX__/g, idx);
    const $row = $(tmpl);
    $row.find('.row-number').text($('#requestRows [data-request-row]').length + 1);
    const $sel = $row.find('.req-select-atk');
    reqAtkOptions.forEach(atk => $sel.append(new Option(atk.name, atk.id, false, false)));
    $('#requestRows').append($row);
    $sel.select2({ placeholder: '— Cari & pilih ATK —', allowClear: true, width: '100%' });
    $sel.on('change', function () { onAtkChange($(this).closest('[data-request-row]')); });
    $row.find('.req-qty').on('input', function () { validateQty($(this).closest('[data-request-row]')); });
    feather.replace();
    updateItemCount();
}

function removeRequestRow(btn) {
    const $row = $(btn).closest('[data-request-row]');
    const $sel = $row.find('.req-select-atk');
    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $row.remove();
    $('#requestRows [data-request-row]').each(function (i) { $(this).find('.row-number').text(i + 1); });
    updateItemCount();
    checkDuplicates();
}

function onAtkChange($row) {
    const atkId = $row.find('.req-select-atk').val();
    const atk   = reqAtkOptions.find(a => a.id == atkId);
    if (!atk) {
        $row.find('.row-stock').text('—');
        $row.find('.row-uom').text('—');
        $row.find('.row-status-wrap').html('<span class="text-[10px] text-gray-300 italic">Pilih item</span>');
        $row.find('.req-qty').val('').attr('max', '');
        return;
    }
    const balance = (atk.initial_stock ?? 0) + (atk.total_in ?? 0) - (atk.total_out ?? 0);
    $row.find('.row-stock').text(balance);
    $row.find('.row-uom').text(atk.uom ?? '—');
    $row.find('.req-qty').attr('max', balance).val('');
    renderStockStatus($row, atk, balance);
    checkDuplicates();
}

function validateQty($row) {
    const atkId = $row.find('.req-select-atk').val();
    const atk   = reqAtkOptions.find(a => a.id == atkId);
    if (!atk) return;
    const balance   = (atk.initial_stock ?? 0) + (atk.total_in ?? 0) - (atk.total_out ?? 0);
    const $qtyInput = $row.find('.req-qty');
    const qty       = parseInt($qtyInput.val()) || 0;
    if (balance <= 0) {
        $qtyInput.val('').prop('disabled', true);
        showWarning(`Stok "${atk.name}" kosong. Tidak dapat melakukan request.`);
        return;
    }
    $qtyInput.prop('disabled', false);
    if (qty > balance) {
        $qtyInput.val('').addClass('border-red-400 bg-red-50').removeClass('border-gray-200');
        showWarning(`Qty untuk "${atk.name}" tidak boleh melebihi stok tersedia (${balance} ${atk.uom ?? ''}).`);
        setTimeout(() => {
            $qtyInput.removeClass('border-red-400 bg-red-50').addClass('border-gray-200');
            hideWarning();
        }, 5000);
        return;
    }
    $qtyInput.removeClass('border-red-400 bg-red-50').addClass('border-gray-200');
}

function renderStockStatus($row, atk, balance) {
    let badge;
    if (balance <= 0) {
        badge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-600 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Empty</span>`;
    } else if (balance <= (atk.min_stock ?? 0)) {
        badge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Low Stock</span>`;
    } else {
        badge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>`;
    }
    $row.find('.row-status-wrap').html(badge);
}

function checkDuplicates() {
    const seen    = {};
    const dupMsgs = [];

    $('#requestRows [data-request-row]').each(function (i) {
        const rowNum = i + 1;
        const val    = $(this).find('.req-select-atk').val();
        const name   = $(this).find('.req-select-atk option:selected').text().trim();

        if (!val) return;

        if (seen[val]) {
            seen[val].rows.push(rowNum);
        } else {
            seen[val] = { name, rows: [rowNum] };
        }
    });

    Object.values(seen).forEach(({ name, rows }) => {
        if (rows.length > 1) {
            dupMsgs.push(`"${name}" duplikat di baris ${rows.join(', ')}`);
        }
    });

    if (dupMsgs.length > 0) {
        showWarning('Terdapat item duplikat: ' + dupMsgs.join(' · '));
    } else {
        hideWarning();
    }
}

function showWarning(msg) {
    $('#stockWarningText').text(msg);
    $('#stockWarningBanner').removeClass('hidden');
    feather.replace();
}
function hideWarning() { $('#stockWarningBanner').addClass('hidden'); }
function updateItemCount() { $('#itemCount').text(`${$('#requestRows [data-request-row]').length} item`); }

function resetForm() {
    $('#requestRows [data-request-row]').each(function () {
        const $sel = $(this).find('.req-select-atk');
        if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    });
    $('#requestRows').html('');
    reqRowIndex = 0;
    hideWarning();
    $('#requestNotes').val('');
    addRequestRow();
}

function submitRequest() {
    const items = [];
    let valid   = true;
    const seen  = {};

    $('#requestRows [data-request-row]').each(function () {
        const atkId = $(this).find('.req-select-atk').val();
        const qty   = parseInt($(this).find('.req-qty').val());
        if (!atkId) { showWarning('Pilih item ATK terlebih dahulu di semua baris.'); valid = false; return false; }
        if (seen[atkId]) { showWarning('Terdapat item ATK yang duplikat.'); valid = false; return false; }
        const atk     = reqAtkOptions.find(a => a.id == atkId);
        const balance = atk ? (atk.initial_stock ?? 0) + (atk.total_in ?? 0) - (atk.total_out ?? 0) : 0;
        if (!qty || qty < 1) { showWarning('Qty tidak boleh kosong atau nol.'); valid = false; return false; }
        if (qty > balance) { showWarning(`Qty untuk "${atk?.name}" melebihi stok (${balance}).`); valid = false; return false; }
        seen[atkId] = true;
        items.push({ atk_id: atkId, qty });
    });

    if (!valid || items.length === 0) return;

    const $btn = $('#btnSubmitRequest');
    $btn.prop('disabled', true).html('<i data-feather="loader" class="w-3.5 h-3.5 inline animate-spin"></i> Menyimpan...');
    feather.replace();

    $.ajax({
        url: '{{ route("facility.atk.request") }}',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ items, notes: $('#requestNotes').val() }),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (res) {
            Swal.fire({
                icon: 'success', title: 'Request Terkirim!',
                text: res.message ?? 'ATK Request berhasil disubmit.',
                confirmButtonText: 'OK', confirmButtonColor: '#2563eb',
            }).then(() => { window.location.href = '{{ route("facility.atk.index") }}'; });
        },
        error: function (xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.' });
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i data-feather="send" class="w-3.5 h-3.5 inline"></i> Submit Request');
            feather.replace();
        }
    });
}
</script>
@endpush

@endsection