@extends('layouts.app')

@section('title', 'Transfer In')
@section('page-title', 'DASHBOARD TRANSFER IN')
@section('breadcrumb-item', 'Logistic')
@section('breadcrumb-active', 'Transfer In')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Transfer</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCode" class="block text-sm mb-1 font-medium text-gray-700">Transfer Number</label>
                <input type="text" id="filter-code" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
              <div>
                <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
                 <input type="text" name="date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
            <div>
    <label for="filter-category" class="block text-sm mb-1 font-medium text-gray-700">Category</label>
    <select id="filter-category" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Category --</option>
        <option value="Incoming">Incoming</option>
        <option value="Material Return">Material Return</option>
        <option value="Temporary Saved">Temporary Saved</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
</div>
          <div>
    <label for="filter-from" class="block text-sm mb-1 font-medium text-gray-700">From</label>
    <select id="filter-from" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- Choose From --</option> <!-- Default kosong -->
        <optgroup label="LOCATION">
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ strtoupper($w->name) }}</option>
            @endforeach
        </optgroup>
        <optgroup label="SUPPLIER">
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}">{{ strtoupper($s->name) }}</option>
            @endforeach
        </optgroup>
    </select>
</div>

             <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Draft">Draft</option>
        <option value="Revision">Revision</option>
        <option value="Posted">Posted</option>
        <option value="Closed">Closed</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('ppic.transfer.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

  {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Transfer In List</h2>
    <div class="w-full overflow-x-auto" id="trin-scroll-wrapper">
    <table id="trin-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Transfer Number</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2 !text-center w-28" >Status</th>
                    <th class="px-4 py-2 !text-center w-28" >Type</th>
                    <th class="px-4 py-2">Reference Number</th>
                    <th class="px-4 py-2">From</th>
                    <th class="px-4 py-2">Note</th>
                    <th class="px-4 py-2">Created at</th>
                    <th class="px-4 py-2">Created by</th>
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
#trin-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#trin-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    margin-left: 10px;
}

/* 🧭 Spacing */
#trin-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#trin-table th, #trin-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#trin-table td, #trin-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#trin-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
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

.select2-container .select2-selection--single {
    height: 38px !important; /* sesuaikan dengan input lainnya */
    padding: 4px 10px;
    border: 1px solid #d1d5db; /* warna border sama dengan Tailwind border-gray-300 */
    border-radius: 0.375rem; /* sesuai rounded-md */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px; /* biar teks sejajar vertikal */
    font-size: 12px; /* text-base */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
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

    // Select2 for supplier and article
    $('#filter-status').select2({
      placeholder: "-- All Status --",
      allowClear: true,
      width: '100%'
    });

      $('#filter-category').select2({
      placeholder: "-- All Category --",
      allowClear: true,
      width: '100%'
    });

    $('#filter-supplier').select2({
      placeholder: "-- All Supplier --",
      allowClear: true,
      width: '100%'
    });

   $(document).ready(function() {
    $('#filter-from').select2({
        placeholder: "-- All Location --",
        allowClear: true,
        width: '100%'
    });
});

     });
    let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#trin-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: "{{ route('ppic.transfer-in.data') }}",
        data: function (d) {
                d.code = $('#filter-code').val();
                d.transfer_category = $('#filter-category').val();
                d.status = $('#filter-status').val();
                d.from = $('#filter-from').val();
                d.date = $('#filter-date').val();
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
                filename: 'Transfer_In_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Transfer_In' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Transfer In ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
            { data: 'code', name: 'code',},
            { data: 'date', name: 'date' },
            { data: 'status', name: 'status' }, 
            { data: 'transfer_category', name: 'transfer_category' },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'from_location', name: 'from_location' },
            { data: 'note', name: 'note' },
            { data: 'created_at', name: 'created_at' },
            { data: 'created_by', name: 'created_by' },
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

function approveIN(id, code) {

    Swal.fire({
        title: 'Post Transfer In',
        html: `Post this Transfer In: <strong>${code} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Post it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/ppic/logistic/transfer_in/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Transfer In has been Posted: ' + res.code);
                 $('#trin-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui Transfer In.');
            });
        }
    });
}
</script>

@endpush


@endsection