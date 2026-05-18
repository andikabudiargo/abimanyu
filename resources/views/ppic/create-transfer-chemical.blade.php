@extends('layouts.app')

@section('title', 'Create Transfer Chemical')
@section('page-title', 'CREATE TRANSFER CHEMICAL')
@section('breadcrumb-item', 'PPIC')
@section('breadcrumb-active', 'Create Transfer Chemical')

@section('content')
<div class="space-y-4">

  {{-- Header Card --}}
  <div class="w-full bg-white shadow-md rounded-xl px-8 space-y-4 pt-6 pb-12">
    <div class="flex items-start gap-3 border-b border-gray-200 pb-3 mb-6 -mx-8 px-8">
      <div>
        <h2 class="text-base font-semibold text-slate-800 tracking-wide">Transfer Chemical</h2>
        <p class="text-sm text-gray-500">Create and manage chemical stock transfer with automated unit conversion</p>
      </div>
    </div>

    <form id="transferForm" class="space-y-4">
      @csrf

      {{-- Row 1: Date, Location From, Location To --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Transfer Date <span class="text-red-600">*</span>
          </label>
          <input type="date" name="transfer_date" id="transfer_date"
            max="{{ date('Y-m-d') }}"
            value="{{ date('Y-m-d') }}"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
            required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Location From <span class="text-red-600">*</span>
          </label>
          <select name="location_from" id="location_from"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
            required>
            <option value="">-- Pilih Lokasi --</option>
            <option value="Warehouse Chemical">Warehouse Chemical</option>
            <option value="Spraybooth 1A">Spraybooth 1A</option>
            <option value="Spraybooth 1B">Spraybooth 1B</option>
            <option value="Spraybooth 1C">Spraybooth 1C</option>
            <option value="Spraybooth 2A">Spraybooth 2A</option>
            <option value="Spraybooth 2B">Spraybooth 2B</option>
            <option value="Spraybooth 2C">Spraybooth 2C</option>
            <option value="Spraybooth 3A">Spraybooth 3A</option>
            <option value="Spraybooth 3B">Spraybooth 3B</option>
            <option value="Spraybooth 3C">Spraybooth 3C</option>
            <option value="Spraybooth 4A">Spraybooth 4A</option>
            <option value="Spraybooth 4B">Spraybooth 4B</option>
            <option value="Spraybooth 4C">Spraybooth 4C</option>
            <option value="Spraybooth 5A">Spraybooth 5A</option>
            <option value="Spraybooth 5B">Spraybooth 5B</option>
            <option value="Spraybooth 5C">Spraybooth 5C</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Location To <span class="text-red-600">*</span>
          </label>
          <select name="location_to" id="location_to"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
            required>
           
            <option value="">-- Pilih Lokasi --</option>
            <option value="Warehouse Chemical">Warehouse Chemical</option>
            <option value="Spraybooth 1A">Spraybooth 1A</option>
            <option value="Spraybooth 1B">Spraybooth 1B</option>
            <option value="Spraybooth 1C">Spraybooth 1C</option>
            <option value="Spraybooth 2A">Spraybooth 2A</option>
            <option value="Spraybooth 2B">Spraybooth 2B</option>
            <option value="Spraybooth 2C">Spraybooth 2C</option>
            <option value="Spraybooth 3A">Spraybooth 3A</option>
            <option value="Spraybooth 3B">Spraybooth 3B</option>
            <option value="Spraybooth 3C">Spraybooth 3C</option>
            <option value="Spraybooth 4A">Spraybooth 4A</option>
            <option value="Spraybooth 4B">Spraybooth 4B</option>
            <option value="Spraybooth 4C">Spraybooth 4C</option>
            <option value="Spraybooth 5A">Spraybooth 5A</option>
            <option value="Spraybooth 5B">Spraybooth 5B</option>
            <option value="Spraybooth 5C">Spraybooth 5C</option>
          </select>
        </div>

      </div>

      {{-- Row 2: Note --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <textarea name="note" id="note" rows="3"
          placeholder="Tambahkan catatan jika diperlukan..."
          class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm text-sm
                 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"></textarea>
      </div>

    </form>
  </div>

  {{-- Chemical List Card --}}
  <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">
    <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
      <p class="text-sm font-semibold text-slate-800">Add List Chemical</p>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 bg-white">
      <table id="itemTable" class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="p-2">No</th>
            <th class="p-2">Code</th>
            <th class="p-2">Min Package</th>
            <th class="p-2">Qty</th>
             <th class="p-2">Condition</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody id="chemTableBodyDesktop"></tbody>
      </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="block md:hidden space-y-4" id="chemTableBodyMobile"></div>

    <div class="mt-3 flex justify-end">
      <button type="button" id="addRowBtn"
        class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm font-medium
               px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
        <span class="text-lg leading-none">+</span> Add Row
      </button>
    </div>

     {{-- Transfer Summary --}}
    <div class="mt-6 flex justify-start">
      <div class="w-full md:w-[480px] border border-gray-200 bg-white rounded-md pb-6 overflow-hidden">
 
        {{-- Header --}}
        <div class="flex items-center gap-2 border-b border-gray-200 py-3 px-4 mb-4">
          <i class="fa-solid fa-file text-indigo-700 text-sm"></i>
          <h2 class="text-base font-semibold text-indigo-700 tracking-wide">Transfer Summary</h2>
        </div>
 
        {{-- Location Flow --}}
        <div id="summaryLocation"
          class="mx-4 mb-4 flex items-center justify-between gap-2
                 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
          <div class="flex flex-col items-center text-center min-w-[100px]">
            <i class="fa-solid fa-warehouse text-indigo-500 text-lg mb-1"></i>
            <span class="text-xs font-medium text-indigo-700" id="summaryFrom">—</span>
          </div>
          <div class="flex-1 flex flex-col items-center gap-1">
            <i class="fa-solid fa-arrow-right text-indigo-400 text-xl"></i>
          </div>
          <div class="flex flex-col items-center text-center min-w-[100px]">
            <i class="fa-solid fa-warehouse text-indigo-500 text-lg mb-1"></i>
            <span class="text-xs font-medium text-indigo-700" id="summaryTo">—</span>
          </div>
        </div>
 
        {{-- Stats --}}
        <div class="divide-y divide-gray-100 text-sm px-2">
          <div class="flex justify-between px-4 py-2">
            <span class="text-gray-500">Chemical Utuh</span>
            <span class="font-semibold text-gray-900">
              <span id="summaryUtuh">0</span>
              <span class="text-gray-400 font-normal text-xs">item</span>
            </span>
          </div>
          <div class="flex justify-between px-4 py-2">
            <span class="text-gray-500">Chemical Tidak Utuh / Sisa</span>
            <span class="font-semibold text-gray-900">
              <span id="summarySisa">0</span>
              <span class="text-gray-400 font-normal text-xs">item</span>
            </span>
          </div>
          <div class="flex justify-between px-4 py-2 bg-gray-50 rounded-b">
            <span class="text-gray-700 font-medium">Total Chemical</span>
            <span class="font-bold text-indigo-700">
              <span id="summaryTotal">0</span>
              <span class="text-indigo-400 font-normal text-xs">item</span>
            </span>
          </div>
        </div>
 
      </div>
    </div>

    <hr class="mt-8">

    {{-- Action Buttons --}}
    <div class="flex flex-col md:flex-row gap-2 mt-4">
      <a href="{{ route('ppic.tfcm1.index') }}"
        class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2
               bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
        <i data-feather="arrow-left" class="h-4 w-4"></i> Back
</a>
      <button type="submit" form="transferForm" id="submitBtn"
        class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2
               bg-green-700 hover:bg-green-800 text-white rounded shadow">
        <i data-feather="save" class="h-4 w-4"></i> Save
      </button>
    </div>

  </div>
</div>

<style>
  #itemTable th,
  #itemTable td {
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    padding: 8px 12px;
    vertical-align: middle;
    white-space: nowrap;
    font-size: 0.875rem;
  }

  #itemTable tbody tr:nth-child(even) { background-color: #f9fafb; }
  #itemTable tbody tr:nth-child(odd)  { background-color: #ffffff; }
  #itemTable tbody tr:hover           { background-color: #e0f2fe; }

  .select2-container .select2-selection--single {
    height: 38px;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
  }
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #60a5fa;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.4);
  }

  select:disabled {
    background-color: #f3f4f6;
    color: #9ca3af;
  }
</style>

@push('scripts')
<script>
  $(document).ready(function () {
  $('#location_from, #location_to').select2({
    placeholder: '-- Pilih Lokasi --',
    allowClear: true,
    width: '100%'
  });
});

function syncLocationOptions() {
  const from = $('#location_from').val();
  const to   = $('#location_to').val();

  // reset semua dulu
  $('#location_to option').prop('disabled', false);
  $('#location_from option').prop('disabled', false);

  if (from) {
    $('#location_to option[value="' + from + '"]').prop('disabled', true);
  }

  if (to) {
    $('#location_from option[value="' + to + '"]').prop('disabled', true);
  }

  // refresh select2
  $('#location_from').trigger('change.select2');
  $('#location_to').trigger('change.select2');
}

$('#location_from').on('change', syncLocationOptions);
$('#location_to').on('change', syncLocationOptions);
/* =========================
   STATE
========================= */
let chemicals = [];

const CONDITIONS = ['Utuh', 'Tidak Utuh'];

$(function () {
  chemicals = [createEmptyRow()];
  render();

  $('#addRowBtn').on('click', addRow);
  $('#resetBtn').on('click', resetForm);
});

/* =========================
   FACTORY
========================= */
function createEmptyRow() {
  return { id: '', text: '', condition: '', min_package: '', qty: '', unit: '', conditionOverridden: false };
}

/* =========================
   CRUD
========================= */
function addRow() {
  chemicals.push(createEmptyRow());
  render();
}

function removeRow(index) {
  chemicals.splice(index, 1);
  if (chemicals.length === 0) chemicals.push(createEmptyRow());
  render();
}

function updateField(index, field, value) {
  chemicals[index][field] = value;

  // If user manually changes condition, flag as overridden
  if (field === 'condition') {
    chemicals[index].conditionOverridden = true;
     applyConditionUnit(index);
  }

  // Auto-detect condition based on qty vs min_package (unless user overrode)
  if (field === 'qty' && !chemicals[index].conditionOverridden) {
    autoSetCondition(index);
  }

  updateSummary();
}

/* =========================
   AUTO CONDITION
========================= */
function autoSetCondition(index) {
  const row = chemicals[index];
  const qty = parseFloat(row.qty);
  const minPkg = parseFloat(row.min_package);

  if (!qty || !minPkg) return;

  const isMultiple = Number.isInteger(qty / minPkg);
  const autoCondition = isMultiple ? 'Utuh' : 'Tidak Utuh';

  if (row.condition !== autoCondition) {
    row.condition = autoCondition;

    // Update the condition dropdown in DOM directly (no full re-render)
    const $dRow  = $('#chemTableBodyDesktop tr').eq(index);
    const $mCard = $('#chemTableBodyMobile > div').eq(index);
    $dRow.find('select.conditionSelect').val(autoCondition);
    $mCard.find('select.conditionSelect').val(autoCondition);

    applyConditionUnit(index);
    updateSummary();
  }
}

/* =========================
   APPLY UNIT BASED ON CONDITION
   - Tidak Utuh → selalu KG
   - Utuh        → kembali ke unit asli chemical
========================= */
function applyConditionUnit(index) {
  const row = chemicals[index];
  const displayUnit = getDisplayUnit(row);
 
  const $dRow  = $('#chemTableBodyDesktop tr').eq(index);
  const $mCard = $('#chemTableBodyMobile > div').eq(index);
 
  $dRow.find('[data-field="unit"]').text(displayUnit);
  $mCard.find('[data-field="unit"]').text(displayUnit);
}

function resetForm() {
  chemicals = [createEmptyRow()];
  render();
  updateSummary();
}

/* =========================
   RENDER
========================= */
function render() {
  renderDesktop();
  renderMobile();
  initSelect2();
  updateSummary();
}

function getDisplayUnit(c) {
  return c.condition === 'Tidak Utuh' ? 'KG' : (c.unit || '-');
}

/* --- Desktop --- */
function renderDesktop() {
  const rows = chemicals.map((c, i) => `
    <tr>
      <td class="p-2 text-center">${i + 1}</td>

      <td class="p-2 w-[400px]">
        <select class="chemicalSelect w-full" data-index="${i}"></select>
      </td>

     

    <td class="p-2 w-[100px]">
  <div class="flex items-center">
    
    <input type="number" data-field="min_package"
      class="w-full border px-2 py-1 rounded-l text-right bg-gray-100"
      value="${c.min_package}" readonly>

   <span data-field="min_package_unit"
  class="border border-l-0 px-2 py-1 bg-gray-100 rounded-r text-sm">
  ${c.unit || '-'}
</span>

  </div>
</td>

 <td class="p-2 w-[120]">
        <div class="flex">
          <input type="number"
            class="qtyInput w-full border px-2 py-1 text-right rounded-l"
            data-index="${i}"
            value="${c.qty}">
          <span data-field="unit"
            class="px-3 flex items-center border border-l-0 rounded-r bg-gray-100 text-sm min-w-[60px] justify-center">
             ${getDisplayUnit(c)}
          </span>
        </div>
      </td>

 <td class="p-2 min-w-[140px]">
        <select class="conditionSelect w-full border border-gray-300 rounded px-2 py-1 bg-gray-50"
          data-index="${i}"
          onchange="updateField(${i}, 'condition', this.value)">
          <option value="">-- Pilih --</option>
          ${CONDITIONS.map(opt => `
            <option value="${opt}" ${c.condition === opt ? 'selected' : ''}>${opt}</option>
          `).join('')}
        </select>
      </td>

     

      <td class="p-2 text-center">
        <button type="button" class="removeRowBtn p-2 text-red-500" data-index="${i}">
          <i class="fa-solid fa-trash"></i>
        </button>
      </td>
    </tr>
  `).join('');

  $('#chemTableBodyDesktop').html(rows);
}

/* --- Mobile --- */
function renderMobile() {
  const cards = chemicals.map((c, i) => `
    <div class="border rounded-lg p-3 bg-white space-y-2">
      <div class="flex justify-between">
        <span class="text-sm font-medium text-gray-600">#${i + 1}</span>
        <button type="button" class="removeRowBtn text-red-500" data-index="${i}">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>

      <select class="chemicalSelect w-full" data-index="${i}"></select>

     

      <div class="flex">
        <input type="number" data-field="min_package"
          class="w-full border px-2 py-1 rounded-l text-right bg-gray-100"
          value="${c.min_package}" readonly>
        <span data-field="min_package_unit"
          class="px-3 flex items-center border border-l-0 rounded-r bg-gray-100 text-sm min-w-[60px] justify-center">
          ${c.unit || '-'}
        </span>
      </div>

      <div class="flex">
        <input type="number"
          class="qtyInput w-full border px-2 py-1 text-right rounded-l"
          data-index="${i}"
          value="${c.qty}">
        <span data-field="unit"
          class="px-3 flex items-center border border-l-0 rounded-r bg-gray-100 text-sm min-w-[60px] justify-center">
          ${getDisplayUnit(c)}
        </span>
      </div>

       <select class="conditionSelect w-full border border-gray-300 rounded px-2 py-1 bg-gray-50"
        data-index="${i}"
        onchange="updateField(${i}, 'condition', this.value)">
        <option value="">-- Pilih Kondisi --</option>
        ${CONDITIONS.map(opt => `
          <option value="${opt}" ${c.condition === opt ? 'selected' : ''}>${opt}</option>
        `).join('')}
      </select>
    </div>
  `).join('');

  $('#chemTableBodyMobile').html(cards);
}

/* =========================
   SELECT2 — label persists across re-renders
========================= */
function initSelect2() {
  $('.chemicalSelect').each(function () {
    if ($(this).hasClass('select2-hidden-accessible')) return;

    const $el    = $(this);
    const index  = parseInt($el.data('index'));
    const row    = chemicals[index];

    $el.select2({
      width: '100%',
      placeholder: '-- Select Chemical --',
      allowClear: true,
    ajax: {
  url: '{{ route("ppic.tfcm1.api") }}',
  dataType: 'json',
  delay: 250,
  data: function(params) {
    return {
      search: params.term,
      page: params.page || 1    // Select2 otomatis increment ini
    };
  },
  processResults: function(data) {
    return {
      results: data.data.map(item => ({
        id: item.id,
        text: item.article_code + ' - ' + item.description,
        full: item
      })),
      pagination: {
        more: data.more        // true = Select2 load page berikutnya
      }
    };
  }
}
    });

    // Restore saved selection without triggering AJAX
    if (row.id && row.text) {
      const opt = new Option(row.text, row.id, true, true);
      $el.append(opt).trigger('change');
    }

    // Select
    $el.on('select2:select', function (e) {
      const data  = e.params.data;
      const item  = data.full;

      // Duplicate check: reject if same chemical already exists in another row
      const isDuplicate = chemicals.some((c, i) => i !== index && c.id == item.id);
      if (isDuplicate) {
        Swal.fire({
          icon: 'warning',
          title: 'Duplikat Chemical',
          text: `"${data.text}" sudah ada di baris lain.`,
          confirmButtonColor: '#3b82f6'
        });
        // Clear the selection
        $el.val(null).trigger('change');
        return;
      }

      chemicals[index].id          = item.id;
      chemicals[index].text        = data.text;
      chemicals[index].article_code  = item.article_code; // ✅ INI PENTING
      chemicals[index].min_package = item.min_package ?? '';
      chemicals[index].unit        = item.unit ?? '-';
      updateRowUI(index);
    });

    // Clear
    $el.on('select2:unselect', function () {
      chemicals[index].id          = '';
      chemicals[index].text        = '';
      chemicals[index].article_code        = '';
      chemicals[index].min_package = '';
      chemicals[index].unit        = '';
      updateRowUI(index);
    });
  });
}

/* =========================
   PARTIAL UI UPDATE (after select)
========================= */
function updateRowUI(index) {
  const row    = chemicals[index];
  const $dRow  = $('#chemTableBodyDesktop tr').eq(index);
  const $mCard = $('#chemTableBodyMobile > div').eq(index);

  $dRow.find('[data-field="min_package"]').val(row.min_package);
  $dRow.find('[data-field="min_package_unit"]').text(row.unit || '-');
  $dRow.find('[data-field="unit"]').text(row.unit || '-');

  $mCard.find('[data-field="min_package"]').val(row.min_package);
  $mCard.find('[data-field="min_package_unit"]').text(row.unit || '-');
  $mCard.find('[data-field="unit"]').text(row.unit || '-');

  // Reset override flag when chemical changes so auto-condition kicks in fresh
  chemicals[index].conditionOverridden = false;
  chemicals[index].condition = '';
  $dRow.find('select.conditionSelect').val('');
  $mCard.find('select.conditionSelect').val('');
  applyConditionUnit(index); // reset unit display

  updateSummary();
}

/* =========================
   DELEGATED: qty input
========================= */
$(document).on('input', '.qtyInput', function () {
  const index = parseInt($(this).data('index'));
  chemicals[index].qty = this.value;
  autoSetCondition(index);
  updateSummary();
});

/* =========================
   REMOVE ROW (delegated)
========================= */
$(document).on('click', '.removeRowBtn', function () {
  removeRow(parseInt($(this).data('index')));
});

/* =========================
   SUMMARY — counts rows, not qty
========================= */
function updateSummary() {
  let utuh = 0, sisa = 0, total = 0;
 
  chemicals.forEach(c => {
    if (!c.id) return; // skip empty rows
    total++;
    if (c.condition === 'Utuh')            utuh++;
    else if (c.condition === 'Tidak Utuh') sisa++;
  });
 
  $('#summaryUtuh').text(utuh);
  $('#summarySisa').text(sisa);
  $('#summaryTotal').text(total);
}
 
/* location labels in summary */
function updateSummaryLocation() {
  const from = $('#location_from option:selected').text().trim() || '—';
  const to   = $('#location_to option:selected').text().trim()   || '—';
  $('#summaryFrom').text(from === '-- Pilih Lokasi --' ? '—' : from);
  $('#summaryTo').text(to   === '-- Pilih Lokasi --' ? '—' : to);
}
 
$('#location_from, #location_to').on('change', updateSummaryLocation);
 
/* =========================
   AJAX SAVE
========================= */
$('#transferForm').on('submit', function (e) {
  e.preventDefault();
 
  // --- Validation ---
  const locationFrom = $('#location_from').val();
  const locationTo   = $('#location_to').val();
  let errors = [];
 
  if (!locationFrom) errors.push('Location From belum dipilih.');
  if (!locationTo)   errors.push('Location To belum dipilih.');
 
  const filledRows = chemicals.filter(c => c.id);
  if (filledRows.length === 0) errors.push('Minimal satu chemical harus dipilih.');
 
  chemicals.forEach((c, i) => {
    if (!c.id) return;
    if (!c.qty || parseFloat(c.qty) <= 0)
      errors.push(`Baris ${i + 1}: Qty tidak boleh kosong atau 0.`);
  });
 
  if (errors.length > 0) {
    Swal.fire({
      icon: 'error',
      title: 'Validasi Gagal',
      html: errors.map(msg => `• ${msg}`).join('<br>'),
      confirmButtonColor: '#3b82f6'
    });
    return;
  }
 
  // --- Disable button + spinner ---
  const $btn = $('#submitBtn');
  $btn.prop('disabled', true)
      .html('<svg class="animate-spin h-4 w-4 mr-1 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving...');
 
  // --- Build payload ---
  const payload = {
    _token:        $('input[name="_token"]').val(),
    transfer_date: $('#transfer_date').val(),
    location_from: locationFrom,
    location_to:   locationTo,
    note:          $('#note').val(),
    items: chemicals
      .filter(c => c.id)
      .map(c => ({
        article_code: c.article_code,
        condition:    c.condition,
        min_package:  c.min_package,
        qty:          c.qty,
        unit:         c.condition === 'Tidak Utuh' ? 'KG' : c.unit
      }))
  };
 
  // --- AJAX ---
  $.ajax({
    url:         '{{ route("ppic.tfcm1.store") }}',
    method:      'POST',
    contentType: 'application/json',
    data:        JSON.stringify(payload),
    success: function () {
      // Reset form
      chemicals = [createEmptyRow()];
      $('#transferForm')[0].reset();
      $('#location_from, #location_to').val('').trigger('change');
      render();
      updateSummaryLocation();
 
      Swal.fire({
        toast:            true,
        position:         'top-end',
        icon:             'success',
        title:            'Transfer berhasil disimpan!',
        showConfirmButton: false,
        timer:            3000,
        timerProgressBar: true
      });
    },
    error: function (xhr) {
      let msg = 'Terjadi kesalahan. Silakan coba lagi.';
      if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
      else if (xhr.responseJSON?.errors) {
        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
      }
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menyimpan',
        html: msg,
        confirmButtonColor: '#3b82f6'
      });
    },
    complete: function () {
      $btn.prop('disabled', false)
          .html('<i data-feather="save" class="h-4 w-4"></i> Save');
      feather.replace();
    }
  });
});
</script>
@endpush
@endsection