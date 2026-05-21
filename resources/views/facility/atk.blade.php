@extends('layouts.app')

@section('title', 'ATK Managamenet')
@section('page-title', 'DASHBOARD ATK MANAGEMENT')
@section('breadcrumb-item', 'GA Inventory Management')
@section('breadcrumb-active', 'ATK Management')

@section('content')

<div class="bg-white shadow rounded-xl mb-6 overflow-hidden">
  <div class="flex border-b border-gray-200">
    <button id="tab-btn-request"
      onclick="switchTab('request')"
      class="tab-btn active-tab flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
      <i data-feather="bell" class="w-4 h-4"></i>
      Request
    </button>
    <button id="tab-btn-stock"
      onclick="switchTab('stock')"
      class="tab-btn flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
      <i data-feather="package" class="w-4 h-4"></i>
      Stock
    </button>
     <button id="tab-btn-analisa"
      onclick="switchTab('analisa')"
      class="tab-btn flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 transition-colors">
      <i data-feather="bar-chart-2" class="w-4 h-4"></i>
      Analytics
    </button>
  </div>
</div>


<div id="tab-request">
   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter ATK Management</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Request Number</label>
                <input type="text" name="request_number" id="filter-request-number" class="w-full px-3 py-1 text-lg border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Request Date</label>
                 <input type="text" name="request_date" id="filter-date"
    class="w-full border border-gray-300 rounded-lg text-l px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"
    placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off" />
            </div>
                 <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                  <select id="filter-dept" name="department" class=" w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
         <option value="">-- All --</option>
          <option value="2">HRGAIT</option>
       
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>

            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                 <select id="filter-status" name="status" class="status w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">-- All Status --</option>
        <option value="Submitted">Submitted</option>
        <option value="Approved">Approved</option>
        <option value="Returned from SPV">Returned from SPV</option>
        <option value="Returned from MR">Returned from MR</option>
        <option value="Resubmitted">Resubmitted</option>
        <option value="Published">Published</option>
        <option value="Rejected">Rejected</option>
        <!-- tambahkan sesuai kebutuhan -->
    </select>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('facility.atk.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

  {{-- Table Card --}}
  <div class="bg-white shadow rounded-xl p-6 mb-2">

    {{-- View Toggle --}}
    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-300">
      <h2 class="text-sm font-semibold text-gray-800">ATK Request List</h2>
      <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5 gap-0.5" id="viewToggle">
        <button id="btn-summary" onclick="switchView('summary')"
          class="view-btn active-view flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
          <i data-feather="list" class="w-3.5 h-3.5"></i> Summary
        </button>
        <button id="btn-detail" onclick="switchView('detail')"
          class="view-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-xs font-medium transition">
          <i data-feather="layers" class="w-3.5 h-3.5"></i> Detail
        </button>
      </div>
    </div>

    {{-- Summary Table --}}
    <div id="view-summary">
      <div class="w-full overflow-x-auto">
        <table id="atk-table" class="min-w-full text-sm text-left whitespace-nowrap">
          <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
            <tr>
              <th class="px-4 py-2">Action</th>
              <th class="px-4 py-2">Request Number</th>
              <th class="px-4 py-2">Department</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Created By</th>
              <th class="px-4 py-2">Created At</th>
              <th class="px-4 py-2">Approved By</th>
              <th class="px-4 py-2">Approved At</th>
              <th class="px-4 py-2">Rejected By</th>
              <th class="px-4 py-2">Rejected At</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- Detail Table --}}
    <div id="view-detail" class="hidden">
      <div class="w-full overflow-x-auto">
        <table id="atk-detail-table" class="min-w-full text-sm text-left whitespace-nowrap">
          <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
            <tr>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Department</th>
              <th class="px-4 py-2">Description</th>
              <th class="px-4 py-2">Qty</th>
              <th class="px-4 py-2">UoM</th>
              <th class="px-4 py-2">Created By</th>
              <th class="px-4 py-2">Created At</th>
              <th class="px-4 py-2">Approved By</th>
              <th class="px-4 py-2">Approved At</th>
              <th class="px-4 py-2">Rejected By</th>
              <th class="px-4 py-2">Rejected At</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

