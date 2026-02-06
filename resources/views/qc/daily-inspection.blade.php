@extends('layouts.app')

@section('title', 'Daily Inspection')
@section('page-title', 'DASHBOARD DAILY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Daily Inspection')

@section('content')
<!-- Modal Top Defect -->
<div id="defectModal"
     class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center px-4">

  <div
    class="w-full max-w-2xl bg-white/90 backdrop-blur-lg rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.12)] border border-gray-200 flex flex-col max-h-[75vh]"
  >

    <!-- ================= HEADER ================= -->
    <div
      class="flex justify-between items-start p-6 border-b bg-white/80 backdrop-blur sticky top-0 z-10 rounded-t-2xl">

      <div>
        <h2 id="modal-title"
            class="text-xl font-bold text-gray-800 tracking-tight">
          Top Defect Hari Ini
        </h2>

        <p id="modal-pos"
           class="text-sm text-gray-600 mt-1"></p>
      </div>

      <button id="closeDefectModal"
              class="p-2 rounded-full hover:bg-gray-200 transition">
        ✖
      </button>
    </div>


    <!-- ================= BODY (SCROLL) ================= -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">

      <!-- Top Defect -->
      <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-2">
          Top 10 Defect
        </h3>

        <ul id="top-defect-list" class="space-y-2">
          <li class="text-sm text-gray-700">Loading...</li>
        </ul>
      </div>


      <!-- Top Parts -->
      <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-2">
          Top 10 Part dengan NG Tertinggi
        </h3>

        <ul id="top-part-list" class="space-y-2">
          <li class="text-sm text-gray-700">Loading...</li>
        </ul>
      </div>

    </div>


    <!-- ================= FOOTER ================= -->
    <div
      class="p-4 border-t bg-white/80 backdrop-blur sticky bottom-0 rounded-b-2xl">

      <button id="closeDefectModalBtn"
              class="closeDefectModal w-full py-3 rounded-xl
                     bg-gradient-to-r from-blue-600 to-blue-700
                     text-white font-medium shadow
                     hover:from-blue-700 hover:to-blue-800 transition">

        Tutup
      </button>

    </div>

  </div>
</div>



   <div class="bg-white shadow rounded-xl p-6 mb-4">
    <h2 class="text-lg font-semibold mb-4">Filter Daily Inspection</h2>

    <form id="filter-form">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Inspection Number</label>
            <input id="filter-inspection-number" type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
        </div>
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Inspection Post</label>
            <select id="filter-inspection_post" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Post --</option>
                <option value="Incoming">Incoming</option>
                <option value="Unloading">Unloading</option>
                <option value="Buffing">Buffing</option>
                <option value="Touch Up">Touch Up</option>
                <option value="Final">Final</option>
                <option value="Outgoing">Outgoing</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Inspection Date</label>
            <input id="filter-inspection_date" type="text" placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <!-- SUPPLIER / CUSTOMER COMBINED -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Supplier / Customer <span class="text-red-600">*</span>
  </label>

  <select name="supplier" id="filter-supplier" class="supplier w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Pilih Supplier / Customer --</option>

    <!-- GROUP SUPPLIER -->
    <optgroup label="SUPPLIER">
      @foreach ($suppliers as $supplier)
        <option value="{{ $supplier->code }}">
          {{ $supplier->name }}
        </option>
      @endforeach
    </optgroup>

    <!-- GROUP CUSTOMER -->
    <optgroup label="CUSTOMER">
      @foreach ($customers as $customer)
        <option value="{{ $customer->code }}">
          {{ $customer->name }}
        </option>
      @endforeach
    </optgroup>

  </select>
