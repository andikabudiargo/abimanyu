@extends('layouts.app')

@section('title', 'Ticket Management')
@section('page-title', 'DASHBOARD TICKET')
@section('breadcrumb-item', 'Helpdesk')
@section('breadcrumb-active', 'Ticket Management')

@section('content')

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Assets</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Asset Number</label>
                <input id="filter-ticket-number" type="text" name="ticket_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Purchase Date</label>
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
            <a href="{{ route('it.assets.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold">Assets List</h2>
    <table id="asset-table" class="w-full text-sm">
           <thead class="bg-gradient-to-r from-blue-600 to-blue-500 text-white uppercase text-xs font-semibold tracking-wider shadow-sm whitespace-nowrap">
  <tr>
    <th class="px-4 py-3 text-left">Action</th>
    <th class="px-4 py-3 text-left">Location</th>
    <th class="px-4 py-3 text-left">Asset Type</th>
    <th class="px-4 py-3 text-left !w-[270px]">Asset ID</th>
    <th class="px-4 py-3 text-left">Name</th>
    <th class="px-4 py-3 !text-center">Condition</th>
    <th class="px-4 py-3 text-left">Lifetime</th>
    <th class="px-4 py-3 text-left">Warranty</th>
    <th class="px-4 py-3 !text-center">Status</th>
    <th class="px-4 py-3 text-left">Assignment</th>
    <th class="px-4 py-3 text-left">PIC</th>
    <th class="px-4 py-3 text-left">Category</th>
    <th class="px-4 py-3 text-left">Purchase Date</th>
    <th class="px-4 py-3 text-left">Supplier</th>
    <th class="px-4 py-3 text-left">Note</th>
    <th class="px-4 py-3 text-left">Registered At</th>
    <th class="px-4 py-3 text-left">Updated At</th>
  </tr>
</thead>

            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#asset-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#asset-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

table.dataTable thead th {
    border-bottom: 2px solid #2563eb; /* biru lebih pekat */
}
table.dataTable tbody tr:hover {
    background-color: #f1f5f9; /* efek hover abu lembut */
    transition: background-color 0.3s ease;
}

/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* Non-Tailwind CSS */
#asset-table td,
#asset-table th {
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
#asset-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#asset-table th, #asset-table td {
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
let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#asset-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("it.assets.data") }}',
            data: function (d) {
                d.ticket_number = $('#filter-ticket-number').val();
                d.status = $('#filter-status').val();
                d.processed_by = $('#filter-teknisi').val();
                d.category = $('#filter-category').val();
                d.date = $('#filter-date').val();
                d.department = $('#filter-department').val();
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
                filename: 'Helpdesk_Ticket_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Helpdesk_Ticket_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Helpdesk Ticket ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            },
             {
    text: '<i class="fas fa-chart-pie mr-1" style="font-size: 12px;"></i>Report',
    className: 'text-sm rounded shadow-sm flex items-center',
    action: function (e, dt, node, config) {
        window.open('/it/ticket/report', '_blank'); // ganti sesuai URL
    }
},
        ]
    },

    
    
],
      columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'location', name: 'location', orderable: false },
        { data: 'asset_type', name: 'asset_type', orderable: false },
        { data: 'asset_number', name: 'asset_number',  orderable: false },
        { data: 'asset_name', name: 'asset_name', orderable: false },
        { data: 'conditions', name: 'conditions',  className: 'text-center', orderable: false },
        { data: 'lifetime', name: 'lifetime', className: 'text-center', orderable: false },
        { data: 'warranty', name: 'warranty', orderable: false }, // relasi pivot
        { data: 'status', name: 'status', className: 'text-center', orderable: false },
        { data: 'assignment_type', name: 'assignment_type', className: 'text-center', orderable: false },
        { data: 'assigned_to', name: 'assigned_to', className: 'text-center', orderable: false },
        { data: 'acquistion_type', name: 'acquistion_type', orderable: false },
        { data: 'purchase_date', name: 'purchase_date', className: 'text-center', orderable: false },
        { data: 'supplier_id', name: 'supplier_id', className: 'text-center', orderable: false },
        { data: 'note', name: 'note', orderable: false },
        { data: 'created_at', name: 'created_at', orderable: false },
        { data: 'updated_at', name: 'updated_at',  className: 'text-center', orderable: false },
      ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

  $(document).ready(function () {
    $('.select2').select2({
        width: 'resolve', // atau '100%' bisa juga
        theme: 'default' // biar tidak override tailwind terlalu banyak
    });

    // Sinkronisasi tinggi agar konsisten dengan input biasa
    $('.select2').on('select2:open', function (e) {
        $('.select2-container--default .select2-selection--single').css({
            'height': '38px', // sama dengan input
            'padding': '4px 10px', // padding input
            'border': '1px solid #d1d5db', // warna border tailwind gray-300
            'border-radius': '0.375rem' // rounded-md
        });
    });
});
  
  </script>
@endpush


@endsection