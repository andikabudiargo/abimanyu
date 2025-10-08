@extends('layouts.app')

@section('title', 'Ticket Management')
@section('page-title', 'DASHBOARD TICKET')
@section('breadcrumb-item', 'Helpdesk')
@section('breadcrumb-active', 'Ticket Management')

@section('content')

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Backup</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Data Source</label>
                <input id="filter-ticket-number" type="text" name="ticket_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <input id="filter-date" type="text" name="date"  placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
    
</div>

            <div>
    <label for="filter-category" class="block text-sm mb-1 font-medium text-gray-700">Category</label>
    <select id="filter-category" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
       
    </select>

    
</div>

<!-- Status -->
<div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Work in Progress">Work in Progress</option>
        <option value="On Hold">On Hold</option>
        <option value="Done">Done</option>
        <option value="Closed">Closed</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>

<!-- Department -->
<div>
    <label for="filter-department" class="block text-sm mb-1 font-medium text-gray-700">Location</label>
    <select id="filter-department" name="department" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
      
    </select>
</div>

<div>
    <label for="filter-teknisi" class="block text-sm mb-1 font-medium text-gray-700">Assigned</label>
    <select id="filter-teknisi" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Andika Budiargo">Andika Budiargo</option>
        <option value="Ivan Jovian">Ivan Jovian</option>
        <option value="Iwan Kuswandi">Iwan Kuswandi</option>
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
    <h2 class="text-lg font-semibold">Backup History</h2>
    <table id="backup-log-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2 !text-center">Backup Date</th>
                    <th class="px-4 py-2">Backup Name</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2 !text-center">Duration</th>
                    <th class="px-4 py-2 !text-center">File Size</th>
                    <th class="px-4 py-2 !text-center">Evidence</th>
                    <th class="px-4 py-2">Remark</th>
                    <th class="px-4 py-2">Created by</th>
                    <th class="px-4 py-2">Created at</th>
                    <th class="px-4 py-2">Updated at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
</div>

<div id="BackupLogModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-5xl rounded-xl shadow-lg relative 
              max-h-[80vh] flex flex-col"> <!-- flex kolom -->

    <!-- Header -->
    <div class="p-6 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Create Backup Log</h3>
      <button id="closeModalIcon" class="text-gray-400 hover:text-red-500 text-xl">&times;</button>
    </div>

    <!-- Reminder: Old Backup -->
<div class="px-6 pt-3">
  <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200 flex items-start gap-3">
    <i data-feather="alert-triangle" class="text-yellow-500 w-5 h-5 mt-1"></i>
    <p class="text-sm text-yellow-800 font-medium leading-relaxed">
      Don't forget to delete the oldest data, before starting a new backup.
    </p>
  </div>
</div>


    <!-- Body (scrollable) -->
    <div class="p-6 overflow-y-auto flex-1">
      <form id="BackupLogForm" class="space-y-4">
        @csrf

      <!-- Select Plan -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Select Backup Plan</label>
        <select id="backup_plan" name="backup_plan_id"
          class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
          <option value="">-- Choose Backup Plan --</option>
          <!-- options dari database -->
        </select>
      </div>

      <!-- Source & Target info -->
      <div class="grid grid-cols-3 items-center gap-6">
        <!-- Source -->
        <div class="border rounded-lg p-4 shadow-sm">
          <div class="flex items-center gap-2 mb-3">
            <i data-feather="database" class="text-indigo-600"></i>
            <h4 class="text-md font-semibold text-indigo-600">Source</h4>
          </div>
          <span id="source_info" class="text-sm text-gray-700">-</span>
        </div>

        <!-- Arrow -->
        <div class="flex justify-center items-center">
          <i data-feather="chevrons-right" class="w-10 h-10 text-gray-500"></i>
        </div>

        <!-- Target -->
        <div class="border rounded-lg p-4 shadow-sm">
          <div class="flex items-center gap-2 mb-3">
            <i data-feather="server" class="text-green-600"></i>
            <h4 class="text-md font-semibold text-green-600">Target</h4>
          </div>
          <span id="target_info" class="text-sm text-gray-700">-</span>
        </div>
      </div>

        <div id="backup_info" class="hidden p-4 rounded-lg bg-blue-50 border border-blue-200 flex flex-col gap-2 shadow-sm">
  <div class="flex items-center gap-2">
    <i data-feather="info" class="text-blue-500 w-5 h-5"></i>
    <h4 class="text-blue-700 font-semibold">Information</h4>
  </div>
  <p class="text-sm text-blue-800" id="backup_type_desc">-</p>
