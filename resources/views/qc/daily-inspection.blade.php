@extends('layouts.app')

@section('title', 'Daily Inspection')
@section('page-title', 'DASHBOARD DAILY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Daily Inspection')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
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
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Inspection Date</label>
            <input id="filter-inspection_date" type="text" placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Supplier</label>
            <select id="filter-supplier" class="supplier w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Supplier --</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->code }}">{{ $supplier->name }}</option>
                @endforeach
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
                    <th class="px-4 py-2">Supplier</th>
                    <th class="px-4 py-2 ">Part Name</th>
                    <th class="px-4 py-2 ">Inspection Method</th>
                    <th class="px-4 py-2 text-center">Qty Received</th>
                    <th class="px-4 py-2 text-center">Total Check</th>
                    <th class="px-4 py-2 text-center">Total OK</th>
                    <th class="px-4 py-2 text-center">Total OK Repair/NC</th>
                    <th class="px-4 py-2 text-center">Total NG</th>
                    <th class="px-4 py-2 text-center">Pass Rate</th>
                    <th class="px-4 py-2 text-center">NC/OK Repair Rate</th>
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
  { data: 'supplier.name', name: 'supplier.name', className: 'text-left', orderable: false },
  { data: 'part_name', name: 'part_name', className: 'text-left', orderable: false },
  { data: 'check_method', name: 'check_method', className: 'text-center', orderable: false },
  { data: 'qty_received', name: 'qty_received', className: 'text-center', orderable: false },
  { data: 'total_check', name: 'total_check', className: 'text-center', orderable: false },
  { data: 'total_ok', name: 'total_ok', className: 'text-center', orderable: false },
  { data: 'total_ok_repair', name: 'total_ok_repair', className: 'text-center', orderable: false },
  { data: 'total_ng', name: 'total_ng', className: 'text-center', orderable: false },
  { data: 'pass_rate', name: 'pass_rate', className: 'text-center', orderable: false },
  { data: 'ok_repair_rate', name: 'ok_repair_rate', className: 'text-center', orderable: false },
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

  </script>

@endpush


@endsection