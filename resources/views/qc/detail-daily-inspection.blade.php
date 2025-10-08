@extends('layouts.app')

@section('title', 'Detail Quality Inspection')
@section('page-title', 'DETAIL QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Detail Quality Inspection')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
  <!-- 📘 Sidebar Search Panel -->
  <!-- Sidebar -->
<div class="w-full md:w-1/4 bg-white shadow-lg rounded-2xl p-6 space-y-6">
  <!-- Header -->
  <div>
    <h2 class="text-lg font-semibold text-gray-700">Inspection Overview</h2>
  </div>

  <!-- Operator Information -->
  <div class="space-y-3">
    <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Operator Information</h3>
    <div class="flex justify-between text-sm text-gray-600">
      <span class="font-medium text-gray-500">Operator Name:</span>
      <span class="text-gray-800">{{ $inspection->user->name }}</span>
    </div>
    <div class="flex justify-between text-sm text-gray-600">
      <span class="font-medium text-gray-500">Shift:</span>
      <span id="shift-label" class="text-gray-800">{{ $inspection->shift }}</span>
    </div>
    <div class="flex justify-between text-sm text-gray-600">
      <span class="font-medium text-gray-500">Inspection Date:</span>
      <span id="inspection-date" class="text-gray-800">{{ $inspection->inspection_date }}</span>
    </div>
  </div>

  <!-- Part Information -->
  <div class="space-y-3">
    <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Part Information</h3>
    <div class="flex justify-between items-start text-sm gap-2 text-gray-600">
      <span class="font-medium text-gray-500 whitespace-nowrap">Part Name:</span>
      <span data-info="part-name" class="text-gray-800 text-right max-w-[70%] break-words">{{ $inspection->article->description }}</span>
    </div>
    <div class="flex justify-between items-start text-sm gap-2 text-gray-600">
      <span class="font-medium text-gray-500 whitespace-nowrap">Supplier:</span>
      <span data-info="supplier" class="text-gray-800 text-right max-w-[70%] break-words">{{ $inspection->supplier->name }}</span>
    </div>
  </div>

  <!-- Summary Inspection -->
  <div class="space-y-3">
    <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Summary Inspection</h3>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">

      <!-- Total Check -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-gray-500">
          <i data-feather="search" class="w-4 h-4"></i>
          <span class="font-medium">Total Check</span>
        </div>
        <span data-info="total-check" class="text-gray-800 font-semibold">{{ $inspection->total_check }}</span>
      </div>

      <!-- Total OK -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-green-600">
          <i data-feather="check-circle" class="w-4 h-4"></i>
          <span class="font-medium">Total OK</span>
        </div>
        <span data-info="total-ok" class="text-green-600 font-semibold">{{ $inspection->total_ok }}</span>
      </div>

      <!-- Total OK Repair -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-yellow-500">
          <i data-feather="tool" class="w-4 h-4"></i>
          <span class="font-medium">Total OK Repair</span>
        </div>
        <span data-info="total-ok-repair" class="text-yellow-500 font-semibold">{{ $inspection->total_ok_repair }}</span>
      </div>

      <!-- Total NG -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-red-600">
          <i data-feather="x-circle" class="w-4 h-4"></i>
          <span class="font-medium">Total NG</span>
        </div>
        <span data-info="total-ng" class="text-red-600 font-semibold">{{ $inspection->total_ng }}</span>
      </div>

    </div>
  </div>

   <!-- Percentage Inspection -->
  <div class="space-y-3">
    <h3 class="text-md font-semibold text-gray-700 border-b pb-1">Percentage Inspection</h3>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">

      <!-- Total Check -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-green-500">
          <i data-feather="check-circle" class="w-4 h-4"></i>
          <span class="font-medium">Pass Rate</span>
        </div>
        <span data-info="pass-rate" class="text-green-500 font-semibold">0</span>
      </div>


      <!-- Total OK Repair -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-yellow-500">
          <i data-feather="tool" class="w-4 h-4"></i>
          <span class="font-medium">OK Repair</span>
        </div>
        <span data-info="ok-repair-rate" class="text-yellow-500 font-semibold">-</span>
      </div>

      <!-- Total OK -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2 text-red-600">
          <i data-feather="x-circle" class="w-4 h-4"></i>
          <span class="font-medium">NG Rate</span>
        </div>
        <span data-info="ng-rate" class="text-red-600 font-semibold">-</span>
      </div>

    </div>
  </div>
</div>


  <!-- 📦 Main Transfer Panel -->
  <!-- Main Panel -->
