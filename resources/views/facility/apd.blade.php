@extends('layouts.app')

@section('title', 'APD Management')
@section('page-title', 'APD Management')
@section('breadcrumb-item', 'GA Inventory Management')
@section('breadcrumb-active', 'APD Management')

@section('content')

<!-- === FORM PERHITUNGAN === -->
<div class="mt-4 bg-gray-50 shadow-xl rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300">

  <!-- === NAVIGATION TAB === -->
  <div class="bg-teal-500 text-gray-300 rounded-t-2xl">
    <div class="flex items-center justify-start">
       <button id="tab-dashboard"
        class="tab-btn px-6 py-3 text-sm font-semibold text-white border-b-4 border-transparent hover:text-yellow-100 transition-all duration-300 flex items-center gap-2">
        <i class="fa-solid fa-chart-line"></i>
        <span class="hidden md:block">Dashboard</span>
        <span class="md:hidden">Dashboard</span>
      </button>
      <button id="tab-distribution"
        class="tab-btn px-6 py-3 text-sm font-semibold text-white border-b-4 border-transparent hover:text-yellow-100 transition-all duration-300 flex items-center gap-2">
        <i class="fa-solid fa-people-carry-box"></i>
        <span class="hidden md:block">APD Distribution Management</span>
        <span class="md:hidden">Distribution</span>
      </button>
      <button id="tab-stock"
        class="tab-btn px-6 py-3 text-sm font-semibold text-white border-b-4 border-transparent hover:text-yellow-100 transition-all duration-300 flex items-center gap-2">
        <i class="fa-solid fa-shirt"></i>
        <span class="hidden md:block">APD Stock Management</span>
         <span class="md:hidden">Stock</span>
      </button>
    </div>
  </div>

 <!-- === PANEL DASHBOARD === -->
<div id="dashboardPanel" class="hidden bg-white flex flex-col p-6 gap-4 animate-fadeIn">

    <!-- Grid Chart + List -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">

        <!-- === CARD CHART (2 kolom) === -->
        <div class="md:col-span-2 bg-white p-4 rounded-lg shadow-md border">
            
            <!-- Header Chart + Filter Tahun -->
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-700">Distribusi & Pengembalian APD (Tahunan)</h3>

                <select id="filterYearChart" 
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-400 focus:outline-none">
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
            </div>

            <canvas id="yearlyAPDChart" height="120"></canvas>
        </div>

        <!-- === CARD LIST KARYAWAN (1 kolom) === -->
        <div class="bg-white p-4 rounded-lg shadow-md border h-full">
<div class="flex flex-col mb-2 p-2 bg-white">
    <div class="flex justify-between items-center">
        <div class="flex flex-col">
            <h2 class="text-lg font-bold text-gray-900">List Pergantian APD</h2>
            <p class="text-sm text-gray-500 mt-1">Cek karyawan yang perlu pergantian APD</p>
        </div>
        <span id="apdReminderBadge" class="bg-teal-100 text-teal-800 px-4 py-2 rounded-full font-bold text-lg">
            0
        </span>
    </div>
    <div class="w-24 h-1 bg-teal-500 rounded mt-3"></div>
</div>


            <!-- Filter Tahun & Bulan -->
            <div class="grid grid-cols-2 gap-2 mb-4">
                <select id="filterYearEmployee"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-400 focus:outline-none">
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>

                <select id="filterMonthEmployee"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-400 focus:outline-none">
                    <option value="">Semua Bulan</option>
                    <option value="Jan">Januari</option>
                    <option value="Feb">Februari</option>
                    <option value="Mar">Maret</option>
                    <option value="Apr">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Jun">Juni</option>
                    <option value="Jul">Juli</option>
                    <option value="Agu">Agustus</option>
                    <option value="Sep">September</option>
                    <option value="Okt">Oktober</option>
                    <option value="Nov">November</option>
                    <option value="Des">Desember</option>
                </select>
            </div>

            <ul id="employeeAPDList" class="space-y-3 text-sm">
                <!-- Data akan diisi via jQuery -->
            </ul>

        </div>

    </div>


    
</div>


   <!-- === PANEL DISTRIBUTION === -->
<div id="distributionPanel" class="hidden bg-white flex flex-col p-6 gap-4 animate-fadeIn">

  <!-- HEADER -->
  <div class="flex flex-col md:flex-row border-dotted border-b pb-4 md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="bg-teal-100 p-3 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-people-carry-box text-3xl text-teal-600"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">MANAJEMEN DISTRIBUSI APD</h2>
        <p class="text-sm text-gray-500">Pengelolaan Distribusi APD Karyawan</p>
        <div class="w-20 h-1 bg-teal-600 rounded mt-2"></div>
      </div>
    </div>
 <div class="flex flex-col md:flex-row gap-3">
    <button onclick="openDistribusiModal()"
      class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg px-3 py-2 flex items-center justify-center gap-2 shadow-md transition w-full md:w-36 ">
      <i class="fa-solid fa-hand-holding-hand"></i>
      <span class="font-medium text-sm">Distribution</span>
    </button>
     <button onclick="openReturnModal()()"
      class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg px-3 py-2 flex items-center justify-center gap-2 shadow-md transition w-full md:w-36">
      <i class="fa-solid fa-rotate"></i>
      <span class="font-medium text-sm">Return</span>
    </button>
 </div>
  </div>

  <!-- ACCORDION WRAPPER -->
<div class="mt-1">
 <button onclick="toggleWarningList()"
    class="w-full bg-yellow-50 border border-yellow-300 hover:bg-yellow-100
           rounded-xl px-5 py-4 transition flex items-center justify-between">

   <div class="flex items-center gap-3">
    <div class="text-yellow-700 p-2 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
    </div>

    <div class="text-left leading-tight">
        <p class="text-sm font-bold text-yellow-800">
            PERINGATAN :
        </p>
        <p class="text-xs text-yellow-700">
           Terdapat {{ count($warnings) }} item APD yang 2 bulan lagi akan memasuki atau telah melewati masa pergantian.
        </p>
    </div>
</div>



    <!-- Kanan (Arrow) -->
    <i id="warningArrow"
       class="fa-solid fa-chevron-down text-yellow-700 transition-transform duration-300"></i>
</button>


  <!-- LIST (default: hidden) -->
  <div id="warningList" class="hidden mt-3 bg-yellow-50 border-l-4 border-yellow-600 rounded-lg p-4 shadow-md">
    
    @foreach ($warnings as $w)
      <div class="flex items-center justify-between px-3 py-2 border-b last:border-0 hover:bg-yellow-100 rounded transition">

        <!-- Karyawan -->
        <div class="w-full sm:w-1/3 flex items-center gap-2">
          <span class="h-2 w-2 bg-yellow-500 rounded-full"></span>
          <div>
            <p class="font-semibold text-yellow-900 text-xs">{{ $w['employee']->name }}</p>
            <p class="text-[10px] text-yellow-700">
             {{ $w['employee']->departments->first()->name ?? '-' }}

            </p>
          </div>
        </div>

        <!-- APD -->
        <div class="w-full sm:w-1/3 font-medium text-[13px] text-yellow-900">
          {{ $w['apd']->name }}  
          <span class="text-yellow-700">—</span> 
          {{ $w['qty'] }} {{ $w['uom'] }}
        </div>

        <!-- Tanggal Pergantian -->
        <div class="w-full sm:w-1/3 text-right">
          <span class="px-2 py-1 rounded-lg text-[11px] font-semibold 
            {{ $w['isExpired'] ? 'bg-red-100 text-red-700' : 'bg-yellow-200 text-yellow-800' }}">
              {{ $w['isExpired'] ? 'Lewat Masa Pakai:' : 'Tanggal Pergantian:' }}
              {{ $w['replaceDate'] }}
          </span>
        </div>

      </div>
    @endforeach
 <!-- === BUTTON: OPEN STOCK MODAL === -->
    <div class="mt-4 text-left">
        <button onclick="openWarningModal()" 
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition">
            <i class="fa-solid fa-eye mr-1"></i> Lihat Stok APD
        </button>
    </div>

</div>
</div>

<!-- === FILTER SECTION === -->
<div class="bg-gradient-to-r from-teal-600 to-teal-500 text-white px-6 py-4 rounded-t-2xl shadow">

  <h2 class="text-lg text-white font-semibold mb-4">
    <i class="fa-solid fa-filter mr-2"></i>Filter Employee
  </h2>

  <!-- === GRID === -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- Search -->
    <div class="relative">
      <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
      <input type="text" id="searchEmployee"
        placeholder="Search Name / NIK..."
        class="pl-10 pr-3 py-2 rounded-lg border border-gray-300 text-gray-700 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- Department -->
    <div>
      <select id="filterDepartment"
        class="filterDepartment rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">-- All Deptartments --</option>
        @foreach($departments as $dept)
        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Type Employee -->
    <div>
      <select id="filterTypeEmployee"
        class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="" {{ $typeEmployee == '' ? 'selected' : '' }}>All Type</option>
        <option value="1" {{ $typeEmployee == 1 ? 'selected' : '' }}>Organik</option>
        <option value="2" {{ $typeEmployee == 2 ? 'selected' : '' }}>Non-Organik</option>
      </select>
    </div>

    <!-- Status APD -->
    <div>
      <select id="filterStatusAPD"
        class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">All Status</option>
        <option value="safe">APD Lengkap</option>
        <option value="warning">APD Masuk Masa Pergantian</option>
        <option value="critical">Belum Menerima APD</option>
      </select>
    </div>

  </div>
</div>




<!-- MODAL OVERLAY -->
<div id="warningModal" 
     class="hidden fixed inset-0 z-[1050] flex items-center justify-center bg-black/50">

 <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[80vh] flex flex-col z-[10000]">

     <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6 sticky top-0 z-10">
    <div class="flex items-center gap-4">
      <div class="bg-blue-100 p-3 rounded-2xl flex items-center justify-center">
       <i class="fa-solid fa-box text-3xl text-blue-600"></i>

      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">STOCK APD</h2>
<p class="text-sm text-gray-500">Cek Stock APD yang dibutuhkan untuk pergantian</p>

        <div class="w-20 h-1 bg-blue-600 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeWarningModal()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>

  <!-- FILTER SECTION TITLE -->
<div class="px-6 py-2 bg-white sticky top-[85px] z-20 flex items-center gap-2">
    <i class="fa-solid fa-sliders text-blue-600 text-lg"></i>
    <span class="font-semibold text-gray-700 text-sm">Filter Data APD</span>
