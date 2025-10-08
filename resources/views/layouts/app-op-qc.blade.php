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
  <body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
      <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
        <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
      </div>
    </div>

 <!-- [ Header Topbar ] start -->
<header class="px-8 py-6 rounded-3xl mt-6 mx-8">
  <!-- [ Header Topbar ] start -->
<div class="relative">
  <!-- Blue Background Layer -->
  <div class="bg-gray-400 px-8 pt-6 pb-20 rounded-xl shadow-md">
    <div class="flex justify-between items-center">
      <!-- Logo -->
      <div class="flex items-center space-x-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-28 w-auto object-contain" />
      </div>

      <!-- Logout -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
          class="flex items-center gap-2 bg-white text-red-800 font-semibold px-4 py-2 rounded-xl hover:bg-red-800 hover:text-white transition-all shadow">
          <i data-feather="log-out" class="h-4 w-4"></i>
          Log Out
        </button>
      </form>
    </div>
  </div>

  <!-- Breadcrumb Box Layer -->
  <div class=" absolute left-8 right-8 -bottom-6 z-10">
    <div class="bg-red-800 shadow-lg rounded-xl px-6 py-4 flex justify-between items-center">
      <!-- Title -->
      <div class="page-header-title">
        <h5 class="text-lg font-semibold text-white flex items-center gap-2">
  <i data-feather="search" class="w-5 h-5"></i>
  <span>@yield('page-title', 'Dashboard')</span>
</h5>

      </div>

      <!-- Breadcrumb -->
      <ul class="text-xs text-white flex items-center">
        <li class="flex items-center">
          <a href="{{ url('/') }}" class="text-white hover:underline">
            <i data-feather="home" class="w-4 h-4"></i>
          </a>
          <span class="mx-2 text-white">›</span>
        </li>
        <li class="flex items-center">
          <span>@yield('breadcrumb-item')</span>
          <span class="mx-2 text-white">›</span>
        </li>
        <li class="text-white font-medium">@yield('breadcrumb-active')</li>
      </ul>
    </div>
  </div>
</div>
<!-- [ Header Topbar ] end -->

        <!-- [ Main Page Content ] -->
        <main class="mt-12">
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
      
    // Cek session setiap 1 menit
    setInterval(() => {
        fetch('/check-session', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (res.status === 401) {
                // Session habis (unauthorized), redirect ke login
                alert("Session Anda telah habis. Anda akan dialihkan ke halaman login.");
                window.location.href = '/login';
            }
        });
    }, 60 * 1000); // Setiap 1 menit
</script>

@stack('scripts')


</body>
</html>