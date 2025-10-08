@extends('layouts.app')

@section('title', 'Article Type')
@section('page-title', 'DASHBOARD ARTICLE TYPE')
@section('breadcrumb-item', 'Inventory')
@section('breadcrumb-active', 'Article Type')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Article Type</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Type</label>
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
    <h2 class="text-lg font-semibold mb-2">Article Type List</h2>
    <div class="w-full overflow-x-auto">
        <table id="type-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Article Type</th>
                    <th class="px-4 py-2">Location</th>
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

<!-- 🌟 MODAL CREATE WAREHOUSE -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Article Type</h3>

    <form id="createTypeForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">Code</label>
          <input type="text" name="code" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
       <div>
  <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
  <select name="warehouse_id" id="warehouse_id"
          class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
    <option value="">-- Choose Warehouse --</option>
  </select>
</div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Article Type</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Note</label>
          <textarea name="note" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button type="button" id="closeCreateModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
      </div>
    </form>

    <!-- ❌ Tombol Close di pojok -->
    <button id="closeCreateModalIcon" class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl">&times;</button>
  </div>
</div>

<!-- 🌟 MODAL EDIT WAREHOUSE -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Edit Article Type</h3>

    <form id="editTypeForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">Code</label>
          <input type="text" name="code" value="" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300" disabled>
        </div>
       <div>
  <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
  <select name="warehouse_id" id="warehouse_id_edit"
          class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
    <option value="">-- Choose Warehouse --</option>
  </select>
</div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Article Type</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Note</label>
          <textarea name="note" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button type="button" id="closeEditModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Update</button>
      </div>
    </form>

    <!-- ❌ Tombol Close di pojok -->
    <button id="closeEditModalIcon" class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl">&times;</button>
  </div>
