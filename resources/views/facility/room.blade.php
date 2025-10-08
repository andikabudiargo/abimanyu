@extends('layouts.app')

@section('title', 'Room Management')
@section('page-title', 'DASHBOARD ROOM MANAGEMENT')
@section('breadcrumb-item', 'Meeting Room')
@section('breadcrumb-active', 'Room Management')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Room</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
           <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Code</label>
                <input type="text" id="filter-code" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Capacity</label>
                <input type="number" id="filter-capacity" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Location</label>
                <select id="filter-location" class="select2 w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All Location --</option>
        <option value="Plant 1, 1st Floor">Plant 1, 1st Floor</option>
        <option value="Plant 1, 2nd Floor">Plant 1, 2nd Floor</option>
        <option value="Plant 2, 1st Floor">Plant 2, 1st Floor</option>
        <option value="Plant 2, 2nd Floor">Plant 2, 2nd Floor</option>
    </select>
            </div>
             <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Equipment</label>
                <select id="filter-equipment" class="select2 w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All Equipment --</option>
        <option value="Whiteboard">Whiteboard</option>
        <option value="TV/Monitor">TV/Monitor</option>
        <option value="Fotocopy/Printer">Fotocopy/Printer</option>
        <option value="Wi-Fi">Wi-Fi</option>
         <option value="Projector">Projector</option>
    </select>
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
    <h2 class="text-lg font-semibold mb-2">Room List</h2>
    <div class="w-full overflow-x-auto" id="room-scroll-wrapper">
    <table id="room-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Capacity</th>
                    <th class="px-4 py-2">Location</th>
                    <th class="px-4 py-2">Equipment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

<!-- 🔲 Modal Form -->
<div id="roomModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
        <h2 class="text-xl font-semibold mb-4">Create New Room</h2>
        <form id="create-room-form">
           @csrf
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" id="code" placeholder="Automatic" class="w-full border border-gray-300 bg-gray-200 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" readonly>
            </div>

           <div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
   <input type="text" id="name" name="name" class="w-full border border-gray-300 bg-white rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
</div>
<div class="mb-4">
    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
   <input type="number" id="capacity" name="capacity" class="w-full border border-gray-300 bg-white rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
</div>
<!-- Equipment -->
<div class="mb-4">
  <label for="equipment" class="block text-sm font-medium text-gray-700 mb-1">
    Equipment
  </label>
  <select id="equipment" name="equipment[]" 
    class="w-full border text-sm border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
    multiple="multiple">
    <option value="Whiteboard">Whiteboard</option>
    <option value="TV/Monitor">TV/Monitor</option>
    <option value="Fotocopy/Printer">Fotocopy/Printer</option>
    <option value="Wi-Fi">Wi-Fi</option>
    <option value="Projector">Projector</option>
  </select>
</div>

<!-- Location -->
<div class="mb-4">
  <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
    Location
  </label>
  <select id="location" name="location"
    class="w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Select Location --</option>
    <option value="Plant 1, 1st Floor">Plant 1, 1st Floor</option>
    <option value="Plant 1, 2nd Floor">Plant 1, 2nd Floor</option>
    <option value="Plant 2, 1st Floor">Plant 2, 1st Floor</option>
    <option value="Plant 2, 2nd Floor">Plant 2, 2nd Floor</option>
  </select>
</div>

<input type="hidden" id="room_id" name="room_id" value="">


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
#room-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#room-table tbody tr:nth-child(odd) {
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
#room-table td,
#room-table th {
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
#room-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#room-table th, #room-table td {
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
    $(document).ready(function () {
  // Select2 untuk multi equipment
  $('#equipment').select2({
    placeholder: "-- Select Equipments --",
    allowClear: true,
    width: "100%"
  });

  // Select2 untuk single location
  $('#location').select2({
    placeholder: "-- Select Location --",
    allowClear: true,
    width: "100%",
    minimumResultsForSearch: 5 // optional, sembunyikan search kalau < 5 option
  });
});
    function openModal() {
        document.getElementById('roomModal').classList.remove('hidden');
        document.getElementById('roomModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('roomModal').classList.add('hidden');
        document.getElementById('roomModal').classList.remove('flex');
    }


   $('#create-room-form').on('submit', function (e) {
    e.preventDefault();

    let id = $('#room_id').val(); // ambil id
    let url = id ? '/facility/room/' + id + '/update' : '/facility/room/store';
    let method = id ? 'PUT' : 'POST'; // method sesuai REST

     const formData = $(this).serialize() + (id ? '&_method=PUT' : '');

    $.ajax({
        url: url,       // ✅ gunakan variabel
        type: method,   // ✅ gunakan variabel
        data: formData,
        success: function(response){
            Swal.fire({
                icon: 'success',
                title: id ? 'Updated!' : 'Created!',
                text: id ? 'Room data updated successfully.' : 'Room created successfully.',
                timer: 1500,
                showConfirmButton: false
            });
            $('#roomModal').addClass('hidden').removeClass('flex');
            $('#room-table').DataTable().ajax.reload();
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


let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
$(document).ready(function () {
   const table = $('#room-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
        drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
         ajax: {
            url: '{{ route("facility.room.data") }}',
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
            { data: 'name', name: 'name' },
            { data: 'capacity', name: 'capacity' },
            { data: 'location', name: 'location' },
            { data: 'equipment', name: 'equipment' },
        ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

  function openEditModal(id) {
    // Tampilkan modal
    $('#roomModal').removeClass('hidden').addClass('flex');
    
    // Ganti judul modal
    $('#roomModal h2').text('Edit Room');

    // Ambil data dari server via AJAX
    $.ajax({
        url: '/facility/room/' + id + '/edit', // route untuk ambil data room
        type: 'GET',
        success: function(response) {
            // Isi form dengan data dari database
            $('#room_id').val(id); // set id room
            $('#code').val(response.code);
            $('#name').val(response.name);
            $('#capacity').val(response.capacity);
           $('#location').val(response.location).trigger('change'); // trigger agar select2 update


            // Equipment (multi select)
            $('#equipment').val(response.equipment).trigger('change');
        },
        error: function(xhr) {
            // Ganti alert biasa dengan SweetAlert error
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Failed to fetch room data!',
            });
            closeModal();
        }
    });
}

function deleteRoom(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the room!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/facility/room/' + id + '/destroy', // sesuai route
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}' // jangan lupa CSRF
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Room has been deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#room-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Room could not be deleted.'
                    });
                }
            });
        }
    });
}


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