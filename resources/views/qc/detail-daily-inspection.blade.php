@extends('layouts.app')

@section('title', 'Detail Quality Inspection')
@section('page-title', 'DETAIL QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Detail Quality Inspection')

@section('content')

<div class="space-y-4">
 <div class="w-full bg-white shadow-md rounded-xl px-8 pt-6 pb-10 space-y-6">

    <!-- ===== HEADER ===== -->
    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 flex items-center justify-center
                        bg-indigo-100 text-indigo-600 rounded-lg">
                <i class="fa-solid fa-clipboard-check text-sm"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Daily Inspection Detail
                </h2>
                <p class="text-xs text-gray-500">
                    Inspection information overview
                </p>
            </div>
        </div>
    </div>

    <!-- ===== ROW 1 ===== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="info-field">
            <span class="info-label">Inspection Date</span>
            <div class="info-value">
                {{ $inspection->inspection_date ?? '-' }}
            </div>
        </div>

        <div class="info-field">
            <span class="info-label">Inspection Post</span>
            <div class="info-value">
                {{ $inspection->inspection_post ?? '-' }}
            </div>
        </div>

        <div class="info-field">
            <span class="info-label">Spray Booth</span>
            <div class="info-value">
                {{ $inspection->spraybooth ?? '-' }}
            </div>
        </div>

    </div>


    <!-- ===== ROW 2 ===== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="info-field">
            <span class="info-label">Supplier / Customer</span>
            <div class="info-value">
                {{ optional($inspection->partner)->name ?? '-' }}
            </div>
        </div>

        <div class="info-field">
            <span class="info-label">Part Name</span>
            <div class="info-value">
                {{ $inspection->article->description ?? '-' }}
            </div>
        </div>

    </div>


    <!-- ===== OPTIONAL FIELD ===== -->
    <div id="check_method_container" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">

        <div class="info-field">
            <span class="info-label">Inspection Method</span>
            <div class="info-value">
                {{ $inspection->check_method ?? '-' }}
            </div>
        </div>

        <div id="qty-received-wrapper" class="info-field hidden">
            <span class="info-label">Qty Received</span>
            <div class="info-value">
                {{ $inspection->qty_received ?? '-' }}
            </div>
        </div>

    </div>


    <!-- ===== KPI TOTAL CHECK ===== -->
    <div class="mt-2">
        <div class="bg-indigo-50 border border-indigo-200
                    rounded-xl px-6 py-5
                    flex items-center justify-between">

            <div>
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">
                    Total Check
                </p>

                <p class="text-3xl font-bold text-indigo-700 mt-1">
                    {{ $inspection->total_check ?? 0 }}
                </p>
            </div>

            <i class="fa-solid fa-chart-column text-indigo-400 text-2xl"></i>
        </div>
    </div>

</div>




      <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">
       <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
  <i class="fa-solid fa-circle-exclamation text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    List Defect
  </h2>
</div>


      <!-- Table -->
     <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
 @php
    $totalDefect = $inspection->inspection_defects->sum('qty');
@endphp

