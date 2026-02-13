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

  <!-- Info Keterangan -->

    <!-- ================= BODY (SCROLL) ================= -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
<div
  class="p-3 bg-yellow-50 border border-yellow-500 rounded-lg text-sm text-yellow-800 leading-relaxed">

  <span class="font-medium">
    Berikut ranking 10 defect tertinggi dan persentasenya
    sesuai rentang waktu yang dipilih.
  </span>

</div>
    <!-- Top Defect -->
<div>

  <!-- Judul + Summary (Sejajar) -->
  <div class="flex items-center justify-between mb-2">

    <h3 class="text-sm font-semibold text-gray-700">
      Top 10 Defect
    </h3>

    <!-- Keterangan summary -->
    <p id="defect-summary"
       class="text-xs text-gray-500 italic text-right">
       Loading summary...
    </p>

  </div>

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

        Close
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
    Supplier / Customer
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
            <label class="block text-sm mb-1 font-medium text-gray-700">Category</label>
            <select id="filter-jenis_part" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Part --</option>
                <option value="Buffing">Buffing</option>
                <option value="Non-Buffing">Non-Buffing</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Spray Booth</label>
            <select id="filter-spraybooth" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Booth --</option>
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
            <label class="block text-sm mb-1 font-medium text-gray-700">Part Name</label>
            <select id="filter-part_name" class="part-name w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Part --</option>
                @foreach ($articles as $article)
                <option value="{{ $article->article_code }}">{{ $article->article_code }} - {{ $article->description }}</option>
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

<!-- ================= TAB NAVIGATION ================= 


</div>-->



<div class="w-full mb-6">

    <!-- QC Cards Section -->
    <div class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">

       <div class="qc-card group p-4 rounded-2xl bg-white/70 backdrop-blur-md border border-gray-200 shadow-[0_4px_14px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_rgba(0,0,0,0.12)] transition-all cursor-pointer" data-pos="Incoming">
        <div class="flex justify-between items-center">
            <h3 class="text-xs font-semibold text-gray-500">Incoming</h3>
            <span class="text-[10px] px-2 py-1 bg-green-100 text-green-700 rounded-full">Live</span>
        </div>
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
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
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
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
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
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
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
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
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
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
        <div class="mt-2 text-lg font-bold text-gray-800 qc-total">0</div>
        <div class="flex justify-between mt-1 text-sm">
            <span class="text-green-600 qc-ok">OK: 0</span>
            <span class="text-red-600 qc-ng">NG: 0</span>
        </div>

        <button class="open-defect-modal mt-3 w-full py-2 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-sm group-hover:from-blue-600 group-hover:to-blue-700" data-pos="Outgoing">
            Detail
        </button>
    </div>


    </div>



    <!-- ================= PASS RATE CHART ================= -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
       <div class="flex justify-between items-center mb-4">
 <div class="bg-gray-100 rounded-lg p-1 inline-flex text-xs font-medium">

    <button data-type="performance"
        class="chart-tab active px-4 py-1.5 rounded-md transition">
        Performance
    </button>

    <button data-type="pareto"
        class="chart-tab px-4 py-1.5 rounded-md transition">
        Pareto Defect
    </button>

</div>


    <div class="flex items-center gap-3">

        <!-- Segmented Toggle -->
      

        <!-- Month Year Filter -->
        <div class="flex gap-2">
            <select id="filter-month"
                class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
            </select>

            <select id="filter-year"
                class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
        </div>

    </div>



        </div>
       <div id="filter-wrapper" class="mb-3 text-xs text-gray-600 hidden">
    <span class="mr-2 font-medium text-gray-500">Active Filter :</span>
    <div id="active-filters" class="flex flex-wrap gap-2 mt-1"></div>
</div>

        <canvas id="passChart" height="130"></canvas>
    </div>

    <!-- ================= PERFORMANCE CHART ================= 
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">
                Performa Painting (Stacked by Spraybooth)
            </h3>
            <span class="text-xs text-gray-400">
                Spraybooth 1–5 (A,B,C)
            </span>
        </div>
        <canvas id="performanceChart" height="130"></canvas>
    </div>-->


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
                    <th class="px-4 py-2">Booth</th>
                    <th class="px-4 py-2">Supplier/Customer</th>
                    <th class="px-4 py-2 ">Part Name</th>
                    <th class="px-4 py-2 text-center">Total Check</th>
                    <th class="px-4 py-2 text-center">Total OK</th>
                    <th class="px-4 py-2 text-center">Total NG</th>
                    <th class="px-4 py-2 text-center">Total NC/OK Repair</th>
                    <th class="px-4 py-2 text-center">Pass Rate</th>
                    <th class="px-4 py-2 text-center">Pass Trough/Performance</th>
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

    .chart-tab {
    color: #6b7280;
}

