{{-- resources/views/suggestion/dashboard.blade.php --}}
{{-- Mobile-first Tailwind · SweetAlert2 · Unified roles --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Suggestion System — PT. Abimanyu Sekar Nusantara</title>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
{{-- jQuery HARUS pertama --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

{{-- Tailwind --}}
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                navy: { DEFAULT: '#1e3a5f', light: '#eff6ff', dark: '#162d4a' },
            },
            fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
        }
    }
}
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .period-dot { animation: blink 1.5s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .collapse-body { display: none; }
    .collapse-body.open { display: block; }
    .chevron { transition: transform .2s; }
    .chevron.open { transform: rotate(180deg); }
    /* Scrollable table wrapper */
    .tbl-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    /* Hide scrollbar but keep functional */
    .tbl-scroll::-webkit-scrollbar { height: 4px; }
    .tbl-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
    /* Score input number arrows hide */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { opacity: 1; }
    /* Wrapper */
.dataTables_wrapper {
    font-size: 12px;
    color: #374151;
}

/* Top controls */
.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate {
    margin-top: 12px;
    margin-bottom: 12px;
}

/* Select + input */
.dataTables_wrapper select,
.dataTables_wrapper input {
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    font-size: 12px !important;
    background: white !important;
    outline: none !important;
}

/* Table */
table.dataTable {
    width: 100% !important;
    border-collapse: collapse !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

/* Header */
table.dataTable thead th {
    background: #f9fafb !important;
    border-bottom: 1px solid #e5e7eb !important;
    padding: 14px 12px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #6b7280 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
}

/* Body */
table.dataTable tbody td {
    padding: 14px 12px !important;
    border-bottom: 1px solid #f3f4f6 !important;
    vertical-align: middle;
    font-size: 12px;
}

/* Hover */
table.dataTable tbody tr:hover {
    background: #fafafa !important;
}

/* Pagination */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background: white !important;
    padding: 4px 10px !important;
    margin-left: 4px !important;
    font-size: 12px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #1e3a5f !important;
    color: white !important;
    border-color: #1e3a5f !important;
}

/* Remove ugly sorting arrows spacing issue */
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc {
    background-image: none !important;
}

.tab-btn {
    padding: 6px 10px;
    border-radius: 6px;
    color: #64748b;
    transition: 0.2s;
}

.tab-btn:hover {
    background: #f1f5f9;
}

.tab-btn.active {
    background: #e2e8f0;
    color: #0f172a;
    font-weight: 600;
}

.flip-face { backface-visibility: hidden; }
.flip-wrap.flipping .flip-card {
  animation: flip-cd .4s cubic-bezier(.4,0,.2,1);
}

@keyframes flip-cd {
  0%   { transform: rotateX(0deg); }
  49%  { transform: rotateX(-90deg); }
  50%  { transform: rotateX(90deg); }
  100% { transform: rotateX(0deg); }
}

/* hilangkan spinner di input number */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}
</style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

{{-- ═══════════════ TOP NAV ═══════════════ --}}
<nav class="bg-[#1e3a5f] sticky top-0 z-50 shadow-md">
    <div class="max-w-screen-xl mx-auto px-4 h-14 flex items-center justify-between gap-3">

        {{-- Brand --}}
        <a href="{{ route('suggestion.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
            <img src="{{ asset('img/asn-logo-bulat.png') }}" alt="ASN" class="w-7 h-7 rounded-full">
            <div class="">
                <div class="text-white text-xs font-semibold leading-tight">Portal Suggestion System</div>
                <div class="text-white/40 text-[10px] leading-tight">PT. Abimanyu Sekar Nusantara</div>
            </div>
        </a>

        {{-- Right: user + logout --}}
        <div class="flex items-center gap-2 ml-auto">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-white/15 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="hidden sm:block">
                    <div class="text-white/80 text-xs font-medium leading-tight">{{ $user->name }}</div>
                    <div class="text-white/40 text-[10px] leading-tight">{{ $user->departments->first()?->name ?? $user->department }}</div>
                </div>
            </div>
            <form action="{{ route('suggestion.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-1 text-[11px] text-white/50 border border-white/15 rounded px-2.5 py-1.5 hover:text-white hover:border-white/35 transition-all font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="1.5"/>
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ═══════════════ SUB NAV (Improvement & Manager) ═══════════════ --}}
@if($isImprovement || $isManager || $isSpv)
<div class="bg-white border-b border-gray-200 sticky top-14 z-40 shadow-sm">
    <div class="max-w-screen-xl mx-auto px-2 sm:px-4">
        <div class="flex items-center justify-around sm:justify-start gap-0 overflow-x-auto scrollbar-none">

            {{-- DASHBOARD --}}
            <button id="subnav-overview"
                class="subnav-btn shrink-0 flex items-center justify-center sm:justify-start gap-0 sm:gap-1.5
                       px-3 sm:px-3.5 py-3 text-xs font-medium text-gray-500
                       border-b-2 border-transparent -mb-px transition-all whitespace-nowrap active"
                onclick="switchSubnav('overview')">

                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" stroke-width="1.5"/>
                    <rect x="14" y="3" width="7" height="7" stroke-width="1.5"/>
                    <rect x="14" y="14" width="7" height="7" stroke-width="1.5"/>
                    <rect x="3" y="14" width="7" height="7" stroke-width="1.5"/>
                </svg>

                <span class="hidden sm:inline">
                    Dashboard
                </span>
            </button>

            {{-- ANALYTICS --}}
            <button id="subnav-analytics"
                class="subnav-btn shrink-0 flex items-center justify-center sm:justify-start gap-0 sm:gap-1.5
                       px-3 sm:px-3.5 py-3 text-xs font-medium text-gray-500
                       border-b-2 border-transparent -mb-px transition-all whitespace-nowrap"
                onclick="switchSubnav('analytics')">

                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                          stroke-width="1.5"/>
                </svg>

                <span class="hidden sm:inline">
                    Analytics
                </span>
            </button>

            @if($isImprovement)

            {{-- PERIODE --}}
            <button id="subnav-periods"
                class="subnav-btn shrink-0 flex items-center justify-center sm:justify-start gap-0 sm:gap-1.5
                       px-3 sm:px-3.5 py-3 text-xs font-medium text-gray-500
                       border-b-2 border-transparent -mb-px transition-all whitespace-nowrap"
                onclick="switchSubnav('periods')">

                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.5"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.5"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.5"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.5"/>
                </svg>

                <span class="hidden sm:inline">
                    Periode
                </span>
            </button>

            {{-- PENILAIAN --}}
            <button id="subnav-formula"
                class="subnav-btn shrink-0 flex items-center justify-center sm:justify-start gap-0 sm:gap-1.5
                       px-3 sm:px-3.5 py-3 text-xs font-medium text-gray-500
                       border-b-2 border-transparent -mb-px transition-all whitespace-nowrap"
                onclick="switchSubnav('formula')">

                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3"
                          stroke-width="1.5"/>
                    <path d="M14 2l6 6m0 0l-6 6"
                          stroke-width="1.5"/>
                </svg>

                <span class="hidden sm:inline">
                    Penilaian
                </span>
            </button>

            @endif
        </div>
    </div>
</div>
@endif

{{-- ═══ PAGE LAYOUT ═══ --}}
<div class="px-6 py-4">
<div class="flex gap-3 items-start">

  {{-- ══════════════ SIDEBAR ══════════════ --}}
  <div class="w-64 shrink-0 hidden lg:flex flex-col gap-3">

 @if($activePeriod)
@php
  $end      = \Carbon\Carbon::parse($activePeriod->end_date)->endOfDay();
  $start    = \Carbon\Carbon::parse($activePeriod->start_date)->startOfDay();
  $isClosed = now()->greaterThan($end);
@endphp

<div class="bg-white rounded-xl overflow-hidden shadow-sm">

  {{-- HEADER --}}
  <div class="px-5 py-4 border-b border-slate-100">
    <div class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
      Periode Aktif
    </div>

    <div class="mt-1 text-base font-semibold text-slate-800 tracking-tight">
      {{ $activePeriod->name }}
    </div>
  </div>

  {{-- BODY --}}
  <div class="px-5 py-6">

    
 {{-- COUNTDOWN --}}
<div class="flex justify-center">
  <div class="grid grid-cols-4 gap-2 sm:gap-3 max-w-[260px] w-full">

    @foreach(['d'=>'Hari','h'=>'Jam','m'=>'Menit','s'=>'Detik'] as $key => $lbl)
    <div class="flex flex-col items-center">

      {{-- FLIP WRAPPER --}}
      <div id="fw-{{ $key }}"
           class="flip-wrap w-full aspect-square rounded-lg border border-slate-200 bg-slate-50
                  flex items-center justify-center shadow-sm overflow-hidden">

        {{-- FRONT --}}
        <div id="fd-{{ $key }}"
             class="flip-face font-mono text-base sm:text-lg font-semibold text-slate-800 tabular-nums">
          00
        </div>

        {{-- BACK --}}
        <div id="bd-{{ $key }}"
             class="flip-face font-mono text-base sm:text-lg font-semibold text-slate-800 tabular-nums hidden">
          00
        </div>

      </div>

      {{-- LABEL --}}
      <span class="mt-1 text-[9px] text-slate-400 uppercase tracking-wide text-center">
        {{ $lbl }}
      </span>

    </div>
    @endforeach

  </div>
</div>

    {{-- STATUS --}}
    <div class="mt-6 flex items-center justify-between text-xs">

      <div class="flex items-center gap-2">
        <div class="w-2 h-2 rounded-full {{ $isClosed ? 'bg-slate-400' : 'bg-red-500 animate-pulse' }}"></div>

        <span class="text-slate-500 font-medium">
          {{ $isClosed ? 'Status' : 'Batas Pengumpulan' }}
        </span>
      </div>

      <span class="font-mono font-medium {{ $isClosed ? 'text-slate-500' : 'text-red-500' }}">
        @if($isClosed)
          Ditutup
        @else
          {{ $end->format('d M Y') }}
        @endif
      </span>

    </div>

    {{-- CLOSED TEXT --}}
    @if($isClosed)
    <div class="mt-4 text-center text-[11px] text-slate-500">
      Periode sudah ditutup
    </div>
    @endif

  </div>
