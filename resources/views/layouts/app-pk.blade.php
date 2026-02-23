<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" dir="ltr" data-pc-theme="light">
  <head>
    <title>@yield('title', 'Home') | Abimanyu Live System</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Abimanyu Live System for Internal Program" />
    <meta name="keywords" content="Bootstrap, dashboard, admin, template" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="CodedThemes" />

    <link rel="icon" href="{{ asset('img/asn-logo-bulat.png') }}" type="image/png" sizes="32x32" />
<link rel="apple-touch-icon" href="{{ asset('img/asn-logo-bulat.png') }}" />

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <!-- CSS (letakkan di head atau @push('styles')) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
    <!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/css/pluginsCss.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/plugins.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/css/luckysheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/js/plugin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/luckysheet.umd.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/feather.css') }}" />
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
  <body class="p-4">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
      <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
        <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
      </div>
    </div>
        <!-- [ Main Page Content ] -->
        <main>
          @yield('content')
        </main>
      </div>
    </div>
     @include('layouts.footer')

  </body>
</html>