<div class="w-full md:w-3/4 bg-white shadow-md rounded-xl p-4 space-y-4">

    <h2 class="text-lg font-semibold text-gray-700">Quality Inspection</h2>
    <form id="inspection-form">
      <!-- 🔢 Nomor Referensi -->
    <div class="flex flex-col gap-4 mb-8">
  <!-- Baris 1 -->
  <div class="flex flex-col md:flex-row gap-4">
    <div class="w-full md:w-1/2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Inspection Post
      </label>
      <input type="text" id="inspection_post"
        class="w-full px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ $inspection->inspection_post }}" readonly>
    </div>

    <div class="w-full md:w-1/2">
      <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">
        Supplier
      </label>
     <input type="text" id="supplier"
        class="w-full px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ $inspection->supplier->name }}" readonly>
    </div>
  </div>

  <!-- Baris 2 -->
  <div class="flex flex-col md:flex-row gap-4">
    <div class="w-full md:w-1/2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Part Name
      </label>
       <input type="text" id="part_name"
        class="w-full px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ $inspection->article->description }}" readonly>
    </div>

    <div class="w-full md:w-1/2">
      <label for="total_check" class="block text-sm font-medium text-gray-700 mb-1">
        Qty Received
      </label>
      <input type="number" name="qty_received" id="qty_received"
        class="w-full px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        value="{{ $inspection->qty_received}}" readonly />
    </div>
  </div>

  <!-- Catatan -->
  <div class="w-full">
    <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
    <textarea id="note" rows="2" value="{{ $inspection->note}}" readonly
      class="mt-1 block w-full border border-gray-300 bg-gray-200 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
  </div>
</div>


      <div class="flex gap-4 mb-4">
    <!-- Check Method -->
  <div id="check_method_container">
    <label for="check_method" class="block text-sm font-medium text-gray-700 mb-1">
      Inspection Method <small class="text-red-600">*</small></label>
  <input type="text" name="check_method" id="check_method"
        class="w-64 px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        value="{{ $inspection->check_method }}" readonly />
  </div>
  <div>
    <label for="total_check" class="block text-sm font-medium text-gray-700 mb-1">
      Total Check
    </label>
    <input type="number" name="total_check" id="total_check"
           class="w-64 px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
           value="{{ $inspection->total_check }}" readonly/>
  </div>
</div>


     <!-- 📋 Tabel Artikel yang Dipindahkan -->
<div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400">
 <table id="itemTable" class="min-w-full bg-white border border-gray-200">
    <thead class="bg-blue-500 text-white">
        <tr>
            <th class="p-2 border">No.</th>
            <th class="p-2 border">Defect</th>
            <th class="p-2 border w-24">Qty</th>
            <th class="p-2 border w-24">OK Repair</th>
            <th class="p-2 border">Note</th>
        </tr>
    </thead>
    <tbody id="defectTableBody">
        @forelse($inspection->inspection_defects as $index => $defect)
        <tr>
            <td class="p-2 border text-center">{{ $index + 1 }}</td>
            <td class="p-2 border">{{ $defect->defect->defect ?? '-' }}</td>
            <td class="p-2 border text-center">{{ $defect->qty ?? 0 }}</td>
            <td class="p-2 border text-center">{{ $defect->ok_repair ?? 0 }}</td>
            <td class="p-2 border">{{ $defect->note ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td class="p-2 border text-center" colspan="5">No defect added</td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>


      <!-- 🎯 Tombol Submit -->
     <hr>
   <div class="flex justify-start space-x-2 mt-4">
   <a href="{{ route('qc.inspections.index') }}" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   ← Back
</a>

<button type="button" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded shadow">
   <i data-feather="printer" class="h-4 w-4"></i>
   Print
</button>
    </form>
  </div>
</div>


</style>
@push('scripts')
<script>
  $(document).ready(function () {
    const $totalCheck = $('[data-info="total-check"]');
    const $totalOk = $('[data-info="total-ok"]');
    const $totalNg = $('[data-info="total-ng"]');
    const $totalOkRepair = $('[data-info="total-ok-repair"]');

    const $passRate = $('[data-info="pass-rate"]');
    const $ngRate = $('[data-info="ng-rate"]');
    const $okRepairRate = $('[data-info="ok-repair-rate"]');

    function updateRates() {
      const totalCheck = parseFloat($totalCheck.text()) || 0;
      const totalOk = parseFloat($totalOk.text()) || 0;
      const totalNg = parseFloat($totalNg.text()) || 0;
      const totalOkRepair = parseFloat($totalOkRepair.text()) || 0;

      const passRate = totalCheck ? ((totalOk / totalCheck) * 100).toFixed(0) : 0;
     const ngRate = totalCheck 
    ? ((totalNg / totalCheck) * 100).toFixed(0) 
    : 0;
      const okRepairRate = totalCheck ? ((totalOkRepair / totalCheck) * 100).toFixed(0) : 0;

      $passRate.text(passRate + '%');
      $ngRate.text(ngRate + '%');
      $okRepairRate.text(okRepairRate + '%');
    }

    updateRates();
  });
</script>

    @endpush
    @endsection
