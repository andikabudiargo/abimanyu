@extends('layouts.app')

@section('title', 'e-STO')
@section('page-title', 'DASHBOARD E-STO')
@section('breadcrumb-item', 'e-STO')
@section('breadcrumb-active', 'e-STO')

@section('content')

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter E-STO</h2>

    <form id="filter-form">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm mb-1 font-medium text-gray-700">Location</label>
            <select id="filter-location" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- All Location --</option>
                <option value="Raw Material">Raw Material</option>
                <option value="Finish Goods">Finish Goods</option>
                <option value="Chemical">Chemical</option>
                <option value="Consumable">Consumable</option>
                <option value="WIP Sanding">WIP Sanding</option>
      <option value="WIP Buffing">WIP Buffing</option>
      <option value="Werate">Werate</option>
      <option value="WIP Touch Up">WIP Touchup</option>
                <option value="OT">OT</option>
                 <option value="Dead Stock CM1">Dead Stock CM1</option>
            </select>
        </div>
      <div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Article Code
  </label>

  <select id="filter-article"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All Article --</option>
  </select>
</div>

       <div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Number e-STO
  </label>

  <select id="filter-sto_number"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All STO Number --</option>
  </select>
</div>

 @if(auth()->id() == 53)
<div>
  <label class="block text-sm mb-1 font-medium text-gray-700">
    Periode STO
  </label>

  <select id="filter-sto-periode"
          class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">-- All STO Periode --</option>
    <option value="2026/02">2026 Februari</option>
    <option value="2026/01">2026 Januari (Closed)</option>
    <option value="2025/12">2025 Desember (Closed)</option>
  </select>
</div>
@endif

        </div>

    <div class="flex justify-start gap-2 mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
        <a href="{{ route('facility.sto.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
    </div>
</form>

</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">E-STO List</h2>
    <div class="w-full overflow-x-auto" id="sto-scroll-wrapper">
    <table id="sto-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                     <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Location</th>
                    <th class="px-4 py-2 text-left">Part Code</th>
                    <th class="px-4 py-2 text-left">Part Name</th>
                     <th class="px-4 py-2 text-center">Qty</th>
                      <th class="px-4 py-2 text-center">Qty Box</th>
                    <th class="px-4 py-2 text-center">UoM</th>
                    <th class="px-4 py-2 text-center">STO Number</th>
                    <th class="px-4 py-2 text-center">Created by</th>
                    <th class="px-4 py-2 text-center">Created at</th>
                    <th class="px-4 py-2 text-left">Note</th>
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
    /* hover background */
#sto-table tbody tr.sto-row:hover {
  background-color: #eff6ff; /* bg-blue-50 */
}

/* show tooltip on hover */
#sto-table tbody tr.sto-row:hover::after {
  opacity: 1;
}
/* Ubah warna baris even dan odd */
#sto-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#sto-table tbody tr:nth-child(odd) {
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
#sto-table td,
#sto-table th {
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
#sto-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#sto-table th, #sto-table td {
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

/* MOBILE CARD VIEW: Ubah tabel jadi card saat layar kecil */
@media (max-width: 768px) {

  #sto-table thead {
    display: none;
  }

  #sto-table, 
  #sto-table tbody, 
  #sto-table tr, 
  #sto-table td {
    display: block;
    width: 100%;
  }

  #sto-table tr {
    margin-bottom: 18px;
    background: #ffffff;
    border-radius: 14px;
    padding: 14px 12px;
    box-shadow: 0 3px 14px rgba(0,0,0,0.07);
  }

  #sto-table td {
    padding: 8px 4px;
    position: relative;
    text-align: left !important;
    border: none;
  }

  #sto-table td::before {
    content: attr(data-label);
    font-size: 12px;
    font-weight: 600;
    color: #1e40af;
    display: block;
    margin-bottom: 4px;
    opacity: 0.8;
  }

  
}

</style>
<script>
$('#sto-table').on('draw.dt', function () {
    $('#sto-table tbody tr').each(function () {
        $(this).find('td').each(function (index) {
            const headers = [
                "Action",
                "Location",
                "Part Code",
                "Part Name",
                "Qty",
                "UoM",
                "Qty Box",
                "STO Number",
                "Created By",
                "Created At",
                "Note"
            ];
            $(this).attr('data-label', headers[index]);
        });
    });
});

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

$(function () {

  const table = $('#sto-table').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,

    ajax: {
      url: '/facility/sto/data',
      type: 'GET',
      data: function (d) {
        d.location   = $('#filter-location').val();
        d.article    = $('#filter-article').val();
        d.sto_number = $('#filter-sto_number').val();
        d.sto_month = $('#filter-sto-periode').val();
      }
    },

    columns: [
    { data: 'action', orderable: false, searchable: false },
      { data: 'location', className: 'text-center' },
      { data: 'article_code' },
      { data: 'part_name' },
      { data: 'qty', className: 'text-center' },
       { data: 'min_package', className: 'text-center' },
      { data: 'unit', className: 'text-center' },
      { data: 'sto_number', className: 'text-center' },
      { data: 'created_by', className: 'text-center' },
      { 
        data: 'created_at',
        className: 'text-center',
    
      },
      { data: 'note' },
    ],

    order: [[7, 'desc']],

    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, 'All']
    ],

    dom: `
      <"flex flex-wrap items-center justify-between mb-4"
        <"flex items-center gap-3"l>
        <"flex items-center gap-1 ml-auto"f B>
      >
      rt
      <"flex items-center justify-between mt-4"ip>
    `,

   buttons: [
{
    extend: 'collection',
    text: '<i class="fa fa-download mr-1"></i> Export',
    className: 'px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm',
    autoClose: true,

    buttons: [

        // ======================
        // SUMMARY (EXPORT DATATABLE)
        // ======================
        {
            extend: 'excelHtml5',
            text: '<i class="fa fa-table mr-2"></i> Summary (FAT)',
            exportOptions: {
                columns: ':not(:first-child)'
            }
        },

        // ======================
        // REPORT BY BOM (LARAVEL EXPORT)
        // ======================
        {
            text: '<i class="fa fa-file-text-o mr-2"></i> Report (PPIC)',
            action: function () {

                // redirect ke controller export
                window.location.href = '/facility/sto/report';

                setTimeout(() => {
        $(btn).removeClass('opacity-50 pointer-events-none');
    }, 3000);

            }
        }
    ]
}
]
  });
// 🔍 Feather icons
  table.on('draw', function () {
    feather.replace();
  });
  // 🔍 Submit filter
  $('#filter-form').on('submit', function (e) {
    e.preventDefault();
    table.ajax.reload();
  });

});

$(document).ready(function () {

  $('#filter-sto_number').select2({
    placeholder: '-- All STO Number --',
    allowClear: true,
    width: '100%',
    ajax: {
      url: '/facility/sto/select',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data.results
        };
      }
    }
  });

});

$(document).ready(function () {

  $('#filter-article').select2({
    placeholder: '-- All Article --',
    allowClear: true,
    width: '100%',
    ajax: {
      url: '/facility/article/select',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data.results
        };
      }
    }
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

function deleteSTO(id) {
    Swal.fire({
        title: 'Yakin ingin hapus?',
        text: "STO Number ini akan terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/facility/sto/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire(
                        'Terhapus!',
                        response.message,
                        'success'
                    );
                    $('#sto-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus.',
                        'error'
                    );
                }
            });
        }
    });
}

  </script>

@endpush


@endsection