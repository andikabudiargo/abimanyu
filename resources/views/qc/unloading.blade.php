@extends('layouts.app')

@section('title', 'Stock')
@section('page-title', 'STOCK DASHBOARD')
@section('breadcrumb-item', 'Inventory')
@section('breadcrumb-active', 'Stock')

@section('content')

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-red-500 cursor-pointer transition-transform hover:scale-105"
       data-type="critical">
    <p class="text-sm text-gray-500">Top Defect</p>
    <h3 class="text-xl font-bold text-red-600">12 Defect</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500 cursor-pointer transition-transform hover:scale-105"
       data-type="overload">
    <p class="text-sm text-gray-500">Top NG Part</p>
    <h3 class="text-xl font-bold text-yellow-600">8 Part</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500 cursor-pointer transition-transform hover:scale-105"
       data-type="dead">
    <p class="text-sm text-gray-500">Top Performance Part</p>
    <h3 class="text-xl font-bold text-indigo-600">5 Part</h3>
  </div>
</div>

<!-- MODAL -->
<div id="stockModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
    <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">×</button>
    <h2 id="modalTitle" class="text-xl font-semibold mb-4"></h2>
    <div id="modalContent" class="text-sm text-gray-700">
      <!-- List item akan dimuat via JS -->
      <p>Loading items...</p>
    </div>
  </div>
