@extends('layouts.app')

@section('title', 'Purchase Order')
@section('page-title', 'DASHBOARD PURCHASE ORDER')
@section('breadcrumb-item', 'Purchasing')
@section('breadcrumb-active', 'Purchase Order')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Purchase Order</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Purchase Order Number</label>
                <input type="text" id="filter-order-number" class="w-full px-3 py-1 text-lg border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Supplier</label>
                 <select id="filter-supplier" class="supplier w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Supplier --</option>
        <?php foreach ($suppliers as $supp): ?>
            <option value="<?= $supp->id ?>"><?= htmlspecialchars($supp->name) ?></option>
        <?php endforeach; ?>
    </select>
            </div>
             <div>
                <label for="filter-order-date" class="block text-sm mb-1 font-medium text-gray-700">Order Date</label>
                 <input type="text" name="date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Draft">Draft</option>
        <option value="Revision">Revision</option>
        <option value="Approved">Approved</option>
        <option value="Authorized">Authorized</option>
        <option value="Verified">Verified</option>
        <option value="Partially Received">Partially Received</option>
        <option value="Closed">Closed</option>
        <option value="Rejected">Rejected</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('purchasing.po.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Purchase Order List</h2>
    <div class="w-full overflow-x-auto" id="po-scroll-wrapper">
    <table id="po-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Order Number</th>
                    <th class="px-4 py-2">Supplier</th>
                    <th class="px-4 py-2 !text-center w-28">Status</th>
                    <th class="px-4 py-2 text-center">Order Date</th>
                    <th class="px-4 py-2 text-center">Delivery Date</th>
                    <th class="px-4 py-2 text-center">Tax</th>
                    <th class="px-4 py-2 text-center">Termin</th>
                    <th class="px-4 py-2 text-right !text-right">Bruto</th>
                    <th class="px-4 py-2 text-right !text-right">Discount</th>
                    <th class="px-4 py-2 text-right !text-right">PPN</th>
                    <th class="px-4 py-2 text-right !text-right">Netto</th>
                    <th class="px-4 py-2">Note</th>
                    <th class="px-4 py-2">Created by</th>
                    <th class="px-4 py-2">Created at</th>
                    <th class="px-4 py-2">Approved by</th>
                    <th class="px-4 py-2">Approved at</th>
                    <th class="px-4 py-2">Authorized by</th>
                    <th class="px-4 py-2">Authorized at</th>
                    <th class="px-4 py-2">Verified by</th>
                    <th class="px-4 py-2">Verified at</th>
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
#po-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#po-table tbody tr:nth-child(odd) {
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
#po-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#po-table th, #po-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#po-table td, #po-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#po-scroll-wrapper {
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

    $('#filter-supplier').select2({
      placeholder: "-- All Supplier --",
      allowClear: true,
      width: '100%'
    });
     });
    let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#po-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("purchasing.po.data") }}',
            
            data: function (d) {
                d.order_number = $('#filter-order-number').val();
                d.status = $('#filter-status').val();
                d.supplier = $('#filter-supplier').val();
                d.order_date = $('#filter-date').val();
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
                filename: 'Purchase_Order_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Purchase_Order_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Purchase Order ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
  { data: 'order_number', name: 'order_number', orderable: false },
  { data: 'supplier.name', name: 'supplier.name', orderable: false },
  { data: 'status', name: 'status', className: 'text-center', orderable: false },
  { data: 'order_date', name: 'order_date', className: 'text-center', orderable: false },
  { data: 'delivery_date', name: 'delivery_date', className: 'text-center', orderable: false },
  { data: 'pkp', name: 'pkp', className: 'text-center', orderable: false },
  { data: 'top', name: 'top', className: 'text-center', orderable: false },
  { data: 'subtotal', name: 'subtotal', className: 'text-right', orderable: false },
  { data: 'discount', name: 'discount', className: 'text-right', orderable: false },
  { data: 'ppn', name: 'ppn', className: 'text-right', orderable: false },
  { data: 'pph', name: 'pph', className: 'text-right', orderable: false },
  { data: 'note', name: 'note', orderable: false },
  { data: 'created_by', name: 'created_by', orderable: false },
  { data: 'created_at', name: 'created_at', orderable: false },
  { data: 'approved_by', name: 'approved_by', orderable: false },
  { data: 'approved_at', name: 'approved_at', orderable: false },
  { data: 'authorized_by', name: 'authorized_by', orderable: false },
  { data: 'authorized_at', name: 'authorized_at', orderable: false },
  { data: 'verified_by', name: 'verified_by', orderable: false },
  { data: 'verified_at', name: 'verified_at', orderable: false },
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

function approvePO(id, orderNumber) {

    Swal.fire({
        title: 'Approve Purchase Order',
        html: `Approve this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Approved: ' + res.order_number);
                setTimeout(() => location.reload(), 3000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui PO.');
            });
        }
    });
}

function verifiedPO(id, orderNumber) {

    Swal.fire({
        title: 'Verified Purchase Order',
        html: `Verified this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verified it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/verified`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Verified: ' + res.order_number);
                setTimeout(() => location.reload(), 3000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan PO.');
            });
        }
    });
}

function authorizedPO(id, orderNumber) {

    Swal.fire({
        title: 'Authorize Purchase Order',
        html: `Authorize this Purchase Order: <strong>${orderNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-order/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Order has been Authorized: ' + res.order_number);
                setTimeout(() => location.reload(), 3000);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan PO.');
            });
        }
    });
}
  </script>

@endpush


@endsection