</div>


   <!-- FILTER SECTION -->
        <div class="px-6 py-2 bg-white sticky top-[85px] z-10">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                <!-- SEARCH -->
                <input id="searchStock"
                    type="text"
                    placeholder="Cari APD..."
                    class="w-full sm:w-1/2 px-3 py-2 border rounded-lg text-sm"
                    onkeyup="filterTableStock()">

                <!-- FILTER PEMBELIAN -->
                <select id="purchaseFilter"
                    class="w-full sm:w-1/2 px-3 py-2 border rounded-lg text-sm"
                    onchange="filterTableStock()">
                    <option value="">— Semua —</option>
                    <option value="need">Perlu Pembelian</option>
                    <option value="noneed">Tidak Perlu Pembelian</option>
                </select>
            </div>
        </div>

        <!-- TABLE WRAPPER (scrollable) -->
        <div class="flex-1 overflow-auto p-4">

            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full border-t-b border-gray-300 rounded-lg" id="warningTable">
                    <thead class="bg-blue-500  border-t-b text-white text-sm sticky top-0 z-10">
                        <tr>
                            <th class="border-t-b text-left px-3 py-2">APD Perlu Disiapkan</th>
                            <th class="border-t-b px-3 py-2">Jumlah Pergantian</th>
                            <th class="border-t-b px-3 py-2">Stock Tersedia</th>
                            <th class="border-t-b px-3 py-2">Perlu Pembelian</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm">
                        @foreach ($groupedWarnings as $item)
                            <tr>
                                <td class="border-t px-3 py-2 font-semibold">
                                   {{ $item['apd_code'] }} - {{ $item['apd_name'] }}
                                </td>
                                <td class="border-t px-3 py-2 text-red-500 text-center">
                                    {{ $item['qtyPergantian'] }}
                                </td>
                                <td class="border-t px-3 py-2 text-blue-500 text-center">
                                    {{ $item['stockBaru'] }}
                                </td>
                                <td class="border-t px-3 py-2 text-center font-bold
                                    {{ $item['perluPembelian'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item['perluPembelian'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <!-- FOOTER (sticky) -->
        <div class="p-4 border-t rounded-lg bg-white sticky bottom-0 z-10 text-right">
            <button onclick="closeWarningModal()" 
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">
                Close
            </button>

            <a href="https://abimanyugreats.com/purchaseRequests/create"
                target="_blank"
                rel="noopener noreferrer"
                class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white transition text-sm">
                Create PR
            </a>
        </div>

    </div>
</div>



     <!-- Scrollable table -->
  <div class="h-[500px] overflow-y-auto space-y-4 pr-2">
   <div class="mb-3 text-gray-700 font-semibold">
    Total Karyawan: <span id="employeeCount">{{ $employees->count() }}</span>
</div>

    <!-- === LIST DISTRIBUSI PER KARYAWAN (EXPANDABLE) === -->
  @foreach ($employees as $employee)
  @php
      // Ambil relasi distribusi (parent) dari employee
      $distributions = $employee->distributions ?? collect();

      // Ambil distribusi terakhir berdasarkan distribution_date (di apd_distributions)
     $lastDistribution = $distributions
    ->filter(fn($d) => !empty($d->distribution->distribution_date ?? null))
    ->sortByDesc(fn($d) => $d->distribution->distribution_date)
    ->first();


    $nearingReplacement = collect();
$hasApd = false;

foreach ($distributions as $item) {
    $distDate = !empty($item->distribution->distribution_date ?? null)
        ? \Carbon\Carbon::parse($item->distribution->distribution_date)
        : null;

    if (!$distDate) continue;

    // hitung sisa qty (qty - qty_return)
    $sisaQty = ($item->qty ?? 0) - ($item->qty_return ?? 0);

    if ($sisaQty <= 0) continue; // sudah direturn semua, lewati

    $hasApd = true; // ada APD tersisa

    // lifetime APD
    $lifetime = isset($item->apd->lifetime) ? (int) $item->apd->lifetime : 6;

    // hitung tanggal pergantian
    $endDate = $distDate->copy()->addMonths($lifetime);

    // hitung sisa bulan
    $monthsLeft = \Carbon\Carbon::now()->diffInMonths($endDate, false);

    if ($monthsLeft <= 2) {
        $nearingReplacement->push((object) [
            'apd_name' => $item->apd->name ?? '-',
            'distribution_date' => $distDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'remaining_months' => $monthsLeft,
        ]);
    }
}
@endphp

  @php
    $statusTag = '';

    if (!$hasApd) {
        $statusTag = 'critical'; // belum menerima APD
    } elseif ($nearingReplacement->isNotEmpty()) {
        $statusTag = 'warning'; // masuk masa pergantian
    } else {
        $statusTag = 'safe'; // lengkap & aman
    }
@endphp

@php
   $deptIds = $employee->departments->pluck('id')->values();
@endphp

    <div class="employee-card bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden" data-status="{{ $statusTag }}" data-departments='@json($employee->departments->pluck("id")->values())'>
      <button
        class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-teal-50 transition-all duration-200"
        onclick="toggleExpand(this)">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-user text-blue-600 text-xl"></i>
          <div class="text-left">
            <h3 class="text-base font-semibold text-gray-900">
              {{ $employee->nik }} - {{ $employee->name }}
            </h3>
            <p class="text-xs text-gray-500">
           @if ($employee->positions->isNotEmpty())
    {{ $employee->positions->pluck('name')->join(', ') }}
@else
                <span class="text-gray-400 italic">Belum ada departemen</span>
              @endif
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
      @if (!$hasApd)
      <span class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full font-medium">
          {{ $employee->name }} belum menerima APD
      </span>
  @elseif ($nearingReplacement->isNotEmpty())
      <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-medium">
          {{ $employee->name }} punya {{ $nearingReplacement->count() }} APD yang masuk masa pergantian
      </span>
  @else
    <span class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium">
      Last Distribution:
      {{ optional($lastDistribution->distribution)->distribution_date 
      ? \Carbon\Carbon::parse($lastDistribution->distribution->distribution_date)->format('d M Y') 
      : '-' 
}}

  </span>

  @endif
          <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-300"></i>
        </div>
      </button>

    <!-- === DETAIL ITEM APD === -->
    <div class="hidden px-6 py-4 border-t bg-white animate-fadeIn">
      <div class="space-y-2">
       @php
    // Filter distribusi: hanya tampilkan yang qty - qty_return > 0
    $activeDistributions = $employee->distributions->filter(function($distItem) {
        return ($distItem->qty - ($distItem->qty_return ?? 0)) > 0;
    });
@endphp
@forelse ($activeDistributions as $distItem)
 @php
    $replaceDate = null;

    // cek ada tanggal distribusi & lifetime valid
    if (!empty($distItem->distribution->distribution_date) 
        && isset($distItem->apd->lifetime) 
        && is_numeric($distItem->apd->lifetime)) {

        $lifetime = (int) $distItem->apd->lifetime; // pastikan int
        if ($lifetime > 0) {
            try {
                $distDate = \Carbon\Carbon::parse($distItem->distribution->distribution_date);
                $replaceDate = $distDate->copy()->addMonths($lifetime); // gunakan copy() agar aman
            } catch (\Exception $e) {
                $replaceDate = null;
            }
        }
    }

    $today = \Carbon\Carbon::now();
    $bgColor = 'bg-white';

    if ($replaceDate) {
        $diffMonths = $today->diffInMonths($replaceDate, false);

        if ($replaceDate->isPast()) {
            $bgColor = 'bg-red-100';
        } elseif ($diffMonths <= 2) {
            $bgColor = 'bg-yellow-100';
        }
    }
@endphp


          <div
            class="flex flex-wrap items-center justify-between {{ $bgColor }} border border-gray-200 rounded-2xl px-5 py-4 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-300">
            
            <!-- Kiri -->
            <div class="flex items-center gap-4 w-full md:w-1/3">
              <div class="bg-teal-100 p-3 rounded-xl">
               <i class="fa-solid {{ $distItem->apd->icon ?: 'fa-question' }} text-teal-600 text-2xl"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-base">
                  {{ $distItem->apd->name ?? 'APD Tidak Diketahui' }} Kondisi {{ $distItem->apd->conditions ?? 'Kondisi Tidak Diketahui' }}
                </p>
                <p class="text-sm text-gray-500">
                  Qty: {{ $distItem->qty }} {{ $distItem->apd->uom ?? '' }} - 
                  Lifetime: {{ $distItem->apd->lifetime ?? '-' }} Bulan
                </p>
              </div>
            </div>

            <!-- Tengah -->
            <div class="flex flex-col text-sm w-full md:w-1/3 text-gray-700">
              <span>
                <i class="fa-regular fa-calendar text-blue-500 mr-1"></i>
                Tanggal Penerimaan:
                <span class="font-medium">
                  {{ $distItem->distribution->distribution_date ?? '-' }}
                </span>
              </span>
              <span>
                <i class="fa-solid fa-user-tie text-emerald-500 mr-1"></i>
                Diberikan oleh:
                <span class="font-medium">
                  {{ $distItem->distribution->creator->name ?? 'Admin GA' }}
                </span>
              </span>
            </div>

            <!-- Kanan -->
            <div class="flex flex-col text-sm w-full md:w-1/3 text-right">
              @php
                $replaceDate = isset($distItem->distribution->distribution_date, $distItem->apd->lifetime)
                    ? \Carbon\Carbon::parse($distItem->distribution->distribution_date)->addMonths($distItem->apd->lifetime)
                    : null;
              @endphp
              @if ($replaceDate)
                <span class="text-red-600 font-semibold">
                  <i class="fa-solid fa-repeat mr-1"></i>
                  Tanggal Pergantian: {{ $replaceDate->format('Y-m-d') }}
                </span>
              @endif
            </div>
          </div>
        @empty
          <p class="text-gray-400 italic text-center py-3">Karyawan Belum menerima APD</p>
        @endforelse
      </div>
    </div>
  </div>
@endforeach



  </div>
</div>



    <!-- === PANEL STOCK === -->
     
   <div id="stockPanel" class="hidden bg-white flex flex-col p-6 gap-4 animate-fadeIn">
  <!-- === HEADER === -->
  <div class="flex flex-col md:flex-row border-dotted border-b pb-4 md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="bg-teal-100 p-3 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-shirt text-3xl text-teal-500"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">MANAJEMEN STOCK APD</h2>
        <p class="text-sm text-gray-500">Pengendalian dan Pergerakan Stock APD Karyawan</p>
        <div class="w-20 h-1 bg-teal-600 rounded mt-2"></div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row gap-3">
      <button onclick="openAdjustmentModal()"
        class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg px-4 py-2 flex items-center justify-center gap-2 shadow-md transition w-full md:w-auto">
        <i class="fa-solid fa-arrows-rotate"></i>
        <span class="font-medium text-sm">Adjustment Stock</span>
      </button>

      <button onclick="openModalAddAPD()"
        class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2 flex items-center justify-center gap-2 shadow-md transition w-full md:w-auto">
        <i class="fa-solid fa-plus"></i>
        <span class="font-medium text-sm">Add New APD</span>
      </button>
    </div>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 mt-4">

    <!-- CARD TEMPLATE -->
    <div class="bg-white rounded-xl p-4 border border-teal-700 shadow hover:shadow-md transition">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-green-500 uppercase tracking-wide">SAFE</h2>
            <i class="fa-solid fa-shield-heart text-green-500 text-xl"></i>
        </div>
        <p id="cardSafe" class="text-3xl font-extrabold text-gray-800">0</p>

        <div class="flex items-center mt-1 text-sm" id="safeTrend">
            <!-- Auto isi via JS -->
        </div>
    </div>

    <div class="bg-white rounded-xl p-4 border border-teal-700 shadow hover:shadow-md transition">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-yellow-500 uppercase tracking-wide">CRITICAL</h2>
            <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-xl"></i>
        </div>
        <p id="cardCritical" class="text-3xl font-extrabold text-gray-800">0</p>

        <div class="flex items-center mt-1 text-sm" id="criticalTrend"></div>
    </div>

    <div class="bg-white rounded-xl p-4 border border-teal-700 shadow hover:shadow-md transition">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wide">EMPTY</h2>
            <i class="fa-solid fa-box-open text-gray-500 text-xl"></i>
        </div>
        <p id="cardEmpty" class="text-3xl font-extrabold text-gray-800">0</p>

        <div class="flex items-center mt-1 text-sm" id="emptyTrend"></div>
    </div>

    <div class="bg-white rounded-xl p-4 border border-teal-700 shadow hover:shadow-md transition">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-blue-500 uppercase tracking-wide">SECONDHAND</h2>
            <i class="fa-solid fa-recycle text-blue-500 text-xl"></i>
        </div>
        <p id="cardBekas" class="text-3xl font-extrabold text-gray-800">0</p>

        <div class="flex items-center mt-1 text-sm" id="bekasTrend"></div>
    </div>

    <div class="bg-white rounded-xl p-4 border border-teal-700 shadow hover:shadow-md transition">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xs font-bold text-red-500 uppercase tracking-wide">BROKEN</h2>
            <i class="fa-solid fa-explosion text-red-500 text-xl"></i>
        </div>
        <p id="cardRusak" class="text-3xl font-extrabold text-gray-800">0</p>

        <div class="flex items-center mt-1 text-sm" id="rusakTrend"></div>
    </div>

</div>



 <!-- === FILTER SECTION === -->
<div class="bg-gradient-to-r from-teal-600 to-teal-500 text-white px-6 py-4 rounded-t-2xl shadow">

  <h2 class="text-lg text-white font-semibold mb-4">
    <i class="fa-solid fa-filter mr-2"></i>Filter APD
  </h2>

  <!-- === GRID === -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- Search -->
    <div class="col-span-4">
      <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
      <input type="text" id="searchAPD"
        placeholder="Search APD Name / Code..."
        class="pl-10 pr-3 py-2 rounded-lg border border-gray-300 text-gray-700 w-full 
               focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- Status Filter -->
    <div>
      <select id="filterStatus"
        class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full 
               focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">-- All Status --</option>
        <option value="Safe">Safe</option>
        <option value="Critical">Critical</option>
        <option value="Empty">Empty</option>
         <option value="Secondhand">Secondhand</option>
        <option value="Broken">Broken</option>
      </select>
    </div>

    <!-- Condition Filter -->
    <div>
      <select id="filterCondition"
        class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full 
               focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="">-- All Condition --</option>
        <option value="Baru">Baru</option>
        <option value="Bekas">Bekas</option>
        <option value="Rusak">Rusak</option>
      </select>
    </div>
<!-- Filter Tahun -->
    <div>
        <select id="filterTahun"
            class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full 
                   focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">-- Semua Tahun --</option>
            @for ($y = 2023; $y <= now()->year; $y++)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <!-- Filter Bulan -->
    <div>
        <select id="filterBulan"
            class="rounded-lg border border-gray-300 text-gray-700 px-3 py-2 w-full 
                   focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">-- Semua Bulan --</option>
            @foreach ([1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"] as $num => $nama)
                <option value="{{ $num }}">{{ $nama }}</option>
            @endforeach
        </select>
    </div>

  </div>
</div>


<!-- === TABLE STOCK === -->
<div class="relative mt-4">
  <div class="overflow-x-auto overflow-y-auto max-w-full max-h-[500px] border border-gray-200 rounded-xl">
    <table id="apdStockTable" class="min-w-full border-collapse">
      <thead class="bg-green-50 sticky top-0 z-30">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ACTION</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 z-20 bg-green-50">KODE</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NAMA</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">KONDISI</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">MIN. STOCK</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STOCK AWAL</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">MASUK</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">KELUAR</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">BALANCE</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STATUS</th>
          <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">LIFETIME</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 text-sm">
        @foreach ($apds as $index => $apd)
        <tr  class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
          <td class="px-4 py-3 text-center relative">
  <button 
    class="text-gray-700 hover:text-green-600 focus:outline-none"
    onclick="toggleDropdown({{ $apd->id }})"
  >
    <i class="fa-solid fa-ellipsis-vertical"></i>
  </button>

  <!-- DROPDOWN -->
  <div id="dropdown-{{ $apd->id }}" 
       class="hidden absolute right-50 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
    <ul class="text-sm text-gray-700">
      <li>
 <button 
    onclick="openApdMovementModal({{ $apd->id }}, '{{ $apd->code }}', '{{ $apd->name }}', '{{ $apd->conditions }}')"
    class="w-full text-left block px-4 py-2 hover:bg-blue-50 hover:text-blue-600">
    <i class="fa-solid fa-right-left mr-2"></i>Movement
</button>

</li>

      <li>
        <button onclick='openModalEditAPD(@json($apd))' 
           class="w-full text-left block px-4 py-2 hover:bg-green-50 hover:text-green-600">
           <i class="fa-solid fa-pen mr-2"></i>Edit
    </button>
      </li>
       <li>
        <button onclick="deleteAPD({{ $apd->id }})"
                class="w-full text-left px-4 py-2 hover:bg-red-50 hover:text-red-600">
          <i class="fa-solid fa-trash mr-2"></i>Delete
        </button>
      </li>
    </ul>
  </div>
</td>

          <!-- Sticky columns -->
          <td class="px-4 py-3 font-medium text-gray-900 sticky left-0 z-10 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} whitespace-nowrap">
            {{ $apd->code }}
          </td>
          <td class="px-4 py-3 text-gray-600  whitespace-nowrap">
  {{ $apd->name }}
</td>


          <td class="px-4 py-3 text-center font-semibold whitespace-nowrap
    {{ 
    $apd->conditions === 'Baru' ? 'text-blue-500' : (
    $apd->conditions === 'Bekas' ? 'text-yellow-500' : (
    $apd->conditions === 'Rusak' ? 'text-red-500' : 'text-gray-500')) 
}}">
    {{ $apd->conditions ?? '-' }}
</td>


          <td class="px-4 py-3 text-center font-semibold">{{ $apd->min_stock ?? '-' }}</td>
          <td class="px-4 py-3 text-center font-semibold">{{ $apd->initial_stock ?? '-' }}</td>
         <td class="px-4 py-3 text-center text-green-500">
      {{ ($apd->total_in ?? 0) + ($apd->total_return ?? 0 ) }}
  </td>
  <td class="px-4 py-3 text-center text-red-500">
    {{ ($apd->total_out ?? 0) + ($apd->total_distribution ?? 0) }}
</td>

          <td class="px-4 py-3 text-center text-gray-900"> {{ $apd->balance }}</td>
          <td class="px-4 py-3 text-center">
 @php
    $statusClass = '';
    $statusText  = '';

    // Jika APD Bekas → Secondhand
    if ($apd->conditions === 'Bekas') {
        $statusClass = 'bg-blue-500';
        $statusText  = 'Secondhand';

    // Jika APD Rusak → Broken
    } elseif ($apd->conditions === 'Rusak') {
        $statusClass = 'bg-red-600';
        $statusText  = 'Broken';

    // Jika Baru → pakai perhitungan stock
    } else {

        if ($apd->balance == 0) {
            $statusClass = 'bg-gray-500';
            $statusText = 'Empty';
        } 
        elseif ($apd->balance < $apd->min_stock) {
            $statusClass = 'bg-yellow-500';
            $statusText = 'Critical';
        } 
        else {
            $statusClass = 'bg-green-600';
            $statusText = 'Safe';
        }

    }
@endphp

<span class="inline-block w-28 text-center px-2 py-1 rounded-full text-white font-semibold {{ $statusClass }}">
    {{ $statusText }}
</span>

</td>

          <td class="px-4 py-3 text-center text-gray-500"> {{ $apd->lifetime }}</td>
        

        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>


</div>

<!-- === MODAL INPUT APD BARU === -->
<div id="modalAddAPD" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-6 z-[1050]">
  <div class="bg-white border rounded-2xl shadow-2xl w-full max-w-5xl max-h-[70vh] flex flex-col relative">

    <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6">
    <div class="flex items-center gap-4">
      <div class="bg-green-100 p-3 rounded-2xl flex items-center justify-center">
       <i id="modalAPDHeaderIcon" class="fa-solid fa-plus text-3xl text-green-600"></i>

      </div>
      <div>
        <h2 id="modalAPDTitle" class="text-xl font-bold text-gray-900">ADD NEW APD</h2>
<p id="modalAPDSubtitle" class="text-sm text-gray-500">Tambah APD Baru</p>

        <div class="w-20 h-1 bg-green-600 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeModalAddAPD()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>

    <!-- Body Scrollable -->
    <div class="overflow-y-auto px-6 pb-6">
      
      <!-- === PILIH ICON (POSISI ATAS) === -->
      <div class="mb-6 mt-4">
        <label class="block text-gray-700 font-medium mb-2 text-center">Pilih Icon APD</label>
        
        <!-- Preview Icon -->
        <div id="iconPreview" class="flex justify-center items-center py-4">
          <i class="fa-solid fa-question text-6xl text-gray-300 transition-all duration-300"></i>
        </div>


      <!-- Grid Pilihan Icon -->
      <div class="grid grid-cols-6 gap-3 text-center">
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-helmet-safety" title="Helm">
          <i class="fa-solid fa-helmet-safety text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-glasses" title="Kacamata">
          <i class="fa-solid fa-glasses text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-shirt" title="Kaos">
          <i class="fa-solid fa-shirt text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-user-tie" title="Kemeja">
          <i class="fa-solid fa-user-tie text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-mask-ventilator" title="Jaket">
          <i class="fa-solid fa-mask-ventilator text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-socks" title="Sepatu">
         <i class="fa-solid fa-socks text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-id-badge" title="Pin / ID Card">
          <i class="fa-solid fa-id-badge text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-mitten" title="Sarung Tangan">
          <i class="fa-solid fa-mitten text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-hand-dots" title="Manset">
          <i class="fa-solid fa-hand-dots text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-mask-face" title="Masker">
          <i class="fa-solid fa-mask-face text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-vest" title="Rompi">
          <i class="fa-solid fa-vest text-xl"></i>
        </button>
        <button type="button" class="icon-option p-2 rounded-lg border hover:bg-green-100" data-icon="fa-mobile-screen" title="Sepatu Boot">
          <i class="fa-solid fa-mobile-screen text-xl"></i>
        </button>
      </div>
    </div>

   <!-- === FORM === -->
     <form id="formAddAPD" class="space-y-4 text-sm">
      @csrf
      <input type="hidden" id="apd_id">


      <input type="hidden" name="icon" id="selectedIcon">

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-gray-700 font-medium mb-1">Kode APD</label>
      <input type="text" name="code" placeholder="Salin Dari IMS..."
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none" required>
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Kondisi</label>
      <select name="conditions"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none" required>
        <option value="" disabled selected>Pilih kondisi</option>
        <option value="Baru">Baru</option>
        <option value="Bekas">Bekas</option>
        <option value="Rusak">Rusak</option>
      </select>
    </div>

    <!-- APD Name jadi colspan 2 -->
    <div class="col-span-2">
      <label class="block text-gray-700 font-medium mb-1">Nama APD</label>
      <input type="text" name="name" placeholder="Helm Safety"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none" required>
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Stock Awal</label>
      <input type="number" name="initial_stock" placeholder="0"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Minimal Stock</label>
      <input type="number" name="min_stock" placeholder="Default 0"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">UoM</label>
      <input type="text" name="uom" placeholder="PCS"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-1">Lifetime (bulan)</label>
      <input type="number" name="lifetime" placeholder="6"
        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
    </div>
    
  </div>


    </div>
<hr>
    <!-- Footer (Sticky) -->
    <div class="flex justify-end gap-3 border-t rounded-2xl bg-white px-6 py-4 sticky bottom-0">
      <button type="button" onclick="closeModalAddAPD()"
        class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">Cancel</button>
      <button type="submit" form="formAddAPD"
        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow transition">Save</button>
    </div>
    </form>
  </div>
</div>

<!-- === MODAL: STOCK ADJUSTMENT APD === -->
<div id="modalAdjustment" class="modalAdjustment hidden fixed inset-0 bg-black/50 flex items-center justify-center p-6 z-[1050]">
  <div class="bg-white border rounded-2xl shadow-2xl w-full max-w-5xl max-h-[70vh] flex flex-col relative">
    
  <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6">
    <div class="flex items-center gap-4">
      <div class="bg-yellow-100 p-3 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-rotate text-3xl text-yellow-600"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">STOCK ADJUSTMENT</h2>
        <p class="text-sm text-gray-500">Penyesuaian Stock APD</p>
        <div class="w-20 h-1 bg-yellow-600 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeAdjustmentModal()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>
    <!-- Body scrollable -->
    <div class="overflow-y-auto px-6 py-4 space-y-4 flex-1">
      <form id="formAdjustment" class="space-y-4">
          @csrf
        <!-- === FORM HEADER === -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">Kode Transaksi</label>
            <input type="text" placeholder="Auto-generate"
              class="w-full border border-gray-300 bg-gray-300 rounded-lg text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" disabled>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">No. Reference <small>(Optional)</small></label>
            <input type="text" name="reference_number" placeholder="Misal Nomor PO atau Nomor STO..."
              class="w-full border border-gray-300 rounded-lg text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">Adjustment Date</label>
            <input type="date" name="adjustment_date" value="{{ date('Y-m-d') }}"
              class="w-full border border-gray-300 rounded-lg text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">Adjustment Type</label>
            <select name="adjustment_type"
              class="w-full border border-gray-300 rounded-lg text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
              <option value="">Select Type</option>
              <option value="IN">IN (Penambahan)</option>
              <option value="OUT">OUT (Pengurangan)</option>
            </select>
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Alasan Penyesuian</label>
            <textarea name="adjustment_reason" rows="2"
              class="w-full border border-gray-300 rounded-lg text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="contoh: Persediaan awal bulan, hasil pengecekan STO..."></textarea>
          </div>
        </div>

        <!-- === TABEL ITEM === -->
       <div class="mt-6">
  <div class="flex justify-between items-center mb-2">
    <h3 class="text-gray-700 font-semibold text-sm uppercase">Adjustment Items</h3>
  </div>

  <!-- Tambahkan overflow-x-auto untuk scroll horizontal -->
  <div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-blue-500 text-white uppercase sticky top-0 z-10">
        <tr>
          <th class="px-3 py-2 text-left">#</th>
          <th class="px-3 py-2 text-left">APD</th>
          <th class="px-3 py-2 text-center">Current Stock</th>
          <th class="px-3 py-2 text-center">Adjustment Qty</th>
          <th class="px-3 py-2 text-center">Action</th>
        </tr>
      </thead>
      <tbody id="adjustmentBody" class="divide-y divide-gray-100">
        <tr>
          <td class="px-3 py-2 text-gray-600 text-center">1</td>
          <td class="px-3 py-2">
            <select id="apd_id" name="apd_id[]" class="apdSelect select2-in-adjustment border border-gray-300 rounded-md text-sm p-1.5 w-full">
              <option value="">--- Select APD ---</option>
              @foreach ($apds as $apd)
                <option value="{{ $apd->id }}">{{ $apd->name ?? '-' }} {{ $apd->conditions ?? '-' }}</option>
              @endforeach
            </select>
          </td>
          <td class="px-3 py-2 text-center text-gray-500 stock-cell">—</td>
          <td class="px-3 py-2 text-center">
            <div class="relative inline-block w-28">
              <input type="number" name="qty[]" 
                class="qtyInput border border-gray-300 rounded-md text-center w-full py-1.5 pr-8 pl-2 text-sm focus:ring-2 focus:ring-teal-400" 
                placeholder="0">
              <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">-</span>
            </div>
          </td>
          <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAdjustmentRow(this)">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <button type="button" onclick="addAdjustmentRow()" 
    class="text-sm bg-purple-500 text-white mt-4 px-3 py-1 rounded-md hover:bg-purple-600">
    <i class="fa-solid fa-plus mr-1"></i> Add Row
  </button>
</div>
      </form>
    </div>
<hr>
    <!-- Footer (sticky) -->
    <div class="flex justify-end gap-3 rounded-2xl bg-white px-6 py-4 sticky bottom-0">
      <button type="button" onclick="closeAdjustmentModal()" 
        class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">Cancel</button>
      <button type="submit" form="formAdjustment" 
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save</button>
    </div>

  </div>
</div>


<!-- === MODAL DISTRIBUSI APD === --> 
<div id="modalDistribusi" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-6 z-[1050]">
  <div class="bg-white border rounded-2xl shadow-2xl w-full max-w-5xl max-h-[70vh] flex flex-col relative">


  
 <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6">
    <div class="flex items-center gap-4">
      <div class="bg-purple-100 p-3 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-hand-holding-hand text-3xl text-purple-600"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">APD DISTRIBUTION</h2>
        <p class="text-sm text-gray-500">Distribusikan APD Karyawan</p>
        <div class="w-20 h-1 bg-purple-600 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeDistribusiModal()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto px-6 pb-6">
      <form id="formDistribusi" class="space-y-4">
        @csrf

        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">No. Distribusi</label>
            <input type="text" class="w-full border border-gray-300 bg-gray-200 rounded-lg text-sm p-2" placeholder="Auto-generated" disabled>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Distribusi</label>
            <input type="date" name="distribution_date"  value="{{ date('Y-m-d') }}"  class="w-full border border-gray-300 rounded-lg text-sm p-2">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan</label>
            <textarea name="note" rows="3" class="w-full border border-gray-300 rounded-lg text-sm p-2" placeholder="Misal: Distribusi rutin bulanan..."></textarea>
          </div>
        </div>

        <!-- === TABEL ITEM === -->
        <div class="mt-6">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-gray-700 font-semibold text-sm">List Items</h3>
          </div>

         <div class="border border-gray-200 rounded-lg overflow-x-auto">
  <table class="min-w-full text-sm whitespace-nowrap"> <!-- ✅ nowrap -->
    <thead class="bg-blue-500 text-white uppercase sticky top-0 z-10">
      <tr>
        <th class="px-3 py-2 text-left w-[50px]">#</th>
        <th class="px-3 py-2 text-left w-[240px]">APD</th> <!-- ✅ Lebar diperlebar -->
        <th class="px-3 py-2 text-left w-[100px]">Stock Tersedia</th>
        <th class="px-3 py-2 text-center w-[100px]">Qty</th>
        <th class="px-3 py-2 text-center w-[240px]">Penerima</th> <!-- ✅ Lebar diperlebar -->
        <th class="px-3 py-2 text-center w-[50px]">Action</th>
      </tr>
    </thead>
    <tbody id="distribusiBody" class="divide-y divide-gray-100">
      <tr>
        <td class="px-3 py-2 text-gray-600 text-center">1</td>

        <!-- APD -->
        <td class="px-3 py-2 w-[240px]">
          <select id="apd_distribution" name="apd_id[]" 
            class="apdDistribution select2-in-modal border border-gray-300 rounded-md text-sm p-1.5 w-full">
            <option value="">--- Select APD ---</option>
            @foreach ($apds as $apd)
              <option value="{{ $apd->id }}">{{ $apd->name ?? '-' }} {{ $apd->conditions ?? '-' }}</option>
            @endforeach
          </select>
        </td>

        <!-- Current Stock -->
        <td class="px-3 py-2 text-gray-600 text-center stock-cell">—</td>

        <!-- Qty -->
        <td class="px-3 py-2 text-center text-gray-500">
          <div class="relative inline-block w-24">
            <input type="number" name="qty[]"
              class="qtyInput border border-gray-300 rounded-md text-center w-full py-1.5 pr-8 pl-2 text-sm focus:ring-2 focus:ring-teal-400"
              placeholder="0">
            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">-</span>
          </div>
        </td>

        <!-- Penerima -->
        <td class="px-3 py-2 text-center w-[240px]">
          <select id="employeeDistribution" name="receiver[]" 
            class="employeeDistribution select2-in-modal border border-gray-300 rounded-md text-sm p-1.5 w-full">
            <option value="">--- Select Employee ---</option>
            @foreach ($employees as $employee)
              <option value="{{ $employee->id }}">
                {{ $employee->nik ?? '-' }} — {{ $employee->name ?? '-' }}
              </option>
            @endforeach
          </select>
        </td>

        <!-- Action -->
        <td class="px-3 py-2 text-center">
          <button type="button" class="text-red-500 hover:text-red-700" onclick="removeDistribusiRow(this)">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>


          <button type="button" onclick="addDistribusiRow()"
            class="text-sm bg-purple-500 text-white mt-4 px-3 py-1 rounded-md hover:bg-purple-600">
            <i class="fa-solid fa-plus mr-1"></i> Add Row
          </button>
        </div>
      </form>
    </div>

    <!-- Sticky Footer -->
     <hr>
    <div class="flex justify-end gap-3 rounded-2xl bg-white px-6 py-4 sticky bottom-0">
      <button type="button" onclick="closeDistribusiModal()" class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">Cancel</button>
      <button type="submit" form="formDistribusi" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Distribute Now!</button>
    </div>

  </div>
</div>

 
<!-- === MODAL RETURN APD === -->
<div id="modalReturn" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-6 z-[1050]">
  <div class="bg-white border rounded-2xl shadow-2xl w-full max-w-5xl max-h-[70vh] flex flex-col relative">
    <!-- Header -->
    <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6">
    <div class="flex items-center gap-4">
      <div class="bg-yellow-100 p-3 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-rotate text-3xl text-yellow-600"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">APD RETURN</h2>
        <p class="text-sm text-gray-500">Pengembalian APD Karyawan</p>
        <div class="w-20 h-1 bg-yellow-600 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeReturnModal()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto px-6 pb-6">
    <!-- Form -->
    <form id="formReturn" class="space-y-4">
      @csrf

      <!-- Header Info -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">No. Return</label>
          <input type="text" class="w-full border bg-gray-200 border-gray-300 rounded-lg text-sm p-2" placeholder="Auto-generated" disabled>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Return</label>
          <input type="date" name="return_date"  value="{{ date('Y-m-d') }}"  class="w-full border border-gray-300 rounded-lg text-sm p-2">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="col-span-2">
          <label class="block text-sm font-semibold text-gray-600 mb-1">Catatan</label>
          <textarea name="note" rows="3" class="w-full border border-gray-300 rounded-lg text-sm p-2" placeholder="Misal: Pengembalian APD karena rusak atau tidak layak pakai..."></textarea>
        </div>
      </div>

      <!-- === TABEL ITEM RETURN === -->
      <div class="mt-6">
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-gray-700 font-semibold text-sm uppercase">Return Items</h3>
        </div>

        <div class="border border-gray-200 rounded-lg overflow-x-auto">
          <table class="min-w-full text-sm whitespace-nowrap"> <!-- ✅ nowrap -->
    <thead class="bg-blue-500 text-white uppercase sticky top-0 z-10">
              <tr>
                <th class="px-3 py-2 text-left w-[50]">#</th>
                <th class="px-3 py-2 text-left w-[240]">Yang Mengembalikan</th>
                <th class="px-3 py-2 text-center w-[240]">APD</th>
                <th class="px-3 py-2 text-center w-[100]">Qty</th>
                 <th class="px-3 py-2 text-center w-[100]">Kondisi</th>
                <th class="px-3 py-2 text-center w-[50]">Action</th>
              </tr>
            </thead>
            <tbody id="returnBody" class="divide-y divide-gray-100">
              <tr>
                <td class="px-3 py-2 text-center text-gray-600">1</td>
                <td class="px-3 py-2">
                   <select name="returned_from[]" class="employeeReturn select2-in-return border border-gray-300 rounded-md text-sm p-1.5 w-full min-w-[220px]">
                    <option value="">--- Select Employee ---</option>
                    @foreach ($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->nik ?? '-' }} — {{ $employee->name ?? '-' }}</option>
                    @endforeach
                  </select>
                 
                </td>
               
                <td class="px-3 py-2 text-center">
                  <select name="apd_id[]" class="apdReturn select2-in-return border border-gray-300 rounded-md text-sm p-1.5 w-full min-w-[220px]">
                    <option value="">--- Select APD ---</option>
                   
                  </select>
                </td>

                 <td class="px-3 py-2 text-center">
                   <div class="relative inline-block w-24">
          <input type="number" name="qty[]" 
            class="qtyInput border border-gray-300 rounded-md text-center w-full p-2 py-1.5 pr-8 pl-2 text-sm focus:ring-2 focus:ring-teal-400" 
            placeholder="0">
          <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">-</span>
        </div>
                </td>
                <!-- Kondisi -->
  <td class="px-3 py-2 text-center">
    <select name="conditions[]" class="border border-gray-300 rounded-md text-sm p-1.5 w-full">
      <option value="">--- Select Condition ---</option>
      <option value="Bekas">Bekas</option>
      <option value="Rusak">Rusak</option>
    </select>
  </td>
                <td class="px-3 py-2 text-center">
                  <button type="button" class="text-red-500 hover:text-red-700" onclick="removeReturnRow(this)">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <button type="button" onclick="addReturnRow()"
          class="text-sm bg-purple-500 text-white mt-4 px-3 py-1 rounded-md hover:bg-purple-600">
          <i class="fa-solid fa-plus mr-1"></i> Add Row
        </button>
      </div>
       
    </div>

      <!-- Footer -->
       <hr>
    <div class="flex justify-end gap-3 rounded-2xl bg-white px-6 py-4 sticky bottom-0">
        <button type="button" onclick="closeReturnModal()" class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Return Now!</button>
      </div>
   </form>
  </div>
</div>


<!-- === MODAL: APD MOVEMENT === -->
 <div id="modalApdMovement" class="hidden fixed inset-0 z-[1050] flex items-center justify-center bg-black/50">

 <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[80vh] flex flex-col z-[1060]">

  <div class="flex justify-between items-center border-dotted border-b pb-3 px-6 pt-6">
    <div class="flex items-center gap-4">
      <div class="bg-green-100 p-3 rounded-2xl flex items-center justify-center">
       <i class="fa-solid fa-arrow-right-arrow-left text-3xl text-green-600"></i>

      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">KARTU STOCK</h2>
<p id="movementSubtitle" class="text-sm text-gray-500">Cek Pergerakan Keluar Masuk APD</p>

        <div class="w-20 h-1 bg-teal-500 rounded mt-2"></div>
      </div>
    </div>
 <button onclick="closeApdMovementModal()" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
  </div>
    <!-- Body scrollable -->
    <div class="px-6 py-4 flex-1 overflow-y-auto space-y-4">

      <!-- === FILTER === -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Start Date</label>
          <input type="date" id="filterStart" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">End Date</label>
          <input type="date" id="filterEnd" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Type</label>
          <select id="filterType" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400">
            <option value="">All</option>
            <option value="IN">IN</option>
            <option value="OUT">OUT</option>
          </select>
        </div>
        <div>
          <button id="btnExportMovement" class="w-full bg-teal-500 text-white rounded-lg px-3 py-2 hover:bg-teal-600 text-sm">
            <i class="fa-solid fa-file-export mr-1"></i> Export
          </button>
        </div>
      </div>

    <!-- WRAPPER HORIZONTAL -->
<div class="overflow-x-auto border border-gray-200 rounded-lg">

  <!-- WRAPPER VERTICAL -->
  <div class="max-h-[500px] overflow-y-auto">

    <table class="min-w-full text-sm whitespace-nowrap">
      <thead class="bg-teal-50 text-gray-700 uppercase sticky top-0 z-20">
        <tr>
          <th class="px-3 py-2 text-left">Date</th>
          <th class="px-3 py-2 text-center">Type</th>
          <th class="px-3 py-2 text-left">Sumber</th>
          <th class="px-3 py-2 text-right">Stock Awal</th>
          <th class="px-3 py-2 text-right">Qty</th>
          <th class="px-3 py-2 text-right">Balance</th>
          <th class="px-3 py-2 text-left">Pemberi</th>
          <th class="px-3 py-2 text-left">Penerima</th>
          <th class="px-3 py-2 text-left">Catatan</th>
        </tr>
      </thead>

      <tbody id="apdMovementBody" class="divide-y divide-gray-100">
      </tbody>
    </table>

  </div>
</div>



    </div>

    <!-- Footer sticky -->
    <div class="flex justify-end gap-3 px-6 py-4 border-b rounded-3xl sticky bottom-0 bg-white z-10">
      <button onclick="closeApdMovementModal()" class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300">Close</button>
    </div>

  </div>
</div>

<style>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
  }

   .rotate-180 {
    transform: rotate(180deg);
  }

    /* === Warna aktif untuk tab (teal-500) === */
  .active-tab {
    background-color: white !important;
    color: #14b8a6 !important; /* teal-500 */
  }
  .active-tab i,
  .active-tab span {
    color: #14b8a6 !important;
  }
 .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1px solid #d1d5db !important; /* Tailwind gray-300 */
    border-radius: 0.5rem !important; /* rounded-lg */
    padding: 4px 8px;
  }
  .select2-selection__arrow {
    top: 7px !important;
    right: 8px !important;
  }

   @keyframes bounceIn {
    0% { transform: scale(0.8); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); }
  }
  /* Animasi Fade + Zoom */
  .icon-animate-out {
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.25s ease-in-out;
  }

  .icon-animate-in {
    opacity: 1;
    transform: scale(1.2);
    transition: all 0.25s ease-out;
  }

  .icon-final {
    transform: scale(1);
    transition: all 0.15s ease-out;
  }

  .swal2-container {
   z-index: 9999;
}

  
</style>

@push('scripts')
<script>
$(document).ready(function () {

  $('.select2-in-modal').select2({
    dropdownParent: $('#modalDistribusi')
});

$('.select2-in-return').select2({
    dropdownParent: $('#modalReturn')
});

$('.select2-in-adjustment').select2({
    dropdownParent: $('#modalAdjustment')
});


    /* ================================
       FLATPICKR PERIODE FILTER
    ================================= */
    flatpickr("#filterPeriode", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
    });


    /* ================================
       PILIH ICON
    ================================= */
    $(".icon-option").on("click", function () {
        const icon = $(this).data("icon");

        // Set hidden input & preview
        $("#selectedIcon").val(icon);
        $("#iconPreview").html(
            `<i class="fa-solid ${icon} text-6xl text-green-600 animate-bounce"></i>`
        );

        // Highlight
        $(".icon-option").removeClass("bg-green-200 border-green-500");
        $(this).addClass("bg-green-200 border-green-500");
    });


    /* ================================
       SUBMIT FORM APD (ADD / EDIT)
    ================================= */
    $("#formAddAPD").on("submit", function (e) {
        e.preventDefault();

        let apdID = $("#apd_id").val();
        let url = apdID ? `/facility/apd/update/${apdID}` : "/facility/apd/store";

        $.ajax({
            url: url,
            method: "POST",
            data: $(this).serialize(),

            success: function (res) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false,
                });

                setTimeout(() => location.reload(), 1500);
            },

            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: xhr.responseJSON?.message ?? "Terjadi kesalahan."
                });
            }
        });
    });

}); // END DOCUMENT READY





