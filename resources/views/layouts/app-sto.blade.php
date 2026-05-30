<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" dir="ltr" data-pc-theme="light">
  <head>
    <title>@yield('title', 'Home') | Abimanyu Internal System</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Datta Able dashboard template using Bootstrap 5." />
    <meta name="keywords" content="Bootstrap, dashboard, admin, template" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="CodedThemes" />

   <link rel="icon" href="{{ asset('img/asn-logo-bulat.png') }}" type="image/png" sizes="32x32" />
<link rel="apple-touch-icon" href="{{ asset('img/asn-logo-bulat.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/style.css') }}" id="main-style-link" />

    @stack('styles')
  <style>
/* Styling badge/tag pilihan */
.select2-selection__choice {
    background-color: #2563eb !important; /* Biru (blue-600) */
    color: white !important;
    border: none !important;
    border-radius: 0.375rem !important; /* rounded-md */
    font-size: 0.875rem;
    position: relative;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #fff !important;
    font-weight: bold;
    font-size: 14px;
    background: transparent;
    border: none;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

/* Hover: saat mouse berada di atas tombol × */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #dc2626 !important; /* Tailwind red-600 */
    background: none !important; /* hilangkan latar belakang */
}
</style>




  </head>
  <body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
      <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
        <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
      </div>
    </div>
   <!-- MOBILE FULLSCREEN LOADER 
<div id="mobile-loader"
     class="fixed inset-0 z-[9999] bg-blue-600 flex flex-col items-center justify-center opacity-100 transition-opacity duration-700 sm:hidden">

    

    
    <div class="animate-bounce mb-6">
      🧐📝📦🔍🎯
    </div>

   
    <p class="text-white text-xl tracking-wide font-semibold animate-pulse">
      Memuat data...
    </p>
</div>-->

     @include('layouts.sidebar')
      <!-- [ Header Topbar ] start -->
<header class="pc-header bg-transparent">
  <div class="header-wrapper flex items-center h-14 max-sm:px-[15px] px-[25px] grow
              bg-transparent lg:bg-transparent">

    <!-- [Mobile Burger] tetap tampil di mobile -->
    <div class="me-auto pc-mob-drp">
      <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse max-lg:hidden lg:inline-flex">
          <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="sidebar-hide">
            <i data-feather="menu"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup lg:hidden">
          <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="mobile-collapse">
            <i data-feather="menu" class="text-white lg:text-gray-600"></i>
          </a>
        </li>
      </ul>
    </div>

    <!-- Logo di tengah hanya untuk mobile/tablet -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 lg:hidden flex items-center">
      <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-8">
    </div>

    <!-- Right menu -->
    <div class="ms-auto">
      <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            <i data-feather="sun" class="text-white lg:text-gray-600"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
              <i data-feather="moon"></i>
              <span>Dark</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
              <i data-feather="sun"></i>
              <span>Light</span>
            </a>
            <a href="#!" class="dropdown-item" onclick="layout_change_default()">
              <i data-feather="settings"></i>
              <span>Default</span>
            </a>
          </div>
        </li>
    <!--<li class="dropdown pc-h-item">
      <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false">
        <i data-feather="settings"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
        <a href="#!" class="dropdown-item">
          <i class="ti ti-user"></i>
          <span>My Account</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-settings"></i>
          <span>Settings</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-headset"></i>
          <span>Support</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-lock"></i>
          <span>Lock Screen</span>
        </a>
        <a href="#!" class="dropdown-item">
          <i class="ti ti-power"></i>
          <span>Logout</span>
        </a>
      </div>
    </li>
   @php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$canApprove = $user->roles()->whereIn('name', ['Supervisor Special Access', 'Manager Special Access'])->exists() &&
              $user->departments()->where('name', 'Information & Technology')->exists();

$ticketsToApprove = $canApprove
    ? \App\Models\Ticket::where('status', 'Pending')->latest()->take(5)->get()
    : collect(); // kosong jika tidak berhak
@endphp