<table id="itemTable" class="min-w-full text-sm text-gray-700">

    <thead class="bg-gray-100 border-b border-gray-200">
        <tr>
            <th class="px-3 py-2 text-center font-medium min-w-[20px]">No</th>
            <th class="px-3 py-2 font-medium min-w-[160px]">Defect</th>
            <th class="px-3 py-2 text-center font-medium min-w-[60px]">Qty</th>
            <th class="px-3 py-2 text-center font-medium min-w-[80px]">%</th>
            <th class="px-3 py-2 text-center font-medium min-w-[60px] ok-repair-wrapper">
                OK Repair
            </th>
            <th class="px-3 py-2 font-medium min-w-[180px]">Note</th>
        </tr>
    </thead>

    <tbody id="defectTableBody" class="divide-y divide-gray-100">

        @forelse($inspection->inspection_defects as $index => $defect)

            @php
                $qty = $defect->qty ?? 0;
                $percent = $totalDefect > 0
                    ? ($qty / $totalDefect) * 100
                    : 0;
            @endphp

            <tr class="hover:bg-gray-50 transition">
                <td class="p-2 border text-center">
                    {{ $index + 1 }}
                </td>

                <td class="p-2 border font-medium">
                    {{ $defect->defect->defect ?? '-' }}
                </td>

                <td class="p-2 border text-center font-semibold">
                    {{ $qty }}
                </td>

                <!-- PERCENT -->
                <td class="p-2 border text-center">
                    <div class="flex flex-col items-center gap-1">

                        <span class="text-xs font-semibold text-indigo-600">
                            {{ number_format($percent,1) }}%
                        </span>

                        <!-- mini progress bar -->
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full"
                                 style="width: {{ $percent }}%"></div>
                        </div>

                    </div>
                </td>

                <td class="p-2 border text-center">
                    {{ $defect->ok_repair ?? 0 }}
                </td>

                <td class="p-2 border">
                    {{ $defect->note_defect ?? '-' }}
                </td>
            </tr>

        @empty
            <tr>
                <td class="p-3 border text-center" colspan="6">
                    No defect added
                </td>
            </tr>
        @endforelse

    </tbody>


    <!-- ===== TOTAL FOOTER ===== -->
    <tfoot>
        <tr class="bg-gray-50 font-semibold">
            <td colspan="2" class="p-3 border text-right">
                TOTAL DEFECT
            </td>

            <td class="p-3 border text-center text-red-600">
                {{ $totalDefect }}
            </td>

            <td colspan="3" class="p-3 border"></td>
        </tr>
    </tfoot>

  </table>
</div>

<!-- Inspection Summary -->
<div class="mt-6 flex justify-start">
  <div class="w-full md:w-96 border border-gray-200 bg-white px-2 rounded-md pb-8">

   <div class="flex items-center gap-2 border-b border-gray-200 py-3 px-2 mb-6">
  <i class="fa-solid fa-file text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    Inspection Summary
  </h2>
   </div>

    <div class="divide-y divide-gray-100 text-sm">

      <div class="flex justify-between px-4 py-2 hidden">
  <span class="text-gray-600">Total Defect Qty</span>
  <span class="font-medium text-gray-900">
    <span id="totalDefectQty">0</span>
    <span class="text-gray-500">(<span id="totalDefectPercent">0</span>%)</span>
  </span>
</div>

 <div class="flex justify-between px-4 py-2 border-t border-gray-200 mt-4">
  <span class="text-sm text-gray-600">Total Check</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalCheckDisplay">{{ $inspection->total_check ?? '0' }}</span>
  </span>
</div>


<div class="flex justify-between px-4 py-2">
  <span class="text-sm text-gray-600">Total OK</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalOkDisplay">{{ $inspection->total_ok ?? '0' }}</span>
    <span class="text-gray-500">
      (<span id="totalOkPercent">0</span>%)
    </span>
  </span>
</div>


      <div class="flex justify-between px-4 py-2">
  <span class="text-sm text-gray-600">Total NG</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalNGDisplay">{{ $inspection->total_ng ?? '-' }}</span>
    <span class="text-gray-500">
      (<span id="totalNGPercent">0</span>%)
    </span>
  </span>
</div>


      <div class="flex justify-between px-4 py-2">
  <span id="totalNCLabel" class="text-sm text-gray-600">Total NC / OK Repair</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalNCDisplay">{{ $inspection->total_ok_repair ?? '-' }}</span>
    <span class="text-gray-500">
      (<span id="totalNCPercent">0</span>%)
    </span>
  </span>
</div>

     <div id="totalPTWrapper" class="flex justify-between px-4 py-2">
  <span class="text-gray-600">Total Pass Through</span>
  <span id="totalPTDisplay" class="font-medium text-gray-900">0</span>
</div>

      <div class="flex justify-between px-4 py-2">
        <span class="text-gray-600">Pass Rate</span>
        <span id="passRate" class="font-medium text-gray-900">0</span>
      </div>

        <div class="flex justify-between px-4 py-2">
        <span id="passTroughLabel" class="text-gray-600">Pass Trough / Performance</span>
        <span id="passTroughDisplay" class="font-medium text-gray-900">0</span>
      </div>
    </div>
  </div>
