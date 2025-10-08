@extends('layouts.app')

@section('title', 'Purchase Request')
@section('page-title', 'DASHBOARD PURCHASE REQUEST')
@section('breadcrumb-item', 'Purchasing')
@section('breadcrumb-active', 'Purchase Request')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Purchase Request</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filter-request-number" class="block text-sm mb-1 font-medium text-gray-700">Purchase Request Number</label>
                <input type="text" id="filter-request-number" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filter-request-date" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
                 <input type="text" name="date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
            <div>
    <label for="filter-order-type" class="block text-sm mb-1 font-medium text-gray-700">Order Type</label>
    <select id="filter-order-type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Type --</option>
        <option value="Standard">Standard</option>
        <option value="Target Sales Order">Target Sales Order</option>
        <option value="GA Request">General Affair Request</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
</div>

<div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Draft">Draft</option>
        <option value="Approved">Approved</option>
        <option value="Authorized">Authorized</option>
        <option value="Verified">Verified</option>
        <option value="Partially Order">Partially Order</option>
        <option value="Full Order">Full Order</option>
        <option value="Closed">Closed</option>
        <option value="Rejected">Rejected</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
</div>

<div>
    <label for="filter-department" class="block text-sm mb-1 font-medium text-gray-700">Department</label>
    <select id="filter-department" class="department w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Department --</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept->id ?>"><?= htmlspecialchars($dept->name) ?></option>
        <?php endforeach; ?>
    </select>
</div>

            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('purchasing.pr.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Purchase Request List</h2>
    <div class="w-full overflow-x-auto" id="request-scroll-wrapper">
    <table id="request-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Request Number</th>
                    <th class="px-4 py-2">Order Type</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2 !text-center">Request Date</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2">Note</th>
                    <th class="px-4 py-2 !text-center">Created by</th>
                    <th class="px-4 py-2 !text-center">Created at</th>
                    <th class="px-4 py-2 !text-center">Approved by</th>
                    <th class="px-4 py-2 !text-center">Approved at</th>
                    <th class="px-4 py-2 !text-center">Authorized by</th>
                    <th class="px-4 py-2 !text-center">Authorized at</th>
                    <th class="px-4 py-2 !text-center">Verified by</th>
                    <th class="px-4 py-2 !text-center">Verified at</th>
                    <th class="px-4 py-2 !text-center">Updated at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-2xl transform transition-all scale-95">
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-red-100 text-red-600 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0-12.728l12.728 12.728" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Reject Purchase Request</h2>
        </div>

        <form id="rejectForm" class="space-y-4">
            @csrf
            <input type="hidden" name="reject_id" id="reject_id">

            <!-- Reason -->
            <div>
                <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Reason for Rejection
                </label>
                <textarea 
                    name="rejected_reason" 
                    id="rejected_reason" 
                    rows="4" 
                    required
                    placeholder="e.g. Budget exceeded, item not required, incorrect specification..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve future requests.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="closeRejectModal()"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition"
                >
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#request-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#request-table tbody tr:nth-child(odd) {
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
#request-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#request-table th, #request-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#request-table td, #request-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#request-scroll-wrapper {
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

    $('#filter-department').select2({
      placeholder: "-- All Department --",
      allowClear: true,
      width: '100%'
    });
     });
let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#request-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("purchasing.pr.data") }}',
           data: function (d) {
                d.request_number = $('#filter-request-number').val();
                d.request_date = $('#filter-date').val();
                d.status = $('#filter-status').val();
                d.department = $('#filter-department').val();
                d.order_type = $('#filter-order-type').val(); // nama artikel
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
                filename: 'Purchase_Request_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Purchase_Request_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
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
                title: 'Purchase_Request_ ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
            { data: 'request_number', name: 'request_number', orderable: false },
            { data: 'order_type', name: 'order_type', orderable: false },
            { data: 'department', name: 'department', orderable: false },    
            { data: 'request_date', name: 'request_date', className: 'text-center', orderable: false },
            { data: 'status', name: 'status', className: 'text-center', orderable: false },
            { data: 'pr_note', name: 'pr_note', orderable: false },
            { data: 'created_by', name: 'created_by', orderable: false },
            { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false  },
            { data: 'approved_by', name: 'approved_by', orderable: false },
            { data: 'approved_at', name: 'approved_at', className: 'text-center', orderable: false  },
            { data: 'authorized_by', name: 'authorized_by', orderable: false },
            { data: 'authorized_at', name: 'authorized_at', className: 'text-center', orderable: false  },
            { data: 'verified_by', name: 'verified_by', orderable: false },
            { data: 'verified_at', name: 'verified_at', className: 'text-center', orderable: false  },
            { data: 'updated_at', name: 'updated_at', className: 'text-center', orderable: false  },
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


function approvePR(id, requestNumber) {

    Swal.fire({
        title: 'Approve Purchase Request',
        html: `Approve this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Approved: ' + res.request_number);
                 $('#request-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui PR.');
            });
        }
    });
}

function verifiedPR(id, requestNumber) {

    Swal.fire({
        title: 'Verified Purchase Request',
        html: `Verified this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verified it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/verified`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Verified: ' + res.request_number);
                $('#request-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Purchase Request Verification Failed');
            });
        }
    });
}

function authorizedPR(id, requestNumber) {

    Swal.fire({
        title: 'Authorize Purchase Request',
        html: `Authorize this Purchase Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/purchasing/purchase-request/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Purchase Request has been Authorized: ' + res.request_number);
                $('#request-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan PR.');
            });
        }
    });
}

function rejectPR(id, requestNumber) {
    // Isi ID
    document.getElementById('reject_id').value = id;

    // Kalau mau tampilkan nomor PR di modal
    const prInfo = document.getElementById('pr_info');
    if (prInfo) {
        prInfo.textContent = requestNumber;
    }

    // Reset textarea
    document.getElementById('rejected_reason').value = '';

    // Show modal
    document.getElementById('rejectModal').classList.remove('hidden');
}


function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Submit form reject
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let id = document.getElementById('reject_id').value;
    let reason = document.getElementById('rejected_reason').value.trim();

    if (!reason) {
        showToast('error', 'Reject reason is required.');
        return;
    }

    $.post(`/purchasing/purchase-request/${id}/reject`, {
        _token: '{{ csrf_token() }}',
        rejected_reason: reason
    })
    .done(function(res) {
        showToast('success', 'Purchase Request rejected.');
        closeRejectModal();
        $('#request-table').DataTable().ajax.reload(null, false);
    })
    .fail(function() {
        showToast('error', 'Gagal reject PR.');
    });
});

</script>


@endpush


@endsection