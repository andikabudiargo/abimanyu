@extends('layouts.app')

@section('title', 'Detail Quality Inspection')
@section('page-title', 'DETAIL QUALITY INSPECTION')
@section('breadcrumb-item', 'Quality Control')
@section('breadcrumb-active', 'Detail Quality Inspection')

@section('content')

<div class="space-y-4">
 <div class="w-full bg-white shadow-md rounded-xl px-8 pt-6 pb-10 space-y-6">

  <div class="flex flex-col md:flex-row md:items-center md:justify-between
            gap-4 border-b border-gray-200 pb-4 mb-6">

    <!-- LEFT : TITLE -->
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 flex items-center justify-center
                    bg-indigo-100 text-indigo-600 rounded-lg shrink-0">
            <i class="fa-solid fa-clipboard-check text-sm"></i>
        </div>

        <div>
            <h2 class="text-base md:text-lg font-semibold text-gray-800">
                Daily Inspection Detail
            </h2>
            <p class="text-xs text-gray-500">
                Inspection information overview
            </p>
        </div>
    </div>


    <!-- RIGHT : META INFO -->
    <div class="grid grid-cols-2 md:flex md:items-center
                gap-3 md:gap-8 text-sm">

        <!-- Operator -->
        <div class="text-left md:text-right">
            <p class="text-gray-400 text-[10px] md:text-xs uppercase tracking-wide">
                Operator
            </p>
            <p class="font-semibold text-gray-700 text-sm">
                {{ $inspection->user->name ?? '-' }}
            </p>
        </div>

        <!-- Created At -->
        <div class="text-left md:text-right">
            <p class="text-gray-400 text-[10px] md:text-xs uppercase tracking-wide">
                Created At
            </p>
            <p class="font-semibold text-gray-700 text-sm">
                {{ \Carbon\Carbon::parse($inspection->created_at)->format('d M Y H:i') }}
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

        <div id="spraybooth-wrapper" class="info-field">
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
                    {{ $inspection->total_check ?? 0 }} PCS
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
                    {{ $defect->category ?? '-' }} - {{ $defect->defect->defect ?? '-' }}
                </td>

                <td class="p-2 border text-center font-semibold">
                    {{ $qty }}
                </td>

                <!-- PERCENT -->
                <td class="p-2 border text-center">
                    <div class="flex flex-col items-center gap-1">

                        <span class="text-xs font-semibold text-indigo-600">
                            {{ number_format($percent,0) }}%
                        </span>

                        <!-- mini progress bar -->
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full"
                                 style="width: {{ $percent }}%"></div>
                        </div>

                    </div>
                </td>

               <td class="p-2 border text-center ok-repair-wrapper">
    {{ $defect->ok_repair ?? 0 }}
</td>


                <td class="p-2 border">
                    {{ $defect->note_defect ?? '-' }}
                </td>
            </tr>

        @empty
            <tr>
                <td class="p-3 border text-center no-data-cell" colspan="6">
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

        <!-- spacer footer -->
        <td class="p-3 border total-footer-spacer" colspan="3"></td>
    </tr>
</tfoot>


  </table>
</div>

<!-- Inspection Summary -->
<div class="mt-6 flex flex-col md:flex-row gap-6 items-stretch">

  <!-- SUMMARY -->
  <div class="w-full md:w-96 border border-gray-200 bg-white px-2 rounded-md pb-8">


   <div class="flex items-center gap-2 border-b border-gray-200 py-3 px-2 mb-6">
  <i class="fa-solid fa-file text-indigo-700 text-sm"></i>

  <h2 class="text-base font-semibold text-indigo-700 tracking-wide">
    Inspection Summary
  </h2>
   </div>

    <div class="divide-y divide-gray-100 text-sm">

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
    <span id="totalNGDisplay">{{ $inspection->total_ng ?? '0' }}</span>
    <span class="text-gray-500">
      (<span id="totalNGPercent">0</span>%)
    </span>
  </span>
</div>


      <div class="flex justify-between px-4 py-2">
  <span id="totalNCLabel" class="text-sm text-gray-600">Total NC / OK Repair</span>
  <span class="text-sm font-semibold text-gray-900">
    <span id="totalNCDisplay">{{ $inspection->total_ok_repair ?? '0' }}</span>
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

 <!-- STAMP -->
