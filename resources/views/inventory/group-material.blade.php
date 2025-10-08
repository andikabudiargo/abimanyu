@extends('layouts.app')

@section('title', 'Group of Material')
@section('page-title', 'DASHBOARD GROUP OF MATERIAL')
@section('breadcrumb-item', 'Inventory')
@section('breadcrumb-active', 'Group of Material')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Group of Material</h2>

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
        </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <button type="button" id="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</button>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Group of Material List</h2>
    <div class="w-full overflow-x-auto">
        <table id="gom-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Group Name</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Updated at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

<div id="createModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Group of Material</h3>

    <form id="createGroupForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
          <input type="text" name="code" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea name="description" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
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
    <h3 class="text-lg font-semibold mb-4">Edit Group of Material</h3>

    <form id="editTypeForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
          <input type="text" name="code" value="" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300" disabled>
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea name="description" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
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
#gom-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#gom-table tbody tr:nth-child(odd) {
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
#gom-table td,
#gom-table th {
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
#gom-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#gom-table th, #gom-table td {
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
  let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#gom-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("inventory.gom.data") }}',
             data: function (d) {
                d.code = $('#filter-code').val();
                d.capacity = $('#filter-capacity').val();
                d.location = $('#filter-location').val();
                d.equipment = $('#filter-equipment').val();
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
                filename: 'List_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'List_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
            },
            {
                extend: 'print',
                title: 'List Ruangan ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
            { data: 'department', name: 'department' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'updated_at', name: 'updated_at' },
        ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
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


$(document).ready(function () {
  $('#createGroupForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: "{{ route('inventory.gom.store') }}",
      type: "POST",
      data: $(this).serialize(),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
          });

          // reset form
          $('#createGroupForm')[0].reset();

          // tutup modal
          $('#createModal').addClass('hidden');

          // refresh tabel (jika pakai DataTables)
          if (typeof table !== 'undefined') {
            table.ajax.reload();
          }
        }
      },
      error: function (xhr) {
        let res = xhr.responseJSON;
        if (res && res.errors) {
          let msg = '';
          $.each(res.errors, function (key, value) {
            msg += value + "\n";
          });

          Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: msg
          });

        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Terjadi kesalahan saat menyimpan data.'
          });
        }
      }
    });
  });
});

</script>

@endpush



@endsection