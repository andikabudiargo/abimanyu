@extends('layouts.app')

@section('title', 'Role Management')
@section('page-title', 'ROLE MANAGEMENT')
@section('breadcrumb-item', 'Role Management')
@section('breadcrumb-active', 'Role Management')

@section('content')

{{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Role List</h2>
        <button id="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow text-sm">
            + Add New Role
</button>
    </div>
    <hr>
    <div class="w-full overflow-x-auto" id="role-scroll-wrapper">
    <table id="role-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Name</th>
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

<!-- 🌟 MODAL CREATE WAREHOUSE -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Role</h3>

    <form id="createRoleForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300" required>
        </div>

      <div class="flex justify-start gap-2 mt-6">
        <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
      </div>
    </form>

    <!-- ❌ Tombol Close di pojok -->
    <button id="closeModalIcon" class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl">&times;</button>
  </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#role-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#role-table tbody tr:nth-child(odd) {
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
#role-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#role-table th, #role-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#role-table td, #role-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#role-scroll-wrapper {
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
  document.getElementById('createRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

   fetch('{{ route("setting.role.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(res => res.json())
    .then(data => {
      alert('Role berhasil dibuat!');
      createModal.classList.add('hidden');
      this.reset();
      // TODO: Refresh DataTable jika ada
    })
    .catch(err => {
      console.error(err);
      alert('Gagal menyimpan data role.');
    });
  });

  $(document).ready(function () {
    const table = $('#role-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
    },
      ajax: '{{ route("setting.role.data") }}',
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