@if ($canApprove)
<li class="dropdown pc-h-item relative">
  <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button">
    <i data-feather="bell"></i>
    @if ($ticketsToApprove->count())
      <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">{{ $ticketsToApprove->count() }}</span>
    @endif
  </a>
  <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown p-2 w-96">
    <div class="dropdown-header flex items-center justify-between py-4 px-5">
      <h5 class="m-0">Tickets to Approve</h5>
      <a href="{{ route('it.ticket.index') }}" class="btn btn-link btn-sm">View All</a>
    </div>
    <div class="dropdown-body header-notification-scroll relative py-2 px-3" style="max-height: 400px; overflow-y: auto;">
      @forelse ($ticketsToApprove as $ticket)
      <div class="card mb-2 shadow-sm border border-gray-200">
        <div class="card-body px-3 py-2">
          <div class="text-sm font-semibold text-gray-700 mb-1">
            {{ $ticket->ticket_number }} - {{ $ticket->title }}
          </div>
          <p class="text-xs text-gray-600 mb-2">{{ $ticket->category }}</p>
          <div class="flex justify-end gap-2">
            <a href=""
              class="btn btn-xs btn-outline-primary">Detail</a>
            <button onclick="approveTicket({{ $ticket->id }})" class="btn btn-xs btn-success">Approve</button>
            <button onclick="rejectTicket({{ $ticket->id }})" class="btn btn-xs btn-danger">Reject</button>
          </div>
        </div>
      </div>
      @empty
      <p class="text-sm text-gray-500 text-center">No pending tickets.</p>
      @endforelse
    </div>
    <div class="text-center py-2">
      <a href="{{ route('it.ticket.index') }}" class="text-primary hover:underline">Lihat semua ticket</a>
    </div>
  </div>
</li>
@endif-->


   <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false">
            <i data-feather="user" class="text-white lg:text-gray-600"></i>
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-2 overflow-hidden">
            <div class="dropdown-header flex items-center justify-between py-4 px-5 bg-teal-500">
              <div class="flex mb-1 items-center">
                <div class="shrink-0">
                 <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('img/avatar-dummy.png') }}"
         alt="user-image" class="w-10 h-10 bg-white rounded-full object-cover" />
                </div>
                <div class="grow ms-3">
                  <h6 class="mb-1 text-white font-bold">{{ Auth::user()->name }}</h6>
                  <span class="text-white"> {{ Auth::user()->departments->first()->name ?? '-' }}</span>
                </div>
              </div>
            </div>
            <div class="dropdown-body py-4 px-5">
              <a href="{{ route('setting.user.edit', $user->id) }}" class="dropdown-item">
                <span>
                    <svg class="pc-icon text-muted me-2 inline-block">
                        <use xlink:href="#custom-lock-outline"></use>
                    </svg>
                    <span>Change Password</span>
                </span>
              </a>
              <div class="grid my-3">
               <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="btn bg-teal-500 hover:text-red-500 text-white flex items-center justify-center w-full">
                    <svg class="pc-icon me-2 w-[22px] h-[22px]">
                      <use xlink:href="#custom-logout-1-outline"></use>
                    </svg>
                    Logout
                  </button>
               </form>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>

@php
  $isChemCons = in_array($warehouse, ['Chemical', 'Consumable']);
@endphp

<div class="relative block sm:hidden h-[46vh] p-5 overflow-hidden bg-gradient-to-tr from-sky-600 via-blue-700 to-blue-900 rounded-b-3xl shadow-xl">

 
  <div class="absolute inset-0 bg-gradient-to-b from-blue-950/70 via-blue-900/60 to-blue-800/70 backdrop-blur-sm"></div>

  <div class="absolute inset-0 flex flex-col items-center justify-center text-center z-10 px-6">

    <h1 class="text-xl font-bold text-white drop-shadow-lg tracking-wide">
      Halo,
      <span class="font-semibold underline decoration-white/60 text-yellow-500">{{ Auth::user()->name }}</span> 👋
    </h1>

   <p class="text-sm mt-2 text-white/90 leading-relaxed">
  @if(in_array(auth()->id(), [53, 2]))
      Silahkan pilih nomor STO dan Lokasi untuk memulai proses stock opname.
  @else
      Silahkan pilih nomor STO untuk memulai proses stock opname.
  @endif
