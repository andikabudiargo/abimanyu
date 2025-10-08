@extends('layouts.app')

@section('title', 'Stock')
@section('page-title', 'STOCK DASHBOARD')
@section('breadcrumb-item', 'Inventory')
@section('breadcrumb-active', 'Stock')

@section('content')

<div class="flex justify-between items-center mb-6 bg-white px-4 py-3 rounded-2xl shadow w-full">
  <!-- KIRI: Tab Buttons -->
  <div class="flex space-x-3">
    <!-- Aktif -->
    <button onclick="showPanel('stockPanel')" 
            id="stockTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 shadow-sm hover:shadow-md transition">
      <i data-feather="box" class="w-4 h-4"></i>
      <span>Stock Warehouse</span>
    </button>

    <!-- Tidak aktif -->
    <button onclick="showPanel('historyPanel')" 
            id="historyTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 hover:bg-gray-100 hover:text-indigo-600 transition">
      <i data-feather="activity" class="w-4 h-4"></i>
      <span>Stock Movement</span>
    </button>

     <button onclick="showPanel('monitoringPanel')" 
            id="monitoringTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 hover:bg-gray-100 hover:text-indigo-600 transition">
      <i data-feather="search" class="w-4 h-4"></i>
      <span>Inventory Tracker</span>
    </button>

     <button onclick="showPanel('schedulePanel')" 
            id="historyTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 hover:bg-gray-100 hover:text-indigo-600 transition">
      <i data-feather="truck" class="w-4 h-4"></i>
      <span>Schedule Incoming</span>
    </button>
  </div>

  <!-- KANAN: Jam dan Icon -->
  <div class="flex items-center gap-2 text-gray-700 text-sm font-medium">
    <i data-feather="sun" id="time-icon" class="w-5 h-5"></i>
    <span id="current-time">--:--</span>
  </div>
</div>

<div id="stockPanel">
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
     <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-gray-500 cursor-pointer transition-transform hover:scale-105"
       data-type="critical">
    <p class="text-sm text-gray-500">Empty Stock</p>
    <h3 class="text-xl font-bold text-gray-600">12 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-red-500 cursor-pointer transition-transform hover:scale-105"
       data-type="critical">
    <p class="text-sm text-gray-500">Critical Stock</p>
    <h3 class="text-xl font-bold text-red-600">12 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500 cursor-pointer transition-transform hover:scale-105"
       data-type="overload">
    <p class="text-sm text-gray-500">Overload Stock</p>
    <h3 class="text-xl font-bold text-yellow-600">8 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500 cursor-pointer transition-transform hover:scale-105"
       data-type="dead">
    <p class="text-sm text-gray-500">Safe Stock</p>
    <h3 class="text-xl font-bold text-indigo-600">5 Items</h3>
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
    

    <h2 class="text-lg font-semibold mb-4">Filter Stock</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Code</label>
                <input type="text" id="filter-article-code" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Type</label>
                <select name="article_type" id="filter-article-type" class="article-type w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Choose Article Type --</option>
    @foreach($articleTypes as $type)
        <option value="{{ $type }}">{{ $type }}</option>
    @endforeach
</select>

            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Customer/Supplier</label>
                <select name="supplier" id="filter-supplier" class="supplier w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Choose Supplier/Customer --</option>
    @foreach($suppliers as $supp)
        <option value="{{ $supp->name }}">{{ $supp->name }}</option>
    @endforeach
</select>
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Empty">Empty</option>
        <option value="Critical">Critical</option>
        <option value="Safe">Safe</option>
        <option value="Overload">Overload</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Location</label>
               <select name="warehouse" id="filter-location" class="location w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- All Location --</option>
   @foreach($warehouses as $w)
    <option value="{{ $w }}">{{ $w }}</option>
@endforeach

</select>

            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="w-28 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <button type="button" class="w-28 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Adjustment</button>
        </div>
    </form>
