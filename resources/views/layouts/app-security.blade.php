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
    <!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/css/pluginsCss.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/plugins.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luckysheet/dist/css/luckysheet.css">
    <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/plugins/js/plugin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luckysheet/dist/luckysheet.umd.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/style.css') }}" id="main-style-link" />
   <script>
(async () => {
    if (!('serviceWorker' in navigator)) return;

    try {
        const registration = await navigator.serviceWorker.register('/sw.js');

        let subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return console.log('User menolak notifikasi');

            const publicVapidKey = '{{ env("VAPID_PUBLIC_KEY") }}';
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicVapidKey)
            });

            await fetch('/save-subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(subscription)
            });

            console.log('Berhasil subscribe dan disimpan di server');
        } else {
            console.log('Sudah pernah subscribe sebelumnya');
        }
    } catch (err) {
        console.error('Gagal subscribe:', err);
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
    }
})();
</script>


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
  <body class="bg-white">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
      <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
        <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
      </div>
    </div>

    <!-- [ Header ] -->
    @include('layouts.header-security')

    <!-- [ Content Wrapper ] -->
    <div class="">
      <div class="p-2">
          @yield('content')
      </div>
    </div>




    
<!-- Required Js -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 {{-- DataTables CSS & JS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
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
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>



@stack('scripts')


</body>
</html>