/* ===========================================================
   OPEN MODAL ADD NEW APD
=========================================================== */
function openModalAddAPD() {
    resetAPDModal();

    $("#modalAPDTitle").text("ADD NEW APD");
    $("#modalAPDSubtitle").text("Tambah APD Baru");

    $("#modalAddAPD").removeClass("hidden");
}



/* ===========================================================
   OPEN MODAL EDIT APD
=========================================================== */
function openModalEditAPD(data) {
    resetAPDModal();

    $("#modalAPDTitle").text("EDIT APD");
    $("#modalAPDSubtitle").text("Ubah Data APD");

    // Isi form
    $("#apd_id").val(data.id);
    $("[name='code']").val(data.code);
    $("[name='conditions']").val(data.conditions);
    $("[name='name']").val(data.name);
    $("[name='initial_stock']").val(data.initial_stock);
    $("[name='min_stock']").val(data.min_stock);
    $("[name='uom']").val(data.uom);
    $("[name='lifetime']").val(data.lifetime);

    // Set ikon
    $("#selectedIcon").val(data.icon);
    $("#iconPreview").html(
        `<i class="fa-solid ${data.icon} text-6xl text-green-600 animate-bounce"></i>`
    );

    // Highlight ikon
    $(".icon-option").removeClass("bg-green-200 border-green-500");
    $(`.icon-option[data-icon='${data.icon}']`)
        .addClass("bg-green-200 border-green-500");

    $("#modalAddAPD").removeClass("hidden");
}