</div>{{-- end #tab-transfer --}}

{{-- ═══════════════════════════════════════════════════════
     TAB: STOCK
════════════════════════════════════════════════════════════ --}}
<div id="tab-stock" class="hidden">

  <div class="bg-white shadow rounded-xl p-6 mb-6">

    {{-- Header + Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-gray-200">
      <div>
        <h2 class="text-lg font-semibold text-gray-800">Stock ATK</h2>
        <p class="text-xs text-gray-400 mt-0.5">Manajemen Stok Alat Tulis Kantor</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <button onclick="openModalAdjustment()"
          class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg shadow flex items-center gap-1.5 transition">
          <i data-feather="trending-up" class="w-3.5 h-3.5"></i> Adjustment Stock
        </button>
        <button onclick="openModalAddATK()"
          class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2 rounded-lg shadow flex items-center gap-1.5 transition">
          <i data-feather="plus" class="w-3.5 h-3.5"></i> Add New ATK
        </button>
      </div>
    </div>

    {{-- Search + Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
      <div class="relative">
        <i data-feather="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" id="searchATK" placeholder="Cari nama ATK..."
          class="pl-8 pr-4 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 w-52">
      </div>
      <div class="flex items-center gap-2">
        <select id="filterStatus"
          class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-gray-600">
          <option value="">Semua Status</option>
          <option value="safe">Safe</option>
          <option value="critical">Critical</option>
          <option value="empty">Empty</option>
        </select>
        <span class="text-xs text-gray-400" id="tableInfo">— item</span>
      </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg ">
      <table class="w-full text-xs text-left" id="tableATK">
        <thead>
          <tr class=" border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[11px]">
            <th class="px-4 py-3 font-medium w-10 text-center">#</th>
            <th class="px-4 py-3 font-medium w-12 text-center">Foto</th>
            <th class="px-4 py-3 font-medium">Nama ATK</th>
            <th class="px-4 py-3 font-medium text-center">UoM</th>
            <th class="px-4 py-3 font-medium text-center">Min Stock</th>
            <th class="px-4 py-3 font-medium text-center">Status</th>
            <th class="px-4 py-3 font-medium text-center">Stock Awal</th>
            <th class="px-4 py-3 font-medium text-center text-emerald-600">In</th>
            <th class="px-4 py-3 font-medium text-center text-red-500">Out</th>
            <th class="px-4 py-3 font-medium text-center font-semibold text-gray-700">Actual Stock</th>
            <th class="px-4 py-3 font-medium text-center w-28">Action</th>
          </tr>
        </thead>
        <tbody id="tbodyATK" class="divide-y divide-gray-100">
          {{-- Skeleton loader --}}
          @for ($i = 0; $i < 5; $i++)
          <tr class="animate-pulse">
            <td class="px-4 py-3 text-center"><div class="h-3 w-4 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="w-8 h-8 bg-gray-200 rounded-lg mx-auto"></div></td>
            <td class="px-4 py-3"><div class="h-3 w-32 bg-gray-200 rounded"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-10 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-8 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-5 w-14 bg-gray-200 rounded-full mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-8 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-8 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-8 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-3 w-10 bg-gray-200 rounded mx-auto"></div></td>
            <td class="px-4 py-3 text-center"><div class="h-6 w-20 bg-gray-200 rounded mx-auto"></div></td>
          </tr>
          @endfor
        </tbody>
      </table>
    </div>

    {{-- Empty state --}}
    <div id="emptyATK" class="hidden py-16 text-center">
      <i data-feather="inbox" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
      <p class="text-sm font-medium text-gray-400">Belum ada data ATK</p>
      <p class="text-xs text-gray-300 mt-1">Klik "Add New ATK" untuk menambahkan data</p>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100" id="paginationATK">
      <p class="text-xs text-gray-400" id="paginationInfo"></p>
      <div class="flex gap-1" id="paginationButtons"></div>
    </div>

  </div>

</div>{{-- end #tab-stock --}}

{{-- Desktop: center modal; Mobile: bottom sheet --}}
<div id="modalAddATK" class="hidden fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-[1050] p-0 sm:p-6">
  <div class="bg-white w-full sm:max-w-3xl sm:rounded-xl rounded-t-2xl shadow-2xl flex flex-col
              max-h-[90dvh] sm:max-h-[80vh]">

    {{-- Handle (mobile only) --}}
    <div class="flex justify-center pt-3 pb-1 sm:hidden">
      <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center border-b border-dashed py-3 px-6">
      <div>
        <h2 class="text-sm font-semibold text-gray-900">Add New ATK</h2>
        <p class="text-xs text-gray-500">Tambahkan ATK Baru untuk dikelola</p>
      </div>
      <button onclick="closeModalAddATK()" class="text-gray-400 hover:text-red-500 text-xl">&times;</button>
    </div>

    {{-- Body --}}
    <div class="overflow-y-auto px-6 pb-2 flex-1">
      <form id="formAddATK" class="space-y-4 text-sm py-4">
        @csrf
    <input type="hidden" id="atk_id" name="atk_id">

  <div class="grid grid-cols-2 gap-4">
<div class="col-span-2">
  <label class="block text-gray-700 font-medium mb-1">Foto</label>
  <div class="flex gap-3 items-start">

    {{-- Preview 1:1 — lebih besar, di kiri --}}
    <div class="relative w-36 h-36 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
      <div id="previewPlaceholder" class="flex flex-col items-center gap-1 text-gray-300">
        <i class="ti ti-photo text-3xl"></i>
        <span class="text-[10px]">Preview 1:1</span>
      </div>
      <img id="previewImg" src="" alt="Preview" class="w-full h-full object-cover hidden">
      <button type="button" id="removeBtn" onclick="removePhoto()"
        class="absolute top-1 right-1 w-5 h-5 bg-black/50 text-white rounded-full text-xs hidden items-center justify-center">&times;</button>
    </div>

    {{-- Upload Zone + hint --}}
    <div class="flex-1 flex flex-col gap-1">
      <div id="uploadZone"
        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition"
        onclick="document.getElementById('photoInput').click()">
        <div class="text-gray-400 text-2xl mb-1"><i class="ti ti-photo-up"></i></div>
        <p class="text-xs font-medium text-gray-700">Klik atau seret foto ke sini</p>
        <input type="file" id="photoInput" name="photo" class="hidden" accept="image/jpeg,image/png,image/webp">
      </div>
      {{-- Hint di bawah upload zone --}}
      <p class="text-[11px] text-gray-400">Format: JPG, PNG, WEBP &nbsp;·&nbsp; Maks. 2 MB</p>
      <p id="photoError" class="text-xs text-red-500 hidden"></p>
    </div>

  </div>
</div>
    <!-- APD Name jadi colspan 2 -->
    <div class="col-span-2">
      <label class="block text-gray-700 font-medium mb-1">Nama ATK <small class="text-red-600"> *</small></label>
      <input type="text" name="name" placeholder="Pensil Dermatografi..."
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Stock Awal</label>
      <input type="number" name="initial_stock" placeholder="0"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Minimal Stock</label>
      <input type="number" name="min_stock" placeholder="0"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
         <p class="text-[11px] text-gray-400">Minimal stock digunakan untuk warning stock</p>
    </div>
    

    <div>
      <label class="block text-gray-700 font-medium mb-1">UoM <small class="text-red-600"> *</small></label>
      <input type="text" name="uom" placeholder="PCS"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
    </div>
    
  </div>
</form>

    </div>
<hr>
  {{-- Footer --}}
    <div class="flex justify-end gap-3 border-t bg-white px-6 py-4">
      <button type="button" onclick="closeModalAddATK()"
        class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">Cancel</button>
      <button type="submit" form="formAddATK" id="btnSaveATK"
        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Save</button>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL: ADJUSTMENT STOCK
════════════════════════════════════════════════════════════ --}}
<div id="modalAdjustmentStock" class="hidden fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-[1050] p-0 sm:p-6">
  <div class="bg-white w-full sm:max-w-5xl sm:rounded-xl rounded-t-2xl shadow-2xl flex flex-col max-h-[90dvh] sm:max-h-[85vh]">

    {{-- Handle (mobile only) --}}
    <div class="flex justify-center pt-3 pb-1 sm:hidden">
      <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center border-b border-dashed py-3 px-6 flex-shrink-0">
      <div>
        <h2 class="text-sm font-semibold text-gray-900">Adjustment Stock</h2>
        <p class="text-xs text-gray-500">Tambah atau kurangi stok ATK secara manual</p>
      </div>
      <button onclick="closeModalAdjustment()" class="text-gray-400 hover:text-red-500 text-xl leading-none">&times;</button>
    </div>

     

    {{-- Body --}}
    <div class="overflow-y-auto px-6 pb-4 pt-4 flex-1">
         <form id="formAdjustment" class="py-4 space-y-4 text-sm">
        @csrf
    
        {{-- Adjustment Type + Reason --}}
        <div class="grid grid-cols-1 gap-4">

          {{-- Type --}}
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Tipe Adjustment</label>
            <div class="flex gap-2" id="adjustmentTypeGroup">
              <button type="button" data-type="in"
                onclick="setAdjustmentType('in')"
                class="adj-type-btn flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border-2 text-xs font-medium transition
                       border-emerald-500 bg-emerald-50 text-emerald-700">
                <i data-feather="plus-circle" class="w-3.5 h-3.5"></i> IN — Tambah Stock
              </button>
              <button type="button" data-type="out"
                onclick="setAdjustmentType('out')"
                class="adj-type-btn flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border-2 text-xs font-medium transition
                       border-gray-200 bg-white text-gray-500 hover:border-gray-300">
                <i data-feather="minus-circle" class="w-3.5 h-3.5"></i> OUT — Kurangi Stock
              </button>
            </div>
            <input type="hidden" name="type" id="adjustmentType" value="in">
          </div>

          {{-- Reason --}}
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Alasan Adjustment</label>
            <input type="text" name="reason" id="adjustmentReason" required
              placeholder="cth: Pembelian baru, Koreksi opname, Rusak/hilang..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400">
            <p id="error_reason" class="field-error text-[11px] text-red-500 mt-1 hidden"></p>
          </div>

        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-3 pt-6 pb-6">
          <span class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Adjustment Item</span>
          <div class="flex-1 border-t border-gray-100"></div>
        </div>

        {{-- Item Rows --}}
        <div id="adjustmentRows" class="space-y-2.5 mb-4">
          {{-- Row awal diisi via JS --}}
        </div>

        {{-- Add Row --}}
        <button type="button" onclick="addAdjustmentRow()"
          class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg border border-dashed border-gray-300
                 text-xs text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50/50 transition">
          <i data-feather="plus" class="w-3.5 h-3.5"></i> Tambah Item
        </button>

</form>
    </div>

    {{-- Footer --}}
    <div class="flex justify-between items-center gap-3 border-t bg-white px-6 py-4 flex-shrink-0 rounded-b-xl">
      <p class="text-xs text-gray-400" id="adjRowCount">0 item dipilih</p>
      <div class="flex gap-2">
        <button type="button" onclick="closeModalAdjustment()"
          class="px-4 py-2 rounded-lg border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 transition">
          Cancel
        </button>
        <button type="submit" form="formAdjustment" id="btnSaveAdjustment"
          class="px-5 py-2 bg-teal-600 text-white text-xs font-medium rounded-lg hover:bg-teal-700 shadow transition flex items-center gap-1.5">
          <i data-feather="check" class="w-3.5 h-3.5"></i> Adjust Stock
        </button>
      </div>
    </div>
  </div>
</div>

<template id="tmplAdjRow">
  <div class="adj-row bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5" data-row>

    <div class="flex items-start gap-2">

      {{-- Nomor --}}
      <div class="flex-shrink-0 flex flex-col w-10">
        <span class="text-[10px] text-gray-400 leading-tight mb-0.5">&nbsp;</span>
        <span class="text-[11px] font-medium text-gray-400 row-number py-1">#1</span>
      </div>

      {{-- Select ATK --}}
      <div class="flex-1 min-w-0 flex flex-col">
        <span class="text-[10px] text-gray-400 leading-tight mb-0.5">Pilih ATK</span>
        <select class="adj-select-atk w-full text-xs" name="items[__IDX__][atk_id]" required>
          <option value="">— Cari & pilih ATK —</option>
        </select>
      </div>

      {{-- Current --}}
      <div class="flex-shrink-0 flex flex-col items-center w-14">
        <span class="text-[10px] text-gray-400 leading-tight mb-0.5">Current</span>
        <div class="w-full h-7 rounded-md py-1 text-center">
          <span class="text-xs font-semibold text-yellow-500 row-current-stock">—</span>
        </div>
      </div>

      {{-- Adj --}}
      <div class="flex-shrink-0 flex flex-col items-center w-14">
        <span class="text-[10px] text-gray-400 leading-tight mb-0.5">Adj</span>
        <input type="number" name="items[__IDX__][qty]" min="1" value="1" required
          class="row-qty h-7 w-full text-center text-xs
                 bg-white border border-gray-200 rounded-md
                 focus:outline-none focus:ring-1 focus:ring-blue-400
                 py-1 leading-tight">
      </div>

      {{-- After --}}
      <div class="flex-shrink-0 flex flex-col items-center w-14">
        <span class="text-[10px] text-gray-400 leading-tight mb-0.5">After</span>
        <div class="w-full h-7 rounded-md px-1 py-1 text-center">
          <span class="text-xs font-semibold row-after-stock text-gray-700 row-after-wrap">—</span>
        </div>
      </div>

      {{-- Delete --}}
      <div class="flex-shrink-0 flex flex-col items-center">
        <span class="text-[10px] text-transparent leading-tight mb-0.5">·</span>
        <button type="button" onclick="removeAdjustmentRow(this)"
          class="w-6 h-6 flex items-center justify-center rounded-lg
                 bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition">
          <i data-feather="trash-2" class="w-3 h-3"></i>
        </button>
      </div>

    </div>
  </div>
</template>


{{-- ═══════════════════════════════════════════════════════
     MODAL: MOVEMENT ATK
════════════════════════════════════════════════════════════ --}}
<div id="modalMovement" class="hidden fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-[1050] p-0 sm:p-6">
  <div class="bg-white w-full sm:max-w-6xl sm:rounded-xl rounded-t-2xl shadow-2xl flex flex-col max-h-[90dvh] sm:max-h-[90vh]">

    {{-- Handle (mobile only) --}}
    <div class="flex justify-center pt-3 pb-1 sm:hidden">
      <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center border-b border-dashed py-3 px-6 flex-shrink-0">
      <div>
        <h2 class="text-sm font-semibold text-gray-900" id="movementModalTitle">Movement ATK</h2>
        <p class="text-xs text-gray-500">Riwayat keluar masuk stok ATK</p>
      </div>
      <button onclick="closeModalMovement()" class="text-gray-400 hover:text-red-500 text-xl leading-none">&times;</button>
    </div>

    {{-- Filter Bar --}}
    <div class="flex-shrink-0 px-6 py-3 border-b border-gray-100 bg-gray-50/50">
      <div class="flex flex-wrap items-end gap-3">

        {{-- Date Range --}}
        <div class="flex flex-col gap-1">
          <label class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Start Date</label>
          <input type="date" id="mvmStartDate"
            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">End Date</label>
          <input type="date" id="mvmEndDate"
            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
        </div>

        {{-- Type --}}
        <div class="flex flex-col gap-1">
          <label class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Type</label>
          <select id="mvmType"
            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white text-gray-600">
            <option value="">Semua Type</option>
            <option value="in">IN</option>
            <option value="out">OUT</option>
          </select>
        </div>

        {{-- Source --}}
        <div class="flex flex-col gap-1">
          <label class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Source</label>
          <select id="mvmSource"
            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white text-gray-600">
            <option value="">Semua Source</option>
            <option value="adjustment">Adjustment</option>
            <option value="request">Request</option>
          </select>
        </div>

        {{-- Actions --}}
        <div class="flex items-end gap-2 ml-auto">
          <button onclick="applyMovementFilter()"
            class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition">
            <i data-feather="filter" class="w-3.5 h-3.5"></i> Filter
          </button>
          <button onclick="resetMovementFilter()"
            class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 hover:bg-gray-100 text-gray-600 text-xs rounded-lg transition">
            <i data-feather="x" class="w-3.5 h-3.5"></i> Reset
          </button>
          <button onclick="exportMovementExcel()"
            class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-lg transition">
            <i data-feather="download" class="w-3.5 h-3.5"></i> Export Excel
          </button>
        </div>

      </div>
    </div>

    {{-- Summary Strip --}}
    <div class="flex-shrink-0 px-6 py-2.5 border-b border-gray-100 flex items-center gap-6">
      <div class="flex items-center gap-2">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Total IN</span>
        <span class="text-xs font-semibold text-emerald-600" id="mvmSumIn">—</span>
      </div>
      <div class="w-px h-3 bg-gray-200"></div>
      <div class="flex items-center gap-2">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Total OUT</span>
        <span class="text-xs font-semibold text-red-500" id="mvmSumOut">—</span>
      </div>
      <div class="w-px h-3 bg-gray-200"></div>
      <div class="flex items-center gap-2">
        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Balance</span>
        <span class="text-xs font-semibold text-gray-700" id="mvmSumBalance">—</span>
      </div>
      <div class="ml-auto text-[11px] text-gray-400" id="mvmRowCount">— records</div>
    </div>

    {{-- Table --}}
    <div class="overflow-y-auto flex-1 px-6 py-4">
      <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-xs text-left" id="tableMovement">
          <thead>
            <tr class="border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[10px] bg-gray-50">
              <th class="px-3 py-2.5 font-medium w-8 text-center">#</th>
              <th class="px-3 py-2.5 font-medium">Tanggal</th>
              <th class="px-3 py-2.5 font-medium text-center">Type</th>
              <th class="px-3 py-2.5 font-medium text-center">Source</th>
              <th class="px-3 py-2.5 font-medium text-center">Stock Awal</th>
              <th class="px-3 py-2.5 font-medium text-center">Qty</th>
              <th class="px-3 py-2.5 font-medium text-center">Balance</th>
              <th class="px-3 py-2.5 font-medium">PIC</th>
              <th class="px-3 py-2.5 font-medium">Received By</th>
              <th class="px-3 py-2.5 font-medium">Catatan</th>
            </tr>
          </thead>
          <tbody id="tbodyMovement" class="divide-y divide-gray-100">
          </tbody>
        </table>
      </div>

      {{-- Empty state --}}
      <div id="emptyMovement" class="hidden py-16 text-center">
        <i data-feather="activity" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
        <p class="text-sm font-medium text-gray-400">Belum ada riwayat movement</p>
        <p class="text-xs text-gray-300 mt-1">Movement akan muncul setelah ada adjustment atau request</p>
      </div>

      {{-- Skeleton --}}
      <div id="skeletonMovement" class="hidden space-y-2 mt-2">
        @for ($i = 0; $i < 6; $i++)
        <div class="animate-pulse flex gap-3 px-3 py-2">
          <div class="h-3 w-4 bg-gray-200 rounded"></div>
          <div class="h-3 w-20 bg-gray-200 rounded"></div>
          <div class="h-3 w-10 bg-gray-200 rounded"></div>
          <div class="h-3 w-16 bg-gray-200 rounded"></div>
          <div class="h-3 w-10 bg-gray-200 rounded ml-auto"></div>
        </div>
        @endfor
      </div>
    </div>

  </div>
</div>



{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#doc-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#doc-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Paksa Select2 container tingginya konsisten */
.adj-select-atk + .select2-container .select2-selection--single {
    height: 30px !important;
    display: flex;
    align-items: center;
}
.adj-select-atk + .select2-container .select2-selection__rendered {
    line-height: 30px !important;
    font-size: 12px;
    padding-left: 8px;
}
.adj-select-atk + .select2-container .select2-selection__arrow {
    height: 30px !important;
}

/* Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
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
#doc-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#doc-table th, #doc-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#doc-table td, #doc-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#doc-scroll-wrapper {
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

 /* ── Tab navigation ── */
  .tab-btn {
    color: #6b7280;
    border-bottom-color: transparent;
    border-bottom-width: 2px;
  }
  .tab-btn:hover { color: #1d4ed8; background: #f8faff; }
  .tab-btn.active-tab {
    color: #1d4ed8;
    border-bottom-color: #3b82f6;
    font-weight: 600;
  }

</style>
<script>
function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

/* ================================================================
   TAB NAVIGATION
================================================================ */
let activeTab = 'request';

function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tab-request').classList.toggle('hidden', tab !== 'request');
    document.getElementById('tab-stock').classList.toggle('hidden', tab !== 'stock');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active-tab'));
    document.getElementById('tab-btn-' + tab).classList.add('active-tab');
    feather.replace();
    if (tab === 'request') {
        if (typeof summaryTable !== 'undefined') summaryTable.columns.adjust();
        if (typeof detailTable  !== 'undefined') detailTable.columns.adjust();
    }
}

// ✏️ UBAH — openModalAddATK reset dulu sebelum buka (biar tidak sisa data edit)
function openModalAddATK() {
    resetFormATK();
    $('#atk_id').val('');
    $('#modalATKTitle').text('Add New ATK');
    $('#modalATKSubtitle').text('Tambah ATK Baru');
    $('#modalAddATK').removeClass('hidden');
}

function closeModalAddATK() {
    $('#modalAddATK').addClass('hidden');
    resetFormATK();
}

/* ================================================================
   PHOTO UPLOAD & PREVIEW
================================================================ */
const MAX_MB  = 2;
const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

document.getElementById('photoInput').addEventListener('change', e => {
    handleFile(e.target.files[0]);
});

const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-blue-500', 'bg-blue-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-blue-500', 'bg-blue-50'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('border-blue-500', 'bg-blue-50');
    handleFile(e.dataTransfer.files[0]);
});

function handleFile(file) {
    const err = document.getElementById('photoError');
    err.classList.add('hidden');
    if (!file) return;
    if (!ALLOWED.includes(file.type)) return showPhotoError('Format tidak didukung. Gunakan JPG, PNG, atau WEBP.');
    if (file.size > MAX_MB * 1024 * 1024) return showPhotoError(`Ukuran melebihi ${MAX_MB} MB.`);
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('previewImg').src = ev.target.result;
        document.getElementById('previewImg').classList.remove('hidden');
        document.getElementById('previewPlaceholder').classList.add('hidden');
        document.getElementById('removeBtn').classList.remove('hidden');
        document.getElementById('removeBtn').classList.add('flex');
    };
    reader.readAsDataURL(file);
}

function removePhoto() {
    document.getElementById('previewImg').src = '';
    document.getElementById('previewImg').classList.add('hidden');
    document.getElementById('previewPlaceholder').classList.remove('hidden');
    document.getElementById('removeBtn').classList.add('hidden');
    document.getElementById('photoInput').value = '';
}

function showPhotoError(msg) {
    const el = document.getElementById('photoError');
    el.textContent = msg;
    el.classList.remove('hidden');
}

/* ================================================================
   SUBMIT FORM — Store & Update (✏️ UBAH: gabung jadi satu handler)
================================================================ */
$('#formAddATK').on('submit', function (e) {
    e.preventDefault();

    const id     = $('#atk_id').val();
    const isEdit = id !== '' && id !== undefined && id !== '0';

    const photoFile = document.getElementById('photoInput').files[0];
    if (photoFile) {
        if (!ALLOWED.includes(photoFile.type)) return showPhotoError('Format tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        if (photoFile.size > MAX_MB * 1024 * 1024) return showPhotoError(`Ukuran melebihi ${MAX_MB} MB.`);
    }

    const formData = new FormData(this);

    // ✅ TAMBAH — method spoofing untuk update
    if (isEdit) {
        formData.append('_method', 'POST');
    }

    const url = isEdit
        ? `/facility/atk/${id}`
        : '{{ route("facility.atk.store") }}';

    const $btn = $('#btnSaveATK');
    $btn.prop('disabled', true).text('Menyimpan...');

    $.ajax({
        url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (res) {
            closeModalAddATK();
            showToast('success', res.message ?? (isEdit ? 'ATK berhasil diperbarui.' : 'ATK berhasil ditambahkan.'));
            loadTableATK();
        },
        error: function (xhr) {
            const res = xhr.responseJSON;
            if (xhr.status === 422) {
                showValidationErrors(res.errors ?? {});
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message ?? 'Terjadi kesalahan, coba lagi.' });
            }
        },
        complete: function () {
            $btn.prop('disabled', false).text('Save');
        }
    });
});

/* ================================================================
   ✅ TAMBAH — Open Edit: load data ke modal
================================================================ */
function openEditATK(id) {
    $.ajax({
        url: `/facility/atk/${id}/edit`,
        method: 'GET',
        success: function (res) {
            const d = res.data;

            $('#atk_id').val(d.id);
            $('#modalATKTitle').text('Edit ATK');
            $('#modalATKSubtitle').text('Perbarui data ATK');

            $('#formAddATK [name="name"]').val(d.name);
            $('#formAddATK [name="uom"]').val(d.uom);
            $('#formAddATK [name="min_stock"]').val(d.min_stock);
            $('#formAddATK [name="initial_stock"]').val(d.initial_stock);

            // Tampilkan foto existing
            if (d.photo_url) {
                $('#previewImg').attr('src', d.photo_url).removeClass('hidden');
                $('#previewPlaceholder').addClass('hidden');
                $('#removeBtn').removeClass('hidden').addClass('flex');
            } else {
                removePhoto();
            }

            $('#modalAddATK').removeClass('hidden');
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memuat data ATK.' });
        }
    });
}

function showValidationErrors(errors) {
    $('.field-error').text('').addClass('hidden');
    $('input, select, textarea').removeClass('border-red-500');
    $.each(errors, function (field, messages) {
        $(`[name="${field}"]`).addClass('border-red-500');
        const $err = $(`#error_${field}`);
        if ($err.length) $err.text(messages[0]).removeClass('hidden');
        if (field === 'photo') showPhotoError(messages[0]);
    });
}

function resetFormATK() {
    $('#formAddATK')[0].reset();
    $('#atk_id').val('');
    removePhoto();
    $('.field-error').text('').addClass('hidden');
    $('input, select, textarea').removeClass('border-red-500');
}

/* ================================================================
   ADJUSTMENT STOCK
================================================================ */
let adjRowIndex   = 0;
let adjType       = 'in';
let atkOptions    = []; // cache dari atkData

function openModalAdjustment() {
    adjType = 'in';
    setAdjustmentType('in');
    $('#adjustmentReason').val('');
    $('#adjustmentRows').html('');
    adjRowIndex = 0;
    addAdjustmentRow();
    $('#modalAdjustmentStock').removeClass('hidden');
    feather.replace();
}

function closeModalAdjustment() {
    $('#modalAdjustmentStock').addClass('hidden');
    // Destroy semua Select2 agar tidak leak
    $('#adjustmentRows .adj-select-atk').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
    });
    $('#adjustmentRows').html('');
    adjRowIndex = 0;
}

function setAdjustmentType(type) {
    adjType = type;
    $('#adjustmentType').val(type);

    const $btns = $('.adj-type-btn');
    $btns.each(function () {
        const t = $(this).data('type');
        if (t === type) {
            if (type === 'in') {
                $(this).removeClass('border-gray-200 bg-white text-gray-500')
                       .addClass('border-emerald-500 bg-emerald-50 text-emerald-700');
            } else {
                $(this).removeClass('border-gray-200 bg-white text-gray-500')
                       .addClass('border-red-400 bg-red-50 text-red-600');
            }
        } else {
            $(this).removeClass('border-emerald-500 bg-emerald-50 text-emerald-700 border-red-400 bg-red-50 text-red-600')
                   .addClass('border-gray-200 bg-white text-gray-500');
        }
    });

    // Recalculate semua baris
    $('[data-row]').each(function () { recalcRow($(this)); });
}

function addAdjustmentRow() {
    const idx      = adjRowIndex++;
    const tmpl     = document.getElementById('tmplAdjRow').innerHTML
                        .replace(/__IDX__/g, idx);
    const $row     = $(tmpl);
    $row.find('.row-number').text(`#${$('#adjustmentRows [data-row]').length + 1}`);
    $('#adjustmentRows').append($row);

    // Populate options
    const $sel = $row.find('.adj-select-atk');
    atkOptions.forEach(atk => {
        $sel.append(new Option(atk.name, atk.id, false, false));
    });

    // Init Select2
    $sel.select2({
        placeholder: '— Cari & pilih ATK —',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalAdjustmentStock'),
    });

    // On change → update stock info
    $sel.on('change', function () {
        recalcRow($(this).closest('[data-row]'));
    });

    // On qty change → update after adj
    $row.find('.row-qty').on('input', function () {
        recalcRow($(this).closest('[data-row]'));
    });

    feather.replace();
    updateRowCount();
}

function removeAdjustmentRow($btn) {
    const $row = $($btn).closest('[data-row]');
    const $sel = $row.find('.adj-select-atk');
    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
    $row.remove();
    // Renumber
    $('#adjustmentRows [data-row]').each(function (i) {
        $(this).find('.row-number').text(`Item #${i + 1}`);
    });
    updateRowCount();
}

function recalcRow($row) {
    const atkId   = $row.find('.adj-select-atk').val();
    const qty     = parseInt($row.find('.row-qty').val()) || 0;
    const atk     = atkOptions.find(a => a.id == atkId);

    const $current = $row.find('.row-current-stock');
    const $after   = $row.find('.row-after-stock');
    const $wrap    = $row.find('.row-after-wrap');

    if (!atk) {
        $current.text('—');
        $after.text('—').removeClass('text-emerald-600 text-red-500 text-gray-700');
        $wrap.removeClass('border-emerald-200 bg-emerald-50 border-red-200 bg-red-50').addClass('border-gray-200 bg-white');
        return;
    }

    const current = (atk.initial_stock ?? 0) + (atk.total_in ?? 0) - (atk.total_out ?? 0);
    const after   = adjType === 'in' ? current + qty : current - qty;

    $current.text(current);

    const isIn   = adjType === 'in';
    const symbol = isIn ? `<i data-feather="arrow-up-right" class="w-3 h-3 inline"></i> +${qty}` : `<i data-feather="arrow-down-right" class="w-3 h-3 inline"></i> -${qty}`;

    $after.html(`${after}`)
          .removeClass('text-emerald-600 text-red-500 text-gray-700')
          .addClass(after > current ? 'text-emerald-600' : after < current ? 'text-red-500' : 'text-gray-700');

    $wrap.removeClass('border-gray-200 bg-white border-emerald-200 bg-emerald-50 border-red-200 bg-red-50')
         .addClass(isIn ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50');

    feather.replace();
}

function updateRowCount() {
    const n = $('#adjustmentRows [data-row]').length;
    $('#adjRowCount').text(`${n} item dipilih`);
}

$('#formAdjustment').on('submit', function (e) {
    e.preventDefault();

    const type   = $('#adjustmentType').val();
    const reason = $('#adjustmentReason').val().trim();

    if (!type) {
        Swal.fire({ icon: 'warning', title: 'Tipe belum dipilih' });
        return;
    }

    const items = [];
    const atkSet = new Set();
    let valid = true;

    $('#adjustmentRows [data-row]').each(function () {
        const atkId = $(this).find('.adj-select-atk').val();
        const qty   = Number($(this).find('.row-qty').val());

        if (!atkId || !Number.isInteger(qty) || qty < 1) {
            valid = false;
            return false;
        }

        if (atkSet.has(atkId)) {
            valid = false;
            Swal.fire({
                icon: 'warning',
                title: 'Duplicate item',
                text: 'ATK tidak boleh duplikat.'
            });
            return false;
        }

        atkSet.add(atkId);
        items.push({ atk_id: atkId, qty });
    });

    if (!valid || items.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Periksa kembali',
            text: 'Pastikan semua item valid.'
        });
        return;
    }

    const $btn = $('#btnSaveAdjustment');
    $btn.prop('disabled', true)
        .html('<i data-feather="loader" class="w-3.5 h-3.5 inline animate-spin"></i> Menyimpan...');
    feather.replace();

    $.ajax({
        url: '{{ route("facility.atk.adjustment") }}',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ type, reason, items }),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

        success: function (res) {
            closeModalAdjustment();
            showToast('success', res.message ?? 'Adjustment berhasil disimpan.');
            loadTableATK();
        },

        error: function (xhr) {
            let msg = 'Terjadi kesalahan.';

            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
            }

            Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
        },

        complete: function () {
            $btn.prop('disabled', false)
                .html('<i data-feather="check" class="w-3.5 h-3.5 inline"></i> Adjust Stock');
            feather.replace();
        }
    });
});

