@extends('layouts.app')

@section('title', 'e-STO')
@section('page-title', 'DASHBOARD E-STO')
@section('breadcrumb-item', 'e-STO')
@section('breadcrumb-active', 'e-STO')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter E-STO</h2>

    <form id="filter-form">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Location</label>
            <select id="filter-location" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Location --</option>
                <option value="Raw Material">Raw Material</option>
                <option value="Finish Goods">Finish Goods</option>
                <option value="Chemical">Chemical</option>
                <option value="Consumable">Consumable</option>
                <option value="WIP Sanding">WIP Sanding</option>
      <option value="WIP Buffing">WIP Buffing</option>
      <option value="Werate">Werate</option>
      <option value="WIP Touch Up">WIP Touchup</option>
                <option value="OT">OT</option>
                 <option value="Dead Stock CM1">Dead Stock CM1</option>
            </select>
        </div>
      <div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Article Code
  </label>

  <select id="filter-article"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All Article --</option>
  </select>
</div>

       <div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Number e-STO
  </label>

  <select id="filter-sto_number"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All STO Number --</option>
  </select>
</div>

 @if(in_array(auth()->id(), [53, 2]))
<div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Periode STO
  </label>

  <select id="filter-sto-periode"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All STO Periode --</option>
    <option value="2026/04">2026 April</option>
    <option value="2026/03">2026 Maret (Closed)</option>
    <option value="2026/02">2026 Februari (Closed)</option>
    <option value="2026/01">2026 Januari (Closed)</option>
    <option value="2025/12">2025 Desember (Closed)</option>
  </select>
</div>
@endif

        </div>

    <div class="flex justify-start gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
        <a href="" onclick="event.preventDefault(); openGenerateModal()" 
   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
   Generate
</a>
</form>

</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">E-STO v2 Data</h2>
    <div class="w-full overflow-x-auto" id="sto-scroll-wrapper">
    <table id="sto-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                     <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Location</th>
                    <th class="px-4 py-2 text-left">Part Code</th>
                    <th class="px-4 py-2 text-left">Part Name</th>
                     <th class="px-4 py-2 text-center">Qty</th>
                      <th class="px-4 py-2 text-center">Qty Box</th>
                    <th class="px-4 py-2 text-center">UoM</th>
                    <th class="px-4 py-2 text-center">STO Number</th>
                    <th class="px-4 py-2 text-center">Created by</th>
                    <th class="px-4 py-2 text-center">Created at</th>
                    <th class="px-4 py-2 text-left">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
       
    </div>
</div>

{{-- MODAL GENERATE --}}
<div id="gen-overlay" class="fixed inset-0 z-50 hidden items-center justify-center" 
     style="background:rgba(0,0,0,0.45)">
  <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
    
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h3 class="text-base font-semibold text-gray-800">Generate Form STO</h3>
        <p class="text-xs text-gray-500 mt-0.5">Pilih warehouse untuk mencetak kartu stock opname</p>
      </div>
      <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- Body --}}
    <div class="px-6 py-5">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Pilih Warehouse</p>
      <div class="grid grid-cols-2 gap-3" id="wh-grid"></div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
      <button onclick="closeGenerateModal()" 
              class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        Batal
      </button>
      <button id="btn-do-print" disabled onclick="doPrint()"
        class="px-4 py-2 text-sm text-white bg-blue-700 rounded-lg hover:bg-blue-800 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
  </svg>
  Print
</button>
    </div>
  </div>
</div>

{{-- MODAL PRINT PREVIEW --}}
<div id="print-modal" class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto py-8"
     style="background:rgba(0,0,0,0.55)">
  <div class="bg-white rounded-xl shadow-2xl w-full mx-4" style="max-width:900px">
    
    {{-- Print Toolbar --}}
    <div class="flex items-center justify-between px-6 py-3 bg-slate-800 rounded-t-xl">
      <span id="print-modal-label" class="text-white text-sm font-medium">Form Stock Opname</span>
      <div class="flex items-center gap-2">
        <button onclick="doPrint()" 
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Print
        </button>
        <button onclick="closePrintModal()" 
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-slate-500 text-slate-300 hover:text-white rounded-lg transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Tutup
        </button>
      </div>
    </div>

    {{-- Preview Area --}}
    <div class="bg-slate-100 p-6 rounded-b-xl overflow-x-auto">
      <div id="print-area" class="bg-white shadow-sm mx-auto" style="width:820px; min-height:600px; padding:28px; font-family:Arial,sans-serif; font-size:11px; color:#000;">
        {{-- diisi via JS --}}
      </div>
    </div>

  </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
    /* hover background */