</div>

      <!-- Backup Info -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Create Folder</label>
          <input type="text" name="create_folder" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Delete Folder</label>
          <input type="text" name="create_folder" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Backup Start</label>
          <input type="time" name="start_time" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Backup End</label>
          <input type="time" name="end_time" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status Backup</label>
          <select name="status" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="success">Success</option>
            <option value="failed">Failed</option>
          </select>
        </div>
       <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Final Size</label>
  <div class="relative">
    <input type="number" step="0.01" name="final_size"
      class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300 pr-12"
      placeholder="0.00">
    <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 font-medium">GB</span>
  </div>
</div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Evidence</label>
          <input type="file" name="evidence" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
      </div>

      <!-- Remark -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Remark</label>
        <textarea name="remark" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
      </div>
    </form>
 </div>

    <!-- Footer (sticky) -->
    <div class="p-4 border-t flex justify-end gap-2">
      <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
      <button type="submit" form="BackupLogForm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
    </div>
  </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#ticket-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#ticket-table tbody tr:nth-child(odd) {
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
#ticket-table td,
#ticket-table th {
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
 $(document).ready(function() {
// =================== MODAL MANAGEMENT ========================
  $('#openModalBtn').on('click', function(e){
    e.preventDefault();
    $('#BackupLogModal').removeClass('hidden');
  });

  // Tutup modal (Cancel / X)
  $('#closeModalBtn, #closeModalIcon').on('click', function(){
    $('#BackupLogModal').addClass('hidden');
  });

  // Klik luar modal untuk close
  $('#BackupLogModal').on('click', function(e){
    if ($(e.target).is('#BackupLogModal')) {
      $(this).addClass('hidden');
    }
  });

  // =================== SELECT2 ====================================
  $('#backup_plan').select2({
    placeholder: "-- Choose Backup Plan --",
    allowClear: true,
    ajax: {
        url: '/it/backup-schedule/select',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        id: item.id,
                        text: item.job_name,
                        source: item.source,   // simpan source
                        target: item.target,
                        backup_type: item.backup_type   // simpan target
                    }
                })
            };
        },
        cache: true
    }
});

// Saat user pilih plan
$('#backup_plan').on('select2:select', function (e) {
    let data = e.params.data;
    $('#source_info').text(data.source ?? '-');
    $('#target_info').text(data.target ?? '-');
    $('#backup_info').removeClass('hidden');
    // update isi div dengan bold untuk backup type
    let desc = '';
if(data.backup_type === 'Full Backup') {
    desc = 'The Backup Plan you selected follows a <strong>Full Backup</strong> procedure, copying all data for backup.';
} else if(data.backup_type === 'Incremental Backup') {
    desc = 'The Backup Plan you selected follows an <strong>Incremental Backup</strong> procedure, backing up only the data that has changed since the last backup.';
} else if(data.backup_type === 'Differential Backup') {
    desc = 'The Backup Plan you selected follows a <strong>Differential Backup</strong> procedure, backing up the data that has changed since the last full backup.';
} else {
    desc = '-';
}


    $('#backup_type_desc').html(desc); // baru set html setelah desc diisi
});

// Saat clear pilihan
$('#backup_plan').on('select2:clear', function () {
    $('#source_info').text('-');
    $('#target_info').text('-');
     $('#backup_type_desc').text('');
     $('#backup_info').addClass('hidden');
});

$('#BackupLogForm').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this); // gunakan FormData untuk file

    $.ajax({
        url: "{{ route('it.backup.store') }}",
        type: "POST",
        data: formData,
        processData: false,  // penting agar file terkirim
        contentType: false,  // penting agar file terkirim
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Backup Log Successfully Saved!',
                timer: 2000,
                showConfirmButton: false
            });
            location.reload();
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


//======================= DATATABLE ==========================================
let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#backup-log-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("it.backup.data") }}',
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
            { data: 'backup_date', name: 'backup_date', className: 'text-center', orderable: false },
            { data: 'backup_plan_id', name: 'backup_plan_id', orderable: false },
            { data: 'status', name: 'status', orderable: false, className: 'text-center' },
            { data: 'start_time', name: 'start_time', orderable: false },
            { data: 'final_size', name: 'final_size', className: 'text-center', orderable: false },
            { data: 'evidence', name: 'evidence', className: 'text-center', orderable: false },
            { data: 'remark', name: 'remark', orderable: false },
            { data: 'created_by', name: 'created_by', orderable: false },
            { data: 'created_at', name: 'created_at', orderable: false },
            { data: 'updated_at', name: 'updated_at', orderable: false },
        ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });


});
  
  </script>
@endpush


@endsection