@extends('layouts.app')

@section('title', 'Master Defect')
@section('page-title', 'DASHBOARD DEFECT')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Master Defect')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Defect</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Defect</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Category</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Inspection Post</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Used Raw Material</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
        </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <button type="button" id="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</button>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Defect List</h2>
    <div class="w-full overflow-x-auto">
        <table id="defect-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Inspection Post</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Defect</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Raw Material</th>
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

<!-- 🌟 MODAL CREATE WAREHOUSE -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Defect</h3>

    <form id="createDefectForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Defect</label>
          <input type="text" name="defect" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300" required>
        </div>
         <div class="flex items-center h-full pt-6">
        <input type="checkbox" name="raw_material" value="0" class="form-checkbox text-indigo-600 mr-2">
        <label for="raw_material" class="text-sm text-gray-700">Using Raw Material?</label>
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <input type="text" name="description" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4 mb-2">
         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Post</label>
           <select name="inspection_post" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="">-- Choose Post --</option>
            <option value="Incoming">Incoming</option>
            <option value="Unloading">Unloading</option>
            <option value="Buffing">Buffing</option>
            <option value="Touch Up">Touch Up</option>
            <option value="Final">Final</option>
          </select>
        </div>
         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
           <select name="category" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="">-- Choose Category --</option>
            <option value="NG">No Good (NG)</option>
            <option value="NC">Non-Conformance (NC)</option>
            <option value="Both">Both</option>
          </select>
        </div>
        
      </div>

      <div class="flex justify-end gap-2 mt-6">
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
#defect-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#defect-table tbody tr:nth-child(odd) {
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
#defect-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#defect-table th, #defect-table td {
    border: none !important;
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
  document.getElementById('createDefectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

   fetch('{{ route("qc.defect.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(res => res.json())
    .then(data => {
      alert('Defect berhasil dibuat!');
      createModal.classList.add('hidden');
      this.reset();
      // TODO: Refresh DataTable jika ada
    })
    .catch(err => {
      console.error(err);
      alert('Gagal menyimpan data defect.');
    });
  });

  $(document).ready(function () {
    const table = $('#defect-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
    },
      ajax: '{{ route("qc.defect.data") }}',
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
        { data: 'inspection_post', name: 'inspection_post' },
        { data: 'category', name: 'category' },
        { data: 'defect', name: 'defect' },
        { data: 'description', name: 'description' },
        { data: 'raw_material', name: 'raw_material' },
        { data: 'status', name: 'status' },
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