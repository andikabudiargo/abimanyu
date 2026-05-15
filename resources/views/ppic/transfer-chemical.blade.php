@extends('layouts.app')

@section('title', 'Transfer Chemical')
@section('page-title', 'DASHBOARD TRANSFER CHEMICAL')
@section('breadcrumb-item', 'PPIC')
@section('breadcrumb-active', 'Transfer Chemical')

@section('content')

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

{{-- TABLE CARD --}}
<div class="bg-white shadow rounded-xl p-6 mb-2">

  {{-- TOGGLE BAR --}}
  <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-300">
    <h2 class="text-sm font-semibold text-gray-800">Transfer Chemical List</h2>

    <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5 gap-0.5" id="viewToggle">
      <button id="btn-summary"
        onclick="switchView('summary')"
        class="view-btn active-view flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
        <i data-feather="list" class="w-3.5 h-3.5"></i> Summary
      </button>
      <button id="btn-detail"
        onclick="switchView('detail')"
        class="view-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
        <i data-feather="layers" class="w-3.5 h-3.5"></i> Detail
      </button>
    </div>
  </div>

  {{-- ── SUMMARY TABLE ── --}}
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

  {{-- ── DETAIL TABLE ── --}}
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

{{-- EXPORT MODAL --}}
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
  <div class="bg-white w-full max-w-xl rounded-xl shadow-xl p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-lg font-semibold text-gray-800">Export Data</h2>
      <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">✕</button>
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
        class="group border rounded-xl p-5 hover:border-gray-700 hover:shadow-md transition text-left">
        <div class="flex flex-col items-start gap-3">
          <i data-feather="printer" class="w-8 h-8 text-gray-700"></i>
          <div>
            <h3 class="font-semibold text-gray-800 group-hover:text-gray-700">Print</h3>
            <p class="text-sm text-gray-500">Cetak langsung dalam format siap print</p>
          </div>
        </div>
      </button>
    </div>
  </div>
</div>

