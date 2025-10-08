@extends('layouts.app')

@section('title', 'Storage Management')
@section('page-title', 'DASHBOARD STORAGE MANAGEMENT')
@section('breadcrumb-item', 'Backup Managament')
@section('breadcrumb-active', 'Storage Management')

@section('content')

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Storage Management</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input id="filter-ticket-number" type="text" name="ticket_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Storage Name</label>
    <input id="filter-date" type="text" name="date"  placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
    
</div>

<!-- Status -->
<div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Normal">Normal</option>
        <option value="Warning">Warning</option>
        <option value="Empty">Empty</option>
        <option value="Full">Full</option>
    </select>
</div>

            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="#" id="openModalBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
  Create
</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold">Storage List</h2>
    <table id="storage-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Storage</th>
                    <th class="px-4 py-2 !text-center">Storage Type</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2 !text-center">Category</th>
                    <th class="px-4 py-2">Updated at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
</div>

<div id="ModalCreateStorage" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Storage</h3>

    <form id="createStorageForm">
        @csrf
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Storage Type</label>
           <select name="storage_type" id="storage_type" class="select2  w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="">-- Choose Type --</option>
            <option value="Internal HDD">Internal HDD</option>
            <option value="External HDD">External HDD</option>
            <option value="Cloud">Cloud</option>
            <option value="NAS">NAS</option>
            <option value="Flashdisk">Flashdisk</option>
            <option value="SD Card">SD Card</option>
            <option value="CD/DVD">CD/DVD</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
           <select name="category" id="category" class="select2  w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="">-- Choose Category --</option>
            <option value="Primary Storage">Primary Storage</option>
            <option value="Backup Storage">Backup Storage</option>
            <option value="Temporary Storage">Temporary Storage</option>
          </select>
        </div>
         <div class="col-span-2">
  <label class="block text-sm font-medium text-gray-700 mb-1">Storage Name</label>
 <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
</div>

      <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Total Capacity</label>
  <div class="relative">
    <input 
      type="text" 
      name="capacity" 
      class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300 pr-12"
      placeholder="0"
      oninput="this.value = this.value.replace(/[^0-9.,]/g, '').replace(',', '.')"
    >
    <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 font-medium">GB</span>
  </div>
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Used Capacity</label>
  <div class="relative">
    <input 
      type="text" 
      name="used" 
      class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300 pr-12"
      placeholder="0"
      oninput="this.value = this.value.replace(/[^0-9.,]/g, '').replace(',', '.')"
    >
    <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 font-medium">GB</span>
  </div>
</div>


      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
      </div>
    </form>

    <!-- ❌ Tombol Close di pojok -->
    <button id="closeModalIcon" class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl">&times;</button>
  </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#storage-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#storage-table tbody tr:nth-child(odd) {
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
#storage-table td,
#storage-table th {
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
#ticket-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#ticket-table th, #ticket-table td {
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
$(document).ready(function(){

//======================== SELECT2 ==============================
// Storage Type
    $('#storage_type').select2({
        placeholder: "-- Choose Storage Type --",
        allowClear: true,
        width: '100%'
    });

    // Category
    $('#category').select2({
        placeholder: "-- Choose Category --",
        allowClear: true,
        width: '100%'
    });

//======================== DATATABEL ============================

let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#storage-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("it.storage.data") }}',
             data: function (d) {
                d.code = $('#filter-code').val();
                d.capacity = $('#filter-capacity').val();
                d.location = $('#filter-location').val();
                d.equipment = $('#filter-equipment').val();
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
                filename: 'List_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'List_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
            },
            {
                extend: 'print',
                title: 'List Ruangan ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
            { data: 'name', name: 'name' },
            { data: 'storage_type', name: 'storage_type', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
             { data: 'category', name: 'category', className: 'text-center' },
            { data: 'updated_at', name: 'updated_at' },
        ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

//======================== MODAL MANAGEMENT ======================

  // Buka modal
  $('#openModalBtn').on('click', function(e){
    e.preventDefault();
    $('#ModalCreateStorage').removeClass('hidden');
  });

  // Tutup modal (Cancel / X)
  $('#closeModalBtn, #closeModalIcon').on('click', function(){
    $('#ModalCreateStorage').addClass('hidden');
  });

  // Klik luar modal untuk close
  $('#ModalCreateStorage').on('click', function(e){
    if ($(e.target).is('#ModalCreateStorage')) {
      $(this).addClass('hidden');
    }
  });


  //===================== SIMPAN DATA ========================================
    // Simpan data via Ajax
$('#createStorageForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();

    $.ajax({
        url: "{{ route('it.storage.store') }}",
        type: "POST",
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Storage Successfully Saved!',
                timer: 2000,
                showConfirmButton: false
            });

            // Tutup modal
            $('#ModalCreateStorage').addClass('hidden');

        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: xhr.responseJSON?.message || 'Terjadi kesalahan!',
            });
        }
    });
});

});
  
  </script>
@endpush
@endsection