</div>

        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Part Name</label>
            <select id="filter-part_name" class="part-name w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Part --</option>
                @foreach ($articles as $article)
                <option value="{{ $article->description }}">{{ $article->article_code }} - {{ $article->description }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-start gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
        <a href="{{ route('qc.inspections.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
    </div>
</form>

</div>

<div class="w-full mb-6">

    <!-- QC Cards Section -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 mb-4">

       <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Incoming">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Incoming</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Incoming">
            Detail
        </button>
    </div>

         <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Unloading">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Unloading</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Unloading">
            Detail
        </button>
    </div>

        <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Buffing">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Buffing</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Buffing">
            Detail
        </button>
    </div>

        <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Touch Up">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Touch Up</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Touch Up">
            Detail
        </button>
    </div>

    
        <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Final">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Final</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Final">
            Detail
        </button>
    </div>

        <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Outgoing">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Outgoing</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Outgoing">
            Detail
        </button>
    </div>


    </div>

    <!-- Chart Section -->
    <h2 class="font-bold text-lg mb-3">Chart Pass Rate & Pass Trough/Performance</h2>
    <div class="bg-white p-4 rounded-lg shadow-md">
        <canvas id="passChart" height="100"></canvas>
    </div>

</div>


   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Daily Inspection List</h2>
    <div class="w-full overflow-x-auto" id="inspection-scroll-wrapper">
    <table id="inspection-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2 text-center">Inspection Number</th>
                    <th class="px-4 py-2 text-center">Inspection Date</th>
                     <th class="px-4 py-2 text-center">Inspection Post</th>
                    <th class="px-4 py-2">Supplier/Customer</th>
                    <th class="px-4 py-2 ">Part Name</th>
                    <th class="px-4 py-2 text-center">Total Check</th>
                    <th class="px-4 py-2 text-center">Total OK</th>
                    <th class="px-4 py-2 text-center">Total NG</th>
                    <th class="px-4 py-2 text-center">Pass Rate</th>
                    <th class="px-4 py-2 text-center">Pass Trough/Performance</th>
                    <th class="px-4 py-2 text-center">NG Rate</th>
                    <th class="px-4 py-2 ">Note</th>
                    <th class="px-4 py-2 ">Operator</th>
                    <th class="px-4 py-2 text-center">Created at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#inspection-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#inspection-table tbody tr:nth-child(odd) {
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
#inspection-table td,
#inspection-table th {
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
#inspection-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#inspection-table th, #inspection-table td {
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


</style>
<script>

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
    
 $(document).ready(function () {

    $('.part-name').select2({
        width: '100%',
        placeholder: '-- All Part --',
        allowClear: true
    });

    $('.supplier').select2({
        width: '100%',
        placeholder: '-- All Supplier --',
        allowClear: true
    });

    const table = $('#inspection-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("qc.inspections.data") }}',
            
            data: function (d) {
                d.inspection_number = $('#filter-inspection-number').val();
                d.inspection_post = $('#filter-inspection_post').val();
                d.inspection_date = $('#filter-inspection_date').val();
                d.supplier_code = $('#filter-supplier').val();
                d.part_name = $('#filter-part_name').val(); // nama artikel
            }
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
       buttons: [
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
                filename: 'QC_Inspection_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'QC_Inspection_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]// kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'QC_Inspection_ ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            }
        ]
    }
],
    columns: [
  { data: 'action', name: 'action', orderable: false, searchable: false },
  { data: 'inspection_number', name: 'inspection_number', orderable: false },
  { data: 'inspection_date', name: 'inspection_date', className: 'text-center', orderable: false },
  { data: 'inspection_post', name: 'inspection_post', className: 'text-center', orderable: false },
  { data: 'partner_name', className: 'text-left', orderable: false},
  { data: 'part_name', name: 'part_name', className: 'text-left', orderable: false },
  { data: 'total_check', name: 'total_check', className: 'text-center', orderable: false },
  { data: 'total_ok', name: 'total_ok', className: 'text-center', orderable: false },
  { data: 'total_ng', name: 'total_ng', className: 'text-center', orderable: false },
  { data: 'pass_rate', name: 'pass_rate', className: 'text-center', orderable: false },
  { data: 'pass_trough', name: 'pass_trough', className: 'text-center', orderable: false },
  { data: 'ng_rate', name: 'ng_rate', className: 'text-center', orderable: false },
  { data: 'note', name: 'note', orderable: false },
  { data: 'user_id', name: 'user_id', className: 'text-center', orderable: false },
  { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false },
]

    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

  let openDropdown = null;

function toggleDropdown(id, event) {
  const trigger = event.currentTarget;
  const existingDropdown = document.getElementById('global-dropdown');

  // Hapus dropdown lama jika ada
  if (existingDropdown) {
    existingDropdown.remove();
    if (openDropdown === id) {
      openDropdown = null;
      return;
    }
  }

  // Ambil isi dropdown dari elemen tersembunyi
  const dropdownTemplate = document.getElementById(id);
  if (!dropdownTemplate) return;

  // Buat dropdown baru
  const newDropdown = document.createElement('div');
  newDropdown.id = 'global-dropdown';
  newDropdown.className = 'absolute z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700';
  newDropdown.innerHTML = dropdownTemplate.innerHTML;
  document.body.appendChild(newDropdown);

  // Hitung posisi tombol
  const rect = trigger.getBoundingClientRect();
  newDropdown.style.position = 'fixed';
  newDropdown.style.top = `${rect.bottom + 4}px`;
  newDropdown.style.left = `${rect.left}px`;

  openDropdown = id;
}

// Tutup saat klik di luar
document.addEventListener('click', function (e) {
  const dropdown = document.getElementById('global-dropdown');
  if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button[data-dropdown-id]')) {
    dropdown.remove();
    openDropdown = null;
  }
});

 // Inisialisasi Flatpickr
flatpickr("#filter-inspection_date", {
    mode: "range",
    dateFormat: "Y-m-d"
});

// Event submit form filter
$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    $('#inspection-table').DataTable().ajax.reload();
});

function getActiveDate() {
    return $('#filter-inspection_date').val();
}

function getFilterLabel() {

    const date = $('#filter-inspection_date').val();

    if (!date) {
        return 'Hari Ini';
    }

    if (date.includes(' to ')) {
        return date.replace(' to ', ' s/d ');
    }

    return date;
}