</div>
@endif


    {{-- ── 2. SS SAYA ── --}}
     @if(!$isManager || !$isSpv)
    <div class="bg-white overflow-hidden rounded-md">
      <div class="px-4 py-2.5 border-b border-slate-100">
        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Statistik Saya</span>
      </div>
      @php
        $myStats = [
          ['label'=>'Total',     'val'=>$mySSStats->total     ?? 0, 'color'=>'#334155', 'max'=>1],
          ['label'=>'Submitted', 'val'=>$mySSStats->submitted ?? 0, 'color'=>'#d97706'],
          ['label'=>'Approved',  'val'=>$mySSStats->approved  ?? 0, 'color'=>'#16a34a'],
          ['label'=>'Rejected',  'val'=>$mySSStats->rejected  ?? 0, 'color'=>'#dc2626'],
          ['label'=>'Returned',  'val'=>$mySSStats->returned  ?? 0, 'color'=>'#ea580c'],
          ['label'=>'Scored',    'val'=>$mySSStats->scored    ?? 0, 'color'=>'#7c3aed'],
          ['label'=>'Closed',    'val'=>$mySSStats->closed    ?? 0, 'color'=>'#0d9488'],
        ];
        $myTotal = max(1, $mySSStats->total ?? 1);
      @endphp
      <div class="py-1">
        @foreach($myStats as $s)
        <div class="flex items-center justify-between px-4 py-1.5 hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $s['color'] }}"></div>
            <span class="text-[11px] text-slate-500">{{ $s['label'] }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-12 h-0.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full"
                   style="background:{{ $s['color'] }};width:{{ $loop->first ? 100 : round($s['val']/$myTotal*100) }}%"></div>
            </div>
            <span class="font-mono text-xs font-medium text-slate-700 w-5 text-right">{{ $s['val'] }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ── 3. STATISTIK DEPT / SEMUA ── --}}
    @if($isImprovement || $isManager || $isSpv)
    <div class="bg-white overflow-hidden rounded-xl">
      <div class="px-4 py-2.5 border-b border-slate-100 rounded-xl">
        <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">
          @if($isImprovement) Semua Departemen @else Dept. {{ $user->departments->first()?->name }} @endif
        </span>
      </div>
      @php
        $deptStats = [
          ['label'=>'Total',     'val'=>$deptSSStats->total     ?? 0, 'color'=>'#334155'],
          ['label'=>'Submitted', 'val'=>$deptSSStats->submitted ?? 0, 'color'=>'#d97706'],
          ['label'=>'Approved',  'val'=>$deptSSStats->approved  ?? 0, 'color'=>'#16a34a'],
          ['label'=>'Rejected',  'val'=>$deptSSStats->rejected  ?? 0, 'color'=>'#dc2626'],
          ['label'=>'Returned',  'val'=>$deptSSStats->returned  ?? 0, 'color'=>'#ea580c'],
          ['label'=>'Scored',    'val'=>$deptSSStats->scored    ?? 0, 'color'=>'#7c3aed'],
          ['label'=>'Closed',    'val'=>$deptSSStats->closed    ?? 0, 'color'=>'#0d9488'],
        ];
        $deptTotal = max(1, $deptSSStats->total ?? 1);
      @endphp
      <div class="py-1">
        @foreach($deptStats as $s)
        <div class="flex items-center justify-between px-4 py-1.5 hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $s['color'] }}"></div>
            <span class="text-[11px] text-slate-500">{{ $s['label'] }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-12 h-0.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full"
                   style="background:{{ $s['color'] }};width:{{ $loop->first ? 100 : round($s['val']/$deptTotal*100) }}%"></div>
            </div>
            <span class="font-mono text-xs font-medium text-slate-700 w-5 text-right">{{ $s['val'] }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

 
  </div>{{-- /sidebar --}}

{{-- ═══════════════ PAGE CONTENT ═══════════════ --}}
<div class="flex-1 min-w-0 px-3 py-4 ">
<div class="mb-3 ">
  <div class="relative overflow-visible rounded-md
              bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400
               px-6 py-5 md:px-8
              flex items-end justify-between gap-4">

    <!-- Ornamen lingkaran -->
    <div class="absolute -right-4 top-1/2 -translate-y-1/2
                w-24 h-24 rounded-full bg-blue-300 opacity-35 z-0"></div>
    <div class="absolute right-12 -bottom-5
                w-14 h-14 rounded-full bg-blue-300 opacity-20 z-0"></div>

    <!-- TEXT + CTA -->
    <div class="flex-1 min-w-0 relative z-10 pr-4 pb-1">
      <h2 class="text-sm md:text-base font-semibold text-white mb-1">
        Welcome back,
        <span class="underline text-white">{{ $user->name }}</span>
      </h2>
      <p class="text-xs text-slate-200 leading-relaxed max-w-sm mb-4">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Donec volutpat felis velit, vitae fermentum nulla ultrices et.
      </p>

 @php
    $hasActivePeriod = \App\Models\SuggestionPeriod::where('is_active', 1)
        ->whereDate('end_date', '>=', now())
        ->exists();
@endphp

@if(!$isSpv && !$isManager && $hasActivePeriod)
    <a href="{{ route('suggestion.create') }}"
       class="group relative inline-flex items-center gap-2 px-5 py-2.5
              rounded-lg
              bg-white
              text-blue-600 text-xs font-semibold
              border border-slate-200
              shadow-sm
              transition-all duration-300 ease-out

              hover:bg-slate-50
              hover:shadow-md hover:shadow-slate-200/40
              hover:-translate-y-0.5
              hover:border-blue-200

              active:scale-[0.98] active:shadow-sm
              focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2">

        <i class="fa-solid fa-plus text-blue-600 text-xs transition-transform duration-300 group-hover:translate-x-0.5"></i>

        <span class="transition-all duration-300 group-hover:tracking-wide">
            Buat SS Sekarang
        </span>
    </a>
@endif
    </div>

    <!-- IMAGE -->
    <div class="shrink-0 relative z-10 self-end">
      <img src="{{ asset('img/staff.png') }}"
           alt="illustration"
           class="w-20 md:w-40 object-contain object-bottom
                  -mt-12 md:-mt-16 mb-0" />
    </div>

  </div>
</div>

 

   {{-- ================= OVERVIEW PANE (CARD LIST / MOBILE FRIENDLY) ================= --}}
<div class="tab-pane active" id="pane-overview">

  {{-- WRAPPER CARD --}}
<div class="bg-white shadow-sm overflow-hidden rounded-xl">

  {{-- ── HEADER ACCORDION ── --}}
  <div id="accordion-header"
       onclick="toggleAccordion()"
       class="px-5 py-4 flex items-center justify-between
              cursor-pointer select-none
              border-b border-slate-200
              hover:bg-slate-50 transition-colors">

   {{-- LEFT --}}
<div class="flex items-center gap-3">

    <div class="
        w-8 h-8
        flex items-center justify-center
        rounded-full
        border border-blue-200
        bg-blue-50
        shrink-0
    ">
        <i class="fa-regular fa-lightbulb text-sm text-blue-500"></i>
    </div>

    <span class="text-sm font-medium text-slate-700">
        List Suggestion System (SS)
    </span>

</div>
    {{-- RIGHT: chevron only --}}
    <svg id="chevron-icon"
         class="w-4 h-4 text-slate-400 transition-transform duration-300"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
  </div>

        {{-- FILTER --}}
        <div id="accordion-body"
       class="overflow-hidden transition-all duration-300 ease-in-out"
       style="opacity: 1;">
        <div class="px-6 py-5 border-b border-slate-200 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-2">

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-2">No. SS</label>
                    <input id="filter-ss-number" type="text" placeholder="Cari nomor SS"
                        class="w-full h-10 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400">
                </div>

                 <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-2">Kategori</label>
                    <select id="filter-category"
                        class="w-full h-10 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <option value="">Semua Kategori</option>
                        <option value="Safety">Safety</option>
                        <option value="Moral">Moral</option>
                        <option value="Quality">Quality</option>
                        <option value="Productivity">Productivity</option>
                        <option value="Cost">Cost</option>
                        <option value="Environment">Environment</option>
                        <option value="Delivery">Delivery</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                    <select id="filter-status"
                        class="w-full h-10 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved_spv">Approved</option>
                        <option value="rejected_spv">Rejected</option>
                        <option value="returned_spv">Returned</option>
                        <option value="scored">Scored</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-2">Tema</label>
                    <input id="filter-theme" type="text" placeholder="Cari tema"
                        class="w-full h-10 px-3 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400">
                </div>
        </div>
</div>

        {{-- TOOLBAR --}}
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500">Show</span>
                    <select id="per-page"
                        class="h-9 px-3 text-sm border border-slate-300 rounded-md focus:outline-none">
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-xs text-slate-500">entries</span>
                </div>

                <div id="table-info" class="text-xs text-slate-500">
                    Loading data...
                </div>
            </div>
        </div>

        {{-- CARD LIST (SERVER SIDE AJAX) --}}
        <div id="table-body" class="space-y-4 p-4 bg-slate-100/60">
            <div class="px-6 py-10 text-center text-sm text-slate-400">
                Memuat data...
            </div>
        </div>

        {{-- FOOTER --}}
       

    </div>
     <div class="px-6 py-4 border-t border-slate-200 bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <div id="table-summary" class="text-xs text-slate-500">
                    Loading...
                </div>

                <div id="pagination-container" class="flex flex-wrap items-center gap-2">
                    {{-- AJAX PAGINATION --}}
                </div>
            </div>
        </div>
</div>
</div>


    {{-- ═══ PANE: ANALYTICS ═══ --}}
    @if($isImprovement || $isManager || $isSpv)
    <div class="tab-pane" id="pane-analytics">

        {{-- Trend + Category --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            {{-- Department Performance --}}
<div class="col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">

        <div>
            <div class="text-sm font-semibold text-slate-800">
                Department Performance
            </div>

            <div class="mt-0.5 text-[11px] text-slate-400">
                Penilaian objektif berdasarkan rata-rata score dan total reward
            </div>
        </div>

        {{-- PERIOD FILTER --}}
        <div>
            <select id="reward-period-filter"
                class="h-9 px-3 rounded-lg border border-slate-200 bg-white
                       text-xs text-slate-600
                       focus:outline-none focus:ring-2 focus:ring-amber-100
                       transition">

                @foreach($periods ?? [] as $p)
                    <option value="{{ $p->id }}"
                        {{ $activePeriod?->id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach

            </select>
        </div>

    </div>

    {{-- BODY --}}
    <div class="p-5">

        @php
            $deptStats = collect($analyticsData['dept_stats'] ?? []);

            $totalReward = $deptStats->sum('total_reward');
            $totalSS     = $deptStats->sum('total_ss');
            $avgScore    = round($deptStats->avg('avg_score'), 1);
        @endphp

        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">

            {{-- TOTAL REWARD --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                <div class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">
                    Total Reward
                </div>

                <div id="reward-total"
                     class="mt-1 text-lg font-bold tracking-tight text-amber-600">
                    Rp {{ number_format($totalReward, 0, ',', '.') }}
                </div>

            </div>

            {{-- TOTAL SS --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                <div class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">
                    Total Suggestion
                </div>

                <div class="mt-1 text-lg font-bold tracking-tight text-slate-700">
                    {{ number_format($totalSS) }}
                </div>

            </div>

            {{-- AVG SCORE --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                <div class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">
                    Average Score
                </div>

                <div class="mt-1 text-lg font-bold tracking-tight text-blue-700">
                    {{ number_format($avgScore, 1) }}
                </div>

            </div>

        </div>

        {{-- INFO --}}
        <div class="mb-5 flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">

            <div class="mt-0.5 w-2 h-2 rounded-full bg-blue-500"></div>

            <div class="text-[11px] leading-relaxed text-slate-600">
                Ranking department diurutkan berdasarkan
                <span class="font-semibold text-slate-700">
                    average score
                </span>
                agar lebih objektif dan tidak bias jumlah submit.
                Total reward tetap ditampilkan sebagai indikator apresiasi.
            </div>

        </div>

        {{-- CHART --}}
        <div class="relative h-[340px]">
            <canvas id="chartReward"></canvas>
        </div>

    </div>

</div>

              <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">

        <div>
            <div class="text-sm font-semibold text-slate-800">
                Kategori Improvement
            </div>

            <div class="mt-0.5 text-[11px] text-slate-400">
                Kategori paling banyak dilakukan improvement
            </div>
        </div>

        <select id="category-period-filter"
            class="h-9 px-3 rounded-lg border border-slate-200 bg-white
                   text-xs text-slate-600
                   focus:outline-none focus:ring-2 focus:ring-slate-100">

            @foreach($periods ?? [] as $p)
                <option value="{{ $p->id }}"
                    {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
            @endforeach

        </select>

    </div>

  <div class="p-5">
    <div class="relative h-[420px]">
        <canvas id="chartCategory"></canvas>
    </div>
</div>

</div>

        {{-- Top SS + Top Dept --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <div class="text-xs font-semibold text-gray-800">Top SS by Score</div>
                    <span class="text-[10px] text-gray-400">SS dengan nilai tertinggi</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($topSS ?? [] as $idx => $ss)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-300 w-4">{{ $idx+1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium text-gray-700 truncate">{{ $ss->theme }}</div>
                            <div class="text-[10px] text-gray-400">{{ $ss->departments->name }} — {{ $ss->user->name }}</div>
                        </div>
                        <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden shrink-0">
                            <div class="h-full bg-[#1e3a5f] rounded-full" style="width:{{ $ss->score_total }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-800 w-8 text-right">{{ number_format($ss->score_total,1) }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada SS yang dinilai.</p>
                    @endforelse
                </div>
            </div>
          
        </div>

     
    </div>
    @endif

    {{-- ═══ PANE: PERIODE ═══ --}}
    @if($isImprovement)
    <div class="tab-pane" id="pane-periods">

        {{-- Form Buat Periode --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
            <div class="px-4 py-3 border-b border-gray-100 text-xs font-semibold text-gray-800">Buat Periode Baru</div>
            <div class="p-4">
                <form method="POST" action="{{ route('suggestion.improvement.period.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Nama Periode *</label>
                            <input type="text" name="name" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f] transition-colors" placeholder="Q2 2025" required/>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Tanggal Mulai *</label>
                            <input type="date" name="start_date" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f] transition-colors" required/>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Tanggal Tutup *</label>
                            <input type="date" name="end_date" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f] transition-colors" required/>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Max Submit/Karyawan</label>
                            <input type="number" name="max_submissions" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f] transition-colors" placeholder="Kosong = tak terbatas" min="1"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 text-xs cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="accent-[#1e3a5f]"/>
                            Aktifkan langsung
                        </label>
                        <button type="submit" class="px-4 py-2 bg-[#1e3a5f] text-white text-xs font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                            Simpan Periode
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Periode --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 text-xs font-semibold text-gray-800">Daftar Periode</div>

            {{-- Mobile card list --}}
            <div class="divide-y divide-gray-50 sm:hidden">
                @forelse($periods ?? [] as $period)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <div class="text-xs font-semibold text-gray-800">{{ $period->name }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}
                            </div>
                            <div class="text-[10px] text-gray-400">Max: {{ $period->max_submissions ?? 'Tidak terbatas' }}</div>
                        </div>
                        @if($period->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700 shrink-0">Aktif</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 shrink-0">Non-aktif</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$period->is_active)
                        <form method="POST" action="{{ route('suggestion.improvement.period.update',$period->id) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="name" value="{{ $period->name }}"/>
                            <input type="hidden" name="start_date" value="{{ $period->start_date }}"/>
                            <input type="hidden" name="end_date" value="{{ $period->end_date }}"/>
                            <input type="hidden" name="is_active" value="1"/>
                            <button type="submit" class="px-3 py-1.5 text-[11px] font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">Aktifkan</button>
                        </form>
                        @endif
                        <button onclick="confirmDeletePeriod({{ $period->id }})"
                            class="px-3 py-1.5 text-[11px] font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            Hapus
                        </button>
                        <form id="delete-period-{{ $period->id }}" method="POST" action="{{ route('suggestion.improvement.period.delete',$period->id) }}" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-gray-400">Belum ada periode.</div>
                @endforelse
            </div>

            {{-- Desktop table --}}
            <div class="hidden sm:block tbl-scroll">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Nama</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Mulai</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Tutup</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Max Submit</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Status</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods ?? [] as $period)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border-b border-gray-50 font-medium text-gray-800">{{ $period->name }}</td>
                            <td class="px-4 py-3 border-b border-gray-50 text-gray-600">{{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 border-b border-gray-50 text-gray-600">{{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 border-b border-gray-50 text-gray-600">{{ $period->max_submissions ?? 'Tidak terbatas' }}</td>
                            <td class="px-4 py-3 border-b border-gray-50">
                                @if($period->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700">Aktif</span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">Non-aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-b border-gray-50">
                                <div class="flex items-center gap-2">
                                    @if(!$period->is_active)
                                    <form method="POST" action="{{ route('suggestion.improvement.period.update',$period->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $period->name }}"/>
                                        <input type="hidden" name="start_date" value="{{ $period->start_date }}"/>
                                        <input type="hidden" name="end_date" value="{{ $period->end_date }}"/>
                                        <input type="hidden" name="is_active" value="1"/>
                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-medium text-green-700 bg-green-50 border border-green-200 rounded hover:bg-green-100 transition-colors">Aktifkan</button>
                                    </form>
                                    @endif
                                    <button onclick="confirmDeletePeriod({{ $period->id }})"
                                        class="px-2.5 py-1 text-[11px] font-medium text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors">
                                        Hapus
                                    </button>
                                    <form id="delete-period-{{ $period->id }}" method="POST" action="{{ route('suggestion.improvement.period.delete',$period->id) }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400 text-xs">Belum ada periode.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>{{-- /pane-periods --}}

    {{-- ═══ PANE: FORMULA ═══ --}}
    <div class="tab-pane" id="pane-formula">

        @php
            $allFormulas = \App\Models\SuggestionRewardFormula::with(['items.criteria','tiers'])
                ->orderByDesc('is_active')
                ->orderByDesc('created_at')
                ->get();
            $activeFormula = $allFormulas->firstWhere('is_active', true);
            $inactiveFormulas = $allFormulas->where('is_active', false);
        @endphp

        {{-- ── Section: Formula Aktif ── --}}
        <div class="flex items-center gap-3 mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Formula Aktif</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        @if($activeFormula)
        <div class="bg-white border-2 border-green-200 rounded-xl shadow-sm mb-4" id="formula-card-{{ $activeFormula->id }}">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3.5 bg-green-50 rounded-t-xl cursor-pointer"
                 onclick="toggleFormula({{ $activeFormula->id }})">
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900">{{ $activeFormula->name }}</div>
                        <div class="text-[10px] text-gray-500 mt-0.5">
                            Dibuat {{ $activeFormula->created_at->format('d M Y') }}
                            · {{ $activeFormula->items->count() }} item
                            · {{ $activeFormula->tiers->count() }} tier reward
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 shrink-0">
                        <span class="period-dot w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Aktif
                    </span>
                </div>
                <div class="flex items-center gap-2 shrink-0" onclick="event.stopPropagation()">
                    <button onclick="confirmDeactivateFormula({{ $activeFormula->id }}, '{{ $activeFormula->name }}')"
                        class="px-2.5 py-1.5 text-[11px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                        Nonaktifkan
                    </button>
                    <form id="deact-form-{{ $activeFormula->id }}" method="POST" action="" style="display:none;">
                        @csrf @method('PATCH')
                    </form>
                    <button onclick="toggleFormula({{ $activeFormula->id }})"
                        class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-colors">
                        <svg id="chevron-{{ $activeFormula->id }}" class="w-3.5 h-3.5 text-gray-400 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Detail collapsible --}}
            <div class="collapse-body" id="formula-detail-{{ $activeFormula->id }}">
                @include('formula_detail', ['formula' => $activeFormula])
            </div>
        </div>
        @else
        <div class="bg-white border border-dashed border-gray-300 rounded-xl p-8 text-center mb-4">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3" stroke-width="1.5"/>
                    <path d="M14 2l6 6m0 0l-6 6" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="text-sm font-medium text-gray-600 mb-1">Tidak ada formula aktif</div>
            <div class="text-xs text-gray-400">Aktifkan formula dari daftar di bawah atau buat yang baru.</div>
        </div>
        @endif

        {{-- ── Section: Formula Lainnya ── --}}
        @if($inactiveFormulas->count() > 0)
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Formula Lainnya</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <div class="space-y-3 mb-4">
            @foreach($inactiveFormulas as $formula)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm" id="formula-card-{{ $formula->id }}">

                <div class="flex items-center justify-between gap-3 px-4 py-3.5 bg-gray-50 rounded-t-xl cursor-pointer"
                     onclick="toggleFormula({{ $formula->id }})">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-600">{{ $formula->name }}</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">
                            Dibuat {{ $formula->created_at->format('d M Y') }}
                            · {{ $formula->items->count() }} item
                            · {{ $formula->tiers->count() }} tier
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0" onclick="event.stopPropagation()">
                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500">Non-aktif</span>
                        <button onclick="confirmActivateFormula({{ $formula->id }}, '{{ $formula->name }}')"
                            class="px-2.5 py-1.5 text-[11px] font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                            Aktifkan
                        </button>
                        <form id="act-form-{{ $formula->id }}" method="POST" action="" style="display:none;">
                            @csrf @method('PATCH')
                        </form>
                        <button onclick="confirmDeleteFormula({{ $formula->id }}, '{{ $formula->name }}')"
                            class="px-2.5 py-1.5 text-[11px] font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            Hapus
                        </button>
                        <form id="del-form-{{ $formula->id }}" method="POST" action="" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                        <button onclick="toggleFormula({{ $formula->id }})"
                            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-colors">
                            <svg id="chevron-{{ $formula->id }}" class="w-3.5 h-3.5 text-gray-400 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="collapse-body" id="formula-detail-{{ $formula->id }}">
                    @include('formula_detail', ['formula' => $formula])
                    <div class="flex items-center justify-between gap-3 px-4 py-3 bg-gray-50 border-t border-gray-100 rounded-b-xl flex-wrap">
                        <span class="text-xs text-gray-400">Mengaktifkan formula ini akan menonaktifkan yang sedang aktif.</span>
                        <button onclick="confirmActivateFormula({{ $formula->id }}, '{{ $formula->name }}')"
                            class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                            Aktifkan Formula Ini
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ── Section: Buat Formula Baru ── --}}
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Buat Formula Baru</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-4 py-3.5 border-b border-gray-100">
                <div class="text-xs font-semibold text-gray-800">Form Pembuatan Formula Penilaian</div>
                <div class="text-[10px] text-gray-400 mt-0.5">Isi item penilaian, kriteria range point, dan tier reward.</div>
            </div>
            <div class="p-4">
                <form id="formula-form" onsubmit="return false;">
                    @csrf

                    {{-- Nama Formula --}}
                    <div class="flex flex-col sm:flex-row sm:items-end gap-3 mb-5">
                        <div class="flex-1">
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Nama Formula *</label>
                            <input type="text" name="name" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2.5 outline-none focus:border-[#1e3a5f] transition-colors" placeholder="Formula Penilaian SS 2026" required/>
                        </div>
                        <label class="flex items-center gap-2 text-xs cursor-pointer pb-0.5">
                            <input type="checkbox" name="is_active" value="1" checked class="accent-[#1e3a5f] w-3.5 h-3.5"/>
                            Aktifkan langsung
                        </label>
                    </div>

                    {{-- Item Penilaian --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Item Penilaian + Kriteria</div>
                            <button type="button" onclick="addItemBlock()"
                                class="flex items-center gap-1 px-3 py-1.5 text-[11px] font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                + Tambah Item
                            </button>
                        </div>
                        <div id="item-container" class="space-y-3">
                            <div class="item-block border border-gray-200 rounded-xl p-4">
                                <div class="flex gap-2 items-center mb-3">
                                    <input type="text" name="items[0][item_name]"
                                        class="flex-1 text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f] transition-colors"
                                        placeholder="Nama Item (contoh: Quality)" required/>
                                    <button type="button" onclick="removeItem(this)"
                                        class="w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors text-sm font-bold shrink-0">✕</button>
                                </div>
                                {{-- Kriteria header (desktop) --}}
                                <div class="hidden sm:grid grid-cols-[80px_70px_70px_1fr_32px] gap-2 mb-2 px-1">
                                    <span class="text-[10px] font-semibold text-gray-400">Grade</span>
                                    <span class="text-[10px] font-semibold text-gray-400">Min</span>
                                    <span class="text-[10px] font-semibold text-gray-400">Max</span>
                                    <span class="text-[10px] font-semibold text-gray-400">Deskripsi</span>
                                    <span></span>
                                </div>
                                <div class="criteria-container space-y-2">
                                    <div class="criteria-row grid grid-cols-2 sm:grid-cols-[80px_70px_70px_1fr_32px] gap-2">
                                        <input type="text" name="items[0][criterias][0][grade]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Grade (A)" required/>
                                        <input type="number" name="items[0][criterias][0][min_point]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Min" min="1" required/>
                                        <input type="number" name="items[0][criterias][0][max_point]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Max" min="1" required/>
                                        <input type="text" name="items[0][criterias][0][description]" class="col-span-2 sm:col-span-1 text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Deskripsi kriteria" required/>
                                        <button type="button" onclick="removeCriteria(this)"
                                            class="w-8 h-8 flex items-center justify-center text-red-400 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors text-sm">✕</button>
                                    </div>
                                </div>
                                <button type="button" onclick="addCriteriaRow(this)"
                                    class="mt-2 flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-medium text-gray-500 hover:text-gray-700 transition-colors">
                                    + Tambah Kriteria
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tier Reward --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Tier Reward</div>
                            <button type="button" onclick="addTierRow()"
                                class="flex items-center gap-1 px-3 py-1.5 text-[11px] font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                + Tambah Tier
                            </button>
                        </div>
                        <div class="hidden sm:grid grid-cols-[1fr_1fr_1fr_32px] gap-2 mb-2 px-1">
                            <span class="text-[10px] font-semibold text-gray-400">Min Avg</span>
                            <span class="text-[10px] font-semibold text-gray-400">Max Avg</span>
                            <span class="text-[10px] font-semibold text-gray-400">Reward (Rp)</span>
                            <span></span>
                        </div>
                        <div id="tier-container" class="space-y-2">
                            <div class="tier-row grid grid-cols-2 sm:grid-cols-[1fr_1fr_1fr_32px] gap-2">
                                <input type="number" name="tiers[0][min_score]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Min Avg" required/>
                                <input type="number" name="tiers[0][max_score]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Max Avg" required/>
                                <input type="number" name="tiers[0][reward_amount]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Contoh: 150000" required/>
                                <button type="button" onclick="removeTier(this)"
                                    class="w-8 h-8 flex items-center justify-center text-red-400 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors text-sm">✕</button>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-5">
                        <label class="block text-[11px] font-medium text-gray-600 mb-1">Catatan (opsional)</label>
                        <input type="text" name="notes" class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2.5 outline-none focus:border-[#1e3a5f] transition-colors" placeholder="Contoh: Berlaku mulai Q1 2026"/>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#1e3a5f] text-white text-xs font-semibold rounded-lg hover:bg-[#162d4a] transition-colors">
                        Simpan Formula
                    </button>
                </form>
            </div>
        </div>
    </div>{{-- /pane-formula --}}
    @endif

</div>{{-- /max-w-screen-xl --}}

{{-- ═══════════════ SPV MODAL ═══════════════ --}}
<div id="spv-modal" class="hidden fixed inset-0 bg-black/50 z-[500] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:w-[440px] sm:max-w-[92vw] max-h-[90vh] overflow-y-auto rounded-t-2xl sm:rounded-xl border border-gray-200 shadow-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="text-sm font-semibold text-gray-900">
                Review SS — <span id="spv-ss-num" class="text-[#1e3a5f] font-mono"></span>
            </div>
            <button onclick="closeModal('spv-modal')" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke-width="2"/></svg>
            </button>
        </div>
        <form id="spv-form" method="POST">
            @csrf
            <div class="p-5">
                <label class="block text-[11px] font-medium text-gray-600 mb-2">Keputusan *</label>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div id="dt-approve" onclick="selectDecision('approve')"
                        class="decision-tile flex flex-col items-center gap-1 p-3 border border-gray-200 rounded-xl cursor-pointer text-xs font-semibold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-all">
                        <input type="radio" name="action" value="approve" id="r-approve" class="hidden"/>
                        <span class="text-lg">✓</span> Setujui
                    </div>
                    <div id="dt-return" onclick="selectDecision('return')"
                        class="decision-tile flex flex-col items-center gap-1 p-3 border border-gray-200 rounded-xl cursor-pointer text-xs font-semibold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-all">
                        <input type="radio" name="action" value="return" id="r-return" class="hidden"/>
                        <span class="text-lg">↩</span> Kembalikan
                    </div>
                    <div id="dt-reject" onclick="selectDecision('reject')"
                        class="decision-tile flex flex-col items-center gap-1 p-3 border border-gray-200 rounded-xl cursor-pointer text-xs font-semibold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-all">
                        <input type="radio" name="action" value="reject" id="r-reject" class="hidden"/>
                        <span class="text-lg">✕</span> Tolak
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-600 mb-1">
                        Catatan <span id="note-req" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="note" id="spv-note" rows="3"
                        class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2.5 outline-none focus:border-[#1e3a5f] resize-none transition-colors"
                        placeholder="Tuliskan alasan atau catatan untuk pengaju..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl sm:rounded-b-xl">
                <button type="button" onclick="closeModal('spv-modal')"
                    class="px-4 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button
    type="submit"
    id="spv-submit-btn"
    class="px-4 py-2 text-xs font-medium text-white bg-[#1e3a5f] rounded-lg hover:bg-[#162d4a] transition-colors">
    Simpan Keputusan
</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════ SCORE MODAL ═══════════════ --}}
<div id="score-modal"
class="
    hidden fixed inset-0
    bg-black/50
    z-[500]
    flex items-end sm:items-center justify-center
    p-0 sm:p-6
">

    <div class="
        bg-[#f8fafc]
        w-full
        sm:max-w-7xl
        max-h-[100dvh]
        sm:max-h-[94vh]
        overflow-y-auto
        
        border border-slate-200
        shadow-2xl
    ">

        {{-- HEADER --}}
        <div class="
            flex items-center justify-between
            px-5 sm:px-6
            py-4
            border-b border-slate-200
            bg-white sticky
        ">

            <div class="min-w-0">

                <div class="
                    text-[10px]
                    font-bold
                    uppercase
                    tracking-[0.2em]
                    text-slate-400
                ">
                    Rubrik Penilaian
                </div>

                <div class="
                    mt-1
                    text-sm sm:text-base
                    font-bold
                    text-slate-800
                    truncate
                ">
                    <span id="score-ss-num"
                        class="text-[#1e3a5f] font-mono">
                    </span> - 
                </div>

            </div>

            <button
                onclick="closeModal('score-modal')"
                class="
                    w-10 h-10
                    rounded-xl
                    border border-slate-200
                    bg-white
                    flex items-center justify-center
                    text-slate-400
                    hover:bg-slate-100
                    transition
                    shrink-0
                "
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

        </div>

        <form id="score-form" method="POST">
            @csrf

            <div class="p-4 sm:p-6">

                @php
                    $formula = \App\Models\SuggestionRewardFormula::with(['items','tiers'])
                        ->where('is_active', true)
                        ->first();
                @endphp

                @if($formula)

                {{-- INFO --}}
                <div class="
                    mb-5
                    rounded-2xl
                    border border-blue-100
                    bg-gradient-to-r from-blue-50 to-white
                    p-4 sm:p-5
                ">

                    <div class="flex items-start gap-3">

                        <div class="
                            w-11 h-11
                            rounded-2xl
                            bg-blue-100
                            text-blue-700
                            flex items-center justify-center
                            shrink-0
                        ">
                            <i class="fa-solid fa-ranking-star"></i>
                        </div>

                        <div>

                            <div class="
                                text-[10px]
                                font-bold
                                uppercase
                                tracking-[0.2em]
                                text-blue-500
                            ">
                                Formula Penilaian Aktif
                            </div>

                            <div class="
                                mt-1
                                text-sm
                                font-bold
                                text-slate-800
                            ">
                                {{ $formula->name }}
                            </div>

                        </div>

                    </div>

                </div>

                {{-- DESKTOP --}}
                <div class="hidden lg:block rounded-2xl border border-slate-200 bg-white">

                    <table class="w-full text-sm border-collapse">

                        <thead>

                            <tr class="bg-gradient-to-r from-slate-50 to-slate-100">

                                <th class="
                                    px-4 py-4
                                    text-center
                                    font-bold
                                    text-[10px]
                                    uppercase
                                    tracking-wider
                                    text-slate-500
                                    border-b border-slate-200
                                    w-14
                                ">
                                    No
                                </th>

                                <th class="
                                    px-4 py-4
                                    text-left
                                    font-bold
                                    text-[10px]
                                    uppercase
                                    tracking-wider
                                    text-slate-500
                                    border-b border-slate-200
                                ">
                                    Kriteria
                                </th>

                                <th class="px-4 py-4 text-center border-b border-slate-200 min-w-[150px]">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-bold text-red-500">A</span>
                                        <span class="text-[10px] text-slate-400">1–2</span>
                                    </div>
                                </th>

                                <th class="px-4 py-4 text-center border-b border-slate-200 min-w-[150px]">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-bold text-orange-500">B</span>
                                        <span class="text-[10px] text-slate-400">3–4</span>
                                    </div>
                                </th>

                                <th class="px-4 py-4 text-center border-b border-slate-200 min-w-[150px]">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-bold text-amber-500">C</span>
                                        <span class="text-[10px] text-slate-400">5–6</span>
                                    </div>
                                </th>

                                <th class="px-4 py-4 text-center border-b border-slate-200 min-w-[150px]">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-bold text-emerald-500">D</span>
                                        <span class="text-[10px] text-slate-400">7–8</span>
                                    </div>
                                </th>

                                <th class="px-4 py-4 text-center border-b border-slate-200 min-w-[150px]">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-bold text-blue-500">E</span>
                                        <span class="text-[10px] text-slate-400">9–10</span>
                                    </div>
                                </th>

                                <th class="
                                    px-4 py-4
                                    text-center
                                    font-bold
                                    text-[10px]
                                    uppercase
                                    tracking-wider
                                    text-slate-500
                                    border-b border-slate-200
                                    w-36
                                ">
                                    Point
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($formula->items as $index => $item)

                            @php
                                $cl = \App\Models\SuggestionRewardFormulaItemCriteria::where('item_id',$item->id)
                                    ->orderBy('min_point')
                                    ->get();

                                $grA = $cl->firstWhere('grade','A');
                                $grB = $cl->firstWhere('grade','B');
                                $grC = $cl->firstWhere('grade','C');
                                $grD = $cl->firstWhere('grade','D');
                                $grE = $cl->firstWhere('grade','E');
                            @endphp

                            <tr class="bg-white hover:bg-slate-50">

                                <td class="
                                    px-4 py-5
                                    text-center
                                    border-b border-slate-100
                                ">

                                    <div class="
                                        w-8 h-8
                                        rounded-xl
                                        bg-slate-100
                                        text-slate-500
                                        font-bold
                                        text-xs
                                        flex items-center justify-center
                                        mx-auto
                                    ">
                                        {{ $index + 1 }}
                                    </div>

                                </td>

                                <td class="
                                    px-4 py-5
                                    border-b border-slate-100
                                    align-top
                                ">

                                    <div class="
                                        text-xs
                                        font-bold
                                        uppercase
                                        tracking-wide
                                        text-slate-800
                                        leading-relaxed
                                    ">
                                        {{ $item->item_name }}
                                    </div>

                                </td>

                                <td class="px-4 py-5 text-[11px] leading-relaxed text-slate-500 border-b border-slate-100 align-top">
                                    {{ $grA?->description ?? '—' }}
                                </td>

                                <td class="px-4 py-5 text-[11px] leading-relaxed text-slate-500 border-b border-slate-100 align-top">
                                    {{ $grB?->description ?? '—' }}
                                </td>

                                <td class="px-4 py-5 text-[11px] leading-relaxed text-slate-500 border-b border-slate-100 align-top">
                                    {{ $grC?->description ?? '—' }}
                                </td>

                                <td class="px-4 py-5 text-[11px] leading-relaxed text-slate-500 border-b border-slate-100 align-top">
                                    {{ $grD?->description ?? '—' }}
                                </td>

                                <td class="px-4 py-5 text-[11px] leading-relaxed text-slate-500 border-b border-slate-100 align-top">
                                    {{ $grE?->description ?? '—' }}
                                </td>

                                <td class="
                                    px-4 py-5
                                    border-b border-slate-100
                                ">

                                    <div class="flex justify-center">

                                        <input
                                            type="number"
                                            name="scores[{{ $item->id }}]"
                                            class="
                                                score-select
                                                w-24 h-12
                                                text-center
                                                text-base
                                                font-bold
                                                rounded-2xl
                                                border-2 border-slate-200
                                                bg-slate-50
                                                outline-none
                                                focus:border-[#1e3a5f]
                                                focus:bg-white
                                            "
                                            max="10"
                                            placeholder="1–10"
                                            data-item-id="{{ $item->id }}"
                                            onchange="updateScoreTotal()"
                                        />

                                    </div>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- MOBILE --}}
                <div class="lg:hidden space-y-4">

                    @foreach($formula->items as $index => $item)

                    @php
                        $cl = \App\Models\SuggestionRewardFormulaItemCriteria::where('item_id',$item->id)
                            ->orderBy('min_point')
                            ->get();

                        $grA = $cl->firstWhere('grade','A');
                        $grB = $cl->firstWhere('grade','B');
                        $grC = $cl->firstWhere('grade','C');
                        $grD = $cl->firstWhere('grade','D');
                        $grE = $cl->firstWhere('grade','E');
                    @endphp

                    <div class="
                        rounded-2xl
                        border border-slate-200
                        bg-white
                        overflow-hidden
                    ">

                        <div class="
                            px-4 py-3
                            border-b border-slate-100
                            bg-slate-50
                        ">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <div class="
                                        text-[10px]
                                        font-bold
                                        uppercase
                                        tracking-wider
                                        text-slate-400
                                    ">
                                        Kriteria {{ $index + 1 }}
                                    </div>

                                    <div class="
                                        mt-1
                                        text-sm
                                        font-bold
                                        text-slate-800
                                    ">
                                        {{ $item->item_name }}
                                    </div>

                                </div>

                                <div class="
                                    w-10 h-10
                                    rounded-xl
                                    bg-blue-50
                                    border border-blue-100
                                    flex items-center justify-center
                                    text-blue-600
                                ">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>

                            </div>

                        </div>

                        <div class="p-4 space-y-2">

                            @foreach([
                                ['A (1–2)', $grA?->description, 'text-red-500'],
                                ['B (3–4)', $grB?->description, 'text-orange-500'],
                                ['C (5–6)', $grC?->description, 'text-amber-500'],
                                ['D (7–8)', $grD?->description, 'text-emerald-500'],
                                ['E (9–10)', $grE?->description, 'text-blue-500'],
                            ] as $grade)

                            <div class="rounded-xl border border-slate-100 p-3">

                                <div class="text-[10px] font-bold {{ $grade[2] }}">
                                    {{ $grade[0] }}
                                </div>

                                <div class="mt-1 text-[11px] text-slate-500 leading-relaxed">
                                    {{ $grade[1] ?? '—' }}
                                </div>

                            </div>

                            @endforeach

                            <div class="pt-3 border-t border-dashed border-slate-200">

                                <label class="
                                    block
                                    text-[11px]
                                    font-bold
                                    text-slate-500
                                    mb-2
                                ">
                                    Input Nilai
                                </label>

                                <input
                                    type="number"
                                    name="scores[{{ $item->id }}]"
                                    class="
                                        score-select
                                        w-full
                                        h-12
                                        text-center
                                        text-base
                                        font-bold
                                        border-2 border-slate-200
                                        rounded-xl
                                        bg-slate-50
                                        outline-none
                                        focus:border-[#1e3a5f]
                                    "
                                    max="10"
                                    placeholder="1–10"
                                    data-item-id="{{ $item->id }}"
                                    onchange="updateScoreTotal()"
                                />

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                {{-- SUMMARY --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5 mb-5">

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Total Nilai
                        </div>
                        <div id="total-score" class="mt-2 text-3xl font-black text-[#1e3a5f]">
                            0
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Rata-rata
                        </div>
                        <div id="avg-score" class="mt-2 text-3xl font-black text-emerald-600">
                            0.00
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600">
                            Estimasi Reward
                        </div>
                        <div id="estimated-reward" class="mt-2 text-lg font-black text-amber-800">
                            —
                        </div>
                    </div>

                </div>

                <script id="tiers-data" type="application/json">
                    @json($formula->tiers->map(fn($t) => [
                        'min'=>$t->min_score,
                        'max'=>$t->max_score,
                        'reward'=>$t->reward_amount
                    ]))
                </script>

                @else

                <div class="
                    py-16
                    text-center
                    rounded-2xl
                    border border-dashed border-slate-200
                    bg-white
                ">

                    <div class="text-4xl text-slate-200 mb-3">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>

                    <div class="text-sm font-medium text-slate-500">
                        Tidak ada formula penilaian aktif
                    </div>

                </div>

                @endif

                {{-- NOTE --}}
                <div class="mt-5">

                    <label class="
                        block
                        text-[11px]
                        font-bold
                        uppercase
                        tracking-wider
                        text-slate-500
                        mb-2
                    ">
                        Catatan Manager
                    </label>

                    <textarea
                        name="manager_note"
                        rows="4"
                        class="
                            w-full
                            rounded-2xl
                            border border-slate-200
                            bg-white
                            px-4 py-3
                            text-sm
                            outline-none
                            resize-none
                            focus:border-[#1e3a5f]
                        "
                        placeholder="Tambahkan catatan penilaian..."
                    ></textarea>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="
                flex items-center justify-end gap-3
                px-5 sm:px-6
                py-4
                border-t border-slate-200
                bg-white
            ">

                <button
                    type="button"
                    onclick="closeModal('score-modal')"
                    class="
                        h-11
                        px-5
                        rounded-xl
                        border border-slate-200
                        bg-white
                        text-sm
                        font-semibold
                        text-slate-600
                        hover:bg-slate-50
                    "
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="
                        h-11
                        px-5
                        rounded-xl
                        bg-[#1e3a5f]
                        text-sm
                        font-semibold
                        text-white
                        hover:bg-[#162d4a]
                    "
                >
                    Simpan Penilaian
                </button>

            </div>

        </form>

    </div>

</div>

<div id="ssSlideOver" class="fixed inset-0 z-50 hidden">
 
    {{-- overlay --}}
    <div id="ssOverlay"
         class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300"
         onclick="closeSlideOver()"></div>
 
    {{-- panel --}}
  <div id="ssPanel"
     class="absolute right-0 top-0 h-full w-full md:w-1/2 lg:w-1/2
            bg-white shadow-2xl translate-x-full
            transition-transform duration-300 ease-out flex flex-col">
 
        {{-- ─── HEADER GELAP ─── --}}
      <div class="flex-shrink-0 bg-white border-b border-slate-200 px-5 py-4 relative overflow-hidden">

    <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-slate-100"></div>
    <div class="absolute right-14 -bottom-8 w-20 h-20 rounded-full bg-slate-50"></div>

    <button onclick="closeSlideOver()"
            class="absolute top-3 right-3 z-10 w-7 h-7 flex items-center justify-center
                   rounded-md border border-slate-200 bg-white
                   text-slate-500 hover:bg-slate-100 transition-colors">
        <i class="fa-solid fa-xmark text-xs"></i>
    </button>

    <div class="flex items-center gap-2 mb-2 relative z-[1]">
        <span id="ssNumber" class="text-[11px] font-mono font-bold text-sky-600 tracking-[.04em]">—</span>
        <div class="w-px h-2.5 bg-slate-200"></div>
        <span id="ssPeriodBadge" class="text-[10px] font-semibold text-slate-500">—</span>
    </div>

    <div id="ssTitle"
         class="text-[15px] font-bold text-slate-800 leading-snug pr-8 mb-1.5 relative z-[1]">—</div>

    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mb-3 relative z-[1] flex-wrap">
        <span id="ssUser">—</span>
        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
        <span id="ssDeptHeader">—</span>
       
    </div>

    <div class="flex items-center gap-1.5 flex-wrap relative z-[1]">
        <div id="ssCategories" class="flex gap-1.5 flex-wrap"></div>
        <div id="ssStatus" class="ml-auto"></div>
    </div>
</div>
 
        {{-- progress strip --}}
        <div class="h-[3px] bg-[#1e293b] flex-shrink-0">
            <div id="ssProgressBar" class="h-full bg-sky-400 transition-all duration-700" style="width:0%"></div>
        </div>

 
        {{-- ─── TAB NAV ─── --}}
      <div class="flex border-b border-slate-100 bg-white flex-shrink-0">

    <button class="ss-tab flex-1 py-2.5 flex items-center justify-center gap-1.5
                   text-[10px] font-bold uppercase tracking-[.05em]
                   text-slate-500 border-b-2 border-transparent transition-all active"
            onclick="ssPanelTab('detail', this)">
        <i class="fa-solid fa-circle-info text-[11px]"></i>
        Detail
    </button>

    <button class="ss-tab flex-1 py-2.5 flex items-center justify-center gap-1.5
                   text-[10px] font-bold uppercase tracking-[.05em]
                   text-slate-500 border-b-2 border-transparent transition-all"
            onclick="ssPanelTab('lampiran', this)">
        <i class="fa-solid fa-paperclip text-[11px]"></i>
        Lampiran
    </button>

    <button class="ss-tab flex-1 py-2.5 flex items-center justify-center gap-1.5
                   text-[10px] font-bold uppercase tracking-[.05em]
                   text-slate-500 border-b-2 border-transparent transition-all"
            onclick="ssPanelTab('nilai', this)">
        <i class="fa-solid fa-star text-[11px]"></i>
        Nilai
    </button>

    <button class="ss-tab flex-1 py-2.5 flex items-center justify-center gap-1.5
                   text-[10px] font-bold uppercase tracking-[.05em]
                   text-slate-500 border-b-2 border-transparent transition-all"
            onclick="ssPanelTab('riwayat', this)">
        <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
        Riwayat
    </button>

</div>
 
        {{-- ─── BODY ─── --}}
        <div class="flex-1 overflow-y-auto bg-slate-50">
 
            {{-- ══ TAB DETAIL ══ --}}
            <div id="ss-tab-detail" class="ss-pane p-4 space-y-3">
 
                {{-- Meta grid --}}
               <div class="grid grid-cols-2 gap-px bg-slate-100 rounded-xl overflow-hidden border border-slate-200">

    <div class="bg-white px-4 py-3">
        <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[.06em] text-slate-400 mb-1">
            <i class="fa-solid fa-location-dot text-[10px]"></i>
            Lokasi
        </div>
        <div id="ssLocation" class="text-[12px] font-semibold text-slate-900">—</div>
    </div>

    <div class="bg-white px-4 py-3">
        <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[.06em] text-slate-400 mb-1">
            <i class="fa-regular fa-calendar text-[10px]"></i>
            Tanggal Pengajuan
        </div>
        <div id="ssDate" class="text-[12px] font-semibold text-slate-900">—</div>
    </div>

</div>
 
               <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <button onclick="ssAccordion(this)"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">

        <div class="flex items-center gap-2 text-slate-600">
            <i class="fa-solid fa-triangle-exclamation text-[11px] text-slate-400"></i>
            <span class="text-[10px] font-bold uppercase tracking-[.07em]">
                Penetapan Tema & Target
            </span>
        </div>

        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
    </button>

    <div class="ss-acc-body hidden border-t border-slate-100 px-4 py-3.5 text-[12.5px] text-slate-600">
        <div id="ssBackground" class="whitespace-pre-line">>—</div>
    </div>
</div>
 
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <button onclick="ssAccordion(this)"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">

        <div class="flex items-center gap-2 text-slate-600">
            <i class="fa-solid fa-diagram-project text-[11px] text-slate-400"></i>
            <span class="text-[10px] font-bold uppercase tracking-[.07em]">
                Analisa Penyebab Masalah
            </span>
        </div>

        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
    </button>

   <div class="ss-acc-body hidden border-t border-slate-100 px-4 py-3.5 text-[12.5px] text-slate-600 space-y-4">

  <!-- Problem -->
  <div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-1">
      Analisa Masalah
    </div>
    <div id="ssProblem" class="whitespace-pre-line text-slate-700">
      —
    </div>
  </div>

  <!-- Divider -->
  <div class="border-t border-slate-100"></div>

  <!-- Root Cause -->
  <div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-1">
      Akar Masalah
    </div>
    <div id="ssRoot" class="whitespace-pre-line text-slate-700">
      —
    </div>
  </div>

</div>
</div>
 
               <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <button onclick="ssAccordion(this)"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">

        <div class="flex items-center gap-2 text-slate-600">
            <i class="fa-solid fa-gears text-[11px] text-slate-400"></i>
            <span class="text-[10px] font-bold uppercase tracking-[.07em]">
                Tindakan Perbaikan
            </span>
        </div>

        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
    </button>

    <div class="ss-acc-body hidden border-t border-slate-100 px-4 py-3.5 text-[12.5px] text-slate-600">
        <div id="ssSolution" class="whitespace-pre-line">>—</div>
    </div>
</div>
 
              <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <button onclick="ssAccordion(this)"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">

        <div class="flex items-center gap-2 text-slate-600">
            <i class="fa-solid fa-chart-line text-[11px] text-slate-400"></i>
            <span class="text-[10px] font-bold uppercase tracking-[.07em]">
                Evaluasi Hasil
            </span>
        </div>

        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
    </button>

    <div class="ss-acc-body hidden border-t border-slate-100 px-4 py-3.5 text-[12.5px] text-slate-600">
    <div class="grid grid-cols-2 gap-3">

        <!-- Sebelum -->
        <div class="rounded-lg overflow-hidden border border-amber-200">
            <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border-b border-amber-200">
                <i class="fa-solid fa-circle-xmark text-[10px] text-amber-500"></i>
                <span class="text-[9px] font-bold uppercase tracking-[.06em] text-amber-700">Sebelum Perbaikan</span>
            </div>
            <div id="ssEvalBefore" class="px-3 py-2.5 whitespace-pre-line bg-white text-[12px]">—</div>
        </div>

        <!-- Sesudah -->
        <div class="rounded-lg overflow-hidden border border-emerald-200">
            <div class="flex items-center gap-2 px-3 py-2 bg-emerald-50 border-b border-emerald-200">
                <i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i>
                <span class="text-[9px] font-bold uppercase tracking-[.06em] text-emerald-700">Sesudah Perbaikan</span>
            </div>
            <div id="ssEvalAfter" class="px-3 py-2.5 whitespace-pre-line bg-white text-[12px]">—</div>
        </div>

    </div>
</div>
</div>
 
               <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <button onclick="ssAccordion(this)"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">

        <div class="flex items-center gap-2 text-slate-600">
            <i class="fa-solid fa-shield-halved text-[11px] text-slate-400"></i>
            <span class="text-[10px] font-bold uppercase tracking-[.07em]">
                Standarisasi
            </span>
        </div>

        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
    </button>

   <div class="ss-acc-body hidden border-t border-slate-100 px-4 py-3.5 text-[12.5px] text-slate-600">

    <!-- Checkbox types -->
    <div id="ssStdTypes" class="flex flex-wrap gap-2 mb-3"></div>

    <!-- Nomor dokumen -->
    <div class="flex items-start gap-2">
        <i class="fa-solid fa-file-lines text-[11px] text-slate-400 mt-0.5"></i>
        <div id="ssStandard" class="text-[12px] text-slate-700">—</div>
    </div>

</div>
</div>
 
            </div>{{-- /ss-tab-detail --}}
 
            {{-- ══ TAB LAMPIRAN ══ --}}
            {{-- PENTING: id prefix "ss-tab-" agar tidak bentrok dengan tab lain di halaman --}}
            <div id="ss-tab-lampiran" class="ss-pane hidden p-4 space-y-3">
 
                {{-- BEFORE --}}
                <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-50">
                        <div class="w-[3px] h-3.5 rounded-sm bg-red-400 flex-shrink-0"></div>
                        <span class="text-[10px] font-bold uppercase tracking-[.07em] text-slate-500 flex-1">Foto Sebelum Perbaikan</span>
                        <span class="text-[9px] font-bold px-2 py-px rounded-full bg-red-50 text-red-500 border border-red-100">BEFORE</span>
                    </div>
                    <div id="beforeCarousel"
                         class="flex gap-3 overflow-x-auto px-4 py-3 scroll-smooth snap-x snap-mandatory">
                    </div>
                    <div id="beforeEmpty" class="hidden px-4 py-7 text-center text-[12px] text-slate-400 italic">
                        Belum ada foto sebelum perbaikan.
                    </div>
                </div>
 
                {{-- AFTER --}}
                <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-50">
                        <div class="w-[3px] h-3.5 rounded-sm bg-green-400 flex-shrink-0"></div>
                        <span class="text-[10px] font-bold uppercase tracking-[.07em] text-slate-500 flex-1">Foto Sesudah Perbaikan</span>
                        <span class="text-[9px] font-bold px-2 py-px rounded-full bg-green-50 text-green-600 border border-green-100">AFTER</span>
                    </div>
                    <div id="afterCarousel"
                         class="flex gap-3 overflow-x-auto px-4 py-3 scroll-smooth snap-x snap-mandatory">
                    </div>
                    <div id="afterEmpty" class="hidden px-4 py-7 text-center text-[12px] text-slate-400 italic">
                        Belum ada foto sesudah perbaikan.
                    </div>
                </div>
 
            </div>{{-- /ss-tab-lampiran --}}
 
            {{-- ══ TAB NILAI ══ --}}
            <div id="ss-tab-nilai" class="ss-pane hidden p-4 space-y-3">
 
                {{-- Score cards container - diisi oleh renderScore() --}}
                <div id="ssScoreCards" class="grid grid-cols-3 gap-2"></div>
 
              <div id="ssTotalRow"
     class="hidden bg-white rounded-xl border border-slate-200 overflow-hidden">

    <div class="grid grid-cols-3 divide-x">

        {{-- TOTAL --}}
        <div class="px-4 py-3 text-center">
            <div class="text-[9px] font-bold uppercase tracking-[.06em] text-slate-400">
                Total
            </div>
            <div id="ssTotalScore"
                 class="text-[16px] font-bold font-mono text-slate-700">
                0
            </div>
        </div>

        {{-- 🔥 RATA-RATA (HIGHLIGHT) --}}
        <div class="px-4 py-3 text-center bg-sky-50">
            <div class="text-[9px] font-bold uppercase tracking-[.06em] text-sky-600">
                Rata-rata
            </div>
            <div id="ssAvgScore"
                 class="text-[22px] font-extrabold font-mono text-sky-600">
                0
            </div>
        </div>

        {{-- REWARD --}}
        <div class="px-4 py-3 text-center">
            <div class="text-[9px] font-bold uppercase tracking-[.06em] text-slate-400">
                Reward
            </div>
            <div id="ssReward"
                 class="text-[16px] font-bold font-mono text-emerald-600">
                0
            </div>
        </div>

    </div>

</div>
 
                {{-- Catatan manager --}}
                <div id="ssManagerNoteWrap"
                     class="hidden bg-white rounded-xl border border-slate-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-50">
                        <div class="w-[3px] h-3.5 rounded-sm bg-violet-400 flex-shrink-0"></div>
                        <span class="text-[10px] font-bold uppercase tracking-[.07em] text-slate-500">Catatan Manager</span>
                    </div>
                    <p id="ssManagerNote" class="px-4 py-3.5 text-[12.5px] text-slate-600 leading-relaxed">—</p>
                </div>
 
                {{-- Empty state - ditampilkan saat belum ada nilai --}}
                <div id="ssNilaiEmpty"
                     class="bg-white rounded-xl border border-slate-100 px-4 py-10 text-center text-[12px] text-slate-400 italic">
                    Belum ada penilaian untuk SS ini.
                </div>
 
            </div>{{-- /ss-tab-nilai --}}
 
          <div id="ss-tab-riwayat" class="ss-pane hidden p-4">
    <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
 
        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-50">
            <div class="w-[3px] h-3.5 rounded-sm bg-sky-400 flex-shrink-0"></div>
            <span class="text-[10px] font-bold uppercase tracking-[.07em] text-slate-500">
                Riwayat Aktivitas
            </span>
        </div>
 
        <div id="ssRiwayat" class="px-4 py-3">
            <div class="py-8 text-center text-sm text-slate-400 italic">
                Memuat riwayat...
            </div>
        </div>
 
    </div>
</div>
 
        </div>{{-- /body --}}
 
        {{-- ─── FOOTER ─── --}}
        <div id="ssFooter"
             class="flex-shrink-0 px-5 py-3 border-t border-slate-100 bg-white
                    flex items-center justify-between gap-3
                    shadow-[0_-2px_10px_rgba(0,0,0,.04)]">
            <div id="ssFooterInfo" class="text-[10px] text-slate-400 truncate flex-1">—</div>
            <div id="ssFooterBtns" class="flex gap-1.5 flex-shrink-0"></div>
        </div>
 
    </div>{{-- /ssPanel --}}
</div>{{-- /ssSlideOver --}}

{{-- ═══════════════ SCRIPTS ═══════════════ --}}
<script>
  /* ─── state ─── */
let currentSSId = null;
 
/* ════════════════════════════════
   OPEN / CLOSE
════════════════════════════════ */
async function openSlideOver(id, mode = 'view') {
    currentSSId = id;
 
    const wrapper = document.getElementById('ssSlideOver');
    const panel   = document.getElementById('ssPanel');
    const overlay = document.getElementById('ssOverlay');
 
    /* tampil skeleton dulu */
    ssSetLoading();
    ssRenderFooter([], id);
 
    wrapper.classList.remove('hidden');
    requestAnimationFrame(() => {
        panel.classList.remove('translate-x-full');
        overlay.classList.remove('opacity-0');
    });
 
    /* reset ke tab detail */
    ssPanelTab('detail', document.querySelector('#ssPanel .ss-tab'));
 
    try {
        const res = await fetch(`/suggestion/${id}/detail`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const d = await res.json();
 
        ssFillHeader(d);
        ssFillDetail(d);
        ssFillLampiran(d);
        ssFillNilai(d);       /* ← kunci perbaikan */
        ssFillRiwayat(d);
        ssRenderFooter(d.actions || [], d.id, d.ss_number || '', mode);
 
    } catch (err) {
        console.error('[SS Panel]', err);
        Toast.fire({ icon: 'error', title: 'Gagal memuat data SS' });
        closeSlideOver();
    }
}
 
function closeSlideOver() {
    document.getElementById('ssPanel').classList.add('translate-x-full');
    document.getElementById('ssOverlay').classList.add('opacity-0');
    setTimeout(() => document.getElementById('ssSlideOver').classList.add('hidden'), 300);
    currentSSId = null;
}
 
/* ════════════════════════════════
   LOADING STATE
════════════════════════════════ */
function ssSetLoading() {
    document.getElementById('ssNumber').textContent      = '...';
    document.getElementById('ssTitle').textContent       = 'Memuat data...';
    document.getElementById('ssUser').textContent        = '';
    document.getElementById('ssDeptHeader').textContent  = '';
    document.getElementById('ssStatus').innerHTML        = '';
    document.getElementById('ssCategories').innerHTML    = '';
    document.getElementById('ssProgressBar').style.width = '0%';
    /* reset nilai tab agar tidak tampil data lama */
    document.getElementById('ssScoreCards').innerHTML    = '';
    document.getElementById('ssTotalRow').classList.add('hidden');
    document.getElementById('ssManagerNoteWrap').classList.add('hidden');
    document.getElementById('ssNilaiEmpty').classList.remove('hidden');
}
 
/* ════════════════════════════════
   FILL HEADER
════════════════════════════════ */
function ssFillHeader(d) {
    document.getElementById('ssNumber').textContent      = d.ss_number    || '—';
    document.getElementById('ssPeriodBadge').textContent = d.period_name  || d.period || '—';
    document.getElementById('ssTitle').textContent       = d.theme        || '—';
    document.getElementById('ssUser').textContent        = d.user         || '—';
    document.getElementById('ssDeptHeader').textContent  = d.department   || '—';
 
    document.getElementById('ssStatus').innerHTML =
        `<span class="text-[10px] font-bold px-2.5 py-1 rounded-full border ${ssStatusClass(d.status)}">
            ${ssStatusLabel(d.status)}
         </span>`;
 
    document.getElementById('ssProgressBar').style.width = ssStatusProgress(d.status) + '%';
 
    document.getElementById('ssCategories').innerHTML = (d.categories || [])
        .map(c => `<span class="text-[10px] px-2 py-0.5 rounded border border-white/10 bg-white/[.07] text-slate-400 font-medium">${c}</span>`)
        .join('');
}
 
/* ════════════════════════════════
   FILL DETAIL TAB
════════════════════════════════ */
function ssFillDetail(d) {

  const stdTypeMap = {
    sop: 'Create / Revise SOP / IK / OPL',
    form: 'Create / Revise Form',
    other: 'Lainnya'
  };

  const rawType = (d.std_type ?? '')
    .toString()
    .trim()
    .toLowerCase();

  const stdLabel = stdTypeMap[rawType] || d.std_type || '—';

  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value || '—';
  };

  setText('ssLocation', d.location);
  setText('ssDate', d.discovery_date);
  setText('ssBackground', d.background);
  setText('ssProblem', d.problem);
  setText('ssRoot', d.root_cause);
  setText('ssSolution', d.improvement_activity);
  setText('ssEvalBefore', d.evaluation_before);
  setText('ssEvalAfter', d.evaluation_after);
  setText('ssStdTypes', stdLabel);
  setText('ssStandard', d.standardization);
}
 
/* ════════════════════════════════
   FILL LAMPIRAN TAB
════════════════════════════════ */
function ssFillLampiran(d) {
    const before = d.photos_before || [];
    const after  = d.photos_after  || [];
 
    const beforeEl    = document.getElementById('beforeCarousel');
    const afterEl     = document.getElementById('afterCarousel');
    const beforeEmpty = document.getElementById('beforeEmpty');
    const afterEmpty  = document.getElementById('afterEmpty');
 
    beforeEl.innerHTML = '';
    afterEl.innerHTML  = '';
 
    if (before.length === 0) {
        beforeEmpty.classList.remove('hidden');
    } else {
        beforeEmpty.classList.add('hidden');
        before.forEach(item => {
            const src = ssPhotoSrc(item);
            beforeEl.innerHTML += ssPhotoCard(src);
        });
    }
 
    if (after.length === 0) {
        afterEmpty.classList.remove('hidden');
    } else {
        afterEmpty.classList.add('hidden');
        after.forEach(item => {
            const src = ssPhotoSrc(item);
            afterEl.innerHTML += ssPhotoCard(src);
        });
    }
}
 
function ssPhotoSrc(item) {
    if (item.url)       return item.url;
    if (item.file_path) return '/' + item.file_path.replace(/^\//, '');
    return '/img/no-image.png';
}
 
function ssPhotoCard(src) {
    return `
        <div class="flex-shrink-0 w-36 h-28 rounded-lg overflow-hidden border border-slate-200
                    bg-slate-100 snap-start cursor-pointer hover:opacity-85 transition-opacity"
             onclick="ssPreviewPhoto('${src}')">
            <img src="${src}" class="w-full h-full object-cover"
                 onerror="this.src='/img/no-image.png'"/>
        </div>`;
}
 
function ssPreviewPhoto(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: 'Foto lampiran',
        showConfirmButton: false,
        background: '#0f172a',
        padding: '8px',
        customClass: { popup: 'rounded-xl' }
    });
}
 
/* ════════════════════════════════
   FILL NILAI TAB
   ─────────────────────────────
   API mengembalikan d.scores (array dari SuggestionScore)
   tiap item: { id, score, formula_item: { item_name }, ... }
   ATAU
   d.score.criteria = [{label, value}], d.score.total, d.score.manager_note
   ─────────────────────────────
   Fungsi ini menangani KEDUA format sekaligus.
════════════════════════════════ */
function ssFillNilai(d) {
    const cardsEl  = document.getElementById('ssScoreCards');
    const totalRow = document.getElementById('ssTotalRow');
    const totalEl  = document.getElementById('ssTotalScore');
    const avgEl    = document.getElementById('ssAvgScore');
    const rewardEl = document.getElementById('ssReward');

    const noteWrap = document.getElementById('ssManagerNoteWrap');
    const noteEl   = document.getElementById('ssManagerNote');
    const emptyEl  = document.getElementById('ssNilaiEmpty');

    /* reset */
    cardsEl.innerHTML = '';
    totalRow.classList.add('hidden');
    noteWrap.classList.add('hidden');
    emptyEl.classList.remove('hidden');

    const COLORS = ['#a78bfa','#38bdf8','#4ade80','#fb923c','#f87171','#fbbf24','#34d399'];

    /* ───────── FORMAT A ───────── */
    const scoresArr = d.scores || [];

    if (scoresArr.length > 0) {
        emptyEl.classList.add('hidden');

        let total = 0;

        scoresArr.forEach((s, i) => {
            const name  = s.formula_item?.item_name || `Item ${i + 1}`;
            const value = parseFloat(s.score ?? 0);

            total += value;

            const pct   = Math.min(100, value * 10);
            const color = COLORS[i % COLORS.length];

            cardsEl.innerHTML += `
                <div class="bg-white rounded-xl border border-slate-100 p-3 text-center">
                    <div class="text-[22px] font-extrabold font-mono text-slate-900">${value}</div>
                    <div class="text-[9px] font-bold uppercase tracking-[.06em] text-slate-400 mt-0.5">${name}</div>
                    <div class="h-[3px] bg-slate-100 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full" style="background:${color};width:${pct}%"></div>
                    </div>
                </div>`;
        });

        const count  = scoresArr.length;
        const avg    = parseFloat(d.score_total ?? 0);
        const reward = parseFloat(d.reward_amount ?? 0);

        totalEl.textContent  = total.toFixed(1);
        avgEl.textContent    = avg.toFixed(2);
        rewardEl.textContent = reward.toLocaleString('id-ID');

        totalRow.classList.remove('hidden');

        const note = d.manager_note || d.score?.manager_note || '';
        if (note) {
            noteEl.textContent = note;
            noteWrap.classList.remove('hidden');
        }

        return;
    }

    /* ───────── FORMAT B ───────── */
    const scoreObj = d.score || null;

    if (scoreObj && scoreObj.criteria && scoreObj.criteria.length > 0) {
        emptyEl.classList.add('hidden');

        let total = 0;

        scoreObj.criteria.forEach((c, i) => {
            const value = parseFloat(c.value ?? 0);
            total += value;

            const pct   = Math.min(100, value * 10);
            const color = COLORS[i % COLORS.length];

            cardsEl.innerHTML += `
                <div class="bg-white rounded-xl border border-slate-100 p-3 text-center">
                    <div class="text-[22px] font-extrabold font-mono text-slate-900">${value}</div>
                    <div class="text-[9px] font-bold uppercase tracking-[.06em] text-slate-400 mt-0.5">${c.label}</div>
                    <div class="h-[3px] bg-slate-100 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full" style="background:${color};width:${pct}%"></div>
                    </div>
                </div>`;
        });

        const count  = scoreObj.criteria.length;
        const avg    = parseFloat(d.score_total ?? 0);
        const reward = parseFloat(d.reward_amount ?? 0);

        totalEl.textContent  = total.toFixed(1);
        avgEl.textContent    = avg.toFixed(2);
        rewardEl.textContent = reward.toLocaleString('id-ID');

        totalRow.classList.remove('hidden');

        if (scoreObj.manager_note) {
            noteEl.textContent = scoreObj.manager_note;
            noteWrap.classList.remove('hidden');
        }
    }
}
 
/* ════════════════════════════════
   FILL RIWAYAT TAB
════════════════════════════════ */
function ssFillRiwayat(d) {
    const el = document.getElementById('ssRiwayat');
    if (!el) return;
 
    /* ─── Susun steps ─── */
    const steps = [];
 
    /* 2 ── Submit */
    if (d.created_at) {
        steps.push({
            title:  'SS Dibuat & Disubmit',
            by:     d.user_name || '',
            time:   d.created_at,
            status: 'done',
        });
    }
 
    /* 3 ── Review SPV */
    if (d.reviewed_at_spv) {
        /* tentukan label berdasarkan status SPV */
        const spvLabel = {
            approved_spv: 'Disetujui',
            rejected_spv: 'Ditolak',
            returned_spv: 'Dikembalikan',
        }[d.status] || 'Disetujui';
 
        steps.push({
            title:  spvLabel,
            by:     d.reviewed_by_spv || '',
            time:   d.reviewed_at_spv,
            note:   d.spv_note || '',
            status: 'done',
        });
 
        /* 3b ── Menunggu Review Manager (muncul setelah SPV approve,
                 hanya jika belum scored) */
        if (!d.scored_at && ['approved_spv'].includes(d.status)) {
            steps.push({
                title:  'Menunggu Review',
                by:     '',
                time:   d.reviewed_at_spv,   /* waktu mulai menunggu = waktu SPV approve */
                status: 'active',
            });
        }
    } else {
        steps.push({
            title:  'Review',
            status: ['submitted'].includes(d.status) ? 'active' : 'pending',
        });
    }
 
    /* 4 ── Scoring Manager */
    if (d.scored_at) {
        steps.push({
            title:  'Penilaian',
            by:     d.scored_by_manager || '',
            time:   d.scored_at,
            note:   d.manager_note || '',
            status: 'done',
        });
    } else if (!['draft','submitted','rejected_spv','returned_spv'].includes(d.status)) {
        /* hanya tampil sebagai pending jika sudah melewati SPV */
        steps.push({
            title:  'Penilaian',
            status: 'pending',
        });
    }
 
    /* 5 ── Tutup / Acknowledge */
    if (d.closed_at) {
        steps.push({
            title:  'SS Disahkan',
            by:     d.acknowledge_by || '',
            time:   d.closed_at,
            status: 'done',
        });
    } else {
        steps.push({
            title:  'SS Ditutup',
            status: 'pending',
        });
    }
 
    /* ─── Render ─── */
    const iconDone = `
        <svg width="10" height="10" fill="none" stroke="#16a34a"
             stroke-width="2.5" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>`;
 
    const iconActive = `
        <div style="width:8px;height:8px;border-radius:50%;
                    background:#2563eb;box-shadow:0 0 0 2px #bfdbfe;">
        </div>`;
 
    el.innerHTML = steps.map((item, i) => {
        const isLast   = i === steps.length - 1;
        const isDone   = item.status === 'done';
        const isActive = item.status === 'active';
 
        /* ── dot / circle styling ── */
        const circleStyle = isDone
            ? 'background:#f0fdf4;border-color:#86efac;'
            : isActive
                ? 'background:#eff6ff;border-color:#93c5fd;'
                : 'background:#f8fafc;border-color:#e2e8f0;';
 
        const icon = isDone ? iconDone : isActive ? iconActive : '';
 
        /* ── connector line ── */
        const connector = !isLast
            ? `<div style="position:absolute;left:9px;top:22px;bottom:0;
                           width:1px;background:#e9eef4;"></div>`
            : '';
 
        /* ── text colours ── */
        const titleColor = isDone ? '#0f172a' : isActive ? '#1d4ed8' : '#94a3b8';
        const byColor    = '#64748b';
        const timeColor  = '#94a3b8';
 
        return `
        <div style="display:flex;gap:12px;padding-bottom:16px;position:relative;">
            ${connector}
 
            {{-- Dot --}}
            <div style="width:20px;height:20px;border-radius:50%;border:1.5px solid;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0;margin-top:2px;${circleStyle}">
                ${icon}
            </div>
 
            {{-- Content --}}
            <div style="flex:1;min-width:0;">
 
                <div style="font-size:12px;font-weight:700;color:${titleColor};
                            line-height:1.3;">
                    ${item.title}
                </div>
 
                ${item.by ? `
                <div style="font-size:11px;color:${byColor};margin-top:2px;">
                    oleh ${item.by}
                </div>` : ''}
 
                ${item.time ? `
                <div style="font-size:10px;color:${timeColor};margin-top:2px;">
                    ${item.time}
                </div>` : ''}
 
                ${(!item.by && !item.time && isActive) ? `
                <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                    Menunggu
                </div>` : ''}
 
                ${(!item.by && !item.time && !isActive && item.status === 'pending') ? `
                <div style="font-size:11px;color:#cbd5e1;margin-top:2px;">
                    Menunggu
                </div>` : ''}
 
                ${item.note ? `
                <div style="margin-top:8px;font-size:11px;color:#475569;
                            background:#f8fafc;border-radius:6px;
                            padding:8px 10px;
                            border-left:2px solid #e2e8f0;
                            line-height:1.6;white-space:pre-line;">
                    ${item.note}
                </div>` : ''}
 
            </div>
        </div>`;
    }).join('');
}

 
/* ════════════════════════════════
   TAB SWITCH (prefix "ss-tab-" agar tidak bentrok)
════════════════════════════════ */
function ssPanelTab(key, btn) {
    document.querySelectorAll('#ssPanel .ss-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('#ssPanel .ss-tab').forEach(b => {
        b.classList.remove('text-slate-900', 'border-slate-900');
        b.classList.add('text-slate-400', 'border-transparent');
    });
 
    const pane = document.getElementById('ss-tab-' + key);
    if (pane) pane.classList.remove('hidden');
 
    if (btn) {
        btn.classList.remove('text-slate-400', 'border-transparent');
        btn.classList.add('text-slate-900', 'border-slate-900');
    }
}
 
/* ════════════════════════════════
   ACCORDION (detail tab)
════════════════════════════════ */
function ssAccordion(btn) {
    const body = btn.nextElementSibling;
    const icon = btn.querySelector('.fa-chevron-down');
    body.classList.toggle('hidden');
    if (icon) icon.classList.toggle('rotate-180');
}
 
/* ════════════════════════════════
   FOOTER
════════════════════════════════ */
function ssRenderFooter(actions, id, ssNum, mode = 'view') {
 
    const info = document.getElementById('ssFooterInfo');
    const btns = document.getElementById('ssFooterBtns');
 
    btns.innerHTML = '';
 
    // ── Helper: navigasi ke halaman edit ──
    const goEdit = () => { window.location.href = `/suggestion/${id}/edit`; };
 
    // ── Tombol Tutup selalu ada ──
    btns.appendChild(ssMakeBtn('Tutup', 'slate', closeSlideOver, 'fa-solid fa-xmark'));
 
    /*
    |--------------------------------------------------------------------------
    | MODE SCORE (dibuka dari card dengan onclick openSlideOver(id, 'score'))
    |--------------------------------------------------------------------------
    */
    if (mode === 'score') {
        btns.innerHTML = '';
        btns.appendChild(ssMakeBtn('Submit Nilai', 'purple', () => openScoreModal(id, ssNum), 'fa-solid fa-star'));
        btns.appendChild(ssMakeBtn('Tutup', 'slate', closeSlideOver, 'fa-solid fa-xmark'));
        return;
    }
 
    /*
    |--------------------------------------------------------------------------
    | MODE REVISION (dibuka dari card dengan onclick openSlideOver(id, 'revision'))
    | Langsung tampil tombol Revisi → arahkan ke halaman edit
    |--------------------------------------------------------------------------
    */
    if (mode === 'revision') {
        btns.innerHTML = '';
        btns.appendChild(ssMakeBtn('Revisi', 'amber', goEdit, 'fa-solid fa-rotate-right'));
        btns.appendChild(ssMakeBtn('Tutup', 'slate', closeSlideOver, 'fa-solid fa-xmark'));
        return;   // ← RETURN LEBIH AWAL, tidak lanjut ke actions loop
    }

    if (mode === 'review') {
    btns.innerHTML = '';

    btns.appendChild(
        ssMakeBtn('Review', 'green', () => openSpvDecision(id, ssNum), 'fa-solid fa-check')
    );

    btns.appendChild(
        ssMakeBtn('Tutup', 'slate', closeSlideOver, 'fa-solid fa-xmark')
    );

    return;
}
 
    /*
    |--------------------------------------------------------------------------
    | ACTIONS dari API (d.actions)
    | Map setiap action string ke tombol yang sesuai
    |--------------------------------------------------------------------------
    */
    (actions || []).forEach(a => {
 
        const map = {
 
            // Owner: draft bisa edit & submit
            edit: () => btns.prepend(
                ssMakeBtn('Edit', 'orange', goEdit, 'fa-solid fa-pen')
            ),
 
            submit: () => btns.prepend(
                ssMakeBtn('Submit', 'blue', () => submitSS(id), 'fa-solid fa-paper-plane')
            ),
 
            // Owner: returned_spv → tombol Revisi, bukan submit
            revision: () => btns.prepend(
                ssMakeBtn('Revisi', 'amber', goEdit, 'fa-solid fa-rotate-right')
            ),
 
            // SPV actions
            approve: () => btns.prepend(
                ssMakeBtn('Setujui', 'green', () => openSpvDecision(id, ssNum), 'fa-solid fa-check')
            ),
            reject: () => btns.prepend(
                ssMakeBtn('Tolak', 'red', () => openSpvDecision(id, ssNum), 'fa-solid fa-xmark')
            ),
            return: () => btns.prepend(
                ssMakeBtn('Kembalikan', 'amber', () => openSpvDecision(id, ssNum), 'fa-solid fa-rotate-left')
            ),
 
            // Manager
            score: () => btns.prepend(
                ssMakeBtn('Beri Nilai', 'purple', () => openScoreModal(id, ssNum), 'fa-solid fa-star')
            ),
 
            // Improvement
            close: () => btns.prepend(
                ssMakeBtn('Acknowledge', 'teal', () => confirmClose(id), 'fa-solid fa-circle-check')
            ),
        };
 
        if (map[a]) map[a]();
    });
}
 
function ssMakeBtn(label, color, fn) {
    const colorMap = {
        blue:   'bg-blue-600 hover:bg-blue-700 text-white',
        green:  'bg-emerald-600 hover:bg-emerald-700 text-white',
        red:    'bg-red-600 hover:bg-red-700 text-white',
        amber:  'bg-amber-500 hover:bg-amber-600 text-white',
        orange:  'bg-orange-500 hover:bg-orange-600 text-white',
        purple: 'bg-violet-600 hover:bg-violet-700 text-white',
        navy:   'bg-[#0f172a] hover:bg-[#1e293b] text-white',
        teal:   'bg-[#0f172a] hover:bg-[#1e293b] text-white',
        slate:  'bg-white hover:bg-slate-50 text-slate-600 border border-slate-200',
    };
    const b = document.createElement('button');
    b.className = `px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-colors ${colorMap[color] || colorMap.slate}`;
    b.textContent = label;
    b.onclick = fn;
    return b;
}
 
/* ════════════════════════════════
   HELPERS
════════════════════════════════ */
function ssStatusLabel(s) {
    return { draft:'Draft', submitted:'Submitted', approved_spv:'Approved SPV',
             rejected_spv:'Rejected', returned_spv:'Returned',
             scored:'Scored', closed:'Closed' }[s] || s;
}
function ssStatusClass(s) {
    return { draft:'bg-slate-100 text-slate-600 border-slate-200',
             submitted:'bg-amber-50 text-amber-700 border-amber-200',
             approved_spv:'bg-green-50 text-green-700 border-green-200',
             rejected_spv:'bg-red-50 text-red-700 border-red-200',
             returned_spv:'bg-orange-50 text-orange-700 border-orange-200',
             scored:'bg-violet-50 text-violet-700 border-violet-200',
             closed:'bg-teal-50 text-teal-700 border-teal-200' }[s]
        || 'bg-slate-100 text-slate-600 border-slate-200';
}
function ssStatusProgress(s) {
    return { draft:10, submitted:35, approved_spv:55, rejected_spv:100,
             returned_spv:25, scored:80, closed:100 }[s] || 0;
}
 
/* ── Backward-compat alias (dipanggil dari card list HTML) ── */
function switchTab(tab, el) { ssPanelTab(tab, el); }

function btn(label, color, fn) {

    const map = {
        blue: 'bg-blue-600 hover:bg-blue-700',
        green: 'bg-emerald-600 hover:bg-emerald-700',
        red: 'bg-red-600 hover:bg-red-700',
        amber: 'bg-amber-500 hover:bg-amber-600',
        purple: 'bg-violet-600 hover:bg-violet-700',
        slate: 'bg-slate-600 hover:bg-slate-700'
    };

    const b = document.createElement('button');

    b.className = `px-3 py-1.5 text-xs text-white rounded-md ${map[color]}`;
    b.innerText = label;
    b.onclick = fn;

    return b;
}

function switchTab(tab, el) {

    document.querySelectorAll('.tab-content').forEach(e => e.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(e => e.classList.remove('active'));

    if (el) el.classList.add('active');
}

@if(session('success')) Toast.fire({ icon: 'success', title: @json(session('success')) }); @endif
@if(session('error'))   Toast.fire({ icon: 'error',   title: @json(session('error'))   }); @endif
@if($errors->any())     Toast.fire({ icon: 'error',   title: 'Terdapat kesalahan pada form.' }); @endif

// ── Sub-nav ──
function switchSubnav(tab) {
    document.querySelectorAll('.subnav-btn').forEach(b => {
        b.classList.remove('active','text-[#1e3a5f]','border-[#1e3a5f]','font-semibold');
        b.classList.add('text-gray-500','border-transparent');
    });
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    const btn = document.getElementById('subnav-' + tab);
    if (btn) {
        btn.classList.add('active','text-[#1e3a5f]','border-[#1e3a5f]','font-semibold');
        btn.classList.remove('text-gray-500','border-transparent');
    }
    document.getElementById('pane-' + tab)?.classList.add('active');
    if (tab === 'analytics') initCharts();
}
// Style active subnav on load
document.querySelectorAll('.subnav-btn.active').forEach(b => {
    b.classList.add('text-[#1e3a5f]','border-[#1e3a5f]','font-semibold');
    b.classList.remove('text-gray-500','border-transparent');
});

// ── Filter table ──
function filterTable(status) {
    document.querySelectorAll('#ss-table tbody tr').forEach(row => {
        row.style.display = (!status || row.dataset.status === status) ? '' : 'none';
    });
}
const urlFilter = new URLSearchParams(location.search).get('filter');
if (urlFilter) { document.getElementById('filter-status').value = urlFilter; filterTable(urlFilter); }

// ── Modal helpers ──
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden');    document.body.style.overflow=''; }
document.querySelectorAll('[id$="-modal"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});


function showToast(text, type = 'success') {
    const colors = {
        success: '#16a34a',
        error: '#dc2626',
        info: '#2563eb'
    };

    Toastify({
        text,
        duration: 3000,
        gravity: 'top',
        position: 'right',
        backgroundColor: colors[type] || colors.success
    }).showToast();
}


// ─────────────────────────────────────────────
// 🚀 ENTRY POINT (ROLE BASED FLOW)
// ─────────────────────────────────────────────
function openScoreFlow(id, ssNum, role) {

    // set common ke score form
    $('#score-form').attr('action', `/suggestion/${id}/score`);
    $('#score-ss-num').text(ssNum);

    if (role === 'supervisor') {
        openSpvDecision(id, ssNum);
    } else {
        openScoreModal(id, ssNum);
    }
}


// ─────────────────────────────────────────────
// 🧑‍💼 SPV DECISION MODAL
// ─────────────────────────────────────────────
function openSpvDecision(id, ssNum) {
    const form = $('#spv-form');

    form.attr('action', `/suggestion/${id}/spv-action`);
    form.data('id', id);
    form.data('ss', ssNum);

    $('#spv-ss-num').text(ssNum);

    // reset pilihan
    ['approve', 'return', 'reject'].forEach(action => {
        const tile = document.getElementById('dt-' + action);

        tile.classList.remove(
    'border-emerald-500','bg-emerald-50','text-emerald-700',
    'border-amber-500','bg-amber-50','text-amber-700',
    'border-red-500','bg-red-50','text-red-700'
);

        tile.classList.add('border-gray-200','text-gray-600');
        document.getElementById('r-' + action).checked = false;
    });

    $('#spv-note').val('');
    $('#note-req').addClass('hidden');

    openModal('spv-modal');
}

function selectDecision(action) {

    ['approve', 'return', 'reject'].forEach(a => {
        const tile = document.getElementById('dt-' + a);
        const radio = document.getElementById('r-' + a);

        // reset
        tile.classList.remove(
            'border-emerald-500','bg-emerald-50','text-emerald-700',
            'border-amber-500','bg-amber-50','text-amber-700',
            'border-red-500','bg-red-50','text-red-700'
        );

        tile.classList.add('border-gray-200','text-gray-600');
        radio.checked = false;
    });

    const tile = document.getElementById('dt-' + action);
    const radio = document.getElementById('r-' + action);

    // set selected
    radio.checked = true;

    if (action === 'approve') {
        tile.classList.add('border-emerald-500','bg-emerald-50','text-emerald-700');
    } else if (action === 'return') {
        tile.classList.add('border-amber-500','bg-amber-50','text-amber-700');
    } else if (action === 'reject') {
        tile.classList.add('border-red-500','bg-red-50','text-red-700');
    }

    // toggle note wajib
    if (action === 'approve') {
        $('#note-req').addClass('hidden');
    } else {
        $('#note-req').removeClass('hidden');
    }
}


// ─────────────────────────────────────────────
// ⚡ SUBMIT SPV (AJAX + BRANCHING)
// ─────────────────────────────────────────────
$('#spv-form').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const selectedAction = $('input[name="action"]:checked').val();
    const note = $('#spv-note').val().trim();

    // ❌ VALIDASI
    if (!selectedAction) {
        showToast('Pilih keputusan terlebih dahulu', 'error');
        return;
    }

    if (selectedAction !== 'approve' && !note) {
        showToast('Catatan wajib diisi untuk return / reject', 'error');
        return;
    }

    // 🔥 APPROVE → LANGSUNG KE SCORE (NO AJAX)
    if (selectedAction === 'approve') {
        closeModal('spv-modal');

        showToast('Lanjut ke penilaian', 'info');

        const id = form.data('id');
        const ssNum = form.data('ss');

        openScoreModal(id, ssNum);
        return;
    }

    // ─────────────────────────────
    // ✅ RETURN / REJECT → AJAX
    // ─────────────────────────────
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        beforeSend: function () {
            $('#spv-submit-btn')
                .prop('disabled', true)
                .text('Menyimpan...');
        },

        success: function (response) {
            closeModal('spv-modal');

            showToast(
                response.message || 'Review berhasil disimpan',
                'success'
            );

            loadTable();
        },

        error: function (xhr) {
            let message = 'Terjadi kesalahan';

            if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
            }

            showToast(message, 'error');
        },

        complete: function () {
            $('#spv-submit-btn')
                .prop('disabled', false)
                .text('Simpan Review');
        }
    });
});


// ─────────────────────────────────────────────
// 📊 SCORE MODAL
// ─────────────────────────────────────────────
function openScoreModal(id, ssNum) {
    const form = document.getElementById('score-form');

    form.action = `/suggestion/${id}/score`;
    document.getElementById('score-ss-num').textContent = ssNum;

    // reset input score
    document.querySelectorAll('.score-select').forEach(i => {
        i.value = '';
    });

    // reset note
    const note = form.querySelector('textarea[name="manager_note"]');
    if (note) note.value = '';

    updateScoreTotal();
    openModal('score-modal');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.score-select').forEach(input => {
        input.addEventListener('input', updateScoreTotal);
        input.addEventListener('change', updateScoreTotal);
    });
    updateScoreTotal();
});
function updateScoreTotal() {
    let total = 0;

    const inputs = document.querySelectorAll('.score-select');
    const count = inputs.length; // selalu sejumlah item

    inputs.forEach(input => {
        let v = parseFloat(input.value);

        // jika kosong / null / bukan angka → anggap 0
        if (isNaN(v)) {
            v = 0;
        }

        // batasi range
        if (v < 0) v = 0;
        if (v > 10) v = 10;

        // selalu set value agar ikut terkirim saat submit
        input.value = v;

        total += v;
    });

    // average selalu dibagi jumlah item
    const avg = count > 0 ? total / count : 0;

    const totalEl = document.getElementById('total-score');
    const avgEl   = document.getElementById('avg-score');
    const rewEl   = document.getElementById('estimated-reward');

    if (totalEl) {
        totalEl.textContent = total.toFixed(0);
    }

    if (avgEl) {
        avgEl.textContent = avg.toFixed(2);
    }

    let reward = 0;
    const tiersEl = document.getElementById('tiers-data');

    if (tiersEl) {
        try {
            const tiers = JSON.parse(tiersEl.textContent);

            tiers.forEach(t => {
                if (
                    !reward &&
                    avg >= parseFloat(t.min) &&
                    avg <= parseFloat(t.max)
                ) {
                    reward = parseFloat(t.reward) || 0;
                }
            });
        } catch (e) {}
    }

    if (rewEl) {
        rewEl.textContent = reward > 0
            ? 'Rp ' + reward.toLocaleString('id-ID')
            : '—';
    }
}

// ── Formula toggle ──
function toggleFormula(id) {
    const detail  = document.getElementById('formula-detail-' + id);
    const chevron = document.getElementById('chevron-' + id);
    if (!detail) return;
    detail.classList.toggle('open');
    chevron.classList.toggle('open');
}

// ── SweetAlert confirms ──
function confirmClose(id) {
    Swal.fire({
        title: 'Tutup SS ini?',
        text: 'Status akan berubah menjadi Selesai dan tidak bisa diubah kembali.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1e3a5f',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, tutup',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl text-sm' }
    }).then(r => { if (r.isConfirmed) document.getElementById('close-form-' + id)?.submit(); });
}
function confirmDeletePeriod(id) {
    Swal.fire({
        title: 'Hapus periode ini?',
        text: 'Data periode akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, hapus',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl text-sm' }
    }).then(r => { if (r.isConfirmed) document.getElementById('delete-period-' + id)?.submit(); });
}
function confirmActivateFormula(id, name) {
    Swal.fire({
        title: 'Aktifkan formula ini?',
        html: `<b>${name}</b> akan diaktifkan.<br>Formula yang sedang aktif akan otomatis dinonaktifkan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#166534',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, aktifkan',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl text-sm' }
    }).then(r => { if (r.isConfirmed) document.getElementById('act-form-' + id)?.submit(); });
}
function confirmDeactivateFormula(id, name) {
    Swal.fire({
        title: 'Nonaktifkan formula?',
        html: `<b>${name}</b> akan dinonaktifkan.<br>Penilaian baru tidak bisa dilakukan sampai ada formula aktif.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#92400e',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, nonaktifkan',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl text-sm' }
    }).then(r => { if (r.isConfirmed) document.getElementById('deact-form-' + id)?.submit(); });
}
function confirmDeleteFormula(id, name) {
    Swal.fire({
        title: 'Hapus formula ini?',
        html: `<b>${name}</b> akan dihapus permanen.<br>SS yang sudah dinilai tidak terpengaruh.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, hapus',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl text-sm' }
    }).then(r => { if (r.isConfirmed) document.getElementById('del-form-' + id)?.submit(); });
}

// ── Formula form builder ──
let itemCount = document.querySelectorAll('.item-block').length || 1;
let tierCount  = document.querySelectorAll('.tier-row').length  || 1;

function buildCriteriaRow(ii, ci) {
    return `<div class="criteria-row grid grid-cols-2 sm:grid-cols-[80px_70px_70px_1fr_32px] gap-2">
        <input type="text"   name="items[${ii}][criterias][${ci}][grade]"       class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Grade" required/>
        <input type="number" name="items[${ii}][criterias][${ci}][min_point]"   class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Min" min="1" required/>
        <input type="number" name="items[${ii}][criterias][${ci}][max_point]"   class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Max" min="1" required/>
        <input type="text"   name="items[${ii}][criterias][${ci}][description]" class="col-span-2 sm:col-span-1 text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Deskripsi" required/>
        <button type="button" onclick="removeCriteria(this)" class="w-8 h-8 flex items-center justify-center text-red-400 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 text-sm">✕</button>
    </div>`;
}
function addItemBlock() {
    const i = itemCount++;
    document.getElementById('item-container').insertAdjacentHTML('beforeend', `
    <div class="item-block border border-gray-200 rounded-xl p-4">
        <div class="flex gap-2 items-center mb-3">
            <input type="text" name="items[${i}][item_name]" class="flex-1 text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Nama Item" required/>
            <button type="button" onclick="removeItem(this)" class="w-8 h-8 flex items-center justify-center text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-bold shrink-0">✕</button>
        </div>
        <div class="hidden sm:grid grid-cols-[80px_70px_70px_1fr_32px] gap-2 mb-2 px-1">
            <span class="text-[10px] font-semibold text-gray-400">Grade</span>
            <span class="text-[10px] font-semibold text-gray-400">Min</span>
            <span class="text-[10px] font-semibold text-gray-400">Max</span>
            <span class="text-[10px] font-semibold text-gray-400">Deskripsi</span>
            <span></span>
        </div>
        <div class="criteria-container space-y-2">${buildCriteriaRow(i,0)}</div>
        <button type="button" onclick="addCriteriaRow(this)" class="mt-2 flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-medium text-gray-500 hover:text-gray-700 transition-colors">+ Tambah Kriteria</button>
    </div>`);
}
function addCriteriaRow(btn) {
    const block = btn.closest('.item-block');
    const cont  = block.querySelector('.criteria-container');
    const ii = [...document.querySelectorAll('.item-block')].indexOf(block);
    const ci = cont.querySelectorAll('.criteria-row').length;
    cont.insertAdjacentHTML('beforeend', buildCriteriaRow(ii, ci));
}
function removeCriteria(btn) {
    const cont = btn.closest('.criteria-container');
    if (cont.querySelectorAll('.criteria-row').length <= 1) {
        Toast.fire({ icon:'warning', title:'Minimal 1 kriteria.' }); return;
    }
    btn.closest('.criteria-row').remove();
}
function removeItem(btn) {
    if (document.querySelectorAll('.item-block').length <= 1) {
        Toast.fire({ icon:'warning', title:'Minimal 1 item penilaian.' }); return;
    }
    btn.closest('.item-block').remove();
}
function addTierRow() {
    const i = tierCount++;
    document.getElementById('tier-container').insertAdjacentHTML('beforeend', `
    <div class="tier-row grid grid-cols-2 sm:grid-cols-[1fr_1fr_1fr_32px] gap-2">
        <input type="number" name="tiers[${i}][min_score]"    class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Min Avg" required/>
        <input type="number" name="tiers[${i}][max_score]"    class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Max Avg" required/>
        <input type="number" name="tiers[${i}][reward_amount]" class="text-xs border border-gray-200 rounded-lg px-2.5 py-2 outline-none focus:border-[#1e3a5f]" placeholder="Reward (Rp)" required/>
        <button type="button" onclick="removeTier(this)" class="w-8 h-8 flex items-center justify-center text-red-400 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 text-sm">✕</button>
    </div>`);
}
function removeTier(btn) {
    if (document.querySelectorAll('.tier-row').length <= 1) {
        Toast.fire({ icon:'warning', title:'Minimal 1 tier reward.' }); return;
    }
    btn.closest('.tier-row').remove();
}

// ── Formula form AJAX ──
$(document).on('submit', '#formula-form', function(e) {
    e.preventDefault();
    const storeUrl = "{{ route('suggestion.formula.store') }}";
    const $btn = $(this).find('button[type="submit"]');
    const orig = $btn.html();
    $btn.prop('disabled', true).html('Menyimpan...');
    const fd = new FormData(this);
    if (!$(this).find('input[name="is_active"]').is(':checked')) fd.delete('is_active');
    $.ajax({
        url: storeUrl, type: 'POST', data: fd,
        processData: false, contentType: false, cache: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        success: res => {
            Toast.fire({ icon:'success', title: res.message || 'Formula berhasil disimpan' });
            $('#formula-form')[0].reset();
            setTimeout(() => location.reload(), 1500);
        },
        error: xhr => {
            let msg = 'Terjadi kesalahan';
            if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors)[0][0];
            else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
            Toast.fire({ icon:'error', title: msg });
        },
        complete: () => $btn.prop('disabled', false).html(orig)
    });
    return false;
});

// ── Charts ──
let chartsInited = false;
const CC = { navy:'#1e3a5f', teal:'#0d9488', amber:'#f59e0b', green:'#22c55e', purple:'#8b5cf6', red:'#ef4444', sky:'#38bdf8', indigo:'#6366f1' };
const analyticsData = @json($analyticsData ?? []);

function initCharts() {
    if (chartsInited) return; chartsInited = true;
    Chart.defaults.font.family = 'Inter'; Chart.defaults.font.size = 11; Chart.defaults.color = '#6b7280';

  const cats = analyticsData.category_counts ?? [];

new Chart(document.getElementById('chartCategory'), {

    type: 'doughnut',

    data: {

        labels: cats.map(c => c.cat),

        datasets: [{
            data: cats.map(c => Number(c.total)),

            backgroundColor: [
                '#1e3a5f',
                '#334e68',
                '#486581',
                '#627d98',
                '#829ab1',
                '#9fb3c8',
                '#bcccdc',
                '#d9e2ec',
            ],

            borderWidth: 0,
            hoverOffset: 6
        }]
    },

    options: {

        responsive: true,
        maintainAspectRatio: false,

        cutout: '64%',

       layout: {
    padding: {
        top: 40,
        right: 60,
        bottom: 40,
        left: 60
    }
},

        plugins: {

            legend: {
                display: false
            },

            tooltip: {
                enabled: false
            }
        }
    },

    plugins: [

        /*
        |--------------------------------------------------------------------------
        | CENTER TOTAL
        |--------------------------------------------------------------------------
        */
        {
            id: 'centerText',

            afterDraw(chart) {

                const { ctx } = chart;

                const total = cats.reduce(
                    (sum, item) => sum + Number(item.total),
                    0
                );

                ctx.save();

                ctx.textAlign = 'center';

                ctx.fillStyle = '#94a3b8';
                ctx.font = '500 10px Inter';

                ctx.fillText(
                    'TOTAL',
                    chart.width / 2,
                    chart.height / 2 - 4
                );

                ctx.fillStyle = '#0f172a';
                ctx.font = '700 22px Inter';

                ctx.fillText(
                    total,
                    chart.width / 2,
                    chart.height / 2 + 20
                );

                ctx.restore();
            }
        },

        /*
        |--------------------------------------------------------------------------
        | LABEL DI LUAR DONUT
        |--------------------------------------------------------------------------
        */
        {
            id: 'outsideLabels',

            afterDraw(chart) {

                const {
                    ctx
                } = chart;

                const meta = chart.getDatasetMeta(0);

                ctx.save();

                meta.data.forEach((arc, index) => {

                    const label = cats[index]?.cat ?? '-';
                    const value = cats[index]?.total ?? 0;

                    const angle = (arc.startAngle + arc.endAngle) / 2;

                   const x = arc.x + Math.cos(angle) * (arc.outerRadius + 14);
const y = arc.y + Math.sin(angle) * (arc.outerRadius + 14);

                    ctx.textAlign = x > arc.x ? 'left' : 'right';

                    /*
                    |--------------------------------------------------------------------------
                    | LABEL CATEGORY
                    |--------------------------------------------------------------------------
                    */

                    ctx.fillStyle = '#0f172a';
                    ctx.font = '600 7px Inter';

                    ctx.fillText(
                        label,
                        x,
                        y
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL
                    |--------------------------------------------------------------------------
                    */

                    ctx.fillStyle = '#64748b';
                    ctx.font = '600 10px Inter';

                    ctx.fillText(
                        value + ' SS',
                        x,
                        y + 14
                    );

                });

                ctx.restore();
            }
        }
    ]
});
   
 const rd = analyticsData.dept_stats ?? [];

new Chart(document.getElementById('chartReward'), {

    type: 'bar',

    data: {

        labels: rd.map(d => d.department),

        datasets: [

            // AVG SCORE (utama & objektif)
            {
                label: 'Average Score',

                data: rd.map(d => Number(d.avg_score)),

                backgroundColor: '#1e3a5f',

                borderRadius: 10,
                borderSkipped: false,

                maxBarThickness: 42,

                yAxisID: 'y',
            },

            // TOTAL REWARD (tetap ada)
            {
                label: 'Total Reward',

                data: rd.map(d => Number(d.total_reward)),

                backgroundColor: 'rgba(30, 58, 95, 0.18)',

                borderRadius: 10,
                borderSkipped: false,

                maxBarThickness: 42,

                yAxisID: 'y1',
            }
        ]
    },

    options: {

        responsive: true,
        maintainAspectRatio: false,

        interaction: {
            mode: 'index',
            intersect: false
        },

        plugins: {

            legend: {
                display: true,
                position: 'top',

                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 18,
                    color: '#475569',

                    font: {
                        size: 11,
                        weight: '600'
                    }
                }
            },

            tooltip: {

                enabled: true,

                callbacks: {

                    label(context) {

                        const d = rd[context.dataIndex];

                        if (context.dataset.label === 'Average Score') {
                            return `Avg Score : ${d.avg_score}`;
                        }

                        return `Reward : Rp ${Number(d.total_reward)
                            .toLocaleString('id-ID')}`;
                    },

                    afterBody(items) {

                        const d = rd[items[0].dataIndex];

                        return [
                            `Total SS : ${d.total_ss}`,
                            `Total Score : ${Number(d.total_score)
                                .toLocaleString('id-ID')}`,
                            `Avg Reward : Rp ${Number(d.avg_reward)
                                .toLocaleString('id-ID')}`
                        ];
                    }
                }
            }
        },

        scales: {

            x: {

                grid: {
                    display: false
                },

                ticks: {
                    color: '#475569',

                    font: {
                        size: 11,
                        weight: '600'
                    }
                }
            },

            // SCORE
            y: {

                position: 'left',

                beginAtZero: true,

                grid: {
                    color: '#f1f5f9'
                },

                ticks: {
                    color: '#475569'
                },

                title: {
                    display: true,
                    text: 'Average Score'
                }
            },

            // REWARD
            y1: {

                position: 'right',

                beginAtZero: true,

                grid: {
                    drawOnChartArea: false
                },

                ticks: {

                    callback(value) {

                        if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000) + 'jt';
                        }

                        if (value >= 1000) {
                            return 'Rp ' + (value / 1000) + 'k';
                        }

                        return value;
                    }
                },

                title: {
                    display: true,
                    text: 'Total Reward'
                }
            }
        }
    },

    plugins: [{

        id: 'valueLabel',

        afterDatasetsDraw(chart) {

            const { ctx } = chart;

            ctx.save();

            ctx.font = '600 11px Inter';
            ctx.fillStyle = '#1e3a5f';
            ctx.textAlign = 'center';

            // label hanya untuk avg score
            chart.getDatasetMeta(0).data.forEach((bar, index) => {

                const value = rd[index].avg_score;

                ctx.fillText(
                    Number(value).toFixed(1),
                    bar.x,
                    bar.y - 8
                );
            });

            ctx.restore();
        }
    }]
});
}

function toggleAccordion() {
  const body    = document.getElementById('accordion-body');
  const chevron = document.getElementById('chevron-icon');
  const isOpen  = body.style.maxHeight !== '0px';

  body.style.maxHeight = isOpen ? '0px'    : '2000px';
  body.style.opacity   = isOpen ? '0'      : '1';
  chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
}

$(function () {

    /*
    |--------------------------------------------------------------------------
    | DEBOUNCE (AUTO FILTER TANPA TOMBOL)
    |--------------------------------------------------------------------------
    */

    let filterTimer = null;

    function debounceLoad(page = 1) {
        clearTimeout(filterTimer);

        filterTimer = setTimeout(function () {
            loadTable(page);
        }, 500);
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD TABLE
    |--------------------------------------------------------------------------
    */

    window.loadTable = function (page = 1) {
        let perPage = $('#per-page').val();

        $('#table-body').html(`
            <tr>
                <td colspan="7" class="px-5 py-16 text-center text-sm text-slate-400">
                    Memuat data...
                </td>
            </tr>
        `);

        $('#table-info').text('Loading data...');
        $('#table-summary').text('Loading...');

        $.ajax({
            url: '{{ route("suggestion.dashboard") }}',
            type: 'GET',
            data: {
                load_table: 1,
                page: page,
                per_page: perPage,
                status: $('#filter-status').val(),
                ss_number: $('#filter-ss-number').val(),
                theme: $('#filter-theme').val(),
                department: $('#filter-department').val(),
                category: $('#filter-category').val()
            },

            success: function (response) {
                $('#table-body').html(response.html);

                $('#table-info').text(
                    `Showing ${response.from}–${response.to} of ${response.total} entries`
                );

                $('#table-summary').text(
                    `Page ${response.page} of ${response.last_page}`
                );

                renderPagination(
                    response.page,
                    response.last_page
                );
            },

            error: function (xhr) {
                console.log(xhr.responseText);

                $('#table-body').html(`
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center text-sm text-red-500">
                            Gagal memuat data.
                        </td>
                    </tr>
                `);

                $('#table-info').text('Failed to load');
                $('#table-summary').text('Please try again');
            }
        });
    };

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    function renderPagination(currentPage, lastPage) {
        let html = '';

        if (lastPage <= 1) {
            $('#pagination-container').html('');
            return;
        }

        /*
        |--------------------------------------------------------------
        | PREV BUTTON
        |--------------------------------------------------------------
        */

        html += `
            <button
                type="button"
                onclick="loadTable(${Math.max(currentPage - 1, 1)})"
                class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white hover:bg-slate-50"
                ${currentPage === 1 ? 'disabled' : ''}
            >
                ←
            </button>
        `;

        /*
        |--------------------------------------------------------------
        | PAGE NUMBERS
        |--------------------------------------------------------------
        */

        for (let i = 1; i <= lastPage; i++) {
            html += `
                <button
                    type="button"
                    onclick="loadTable(${i})"
                    class="
                        px-4 py-2 text-xs font-medium rounded-xl border transition
                        ${
                            i == currentPage
                                ? 'bg-[#1e3a5f] text-white border-[#1e3a5f]'
                                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                        }
                    "
                >
                    ${i}
                </button>
            `;
        }

        /*
        |--------------------------------------------------------------
        | NEXT BUTTON
        |--------------------------------------------------------------
        */

        html += `
            <button
                type="button"
                onclick="loadTable(${Math.min(currentPage + 1, lastPage)})"
                class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white hover:bg-slate-50"
                ${currentPage === lastPage ? 'disabled' : ''}
            >
                →
            </button>
        `;

        $('#pagination-container').html(html);
    }

    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    loadTable(1);

    /*
    |--------------------------------------------------------------------------
    | AUTO FILTER (INPUT TEXT)
    |--------------------------------------------------------------------------
    */

    $('#filter-ss-number, #filter-theme, #filter-department')
        .on('keyup', function () {
            debounceLoad(1);
        });

    /*
    |--------------------------------------------------------------------------
    | AUTO FILTER (SELECT)
    |--------------------------------------------------------------------------
    */

    $('#filter-status, #filter-category, #per-page')
        .on('change', function () {
            loadTable(1);
        });

    /*
    |--------------------------------------------------------------------------
    | RESET FILTER
    |--------------------------------------------------------------------------
    */

    $('#btn-reset-filter').on('click', function () {
        $('#filter-status').val('');
        $('#filter-ss-number').val('');
        $('#filter-theme').val('');
        $('#filter-department').val('');
        $('#filter-category').val('');
        $('#per-page').val('15');

        loadTable(1);
    });

});

(function() {
  const TARGET = new Date('{{ $end->toIso8601String() }}');
  const START  = new Date('{{ $start->toIso8601String() }}');
  const TOTAL  = TARGET - START;
  const pad    = n => String(n).padStart(2,'0');
  const prev   = {d:'',h:'',m:'',s:''};

  function flip(id, val) {
    if (prev[id] === val) return;
    prev[id] = val;
    const fw = document.getElementById('fw-'+id);
    const fd = document.getElementById('fd-'+id);
    const bd = document.getElementById('bd-'+id);
    bd.textContent = val;
    fw.classList.remove('flipping');
    void fw.offsetWidth;
    fw.classList.add('flipping');
    setTimeout(() => { fd.textContent = val; fw.classList.remove('flipping'); }, 400);
  }

  function tick() {
  const now  = new Date();
  const diff = TARGET - now;

  if (diff <= 0) {
    ['d','h','m','s'].forEach(k => flip(k,'00'));
    return;
  }

  flip('d', pad(Math.floor(diff / 86400000)));
  flip('h', pad(Math.floor((diff % 86400000) / 3600000)));
  flip('m', pad(Math.floor((diff % 3600000)  / 60000)));
  flip('s', pad(Math.floor((diff % 60000)    / 1000)));

  // ❗ OPTIONAL (biar tidak error kalau element tidak ada)
  const pctEl  = document.getElementById('prog-pct');
  const fillEl = document.getElementById('prog-fill');

  if (pctEl && fillEl) {
    const pct = Math.min(100, Math.round((now - START) / TOTAL * 100));
    pctEl.textContent  = pct + '%';
    fillEl.style.width = pct + '%';
  }
}

  tick();
  setInterval(tick, 1000);
})();


</script>
</body>
</html>