</div>
   
    <!-- 📦 Panel Stock Article -->
   <div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Stock Warehouse</h2>
    <div class="w-full overflow-x-auto" id="stock-scroll-wrapper">
    <table id="stock-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="border-none p-2">Action</th>
                    <th class="border-none p-2 !text-left">Location</th>
                    <th class="border-none p-2 !text-left">Article Type</th>
                    <th class="border-none p-2 !text-left">Cust/Supp</th>
                    <th class="border-none p-2 !text-left">Article Code</th>
                    <th class="border-none p-2 !text-left">Description</th>
                    <th class="border-none p-2 w-28 !text-center">Status</th>
                    <th class="border-none p-2 !text-center">Actual Stock</th>
                    <th class="border-none p-2 !text-center">Safety Stock</th>
                    <th class="border-none p-2 !text-center">Maximum Stock</th>
                    <th class="border-none p-2 !text-center">Balance</th>
                    <th class="border-none p-2 !text-center">Standard Package</th>
                    <th class="border-none p-2">Last Record</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data -->
            </tbody>
        </table>
    </div>
   </div>
</div>

<!-- Movement Modal -->
<div id="movementModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
    <h2 class="text-lg font-semibold mb-4">Filter Movement</h2>
    <form id="movementFilterForm">
      <input type="hidden" name="article_code" id="modalArticleCode" value="">

      <label class="block mb-2 text-sm font-medium">Date</label>
      <input type="text" id="movementDateRange" name="date_range" class="w-full border rounded px-3 py-2 mb-4" placeholder="Select date range" required>

      <label class="block mb-2 text-sm font-medium">Shift</label>
      <select name="shift" class="w-full border rounded px-3 py-2 mb-4">
        <option value="">-- All Shift --</option>
        <option value="1">Shift 1</option>
        <option value="2">Shift 2</option>
        <option value="3">Shift 3</option>
      </select>

      <label class="block mb-2 text-sm font-medium">Transfer Type</label>
      <select name="transfer_type" class="w-full border rounded px-3 py-2 mb-4">
        <option value="">-- All --</option>
        <option value="In">IN</option>
        <option value="Out">OUT</option>
      </select>

      <div class="flex justify-end">
        <button type="button" id="movementCancel" class="mr-2 px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Submit</button>
      </div>
    </form>
  </div>
</div>


    <!-- 📄 Panel Stock History -->
    <div id="historyPanel" class="hidden">
    <!-- 🔹 Bagian Atas: Stock & Ringkasan -->
      <div class="bg-white shadow rounded-xl p-6 mb-6">
    

    <h2 class="text-lg font-semibold mb-4">Filter Stock</h2>

    <form id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Article Code</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Description</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Customer/Supplier</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Transfer Type</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">From</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Destination</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
             <button type="submit"
                    class="w-32 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
        </div>
    </form>
</div>

    <!-- 🔹 Tabel Movement -->
   <div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Stock Movement</h2>
    <div id="movement-placeholder" class="text-center py-20 text-gray-400">
        No data available. Please use filters to generate data.
    </div>
    <div class="w-full overflow-x-auto hidden" id="movement-scroll-wrapper">
    <table id="movement-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="border-none p-2">Date</th>
                        <th class="border-none p-2">Transfer Type</th>
                        <th class="border-none p-2">Reference Number</th>
                        <th class="border-none p-2">Cust/Supp</th>
                        <th class="border-none p-2">Article Code</th>
                        <th class="border-none p-2">Description</th>
                        <th class="border-none p-2">Qty</th>
                        <th class="border-none p-2">UOM</th>
                        <th class="border-none p-2">From</th>
                        <th class="border-none p-2">Destination</th>
                        <th class="border-none p-2">Created By</th>
                        <th class="border-none p-2">Shift</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data movement -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 📄 Panel Stock History -->
    <div id="monitoringPanel" class="hidden">
    <!-- 🔹 Bagian Atas: Stock & Ringkasan -->
   <div class="flex gap-4 mb-6">
    <div class="w-full bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-2">Filter</h2>
       <form id="periodicFilter">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">

                <!-- Filter Date -->
                <input type="date" name="date" id="date"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">

                <!-- Filter Supplier -->
                <select name="supplier" id="supplier"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
                    <option value="">All Suppliers</option>
                    <option value="SUPP001">Supplier A</option>
                    <option value="SUPP002">Supplier B</option>
                </select>

                <!-- Filter Article -->
                <select name="article" id="article"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
                    <option value="">All Articles</option>
                    <option value="ART001">Article A</option>
                    <option value="ART002">Article B</option>
                </select>

                <!-- Tombol -->
                 <div class="flex justify-end">
                <button type="submit"
                    class="w-32 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
                 </div>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
     <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-gray-500 cursor-pointer transition-transform hover:scale-105"
       data-type="critical">
    <p class="text-sm text-gray-500">Dead Stock</p>
    <h3 class="text-xl font-bold text-gray-600">12 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-red-500 cursor-pointer transition-transform hover:scale-105"
       data-type="critical">
    <p class="text-sm text-gray-500">Slow Moving</p>
    <h3 class="text-xl font-bold text-red-600">12 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500 cursor-pointer transition-transform hover:scale-105"
       data-type="overload">
    <p class="text-sm text-gray-500">Medium Moving</p>
    <h3 class="text-xl font-bold text-yellow-600">8 Items</h3>
  </div>
  <div class="stock-card bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500 cursor-pointer transition-transform hover:scale-105"
       data-type="dead">
    <p class="text-sm text-gray-500">Fast Moving</p>
    <h3 class="text-xl font-bold text-indigo-600">5 Items</h3>
  </div>
