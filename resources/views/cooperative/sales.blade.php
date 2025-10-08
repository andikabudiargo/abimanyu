@extends('layouts.app-pos')

@section('title', 'Quality Inspection')
@section('page-title', 'QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Quality Inspection')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
  <!-- 📘 Sidebar Search Panel -->
  <!-- Sidebar -->
<div class="w-full md:w-2/3 bg-gray-20 rounded-2xl">
  <!-- Header -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-2">

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-4 flex items-center gap-3 hover:shadow-md transition cursor-pointer">
      <div class="bg-gray-100 p-3 rounded-full flex items-center justify-center">
        <i data-feather="grid" class="h-6 w-6 text-blue-500"></i>
      </div>
      <div>
        <h6 class="text-gray-800 font-semibold">All Menu</h6>
        <span class="text-xs text-gray-400">100 Items</span>
      </div>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-4 flex items-center gap-3 hover:shadow-md transition cursor-pointer">
      <div class="bg-gray-100 p-3 rounded-full flex items-center justify-center">
        <i data-feather="utensils" class="h-6 w-6 text-blue-500"></i>
      </div>
      <div>
        <h6 class="text-gray-800 font-semibold">Food</h6>
        <span class="text-xs text-gray-400">50 Items</span>
      </div>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-4 flex items-center gap-3 hover:shadow-md transition cursor-pointer">
      <div class="bg-gray-100 p-3 rounded-full flex items-center justify-center">
        <i data-feather="cup" class="h-6 w-6 text-blue-500"></i>
      </div>
      <div>
        <h6 class="text-gray-800 font-semibold">Drink</h6>
        <span class="text-xs text-gray-400">30 Items</span>
      </div>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-4 flex items-center gap-3 hover:shadow-md transition cursor-pointer">
      <div class="bg-gray-100 p-3 rounded-full flex items-center justify-center">
        <i data-feather="box" class="h-6 w-6 text-blue-500"></i>
      </div>
      <div>
        <h6 class="text-gray-800 font-semibold">Other</h6>
        <span class="text-xs text-gray-400">20 Items</span>
      </div>
    </div>

  </div>

<div class="w-full rounded-lg p-2">
  <div class="relative">
    <input 
      type="text" 
      placeholder="Search something on your mind..." 
      class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:outline-none"
    />
    <div class="absolute inset-y-0 right-3 flex items-center">
      <i data-feather="search" class="w-5 h-5 text-gray-500"></i>
    </div>
  </div>
</div>
<div class="p-4">
  <!-- Grid Menu -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    
    <!-- Card Item -->
    <div class="bg-white shadow rounded-xl p-3 hover:shadow-lg transition flex flex-col items-center">
      <img src="https://via.placeholder.com/120" alt="Beef Crowich" class="rounded-md w-24 h-24 object-cover mb-2">
      <h3 class="text-gray-800 font-semibold text-sm">Beef Crowich</h3>
      <span class="text-xs text-gray-500 mb-1">Sandwich</span>
      <span class="text-blue-600 font-bold text-sm">$5.50</span>
    </div>

    <!-- Card Item -->
    <div class="bg-white shadow rounded-xl p-3 hover:shadow-lg transition flex flex-col items-center">
      <img src="https://via.placeholder.com/120" alt="Croissant" class="rounded-md w-24 h-24 object-cover mb-2">
      <h3 class="text-gray-800 font-semibold text-sm">Buttermilk Croissant</h3>
      <span class="text-xs text-gray-500 mb-1">Pastry</span>
      <span class="text-blue-600 font-bold text-sm">$4.00</span>
    </div>

    <!-- Card Item -->
    <div class="bg-white shadow rounded-xl p-3 hover:shadow-lg transition flex flex-col items-center">
      <img src="https://via.placeholder.com/120" alt="Donut" class="rounded-md w-24 h-24 object-cover mb-2">
      <h3 class="text-gray-800 font-semibold text-sm">Cereal Cream Donut</h3>
      <span class="text-xs text-gray-500 mb-1">Donut</span>
      <span class="text-blue-600 font-bold text-sm">$2.45</span>
    </div>

    <!-- Card Item -->
    <div class="bg-white shadow rounded-xl p-3 hover:shadow-lg transition flex flex-col items-center">
      <img src="https://via.placeholder.com/120" alt="Cheesecake" class="rounded-md w-24 h-24 object-cover mb-2">
      <h3 class="text-gray-800 font-semibold text-sm">Cheesy Cheesecake</h3>
      <span class="text-xs text-gray-500 mb-1">Cake</span>
      <span class="text-blue-600 font-bold text-sm">$3.75</span>
    </div>

  </div>
</div>

</div>


  <!-- 📦 Main Transfer Panel -->
  <!-- Main Panel -->