<div class="w-full md:flex-1 flex justify-center md:items-center">

    <div id="qcStamp"
        class="hidden relative w-full max-w-xl
               border-2 rounded-xl shadow-lg
               bg-white/80 backdrop-blur-sm
               px-8 py-6">

        <div class="flex items-center gap-6">

            <!-- ICON AREA -->
            <div class="flex items-center justify-center
                        w-20 h-20 rounded-lg bg-gray-50 border">
                <i id="qcStampIcon"
                   class="fa-solid text-4xl"></i>
            </div>

            <!-- TEXT AREA -->
            <div class="flex flex-col">
                <span class="text-xs tracking-wider text-gray-500 uppercase">
                    Quality Control Result
                </span>

                <div id="qcStampText"
                     class="text-2xl text-gray-900 font-bold tracking-wide mt-1">
                </div>

                <span class="text-sm text-gray-500 mt-1">
                    Based on QC Inspection
                </span>
            </div>

        </div>

        <!-- subtle background accent -->
        <div class="absolute right-2 top-4 opacity-10 text-7xl font-black select-none">
            QC PASSED
        </div>

    </div>

</div>

</div>  

<hr class="mt-8">
      <!-- Buttons -->
      <div class="flex flex-col md:flex-row gap-2 mt-4">
        <a href="{{ route('qc.inspections.index') }}" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
          <i data-feather="refresh-cw" class="h-4 w-4"></i> Back
</a>
         <!-- Buttons <button type="button" id="printBtn" class="w-full md:w-28 flex items-center justify-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded shadow">
          <i data-feather="printer" class="h-4 w-4"></i> Print
        </button>-->
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
    updateTotalNG();
    updateTotalOK();
    updateNcOrOkRepair();
    updateTotalPassThrough();
    updatePassRate();
    updatePassTrough();
    updateQCStamp();
     
  

   // ambil value dari blade
    let inspectionPost = @json($inspection->inspection_post ?? '');

    if (inspectionPost === 'Incoming') {

        // tampilkan
        $('#check_method_container').removeClass('hidden');
        $('#qty-received-wrapper').removeClass('hidden');

        // sembunyikan spray booth
        $('#spraybooth-wrapper').addClass('hidden');

    } else {

        // selain incoming
        $('#check_method_container').addClass('hidden');
        $('#qty-received-wrapper').addClass('hidden');

        $('#spraybooth-wrapper').removeClass('hidden');
    }

  });

     
// GLOBAL SCOOPE
 const $checkMethod        = $('#check_method');
  const $qtyReceiving       = $('#qty_received');
  const $totalCheck         = $('#total_check');
  const $totalCheckDisplay  = $('#totalCheckDisplay');

  const $postSelect         = $('#inspection_post');

  const $totalNGDisplay     = $('#totalNGDisplay');
  const $totalNGPercent     = $('#totalNGPercent');

  const $totalOkDisplay     = $('#totalOkDisplay');
  const $totalOkPercent     = $('#totalOkPercent');

  const $totalNCLabel       = $('#totalNCLabel');
  const $totalNCDisplay     = $('#totalNCDisplay');
  const $totalNCPercent     = $('#totalNCPercent');

  const $totalPTWrapper     = $('#totalPTWrapper');
  const $totalPTDisplay     = $('#totalPTDisplay');

  const $passRate = $('#passRate');

  const $passTroughLabel = $('#passTroughLabel');
  const $passTroughDisplay = $('#passTroughDisplay');


 function toggleOkRepair() {

    const post = $('#inspection_post').text()?.trim();

    const $okRepairCols = $('.ok-repair-wrapper');
    const $okRepairSummaryRows = $('.ok-repair-summary-row');
    const $passTroughLabel = $('[data-label="pass-trough-label"]');
    const $noDataCell = $('.no-data-cell');
    const $footerSpacer = $('.total-footer-spacer');

    if (post === 'Incoming') {

        // SHOW COLUMN
        $okRepairCols.removeClass('hidden');

        // colspan normal
        $noDataCell.attr('colspan', 6);
        $footerSpacer.attr('colspan', 3);

        $okRepairSummaryRows.removeClass('hidden');
        $passTroughLabel.text('Performance');

    } else {

        // HIDE COLUMN
        $okRepairCols.addClass('hidden');

        // adjust colspan
        $noDataCell.attr('colspan', 5);
        $footerSpacer.attr('colspan', 2);

        $okRepairSummaryRows.addClass('hidden');

        if (post === 'Unloading') {
            $passTroughLabel.text('Pass Through');
        } else {
            $passTroughLabel.text('Performance');
        }
    }
}

 /* =====================================================
   * TOTAL NG
   * ===================================================== */
  function updateTotalNG() {
    const totalCheck = parseInt($totalCheckDisplay.text()) || 0;
    const totalNG = parseInt($totalNGDisplay.text()) || 0;

    const percent = totalCheck > 0
      ? ((totalNG / totalCheck) * 100).toFixed(0)
      : 0;

    $totalNGPercent.text(percent);
  }


  /* =====================================================
   * TOTAL OK
   * ===================================================== */
 function updateTotalOK() {
    const totalCheck = parseInt($totalCheckDisplay.text()) || 0;
    const totalOK = parseInt($totalOkDisplay.text()) || 0;
    const percent = totalCheck > 0
        ? ((totalOK / totalCheck) * 100).toFixed(0)
        : 0;

    $totalOkPercent.text(percent);
}



  /* =====================================================
   * NC / OK REPAIR
   * ===================================================== */
  function updateNcOrOkRepair() {
    let post = @json($inspection->inspection_post ?? '');
    const totalCheck = parseInt($totalCheckDisplay.text()) || 0;
    const totalNC = parseInt($totalNCDisplay.text()) || 0;

    if (post === 'Incoming') {
    // ================= PERFORMANCE =================
    $totalNCLabel.text('Total OK Repair');

  } else {
    // ================= PASS THROUGH =================
    $totalNCLabel.text('Total NC');
}

    const percent = totalCheck > 0
      ? ((totalNC / totalCheck) * 100).toFixed(0)
      : 0;

    $totalNCPercent.text(percent);
  }