</div>

<!-- Warning Restock Needed -->
<div class="bg-red-200 rounded-xl shadow p-6 mb-6 w-full mx-auto flex items-center justify-between">
    <div class="p-2 flex-1">
    <h2 class="text-xl font-semibold text-gray-800 mb-1">⚠️ Restock Needed</h2>
    <p class="text-gray-600 mb-0">
  The following items require immediate restocking based on forecasting demand to avoid stockouts and production delays for next month.
</p>

  </div>
  <div>
  <button
    id="btnCheckRestock"
    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
    type="button"
>
    Check Items Needing Restock
</button>

  </div>
  
</div>


    <!-- 🔹 Tabel Movement -->
     <div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Stock Periodic</h2>
    <div id="periodic-placeholder" class="text-center py-20 text-gray-400">
        Select Period to Generate Data
    </div>
    <div class="w-full overflow-x-aut hidden" id="periodic-scroll-wrapper">
    <table id="periodic-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="border-none p-2">Cust/Supp</th>
                        <th class="border-none p-2">Article Type</th>
                        <th class="border-none p-2">Description</th>
                        <th class="border-none p-2">Status</th>
                        <th class="border-none p-2">Initial Stock</th>
                        <th class="border-none p-2">Incoming</th>
                        <th class="border-none p-2">Outgoing</th>
                        <th class="border-none p-2">Final Stock</th>
                        <th class="border-none p-2">Min Stock</th>
                        <th class="border-none p-2">Max Stock</th>
                        <th class="border-none p-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data movement -->
                </tbody>
            </table>
        </div>
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

#movement-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#movement-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

#periodic-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#periodic-table tbody tr:nth-child(odd) {
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
#movement-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#movement-table th, #movement-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#movement-table td, #movement-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#movement-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
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

/* 🧭 Spacing */
#periodic-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#periodic-table th, #periodic-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#periodic-table td, #periodic-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#periodic-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
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

.select2-container .select2-selection--single {
    height: 38px !important; /* sesuaikan dengan input lainnya */
    padding: 4px 10px;
    border: 1px solid #d1d5db; /* warna border sama dengan Tailwind border-gray-300 */
    border-radius: 0.375rem; /* sesuai rounded-md */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px; /* biar teks sejajar vertikal */
    font-size: 12px; /* text-base */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
         // Flatpickr range
  flatpickr("#movementDateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    allowInput: true
  });

  const movementModal = document.getElementById('movementModal');
  const modalArticleCode = document.getElementById('modalArticleCode');

  // Buka modal saat klik movement link
  document.querySelectorAll('.movement-link').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const articleCode = this.dataset.articleCode;
      modalArticleCode.value = articleCode;
      movementModal.classList.remove('hidden');
    });
  });

  // Tutup modal
  document.getElementById('movementCancel').addEventListener('click', function() {
    movementModal.classList.add('hidden');
  });

  // Submit filter
  document.getElementById('movementFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    if (!formData.get('date_range')) {
      alert('Tanggal wajib diisi!');
      return;
    }

    fetch('/inventory/movement', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      console.log(data); // tampilkan movement data
      // TODO: render di table atau modal sesuai kebutuhan
      movementModal.classList.add('hidden');
    })
    .catch(err => {
      console.error(err);
      alert('Gagal mengambil data movement');
    });
  });

         flatpickr("#dateRange", {
      mode: "range",
      dateFormat: "Y-m-d",
      maxDate: "today",
      allowInput: true
    });

    // Select2 for supplier and article
    $('#filter-supplier').select2({
      placeholder: "-- All Custumoer & Supplier --",
      allowClear: true,
      width: '100%'
    });

     $('#filter-status').select2({
      placeholder: "-- All Status --",
      allowClear: true,
      width: '100%'
    });

    $('#filter-article-type').select2({
      placeholder: "-- All Article Type --",
      allowClear: true,
      width: '100%'
    });
     $('#filter-location').select2({
      placeholder: "-- All Location --",
      allowClear: true,
      width: '100%'
    });
