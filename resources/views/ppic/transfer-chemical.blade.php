@extends('layouts.app')

@section('title', 'Transfer Chemical')
@section('page-title', 'DASHBOARD TRANSFER CHEMICAL')
@section('breadcrumb-item', 'PPIC')
@section('breadcrumb-active', 'Transfer Chemical')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     TAB NAVIGATION
════════════════════════════════════════════════════════════ --}}
<div class="bg-white shadow rounded-xl mb-6 overflow-hidden">
  <div class="flex border-b border-gray-200">
    <button id="tab-btn-transfer"
      onclick="switchTab('transfer')"
      class="tab-btn active-tab flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
      <i data-feather="repeat" class="w-4 h-4"></i>
      Transfer Chemical
    </button>
    <button id="tab-btn-konsumsi"
      onclick="switchTab('konsumsi')"
      class="tab-btn flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
      <i data-feather="bar-chart-2" class="w-4 h-4"></i>
      Konsumsi per Booth
    </button>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     TAB: TRANSFER CHEMICAL
════════════════════════════════════════════════════════════ --}}
<div id="tab-transfer">

  {{-- Filter Card --}}
  <div class="bg-white shadow rounded-xl p-6 mb-6">
    <div class="border-b border-gray-300"><h2 class="text-sm font-semibold mb-4">Filter Transfer Chemical</h2></div>
    <form id="filter-form">
      <div class="grid grid-cols-1 md:grid-cols-3 mt-4 gap-4 mb-4">
        <div>
          <label class="block text-sm mb-1 font-medium text-gray-700">Transfer Date</label>
          <input type="text" id="filter-date"
            class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
            placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
        </div>
        <div>
          <label class="block text-sm mb-1 font-medium text-gray-700">From</label>
          <select id="filter-from" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm">
            <option value="">-- All --</option>
            @foreach(['Warehouse Chemical','Spraybooth 1A','Spraybooth 1B','Spraybooth 1C','Spraybooth 2A','Spraybooth 2B','Spraybooth 2C','Spraybooth 3A','Spraybooth 3B','Spraybooth 3C','Spraybooth 4A','Spraybooth 4B','Spraybooth 4C','Spraybooth 5A','Spraybooth 5B','Spraybooth 5C'] as $loc)
              <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1 font-medium text-gray-700">To</label>
          <select id="filter-to" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm">
            <option value="">-- All --</option>
            @foreach(['Warehouse Chemical','Spraybooth 1A','Spraybooth 1B','Spraybooth 1C','Spraybooth 2A','Spraybooth 2B','Spraybooth 2C','Spraybooth 3A','Spraybooth 3B','Spraybooth 3C','Spraybooth 4A','Spraybooth 4B','Spraybooth 4C','Spraybooth 5A','Spraybooth 5B','Spraybooth 5C'] as $loc)
              <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
          <select id="filter-status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm">
            <option value="">-- All Status --</option>
            <option value="Supply">Supply</option>
            <option value="Return">Return</option>
          </select>
        </div>
      </div>
      <div class="flex justify-start gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
        <a href="{{ route('ppic.tfcm1.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
      </div>
    </form>
  </div>

  {{-- Table Card --}}
  <div class="bg-white shadow rounded-xl p-6 mb-2">

    {{-- View Toggle --}}
    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-300">
      <h2 class="text-sm font-semibold text-gray-800">Transfer Chemical List</h2>
      <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5 gap-0.5" id="viewToggle">
        <button id="btn-summary" onclick="switchView('summary')"
          class="view-btn active-view flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
          <i data-feather="list" class="w-3.5 h-3.5"></i> Summary
        </button>
        <button id="btn-detail" onclick="switchView('detail')"
          class="view-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
          <i data-feather="layers" class="w-3.5 h-3.5"></i> Detail
        </button>
      </div>
    </div>

    {{-- Summary Table --}}
    <div id="view-summary">
      <div class="w-full overflow-x-auto">
        <table id="tfcm1-table" class="min-w-full text-sm text-left whitespace-nowrap">
          <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
            <tr>
              <th class="px-4 py-2">Action</th>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">From</th>
              <th class="px-4 py-2">To</th>
              <th class="px-4 py-2">Created By</th>
              <th class="px-4 py-2">Created At</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- Detail Table --}}
    <div id="view-detail" class="hidden">
      <div class="w-full overflow-x-auto">
        <table id="tfcm1-detail-table" class="min-w-full text-sm text-left whitespace-nowrap">
          <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
            <tr>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">From</th>
              <th class="px-4 py-2">To</th>
              <th class="px-4 py-2">Article Code</th>
              <th class="px-4 py-2">Description</th>
              <th class="px-4 py-2">Condition</th>
              <th class="px-4 py-2">Min Package</th>
              <th class="px-4 py-2">Conversion</th>
              <th class="px-4 py-2">Qty IMS</th>
              <th class="px-4 py-2">UoM IMS</th>
              <th class="px-4 py-2">Qty Conversion</th>
              <th class="px-4 py-2">UoM Conversion</th>
              <th class="px-4 py-2">Created By</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