@push('scripts')
<style>
  /* toggle button states */
  .view-btn { color: #6b7280; }
  .view-btn.active-view { background: #fff; color: #1d4ed8; box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-weight: 600; }

  /* table shared */
  #tfcm1-table th, #tfcm1-table td,
  #tfcm1-detail-table th, #tfcm1-detail-table td {
    white-space: nowrap;
    border: none !important;
  }
  #tfcm1-table tbody tr:nth-child(odd),
  #tfcm1-detail-table tbody tr:nth-child(odd)  { background: #fff; }
  #tfcm1-table tbody tr:nth-child(even),
  #tfcm1-detail-table tbody tr:nth-child(even) { background: #f3f4f6; }
  #tfcm1-table tbody tr:hover,
  #tfcm1-detail-table tbody tr:hover           { background: #eff6ff; }

  /* group divider in detail view */
  #tfcm1-detail-table tbody tr.group-start td {
    border-top: 1px solid gray !important;
  }

  /* select2 */
  .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #d1d5db; border-radius: 0.375rem; }
  .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; font-size: 12px; }
  .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }

  /* dataTables search */
  .dataTables_filter input { border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 10px; margin-left: 10px; }

  /* Export/DT buttons */
  .dt-buttons { position: relative; z-index: 1; margin-left: 10px; }
  .dt-button.buttons-collection { font-size: 0.875rem; padding: 0.4rem 1rem; }
  .dt-button-down-arrow { display: none !important; }

  div.dt-button-collection {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    margin-top: 0.5rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 10000;
  }
  div.dt-button-collection .dt-button {
    color: #1f2937;
    padding: 0.5rem 1rem;
    text-align: left;
    width: 100%;
  }
  div.dt-button-collection .dt-button:hover { background-color: #dfe0e0; }

  /* scroll wrapper */
  #doc-scroll-wrapper { overflow-x: auto; padding-bottom: 8px; margin-bottom: 1rem; }
  .table-scroll-wrapper { overflow-x: auto; }
</style>

<script>
/* =========================
   SHARED FILTER HELPERS
========================= */
function getFilters() {
  return {
    date:   $('#filter-date').val(),
    from:   $('#filter-from').val(),
    to:     $('#filter-to').val(),
    status: $('#filter-status').val(),
  };
}

/* =========================
   VIEW TOGGLE
========================= */
let currentView = 'summary';

function switchView(view) {
  currentView = view;

  // toggle panels
  $('#view-summary').toggleClass('hidden', view !== 'summary');
  $('#view-detail').toggleClass('hidden', view !== 'detail');

  // toggle button styles
  $('#btn-summary, #btn-detail').removeClass('active-view');
  $(`#btn-${view}`).addClass('active-view');

  // redraw the active table (triggers AJAX)
  if (view === 'summary') {
    summaryTable.draw();
  } else {
    detailTable.draw();
  }
}

/* =========================
   SUMMARY DATATABLE
========================= */
let summaryTable;

$(function () {
  flatpickr('#filter-date', { mode: 'range', dateFormat: 'Y-m-d', maxDate: 'today', allowInput: true });
  ['#filter-status', '#filter-from', '#filter-to'].forEach(sel =>
    $(sel).select2({ placeholder: '-- All --', allowClear: true, width: '100%' })
  );

  const today = new Date().toISOString().slice(0, 10);

  // ── Shared export button config factory ───────────────────────────────────
  function makeExportButtons(filename) {
    return [
      {
        extend: 'collection',
        text: '<i class="fas fa-download mr-2"></i>Export',
        className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
        buttons: [
          {
            extend: 'copyHtml5',
            text: '<i class="fas fa-copy mr-2"></i>Copy',
          },
          {
            extend: 'excelHtml5',
            filename: filename + '_' + today,
            title: null,
            text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
          },
          {
            extend: 'pdfHtml5',
            filename: filename + '_' + today,
            title: null,
            orientation: 'landscape',
            pageSize: 'A4',
            text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
            customize: function (doc) {
              doc.styles.tableHeader.fontSize = 8;
              doc.defaultStyle.fontSize = 7;
            }
          },
          {
            extend: 'print',
            title: filename + '_' + today,
            text: '<i class="fas fa-print mr-2"></i>Print',
            customize: function (win) {
              $(win.document.body).css('font-size', '10px');
            }
          }
        ]
      }
    ];
  }

  // ── Summary Table ──────────────────────────────────────────────────────────
  summaryTable = $('#tfcm1-table').DataTable({
    processing: true,
    serverSide: true,
    autoWidth: false,
    scrollX: true,
    drawCallback: () => feather.replace(),
    ajax: {
      url: '{{ route("ppic.tfcm1.data") }}',
      data: d => Object.assign(d, getFilters()),
    },
    columns: [
      { data: 'action',        title: 'Action',     orderable: false },
      { data: 'transfer_date', title: 'Date' },
      { data: 'status',        title: 'Status',     orderable: false },
      { data: 'from',          title: 'From',       orderable: false },
      { data: 'to',            title: 'To',         orderable: false },
      { data: 'created_by',    title: 'Created By', orderable: false },
      { data: 'created_at',    title: 'Created At' },
    ],
    order: [[6, 'desc']],
    buttons: makeExportButtons('Transfer_Chemical_Summary'),
    dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center mt-2"ip>',
  });

  // ── Detail Table ───────────────────────────────────────────────────────────
  detailTable = $('#tfcm1-detail-table').DataTable({
    processing: true,
    serverSide: true,
    autoWidth: false,
    scrollX: true,
    drawCallback: function () {
      feather.replace();

      // Group divider: border-top on first row of each transfer
      let lastId = null;
      $('#tfcm1-detail-table tbody tr').each(function () {
        const id = $(this).data('transfer-id');
        if (id && id !== lastId) {
          $(this).addClass('group-start');
          lastId = id;
        }
      });
    },
    ajax: {
      url: '{{ route("ppic.tfcm1.data.detail") }}',
      data: d => Object.assign(d, getFilters()),
    },
   columns: [
      { data: 'transfer_date',       title: 'Date' },
      { data: 'status',              title: 'Status',        orderable: false, className: 'px-3 py-2' },
      { data: 'from',                title: 'From',          orderable: false },
      { data: 'to',                  title: 'To',            orderable: false },
      { data: 'article_code',        title: 'Article Code',  orderable: false },
      { data: 'description',         title: 'Description',   orderable: false },
      { data: 'condition',           title: 'Condition',     orderable: false },
      { data: 'min_package_display', title: 'Min Package',   orderable: false, className: 'text-right' },
      { data: 'conversion',          title: 'Conversion',    orderable: false, className: 'text-right' },
      { data: 'qty_ims',             title: 'Qty IMS',       orderable: false, className: 'text-right' },
      { data: 'uom_ims',             title: 'UoM IMS',       orderable: false, className: 'text-left' },
      { data: 'qty',                 title: 'Qty Coversion', orderable: false, className: 'text-right' },
      { data: 'uom_con',             title: 'UoM Coversion', orderable: false, className: 'text-left' },
      { data: 'created_by',          title: 'Created By',    orderable: false },
    ],
    order: [[0, 'desc']],
    buttons: makeExportButtons('Transfer_Chemical_Detail'),
    dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center mt-2"ip>',
    createdRow: function (row, data) {
      $(row).attr('data-transfer-id', data.transfer_id);
    },
  });

  // ── Filter submit ──────────────────────────────────────────────────────────
  $('#filter-form').on('submit', function (e) {
    e.preventDefault();
    if (currentView === 'summary') summaryTable.draw();
    else detailTable.draw();
  });

  feather.replace();
});

/* =========================
   DROPDOWN
========================= */
let openDropdown = null;

function toggleDropdown(id, event) {
  const trigger = event.currentTarget;
  const existing = document.getElementById('global-dropdown');
  if (existing) { existing.remove(); if (openDropdown === id) { openDropdown = null; return; } }

  const tpl = document.getElementById(id);
  if (!tpl) return;

  const dd = document.createElement('div');
  dd.id = 'global-dropdown';
  dd.className = 'absolute z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700';
  dd.innerHTML = tpl.innerHTML;
  document.body.appendChild(dd);

  const rect = trigger.getBoundingClientRect();
  dd.style.position = 'fixed';
  dd.style.top  = `${rect.bottom + 4}px`;
  dd.style.left = `${rect.left}px`;
  openDropdown = id;
}

document.addEventListener('click', function (e) {
  const dd = document.getElementById('global-dropdown');
  if (dd && !dd.contains(e.target) && !e.target.closest('button[data-dropdown-id]')) {
    dd.remove(); openDropdown = null;
  }
});

/* =========================
   EXPORT MODAL
========================= */
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

function exportIMS()    { window.location.href = `/ppic/transfer-chemical/${currentExportId}/export/ims`; }
function printData()    { window.open(`/ppic/transfer-chemical/${currentExportId}/print`, '_blank'); }
</script>
@endpush
@endsection