</p>


   <div class="w-full mt-5 p-4 bg-white/10 backdrop-blur-md rounded-xl shadow-lg border border-white/20 space-y-3">

 {{-- SESUDAH --}}
  @if($isChemCons)
    {{-- Area --}}
    <select
      id="area_mobile"
      class="w-full px-4 py-2 rounded-lg bg-white/90 text-gray-800 shadow-sm border border-transparent
             focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-sky-400 transition-all duration-200"
      data-warehouse="{{ $warehouse }}">
      <option value="">— Memuat area... —</option>
    </select>

    {{-- Shelf --}}
    <select
      id="shelf_mobile"
      class="w-full px-4 py-2 rounded-lg bg-white/90 text-gray-800 shadow-sm border border-transparent
             focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-sky-400 transition-all duration-200"
      disabled>
      <option value="">— Pilih area dulu —</option>
    </select>

    <input type="hidden" name="area"       id="area_value_mobile">
    <input type="hidden" name="shelf"      id="shelf_value_mobile">
    <input type="hidden" name="sto_number" id="sto_number_mobile">
    <input type="hidden" id="ref_master_id_mobile" value="">

  @else
    <select
      name="sto_number"
      id="sto_number_mobile"
      required
      class="w-full px-4 py-2 rounded-lg bg-white/90 text-gray-800 shadow-sm border border-transparent
             focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-sky-400 transition-all duration-200"
    >
   
        @php
  $year  = 2026;
  $month = '05';

  // Mapping lokasi → range
 $stoRange = [
    'Dead Stock CM1' => [1, 49],
    'Chemical'       => [1000, 1999],
    'Consumable'     => [2000, 2999],
    'Raw Material'   => [3000, 3999],
    'WIP Buffing'    => [5000, 5999],
    'WIP Sanding'    => [6000, 6999],
    'WIP Touch Up'   => [7000, 7999],
    'Finish Goods'   => [4000, 4999],
    'OT'             => [50, 999],
    'Werate'         => [8000, 8999],
  ];

  $ranges = [];

  // 🔒 User terkunci / mapped
  if (is_array($allowedWarehouses)) {
    foreach ($allowedWarehouses as $wh) {
      if (isset($stoRange[$wh])) {
        $ranges[] = $stoRange[$wh];
      }
    }
  }

  // 🔥 User bebas → semua range
  if (empty($ranges)) {
    $ranges = array_values($stoRange);
  }
@endphp

    @foreach ($ranges as [$start, $end])
  @for ($i = $start; $i <= $end; $i++)
    @php
      $number = str_pad($i, 4, '0', STR_PAD_LEFT);
      $val = "{$year}/{$month}/{$number}";
    @endphp

    @if (!in_array($val, $usedStoNumbers))
      <option value="{{ $val }}">{{ $val }}</option>
    @endif
  @endfor
@endforeach
  </select>
 @endif

  <!-- Warehouse Selector untuk user 67 & 53 -->
  @if(in_array(auth()->id(), [2, 53, 92]))

    @if($warehouse === null)
      <select
        name="warehouse"
        id="warehouse-null" 
        class="w-full px-4 py-2 mt-1 rounded-lg bg-white/90 text-gray-800 shadow-sm border border-transparent
               focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-sky-400 transition-all duration-200"
      >
        <option value="">-- Pilih Gudang --</option>

        @foreach($allowedWarehouses as $wh)
          <option value="{{ $wh }}">{{ $wh }}</option>
        @endforeach
      </select>

    @else
      <input
        type="text"
        class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-100 text-gray-700 border border-gray-200 shadow-sm"
        value="{{ $warehouse }}"
        readonly
      >
    @endif

  @endif

</div>


  </div>

</div>


 <!-- [ Content Wrapper Desktop ] -->


        <!-- [ Main Page Content ] -->
       <main class="p-4">
        
          @yield('content')
        </main>
    </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('template/dist/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/icon/custom-icon.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/component.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/theme.js') }}"></script>
<script src="{{ asset('template/dist/assets/js/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/leader-line"></script> <!-- untuk garis -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script>
  
    
</script>

@stack('scripts')


</body>
</html>