/* ===========================================================
   CLOSE MODAL
=========================================================== */
function closeModalAddAPD() {
    $("#modalAddAPD").addClass("hidden");
}



/* ===========================================================
   RESET MODAL (ADD / EDIT)
=========================================================== */
function resetAPDModal() {

    $("#formAddAPD")[0].reset();
    $("#apd_id").val("");

    // Reset judul default
    $("#modalAPDTitle").text("ADD NEW APD");
    $("#modalAPDSubtitle").text("Tambah APD Baru");

    // Reset ikon
    $("#selectedIcon").val("");
    $("#iconPreview").html(
        `<i class="fa-solid fa-question text-6xl text-gray-300"></i>`
    );

    $(".icon-option").removeClass("bg-green-200 border-green-500");

    // Bersihkan error validasi jika ada
    $(".error-text").text("");
}



/* ===========================================================
   DELETE APD
=========================================================== */
function deleteAPD(id) {
    Swal.fire({
        title: "Hapus APD?",
        text: "Data APD ini akan dihapus secara permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: `/facility/apd/delete/${id}`,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },

                success: function (res) {
                    Swal.fire({
                        icon: "success",
                        title: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => location.reload(), 1500);
                },

                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: xhr.responseJSON?.message ?? "Terjadi kesalahan."
                    });
                }
            });

        }
    });
}