</div>
{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#article-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#article-table tbody tr:nth-child(odd) {
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
#article-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#article-table th, #article-table td {
    border: none !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ==================== 🔄 GLOBAL VARIABEL ====================
  const createModal = document.getElementById('createModal');
  const editModal   = document.getElementById('editModal');
  const openCreateBtn = document.getElementById('openCreateModal');

  // ==================== 🔲 MODAL HANDLING ====================

  function openModal(modal) {
    if (modal) modal.classList.remove('hidden');
  }

  function closeModal(modal) {
    if (modal) modal.classList.add('hidden');
  }

  // 🔲 Event: Buka Modal Create
  if (openCreateBtn && createModal) {
    openCreateBtn.addEventListener('click', () => openModal(createModal));
  }

  // 🔲 Event: Tutup Modal Create
  ['closeCreateModalBtn', 'closeCreateModalIcon'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => closeModal(createModal));
  });

  // 🔲 Event: Tutup Modal Edit
  ['closeEditModalBtn', 'closeEditModalIcon'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', () => closeModal(editModal));
  });

  // ==================== 📥 FORM SUBMIT ====================

  // 📥 Submit CREATE
  const createForm = document.getElementById('createTypeForm');
  if (createForm) {
    createForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('{{ route("inventory.article-type.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert('✅ Article Type berhasil dibuat!');
        closeModal(createModal);
        this.reset();
        $('#type-table').DataTable().ajax.reload();
      })
      .catch(err => {
        console.error(err);
        alert('❌ Gagal menyimpan data Article Type.');
      });
    });
  }

  // 📥 Submit EDIT
  const editForm = document.getElementById('editTypeForm');
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const id = this.querySelector('input[name="id"]').value;
      const formData = new FormData(this);

      fetch(`/inventory/article-type/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-HTTP-Method-Override': 'PUT'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert('✅ Data berhasil diupdate!');
        closeModal(editModal);
        $('#type-table').DataTable().ajax.reload();
      })
      .catch(err => {
        console.error(err);
        alert('❌ Gagal update data!');
      });
    });
  }

  // ==================== 🗑 DELETE ====================
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.deleteTypeBtn');
    if (btn) {
      const id = btn.getAttribute('data-id');
      if (confirm('Yakin ingin menghapus data ini?')) {
        fetch(`/inventory/article-type/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
          alert('✅ ' + data.message);
          $('#type-table').DataTable().ajax.reload();
        })
        .catch(err => {
          console.error(err);
          alert('❌ Gagal menghapus data.');
        });
      }
    }
  });

  // ==================== ✏ EDIT DATA LOAD ====================
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.editTypeBtn');
    if (btn) {
      const id = btn.getAttribute('data-id');

      fetch(`/inventory/article-type/${id}`)
        .then(res => res.json())
        .then(data => {
          const modal = editModal;
          modal.querySelector('input[name="code"]').value = data.code;
          modal.querySelector('input[name="name"]').value = data.name;
          modal.querySelector('textarea[name="note"]').value = data.note;
          modal.querySelector('select[name="warehouse_id"]').value = data.warehouse_id;

          let hiddenId = modal.querySelector('input[name="id"]');
          if (!hiddenId) {
            hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = 'id';
            modal.querySelector('form').appendChild(hiddenId);
          }
          hiddenId.value = data.id;

          openModal(modal);
        })
        .catch(err => {
          console.error(err);
          alert('❌ Gagal mengambil data untuk diedit!');
        });
    }
  });

  // ==================== 🔽 DROPDOWN HANDLING ====================
  let openDropdown = null;
  window.toggleDropdown = function (id) {
    const dropdown = document.getElementById(id);
    if (openDropdown && openDropdown !== dropdown) {
      openDropdown.classList.add('hidden');
    }
    dropdown.classList.toggle('hidden');
    openDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
  };

  document.addEventListener('click', function (e) {
    if (openDropdown && !openDropdown.contains(e.target)) {
      const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
      if (!isTrigger) {
        openDropdown.classList.add('hidden');
        openDropdown = null;
      }
    }
  });

  // ==================== 📊 DATATABLE ====================
  $('#type-table').DataTable({
    processing: true,
    serverSide: true,
    autoWidth: false,
    ajax: '{{ route("inventory.article-type.data") }}',
    drawCallback: () => feather.replace(),
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
    buttons: [
      {
        extend: 'collection',
        text: 'Export',
        className: 'bg-blue-600 text-white px-4 py-1 rounded shadow-sm',
        buttons: [
          { extend: 'copyHtml5', text: 'Copy', exportOptions: { columns: ':visible' } },
          { extend: 'excelHtml5', text: 'Excel', exportOptions: { columns: ':visible' } },
          { extend: 'pdfHtml5', text: 'PDF', exportOptions: { columns: ':visible' } },
          { extend: 'print', text: 'Print', exportOptions: { columns: ':visible' } }
        ]
      }
    ],
    columns: [
      { data: 'action', name: 'action', orderable: false, searchable: false },
      { data: 'code', name: 'code' },
      { data: 'name', name: 'name' },
      { data: 'warehouse_id', name: 'warehouse_id' },
      { data: 'note', name: 'note' },
      { data: 'created_at', name: 'created_at' },
      { data: 'created_by', name: 'created_by' },
    ]
  });

  // ==================== 🏢 LOAD WAREHOUSE SELECT ====================
  fetch('{{ route("ppic.warehouse.list") }}')
    .then(res => res.json())
    .then(data => {
      const selectCreate = document.getElementById('warehouse_id');
      const selectEdit   = document.getElementById('warehouse_id_edit');

      data.forEach(wh => {
        const opt1 = new Option(wh.name, wh.id);
        const opt2 = new Option(wh.name, wh.id);
        selectCreate.appendChild(opt1);
        selectEdit.appendChild(opt2);
      });
    })
    .catch(err => console.error('❌ Gagal memuat data warehouse:', err));

});
</script>

@endpush



@endsection