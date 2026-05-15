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
                <label class="block text-sm mb-1 font-medium text-gray-700">Asset Number</label>
                <input id="filter-asset-number" type="text" name="asset_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Asset Name</label>
                <input id="filter-name" type="text" name="asset_name" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>

            <div>
    <label for="filter-type" class="block text-sm mb-1 font-medium text-gray-700">Asset Type</label>
    <select id="filter-type" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
         <option value="Laptop">Laptop / Notebook</option>
                                    <option value="Desktop">Desktop / PC</option>
                                    <option value="Server">Server</option>
                                     <option value="CCTV">CCTV</option>
                                    <option value="Printer">Printer</option>
                                    <option value="Scanner">Scanner</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Network">Network Device</option>
                                    <option value="Storage">Storage / NAS</option>
                                    <option value="UPS">UPS</option>
                                    <option value="Smartphone">Smartphone</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Software">Software / License</option>
                                    <option value="Peripheral">Peripheral</option>
    </select>

    
</div>

<!-- Department -->
<div>
    <label for="filter-location" class="block text-sm mb-1 font-medium text-gray-700">Location</label>
    <select id="filter-location" name="location" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
         @php
                                        $locations = [
                                            'Ruang General Affair', 'Ruang HR', 'Ruang Server',
                                            'Pantry', 'Lobby Lt.1', 'R. Accounting', 'R. Purchasing & Marketing', 'Resepsionis',
                                            'Ruang Office LT.1', 'Ruang Office LT.2', 'Plant 1', 'Plant 2', 'R.Engineering-Quality',
                                            'Ruang Bima', 'Ruang Arjuna', 'Ruang Srikandi', 'Ruang Yudhistira',
                                        ];
                                    @endphp
        <option value="">-- All --</option>
         @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
         
    </select>
</div>

  <div>
    <label for="filter-condition" class="block text-sm mb-1 font-medium text-gray-700">Condition</label>
    <select id="filter-condition" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Good">Good</option>
                                    <option value="Broken but still usable">Broken but still usable</option>
                                    <option value="Damaged and cannot be used">Damaged and can't be used</option>
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
                d.asset_number = $('#filter-asset-number').val();
                d.location = $('#filter-location').val();
                d.asset_type = $('#filter-type').val();
                d.asset_name = $('#filter-name').val();
                d.condition = $('#filter-condition').val();
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
        filename: 'IT-Assets-' + today,
        title: null,
        text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
        exportOptions: {
            columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14]
        }
    },

    // ✅ FULL EXPORT MASUK SINI
    {
        text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Report',
       
        action: function (e, dt, node, config) {

            let params = dt.ajax.params(); // filter datatable
            let url = '/it/assets/export?' + $.param(params);

            window.location.href = url;
        }
    },

    {
        extend: 'pdfHtml5',
        filename: 'IT-Assets-' + today,
        title: null,
        orientation: 'landscape',
        pageSize: 'A4',
        text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
        exportOptions: {
            columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14]
        },
        customize: function(doc) {
            doc.styles.tableHeader.fontSize = 8;
            doc.defaultStyle.fontSize = 7;
        }
    },
    {
        extend: 'print',
        title: 'IT Assets ' + today,
        text: '<i class="fas fa-print mr-2"></i>Print',
        exportOptions: {
            columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14]
        },
        customize: function (win) {
            $(win.document.body).css('font-size', '10px');
        }
    }
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
  
  </script>
@endpush


@endsection