/* ===========================================================
   OPEN / CLOSE MODAL
=========================================================== */
function openApdMovementModal(apdId,code,name, conditions) {
    $("#modalApdMovement").removeClass("hidden");
    $("#modalApdMovement").data("apd-id", apdId); // simpan apdId ke modal
   // UPDATE SUBTITLE → "APD-001 – Helm Safety"
    $("#movementSubtitle").text(`${code} – ${name} ${conditions}`);
    loadApdMovement(); // load pertama kali
}

function closeApdMovementModal() {
    $("#modalApdMovement").addClass("hidden");
}



/* ===========================================================
   LOAD MOVEMENT DATA (JQUERY AJAX)
=========================================================== */
function loadApdMovement() {
    const apdId = $("#modalApdMovement").data("apd-id");

    const start = $("#filterStart").val();
    const end   = $("#filterEnd").val();
    const type  = $("#filterType").val();

    const $tbody = $("#apdMovementBody");

    // Loading indicator
    $tbody.html(`
        <tr>
            <td colspan="8" class="text-center py-6 text-gray-500 italic">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading data...
            </td>
        </tr>
    `);

    $.ajax({
        url: `/facility/apd/movement/${apdId}`,
        method: "GET",
        data: { start, end, type },

        success: function (data) {
            $tbody.empty();

            if (!data.length) {
                $tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500 italic">
                            No movement found
                        </td>
                    </tr>
                `);
                return;
            }

            data.forEach((row) => {
                const dateFormatted = new Date(row.date).toLocaleDateString("id-ID", {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                });

                const tr = `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-2 text-gray-700">${dateFormatted}</td>

                        <td class="px-3 py-2 text-center ${
                            row.type === "IN"
                                ? "text-green-600 font-semibold"
                                : "text-red-600 font-semibold"
                        }">
                            ${row.type}
                        </td>

                        <td class="px-3 py-2 text-gray-700">${row.source || "-"}</td>

                        <td class="px-3 py-2 text-right text-gray-700">
                            ${row.initial_stock}
                        </td>

                        <td class="px-3 py-2 text-right ${
                            row.type === "IN"
                                ? "text-green-600 font-semibold"
                                : "text-red-600 font-semibold"
                        }">
                            ${row.type === "IN" ? "+" : "-"}${row.qty}
                        </td>

                        <td class="px-3 py-2 text-right text-gray-700 font-semibold">
                            ${row.balance}
                        </td>
  <td class="px-3 py-2 text-gray-600">${row.giver || "-"}</td>
    <td class="px-3 py-2 text-gray-600">${row.receiver || "-"}</td>
                        <td class="px-3 py-2 text-gray-600">${row.note || "-"}</td>
                    </tr>
                `;

                $tbody.append(tr);
            });
        },

        error: function () {
            $tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-6 text-red-500">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Failed to load movement data.
                    </td>
                </tr>
            `);
        },
    });
}



/* ===========================================================
   FILTER LISTENERS (jQuery)
=========================================================== */
$("#filterStart, #filterEnd, #filterType").on("change", function () {
    loadApdMovement();
});



/* ===========================================================
   EXPORT BUTTON
=========================================================== */
$("#btnExportMovement").on("click", function () {
    const start = $("#filterStart").val();
    const end   = $("#filterEnd").val();
    const type  = $("#filterType").val();
    const apdId = $("#modalApdMovement").data("apd-id");

    window.open(
        `/facility/apd/movement/export/${apdId}?start=${start}&end=${end}&type=${type}`,
        "_blank"
    );
});

// =======================
// MODAL OPEN / CLOSE
// =======================
function openAdjustmentModal() {
    $('#modalAdjustment').removeClass('hidden');
}

function closeAdjustmentModal() {
    $('#modalAdjustment').addClass('hidden');
}