#sto-table tbody tr.sto-row:hover {
  background-color: #eff6ff; /* bg-blue-50 */
}

/* show tooltip on hover */
#sto-table tbody tr.sto-row:hover::after {
  opacity: 1;
}
/* Ubah warna baris even dan odd */
#sto-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#sto-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}


/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* Non-Tailwind CSS */
#sto-table td,
#sto-table th {
    white-space: nowrap;
}


/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    position: relative;
    z-index: 1;
    margin-left: 10px;
}


/* Ukuran tombol collection (export) */
.dt-button.buttons-collection {
    font-size: 0.875rem; /* text-sm */
    padding: 0.4rem 1rem;
}

.dt-button-down-arrow {
    display: none !important;
}

div.dt-button-collection {
    top: 100% !important;
    margin-top: 0.5rem !important; /* Jarak dari tombol */
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 9999 !important;
}


/* Dropdown Export agar tampil di bawah */
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

/* Item dropdown */
div.dt-button-collection .dt-button {
    color: #1f2937;
    padding: 0.5rem 1rem;
    text-align: left;
    width: 100%;
}

div.dt-button-collection .dt-button:hover {
    background-color: #dfe0e0ff;
}


/* 🧭 Spacing */
#sto-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#sto-table th, #sto-table td {
    border: none !important;
}

.select2-container {
    width: 100% !important;
}


 .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 4px 10px !important;
        border: 1px solid #d1d5db !important; /* gray-300 */
        border-radius: 0.375rem !important; /* rounded-md */
        font-size: 1rem !important; /* text-base */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px;
    }

/* MOBILE CARD VIEW: Ubah tabel jadi card saat layar kecil */
@media (max-width: 768px) {

  #sto-table thead {
    display: none;
  }

  #sto-table, 
  #sto-table tbody, 
  #sto-table tr, 
  #sto-table td {
    display: block;
    width: 100%;
  }

  #sto-table tr {
    margin-bottom: 18px;
    background: #ffffff;
    border-radius: 14px;
    padding: 14px 12px;
    box-shadow: 0 3px 14px rgba(0,0,0,0.07);
  }

  #sto-table td {
    padding: 8px 4px;
    position: relative;
    text-align: left !important;
    border: none;
  }

  #sto-table td::before {
    content: attr(data-label);
    font-size: 12px;
    font-weight: 600;
    color: #1e40af;
    display: block;
    margin-bottom: 4px;
    opacity: 0.8;
  }

  
}

</style>
<script>
  // ─────────────────────────────────────────────────────────────