<div class="w-full md:w-1/3 bg-white shadow-md rounded-xl p-4 space-y-4">

    <h2 class="text-lg font-semibold text-gray-700">Quality Inspection</h2>
    <form id="inspection-form">
      <!-- 🔢 Nomor Referensi -->
    <div class="flex flex-col gap-4 mb-8">
  <!-- Baris 1 -->
  <div class="flex flex-col md:flex-row gap-4">
    <div class="w-full md:w-1/2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Inspection Post <small class="text-red-600"> *</small>
      </label>
      <select name="inspection_post" id="inspection_post"
        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
        <option value="">-- Choose Post --</option>
        <option value="Incoming">Incoming</option>
        <option value="Unloading">Unloading</option>
        <option value="Buffing">Buffing</option>
        <option value="Touch Up">Touch Up</option>
        <option value="Final">Final</option>
      </select>
    </div>

    <div class="w-full md:w-1/2">
      <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">
        Supplier<small class="text-red-600"> *</small>
      </label>
      <select name="supplier" id="supplier"
        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
        <option value="">-- Pilih Supplier --</option>
       
      </select>
    </div>
  </div>

  <!-- Baris 2 -->
  <div class="flex flex-col md:flex-row gap-4">
    <div class="w-full md:w-1/2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Part Name<small class="text-red-600"> *</small>
      </label>
      <select name="part_name" id="part_name"
        class="part-select w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
        <option value="">-- Select Part --</option>
      </select>
    </div>

    <div class="w-full md:w-1/2">
      <label for="total_check" class="block text-sm font-medium text-gray-700 mb-1">
        Qty Received <small class="text-red-600">*</small>
      </label>
      <input type="number" name="qty_received" id="qty_received"
        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        placeholder="Masukan Qty Total Kedatangan Barang ..." required />
    </div>
  </div>

  <!-- Catatan -->
  <div class="w-full">
    <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
    <textarea id="note" rows="2"
      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
  </div>
</div>


      <div class="flex gap-4 mb-4">
    <!-- Check Method -->
  <div id="check_method_container" class="hidden">
    <label for="check_method" class="block text-sm font-medium text-gray-700 mb-1">
      Inspection Method <small class="text-red-600">*</small></label>
   <select name="check_method" id="check_method"
                  class="w-64 px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Choose Method --</option>
            <option value="100%">100% (A)</option> <!-- Dari Produksi -->
            <option value="Sampling">Sampling (S)</option> <!-- Dari Supplier -->
          </select>
  </div>
  <div>
    <label for="total_check" class="block text-sm font-medium text-gray-700 mb-1">
      Total Check <small class="text-red-600">*</small>
    </label>
    <input type="number" name="total_check" id="total_check"
           class="w-64 px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
           placeholder="Masukan Total Qty Part ..." />
  </div>
</div>


     <!-- 📋 Tabel Artikel yang Dipindahkan -->
<div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400">
  <table id="itemTable" class="min-w-full bg-white border border-gray-200">
    <thead class="bg-red-800 text-white">
      <tr>
        <th class="p-2 border">No.</th>
        <th class="p-2 border">Defect</th>
        <th class="p-2 border w-24">Qty</th>
        <th class="p-2 border w-24">OK Repair</th>
        <th class="p-2 border">Note</th>
        <th class="p-2 border">Action</th>
      </tr>
    </thead>
    <tbody id="defectTableBody">
      <!-- Baris awal -->
    </tbody>
  </table>
  <button type="button" id="addRowBtn" class="mt-2 bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
    + Add Row
  </button>
</div>

 <hr class="mt-4">
   <div class="flex justify-start space-x-2 mt-4">
   <button id="resetBtn"
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   <i data-feather="refresh-cw" class="h-4 w-4"></i> Reset
</button>

<button id="submitBtn" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
   <i data-feather="save" class="h-4 w-4"></i>
   Save
</button>
  </div>
   </form>
  </div>
</div>

<style>

  /* ✅ Perbaiki Border dan Padding Kolom */
  #itemTable th, #itemTable td {
    border: 1px solid #e5e7eb !important;  /* Tailwind gray-200 */
    padding: 8px 12px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    font-size: 0.875rem;  /* Tailwind text-sm */
  }

  /* ✅ Baris Genap & Ganjil */
  #itemTable tbody tr:nth-child(even) {
    background-color: #f9fafb !important;  /* Tailwind gray-50 */
  }
  #itemTable tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
  }

  /* ✅ Hover Warna */
  #itemTable tbody tr:hover {
    background-color: #e0f2fe !important;  /* Tailwind blue-100 */
  }

  /* ✅ Hilangkan border horizontal agar tampak lebih modern */
  #itemTable td, #itemTable th {
    border-left: none !important;
    border-right: none !important;
  }

  /* ✅ Pagar kiri-kanan (opsional) */
  #itemTable {
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
  }

  /* ✅ Perbaiki Search, Length, Info, Pagination */
  #itemTable_wrapper .dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.875rem;
  }

  #itemTable_wrapper .dataTables_length select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.875rem;
  }

  #itemTable_wrapper .dataTables_info {
    font-size: 0.75rem;  /* Tailwind text-xs */
    color: #6b7280;      /* Tailwind gray-500 */
  }

  #itemTable_wrapper .dataTables_paginate {
    font-size: 0.75rem;
  }

  /* ✅ Scroll wrapper */
  .datatable-container {
    overflow-x: auto;
  }

  .select2-container .select2-selection--single {
  min-height: 2.4rem;
  line-height: 1rem;
}

 .defect-select {
  min-height: 1.8rem;
  line-height: 0.8rem;
}

select:disabled {
  background-color: #f3f4f6; /* Tailwind gray-100 */
  color: #9ca3af; /* Tailwind gray-400 */
}



</style>
@push('scripts')
<script>


</script>
@endpush
@endsection