/* ================================================================
   LOAD & RENDER TABEL ATK
================================================================ */
let atkData    = [];
let currentPage = 1;
const perPage   = 10;

function loadTableATK() {
    $.ajax({
        url: '{{ route("facility.atk.data.stock") }}',
        method: 'GET',
        success: function (res) {
            atkData = res.data ?? [];
            atkOptions = res.data ?? [];  // ← tambahkan ini
            currentPage = 1;
            renderTableATK();
        },
        error: function () {
            $('#tbodyATK').html(`
                <tr><td colspan="11" class="text-center py-10 text-xs text-gray-400">
                    Gagal memuat data.
                    <button onclick="loadTableATK()" class="text-blue-600 underline">Coba lagi</button>
                </td></tr>
            `);
        }
    });
}

function getFilteredData() {
    const search = $('#searchATK').val().toLowerCase();
    const status = $('#filterStatus').val();
    return atkData.filter(item => {
        const matchSearch = item.name.toLowerCase().includes(search);
        const matchStatus = !status || getStatusKey(item) === status;
        return matchSearch && matchStatus;
    });
}

function getStatusKey(item) {
    const balance = (item.initial_stock ?? 0) + (item.total_in ?? 0) - (item.total_out ?? 0);
    if (balance <= 0) return 'empty';
    if (balance <= (item.min_stock ?? 0)) return 'critical';
    return 'safe';
}

