@extends('layouts.app')

@section('title', 'Category Management')
@section('page-title', 'DASHBOARD CATEGORY MANAGEMENT')
@section('breadcrumb-item', 'Service Management')
@section('breadcrumb-active', 'Category Management')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Category</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
           <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Description</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <button type="button" onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
    Create
</button>

        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Category List</h2>
    <div class="w-full overflow-x-auto" id="category-scroll-wrapper">
    <table id="category-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Responsible</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Created at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

<!-- 🔲 Modal Form -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <h2 class="text-xl font-semibold mb-4">Create New Category</h2>
        <form id="create-category-form">
           @csrf
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" id="code" placeholder="Automatic" class="w-full border border-gray-300 bg-gray-200 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" readonly>
            </div>

           <div class="mb-4">
    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
    <select id="department_id" name="department_id" class="w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- Select Department --</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Staff Responsible</label>
    <select id="user_id" name="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- Select Staff --</option>
    </select>
</div>


            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
            </div>
        </form>

        <!-- ❌ Close button corner -->
        <button onclick="closeModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#category-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#category-table tbody tr:nth-child(odd) {
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
#category-table td,
#category-table th {
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
#category-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#category-table th, #category-table td {
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
    function openModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('categoryModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('flex');
    }

    document.getElementById('department_id').addEventListener('change', function () {
    const departmentId = this.value;
    const userSelect = document.getElementById('user_id');
    userSelect.innerHTML = '<option value="">Loading...</option>';

    fetch(`/it/departments/${departmentId}/users`)
        .then(response => response.json())
        .then(data => {
            userSelect.innerHTML = '<option value="">-- Select Staff --</option>';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                userSelect.appendChild(option);
            });
        })
        .catch(() => {
            userSelect.innerHTML = '<option value="">Error loading users</option>';
        });
});

$(document).ready(function () {
    // Ganti user saat departemen berubah
    $('#department_id').on('change', function () {
        const deptId = $(this).val();

        if (deptId) {
            $.get(`/it/departments/${deptId}/users`, function (data) {
                const $user = $('#user_id');
                $user.empty().append('<option value="">-- Pilih User --</option>');
                $.each(data, function (i, user) {
                    $user.append(`<option value="${user.id}">${user.name}</option>`);
                });
            });
        }
    });

   $('#create-category-form').off('submit').on('submit', function (e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: "{{ route('it.category.store') }}",
        method: 'POST',
        data: formData,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Category succesfully saved.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload(); // reload halaman setelah popup selesai
            });
        },
        error: function (xhr) {
            let errorMsg = 'Terjadi kesalahan';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join(', ');
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMsg,
            });
        }
    });
});

});
let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#category-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("it.category.data") }}',
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
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
            { data: 'code', name: 'code' },
            { data: 'department.name', name: 'department.name' },
            { data: 'user.name', name: 'user.name' },
            { data: 'description', name: 'description' },
            { data: 'created_at', name: 'created_at' },
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
  const dropdown = document.getElementById(id);

  // Tutup dropdown lain
  if (openDropdown && openDropdown !== dropdown) {
    openDropdown.classList.add('hidden');
  }

  if (dropdown.classList.contains('hidden')) {
    // Hitung posisi tombol
    const rect = event.currentTarget.getBoundingClientRect();
    const dropdownHeight = dropdown.offsetHeight;
    const windowHeight = window.innerHeight;

    // Tentukan apakah dropdown muncul di atas atau bawah
    const showAbove = rect.bottom + dropdownHeight > windowHeight;

    dropdown.style.position = 'fixed';
    dropdown.style.left = `${rect.left}px`;
    dropdown.style.top = showAbove ? `${rect.top - dropdownHeight}px` : `${rect.bottom}px`;
    dropdown.classList.remove('hidden');

    openDropdown = dropdown;
  } else {
    dropdown.classList.add('hidden');
    openDropdown = null;
  }
}

// Tutup saat klik di luar
document.addEventListener('click', function (e) {
  if (openDropdown && !openDropdown.contains(e.target)) {
    const isTrigger = e.target.closest('button[data-dropdown-id]');
    if (!isTrigger) {
      openDropdown.classList.add('hidden');
      openDropdown = null;
    }
  }
});
</script>
@endpush
@endsection