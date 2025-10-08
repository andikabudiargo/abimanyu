@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'USERS DASHBOARD')
@section('breadcrumb-item', 'Users')
@section('breadcrumb-active', 'Users')

@section('content')
   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Users</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Username</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('setting.user.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">User List</h2>
    <div class="bg-white rounded-xl">
    <table id="user-table" class="w-max text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2 min-w-[250px]">User</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Roles</th>
                    <th class="px-4 py-2">Last Login</th>
                    <th class="px-4 py-2">Last IP Address</th>
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
#user-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#user-table tbody tr:nth-child(odd) {
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
#user-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#user-table th, #user-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#user-table td, #user-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#user-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}

</style>

<script>
  $(document).ready(function () {
    const table = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
   // Tambahkan scroll wrapper hanya jika belum ada
    if (!$('#user-table').parent().hasClass('scroll-wrapper')) {
        $('#user-table').wrap('<div class="scroll-wrapper overflow-x-auto"></div>');
    }
},
      ajax: '{{ route("setting.user.data") }}',
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
        { data: 'user_info', name: 'name' },
        { data: 'departments', name: 'departments' },
        { data: 'email', name: 'email' },
        { data: 'status', name: 'status' },
        { data: 'roles', name: 'roles' },
        { data: 'last_login', name: 'last_login' },
        { data: 'last_ip', name: 'last_ip' },
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

  $(document).on('change', '.status-toggle', function () {
    const $checkbox = $(this);
    const userId = $checkbox.data('id');
    const $label = $checkbox.closest('div').siblings('.status-label');

    $.ajax({
        url: '{{ route("setting.user.toggleStatus") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: userId
        },
        success: function (res) {
            if (res.success) {
                if (res.status) {
                    $label.text('Active').removeClass('text-red-500').addClass('text-green-600');
                } else {
                    $label.text('Inactive').removeClass('text-green-600').addClass('text-red-500');
                }
               // Refresh data table
                $('#user-table').DataTable().ajax.reload(null, false);
            } else {
                alert('Gagal toggle status!');
            }
        },
         error: function (xhr) {
            console.error(xhr.responseText);
            alert('Terjadi kesalahan server.');
        }
    });
});

</script>
@endpush


@endsection