/* =====================================================
   * TOTAL PASS THROUGH
   * ===================================================== */
  function updateTotalPassThrough() {
     let post = @json($inspection->inspection_post ?? '');

    if (post === 'Incoming') {
      $totalPTWrapper.addClass('hidden');
      return;
    }

    $totalPTWrapper.removeClass('hidden');

    const totalCheck = parseInt($totalCheckDisplay.text()) || 0;
    const totalNG    = parseInt($totalNGDisplay.text()) || 0;
    const totalNC    = parseInt($totalNCDisplay.text()) || 0;

    const passThrough = Math.max(
      totalCheck - totalNG - totalNC,
      0
    );

    $totalPTDisplay.text(passThrough);
  }

  /*======================================================
   * PASS RATE
   * ===================================================== */
  function updatePassRate() {
  const totalCheck = parseInt($totalCheckDisplay.text()) || 0;
  const totalOK    = parseInt($totalOkDisplay.text()) || 0;

  let passRate = 0;

  if (totalCheck > 0) {
    passRate = ((totalOK / totalCheck) * 100).toFixed(0);
  }

  $passRate.text(`${passRate}%`);
  }

   /* =====================================================
   * PASS THROUGH
   * ===================================================== */

    function updatePassTrough() {
    let post = @json($inspection->inspection_post ?? '');
    const totalCheck = parseInt($totalCheckDisplay.text()) || 0;

    let numerator = 0;
    let percent   = 0;

  if (totalCheck <= 0) {
    $passTroughDisplay.text('0%');
    return;
  }

  if (post === 'Incoming') {
    // ================= PERFORMANCE =================
    $passTroughLabel.text('Performa');

    const totalOK       = parseInt($totalOkDisplay.text()) || 0;
    const totalOkRepair = parseInt($totalNCDisplay.text()) || 0;

    numerator = totalOK - totalOkRepair;

  } else {
    // ================= PASS THROUGH =================
    $passTroughLabel.text('Pass Through');

    const totalPassThrough =
      parseInt($totalPTDisplay.text()) || 0;

    numerator = totalPassThrough;
  }

  if (numerator < 0) numerator = 0;

  percent = ((numerator / totalCheck) * 100).toFixed(0);
  $passTroughDisplay.text(`${percent}%`);
}

function updateQCStamp() {

    let passRate   = parseFloat($('#passRate').text()) || 0;
    let passThrough = parseFloat($('#passTroughDisplay').text()) || 0;

    const $stamp = $('#qcStamp');
    const $icon  = $('#qcStampIcon');
    const $text  = $('#qcStampText');

    $stamp.removeClass('hidden');

    // reset class warna
    $stamp.removeClass('border-green-500 border-red-500');
    $icon.removeClass('text-green-600 text-red-600');

    if (passRate >= 95 && passThrough >= 65) {

        $stamp.addClass('border-green-500');
        $icon.addClass('fa-circle-check text-green-600');
        $text.text('TARGET ACHIEVED');

    } else {

        $stamp.addClass('border-red-500');
        $icon.addClass('fa-triangle-exclamation text-red-600');
        $text.text('NEED IMPROVEMENT');
    }
}



</script>

    @endpush
    @endsection
