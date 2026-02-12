@extends('layouts.app')

@section('title', 'CAPA Management')
@section('page-title', 'DASHBOARD CAPA MANAGEMENT')
@section('breadcrumb-item', 'Management Representative')
@section('breadcrumb-active', 'CAPA Management')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter CAPA Management</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">CAPA Number</label>
                <input type="text" placeholder="Masukan CAPA No..." id="filter-capa-number" class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Category</label>
                   <select id="filter-category" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Category --</option>
        <option value="Critical">Critical</option>
        <option value="Minor">Minor</option>
        <option value="Major">Major</option>
        <option value="Observation">Observation</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
             <div>
                <label for="filter-order-date" class="block text-sm mb-1 font-medium text-gray-700">Report Date</label>
                 <input type="text" name="report_date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                 <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                  <select name="dept" id="filter-department" class=" w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
         <option value="">-- All --</option>
       @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Auditor</label>
                 <select name="auditor" id="filter-auditor" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Auditor --</option>
        @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>

            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Draft">Draft</option>
        <option value="Posted">Posted</option>
        <option value="Verified">Verified</option>
        <option value="In Progress">In Progress</option>
        <option value="Submitted">Submitted</option>
        <option value="Authorized">Authorized</option>
        <option value="Closed">Closed</option>
        <option value="Returned for Action">Returned for Action</option>
        <option value="Returned for Evidence">Returned for Evidence</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('mr.capa.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

@php
    $isMR = auth()->check() &&
        auth()->user()
            ->departments()
            ->whereRaw('LOWER(name) = ?', ['management representative'])
            ->exists();
@endphp


<div class="p-6 bg-white rounded-lg shadow-md mb-4 {{ $isMR ? '' : 'hidden' }}">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
    <h2 class="text-lg font-bold text-gray-800 mb-2 sm:mb-0">
      CAPA Overview
    </h2>
     <p class="text-sm text-gray-500">
        Total <span class="font-bold text-gray-800 underline">{{ $total }}</span> CAPA was created 😎
      </p>
  </div>

  <!-- Divider -->
  <div class="border-t border-gray-200 mb-4"></div>

  <!-- Grid Card -->
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

    <!-- Draft -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-gray-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-file-pen text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Draft</p>
        <p class="text-xl font-bold text-gray-800"> {{ $draft }}</p>
      </div>
    </div>

    <!-- Posted -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-yellow-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-paper-plane text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Posted</p>
        <p class="text-xl font-bold text-gray-800">{{ $posted }}</p>
      </div>
    </div>

    <!-- Verified -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-blue-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Verified</p>
        <p class="text-xl font-bold text-gray-800">{{ $verified }}</p>
      </div>
    </div>

    <!-- In Progress -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-orange-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-spinner text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">In Progress</p>
        <p class="text-xl font-bold text-gray-800">{{ $inProgress }}</p>
      </div>
    </div>

    <!-- Submitted -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-indigo-600 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-inbox text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Submitted</p>
        <p class="text-xl font-bold text-gray-800">{{ $submitted }}</p>
      </div>
    </div>

    <!-- Authorized -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-green-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-user-check text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Authorized</p>
        <p class="text-xl font-bold text-gray-800">{{ $authorized }}</p>
      </div>
    </div>

    <!-- Closed -->
    <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
      <div class="p-3 bg-teal-500 text-white shadow-md rounded-lg mr-3">
        <i class="fa-solid fa-lock text-lg"></i>
      </div>
      <div>
        <p class="text-gray-600 text-sm">Closed</p>
        <p class="text-xl font-bold text-gray-800">{{ $closed }}</p>
      </div>
    </div>

    <!-- Overdue -->
    <!-- Overdue -->
<div
  id="btnOverdue"
  class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition cursor-pointer"
>
  <div class="p-3 bg-red-500 text-white shadow-md rounded-lg mr-3">
    <i class="fa-solid fa-calendar-xmark text-lg"></i>
  </div>
  <div>
    <p class="text-gray-600 text-sm">Overdue</p>
    <p class="text-2xl font-extrabold text-gray-900">
      {{ $overdue }}
    </p>
  </div>
</div>


  </div>
</div>

 

{{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
  {{-- Navigation / Title --}}

@if(
    auth()->user()
        ->departments()
        ->where('name','Management Representative')
        ->exists()
)


    {{-- MR View → Tanpa Tab --}}
    <h2 class="text-lg font-semibold mb-2">
        List of CAPA
    </h2>

@else

    {{-- Navigation Tab --}}
<div class="mb-6">
    <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner">

        {{-- Auditee --}}
        <button id="tab-auditee"
            class="tab-btn flex-1 flex items-center justify-center gap-2
                   px-4 py-2.5 rounded-lg text-sm font-semibold
                   transition-all duration-200
                   bg-blue-600 text-white shadow hover:text-blue-600
                   ">

            <i class="fa-solid fa-user-clock"></i>
            <span class="hidden sm:inline">CAPA as Auditee</span>
        </button>

        {{-- Auditor --}}
        <button id="tab-auditor"
            class="tab-btn flex-1 flex items-center justify-center gap-2
                   px-4 py-2.5 rounded-lg text-sm font-semibold
                   transition-all duration-200
                   text-gray-600 hover:text-blue-600">

            <i class="fa-solid fa-user-check"></i>
            <span class="hidden sm:inline">CAPA as Auditor</span>
        </button>

    </div>
</div>


@endif

   
    <div class="w-full overflow-x-auto" id="capa-scroll-wrapper">
        <table id="capa-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr class="bg-blue-500 text-white text-xs uppercase font-bold">
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Auditor</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Dept. Representative</th>
                    <th class="px-4 py-2 !text-center">CAPA No.</th>
                    <th class="px-4 py-2 !text-center">Report Date</th>
                    <th class="px-4 py-2 !text-center">Category</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2">Detail of Information</th>
                    <th class="px-4 py-2">Created By</th>
                    <th class="px-4 py-2">Created At</th>
                    <th class="px-4 py-2">Posted By</th>
                    <th class="px-4 py-2">Posted At</th>
                    <th class="px-4 py-2">Verified By</th>
                    <th class="px-4 py-2">Verified At</th>
                    <th class="px-4 py-2">Processed By</th>
                    <th class="px-4 py-2">Processed At</th>
                    <th class="px-4 py-2">Review By</th>
                    <th class="px-4 py-2">Review At</th>
                    <th class="px-4 py-2">Submitted By</th>
                    <th class="px-4 py-2">Submitted At</th>
                    <th class="px-4 py-2">Authorized By</th>
                    <th class="px-4 py-2">Authorized At</th>
                    <th class="px-4 py-2">Approved By</th>
                    <th class="px-4 py-2">Approved At</th>
                    <th class="px-4 py-2">Returned By</th>
                    <th class="px-4 py-2">Returned At</th>
                </tr>
            </thead>
            <tbody>
                {{-- DataTables mengisi otomatis --}}
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Overdue (Enterprise Style) -->
<div
  id="overdueModal"
  class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm"
>

  <!-- Container -->
  <div
    class="bg-white w-full max-h-[70vh] max-w-4xl rounded-2xl shadow-2xl overflow-hidden"
    id="overdueBox"
  >

    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b">

      <div>
        <h3 class="text-xl font-semibold text-gray-800">
          Overdue CAPA (Preventive Action)
        </h3>
        <p class="text-sm text-gray-500">
          List of delayed corrective & preventive actions
        </p>
      </div>

      <button
        id="closeOverdue"
        class="text-gray-400 hover:text-red-500 text-2xl transition"
      >
        <i class="fa-solid fa-xmark"></i>
      </button>

    </div>


    <!-- Body -->
    <div class="p-6 max-h-[70vh] overflow-y-auto">

      <div class="overflow-x-auto">

        <table class="w-full text-sm">

          <!-- Head -->
          <thead class="sticky top-0 bg-white shadow-sm">

            <tr class="text-left text-gray-600 border-b">

              <th class="py-3 px-4 font-semibold w-14">#</th>

              <th class="py-3 px-4 font-semibold">
                CAPA No.
              </th>

              <th class="py-3 px-4 font-semibold">
                Representative
              </th>

              <th class="py-3 px-4 font-semibold">
                Due Date
              </th>

              <th class="py-3 px-4 font-semibold text-center">
                Status
              </th>

            </tr>

          </thead>


          <!-- Body -->
          <tbody class="divide-y">

            @forelse($overdueList as $i => $row)

            <tr
              class="group hover:bg-red-50 cursor-pointer transition"
              data-url="{{ url('/mr/capa/'.$row->capa_id.'/detail') }}"
            >

              <!-- No -->
              <td class="py-3 px-4 text-gray-500">
                {{ $i + 1 }}
              </td>

              <!-- CAPA Number -->
              <td class="py-3 px-4 font-medium text-blue-600 group-hover:underline">
                {{ $row->capa->capa_number ?? '-' }}
              </td>

              <!-- Representative -->
              <td class="py-3 px-4">
                {{ $row->capa->representative->name ?? '-' }}
              </td>

              <!-- Due Date -->
              <td class="py-3 px-4 text-red-600 font-semibold">
                {{ \Carbon\Carbon::parse($row->due_date)->format('d M Y') }}
              </td>

              <!-- Status -->
             <td class="py-3 px-4 text-center">

  @php
  $due  = \Carbon\Carbon::parse($row->due_date)->startOfDay();
  $now  = today();

  $late = $due->diffInDays($now);
@endphp



  <span
    class="px-3 py-1 text-xs font-semibold text-red-700"
  >
    Terlambat Submit {{ $late }} Hari
  </span>

</td>


            </tr>

            @empty

            <tr>
              <td colspan="5" class="py-10 text-center text-gray-400">
                No overdue CAPA found
              </td>
            </tr>

            @endforelse

          </tbody>

        </table>

      </div>

    </div>


    <!-- Footer -->
    <div
      class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t"
    >

      <p class="text-sm text-gray-500">
        Total Overdue:
        <span class="font-semibold text-red-600">
          {{ $overdue }}
        </span>
      </p>

      <button
        id="closeOverdue2"
        class="px-4 py-2 text-sm bg-gray-700 text-white rounded-lg
               hover:bg-gray-800 transition"
      >
        Close
      </button>

    </div>

  </div>
</div>



{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#capa-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#capa-table tbody tr:nth-child(odd) {
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
#capa-table td,
#capa-table th {
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
#capa-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#capa-table th, #capa-table td {
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
  document.addEventListener('DOMContentLoaded', function () {

         flatpickr("#filter-date", {
      mode: "range",
      dateFormat: "Y-m-d",
      maxDate: "today",
      allowInput: true
    });

    $('#filter-status').select2({
      placeholder: "-- All Status --",
      allowClear: true,
      width: '100%'
    });

    // Select2 for supplier and article
    $('#filter-category').select2({
      placeholder: "-- All Category --",
      allowClear: true,
      width: '100%'
    });

     $('#filter-auditor').select2({
      placeholder: "-- All Auditor --",
      allowClear: true,
      width: '100%'
    });

    $('#filter-department').select2({
      placeholder: "-- All Department --",
      allowClear: true,
      width: '100%'
    });
     });
    let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
  let currentTab = 'auditee'; // default tab
   const table = $('#capa-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
}, 
       ajax: {
            url: '{{ route("mr.capa.data") }}',
            
            data: function (d) {
                 d.tab = currentTab; // tambahkan tab ke server
                d.capa_number = $('#filter-capa-number').val();
                d.category = $('#filter-category').val();
                d.status = $('#filter-status').val();
                d.auditor = $('#filter-auditor').val();
                d.dept = $('#filter-department').val();
                d.report_date = $('#filter-date').val();
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
                filename: 'List CAPA_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'List CAPA_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns:  [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'List CAPA' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                orientation: 'landscape',
                exportOptions: {
                columns:  [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
  { data: 'auditors', name: 'auditors', orderable: false, searchable: false },
  { data: 'departemen', name: 'departemen', orderable: false, searchable: false },
  { data: 'dept_representative', name: 'dept_representative', orderable: false, searchable: false },
  { data: 'capa_number', name: 'capa_number', orderable: false,searchable: false },
  { data: 'report_date', name: 'report_date', className: 'text-center', orderable: false, searchable: false },
  { data: 'category', name: 'category', orderable: false, searchable: false },
  { data: 'status', name: 'status',  className: 'text-center', orderable: false, searchable: false },
  { data: 'detail_of_information', name: 'detail_of_information', orderable: false },
  { data: 'created_by', name: 'created_by', orderable: false, searchable: false },
  { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
  { data: 'posted_by', name: 'posted_by', orderable: false, searchable: false },
  { data: 'posted_at', name: 'posted_at', orderable: false, searchable: false },
  { data: 'verified_by', name: 'verified_by', orderable: false, searchable: false },
  { data: 'verified_at', name: 'verified_at', orderable: false, searchable: false },
  { data: 'processed_by', name: 'submitted_by', orderable: false, searchable: false },
  { data: 'processed_at', name: 'submitted_at', orderable: false, searchable: false },
  { data: 'review_by', name: 'review_by', orderable: false, searchable: false },
  { data: 'review_at', name: 'review_at', orderable: false, searchable: false },
  { data: 'submitted_by', name: 'submitted_by', orderable: false, searchable: false },
  { data: 'submitted_at', name: 'submitted_at', orderable: false, searchable: false },
  { data: 'authorized_by', name: 'authorized_by', orderable: false, searchable: false },
  { data: 'authorized_at', name: 'authorized_at', orderable: false, searchable: false },
  { data: 'approved_by', name: 'approved_by', orderable: false, searchable: false },
  { data: 'approved_at', name: 'approved_at', orderable: false, searchable: false },
  { data: 'returned_by', name: 'returned_by', orderable: false, searchable: false },
  { data: 'returned_at', name: 'returned_at', orderable: false, searchable: false },
]

    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
       
/**
 * ==============================
 * TAB AUDITOR / AUDITEE HANDLER
 * ==============================
 */

if ($('#tab-auditor').length && $('#tab-auditee').length) {

    $('#tab-auditor').on('click', function(){

        if(currentTab === 'auditor') return;

        currentTab = 'auditor';

        $(this)
            .addClass('border-blue-500 text-blue-600')
            .removeClass('text-gray-500 border-transparent');

        $('#tab-auditee')
            .removeClass('border-blue-500 text-blue-600')
            .addClass('text-gray-500 border-transparent');

        table.ajax.reload(null, false);
    });


    $('#tab-auditee').on('click', function(){

        if(currentTab === 'auditee') return;

        currentTab = 'auditee';

        $(this)
            .addClass('border-blue-500 text-blue-600')
            .removeClass('text-gray-500 border-transparent');

        $('#tab-auditor')
            .removeClass('border-blue-500 text-blue-600')
            .addClass('text-gray-500 border-transparent');

        table.ajax.reload(null, false);
    });

}
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


function confirmPosting(id) {
    Swal.fire({
        title: 'Post this CAPA?',
        text: 'Post CAPA and submit to MR?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Post It!'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/capa/${id}/posted`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                if (res.success) {
                    showToast('success', 'CAPA Posted.');
                     $('#capa-table').DataTable().ajax.reload(null, false);
                } else {
                    showToast('error', res.message || 'Failed to post CAPA.');
                }
            }).fail(function (err) {
                console.error(err.responseText);
                showToast('error', 'An error occurred while post the CAPA.');
            });
        }
    });
}


 function confirmDelete(id) {
    Swal.fire({
         title: "Are you sure you want to delete?",
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/mr/capa/delete/${id}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.fire('CAPA Deleted!', res.doc_number, 'success');
                   $('#capa-table').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal!', 'Delete CAPA Failed.', 'error');
                }
            });
        }
    });
}

$(document).ready(function () {

  // Open modal
  $('#btnOverdue').on('click', function () {

    $('#overdueModal')
      .removeClass('hidden')
      .addClass('flex');

    $('#overdueBox')
      .removeClass('scale-95 opacity-0')
      .addClass('scale-100 opacity-100');

  });


  // Close modal
  $('#closeOverdue, #closeOverdue2').on('click', function () {
    closeOverdueModal();
  });


  // Click outside
  $('#overdueModal').on('click', function (e) {

    if ($(e.target).is('#overdueModal')) {
      closeOverdueModal();
    }

  });


  function closeOverdueModal() {

    $('#overdueModal')
      .addClass('hidden')
      .removeClass('flex');

  }


  // ============================
  // CLICK ROW → DETAIL CAPA
  // ============================
  $('body').on('click', 'tr[data-url]', function () {

    let url = $(this).data('url');

    window.location.href = url;

  });

});

const tabs = document.querySelectorAll('.tab-btn');

tabs.forEach(tab => {
    tab.addEventListener('click', function () {

        // Reset semua tab
        tabs.forEach(t => {
            t.classList.remove('bg-blue-600', 'text-white', 'shadow');
            t.classList.add('text-gray-600');
        });

        // Aktifkan yang diklik
        this.classList.add('bg-blue-600', 'text-white', 'shadow');
        this.classList.remove('text-gray-600');

    });
});

  </script>

@endpush


@endsection