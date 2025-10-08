<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" dir="ltr" data-pc-theme="light">
  <head>
    <title>@yield('title', 'Home') | Abimanyu Internal System</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Datta Able dashboard template using Bootstrap 5." />
    <meta name="keywords" content="Bootstrap, dashboard, admin, template" />
    <meta name="author" content="CodedThemes" />

    <link rel="icon" href="{{ asset('template/dist/assets/images/favicon.svg') }}" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>
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
  <body >
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
      <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
        <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
      </div>
    </div>

 <!-- [ Header Topbar ] start -->
<header class="bg-gray-20 rounded-3xl">

  <!-- Breadcrumb Box Layer -->
  <div>
    <div class="px-6 py-4 flex justify-between items-center">
      <!-- Title -->
      <div class="page-header-title gap-3 flex justify-between">
       <div class="flex items-center gap-3 p-2 bg-white rounded-full shadow">
  <!-- Icon -->
  <div class="bg-gray-100 p-2 rounded-full flex items-center justify-center">
    <i data-feather="user" class="h-5 w-5 text-blue-500"></i>
  </div>

  <!-- Info -->
  <div class="flex flex-col leading-tight">
    <h6 class="text-gray-800 font-bold">{{ Auth::user()->name }}</h6>
    <span class="text-gray-600 text-sm">
      Cashier
    </span>
  </div>
   <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
          class="flex items-center gap-2 bg-gray-100 text-red-800 font-semibold p-2 rounded-full hover:bg-red-800 hover:text-white transition-all shadow">
          <i data-feather="log-out" class="h-5 w-5"></i>
        </button>
      </form>
</div>

 <div class="flex items-center gap-3 p-2 bg-white rounded-full shadow">
<!-- Icon -->
  <div class="bg-gray-100 p-2 rounded-full flex items-center justify-center">
    <i data-feather="dollar-sign" class="h-5 w-5 text-blue-500"></i>
  </div>

  <!-- Info -->
  <div class="flex flex-col leading-tight pr-4">
    <h6 class="text-gray-800 font-bold"> Your Income Today</h6>
    <span class="text-green-600 text-sm"> Rp.125.000.000,-
    </span> 
  </div>

  <div class="w-px h-10 bg-gray-300"></div>
 
  <!-- Icon -->
  <div class="bg-gray-100 p-2 rounded-full flex items-center justify-center">
    <i data-feather="calendar" class="h-5 w-5 text-blue-500"></i>
  </div>

  <!-- Info -->
  <div class="flex flex-col leading-tight pr-4">
    <h6 id="currentDate" class="text-gray-800 font-bold"></h6>
    <span id="currentTime" class="text-gray-600 text-sm">
    </span> 
  </div>
</div>





      </div>

      <!-- Breadcrumb -->
     <div class="flex items-center gap-3 p-2 bg-red-400 rounded-full shadow hover:bg-red-800">
  <!-- Icon -->
  <div class="bg-gray-100 p-2 rounded-full flex items-center justify-center">
    <i data-feather="power" class="h-5 w-5 text-red-800"></i>
  </div>

  <!-- Info -->
  <div class="flex items-center justify-start pr-3">
     <h6 class="text-white font-bold">Close Order</h6>
  </div>
</div>
    </div>
     <!-- Logout -->
     
  </div>
</div>
<!-- [ Header Topbar ] end -->
</header>

        <!-- [ Main Page Content ] -->
        <main class="px-6">
          @yield('content')
        </main>
      </div>
    </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 {{-- DataTables CSS & JS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
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
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
      function updateDateTime() {
  const now = new Date();
  const h = now.getHours().toString().padStart(2, '0');
  const m = now.getMinutes().toString().padStart(2, '0');
  const s = now.getSeconds().toString().padStart(2, '0');

  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

  // Update tanggal
  $('#currentDate').text(now.toLocaleDateString('id-ID', options));

  // Update jam real-time dengan detik
  $('#currentTime').text(`${h}:${m}:${s} WIB`);

}

// panggil pertama kali
updateDateTime();

// update setiap 1 detik
setInterval(updateDateTime, 1000);


    </script>
@stack('scripts')


</body>
</html>