// =======================
// TAMBAH BARIS
// =======================
function addAdjustmentRow() {
    let rowCount = $('#adjustmentBody tr').length + 1;

    let row = `
      <tr>
        <td class="px-3 py-2 text-center text-gray-600">${rowCount}</td>

        <td class="px-3 py-2">
          <select name="apd_id[]" class="apdSelect select-in-adjustment border-gray-300 rounded-lg text-sm p-1.5 w-full">
            <option value="">Select APD</option>
            @foreach ($apds as $apd)
              <option value="{{ $apd->id }}">{{ $apd->name ?? '-' }} {{ $apd->conditions ?? '-' }}</option>
            @endforeach
          </select>
        </td>

        <td class="px-3 py-2 text-center text-gray-500 stock-cell">—</td>

        <td class="px-3 py-2 text-center">
          <div class="relative inline-block w-28">
            <input type="number" name="qty[]" 
              class="qtyInput border border-gray-300 rounded-lg text-center w-full py-1.5 pr-8 pl-2 text-sm focus:ring-2 focus:ring-teal-400" 
              placeholder="0">
            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">UOM</span>
          </div>
        </td>

        <td class="px-3 py-2 text-center">
          <button type="button" class="text-red-500 hover:text-red-700 btnRemoveRow">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    `;

    $('#adjustmentBody').append(row);
    reinitSelect2();
}

// =======================
// HAPUS BARIS
// =======================
$(document).on('click', '.btnRemoveRow', function() {
    $(this).closest('tr').remove();

    // Re-number rows
    $('#adjustmentBody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
});

// =======================
// REINIT SELECT2 + EVENT
// =======================
function reinitSelect2() {
    // Init Select2 jika belum aktif
    $('.apdSelect').each(function () {
        if (!$(this).data('select2')) {
            $(this).select2({
                width: '100%',
                placeholder: "--- Select APD ---",
                allowClear: true,
                 dropdownParent: $('#modalAdjustment')
            });
        }
    });

    // Hapus event lama agar tidak dobel
    $('.apdSelect').off('change.validate change.update');

    // -------------------------
    // 1️⃣ CEK DUPLIKAT
    // -------------------------
    $('.apdSelect').on('change.validate', function () {
        const selected = $(this).val();
        const all = $('.apdSelect').map(function(){ return $(this).val(); }).get();
        const count = all.filter(v => v === selected).length;

        if (count > 1 && selected !== "") {
            Swal.fire({
                icon: 'warning',
                title: 'Duplicate Item',
                text: 'Item ini sudah dipilih di baris lain!',
            });
            $(this).val('').trigger('change');
        }
    });

    // -------------------------
    // 2️⃣ UPDATE UOM + STOCK
    // -------------------------
    $('.apdSelect').on('change.update', function () {
        const selectedId = $(this).val();
        const row = $(this).closest('tr');

        const apds = @json($apds);
        const item = apds.find(a => a.id == selectedId);

        // Update UOM
        row.find('.uom-label').text(item ? (item.uom ?? 'UOM') : 'UOM');

        // Hitung stock
        if (!item) {
            row.find('.stock-cell').text('-');
            return;
        }

       const balance =
    (parseInt(item.initial_stock) || 0) +
    (parseInt(item.total_in) || 0) +
    (parseInt(item.total_return) || 0) - // ✅ tambahkan total_return
    ((parseInt(item.total_out) || 0) + (parseInt(item.total_distribution) || 0));


        row.find('.stock-cell').text(`${balance} ${item.uom ?? ''}`);

        // -------------------------
        // 3️⃣ VALIDASI QTY (OUT)
        // -------------------------
        const qtyInput = row.find('.qtyInput');
        qtyInput.off('input').on('input', function () {
            const qty = parseInt($(this).val()) || 0;
            const adjustmentType = $('select[name="adjustment_type"]').val();

            if (adjustmentType === 'OUT' && qty > balance) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Qty Melebihi Stok!',
                    text: `Qty tidak boleh lebih besar dari stok tersedia (${balance})`,
                });
                $(this).val('');
            }
        });
    });
}

// =======================
// ON DOCUMENT READY
// =======================
$(document).ready(function () {
    reinitSelect2();
    reinitSelect2Distribution();
    reinitSelect2Return();
});

   function openDistribusiModal() {
    document.getElementById('modalDistribusi').classList.remove('hidden');
  }

  function closeDistribusiModal() {
    document.getElementById('modalDistribusi').classList.add('hidden');
  }

  
function addDistribusiRow() {
  const tbody = document.getElementById('distribusiBody');
  const rowCount = tbody.rows.length + 1;

  const row = `
    <tr>
      <td class="px-3 py-2 text-gray-600 text-center">${rowCount}</td>
      <td class="px-3 py-2 w-[240px]">
        <select name="apd_id[]" class="apdDistribution select2-in-distribution border border-gray-300 rounded-md text-sm p-1.5 w-full">
          <option value="">--- Select APD ---</option>
          @foreach ($apds as $apd)
            <option value="{{ $apd->id }}">{{ $apd->name ?? '-' }} {{ $apd->conditions ?? '-' }}</option>
          @endforeach
        </select>
      </td>
      <td class="px-3 py-2 text-center text-gray-500 stock-cell">—</td>
      <td class="px-3 py-2 text-center text-gray-500">
        <div class="relative inline-block w-24">
          <input type="number" name="qty[]" 
            class="qtyInput border border-gray-300 rounded-md text-center w-full py-1.5 pr-8 pl-2 text-sm focus:ring-2 focus:ring-teal-400" 
            placeholder="0">
          <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">-</span>
        </div>
      </td>
      <td class="px-3 py-2 text-center w-[240px]">
        <select name="receiver[]" class="employeeDistribution select2-in-distribution border border-gray-300 rounded-md text-sm p-1.5 w-full">
          <option value="">--- Select Employee ---</option>
          @foreach ($employees as $employee)
            <option value="{{ $employee->id }}">
             {{ $employee->nik ?? '-' }} — {{ $employee->name ?? '-' }}
            </option>
          @endforeach
        </select>
      </td>
      <td class="px-3 py-2 text-center">
        <button type="button" class="text-red-500 hover:text-red-700" onclick="removeDistribusiRow(this)">
          <i class="fa-solid fa-trash"></i>
        </button>
      </td>
    </tr>`;

  tbody.insertAdjacentHTML('beforeend', row);
  reinitSelect2Distribution(); // inisialisasi ulang Select2 & event handler
}

// === 🔹 Hapus Baris Dengan SweetAlert ===
function removeDistribusiRow(btn) {
 button.closest('tr').remove();
    document.querySelectorAll('#adjustmentBody tr').forEach((tr, i) => {
      tr.querySelector('td:first-child').innerText = i + 1;
    }); 
}

// === 🔹 Reinit Select2 & Update Stock/UOM ===
function reinitSelect2Distribution() {
  // Inisialisasi Select2 APD
  $('.apdDistribution').each(function () {
    if (!$(this).data('select2')) {
      $(this).select2({
        width: '100%',
        placeholder: "--- Select APD ---",
        allowClear: true,
         dropdownParent: $('#modalDistribusi')
      });
    }
  });

  // Inisialisasi Select2 Employee
  $('.employeeDistribution').each(function () {
    if (!$(this).data('select2')) {
      $(this).select2({
        width: '100%',
        placeholder: "--- Select Employee ---",
        allowClear: true,
         dropdownParent: $('#modalDistribusi')
      });
    }
  });


  $('.apdDistribution').on('change', function () {
    const selectedId = $(this).val();
    const row = $(this).closest('tr');
    const uomLabel = row.find('.uom-label');
    const stockCell = row.find('.stock-cell');
    const qtyInput = row.find('.qtyInput');

    const apds = @json($apds);
    const selectedApd = apds.find(apd => apd.id == selectedId);

    // Update UOM
    uomLabel.text(selectedApd ? selectedApd.uom ?? '-' : '-');

    // Update Stok
    if (!selectedApd) {
      stockCell.text('—');
      return;
    }

  const balance = 
    (parseInt(selectedApd.initial_stock) || 0) +
    (parseInt(selectedApd.total_in) || 0) +
    (parseInt(selectedApd.total_return) || 0) - // ✅ tambahkan total_return
    ((parseInt(selectedApd.total_out) || 0) + (parseInt(selectedApd.total_distribution) || 0));


stockCell.text(`${balance} ${selectedApd.uom ?? ''}`);

// === 🔹 HITUNG TOTAL QTY DISTRIBUSI UNTUK APD YANG SAMA ===
function getTotalEnteredQty(selectedApdId) {
    let total = 0;

    document.querySelectorAll('#distribusiBody tr').forEach(row => {
        const apdSelect = row.querySelector('.apdDistribution');
        const qtyInput = row.querySelector('.qtyInput');

        if (apdSelect && qtyInput && apdSelect.value == selectedApdId) {
            total += parseInt(qtyInput.value) || 0;
        }
    });

    return total;
}


qtyInput.off('input').on('input', function () {
    const qty = parseInt($(this).val()) || 0;

    // total qty lain yang sudah diinput untuk APD ini (exclude row ini)
    const currentApdId = selectedId;
    const totalEntered = getTotalEnteredQty(currentApdId) - qty; 
    const allowed = balance - totalEntered;

    if (qty > allowed) {
        Swal.fire({
          icon: 'warning',
          title: 'Qty Melebihi Sisa Stok!',
          text: `Qty maksimal untuk baris ini adalah ${allowed}.`,
          confirmButtonColor: '#3085d6'
        });
        $(this).val('');
    }
});

  });
}

  function removeDistribusiRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    document.querySelectorAll('#distribusiBody tr').forEach((r, i) => {
      r.querySelector('td:first-child').textContent = i + 1;
    });
  }

  // Contoh handle submit (sementara console.log)
  document.getElementById("formAddAPD").addEventListener("submit", e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    console.log("📦 New APD Data:", data);
    closeModalAddAPD();
  });
    
  document.addEventListener("DOMContentLoaded", function () {
  const tabBtns = document.querySelectorAll(".tab-btn");
  const stockPanel = document.getElementById("stockPanel");
  const distributionPanel = document.getElementById("distributionPanel");
  const dashboardPanel = document.getElementById("dashboardPanel");

  tabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      
      // Reset semua tab
      tabBtns.forEach(b => {
        b.classList.remove("active-tab", "text-blue-600");
      });

      // Sembunyikan semua panel
      stockPanel.classList.add("hidden");
      distributionPanel.classList.add("hidden");
      dashboardPanel.classList.add("hidden");

      // Aktifkan panel sesuai tombol
      if (btn.id === "tab-stock") {
        stockPanel.classList.remove("hidden");
      } 
      else if (btn.id === "tab-distribution") {
        distributionPanel.classList.remove("hidden");
      }
      else if (btn.id === "tab-dashboard") {
        dashboardPanel.classList.remove("hidden");
      }

      // Tandai tab aktif
      btn.classList.add("active-tab", "text-blue-600");
    });
  });

  // ===========================
  // DEFAULT TAB = DASHBOARD
  // ===========================
  document.getElementById("tab-dashboard")
    .classList.add("active-tab", "text-blue-600");

  dashboardPanel.classList.remove("hidden");
  stockPanel.classList.add("hidden");
  distributionPanel.classList.add("hidden");
});


 function toggleExpand(button) {
  const icon = button.querySelector("i.fa-chevron-down");
  const panel = button.nextElementSibling;
  const isOpen = !panel.classList.contains("hidden");

  // Hanya toggle panel ini saja (tidak menutup panel lain)
  panel.classList.toggle("hidden", isOpen);
  icon.classList.toggle("rotate-180", !isOpen);
}