// GENERATE MODAL
// ─────────────────────────────────────────────────────────────
const warehouses = [
  { id: 'rm', label: 'Raw Material',  icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>' },
  { id: 'fg', label: 'Finish Goods',  icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>' },
  { id: 'ch', label: 'Chemical',      icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>' },
  { id: 'co', label: 'Consumable',    icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
];

let selectedWH = null;

function renderWHGrid() {
  const grid = document.getElementById('wh-grid');
  grid.innerHTML = warehouses.map(w => `
    <button type="button"
            onclick="selectWH('${w.id}')"
            class="wh-card flex items-start gap-3 p-3.5 border rounded-lg text-left transition-all
                   ${selectedWH === w.id
                     ? 'border-blue-600 bg-blue-50 ring-1 ring-blue-600'
                     : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'
                   }">
      <div class="mt-0.5 flex-shrink-0 ${selectedWH === w.id ? 'text-blue-700' : 'text-gray-500'}">
        ${w.icon}
      </div>
      <div>
        <p class="text-sm font-medium ${selectedWH === w.id ? 'text-blue-800' : 'text-gray-800'}">${w.label}</p>
        <p class="text-xs mt-0.5 ${selectedWH === w.id ? 'text-blue-500' : 'text-gray-400'}">Klik untuk memilih</p>
      </div>
      ${selectedWH === w.id ? `
        <div class="ml-auto flex-shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>` : ''}
    </button>
  `).join('');
}

function selectWH(id) {
  selectedWH = id;
  renderWHGrid();
  document.getElementById('btn-do-print').disabled = false;
}

function openGenerateModal() {
  selectedWH = null;
  renderWHGrid();
  document.getElementById('btn-do-print').disabled = true;
  const el = document.getElementById('gen-overlay');
  el.classList.remove('hidden');
  el.classList.add('flex');
}

function closeGenerateModal() {
  const el = document.getElementById('gen-overlay');
  el.classList.add('hidden');
  el.classList.remove('flex');
}

document.getElementById('gen-overlay').addEventListener('click', function(e) {
  if (e.target === this) closeGenerateModal();
});

// ─────────────────────────────────────────────────────────────
// PRINT PREVIEW
// ─────────────────────────────────────────────────────────────
function openPrintPreview() {
  const wh = warehouses.find(w => w.id === selectedWH);
  if (!wh) return;

  // Ambil data dari DataTables yang sedang aktif (filter location sesuai wh)
  // Untuk sekarang pakai dummy — nanti ganti dengan data dari server
  const dummyData = getDummyData(selectedWH);

  document.getElementById('print-modal-label').textContent = `Form STO — ${wh.label}`;

  const today = new Date();
  const tanggal = today.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

  const rows = dummyData.map((r, i) => `
    <tr>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px">${i + 1}</td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px">${i + 1}</td>
      <td style="border:0.5px solid #ccc;padding:4px 5px;font-size:9.5px">${r.cust}</td>
      <td style="border:0.5px solid #ccc;padding:4px 5px;font-size:9.5px;font-family:monospace">${r.code}</td>
      <td style="border:0.5px solid #ccc;padding:4px 5px;font-size:9.5px">${r.name}</td>
      <td style="border:0.5px solid #ccc;padding:4px 5px;font-size:9px">${r.partAddr}</td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
      <td style="text-align:center;border:0.5px solid #ccc;padding:4px 5px"></td>
    </tr>
  `).join('');

  document.getElementById('print-area').innerHTML = `
    <div style="display:flex;justify-content:space-between;margin-bottom:8px;align-items:flex-start">
      <div>
        <div style="font-size:13px;font-weight:bold">FORM STOCK OPNAME 30-APRIL-2026</div>
        <div style="font-size:11px;font-weight:bold;color:#1e3a5f;margin-top:2px">${wh.label.toUpperCase()}</div>
      </div>
      <table style="font-size:10px;border-collapse:collapse">
        <tr>
          <td style="padding:1px 8px 1px 0;color:#555">Counter</td>
          <td style="padding:1px 0">: <b>Ilham / Anwar</b></td>
        </tr>
        <tr>
          <td style="padding:1px 8px 1px 0;color:#555">Verf Lapangan</td>
          <td style="padding:1px 0">: <b>Rudi</b></td>
        </tr>
        <tr>
          <td style="padding:1px 8px 1px 0;color:#555">Verf Accounting</td>
          <td style="padding:1px 0">: <b>Siska</b></td>
        </tr>
      </table>
    </div>

    <div style="border-top:2px solid #1e3a5f;margin-bottom:10px"></div>

    <table style="width:100%;border-collapse:collapse;font-size:10px">
      <thead>
        <tr style="background:#1e3a5f;color:#fff">
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:28px">NO</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:28px">NO</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:50px">CUST/<br>SUPP</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:80px">CODE</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center">NAME</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:55px">PART<br>ADDRESS</th>
          <th colspan="4" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center">PLANT-1</th>
          <th colspan="5" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center">PLANT-2</th>
          <th rowspan="2" style="border:0.5px solid #aaa;padding:5px 4px;text-align:center;width:36px">TOTAL</th>
        </tr>
        <tr style="background:#2a4d7a;color:#fff">
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">TRANSIT/<br>RECV LT</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">STORAGE<br>ERML-1</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">STORAGE<br>ERML-2</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">QUALITY<br>AREA</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">LAIN-2</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">STORAGE<br>ERML-1</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">AIR<br>BLOW</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">TROLLEY<br>WIP/<br>BOOTH LT</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">QUALITY<br>AREA</th>
          <th style="border:0.5px solid #aaa;padding:4px;text-align:center;font-size:9px">LAIN-2</th>
        </tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
      <tfoot>
        <tr style="background:#e8edf2;font-weight:bold">
          <td colspan="6" style="border:0.5px solid #999;padding:5px 6px;text-align:right;font-size:10px">TOTAL</td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
          <td style="border:0.5px solid #999;padding:5px 4px;text-align:center"></td>
        </tr>
      </tfoot>
    </table>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:24px;font-size:10px">
      <div style="border:0.5px solid #bbb;border-radius:4px;padding:10px;text-align:center">
        <div style="font-weight:bold;margin-bottom:36px">Counter</div>
        <div style="border-top:0.5px solid #bbb;padding-top:5px">Ilham / Anwar</div>
      </div>
      <div style="border:0.5px solid #bbb;border-radius:4px;padding:10px;text-align:center">
        <div style="font-weight:bold;margin-bottom:36px">Verf Lapangan</div>
        <div style="border-top:0.5px solid #bbb;padding-top:5px">Rudi</div>
      </div>
      <div style="border:0.5px solid #bbb;border-radius:4px;padding:10px;text-align:center">
        <div style="font-weight:bold;margin-bottom:36px">Verf Accounting</div>
        <div style="border-top:0.5px solid #bbb;padding-top:5px">Siska</div>
      </div>
    </div>
  `;

  closeGenerateModal();
  const pm = document.getElementById('print-modal');
  pm.classList.remove('hidden');
  pm.classList.add('flex');
}

function closePrintModal() {
  const pm = document.getElementById('print-modal');
  pm.classList.add('hidden');
  pm.classList.remove('flex');
}

function doPrint() {
  const wh = warehouses.find(w => w.id === selectedWH);
  if (!wh) return;

  // Map id ke label warehouse
  const whLabelMap = {
    rm: 'Raw Material',
    fg: 'Finish Goods',
    ch: 'Chemical',
    co: 'Consumable',
  };
  const whLabel = whLabelMap[selectedWH];

  // Tampilkan loading state di tombol
  const btn = document.getElementById('btn-do-print');
  btn.disabled = true;
  btn.innerHTML = `
    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
    </svg>
    Memuat...
  `;

  fetch(`/facility/sto/reference-print?warehouse=${encodeURIComponent(whLabel)}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
    credentials: 'same-origin',
  })
  .then(res => {
    if (!res.ok) throw new Error('Server error: ' + res.status);
    return res.json();
  })
  .then(masters => {
    closeGenerateModal();
    buildAndPrint(masters, whLabel);
  })
  .catch(err => {
    console.error(err);
    showToast('error', 'Gagal mengambil data referensi STO.');
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
      </svg>
      Print
    `;
  });
}

function buildAndPrint(masters, whLabel) {
  if (!masters || masters.length === 0) {
    showToast('warning', 'Tidak ada data referensi STO untuk warehouse ini.');
    return;
  }

  let globalIndex = 1;

  const tableHeader = `
    <thead>
      <tr style="background:#1e3a5f;color:#fff">
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:28px" rowspan="2">NO</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:50px" rowspan="2">CUST/<br>SUPP</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:80px" rowspan="2">CODE</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center" rowspan="2">NAME</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center" rowspan="2">UOM</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:55px" rowspan="2">PART<br>ADDRESS</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center" colspan="3">QTY</th>
      </tr>
      <tr style="background:#1e3a5f;color:#fff">
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:42px">UTUH</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:42px">TIDAK<br>UTUH</th>
        <th style="border:1.5px solid #555;padding:5px 4px;text-align:center;width:42px">TOTAL</th>
      </tr>
    </thead>
  `;

  const tableFoot = `
    <tfoot>
      <tr style="background:#e8edf2;font-weight:bold">
        <td colspan="6" style="border:1.5px solid #555;padding:5px 6px;text-align:right;font-size:10px">TOTAL</td>
        <td style="border:1.5px solid #555;padding:5px 4px"></td>
        <td style="border:1.5px solid #555;padding:5px 4px"></td>
        <td style="border:1.5px solid #555;padding:5px 4px"></td>
      </tr>
    </tfoot>
  `;

  const signatureRow = (counter, verfLapangan, verfAccounting) => `
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;font-size:9px">
      <div style="border:0.5px solid #999;padding:6px 8px;text-align:center">
        <div style="font-weight:bold;margin-bottom:24px">Counter</div>
        <div style="border-top:0.5px solid #999;padding-top:4px">${counter ?? '-'}</div>
      </div>
      <div style="border:0.5px solid #999;padding:6px 8px;text-align:center">
        <div style="font-weight:bold;margin-bottom:24px">Verf Lapangan</div>
        <div style="border-top:0.5px solid #999;padding-top:4px">${verfLapangan ?? '-'}</div>
      </div>
      <div style="border:0.5px solid #999;padding:6px 8px;text-align:center">
        <div style="font-weight:bold;margin-bottom:24px">Verf Accounting</div>
        <div style="border-top:0.5px solid #999;padding-top:4px">${verfAccounting ?? '-'}</div>
      </div>
    </div>
  `;

  const sheets = masters.map((master) => {

    const rows = master.items.map(item => {
      const name = item.article ? item.article.description : '-';
      return `
        <tr>
          <td style="text-align:center;border:1.5px solid #555;padding:4px 5px">${globalIndex++}</td>
          <td style="border:1.5px solid #555;padding:4px 5px;font-size:9.5px">${item.third_party ?? '-'}</td>
          <td style="border:1.5px solid #555;padding:4px 5px;font-size:9.5px;font-family:monospace">${item.article_code ?? '-'}</td>
          <td style="border:1.5px solid #555;padding:4px 5px;font-size:9.5px">${name}</td>
          <td style="border:1.5px solid #555;padding:4px 5px;font-size:9.5px">${item.uom ?? '-'}</td>
          <td style="border:1.5px solid #555;padding:4px 5px;font-size:9px">${item.part_address ?? ''}</td>
          <td style="text-align:center;border:1.5px solid #555;padding:4px 5px"></td>
          <td style="text-align:center;border:1.5px solid #555;padding:4px 5px"></td>
          <td style="text-align:center;border:1.5px solid #555;padding:4px 5px"></td>
        </tr>
      `;
    }).join('');

    return `
      <div style="margin-bottom:0">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
          <div style="font-size:10px;color:#555">
            STO Number : <b style="color:#1e3a5f">${master.sto_number}</b>
          </div>
          <div style="font-size:10px;color:#555">
            Page : <b>${master.page}</b>
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
          <div>
            <div style="font-size:13px;font-weight:bold">FORM STOCK OPNAME 30-APRIL-2026</div>
            <div style="font-size:11px;font-weight:bold;color:#1e3a5f;margin-top:2px">${whLabel.toUpperCase()}</div>
          </div>
          <table style="font-size:10px;border-collapse:collapse">
            <tr><td style="padding:1px 8px 1px 0;color:#555">Counter</td><td>: <b>${master.counter ?? '-'}</b></td></tr>
            <tr><td style="padding:1px 8px 1px 0;color:#555">Verf Lapangan</td><td>: <b>${master.verifikator_lapangan ?? '-'}</b></td></tr>
            <tr><td style="padding:1px 8px 1px 0;color:#555">Verf Accounting</td><td>: <b>${master.verifikator_accounting ?? '-'}</b></td></tr>
          </table>
        </div>
        <div style="border-top:2px solid #1e3a5f;margin-bottom:10px"></div>
       <table style="width:100%;border-collapse:collapse;font-size:10px">
          ${tableHeader}
          <tbody>${rows}</tbody>
          ${tableFoot}
        </table>
        ${signatureRow(master.counter, master.verifikator_lapangan, master.verifikator_accounting)}
      </div>
      <div style="page-break-after:always"></div>
    `;
  }).join('');

  // Halaman kosong terakhir
  const lastMaster = masters[masters.length - 1];
  const lastPage = lastMaster.page ?? masters.length;

  const emptyRows = Array.from({ length: 30 }, () => `
    <tr>
      <td style="text-align:center;border:1.5px solid #555;padding:4px 5px">${globalIndex++}</td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
      <td style="border:1.5px solid #555;padding:4px 5px"></td>
    </tr>
  `).join('');

  const extraSheet = `
    <div style="margin-bottom:0">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <div style="font-size:10px;color:#555">&nbsp;</div>
        <div style="font-size:10px;color:#555">
          Page : <b>${lastPage + 1}</b>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
        <div>
          <div style="font-size:13px;font-weight:bold">FORM STOCK OPNAME 30-APRIL-2026</div>
          <div style="font-size:11px;font-weight:bold;color:#1e3a5f;margin-top:2px">${whLabel.toUpperCase()}</div>
        </div>
        <table style="font-size:10px;border-collapse:collapse">
          <tr><td style="padding:1px 8px 1px 0;color:#555">Counter</td><td>: <b>${lastMaster.counter ?? '-'}</b></td></tr>
          <tr><td style="padding:1px 8px 1px 0;color:#555">Verf Lapangan</td><td>: <b>${lastMaster.verifikator_lapangan ?? '-'}</b></td></tr>
          <tr><td style="padding:1px 8px 1px 0;color:#555">Verf Accounting</td><td>: <b>${lastMaster.verifikator_accounting ?? '-'}</b></td></tr>
        </table>
      </div>
      <div style="border-top:2px solid #1e3a5f;margin-bottom:10px"></div>
      <table style="width:100%;border-collapse:collapse;font-size:10px">
          ${tableHeader}
          <tbody>${emptyRows}</tbody>
          ${tableFoot}
        </table>
        ${signatureRow(lastMaster.counter, lastMaster.verifikator_lapangan, lastMaster.verifikator_accounting)}
      </div>
  `;

  const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Form STO — ${whLabel}</title>
      <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #000; padding: 20px; }
        @page { size: A3; margin: 15mm; }
        @media print { body { padding: 0; } }
      </style>
    </head>
    <body onload="window.print(); window.close();">
      ${sheets}
      ${extraSheet}
    </body>
    </html>
  `;

  const w = window.open('', '_blank');
  w.document.write(html);
  w.document.close();
}
// ─────────────────────────────────────────────────────────────
// DUMMY DATA — ganti dengan fetch ke server nanti
// ─────────────────────────────────────────────────────────────
function getDummyData(whId) {
  const all = [
    { loc:'rm', cust:'API', code:'RMAPI0010', name:'MOLDING GTR WIN UPR RH 62791-0K03000_SB', partAddr:'A1-01' },
    { loc:'rm', cust:'API', code:'RMAPI0011', name:'MOLDING GTR WIN UPR LH 62791-0K03000_SB', partAddr:'A1-02' },
    { loc:'rm', cust:'API', code:'RMAPI0025', name:'CVR OUTER MIRROR LH 37945-B2520-A0WHITE_SB', partAddr:'A2-01' },
    { loc:'fg', cust:'ADW', code:'FGADW0003', name:'BEZEL FR DOOR INJ. RH (PAINTING)', partAddr:'B1-01' },
    { loc:'fg', cust:'API', code:'FGAPI0001', name:'CVR OUTER MIRROR LH 37945-B2510-C0BLACK_SB', partAddr:'B1-02' },
    { loc:'fg', cust:'API', code:'FGAPI0002', name:'CVR OUTER MIRROR RH 37945-B2510-C0BLACK_SB', partAddr:'B1-03' },
    { loc:'fg', cust:'API', code:'FGAPI0019', name:'COVER OUTER MIRROR LH 37945-B2520-B1S2S_SB', partAddr:'B2-01' },
    { loc:'ch', cust:'API', code:'CHAPI0005', name:'COVER LAMP 45 GREEN_SB', partAddr:'C1-01' },
    { loc:'ch', cust:'API', code:'CHAPI0006', name:'COVER LAMP 45 BLUE_SB', partAddr:'C1-02' },
    { loc:'co', cust:'API', code:'COAPI0020', name:'COVER LAMP 45 RED_SB', partAddr:'D1-01' },
    { loc:'co', cust:'API', code:'COAPI0021', name:'SIDE SKIRT LH, SK5 (MAGNETIC SILVER METALLIC)', partAddr:'D1-02' },
  ];
  return all.filter(r => r.loc === whId);
}

$('#sto-table').on('draw.dt', function () {
    $('#sto-table tbody tr').each(function () {
        $(this).find('td').each(function (index) {
            const headers = [
                "Action",
                "Location",
                "Part Code",
                "Part Name",
                "Qty",
                "UoM",
                "Qty Box",
                "STO Number",
                "Created By",
                "Created At",
                "Note"
            ];
            $(this).attr('data-label', headers[index]);
        });
    });
});

 function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type, // success, error, info, warning
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}
    let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"

$(function () {

  const table = $('#sto-table').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,

    ajax: {
      url: '/facility/sto/data',
      type: 'GET',
      data: function (d) {
        d.location   = $('#filter-location').val();
        d.article    = $('#filter-article').val();
        d.sto_number = $('#filter-sto_number').val();
        d.sto_month = $('#filter-sto-periode').val();
      }
    },

    columns: [
    { data: 'action', orderable: false, searchable: false },
      { data: 'location', className: 'text-center' },
      { data: 'article_code' },
      { data: 'part_name' },
      { data: 'qty', className: 'text-center' },
       { data: 'min_package', className: 'text-center' },
      { data: 'unit', className: 'text-center' },
      { data: 'sto_number', className: 'text-center' },
      { data: 'created_by', className: 'text-center' },
      { 
        data: 'created_at',
        className: 'text-center',
    
      },
      { data: 'note' },
    ],

    order: [[7, 'desc']],

    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, 'All']
    ],

    dom: `
      <"flex flex-wrap items-center justify-between mb-4"
        <"flex items-center gap-3"l>
        <"flex items-center gap-1 ml-auto"f B>
      >
      rt
      <"flex items-center justify-between mt-4"ip>
    `,

    buttons: [
      {
        extend: 'collection',
        text: '<i class="fa fa-download mr-1"></i> Export',
        className: 'px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm',
        autoClose: true,
 
        buttons: [
 
          // ======================
          // SUMMARY (EXPORT DATATABLE)
          // ======================
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-table mr-2"></i> Summary',
            exportOptions: {
              columns: ':not(:first-child)'
            }
          },
 
          // ======================
          // REPORT BY BOM (LARAVEL EXPORT)
          // ======================
          {
            text: '<i class="fa fa-file-text-o mr-2"></i> Report',
            action: function (e, dt, node, config) {
              triggerExport('/facility/sto/report');
            }
          },
 
          // ======================
          // REVIEW BY BOM (LARAVEL EXPORT) — ikut filter periode
          // ======================
          {
            text: '<i class="fa fa-square-poll-vertical mr-2"></i> Review',
            action: function (e, dt, node, config) {
 
              // Ambil nilai filter periode dari dropdown
              const periode = $('#filter-sto-periode').val(); // e.g. "2026/02" atau ""
 
              // Bangun URL dengan query param jika ada
              let url = '/facility/sto/review';
              if (periode) {
                url += '?periode=' + encodeURIComponent(periode);
              }
 
              triggerExport(url);
            }
          }
 
        ]
      }
    ]
  });
 
  // ─────────────────────────────────────────────────────────────
  // Helper: tampilkan toast + trigger download via iframe
  // ─────────────────────────────────────────────────────────────
  function triggerExport(url) {
 
    const toastId = 'toast-export-' + Date.now();
 
    // Inject CSS animasi (sekali saja)
    if (!document.getElementById('toast-style')) {
      $('head').append(`
        <style id="toast-style">
          @keyframes slideInToast {
            from { opacity: 0; transform: translateY(-16px); }
            to   { opacity: 1; transform: translateY(0); }
          }
          @keyframes slideOutToast {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-16px); }
          }
          @keyframes spinToast {
            to { transform: rotate(360deg); }
          }
        </style>
      `);
    }
 
    // ── Toast "memproses" ────────────────────────────────────────
    const $toast = $(`
      <div id="${toastId}" style="
        position: fixed; top: 24px; right: 24px; z-index: 9999;
        background: #1f2937; color: #fff;
        padding: 14px 20px; border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        display: flex; align-items: center; gap: 12px;
        font-size: 14px; min-width: 280px;
        animation: slideInToast 0.3s ease;
      ">
        <span style="
          width: 18px; height: 18px;
          border: 3px solid #f97316; border-top-color: transparent;
          border-radius: 50%; display: inline-block;
          animation: spinToast 0.8s linear infinite; flex-shrink: 0;
        "></span>
        <div>
          <div style="font-weight: 600; color: #f97316;">Sedang Memproses...</div>
          <div style="font-size: 12px; color: #9ca3af; margin-top: 2px;">
            File Excel sedang disiapkan, harap tunggu.
          </div>
        </div>
      </div>
    `);
    $('body').append($toast);
 
    // ── Fetch → blob → auto-download ────────────────────────────
    fetch(url, {
      method: 'GET',
      headers: {
        // Kirim CSRF token jika diperlukan Laravel
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') ?? '',
      },
      credentials: 'same-origin',
    })
    .then(function (response) {
      if (!response.ok) {
        throw new Error('Server error: ' + response.status);
      }
 
      // Ambil nama file dari header Content-Disposition jika ada
      const disposition = response.headers.get('Content-Disposition') ?? '';
      const match       = disposition.match(/filename[^;=\n]*=["']?([^"';\n]+)["']?/i);
      const filename    = match ? match[1].trim() : 'export.xlsx';
 
      return response.blob().then(function (blob) {
        return { blob, filename };
      });
    })
    .then(function ({ blob, filename }) {
      // Buat object URL sementara lalu klik otomatis
      const objectUrl = URL.createObjectURL(blob);
      const a         = document.createElement('a');
      a.href          = objectUrl;
      a.download      = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
 
      // Bebaskan memory setelah 60 detik
      setTimeout(() => URL.revokeObjectURL(objectUrl), 60_000);
 
      // ── Toast "File Siap" ──────────────────────────────────────
      $toast.html(`
        <span style="
          width: 18px; height: 18px; background: #16a34a;
          border-radius: 50%; display: inline-flex;
          align-items: center; justify-content: center;
          flex-shrink: 0; font-size: 13px; font-weight: bold;
        ">✓</span>
        <div>
          <div style="font-weight: 600; color: #16a34a;">File Siap!</div>
          <div style="font-size: 12px; color: #9ca3af; margin-top: 2px;">
            Download dimulai secara otomatis.
          </div>
        </div>
      `);
 
      // Hapus toast setelah 3 detik
      setTimeout(function () {
        $toast.css('animation', 'slideOutToast 0.3s ease forwards');
        setTimeout(function () { $toast.remove(); }, 300);
      }, 3000);
    })
    .catch(function (err) {
      console.error('Export error:', err);
 
      // ── Toast "Gagal" ──────────────────────────────────────────
      $toast.html(`
        <span style="
          width: 18px; height: 18px; background: #dc2626;
          border-radius: 50%; display: inline-flex;
          align-items: center; justify-content: center;
          flex-shrink: 0; font-size: 13px; font-weight: bold;
        ">✕</span>
        <div>
          <div style="font-weight: 600; color: #dc2626;">Gagal!</div>
          <div style="font-size: 12px; color: #9ca3af; margin-top: 2px;">
            Terjadi kesalahan saat mengunduh file.
          </div>
        </div>
      `);
 
      setTimeout(function () {
        $toast.css('animation', 'slideOutToast 0.3s ease forwards');
        setTimeout(function () { $toast.remove(); }, 300);
      }, 4000);
    });
  }

 
  // ─────────────────────────────────────────────────────────────
  // Feather icons refresh setiap draw
  // ─────────────────────────────────────────────────────────────
  table.on('draw', function () {
    feather.replace();
  });
 
  // ─────────────────────────────────────────────────────────────
  // Submit filter
  // ─────────────────────────────────────────────────────────────
  $('#filter-form').on('submit', function (e) {
    e.preventDefault();
    table.ajax.reload();
  });
 
});