.chart-tab.active {
    background: white;
    color: #111827;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
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
      $('#filter-inspection_post').select2({
        width: '100%',
        placeholder: '-- All Post --',
        allowClear: true
    });
      $('#filter-jenis_part').select2({
        width: '100%',
        placeholder: '-- All Part --',
        allowClear: true
    });
    $('#filter-spraybooth').select2({
        width: '100%',
        placeholder: '-- All Booth --',
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
                d.spraybooth = $('#filter-spraybooth').val();
                d.category = $('#filter-category').val();
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
  { data: 'spraybooth', name: 'spraybooth', className: 'text-left', orderable: false},
  { data: 'partner_name', className: 'text-left', orderable: false},
  { data: 'part_name', name: 'part_name', className: 'text-left', orderable: false },
  { data: 'total_check', name: 'total_check', className: 'text-center', orderable: false },
  { data: 'total_ok', name: 'total_ok', className: 'text-center', orderable: false },
  { data: 'total_ng', name: 'total_ng', className: 'text-center', orderable: false },
  { data: 'total_ok_repair', name: 'total_ok_repair', className: 'text-center', orderable: false },
  { data: 'pass_rate', name: 'pass_rate', className: 'text-center', orderable: false },
  { data: 'pass_trough', name: 'pass_trough', className: 'text-center', orderable: false },
  { data: 'user_id', name: 'user_id', className: 'text-center', orderable: false },
  { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false },
]

    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
            loadSummary();
             updateActiveFilters();
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
            inspection_date: date,
            supplier: $('#filter-supplier').val(),
            spraybooth: $('#filter-spraybooth').val(),
            part_name: $('#filter-part_name').val()
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

                card.find('.qc-total').text(summary[pos].total + ' PCS');
                card.find('.qc-ok').text('OK: ' + summary[pos].ok);
                card.find('.qc-ng').text('NG: ' + summary[pos].ng);

            });

        },
        error: function() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Gagal mengambil data summary',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

    });

}


    // load awal tanpa filter
    loadSummary();


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

$('#defect-summary').html(
  `Total <b>${res.summary.total_defect}</b> Defect dari 
   <b>${res.summary.total_part_type}</b> Jenis Part`
);
// FINTECH STYLE LIST ITEM
let defectHtml = '';
let partHtml = '';