const today = new Date().toISOString().slice(0,10);
let movementTable = null;

$('#filterForm').on('submit', function(e) {
    e.preventDefault();

    const dateRange = $('#dateRange').val();
    const supplier = $('#supplier').val();
    const article = $('#article').val();

    // Tampilkan wrapper tabel jika belum muncul
    if (!$('#movement-scroll-wrapper').is(':visible')) {
        $('#movement-scroll-wrapper').removeClass('hidden');
        $('#movement-placeholder').hide();

        // Inisialisasi DataTable
        movementTable = $('#movement-table').DataTable({
            processing: true,
            serverSide: false,
            autoWidth: false,
            scrollX: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-download mr-2"></i>Export',
                    className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
                    buttons: [
                        { extend: 'copyHtml5', text: '<i class="fas fa-copy mr-2"></i>Copy' },
                        { extend: 'excelHtml5', filename: 'Receiving_' + today, title: null },
                        { extend: 'pdfHtml5', filename: 'Receiving_' + today, title: null, orientation: 'portrait', pageSize: 'A4' },
                        { extend: 'print', title: 'Receiving ' + today }
                    ]
                }
            ],
            columns: [
                { data: 'date', className: 'text-center' },
                { data: 'status', className: '!text-center' },
                { data: 'reference_number' },
                { data: 'cust_supp' },
                { data: 'article_code' },
                { data: 'description' },
                { data: 'qty', className: 'text-center' },
                { data: 'uom', className: 'text-center' },
                { data: 'from' },
                { data: 'destination' },
                { data: 'created_by' },
                 { data: 'shift' },
            ],
            order: [[0, 'desc']],
            language: { emptyTable: 'No data available for the selected filter.' },
            drawCallback: function(settings) {
                feather.replace();
            }
        });
    }

    // Load data via AJAX
    $.get('/ppic/logistic/stock/movement', {
        date_range: dateRange,
        supplier: supplier,
        article: article
    }, function(res) {
        movementTable.clear();
        if (res.data && res.data.length > 0) {
            movementTable.rows.add(res.data);
        }
        movementTable.draw();
    });
});

let periodicTable = null;