</div>{{-- end #tab-transfer --}}

{{-- ═══════════════════════════════════════════════════════
     TAB: KONSUMSI PER BOOTH
════════════════════════════════════════════════════════════ --}}
<div id="tab-konsumsi" class="hidden">

  <div class="bg-white shadow rounded-xl p-6 mb-6">

    {{-- Header + Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-gray-200">
      <div>
        <h2 class="text-sm font-semibold text-gray-800">Konsumsi per Booth</h2>
        <p class="text-xs text-gray-400 mt-0.5">Net Supply − Return per article per spraybooth</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <input type="text" id="kb-filter-date"
          class="border border-gray-300 rounded-lg text-xs px-3 py-2 focus:outline-none focus:ring focus:border-blue-400 w-56"
          placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
        <button onclick="loadKonsumsi()"
          class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
          <i data-feather="search" class="w-3.5 h-3.5"></i> Apply
        </button>
        <button onclick="exportKonsumsiExcel()"
          class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
          <i data-feather="download" class="w-3.5 h-3.5"></i> Export Excel
        </button>
      </div>
    </div>

    {{-- Split Panel --}}
    <div class="flex gap-0 border border-gray-200 rounded-xl overflow-hidden min-h-[400px]">

      {{-- LEFT: Booth List --}}
      <div class="w-56 shrink-0 border-r border-gray-200 bg-gray-50 flex flex-col">
        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 bg-gray-100">
          Spraybooth
        </div>
        <div id="kb-booth-list" class="overflow-y-auto flex-1">
          <div class="px-4 py-8 text-center text-xs text-gray-400">
            <i data-feather="loader" class="w-5 h-5 mx-auto mb-2 text-gray-300"></i>
            Pilih tanggal lalu klik Apply
          </div>
        </div>
      </div>

      {{-- RIGHT: Detail Panel --}}
      <div class="flex-1 flex flex-col min-w-0">

        {{-- Panel Header --}}
        <div class="px-4 py-2.5 border-b border-gray-200 bg-white flex items-center justify-between">
          <span class="text-xs font-semibold text-gray-700" id="kb-panel-title">— Pilih booth —</span>
          <button id="kb-back-btn" onclick="backToDetail()"
            class="hidden text-xs text-blue-600 hover:underline flex items-center gap-1">
            <i data-feather="arrow-left" class="w-3 h-3"></i> Kembali ke detail artikel
          </button>
        </div>

        {{-- Panel Body --}}
        <div id="kb-panel-body" class="flex-1 overflow-auto p-3">
          <div class="h-full flex items-center justify-center text-xs text-gray-300" style="min-height:300px">
            <div class="text-center">
              <i data-feather="bar-chart-2" class="w-8 h-8 mx-auto mb-2"></i>
              Pilih booth di kiri untuk melihat detail
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>

</div>{{-- end #tab-konsumsi --}}

{{-- Export Modal --}}
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
  <div class="bg-white w-full max-w-xl rounded-xl shadow-xl p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-lg font-semibold text-gray-800">Export Data</h2>
      <button onclick="closeExportModal()" class="text-gray-400 hover:text-red-600">✕</button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <button onclick="exportIMS()"
        class="group border rounded-xl p-5 hover:border-blue-500 hover:shadow-md transition text-left">
        <div class="flex flex-col items-start gap-3">
          <i data-feather="database" class="w-8 h-8 text-blue-600"></i>
          <div>
            <h3 class="font-semibold text-gray-800 group-hover:text-blue-600">Export for IMS</h3>
            <p class="text-sm text-gray-500">Format untuk integrasi ke ERP / Upload ke IMS</p>
          </div>
        </div>
      </button>
      <button onclick="printData()"
        class="group border rounded-xl p-5 hover:border-green-500 hover:shadow-md transition text-left">
        <div class="flex flex-col items-start gap-3">
          <i data-feather="printer" class="w-8 h-8 text-green-600"></i>
          <div>
            <h3 class="font-semibold text-gray-800 group-hover:text-green-600">Print</h3>
            <p class="text-sm text-gray-500">Cetak langsung dalam format siap print</p>
          </div>
        </div>
      </button>
    </div>
  </div>
</div>

@push('scripts')
<style>
  /* ── Tab navigation ── */
  .tab-btn {
    color: #6b7280;
    border-bottom-color: transparent;
    border-bottom-width: 2px;
  }
  .tab-btn:hover { color: #1d4ed8; background: #f8faff; }
  .tab-btn.active-tab {
    color: #1d4ed8;
    border-bottom-color: #3b82f6;
    font-weight: 600;
  }

  /* ── View toggle (summary/detail) ── */
  .view-btn { color: #6b7280; }
  .view-btn.active-view {
    background: #fff; color: #1d4ed8;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-weight: 600;
  }

  /* ── DataTables shared ── */
  #tfcm1-table th, #tfcm1-table td,
  #tfcm1-detail-table th, #tfcm1-detail-table td {
    white-space: nowrap; border: none !important;
  }
  #tfcm1-table tbody tr:nth-child(odd),
  #tfcm1-detail-table tbody tr:nth-child(odd)  { background: #fff; }
  #tfcm1-table tbody tr:nth-child(even),
  #tfcm1-detail-table tbody tr:nth-child(even) { background: #f3f4f6; }
  #tfcm1-table tbody tr:hover,
  #tfcm1-detail-table tbody tr:hover           { background: #eff6ff; }
  #tfcm1-detail-table tbody tr.group-start td  { border-top: 1px solid gray !important; }

  /* ── Select2 ── */
  .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #d1d5db; border-radius: 0.375rem; }
  .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 12px; }
  .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }

  /* ── DataTables UI ── */
  .dataTables_filter input { border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 10px; margin-left: 10px; }
  .dt-buttons { position: relative; z-index: 1; margin-left: 10px; }
  .dt-button.buttons-collection { font-size: 0.875rem; padding: 0.4rem 1rem; }
  .dt-button-down-arrow { display: none !important; }
  div.dt-button-collection {
    position: absolute !important; top: 100% !important; left: 0 !important;
    margin-top: 0.5rem; background-color: white;
    border: 1px solid #e5e7eb; border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 10000;
  }
  div.dt-button-collection .dt-button { color: #1f2937; padding: 0.5rem 1rem; text-align: left; width: 100%; }
  div.dt-button-collection .dt-button:hover { background-color: #dfe0e0; }

  /* ── Konsumsi booth styles ── */
  .kb-booth-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 12px; font-size: 12px; cursor: pointer;
    border-bottom: 1px solid #f3f4f6; color: #374151; transition: background .12s;
  }
  .kb-booth-item:hover { background: #eff6ff; }
  .kb-booth-item.active {
    background: #fff; font-weight: 600; color: #1d4ed8;
    border-left: 3px solid #3b82f6; padding-left: 9px;
  }
  .kb-booth-item .kb-badge {
    font-size: 10px; padding: 1px 7px; border-radius: 9px;
    background: #f3f4f6; color: #6b7280; font-weight: 500;
  }
  .kb-booth-item.active .kb-badge { background: #dbeafe; color: #1d4ed8; }

  .kb-stat { flex:1; background:#f9fafb; border-radius:8px; padding:8px 10px; }
  .kb-stat .ks-label { font-size:10px; color:#9ca3af; margin-bottom:2px; }
  .kb-stat .ks-val   { font-size:16px; font-weight:600; color:#111827; }

  .kb-tbl { width:100%; border-collapse:collapse; font-size:12px; }
  .kb-tbl th {
    font-size:11px; font-weight:600; color:#6b7280; padding:6px 8px;
    border-bottom:1px solid #e5e7eb; text-align:left; white-space:nowrap; background:#f9fafb;
  }
  .kb-tbl th.r { text-align:right; }
  .kb-tbl td { padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#111827; white-space:nowrap; }
  .kb-tbl td.r { text-align:right; font-variant-numeric:tabular-nums; }
  .kb-tbl tr:hover td { background:#f0f9ff; }
  .kb-tbl tr:last-child td { border-bottom:none; }

  .chip-net-pos { font-size:11px;padding:2px 8px;border-radius:9px;background:#fef9c3;color:#854d0e;font-weight:600; }
  .chip-net-neg { font-size:11px;padding:2px 8px;border-radius:9px;background:#dcfce7;color:#15803d;font-weight:600; }

  .kb-eye-btn {
    background:none; border:none; cursor:pointer; padding:3px 5px;
    color:#9ca3af; border-radius:4px; transition:all .15s;
  }
  .kb-eye-btn:hover { color:#3b82f6; background:#eff6ff; }

  .badge-supply { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:9px;background:#dcfce7;color:#15803d;font-size:10px;font-weight:600; }
  .badge-return { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:9px;background:#ffedd5;color:#c2410c;font-size:10px;font-weight:600; }
</style>

<script>
/* ================================================================
   TAB NAVIGATION
================================================================ */
let activeTab = 'transfer';

function switchTab(tab) {
  activeTab = tab;

  // toggle panels
  document.getElementById('tab-transfer').classList.toggle('hidden', tab !== 'transfer');
  document.getElementById('tab-konsumsi').classList.toggle('hidden', tab !== 'konsumsi');

  // toggle tab button styles
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active-tab'));
  document.getElementById('tab-btn-' + tab).classList.add('active-tab');

  feather.replace();

  // Redraw DataTables saat kembali ke tab transfer (menghindari column width bug)
  if (tab === 'transfer') {
    if (typeof summaryTable !== 'undefined') summaryTable.columns.adjust();
    if (typeof detailTable  !== 'undefined') detailTable.columns.adjust();
  }
}

/* ================================================================
   FILTER HELPERS
================================================================ */
function getFilters() {
  return {
    date:   $('#filter-date').val(),
    from:   $('#filter-from').val(),
    to:     $('#filter-to').val(),
    status: $('#filter-status').val(),
  };
}

/* ================================================================
   VIEW TOGGLE (summary / detail)
================================================================ */
let currentView = 'summary';

function switchView(view) {
  currentView = view;
  $('#view-summary').toggleClass('hidden', view !== 'summary');
  $('#view-detail').toggleClass('hidden', view !== 'detail');
  $('#btn-summary, #btn-detail').removeClass('active-view');
  $(`#btn-${view}`).addClass('active-view');
  if (view === 'summary') summaryTable.draw();
  else detailTable.draw();
}

/* ================================================================
   DATATABLES INIT
================================================================ */
let summaryTable, detailTable;

$(function () {
  flatpickr('#filter-date',    { mode: 'range', dateFormat: 'Y-m-d', maxDate: 'today', allowInput: true });
  flatpickr('#kb-filter-date', { mode: 'range', dateFormat: 'Y-m-d', maxDate: 'today', allowInput: true });

  ['#filter-status', '#filter-from', '#filter-to'].forEach(sel =>
    $(sel).select2({ placeholder: '-- All --', allowClear: true, width: '100%' })
  );

  const today = new Date().toISOString().slice(0, 10);

  function makeExportButtons(filename) {
    return [{
      extend: 'collection',
      text: '<i class="fas fa-download mr-2"></i>Export',
      className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
      buttons: [
        { extend: 'copyHtml5',  text: '<i class="fas fa-copy mr-2"></i>Copy' },
        { extend: 'excelHtml5', filename: filename + '_' + today, title: null,
          text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel' },
        { extend: 'pdfHtml5',   filename: filename + '_' + today, title: null,
          orientation: 'landscape', pageSize: 'A4',
          text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
          customize: doc => { doc.styles.tableHeader.fontSize = 8; doc.defaultStyle.fontSize = 7; }
        },
        { extend: 'print', title: filename + '_' + today,
          text: '<i class="fas fa-print mr-2"></i>Print',
          customize: win => $(win.document.body).css('font-size', '10px')
        }
      ]
    }];
  }

  summaryTable = $('#tfcm1-table').DataTable({
    processing: true, serverSide: true, autoWidth: false, scrollX: true,
    drawCallback: () => feather.replace(),
    ajax: { url: '{{ route("ppic.tfcm1.data") }}', data: d => Object.assign(d, getFilters()) },
    columns: [
      { data: 'action',        orderable: false },
      { data: 'transfer_date' },
      { data: 'status',        orderable: false },
      { data: 'from',          orderable: false },
      { data: 'to',            orderable: false },
      { data: 'created_by',    orderable: false },
      { data: 'created_at' },
    ],
    order: [[6, 'desc']],
    buttons: makeExportButtons('Transfer_Chemical_Summary'),
    dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center mt-2"ip>',
  });

  detailTable = $('#tfcm1-detail-table').DataTable({
    processing: true, serverSide: true, autoWidth: false, scrollX: true,
    drawCallback: function () {
      feather.replace();
      let lastId = null;
      $('#tfcm1-detail-table tbody tr').each(function () {
        const id = $(this).data('transfer-id');
        if (id && id !== lastId) { $(this).addClass('group-start'); lastId = id; }
      });
    },
    ajax: { url: '{{ route("ppic.tfcm1.data.detail") }}', data: d => Object.assign(d, getFilters()) },
    columns: [
      { data: 'transfer_date' },
      { data: 'status',              orderable: false, className: 'px-3 py-2' },
      { data: 'from',                orderable: false },
      { data: 'to',                  orderable: false },
      { data: 'article_code',        orderable: false },
      { data: 'description',         orderable: false },
      { data: 'condition',           orderable: false },
      { data: 'min_package_display', orderable: false, className: 'text-right' },
      { data: 'conversion',          orderable: false, className: 'text-right' },
      { data: 'qty_ims',             orderable: false, className: 'text-right' },
      { data: 'uom_ims',             orderable: false, className: 'text-left' },
      { data: 'qty',                 orderable: false, className: 'text-right' },
      { data: 'uom_con',             orderable: false, className: 'text-left' },
      { data: 'created_by',          orderable: false },
    ],
    order: [[0, 'desc']],
    buttons: makeExportButtons('Transfer_Chemical_Detail'),
    dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center mt-2"ip>',
    createdRow: (row, data) => $(row).attr('data-transfer-id', data.transfer_id),
  });

  $('#filter-form').on('submit', function (e) {
    e.preventDefault();
    if (currentView === 'summary') summaryTable.draw();
    else detailTable.draw();
  });

  feather.replace();
});

/* ================================================================
   DROPDOWN
================================================================ */
let openDropdown = null;

function toggleDropdown(id, event) {
  const trigger  = event.currentTarget;
  const existing = document.getElementById('global-dropdown');
  if (existing) { existing.remove(); if (openDropdown === id) { openDropdown = null; return; } }

  const tpl = document.getElementById(id);
  if (!tpl) return;

  const dd = document.createElement('div');
  dd.id = 'global-dropdown';
  dd.className = 'absolute z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700';
  dd.innerHTML  = tpl.innerHTML;
  document.body.appendChild(dd);

  const rect   = trigger.getBoundingClientRect();
  dd.style.position = 'fixed';
  dd.style.top  = `${rect.bottom + 4}px`;
  dd.style.left = `${rect.left}px`;
  openDropdown  = id;
}

document.addEventListener('click', function (e) {
  const dd = document.getElementById('global-dropdown');
  if (dd && !dd.contains(e.target) && !e.target.closest('button[data-dropdown-id]')) {
    dd.remove(); openDropdown = null;
  }
});

/* ================================================================
   EXPORT MODAL
================================================================ */
let currentExportId = null;

function modalExport(id) {
  currentExportId = id;
  const modal = document.getElementById('exportModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  feather.replace();
}

function closeExportModal() {
  const modal = document.getElementById('exportModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

document.addEventListener('click', function (e) {
  const modal = document.getElementById('exportModal');
  if (modal && !modal.classList.contains('hidden') && e.target === modal) closeExportModal();
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeExportModal(); });

function exportIMS()  { window.location.href = `/ppic/transfer-chemical/${currentExportId}/export/ims`; }
function printData()  { window.open(`/ppic/transfer-chemical/${currentExportId}/print`, '_blank'); }

/* ================================================================
   KONSUMSI PER BOOTH
================================================================ */
let kbData          = [];
let kbActiveBooth   = null;
let kbActiveArticle = null;

function loadKonsumsi() {
  const date = $('#kb-filter-date').val();

  $('#kb-booth-list').html(`
    <div class="px-4 py-8 text-center text-xs text-gray-400">
      <i data-feather="loader" class="w-5 h-5 mx-auto mb-2 text-gray-300"></i>Memuat data...
    </div>`);
  feather.replace();

  $.get('{{ route("ppic.tfcm1.konsumsi-booth") }}', { date })
    .done(function (data) {
      kbData        = data;
      kbActiveBooth = null;
      renderBoothList();
      resetPanel();
      feather.replace();
    })
    .fail(function () {
      $('#kb-booth-list').html(`
        <div class="px-4 py-6 text-center text-xs text-red-400">Gagal memuat data. Coba lagi.</div>`);
    });
}

function renderBoothList() {
  if (!kbData.length) {
    $('#kb-booth-list').html(`
      <div class="px-4 py-8 text-center text-xs text-gray-400">Tidak ada data untuk filter ini.</div>`);
    return;
  }
  let html = '';
  kbData.forEach(function (b) {
    const active = b.booth === kbActiveBooth ? 'active' : '';
    html += `
      <div class="kb-booth-item ${active}" onclick="selectBooth('${b.booth}')">
        <span>${b.booth}</span>
        <span class="kb-badge">${b.items.length}</span>
      </div>`;
  });
  $('#kb-booth-list').html(html);
}

function selectBooth(boothName) {
  kbActiveBooth   = boothName;
  kbActiveArticle = null;
  renderBoothList();
  renderDetailPanel();
  feather.replace();
}

function renderDetailPanel() {
  const booth = kbData.find(b => b.booth === kbActiveBooth);
  if (!booth) return;

  $('#kb-panel-title').text(kbActiveBooth);
  $('#kb-back-btn').addClass('hidden');

  const statHtml = `
    <div style="display:flex;gap:8px;margin-bottom:12px;">
      <div class="kb-stat">
        <div class="ks-label">Total Supply</div>
        <div class="ks-val" style="color:#15803d">${fmt(booth.total_supply)}</div>
      </div>
      <div class="kb-stat">
        <div class="ks-label">Total Return</div>
        <div class="ks-val" style="color:#c2410c">${fmt(booth.total_return)}</div>
      </div>
      <div class="kb-stat">
        <div class="ks-label">Net Konsumsi</div>
        <div class="ks-val">${fmt(booth.total_net)}</div>
      </div>
    </div>`;

  let rows = '';
  booth.items.forEach(function (item) {
    const netCls  = item.net >= 0 ? 'chip-net-pos' : 'chip-net-neg';
    const netSign = item.net >= 0 ? '+' : '';
    rows += `
      <tr>
        <td>${item.article_code}</td>
        <td style="color:#6b7280;max-width:180px;overflow:hidden;text-overflow:ellipsis">${item.description}</td>
        <td class="r">${fmt(item.supply)}</td>
        <td class="r">${fmt(item.return)}</td>
        <td class="r"><span class="${netCls}">${netSign}${fmt(item.net)}</span></td>
        <td class="r" style="color:#6b7280;font-size:11px">${item.uom}</td>
        <td class="r">
          <button class="kb-eye-btn" title="Lihat transaksi"
            onclick="loadTransaksi('${kbActiveBooth}','${item.article_code}','${item.description}')">
            <i data-feather="eye" class="w-3.5 h-3.5"></i>
          </button>
        </td>
      </tr>`;
  });

  $('#kb-panel-body').html(statHtml + `
    <div style="overflow-x:auto;">
      <table class="kb-tbl">
        <thead>
          <tr>
            <th>Article Code</th><th>Description</th>
            <th class="r">Supply</th><th class="r">Return</th>
            <th class="r">Net</th><th class="r">UoM</th>
            <th class="r" style="width:36px"></th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>
    </div>`);
  feather.replace();
}

function loadTransaksi(booth, articleCode, description) {
  kbActiveArticle = articleCode;

  $('#kb-panel-title').text(`${booth}  ›  ${articleCode} – ${description}`);
  $('#kb-back-btn').removeClass('hidden');
  $('#kb-panel-body').html(`
    <div class="py-8 text-center text-xs text-gray-400">
      <i data-feather="loader" class="w-5 h-5 mx-auto mb-2 text-gray-300"></i>Memuat transaksi...
    </div>`);
  feather.replace();

  $.get('{{ route("ppic.tfcm1.transaksi-booth") }}', {
    booth, article_code: articleCode, date: $('#kb-filter-date').val()
  })
  .done(function (rows) { renderTransaksiPanel(rows); feather.replace(); })
  .fail(function () {
    $('#kb-panel-body').html(`<div class="py-6 text-center text-xs text-red-400">Gagal memuat transaksi.</div>`);
  });
}

function renderTransaksiPanel(rows) {
  if (!rows.length) {
    $('#kb-panel-body').html(`<div class="py-8 text-center text-xs text-gray-400">Tidak ada transaksi ditemukan.</div>`);
    return;
  }

  let html = '<div style="overflow-x:auto;"><table class="kb-tbl"><thead><tr>';
  html += '<th>Date</th><th>Status</th><th>From</th><th>To</th><th>Condition</th><th class="r">Qty</th><th>UoM</th><th>Created By</th>';
  html += '</tr></thead><tbody>';

  rows.forEach(function (r) {
    const badge = r.status === 'Supply'
      ? `<span class="badge-supply"><i data-feather="arrow-up-circle" class="w-3 h-3"></i>${r.status}</span>`
      : `<span class="badge-return"><i data-feather="rotate-ccw" class="w-3 h-3"></i>${r.status}</span>`;

    const condBadge = r.condition === 'Utuh'
      ? `<span style="font-size:10px;padding:2px 7px;border-radius:9px;background:#dcfce7;color:#15803d;">${r.condition}</span>`
      : `<span style="font-size:10px;padding:2px 7px;border-radius:9px;background:#fef3c7;color:#92400e;">${r.condition}</span>`;

    html += `<tr>
      <td>${r.transfer_date}</td><td>${badge}</td>
      <td style="color:#6b7280;font-size:11px">${r.from}</td>
      <td style="color:#6b7280;font-size:11px">${r.to}</td>
      <td>${condBadge}</td>
      <td class="r" style="font-weight:600;color:#4f46e5">${fmt(r.qty)}</td>
      <td style="color:#6b7280;font-size:11px">${r.uom}</td>
      <td style="color:#6b7280">${r.created_by}</td>
    </tr>`;
  });

  html += '</tbody></table></div>';
  $('#kb-panel-body').html(html);
  feather.replace();
}

function backToDetail() {
  kbActiveArticle = null;
  renderDetailPanel();
  feather.replace();
}

function resetPanel() {
  $('#kb-panel-title').text('— Pilih booth —');
  $('#kb-back-btn').addClass('hidden');
  $('#kb-panel-body').html(`
    <div class="h-full flex items-center justify-center text-xs text-gray-300" style="min-height:300px">
      <div class="text-center">
        <i data-feather="bar-chart-2" class="w-8 h-8 mx-auto mb-2"></i>
        Pilih booth di kiri untuk melihat detail
      </div>
    </div>`);
  feather.replace();
}

function exportKonsumsiExcel() {
  const date   = $('#kb-filter-date').val();
  const params = date ? '?date=' + encodeURIComponent(date) : '';
  window.location.href = '{{ route("ppic.tfcm1.konsumsi-booth.export") }}' + params;
}

function fmt(n) { return parseFloat(n).toFixed(2); }
</script>
@endpush
@endsection