function getStatusBadge(item) {
    const key = getStatusKey(item);
    const map = {
        safe:     { label: 'Safe',     cls: 'bg-emerald-50 text-emerald-700 border border-emerald-200' },
        critical: { label: 'Critical', cls: 'bg-amber-50 text-amber-700 border border-amber-200' },
        empty:    { label: 'Empty',    cls: 'bg-red-50 text-red-600 border border-red-200' },
    };
    const s = map[key];
    return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium ${s.cls}">
                <span class="w-1.5 h-1.5 rounded-full ${key === 'safe' ? 'bg-emerald-500' : key === 'critical' ? 'bg-amber-500' : 'bg-red-500'}"></span>
                ${s.label}
            </span>`;
}

function renderTableATK() {
    const filtered = getFilteredData();
    const total    = filtered.length;
    const start    = (currentPage - 1) * perPage;
    const paged    = filtered.slice(start, start + perPage);

    $('#tableInfo').text(`${total} item`);

    if (total === 0) {
        $('#tbodyATK').html('');
        $('#emptyATK').removeClass('hidden');
        $('#paginationATK').addClass('hidden');
        return;
    }

    $('#emptyATK').addClass('hidden');
    $('#paginationATK').removeClass('hidden');

    const rows = paged.map((item, i) => {
        const balance  = (item.initial_stock ?? 0) + (item.total_in ?? 0) - (item.total_out ?? 0);
        const photoUrl = item.photo_url
            ? `<img src="${item.photo_url}" class="w-8 h-8 rounded-lg object-cover mx-auto border border-gray-200">`
            : `<div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mx-auto text-gray-300">
                 <i data-feather="image" class="w-4 h-4"></i>
               </div>`;

        return `
        <tr class="hover:bg-gray-50/60 transition-colors group">
          <td class="px-4 py-3 text-center text-gray-400">${start + i + 1}</td>
          <td class="px-4 py-3 text-center">${photoUrl}</td>
          <td class="px-4 py-3"><span class="font-medium text-gray-800">${item.name}</span></td>
          <td class="px-4 py-3 text-center text-gray-500">${item.uom ?? '—'}</td>
          <td class="px-4 py-3 text-center text-gray-500">${item.min_stock ?? 0}</td>
          <td class="px-4 py-3 text-center">${getStatusBadge(item)}</td>
          <td class="px-4 py-3 text-center text-gray-500">${item.initial_stock ?? 0}</td>
          <td class="px-4 py-3 text-center font-medium text-emerald-600">+${item.total_in ?? 0}</td>
          <td class="px-4 py-3 text-center font-medium text-red-500">-${item.total_out ?? 0}</td>
          <td class="px-4 py-3 text-center">
            <span class="font-semibold text-gray-800 text-sm">${balance}</span>
          </td>
          <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-1">
              <button onclick="openMovement(${item.id})" title="Movement"
                class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                <i data-feather="activity" class="w-3.5 h-3.5"></i>
              </button>
              <button onclick="openEditATK(${item.id})" title="Edit"
                class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition">
                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>
              </button>
              <button onclick="confirmDeleteATK(${item.id}, '${item.name}')" title="Delete"
                class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                <i data-feather="trash-2" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </td>
        </tr>`;
    }).join('');

    $('#tbodyATK').html(rows);
    feather.replace();
    renderPagination(total);
}

function renderPagination(total) {
    const totalPages = Math.ceil(total / perPage);
    const start      = (currentPage - 1) * perPage + 1;
    const end        = Math.min(currentPage * perPage, total);

    $('#paginationInfo').text(`Menampilkan ${start}–${end} dari ${total} item`);

    let btns = '';
    for (let p = 1; p <= totalPages; p++) {
        const active = p === currentPage
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50';
        btns += `<button onclick="goPageATK(${p})" class="w-7 h-7 text-xs rounded-lg border ${active} transition">${p}</button>`;
    }
    $('#paginationButtons').html(btns);
}

function goPageATK(page) {
    currentPage = page;
    renderTableATK();
}

$('#searchATK, #filterStatus').on('input change', function () {
    currentPage = 1;
    renderTableATK();
});

/* ================================================================
   DELETE
================================================================ */
function confirmDeleteATK(id, name) {
    Swal.fire({
        title: 'Hapus ATK?',
        html: `<span class="text-sm text-gray-600">Item <strong>${name}</strong> akan dihapus permanen.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: `/facility/atk/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                showToast('success', res.message ?? 'ATK berhasil dihapus.');
                loadTableATK();
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Tidak dapat menghapus data.' });
            }
        });
    });
}

/* ================================================================
   MOVEMENT ATK
================================================================ */
let movementAtkId   = null;
let movementRawData = [];
let movementFiltered = [];

function openMovement(id) {
    movementAtkId = id;

    // Ambil nama ATK dari atkData untuk judul modal
    const atk = atkData.find(a => a.id == id);
    $('#movementModalTitle').text(`Movement — ${atk ? atk.name : 'ATK'}`);
    $('#movementModalSubtitle').text(`${atk ? atk.uom ?? '' : ''} · Riwayat keluar masuk stok`);

    // Reset filter
    resetMovementFilter(false);

    $('#modalMovement').removeClass('hidden');
    feather.replace();

    loadMovement();
}

function closeModalMovement() {
    $('#modalMovement').addClass('hidden');
    movementAtkId   = null;
    movementRawData = [];
    movementFiltered = [];
}

function loadMovement() {
    $('#tbodyMovement').html('');
    $('#emptyMovement').addClass('hidden');
    $('#skeletonMovement').removeClass('hidden');
    updateMovementSummary([]);

    $.ajax({
        url: `/facility/atk/${movementAtkId}/movements`,
        method: 'GET',
        success: function (res) {
            movementRawData = res.data ?? [];
            applyMovementFilter();
        },
        error: function () {
            $('#skeletonMovement').addClass('hidden');
            $('#tbodyMovement').html(`
                <tr><td colspan="10" class="text-center py-10 text-xs text-gray-400">
                    Gagal memuat data.
                    <button onclick="loadMovement()" class="text-blue-600 underline">Coba lagi</button>
                </td></tr>
            `);
        }
    });
}

function applyMovementFilter() {
    const startDate = $('#mvmStartDate').val();
    const endDate   = $('#mvmEndDate').val();
    const type      = $('#mvmType').val();
    const source    = $('#mvmSource').val();

    movementFiltered = movementRawData.filter(row => {
        const rowDate = row.date; // format YYYY-MM-DD
        if (startDate && rowDate < startDate) return false;
        if (endDate   && rowDate > endDate)   return false;
        if (type   && row.type   !== type)   return false;
        if (source && row.source !== source) return false;
        return true;
    });

    renderMovement(movementFiltered);
}

function resetMovementFilter(reload = true) {
    $('#mvmStartDate').val('');
    $('#mvmEndDate').val('');
    $('#mvmType').val('');
    $('#mvmSource').val('');
    if (reload && movementAtkId) applyMovementFilter();
}

function renderMovement(rows) {
    $('#skeletonMovement').addClass('hidden');

    if (rows.length === 0) {
        $('#tbodyMovement').html('');
        $('#emptyMovement').removeClass('hidden');
        updateMovementSummary([]);
        $('#mvmRowCount').text('0 records');
        return;
    }

    $('#emptyMovement').addClass('hidden');
    updateMovementSummary(rows);
    $('#mvmRowCount').text(`${rows.length} records`);

    // Hitung rolling stock
    // Ambil initial_stock dari atkData
    const atk     = atkData.find(a => a.id == movementAtkId);
    const initial = atk ? (atk.initial_stock ?? 0) : 0;

    // Sort ascending by date+id untuk rolling stock
    const sorted = [...rows].sort((a, b) => {
        if (a.date !== b.date) return a.date.localeCompare(b.date);
        return a.id - b.id;
    });

    // Hitung running balance per baris
    let runningBalance = initial;

    // Buat map id → {openingStock, balance}
    const balanceMap = {};
    sorted.forEach(row => {
        const opening = runningBalance;
        if (row.type === 'in') {
            runningBalance += row.qty;
        } else {
            runningBalance -= row.qty;
        }
        balanceMap[row.id] = { opening, balance: runningBalance };
    });

    const html = rows.map((row, i) => {
        const bm       = balanceMap[row.id] ?? { opening: '—', balance: '—' };
        const typeBadge = row.type === 'in'
            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                 <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> IN
               </span>`
            : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-600 border border-red-200">
                 <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> OUT
               </span>`;

        const sourceBadge = row.source === 'adjustment'
            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100">
                 <i data-feather="repeat" class="w-2.5 h-2.5"></i> Adjustment
               </span>`
            : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-600 border border-purple-100">
                 <i data-feather="bell" class="w-2.5 h-2.5"></i> Request
               </span>`;

        const qtyDisplay = row.type === 'in'
            ? `<span class="font-semibold text-emerald-600">+${row.qty}</span>`
            : `<span class="font-semibold text-red-500">-${row.qty}</span>`;

        const balanceColor = typeof bm.balance === 'number' && bm.balance <= 0
            ? 'text-red-500' : 'text-gray-800';

        return `
        <tr class="hover:bg-gray-50/60 transition-colors">
          <td class="px-3 py-2.5 text-center text-gray-400">${i + 1}</td>
          <td class="px-3 py-2.5 text-gray-600">${row.date}</td>
          <td class="px-3 py-2.5 text-center">${typeBadge}</td>
          <td class="px-3 py-2.5 text-center">${sourceBadge}</td>
          <td class="px-3 py-2.5 text-center text-gray-500">${bm.opening}</td>
          <td class="px-3 py-2.5 text-center">${qtyDisplay}</td>
          <td class="px-3 py-2.5 text-center font-semibold ${balanceColor}">${bm.balance}</td>
          <td class="px-3 py-2.5 text-gray-500">${row.distributed_by ?? '<span class="text-gray-300">—</span>'}</td>
          <td class="px-3 py-2.5 text-gray-500">${row.received_by ?? '<span class="text-gray-300">—</span>'}</td>
          <td class="px-3 py-2.5 text-gray-500 max-w-[160px] truncate" title="${row.reason ?? ''}">${row.reason ?? '<span class="text-gray-300">—</span>'}</td>
        </tr>`;
    }).join('');

    $('#tbodyMovement').html(html);
    feather.replace();
}

function updateMovementSummary(rows) {
    const totalIn  = rows.filter(r => r.type === 'in').reduce((s, r) => s + r.qty, 0);
    const totalOut = rows.filter(r => r.type === 'out').reduce((s, r) => s + r.qty, 0);
    const atk      = atkData.find(a => a.id == movementAtkId);
    const initial  = atk ? (atk.initial_stock ?? 0) : 0;
    const balance  = initial + totalIn - totalOut;

    $('#mvmSumIn').text(`+${totalIn}`);
    $('#mvmSumOut').text(`-${totalOut}`);
    $('#mvmSumBalance').text(balance);
}

function exportMovementExcel() {
    if (!movementAtkId) return;

    const params = new URLSearchParams({
        start_date : $('#mvmStartDate').val(),
        end_date   : $('#mvmEndDate').val(),
        type       : $('#mvmType').val(),
        source     : $('#mvmSource').val(),
    });

    window.location.href = `/facility/atk/${movementAtkId}/movements/export?${params}`;
}

/* ================================================================
   DATATABLES — ATK REQUEST
================================================================ */
let summaryTable, detailTable;

$(function () {
    summaryTable = $('#atk-table').DataTable({
        processing  : true,
        serverSide  : true,
        ajax: {
            url  : '{{ route("facility.atk.data.summary") }}',
            data : d => Object.assign(d, getFilters()),
        },
        columns: [
            { data: 'action',         orderable: false, searchable: false, width: '80px' },
            { data: 'request_number'  },
            { data: 'department'      },
            { data: 'status'          },
            { data: 'created_by'      },
            { data: 'created_at'      },
            { data: 'approved_by'     },
            { data: 'approved_at'     },
            { data: 'rejected_by'     },
            { data: 'rejected_at'     },
        ],
        order       : [[5, 'desc']],
        pageLength  : 25,
        dom         : 'rtip',   // tanpa search box bawaan DT (kita pakai filter form sendiri)
        language    : { processing: '<span class="text-xs text-gray-400">Memuat data...</span>' },
    });

    detailTable = $('#atk-detail-table').DataTable({
        processing : true,
        serverSide : true,
        ajax: {
            url  : '{{ route("facility.atk.data.detail") }}',
            data : d => Object.assign(d, getFilters()),
        },
        columns: [
            { data: 'created_at'      },
            { data: 'status'          },
            { data: 'department'      },
            { data: 'atk_name'        },
            { data: 'qty'             },
            { data: 'uom'             },
            { data: 'created_by'      },
            { data: 'approved_by'     },
            { data: 'approved_at'     },
            { data: 'rejected_by'     },
            { data: 'rejected_at'     },
        ],
        order      : [[0, 'desc']],
        pageLength : 25,
        dom        : 'rtip',
        language   : { processing: '<span class="text-xs text-gray-400">Memuat data...</span>' },
    });

    // Init setelah feather render
    summaryTable.on('draw', () => feather.replace());
    detailTable.on('draw',  () => feather.replace());
});

// Kumpulkan nilai filter dari form
function getFilters() {
    return {
        request_number : $('#filter-request-number').val(),
        department     : $('#filter-dept').val(),
        status         : $('#filter-status').val(),
        request_date   : $('#filter-date').val(),
    };
}

// Submit filter form
$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    summaryTable.ajax.reload();
    detailTable.ajax.reload();
});

// Switch view summary/detail
function switchView(view) {
    $('#view-summary').toggleClass('hidden', view !== 'summary');
    $('#view-detail').toggleClass('hidden',  view !== 'detail');
    $('#btn-summary, #btn-detail').removeClass('active-view');
    $('#btn-' + view).addClass('active-view');

    // Adjust columns saat tab ditampilkan
    if (view === 'summary') summaryTable.columns.adjust();
    if (view === 'detail')  detailTable.columns.adjust();
}

// Cancel request
function cancelRequest(id, number) {
    Swal.fire({
        title   : 'Cancel Request?',
        html    : `Request <strong>${number}</strong> akan dibatalkan.`,
        icon    : 'warning',
        showCancelButton    : true,
        confirmButtonColor  : '#ef4444',
        cancelButtonText    : 'Batal',
        confirmButtonText   : 'Ya, Cancel',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url    : `/facility/atk/request/${id}/cancel`,
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: res => {
                showToast('success', res.message ?? 'Request berhasil dibatalkan.');
                summaryTable.ajax.reload(null, false);
                detailTable.ajax.reload(null, false);
            },
            error  : xhr => Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.' }),
        });
    });
}

/* ================================================================
   DROPDOWN
================================================================ */
let openDropdown = null;

function toggleDropdown(id, event) {
  const trigger  = event.currentTarget;
  const existing = document.getElementById('global-dropdown');
  if (existing) { existing.remove(); if (openDropdown === id) { openDropdown = null; return; } }

  const tpl = document.getElementById(id);
  if (!tpl) return;

  const dd = document.createElement('div');
  dd.id = 'global-dropdown';
  dd.className = 'absolute z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700';
  dd.innerHTML  = tpl.innerHTML;
  document.body.appendChild(dd);

  const rect   = trigger.getBoundingClientRect();
  dd.style.position = 'fixed';
  dd.style.top  = `${rect.bottom + 4}px`;
  dd.style.left = `${rect.left}px`;
  openDropdown  = id;
}

document.addEventListener('click', function (e) {
  const dd = document.getElementById('global-dropdown');
  if (dd && !dd.contains(e.target) && !e.target.closest('button[data-dropdown-id]')) {
    dd.remove(); openDropdown = null;
  }
});

// Populate atkOptions saat data loaded (tambahkan di dalam loadTableATK success)
// atkOptions = res.data ?? [];  ← tambahkan baris ini di dalam success loadTableATK

loadTableATK();

 
  </script>

@endpush


@endsection