// Event Filter Form
$('#periodicFilter').on('submit', function(e) {
    e.preventDefault();

    const dateRange = $('#dateRange').val();
    const supplier = $('#supplier').val();
    const article = $('#article').val();

    // Tampilkan wrapper tabel jika belum muncul
    if (!$('#periodic-scroll-wrapper').is(':visible')) {
        $('#periodic-scroll-wrapper').removeClass('hidden');
        $('#periodic-placeholder').hide();

        // Inisialisasi DataTable
        periodicTable = $('#periodic-table').DataTable({
            processing: true,
            serverSide: false,
            scrollX: true,
            autoWidth: false,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-download mr-2"></i>Export',
                    className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
                    buttons: [
                        { extend: 'copyHtml5', text: '<i class="fas fa-copy mr-2"></i>Copy' },
                        { extend: 'excelHtml5', filename: 'Periodic_' + today, title: null },
                        { extend: 'pdfHtml5', filename: 'Periodic_' + today, title: null, orientation: 'portrait', pageSize: 'A4' },
                        { extend: 'print', title: 'Periodic ' + today }
                    ]
                }
            ],
            columns: [
                { data: 'cust_supp', defaultContent: '-', className: 'border p-2' },
                { data: 'article_type', defaultContent: '-', className: 'border p-2' },
                { data: null, className: 'border p-2', render: r => `${r.article_code ?? '-'} - ${r.description ?? '-'}` },
                { data: 'status', defaultContent: '-', className: 'border p-2' },
                { data: 'initial', defaultContent: 0, className: 'border p-2 text-center' },
                { data: 'incoming', defaultContent: 0, className: 'border p-2 text-center', render: d => `<span style="color:green;">${d ?? 0}</span>` },
                { data: 'outgoing', defaultContent: 0, className: 'border p-2 text-center', render: d => `<span style="color:red;">${d ?? 0}</span>` },
                { data: 'final', defaultContent: 0, className: 'border p-2 text-center' },
                { data: 'min', className: 'border p-2 text-center' },
                { data: 'max', className: 'border p-2 text-center' },
                { data: 'remarks', defaultContent: 0, className: 'border p-2' },
            ],
           
            order: [[2, 'asc']], // default sort by description
            language: { emptyTable: 'No data available for the selected filter.' },
            drawCallback: function(settings) { feather.replace(); }
        });
    }

    // Load data via AJAX
    $.get('/ppic/logistic/stock/periodic', {
        date_range: dateRange,
        supplier: supplier,
        article: article
    }, function(res) {
        periodicTable.clear();
        if (res && Array.isArray(res) && res.length > 0) {
            periodicTable.rows.add(res);
        }
        periodicTable.draw();
    });
});



});
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
         document.getElementById('monitoringPanel').classList.add('hidden');

        // Tampilkan panel yang dipilih
        document.getElementById(panelId).classList.remove('hidden');

        // Ganti warna tombol
        document.getElementById('stockTab').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('stockTab').classList.add('bg-transparent', 'text-gray-800');
        document.getElementById('historyTab').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('historyTab').classList.add('bg-transparent', 'text-gray-800');
        document.getElementById('monitoringTab').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('monitoringTab').classList.add('bg-transparent', 'text-gray-800');

        if (panelId === 'stockPanel') {
            document.getElementById('stockTab').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('stockTab').classList.remove('bg-transparent', 'text-gray-800');
        } else if (panelId === 'historyPanel') {
            document.getElementById('historyTab').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('historyTab').classList.remove('bg-transparent', 'text-gray-800');
        } else {
            document.getElementById('monitoringTab').classList.add('bg-indigo-600', 'text-white');
            document.getElementById('monitoringTab').classList.remove('bg-transparent', 'text-gray-800');
        }
    }

    let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
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
                d.article_code = $('#filter-article-code').val();
                d.article_type = $('#filter-article-type').val();
                d.supplier     = $('#filter-supplier').val();
                d.status       = $('#filter-status').val();
                d.location     = $('#filter-location').val();
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
                filename: 'Stock_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Stock_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'potrait',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Stock ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
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
    { data: 'warehouse_id', name: 'warehouse_id', orderable: false },
    { data: 'article_type', name: 'article_type', orderable: false },
    { data: 'supplier', name: 'supplier', orderable: false },
    { data: 'article_code', name: 'article_code' , orderable: false},
    { data: 'article_name', name: 'article_name', orderable: false },
    { data: 'status', name: 'status', orderable: false, className: 'text-center' },
    { data: 'qty', name: 'qty' , orderable: false, className: 'text-center'},
    { data: 'min_stock', name: 'min_stock', orderable: false, className: 'text-center' },
    { data: 'max_stock', name: 'max_stock', orderable: false, className: 'text-center' },
    { data: 'balance', name: 'balance', orderable: false, className: 'text-center' },
    { data: 'min_package', name: 'min_package' , orderable: false, className: 'text-center'},
    { data: 'updated_at', name: 'updated_at', orderable: false },
    { data: 'row_class', visible: false } // kolom hidden
],
rowCallback: function(row, data) {
    $(row).removeAttr('style');

    if (data.row_class === 'row-overload') {
        $(row).attr('style', 'background-color: #FEF3C7 !important;');
    } else if (data.row_class === 'row-critical') {
        $(row).attr('style', 'background-color: #FECACA !important;');
    }
}


    });
    

   feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
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