</div>

<hr class="mt-8">
      <!-- Buttons -->
      <div class="flex flex-col md:flex-row gap-2 mt-4">
        <button id="resetBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Back
        </button>
        <button type="submit" id="submitBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
          <i data-feather="save" class="h-4 w-4"></i> Print
        </button>
      </div>

    </form>
  </div>
</div>
<style>
     .info-field{
            background: linear-gradient(to bottom,#f9fafb,#ffffff);
            border:1px solid #e5e7eb;
            border-radius:12px;
            padding:14px 16px;
            transition:.2s;
        }

        .info-field:hover{
            border-color:#6366f1;
            box-shadow:0 4px 10px rgba(0,0,0,0.06);
        }

        .info-label{
            font-size:11px;
            font-weight:600;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:.05em;
        }

        .info-value{
            margin-top:4px;
            font-size:14px;
            font-weight:600;
            color:#111827;
        }

</style>
@push('scripts')
<script>
  
  $(document).ready(function () {
     toggleOkRepair(); // 🔥 INI WAJIB
 
    const $totalCheck = $('[data-info="total-check"]');
    const $totalOk = $('[data-info="total-ok"]');
    const $totalNg = $('[data-info="total-ng"]');
    const $totalOkRepair = $('[data-info="total-ok-repair"]');
 const $passRate = $('[data-info="pass-rate"]');
  const $passTrough = $('[data-info="pass-trough"]');
    const $ngRate = $('[data-info="ng-rate"]');
    const $okRepairRate = $('[data-info="ok-repair-rate"]');

    function updateRates() {
      const totalCheck = parseFloat($totalCheck.text()) || 0;
      const totalOk = parseFloat($totalOk.text()) || 0;
      const totalNg = parseFloat($totalNg.text()) || 0;
      const totalOkRepair = parseFloat($totalOkRepair.text()) || 0;

     const passRate = totalCheck
  ? (((totalOk + totalOkRepair) / totalCheck) * 100).toFixed(0)
  : 0;
      const passTrough = totalCheck
  ? ((totalOk / totalCheck) * 100).toFixed(0)
  : 0;
     const ngRate = totalCheck 
    ? ((totalNg / totalCheck) * 100).toFixed(0) 
    : 0;
      const okRepairRate = totalCheck ? ((totalOkRepair / totalCheck) * 100).toFixed(0) : 0;

      $passRate.text(passRate + '%');
      $passTrough.text(passTrough + '%');
      $ngRate.text(ngRate + '%');
      $okRepairRate.text(okRepairRate + '%');
    }

    updateRates();
  });

 function toggleOkRepair() {
    const post = $('#inspection_post').val()?.trim();

    // Row summary untuk hide/show
    const $okRepairSummaryRows = $('.ok-repair-summary-row');

    // Label KPI
    const $passTroughLabel = $('[data-label="pass-trough-label"]');

    // Wrapper input OK Repair
    const $okRepairWrapper = $('.ok-repair-wrapper');

    if (post === 'Incoming') {

        // Show input OK Repair
        $okRepairWrapper.removeClass('hidden');
        $okRepairWrapper.find('input')
            .prop('required', true)
            .prop('disabled', false);

        // Show summary rows
        $okRepairSummaryRows.removeClass('hidden');

        // KPI label
        $passTroughLabel.text('Performance');

    } else {

        // Hide input OK Repair
        $okRepairWrapper.addClass('hidden');
        $okRepairWrapper.find('input')
            .prop('required', false)
            .prop('disabled', true)
            .val('');

        // Hide summary rows
        $okRepairSummaryRows.addClass('hidden');

        // KPI label
        if (post === 'Unloading') {
            $passTroughLabel.text('Pass Through');
        } else {
            $passTroughLabel.text('Performance');
        }
    }
}


</script>

    @endpush
    @endsection
