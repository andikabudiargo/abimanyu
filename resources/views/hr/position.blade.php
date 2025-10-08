@extends('layouts.app')

@section('title', 'Job Position')
@section('page-title', 'JOB POSITION DASHBOARD')
@section('breadcrumb-item', 'Job Position')
@section('breadcrumb-active', 'Job Position')

@section('content')
   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Job Position</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Position Name</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Availability</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Job Level</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
        </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('hr.position.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Department List</h2>
    <div class="w-full overflow-x-auto" id="dept-scroll-wrapper">
    <table id="dept-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Job Level</th>
                    <th class="px-4 py-2">Availability</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Created at</th>
                    <th class="px-4 py-2">Updated at</th>
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
#dept-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#dept-table tbody tr:nth-child(odd) {
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
#dept-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#dept-table th, #dept-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#dept-table td, #dept-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#dept-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}

</style>

<script>
  const openModalBtn = document.getElementById('openCreateModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const closeModalIcon = document.getElementById('closeModalIcon');
  const createModal = document.getElementById('createModal');

  openModalBtn.addEventListener('click', () => {
    createModal.classList.remove('hidden');
  });

  [closeModalBtn, closeModalIcon].forEach(btn => {
    btn.addEventListener('click', () => {
      createModal.classList.add('hidden');
    });
  });

  // Optional: Submit form pakai AJAX
  document.getElementById('createDeptForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

   fetch('{{ route("hr.department.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(res => res.json())
    .then(data => {
      alert('Department berhasil dibuat!');
      createModal.classList.add('hidden');
      this.reset();
      // TODO: Refresh DataTable jika ada
    })
    .catch(err => {
      console.error(err);
      alert('Gagal menyimpan data warehouse.');
    });
  });

  $(document).ready(function () {
    const table = $('#dept-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
    },
      ajax: '{{ route("hr.department.data") }}',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
        buttons: [
            {
                extend: 'collection',
                text: 'Export',
                className: 'bg-blue-600 text-white px-4 py-1 rounded shadow-sm',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: { columns: ':visible' }
                    }
                ]
            }
        ],
      columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'code', name: 'code' },
        { data: 'type', name: 'type' },
        { data: 'initial', name: 'initial' },
        { data: 'name', name: 'name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'updated_at', name: 'updated_at' },
      ]
    });
  });
  let openDropdown = null;

function toggleDropdown(id) {
  const dropdown = document.getElementById(id);

  // Tutup dropdown lain
  if (openDropdown && openDropdown !== dropdown) {
    openDropdown.classList.add('hidden');
  }

  dropdown.classList.toggle('hidden');
  openDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function (e) {
  if (openDropdown && !openDropdown.contains(e.target)) {
    const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
    if (!isTrigger) {
      openDropdown.classList.add('hidden');
      openDropdown = null;
    }
  }
  });
</script>
@endpush


@endsection