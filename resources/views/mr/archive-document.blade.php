@extends('layouts.app')

@section('title', 'Document Control')
@section('page-title', 'DASHBOARD DOCUMENT CONTROL')
@section('breadcrumb-item', 'Management Representative')
@section('breadcrumb-active', 'Document Control')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Document Archive</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Document Number</label>
                <input type="text" id="filter-document-number" class="w-full px-3 py-1 text-lg border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Document Type</label>
                   <select id="filter-type" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Document Type --</option>
        <option value="Standard">Standard</option>
        <option value="Work Instructions">Work Instructions</option>
        <option value="SOP">SOP</option>
        <option value="Form">Form</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
             <div>
                <label for="filter-order-date" class="block text-sm mb-1 font-medium text-gray-700">Submision Date</label>
                 <input type="text" name="date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                 <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                  <select id="filter-department" class=" w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
         <option value="">-- All --</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
        @endforeach
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Remark</label>
                 <select id="filter-remark" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Remark --</option>
        <option value="New Release">New Release</option>
        <option value="Revision">Revision</option>
        <option value="Obsolete">Obsolete</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>

            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Draft">Draft</option>
        <option value="Approved">Approved</option>
        <option value="Under Review">Under Review</option>
        <option value="Resubmitted">Resubmitted</option>
        <option value="Published">Published</option>
        <option value="Partially Socialized">Partially Socialized</option>
        <option value="Revision">Revision</option>
        <option value="Closed">Closed</option>
        <option value="Obsolete">Obsolete</option>
        <option value="Rejected">Rejected</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('mr.doc.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Document Archive</h2>
    <div class="w-full overflow-x-auto" id="doc-scroll-wrapper">
    <table id="doc-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Document Number</th>
                    <th class="px-4 py-2">Version No.</th>
                    <th class="px-4 py-2">Document Type</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2 !text-center">Remark</th>
                    <th class="px-4 py-2 ">Document Title</th>
                    <th class="px-4 py-2 !text-center !w-28">Status</th>
                    <th class="px-4 py-2">Unduh File</th>
                    <th class="px-4 py-2 ">Created by</th>
                    <th class="px-4 py-2 ">Created at</th>
                    <th class="px-4 py-2 ">Approved by</th>
                    <th class="px-4 py-2 ">Approved at</th>
                    <th class="px-4 py-2 ">Authorized by</th>
                    <th class="px-4 py-2 ">Authorized at</th>
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
               <i data-feather="alert-triangle"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Reject Document</h2>
        </div>

        <form id="rejectForm" class="space-y-4">
            @csrf
             <input type="hidden" name="document_id" id="reject_document_id">
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
                   placeholder="e.g. Duplicate request, issue already resolved, invalid request details..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve future requests.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="closeRejectModal()"
                    class="px-4 py-2 rounded-lg bg-gray-300 border border-gray-300 text-white hover:bg-gray-400 transition"
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

<!-- Modal -->
<!-- Modal Resubmit -->
<div id="resubmitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4" id="resubmitTitle">Resubmit Document</h2>

        <form id="resubmitForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Upload File -->
            <div class="mb-4">
                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload New File
                </label>
                <input type="file" name="file" id="document_file"
                       class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
                       required>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelResubmit"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<div id="obsoleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4" id="obsoleteTitle">Obsolete Document</h2>

        <form id="obsoleteForm" enctype="multipart/form-data">
            @csrf
             <input type="hidden" name="document_id" id="obsolete_document_id">
            <!-- Reason -->
            <div>
                <label for="obsolete_reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Reason for Obsolete
                </label>
                <textarea 
                    name="obsolete_reason" 
                    id="obsolete_reason" 
                    rows="4" 
                    required
                   placeholder="e.g. Duplicate request, issue already resolved, invalid request details..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve future requests.</p>
            </div>

           
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeObsolete()"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<div id="socializeModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 " id="modalContent">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4 border-b pb-2">
      <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
        <i data-feather="calendar"></i>
        Socialize Document
      </h2>
      <button type="button" onclick="closeModal()" class="text-gray-500 hover:text-gray-700 transition">
        ✕
      </button>
    </div>

    <!-- Form -->
    <form id="socializeForm" class="space-y-4">
         @csrf
      <input type="hidden" name="document_id" id="document_id">

      <!-- Container untuk data department & qty -->
      <div id="docCopiesContainer" class="space-y-3">
        <!-- Data akan di-render di sini -->
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-3 pt-4 border-t">
        <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">Cancel</button>
        <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Save</button>
      </div>
    </form>
  </div>
</div>
{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#doc-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#doc-table tbody tr:nth-child(odd) {
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
#doc-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#doc-table th, #doc-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#doc-table td, #doc-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#doc-scroll-wrapper {
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

    $('#filter-status').select2({
      placeholder: "-- All Status --",
      allowClear: true,
      width: '100%'
    });

    // Select2 for supplier and article
    $('#filter-type').select2({
      placeholder: "-- All Document Type --",
      allowClear: true,
      width: '100%'
    });

     $('#filter-remark').select2({
      placeholder: "-- All Remark --",
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
    const table = $('#doc-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("mr.doc.data") }}',
            
            data: function (d) {
                d.document_number = $('#filter-document-number').val();
                d.document_type = $('#filter-type').val();
                d.status = $('#filter-status').val();
                d.remark = $('#filter-remark').val();
                d.department = $('#filter-department').val();
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
                filename: 'Document Archive_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Document Archive_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Document Archive' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
  { data: 'document_number', name: 'document_number', orderable: false },
  { data: 'current_version', name: 'current_version', className: 'text-center', orderable: false, searchable: false },
  { data: 'document_type', name: 'document_type', orderable: false, searchable: false },
  { data: 'department', name: 'department', orderable: false, searchable: false },
  { data: 'remark', name: 'remark',  className: 'text-center', orderable: false, searchable: false },
  { data: 'title', name: 'title', orderable: false },
  { data: 'status', name: 'status', className: '!text-center !w-28', orderable: false, searchable: false },
  { data: 'file', name: 'file', orderable: false,searchable: false },
  { data: 'created_by', name: 'created_by', orderable: false, searchable: false },
  { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
  { data: 'approved_by', name: 'approved_by', orderable: false, searchable: false },
  { data: 'approved_at', name: 'approved_at', orderable: false, searchable: false },
  { data: 'authorized_by', name: 'authorized_by', orderable: false, searchable: false },
  { data: 'authorized_at', name: 'authorized_at', orderable: false, searchable: false },
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

function rejectDOC(docId) {
    $('#reject_document_id').val(docId);
    $('#rejectModal').removeClass('hidden');
    setTimeout(() => {
        $('#rejectModal .modal-content').removeClass('scale-95 opacity-0');
    }, 10);
}

function closeRejectModal() {
    $('#rejectModal .modal-content').addClass('scale-95 opacity-0');
    setTimeout(() => {
        $('#rejectModal').addClass('hidden');
    }, 200);
}

$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let docId = $('#reject_document_id').val();
    let data = form.serialize();

    $.post(`/mr/document/${docId}/reject`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
            $('#doc-table').DataTable().ajax.reload(null, false);
            closeRejectModal();
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
    });
});

function obsoleteDOC(docId, docNumber) {
    $('#obsolete_document_id').val(docId);
    $('#obsoleteModal').removeClass('hidden');
    setTimeout(() => {
        $('#obsoleteModal .modal-content').removeClass('scale-95 opacity-0');
    }, 10);
}

function closeObsolete() {
    $('#obsoleteModal .modal-content').addClass('scale-95 opacity-0');
    setTimeout(() => {
        $('#obsoleteModal').addClass('hidden');
    }, 200);
}

$('#obsoleteForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let docId = $('#obsolete_document_id').val();
    console.log('docId:', docId);
    let data = form.serialize();

    $.post(`/mr/document/${docId}/obsolete`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
            $('#doc-table').DataTable().ajax.reload(null, false);
            closeObsolete();
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
    });
});

function approveDOC(id, docNumber) {

    Swal.fire({
        title: 'Approve Document?',
        html: `Approve this Document: <strong>${docNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document has been Approved: ' + res.document_number);
                 $('#doc-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui document.');
            });
        }
    });
}

function reviewDOC(id, docNumber) {

    Swal.fire({
        title: 'Review Document?',
        html: `Review this Document: <strong>${docNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Review it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/review`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document has been Review: ' + res.document_number);
                 $('#doc-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat review document.');
            });
        }
    });
}

function authorizedDOC(id, docNumber) {

    Swal.fire({
        title: 'Authorize Document',
        html: `Authorize this Document: <strong>${docNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorized it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document has been Authorized and the status is Published: ' + res.document_number);
                 $('#doc-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat mengesahkan Document.');
            });
        }
    });
}
function resubmitDOC(id, docNumber) {
    // Bisa tampilkan nomor dokumen di judul modal
    document.querySelector('#resubmitModal h2').innerText = 
        `Resubmit Document (${docNumber})`;

    // Tampilkan modal
    document.getElementById('resubmitModal').classList.remove('hidden');
}

function closeResubmitModal() {
    document.getElementById('resubmitModal').classList.add('hidden');
}
 // Animasi ketika modal dibuka
  function openModal() {
    document.getElementById('socializeModal').classList.remove('hidden');
    setTimeout(() => {
      document.getElementById('modalContent').classList.remove('scale-95', 'opacity-0');
      document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  // Animasi ketika modal ditutup
  function closeModal() {
    const modalContent = document.getElementById('modalContent');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
      document.getElementById('socializeModal').classList.add('hidden');
    }, 200);
  }

// Open modal dan ambil data dari backend
function updateDOC(documentId) {
    $('#document_id').val(documentId);
    $('#docCopiesContainer').html('<div class="text-center text-gray-500">Loading...</div>');

    $.get('/mr/document/copies/' + documentId, function (data) {
        if (!data || data.length === 0) {
            $('#docCopiesContainer').html('<p class="text-gray-500">No department copies found.</p>');
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `
                <div class="p-3 border rounded-lg bg-gray-50 mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-700">${item.department_name}</span>
                        <span class="text-sm text-gray-500">Qty: ${item.qty}</span>
                    </div>
                    <input type="date" name="dates[${item.id}]" value="${item.date ? item.date : ''}" 
                        class="border rounded-lg px-3 py-1 text-sm w-full focus:outline-none focus:ring-2 focus:ring-green-400" />
                </div>
            `;
        });

        $('#docCopiesContainer').html(html);
        openModal(); // gunakan animasi modal yang sebelumnya kita buat
    }).fail(() => {
        $('#docCopiesContainer').html('<p class="text-red-500">Failed to load data.</p>');
    });
}

// Close modal
function closeModal() {
    const modalContent = $('#modalContent');
    modalContent.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
    setTimeout(() => {
        $('#socializeModal').addClass('hidden');
    }, 200);
}

// Submit form untuk menyimpan tanggal socialize
$('#socializeForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.post('/mr/document/save-socialize', formData, function (res) {
        if (res.success) {
            showToast('success', 'Socialization dates saved successfully!');
            closeModal();
            $('#doc-table').DataTable().ajax.reload(null, false); // reload datatable tanpa reset page
        } else {
            showToast('error', res.message || 'Failed to save socialize dates.');
        }
    }).fail(() => {
        showToast('error', 'Server error while saving dates.');
    });
});

let currentDocId = null;

// buka modal
function resubmitDOC(id, docNumber) {
    currentDocId = id;
    $("#resubmitTitle").text(`Resubmit Document (${docNumber})`);
    $("#resubmitModal").removeClass("hidden");
}

// tutup modal
$("#cancelResubmit").on("click", function () {
    $("#resubmitModal").addClass("hidden");
    $("#resubmitForm")[0].reset();
});

// submit form via AJAX
$("#resubmitForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

     $.ajax({
        url: `/mr/document/${currentDocId}/resubmit`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $("#resubmitModal").addClass("hidden");
                showToast('success', 'Document has been Resubmit: ' + res.document_number);
                 $('#doc-table').DataTable().ajax.reload(null, false); // reload datatable tanpa reset page
            } else {
                 showToast('error', res.message || 'Failed to save socialize dates.');
            }
        }
    });
});

 function confirmDelete(id, docNumber) {
    Swal.fire({
         title: "Are you sure you want to delete " + docNumber +"?",
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
                url: `/mr/document/${id}/destroy`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.fire('Deleted!', res.doc_number, 'success');
                    $('#doc-table').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal!', 'Delete Document Failed.', 'error');
                }
            });
        }
    });
}
  </script>

@endpush


@endsection