$(document).on('click', '.btn-delete-inspection', function () {
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = $(this).data('url');
    var number = $(this).data('number');

    Swal.fire({
        title: 'Are you sure?',
        text: "Delete Inspection Number: " + number + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Inspection Number ' + number + ' has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Optional: Reload or remove row
                         $('#inspection-table').DataTable().ajax.reload(null, false);
                    });
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to delete inspection.', 'error');
                }
            });
        }
    });
});

 function loadSummary() {

    const date = getActiveDate();

    $.ajax({
        url: '/qc/inspection/summary',
        type: 'GET',
        data: {
            date: date
        },
        beforeSend: function() {
            $('.qc-card .qc-total').text('...');
            $('.qc-card .qc-ok').text('...');
            $('.qc-card .qc-ng').text('...');
        },
        success: function(res) {

            const summary = res.summary;

            Object.keys(summary).forEach(function(pos) {

                const card = $('.qc-card[data-pos="' + pos + '"]');

                card.find('.qc-total').text(summary[pos].total + ' Part');
                card.find('.qc-ok').text('OK: ' + summary[pos].ok);
                card.find('.qc-ng').text('NG: ' + summary[pos].ng);

            });

        },
        error: function() {
            alert('Gagal mengambil data summary');
        }
    });

}


    // load awal tanpa filter
    loadSummary();

    // jika ada input tanggal
   $('#filter-date').on('change', function() {
    loadSummary();
});


    $('.open-defect-modal').on('click', function () {
    const pos = $(this).data('pos');
     const date = getActiveDate(); // ambil dari filter

    $.ajax({
        url: '/qc/inspection/top-defect',
        type: 'GET',
       data: {
    pos: pos,
     inspection_date: date // kirim manual
},
        beforeSend: function() {
            $('#top-defect-list').html('<li>Loading...</li>');
            $('#top-part-list').html('<li>Loading...</li>');
        },
        success: function(res) {
           const label = getFilterLabel();

$('#modal-title').text('Top Defect ' + label);
$('#modal-pos').text('Pos: ' + pos);

// FINTECH STYLE LIST ITEM
let defectHtml = '';
let partHtml = '';

res.top_defect.forEach((d, i) => {
    defectHtml += `
        <li class="flex items-center justify-between bg-white/60 backdrop-blur-md border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-md">${i + 1}</span>
                <span class="font-medium text-gray-800">${d.defect_name} ( ${d.category})</span>
            </div>
            <span class="text-gray-700 font-semibold">${d.total_qty} Pcs</span>
        </li>
    `;
});

res.top_part.forEach((p, i) => {
    partHtml += `
        <li class="flex items-center justify-between bg-white/60 backdrop-blur-md border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-1 rounded-md">${i + 1}</span>
                <span class="font-medium text-gray-800">${p.part_name}</span>
            </div>
            <span class="text-gray-700 font-semibold">${p.total_qty} NG</span>
        </li>
    `;
});

$('#top-defect-list').html(defectHtml);
$('#top-part-list').html(partHtml);

$('#defectModal').removeClass('hidden').addClass('flex');

        }
    });
});

$('#closeDefectModal').on('click', function () {
    $('#defectModal').removeClass('flex').addClass('hidden');
});

$('#closeDefectModalBtn').on('click', function () {
    $('#defectModal').removeClass('flex').addClass('hidden');
});

 fetch('{{ route("qc.inspection.chart") }}')
    .then(res => res.json())
    .then(data => {

        const ctx = document.getElementById('passChart').getContext('2d');

        // GRADIENT STYLE ala Fintech/SaaS
        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 300);
        gradientGreen.addColorStop(0, 'rgba(0,196,140,1)');   
        gradientGreen.addColorStop(1, 'rgba(0,196,140,0.25)'); 

        const gradientYellow = ctx.createLinearGradient(0, 0, 0, 300);
        gradientYellow.addColorStop(0, 'rgba(254,207,77,1)');
        gradientYellow.addColorStop(1, 'rgba(254,207,77,0.25)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.days,
                datasets: [
                    {
                        label: 'Pass Rate',
                        data: data.pass_rate,
                        backgroundColor: gradientGreen,
                        borderRadius: 8,
                        barPercentage: 0.55,
                        categoryPercentage: 0.55
                    },
                    {
                        label: 'Pass Trough/Performance',
                        data: data.pass_trough,
                        backgroundColor: gradientYellow,
                        borderRadius: 8,
                        barPercentage: 0.55,
                        categoryPercentage: 0.55
                    }
                ]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#444',
                            font: { size: 12, weight: '600' },
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(20,20,20,0.85)',
                        padding: 12,
                        cornerRadius: 8,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}%`
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: data.month,
                            color: "#555",
                            font: { size: 14, weight: '600' }
                        },
                        ticks: {
                            color: "#777",
                            font: { size: 10 },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: val => val + "%",
                            color: "#777",
                            font: { size: 10 }
                        },
                        grid: {
                            color: "rgba(0,0,0,0.06)",
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });




  </script>

@endpush


@endsection