$(document).ready(function() {
  
 $('#filterDepartment').select2({
    placeholder: "-- All Department --",
    allowClear: true,
    width: '100%'
});

const searchInput = document.getElementById("searchEmployee");
const employeeCards = document.querySelectorAll(".employee-card");

// Container + No Data
const container = $('.h-[500px].overflow-y-auto');
const noDataDiv = $('<div>')
    .addClass('text-center py-6 text-gray-500 italic')
    .text('No employees found.')
    .hide();

container.append(noDataDiv);

function filterEmployeeCards() {
    const query = searchInput.value.toLowerCase();
    const selectedDept = $('#filterDepartment').val(); // STRING atau null
    let anyVisible = false;

    employeeCards.forEach(card => {
        const text = card.querySelector("h3")?.innerText.toLowerCase() || "";

        // 🔥 Ambil department ID per card (JSON array)
        let cardDepartments = [];
        try {
            cardDepartments = JSON.parse(card.dataset.departments || "[]")
                .map(String);
        } catch (e) {
            cardDepartments = [];
        }

        const matchesSearch = text.includes(query);

        // 🔥 Jika dropdown kosong → tampilkan semua
        let matchesDept = true;
        if (selectedDept && selectedDept !== "") {
            matchesDept = cardDepartments.includes(selectedDept);
        }

        const show = matchesSearch && matchesDept;
        card.style.display = show ? "" : "none";

        if (show) anyVisible = true;
    });
 updateEmployeeCount();
    noDataDiv.toggle(!anyVisible);
}

// Event Listener

searchInput.addEventListener("input", filterEmployeeCards);
$('#filterDepartment').on("change", filterEmployeeCards);

});




  const searchAPD = document.getElementById('searchAPD');
  const filterStatus = document.getElementById('filterStatus');
  const filterCondition = document.getElementById('filterCondition');
  const table = document.getElementById('apdStockTable');
  const tbody = table.querySelector('tbody');
  const rows = tbody.getElementsByTagName('tr');

  // Buat elemen pesan "no data"
  const noDataRow = document.createElement('tr');
  noDataRow.innerHTML = `
    <td colspan="12" class="text-center py-6 text-gray-500 italic">
      No filtered rows found.
    </td>
  `;
  noDataRow.style.display = "none";
  tbody.appendChild(noDataRow);

 function filterTable() {
  const searchValue = searchAPD.value.trim().toLowerCase();
  const statusValue = filterStatus.value.trim().toLowerCase();
  const conditionValue = filterCondition.value.trim().toLowerCase();

  let visibleCount = 0;

  for (let i = 0; i < rows.length; i++) {
    if (rows[i] === noDataRow) continue;

    const cells = rows[i].getElementsByTagName('td');
    if (!cells.length) continue;

    const code = (cells[1]?.textContent || "").trim().toLowerCase();
    const name = (cells[2]?.textContent || "").trim().toLowerCase();
    const condition = (cells[3]?.textContent || "").trim().toLowerCase();
    
    const statusSpan = cells[9]?.querySelector('span');
    const status = statusSpan ? statusSpan.textContent.trim().toLowerCase() : "";

    const matchesSearch = code.includes(searchValue) || name.includes(searchValue);
    const matchesStatus = !statusValue || status.includes(statusValue);
    const matchesCondition = !conditionValue || condition.includes(conditionValue);

    const visible = matchesSearch && matchesStatus && matchesCondition;
    rows[i].style.display = visible ? "" : "none";

    if (visible) visibleCount++;
  }

  noDataRow.style.display = visibleCount === 0 ? "" : "none";
}

  // Event listener
  searchAPD.addEventListener('input', filterTable);
  filterStatus.addEventListener('change', filterTable);
  filterCondition.addEventListener('change', filterTable);

    function toggleDropdown(id) {
    // Tutup semua dropdown lain
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
      if (el.id !== `dropdown-${id}`) el.classList.add('hidden');
    });

    // Toggle dropdown yang diklik
    const dropdown = document.getElementById(`dropdown-${id}`);
    dropdown.classList.toggle('hidden');
  }

  // Klik di luar tombol atau dropdown → tutup semua dropdown
  document.addEventListener('click', function(e) {
    const isDropdownButton = e.target.closest('button[onclick^="toggleDropdown"]');
    const isDropdownMenu = e.target.closest('[id^="dropdown-"]');

    // Jika klik bukan di tombol dan bukan di dalam dropdown
    if (!isDropdownButton && !isDropdownMenu) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
    }
  });

   // === Inisialisasi awal Select2 + validasi duplikat ===
  $(document).ready(function() {
    $('.apdSelect').select2({
      width: '100%',
      placeholder: "--- Select APD ---",
      allowClear: true,
      dropdownParent: $('#modalAdjustment')
    });

    // Cek duplikat untuk select yang sudah ada di awal
    $('.apdSelect').on('change', function () {
      const selectedValue = $(this).val();
      const allValues = $('.apdSelect').map(function() { return $(this).val(); }).get();
      const count = allValues.filter(v => v === selectedValue).length;

      if (count > 1 && selectedValue !== "") {
        Swal.fire({
          icon: 'warning',
          title: 'Duplicate Item',
          text: 'Item ini sudah dipilih di baris lain!',
          confirmButtonColor: '#3085d6',
        });
        $(this).val('').trigger('change');
      }
    });
  });

   $('.apdDistribution').select2({
      width: '100%',
      placeholder: "--- Select APD ---",
      allowClear: true,
       dropdownParent: $('#modalDistribusi')
    });

     $('.employeeDistribution').select2({
      width: '100%',
      placeholder: "--- Select Employee ---",
      allowClear: true,
       dropdownParent: $('#modalDistribusi')
    });

   const apdUOM = @json($apds); // pastikan variabel $apds tersedia dari controller

  // Fungsi untuk update UOM saat user memilih APD
  $(document).on('change', 'select[name="apd_id"], select[id="apd_id"]', function() {
    const selectedId = $(this).val();
    const selectedApd = apdUOM.find(apd => apd.id == selectedId);
    const uom = selectedApd ? selectedApd.uom || 'UOM' : 'UOM';

    // Temukan baris tempat select berada
    const row = $(this).closest('tr');
    row.find('.uom-label').text(uom);
  });

  $('#formAdjustment').on('submit', function(e){
  e.preventDefault();

  $.ajax({
    url: "{{ route('facility.adjustment.store') }}",
    type: "POST",
    data: $(this).serialize(),
    success: function(res){
      if(res.status === 'success'){
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: res.message + ' (' + res.transaction_code + ')'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
      }
    },
    error: function(xhr){
      Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText });
    }
  });
});

$('#formDistribusi').on('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    Swal.fire({
      title: 'Simpan Distribusi?',
      text: "Pastikan data sudah benar sebelum disimpan.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#16a34a',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Simpan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
          url: "{{ route('facility.distribution.store') }}",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function () {
            Swal.fire({
              title: 'Menyimpan...',
              text: 'Harap tunggu sebentar',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          },
          success: function (res) {
            if (res.status === 'success') {
              Swal.fire({
  title: 'Distribusi Tersimpan!',
  html: `<b>Nomor:</b> ${res.distribution_number}<br><b>Tanggal:</b> ${res.date || '-'}<br><br>
  <i class="fa-solid fa-check-circle text-green-500 text-3xl"></i>`,
  icon: 'success',
  confirmButtonText: 'Oke',
  confirmButtonColor: '#16a34a'
              }).then(() => {
                closeDistribusiModal();
                $('#formDistribusi')[0].reset();
                location.reload();
              });
            } else {
              Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: res.message || 'Terjadi kesalahan tidak diketahui.'
              });
            }
          },
          error: function (xhr) {
            let msg = 'Terjadi kesalahan server.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }
            Swal.fire({
              icon: 'error',
              title: 'Gagal!',
              text: msg,
              confirmButtonColor: '#dc2626'
            });
          }
        });
      }
    });
  });

  window.openReturnModal = function() {
    document.getElementById('modalReturn').classList.remove('hidden');
}

window.closeReturnModal = function() {
    document.getElementById('modalReturn').classList.add('hidden');
}



// Tambah baris
function addReturnRow() {
    const tbody = document.getElementById('returnBody');
    const rowNumber = document.querySelectorAll('#returnBody tr').length + 1

    const newRow = `
    <tr>
       <td class="px-3 py-2 text-center text-gray-600">${rowNumber}</td>

        <td class="px-3 py-2">
            <select name="returned_from[]" class="employeeReturn border border-gray-300 rounded-md text-sm p-1.5 w-full min-w-[220px]">
                <option value="">--- Select Employee ---</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->nik }} — {{ $employee->name }}</option>
                @endforeach
            </select>
        </td>

        <td class="px-3 py-2 text-center">
            <select name="apd_id[]" class="apdReturn border border-gray-300 rounded-md text-sm p-1.5 w-full min-w-[220px]">
                <option value="">--- Select APD ---</option>
            </select>
        </td>

        <td class="px-3 py-2 text-center">
            <div class="relative inline-block w-24">
                <input type="number" name="qty[]" class="qtyInput border border-gray-300 rounded-md text-center w-full p-2 py-1.5 pr-8 text-sm">
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs uom-label">-</span>
            </div>
        </td>

        <td class="px-3 py-2 text-center">
            <select name="conditions[]" class="border border-gray-300 rounded-md text-sm p-1.5 w-full">
                <option value="">--- Select Condition ---</option>
                <option value="Bekas">Bekas</option>
                <option value="Rusak">Rusak</option>
            </select>
        </td>

        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeReturnRow(this)">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>`;

    tbody.insertAdjacentHTML('beforeend', newRow);

    reinitSelect2Return();
}


// === INIT SELECT2 + LOAD APD PER EMPLOYEE ===
function reinitSelect2Return() {

    $('.apdReturn').select2({
        width: '100%',
        placeholder: "--- Select APD ---",
        allowClear: true,
         dropdownParent: $('#modalReturn')
    });

    $('.employeeReturn').select2({
        width: '100%',
        placeholder: "--- Select Employee ---",
        allowClear: true,
         dropdownParent: $('#modalReturn')
    });

  $(document).on('change', '.employeeReturn', function () {
    const empId = $(this).val();
    const $apdSelect = $(this).closest('tr').find('.apdReturn');

    $apdSelect.empty().append('<option value="">--- Select APD ---</option>');

    if (!empId) {
        // jika kosong, trigger select2 supaya tampil
        $apdSelect.trigger('change.select2');
        return;
    }

    $.ajax({
        url: `/facility/api/distributions/${encodeURIComponent(empId)}`,
        method: 'GET',
        success: function (data) {
            // data = array of {apd_id, apd_name, uom, qty}
            data.forEach(item => {
               $apdSelect.append(
    `<option 
        value="${item.apd_id}" 
        data-uom="${item.uom}" 
        data-qty="${item.qty}" 
        data-conditions="${item.conditions}">
        ${item.apd_name} ${item.conditions} (Qty: ${item.qty} PCS)
     </option>`
);

            });
            // jika pakai select2
            if ($apdSelect.data('select2')) {
                $apdSelect.trigger('change.select2');
            }
        },
        error: function (xhr) {
            console.error('Gagal ambil APD:', xhr.responseText || xhr.statusText);
        }
    });
});

// Saat APD dipilih → isi qty & uom
$(document).off("change.apdFill").on("change.apdFill", ".apdReturn", function () {

    const $selected = $(this).find('option:selected');
    const qty = $selected.data('qty') || '';
    const uom = $selected.data('uom') || '-';

    const $row = $(this).closest('tr');

    // Isi input qty
    $row.find('.qtyInput').val(qty);

    // Isi label UOM
    $row.find('.uom-label').text(uom);
});

// === VALIDASI: CEGAH APD MELEBIHI TOTAL QTY YG DIMILIKI KARYAWAN ===
$(document).on("change.validateAPD", ".apdReturn, .qtyInput", function () {

    const $row = $(this).closest("tr");
    const employeeId = $row.find(".employeeReturn").val();
    const apdId = $row.find(".apdReturn").val();

    if (!employeeId || !apdId) return;

    // qty batas maksimal yg dimiliki employee
    const maxQty = parseInt(
        $row.find(".apdReturn option:selected").data("qty") || 0
    );

    // hitung total qty APD yg sama di semua baris utk employee ini
    let totalUsed = 0;

    $("#returnBody tr").each(function () {
        const emp = $(this).find(".employeeReturn").val();
        const apd = $(this).find(".apdReturn").val();
        const qty = parseInt($(this).find(".qtyInput").val() || 0);

        if (emp == employeeId && apd == apdId) {
            totalUsed += qty;
        }
    });

    if (totalUsed > maxQty) {

        Swal.fire({
            icon: "warning",
            title: "Qty APD Melebihi Batas!",
            text: `Total qty untuk APD ini sudah ${totalUsed} dari maksimum ${maxQty}.`,
        });

        // rollback qty ke nilai yang masih dibolehkan
        const allowed = maxQty - (totalUsed - parseInt($row.find(".qtyInput").val() || 0));
        $row.find(".qtyInput").val(allowed >= 0 ? allowed : 0);
    }
});


}


  function removeReturnRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    document.querySelectorAll('#returnBody tr').forEach((r, i) => {
      r.querySelector('td:first-child').textContent = i + 1;
    });
  }