</div>

   <div class="bg-white shadow rounded-xl p-6 mb-6">
    

    <h2 class="text-lg font-semibold mb-4">Filter Unloading Inspection</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Name</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Customer/Supplier</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('qc.unloading.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>
   
    <!-- 📦 Panel Stock Article -->
   <div id="stockPanel" class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2"></h2>
    <div class="w-full overflow-x-auto" id="stock-scroll-wrapper">
    <table id="stock-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="border-none p-2">Action</th>
                    <th class="border-none p-2">Periode</th>
                    <th class="border-none p-2">Cust/Supp</th>
                    <th class="border-none p-2">Part Name</th>
                    <th class="border-none p-2 text-center">Status</th>
                    <th class="border-none p-2 text-center">Total Check</th>
                    <th class="border-none p-2 text-center">OK</th>
                    <th class="border-none p-2 text-center">NC</th>
                    <th class="border-none p-2 text-center">NG</th>
                    <th class="border-none p-2 text-center">Total Pass Trough</th>
                    <th class="border-none p-2 text-center">Pass Rate</th>
                    <th class="border-none p-2 text-center">Pass Trough</th>
                    <th class="border-none p-2">Created by</th>
                    <th class="border-none p-2">Created at</th>
                </tr>
            </thead>
            <tbody>
                <th class="border-none p-2"><i data-feather="align-justify"></i></td>
                    <td class="border-none p-2">AGUSTUS 2025</td>
                    <td class="border-none p-2">PT. AUTOPLASTIK INDONESIA</td>
                    <td class="border-none p-2">PANEL FRONT SIDE NEW</td>
                    <td class="border-none p-2 text-center"><span class="bg-green-400 rounded-lg p-1 text-white">Approved</span></td>
                    <td class="border-none p-2 text-center">100</td>
                    <td class="border-none p-2 text-center">86</td>
                    <td class="border-none p-2 text-center">2</td>
                    <td class="border-none p-2 text-center">14</td>
                    <td class="border-none p-2 text-center">84</td>
                    <td class="border-none p-2 text-center">82%</td>
                    <td class="border-none p-2 text-center">23%</td>
                    <td class="border-none p-2">Admin QC</td>
                    <td class="border-none p-2">30-07-2025</td>
            </tbody>
        </table>
    </div>

    <!-- 📄 Panel Stock History -->
    <div id="historyPanel" class="hidden table-responsive bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Stock Movement</h2>
        <table id="movement-table" class="w-full mt-4 table-auto border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Date</th>
                    <th class="border p-2">Process</th>
                    <th class="border p-2">Article Type</th>
                    <th class="border p-2">Cust/Supp</th>
                    <th class="border p-2">Article Code</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">UOM</th>
                    <th class="border p-2">Qty</th>
                    <th class="border p-2">Batch Package</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Purpose</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data -->
            </tbody>
        </table>
    </div>

    </div>
@endsection

@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#stock-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#stock-table tbody tr:nth-child(odd) {
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
#stock-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#stock-table th, #stock-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#stock-table td, #stock-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#stock-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}
</style>

<script>
     function updateTime() {
    const now = new Date();
    const h = now.getHours();
    const m = now.getMinutes().toString().padStart(2, '0');
    const icon = document.getElementById('time-icon');
    icon.setAttribute('data-feather', h >= 6 && h < 18 ? 'sun' : 'moon');
    document.getElementById('current-time').textContent = `${h}:${m}`;
    feather.replace();
  }

  updateTime();
  setInterval(updateTime, 60000);
    function showPanel(panelId) {
        // Sembunyikan semua panel
        document.getElementById('stockPanel').classList.add('hidden');
        document.getElementById('historyPanel').classList.add('hidden');

        // Tampilkan panel yang dipilih
        document.getElementById(panelId).classList.remove('hidden');

        // Ganti warna tombol
        document.getElementById('stockTab').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('stockTab').classList.add('bg-transparent', 'text-gray-800');
        document.getElementById('historyTab').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('historyTab').classList.add('bg-transparent', 'text-gray-800');

        if (panelId === 'stockPanel') {
            document.getElementById('stockTab').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('stockTab').classList.remove('bg-transparent', 'text-gray-800');
        } else {
            document.getElementById('historyTab').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('historyTab').classList.remove('bg-transparent', 'text-gray-800');
        }
    }

$(document).ready(function() {
    const table = $('#stock-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
        ajax: {
            url: "{{ route('ppic.stock.data') }}",
            data: function (d) {
                d.article_code = $('input[name=article_code]').val();
                d.article_type = $('input[name=article_type]').val();
                d.supplier     = $('input[name=supplier]').val();
                d.status       = $('input[name=status]').val();
                d.status       = $('input[name=location]').val();
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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
    { data: 'warehouse_id', name: 'warehouse_id', orderable: false },
    { data: 'article_type', name: 'article_type', orderable: false },
    { data: 'supplier', name: 'supplier', orderable: false },
    { data: 'article_code', name: 'article_code' , orderable: false, className: 'text-right'},
    { data: 'article_name', name: 'article_name', orderable: false },
    { data: 'status', name: 'status', orderable: false, className: 'text-center' },
    { data: 'qty', name: 'qty' , orderable: false, className: 'text-center'},
    { data: 'uom', name: 'uom', orderable: false, className: 'text-center' },
    { data: 'min_stock', name: 'min_stock', orderable: false, className: 'text-center' },
    { data: 'max_stock', name: 'max_stock', orderable: false, className: 'text-center' },
    { data: 'min_package', name: 'min_package' , orderable: false, className: 'text-center'},
    { data: 'updated_at', name: 'updated_at', orderable: false },
    { data: 'row_class', visible: false } // kolom hidden
],
rowCallback: function(row, data) {
    $(row).removeAttr('style');

    if (data.row_class === 'row-critical') {
        $(row).attr('style', 'background-color: #FEF3C7 !important;');
    } else if (data.row_class === 'row-overload') {
        $(row).attr('style', 'background-color: #FECACA !important;');
    }
}


    });

    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
});

$(document).ready(function () {
    $('.stock-card').on('click', function () {
        const type = $(this).data('type');
        let title = '';
        let items = [];

        switch(type) {
            case 'critical':
                title = 'List of Critical Stock Items';
                items = ['Item A', 'Item B', 'Item C'];
                break;
            case 'overload':
                title = 'List of Overload Stock Items';
                items = ['Item D', 'Item E'];
                break;
            case 'dead':
                title = 'List of Dead Stock Items';
                items = ['Item F', 'Item G', 'Item H'];
                break;
        }

        $('#modalTitle').text(title);
        $('#modalContent').html(`<ul class="list-disc pl-5">${items.map(i => `<li>${i}</li>`).join('')}</ul>`);
        $('#stockModal').removeClass('hidden');
    });

    $('#closeModal').on('click', function () {
        $('#stockModal').addClass('hidden');
    });

    // Optional: click outside to close
    $('#stockModal').on('click', function(e) {
        if (e.target.id === 'stockModal') {
            $('#stockModal').addClass('hidden');
        }
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
</script>
@endpush