res.top_defect.forEach((d, i) => {
    defectHtml += `
        <li class="flex items-center bg-white/60 backdrop-blur-md border border-gray-200 rounded-lg px-3 py-2 shadow-sm">

            <!-- KIRI: Rank + Nama -->
            <div class="flex items-center gap-2 flex-1">

                <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-md">
                    ${i + 1}
                </span>

                <span class="font-medium text-gray-800">
                    ${d.defect_name} (${d.category})
                </span>

            </div>

            <!-- TENGAH: Persentase -->
            <div class="w-20 text-center text-sm font-semibold text-indigo-600">
                ${d.percentage}%
            </div>

            <!-- KANAN: Qty -->
            <div class="w-20 text-right text-gray-700 font-semibold">
                ${d.total_qty} Pcs
            </div>

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
            <span class="text-gray-700 font-semibold">${p.total_qty} Defect</span>
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

Chart.register(ChartDataLabels);
let passChartInstance = null;

function renderPerformanceChart() {

    fetch('{{ route("qc.inspection.chart") }}')
    .then(res => res.json())
    .then(data => {

        const ctx = document.getElementById('passChart').getContext('2d');

        if (passChartInstance) {
            passChartInstance.destroy();
        }

        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 300);
        gradientGreen.addColorStop(0, 'rgba(0,196,140,0.25)');
        gradientGreen.addColorStop(1, 'rgba(0,196,140,0)');

        const gradientYellow = ctx.createLinearGradient(0, 0, 0, 300);
        gradientYellow.addColorStop(0, 'rgba(254,207,77,0.25)');
        gradientYellow.addColorStop(1, 'rgba(254,207,77,0)');

        passChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.days,
                datasets: [

                    // ================= PASS RATE =================
                    {
                        label: 'Pass Rate',
                        data: data.pass_rate,
                        borderColor: '#00c48c',
                        backgroundColor: gradientGreen,
                        tension: 0.35,
                        borderWidth: 3,
                        spanGaps: false,
                        pointStyle: ctx => ctx.raw === null ? 'cross' : 'circle',
                        pointRadius: ctx => ctx.raw === null ? 6 : 0,
                        pointBorderWidth: ctx => ctx.raw === null ? 2 : 0,
                        pointBackgroundColor: ctx => ctx.raw === null ? '#999' : '#00c48c',
                        pointBorderColor: ctx => ctx.raw === null ? '#999' : '#00c48c',
                        borderCapStyle: 'butt',
                        borderJoinStyle: 'miter',
                        fill: true
                    },

                    // ================= PASS THROUGH =================
                    {
                        label: 'Pass Through',
                        data: data.pass_trough,
                        borderColor: '#fecf4d',
                        backgroundColor: gradientYellow,
                        tension: 0.35,
                        borderWidth: 3,
                        spanGaps: false,
                        pointStyle: ctx => ctx.raw === null ? 'cross' : 'circle',
                        pointRadius: ctx => ctx.raw === null ? 6 : 0,
                        pointBorderWidth: ctx => ctx.raw === null ? 2 : 0,
                        pointBackgroundColor: ctx => ctx.raw === null ? '#999' : '#fecf4d',
                        pointBorderColor: ctx => ctx.raw === null ? '#999' : '#fecf4d',
                        borderCapStyle: 'butt',
                        borderJoinStyle: 'miter',
                        fill: true
                    },

                    // ================= TARGET PR =================
                    {
                        label: 'Target PR',
                        data: data.days.map(() => 94),
                        borderColor: '#00c48c',
                        borderDash: [4,4],
                        borderWidth: 2,
                        tension: 0,
                        pointRadius: ctx =>
                            ctx.dataIndex === ctx.dataset.data.length - 1 ? 5 : 0,
                        pointBackgroundColor: '#00c48c',
                        borderCapStyle: 'butt',
                        borderJoinStyle: 'miter',
                        fill: false,
                        datalabels: {
                             display: (ctx) => ctx.dataIndex === ctx.dataset.data.length - 1,
                             align: 'left',
                             anchor: 'end',
                             offset: 8,
                             clamp: true,
                             clip: false,
                             color: '#00c48c',
                             font: { size: 11, weight: '500' },
                             formatter: () => 'Target Pass Rate 94%' 
                            }
                        },

                    // ================= TARGET PT =================
                    {
                        label: 'Target PT',
                        data: data.days.map(() => 65),
                        borderColor: '#d4a600',
                        borderDash: [4,4],
                        borderWidth: 2,
                        tension: 0,
                        pointRadius: ctx =>
                            ctx.dataIndex === ctx.dataset.data.length - 1 ? 5 : 0,
                        pointBackgroundColor: '#d4a600',
                        borderCapStyle: 'butt',
                        borderJoinStyle: 'miter',
                        fill: false,
                        datalabels: {
                             display: (ctx) => ctx.dataIndex === ctx.dataset.data.length - 1,
                             align: 'left',
                             anchor: 'end',
                             offset: 8,
                             clamp: true,
                             clip: false,
                             color: '#d4a600',
                             font: { size: 11, weight: '500' },
                             formatter: () => 'Target Pass Through 65%' 
                            }
                    }
                ]
            },

            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: { right: 30 }
                },

                plugins: {

                    // 🔥 Hide target from legend
                    legend: {
                        labels: {
                            color: '#444', font: { size: 12, weight: '600' },
                            filter: item => !item.text.includes('Target')
                        }
                    },

                    // 🔥 Hide target from tooltip
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                if (ctx.dataset.label.includes('Target')) {
                                    return null;
                                }
                                return `${ctx.dataset.label}: ${Math.round(ctx.raw)}%`;
                            }
                        }
                    },
                    datalabels: {
                        display: (ctx) => !ctx.dataset.label.includes('Target'),
                        align: 'top',
                        anchor: 'end',
                        color: 'rgba(0,0,0,0.6)',
                        font: { size: 9, weight: '400' },
                        formatter: (value) => Math.round(value) + '%' }

                },

                scales: {
                    x: {
                        grid: { display: false },
                        title: { display: true, text: 'February',
}
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: val => val + "%"
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.04)',
                            drawBorder: false
                        }
                    }
                }
            }
        });

    });
}

 
function renderParetoChart() {
    console.log("pareto belum dibuat");
}


 function updateActiveFilters() {

    const container = $('#active-filters');
    const wrapper = $('#filter-wrapper');

    container.html('');

    const filters = [
        {
            label: 'Inspection Post',
            value: $('#filter-inspection_post').val(),
            text: $('#filter-inspection_post option:selected').text()
        },
        {
            label: 'Supplier / Customer',
            value: $('#filter-supplier').val(),
            text: $('#filter-supplier option:selected').text()
        },
        {
            label: 'Category',
            value: $('#filter-jenis_part').val(),
            text: $('#filter-jenis_part option:selected').text()
        },
        {
            label: 'Spray Booth',
            value: $('#filter-spraybooth').val(),
            text: $('#filter-spraybooth option:selected').text()
        },
        {
            label: 'Part Name',
            value: $('#filter-part_name').val(),
            text: $('#filter-part_name option:selected').text()
        }
    ];

    let activeCount = 0;

    filters.forEach(f => {

        if (!f.value || f.value === '') return;

        const displayValue = f.text ? f.text : f.value;
        if (displayValue.includes('--')) return;

        activeCount++;

        const badge = $(`
            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full border border-blue-200 flex items-center gap-2">
                ${f.label}: ${displayValue}
            </span>
        `);

        container.append(badge);
    });

    // Show / Hide wrapper
    if (activeCount > 0) {
        wrapper.removeClass('hidden');
    } else {
        wrapper.addClass('hidden');
    }
}

let activeChart = 'performance';

$(document).ready(function () {

    // load default chart
    loadChart(activeChart);

    $('.chart-tab').on('click', function () {

        const type = $(this).data('type');
        activeChart = type;

        // switch active tab UI
        $('.chart-tab').removeClass('active');
        $(this).addClass('active');

        // load chart
        loadChart(type);
    });

    function loadChart(type) {

    if(type === 'performance') {
        renderPerformanceChart();
    }

    if(type === 'pareto') {
        renderParetoChart();
    }
}

});









  </script>

@endpush


@endsection