$('#formReturn').on('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    Swal.fire({
        title: 'Simpan Pengembalian APD?',
        text: "Pastikan data sudah benar sebelum disimpan.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('facility.return.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Harap tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function (res) {

                if (res.status === 'success') {

                    Swal.fire({
                        title: 'Pengembalian APD Tersimpan!',
                        html: `
                            <b>Nomor Return:</b> ${res.return_number}<br>
                            <i class="fa-solid fa-check-circle text-green-500 text-3xl mt-3"></i>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Oke',
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        closeReturnModal();      // ← FIX INI
                        $('#formReturn')[0].reset();
                        location.reload();
                    });

                } else {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: res.message || 'Terjadi kesalahan tidak diketahui.'
                    });
                }
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg,
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    });
});



  function toggleWarningList() {
    const list = document.getElementById('warningList');
    const arrow = document.getElementById('warningArrow');

    if (!list) return;

    list.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

 function openWarningModal() {
        document.getElementById('warningModal').classList.remove('hidden');
    }

    function closeWarningModal() {
        document.getElementById('warningModal').classList.add('hidden');
    }

    // Close modal when clicking outside the box
    window.onclick = function(event) {
        const modal = document.getElementById('warningModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }

function filterTableStock() {
    let search = document.getElementById("searchStock").value.toLowerCase();
    let filter = document.getElementById("purchaseFilter").value;

    let rows = document.querySelectorAll("#warningModal table tbody tr");

    rows.forEach(row => {
        let apd = row.children[0].textContent.toLowerCase();
        let perlu = parseInt(row.children[3].textContent.trim());

        let matchSearch = apd.includes(search);
        let matchFilter = true;

        if (filter === "need") matchFilter = perlu > 0;
        if (filter === "noneed") matchFilter = perlu <= 0;

        row.style.display = (matchSearch && matchFilter) ? "" : "none";
    });
}

$('#filterTypeEmployee').on('change', function () {
    let type = $(this).val();

    // buat ulang URL dengan parameter baru
    let url = new URL(window.location.href);

    if (type) {
        url.searchParams.set('typeEmployee', type);
    } else {
        url.searchParams.delete('typeEmployee'); // hapus kalau pilih "All"
    }
 updateEmployeeCount();
    window.location.href = url.toString();
});

$('#filterStatusAPD').on('change', function () {
    let value = $(this).val(); // safe, warning, critical, atau ""

    $('.employee-card').each(function () {
        let status = $(this).data('status');

        // jika filter kosong → tampilkan semua
        if (value === "") {
            $(this).show();
        }
        // cocok status → tampilkan
        else if (status === value) {
            $(this).show();
        }
        // tidak cocok → sembunyikan
        else {
            $(this).hide();
        }
    });
     updateEmployeeCount();
});

function updateEmployeeCount() {
    let visibleCards = $('.employee-card:visible').length;
    $('#employeeCount').text(visibleCards);
}

let apdReminder = {}; // global

  


   $(document).ready(function () {

    const months = [
        "Jan","Feb","Mar","Apr","Mei","Jun",
        "Jul","Agu","Sep","Okt","Nov","Des"
    ];

    const ctx = $("#yearlyAPDChart")[0].getContext("2d");

    // === Chart Kosong saat awal ===
    let chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: months,
            datasets: [
                {
                    label: "APD Didistribusikan",
                    data: Array(12).fill(0),
                    backgroundColor: "rgba(54,162,235,0.8)"
                },
                {
                    label: "APD Dikembalikan",
                    data: Array(12).fill(0),
                    backgroundColor: "rgba(255,159,64,0.8)"
                }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // === Fungsi load data chart dari controller ===
    function loadChart(year) {
        $.ajax({
            url: "/facility/apd/chart/yearly", // GANTI ke route milikmu
            method: "GET",
            data: { year: year },
            success: function (res) {
                chart.data.datasets[0].data = res.distributed;
                chart.data.datasets[1].data = res.returned;
                chart.update();
            },
            error: function () {
                console.error("Gagal memuat data chart");
            }
        });
    }

    // === Load data default (misal tahun 2025) ===
    loadChart($("#filterYearChart").val());

    // === Update chart saat tahun diganti ===
    $("#filterYearChart").change(function () {
        loadChart($(this).val());
    });
const currentDate = new Date();
const currentYear = currentDate.getFullYear();
const monthNames = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
const currentMonth = monthNames[currentDate.getMonth()]; // "Jan".."Des"

// ===============================
// AMBIL DATA DARI CONTROLLER
// ===============================
$.ajax({
    url: "/facility/apd/reminder", // sesuaikan route
    type: "GET",
    dataType: "json",
    success: function (res) {
        console.log("Data APD Reminder:", res);
        apdReminder = res;

        // Ambil semua tahun dari object
        let years = Object.keys(res).sort();
        let yearSelect = $("#filterYearEmployee");
        yearSelect.empty();
        years.forEach(y => yearSelect.append(`<option value="${y}">${y}</option>`));

        // Pilih tahun saat ini jika ada, atau tahun terakhir
        let selectedYear = years.includes(currentYear.toString()) ? currentYear : years[years.length - 1];
        yearSelect.val(selectedYear);

        // Pilih bulan default currentMonth
        $("#filterMonthEmployee").val(currentMonth);

        // Render list default
        renderEmployeeList(selectedYear, currentMonth);
    },
    error: function () {
        Swal.fire({
            icon: "error",
            title: "Gagal Memuat Data",
            text: "Tidak dapat mengambil data APD reminder dari server.",
            confirmButtonColor: "#3085d6"
        });
    }
});

// ===============================
// RENDER LIST KARYAWAN
// ===============================
function renderEmployeeList(year, month = "") {
    let list = $("#employeeAPDList");
    list.empty();

    let data = apdReminder[year] || [];

    // Filter bulan jika dipilih
    if (month !== "") {
        const monthIndex = monthNames.indexOf(month); // 0..11
        data = data.filter(item => {
            const itemMonth = new Date(item.due).getMonth(); // 0..11
            return itemMonth === monthIndex;
        });
    }

    // Update badge count
    $("#apdReminderBadge").text(data.length);

    if (data.length === 0) {
        list.append(`<li class="text-gray-500 italic py-2">Tidak ada data APD yang perlu pergantian.</li>`);
        return;
    }

    data.forEach(item => {
    // Tentukan warna badge berdasarkan status
    let statusClass = item.status === "APD Masih Di Karyawan" 
        ? "bg-yellow-100 text-yellow-800"
        : "bg-green-100 text-green-800";

         // Pakai default jika item.apd_icon kosong
    let iconClass = item.apd_icon && item.apd_icon.trim() !== "" 
        ? item.apd_icon 
        : "fa-solid fa-helmet-safety";

    list.append(`
        <li class="p-4 border rounded-lg shadow hover:shadow-lg transition flex items-start space-x-4 bg-white">

            <!-- Nama, Jadwal, dan Status -->
            <div class="flex-1">
                <div class="font-semibold text-gray-800 text-base">${item.name}</div>
                <div class="text-gray-600 text-sm flex items-center mt-1">
                    <i class="${iconClass} mr-2 text-blue-400"></i>
                    <span>${item.apd_name}</span>
                </div>
                <div class="text-gray-600 text-sm flex items-center mt-1">
                    <i class="fa-regular fa-calendar-days mr-2 text-blue-400"></i>
                    <span>Jadwal Pergantian: ${item.due}</span>
                </div>
                <div class="mt-2">
                    <span class="px-2 py-1 rounded-md text-xs font-semibold ${statusClass}">
                        ${item.status}
                    </span>
                </div>
            </div>
        </li>
    `);
});

}

// ===============================
// EVENT FILTER TAHUN & BULAN
// ===============================
$("#filterYearEmployee, #filterMonthEmployee").change(function () {
    let year = $("#filterYearEmployee").val();
    let month = $("#filterMonthEmployee").val();
    renderEmployeeList(year, month);
});

// ===============================
// CSS untuk scrollable
// ===============================
$("#employeeAPDList").css({
    "max-height": "300px",
    "overflow-y": "auto"
});

});


$(document).ready(function () {

    function loadYearDropdown() {
        $.ajax({
            url: "/facility/apd/chart/years",   // GANTI ke route milikmu
            method: "GET",
            success: function (years) {
                let select = $("#filterYearChart");
                select.empty(); // bersihkan dulu

                years.forEach(y => {
                    select.append(`<option value="${y}">${y}</option>`);
                });

                // Load chart pertama kali memakai tahun pertama (paling baru)
                if (years.length > 0) {
                    loadChart(years[0]);
                }
            }
        });
    }

    // Panggil load tahun
    loadYearDropdown();

});

$(document).ready(function () {

    let apdId = "{{ $apd->id }}"; // atau ambil dari URL

    $('#apdMovementTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/facility/apd/${apdId}/globalMovement`,
            data: function (d) {
                d.start = $('#filterStart').val();
                d.end = $('#filterEnd').val();
                d.type = $('#filterType').val();
            }
        },
        columns: [
            {
                data: null,
                className: "sticky left-0 bg-white z-30",
                render: function(row) {
                    return `
                        <button class="px-2 py-1 text-xs bg-yellow-400 text-white rounded"
                            onclick="editMovement('${row.source}', ${row.item_id})">EDIT</button>
                        
                        <button class="px-2 py-1 text-xs bg-red-500 text-white rounded"
                            onclick="deleteMovement('${row.source}', ${row.item_id})">DELETE</button>
                    `;
                }
            },
            { data: 'type', className: "text-left" },
            { data: 'source', className: "text-left" },
           
            { data: 'initial_stock', className: "text-center" },
            { data: 'qty', className: "text-center" },
            { data: 'balance', className: "text-center" },
            { data: 'giver', className: "text-center" },
            { data: 'receiver', className: "text-center" },
            { data: 'note', className: "text-center" },
        ],
        order: [[1, 'asc']]
    });

});

function setTrend(elementId, value) {
    const el = document.getElementById(elementId);

    // Tentukan trend
    let icon = "";
    if (value >= 50) {
        icon = `<i class="fa-solid fa-arrow-trend-up text-green-600 mr-1"></i>
                <span class="text-green-600 font-semibold">${value}%</span>`;
    } else {
        icon = `<i class="fa-solid fa-arrow-trend-down text-red-600 mr-1"></i>
                <span class="text-red-600 font-semibold">${value}%</span>`;
    }

    el.innerHTML = icon;
}

// ========================================
// Contoh nilai (isi pakai backend kamu)
// ========================================

// Misal data diambil dari controller (Laravel Blade):


let safe = 70;      // contoh >50 → naik
let critical = 30;  // contoh <50 → turun
let empty = 55;
let bekas = 20;
let rusak = 65;

// ========================================
// Isi nilai card + trend arrow
// ========================================
document.getElementById("cardSafe").innerText = safe;
document.getElementById("cardCritical").innerText = critical;
document.getElementById("cardEmpty").innerText = empty;
document.getElementById("cardBekas").innerText = bekas;
document.getElementById("cardRusak").innerText = rusak;

setTrend("safeTrend", safe);
setTrend("criticalTrend", critical);
setTrend("emptyTrend", empty);
setTrend("bekasTrend", bekas);
setTrend("rusakTrend", rusak);
$(document).ready(function() {
    // Ambil nilai dari server (Blade)
    var currentYear = "{{ request('tahun', date('Y')) }}";   // default current year
    var currentMonth = "{{ request('bulan', date('m')) }}";   // default current month

    // Set default selected di dropdown
    $('#filterTahun').val(currentYear);
    $('#filterBulan').val(currentMonth);

    // Event listener untuk filter
    $('#filterTahun, #filterBulan').on('change', function() {
        var tahun = $('#filterTahun').val();
        var bulan = $('#filterBulan').val();

        var params = {};

        if (tahun) params['tahun'] = tahun;
        if (bulan) params['bulan'] = bulan;

        // Build query string
        var queryString = $.param(params);

        // Reload halaman dengan query string
        window.location.href = '?' + queryString;
    });
});
</script>
@endpush

@endsection