$(document).ready(function () {

  $('#filter-sto_number').select2({
    placeholder: '-- All STO Number --',
    allowClear: true,
    width: '100%',
    ajax: {
      url: '/facility/sto/select',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data.results
        };
      }
    }
  });

});

$(document).ready(function () {

  $('#filter-article').select2({
    placeholder: '-- All Article --',
    allowClear: true,
    width: '100%',
    ajax: {
      url: '/facility/article/select',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data.results
        };
      }
    }
  });

});

let openDropdown = null;

function toggleDropdown(id) {
  const dropdown = document.getElementById(id);

  // Tutup dropdown lain
  if (openDropdown && openDropdown !== dropdown) {
    openDropdown.classList.add('hidden');
  }

  dropdown.classList.toggle('hidden');
  openDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function (e) {
  if (openDropdown && !openDropdown.contains(e.target)) {
    const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
    if (!isTrigger) {
      openDropdown.classList.add('hidden');
      openDropdown = null;
    }
  }
  });

function deleteSTO(id) {
    Swal.fire({
        title: 'Yakin ingin hapus?',
        text: "STO Number ini akan terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/facility/sto/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire(
                        'Terhapus!',
                        response.message,
                        'success'
                    );
                    $('#sto-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus.',
                        'error'
                    );
                }
            });
        }
    });
}

  </script>

@endpush


@endsection