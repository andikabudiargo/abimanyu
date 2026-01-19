@extends('layouts.app')

@section('title', 'Daily Inspection')
@section('page-title', 'DASHBOARD DAILY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Daily Inspection')

@section('content')
<!-- Modal Top Defect -->
<div id="defectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-96 rounded-lg shadow-lg p-5">
    <h2 class="text-lg font-bold text-gray-800 mb-2">
      Top Defect Hari Ini
    </h2>

    <p id="modal-pos" class="text-sm text-gray-600 mb-3"></p>

    <!-- Top 3 Defects -->
    <div>
      <h3 class="text-sm font-semibold text-gray-700 mb-1">Top 3 Defect</h3>
      <ul id="top-defect-list" class="text-sm text-gray-800 space-y-1">
        <li>Loading...</li>
      </ul>
    </div>

    <!-- Top 3 Parts -->
    <div class="mt-4">
      <h3 class="text-sm font-semibold text-gray-700 mb-1">Top 3 Part dengan NG Tertinggi</h3>
      <ul id="top-part-list" class="text-sm text-gray-800 space-y-1">
        <li>Loading...</li>
      </ul>
    </div>


    <button id="closeDefectModal" class="mt-5 w-full py-2 bg-gray-700 text-white rounded">
      Tutup
    </button>
  </div>
</div>

   <div class="bg-white shadow rounded-xl p-6 mb-4">
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
                <option value="Outgoing">Outgoing</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Inspection Date</label>
            <input id="filter-inspection_date" type="text" placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <!-- SUPPLIER / CUSTOMER COMBINED -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Supplier / Customer <span class="text-red-600">*</span>
  </label>

  <select name="supplier" id="filter-supplier" class="supplier w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Pilih Supplier / Customer --</option>

    <!-- GROUP SUPPLIER -->
    <optgroup label="SUPPLIER">
      @foreach ($suppliers as $supplier)
        <option value="{{ $supplier->code }}">
          {{ $supplier->name }}
        </option>
      @endforeach
    </optgroup>

    <!-- GROUP CUSTOMER -->
    <optgroup label="CUSTOMER">
      @foreach ($customers as $customer)
        <option value="{{ $customer->code }}">
          {{ $customer->name }}
        </option>
      @endforeach
    </optgroup>

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

<div class="grid grid-cols-2 gap-4 mb-4">
<div class="grid grid-cols-3 gap-4">

  <div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Incoming">
    <h3 class="text-xs font-semibold text-gray-500">Incoming</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Incoming">
      Cek Top 3 Defect
    </button>
</div>

<div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Unloading">
    <h3 class="text-xs font-semibold text-gray-500">Unloading</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Unloading">
      Cek Top 3 Defect
    </button>
</div>

 <div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Buffing">
    <h3 class="text-xs font-semibold text-gray-500">Buffing</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Buffing">
      Cek Top 3 Defect
    </button>
</div>


 <div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Touch Up">
    <h3 class="text-xs font-semibold text-gray-500">Touch Up</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Touch Up">
      Cek Top 3 Defect
    </button>
</div>


<div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Final">
    <h3 class="text-xs font-semibold text-gray-500">Final</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Final">
      Cek Top 3 Defect
    </button>
</div>


 <div class="p-4 bg-white shadow-md rounded-lg qc-card" data-pos="Outgoing">
    <h3 class="text-xs font-semibold text-gray-500">Outgoing</h3>
    <div class="qc-total text-2xl font-bold text-gray-800 mt-1">0 Part</div>
    <div class="qc-ok text-sm text-green-600">OK: 0</div>
    <div class="qc-ng text-sm text-red-600">NG: 0</div>

    <button class="mt-3 px-3 py-1 text-xs bg-blue-600 text-white rounded-md open-defect-modal" data-pos="Outgoing">
      Cek Top 3 Defect
    </button>
</div>

</div>
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
                    <th class="px-4 py-2">Supplier/Customer</th>
                    <th class="px-4 py-2 ">Part Name</th>
                    <th class="px-4 py-2 ">Inspection Method</th>
                    <th class="px-4 py-2 text-center">Total Check</th>
                    <th class="px-4 py-2 text-center">Total OK</th>
                    <th class="px-4 py-2 text-center">Total NG</th>
                    <th class="px-4 py-2 text-center">Pass Rate</th>
                    <th class="px-4 py-2 text-center">Pass Trough/Performance</th>
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
  { data: 'partner_name', className: 'text-left', orderable: false},
  { data: 'part_name', name: 'part_name', className: 'text-left', orderable: false },
  { data: 'check_method', name: 'check_method', className: 'text-center', orderable: false },
  { data: 'total_check', name: 'total_check', className: 'text-center', orderable: false },
  { data: 'total_ok', name: 'total_ok', className: 'text-center', orderable: false },
  { data: 'total_ng', name: 'total_ng', className: 'text-center', orderable: false },
  { data: 'pass_rate', name: 'pass_rate', className: 'text-center', orderable: false },
  { data: 'pass_trough', name: 'pass_trough', className: 'text-center', orderable: false },
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

 function loadSummary(date = null) {

        $.ajax({
            url: '/qc/inspection/summary',
            type: 'GET',
            data: { date: date },
            beforeSend: function() {
                // tampilkan animasi loading ringan
                $('.qc-card .qc-total').text('...');
            },
            success: function(res) {

    const summary = res.summary; // ambil object summary

    Object.keys(summary).forEach(function(pos) {

        const card = $('.qc-card[data-pos="' + pos + '"]');

        card.find('.qc-total').text(summary[pos].total + ' Part');
        card.find('.qc-ok').text('OK: ' + summary[pos].ok);
        card.find('.qc-ng').text('NG: ' + summary[pos].ng);

    });
},
            error: function() {
                alert('Gagal mengambil data summary');
            }
        });

    }

    // load awal tanpa filter
    loadSummary();

    // jika ada input tanggal
    $('#filter-date').on('change', function() {
        loadSummary($(this).val());
    });

    $('.open-defect-modal').on('click', function () {
    const pos = $(this).data('pos');

    $.ajax({
        url: '/qc/inspection/top-defect',
        type: 'GET',
        data: { pos: pos },
        beforeSend: function() {
            $('#top-defect-list').html('<li>Loading...</li>');
            $('#top-part-list').html('<li>Loading...</li>');
        },
        success: function(res) {
            $('#modal-pos').text('Pos: ' + pos);

            let defectHtml = '';
            let partHtml = '';

            res.top_defect.forEach(d => {
                defectHtml += `<li>${d.defect_name} (${d.total_qty})</li>`;
            });

            res.top_part.forEach(p => {
                partHtml += `<li>${p.part_name} (${p.total_qty})</li>`;
            });

            $('#top-defect-list').html(defectHtml);
            $('#top-part-list').html(partHtml);

            // tampilkan modal
            $('#defectModal').removeClass('hidden').addClass('flex');
        }
    });
});

$('#closeDefectModal').on('click', function () {
    $('#defectModal').removeClass('flex').addClass('hidden');
});

  </script>

@endpush


@endsection