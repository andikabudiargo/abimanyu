@extends('layouts.app')

@section('title', 'Chemical Traceability')
@section('page-title', 'Chemical Traceability')
@section('breadcrumb-item', 'Supporting Tools')
@section('breadcrumb-active', 'Chemical Traceability')

@section('content')

<div id="toast-container"
     class="fixed bottom-6 right-6 space-y-3 z-50"></div>


<div class="space-y-6">

  <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700
            shadow-lg rounded-2xl p-6 flex items-center gap-4">

    <!-- ICON -->
    <div class="bg-white/20 backdrop-blur rounded-xl w-12 h-12
                flex items-center justify-center">

        <i class="fa-solid fa-flask text-white text-xl"></i>

    </div>

    <!-- TEXT -->
    <div>
        <h2 class="text-2xl font-semibold text-white tracking-wide">
            Bill of Material Traceability
        </h2>

        <p class="text-indigo-100 text-sm">
            Track Bill of Material usage instantly
        </p>
    </div>

</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-6">
        <div class="bg-indigo-50 text-indigo-600 w-10 h-10
                    flex items-center justify-center rounded-xl">
            <i class="fa-solid fa-file-arrow-up text-lg"></i>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Import BOM Excel
            </h2>
            <p class="text-xs text-gray-500">
                Upload file to generate chemical traceability data
            </p>
        </div>
    </div>

    <!-- FORM -->
    <form id="excel-upload-form" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- DROPZONE -->
        <label for="excel-file"
            class="group cursor-pointer block border-2 border-dashed
                   border-gray-300 rounded-2xl p-10 text-center
                   hover:border-indigo-500 hover:bg-indigo-50/40
                   transition">

            <div class="flex flex-col items-center gap-3">

                <i class="fa-solid fa-cloud-arrow-up
                          text-4xl text-gray-400
                          group-hover:text-indigo-600 transition"></i>

                <p class="text-sm font-medium text-gray-700">
                    Drag & drop Excel here or
                    <span class="text-indigo-600 font-semibold">
                        browse file
                    </span>
                </p>

                <p class="text-xs text-gray-500">
                    Supported format: <b>.xlsx</b> • Max size: <b>5 MB</b>
                </p>

                <p class="text-xs text-gray-400">
                    Need template?
                     <a href="{{ asset('sample/bom_template.xlsx') }}"
                       class="text-indigo-600 hover:underline font-medium">
                        Download here
                    </a>
                </p>

            </div>

            <input type="file"
                name="file"
                id="excel-file"
                class="hidden"
                accept=".xlsx">
        </label>

        <!-- FILE NAME PREVIEW -->
        <div id="file-name"
            class="text-sm text-gray-600 mt-4 hidden">
        </div>

        <!-- PROGRESS BAR -->
        <div id="upload-progress-wrapper"
            class="hidden mt-5">

            <div class="flex justify-between text-xs mb-1">
                <span class="text-gray-600">Uploading...</span>
                <span id="progress-percent">0%</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="upload-progress"
                    class="bg-indigo-600 h-2 rounded-full transition-all"
                    style="width:0%">
                </div>
            </div>
        </div>

        <!-- PROCESS STATUS -->
<div id="process-status" class="hidden mt-6 space-y-3">

    <!-- shimmer rows -->
    <div class="shimmer h-4 rounded"></div>
    <div class="shimmer h-4 rounded"></div>
    <div class="shimmer h-4 rounded w-2/3"></div>

</div>

<!-- RESULT PREVIEW -->
        <!-- BUTTON -->
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="inline-flex items-center gap-2
                       bg-indigo-600 text-white px-6 py-2.5
                       rounded-xl hover:bg-indigo-700 transition">

                <i class="fa-solid fa-upload"></i>
                Import Data
            </button>
        </div>

    </form>

</div>






    <!-- MAIN MODULE -->
<div class="bg-white
            border border-gray-200
            rounded-3xl
            shadow-sm
            p-8 space-y-8">


    <!-- ===================== WORKSPACE ===================== -->
<div class="max-w-7xl mx-auto space-y-8">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>

            <h1 class="text-2xl font-semibold text-gray-900">
                Discover Finish Goods Connected
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Analyze chemical and raw material usage across Finish Goods production.
            </p>
        </div>

        <!-- MODE SWITCH -->
        <div id="traceToggle"
            class="bg-gray-100 p-1 rounded-full flex text-sm font-medium">

            <button data-target="chemical"
                class="toggle-btn active px-6 h-10 rounded-full transition">
                Chemical
            </button>

            <button data-target="raw"
                class="toggle-btn px-6 h-10 rounded-full transition">
                Raw Material
            </button>

        </div>
    </div>


    <!-- ===================== MAIN MODULE ===================== -->
    <div class="bg-white
                ">

        <!-- ================= CHEMICAL PANEL ================= -->
        <div id="panel-chemical" class="trace-panel space-y-8">

            <!-- CONTROL PANEL -->
            <div class="grid md:grid-cols-2 gap-6
                        bg-gradient-to-br from-gray-50 to-white
                        border border-gray-200
                        rounded-2xl p-6">

                <!-- SELECT CHEMICAL -->
                <div class="space-y-2">

                    <label class="text-sm font-medium text-gray-700">
                        Chemical Selector
                    </label>

                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <select id="cmSelect"
                            class="w-full h-12 pl-10 pr-4
                                   border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-indigo-500
                                   focus:border-indigo-500 transition">

                            <option value="">
                                Search chemical...
                            </option>
                        </select>
                    </div>

                    <p class="text-xs text-gray-400">
                        Data refreshes automatically after selection.
                    </p>

                </div>

                <!-- INFO PANEL -->
                <!-- EXPORT BUTTON -->
                <div class="text-right">
                    <button id="export-button"
                        class="inline-flex items-center gap-2
                               bg-green-600 text-white
                               px-6 h-11 rounded-xl font-medium
                               transition-all duration-200
                               hover:bg-green-700
                               hover:shadow-lg hover:-translate-y-[1px]
                               active:scale-[0.97]">

                        <i class="fas fa-file-export"></i>
                        Export CM1
                    </button>

                    <p class="text-xs text-gray-400 mt-1">
                        Generate relationship data
                    </p>
                </div>

            </div>
        </div>


      
        <!-- ================= RAW MATERIAL PANEL ================= -->
<div id="panel-raw" class="trace-panel hidden space-y-8">


    <!-- CONTROL PANEL -->
    <div class="grid md:grid-cols-2 gap-6
                bg-gradient-to-br from-gray-50 to-white
                border border-gray-200
                rounded-2xl p-6">

        <!-- SELECT RAW MATERIAL -->
        <div class="space-y-2">

            <label class="text-sm font-medium text-gray-700">
                Raw Material Selector
            </label>

            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                <select id="rmSelect"
                    class="w-full h-12 pl-10 pr-4
                           border border-gray-300 rounded-xl
                           focus:ring-2 focus:ring-indigo-500
                           focus:border-indigo-500 transition">

                    <option value="">
                        Search raw material...
                    </option>
                </select>
            </div>

            <p class="text-xs text-gray-400">
                Data refreshes automatically after selection.
            </p>

        </div>

        <!-- INFO PANEL -->
       <div class="text-right">
            <button id="export-button-rm"
                class="inline-flex items-center gap-2
                       bg-indigo-600 text-white
                       px-6 h-11 rounded-xl font-medium
                       transition-all duration-200
                       hover:bg-indigo-700
                       hover:shadow-lg hover:-translate-y-[1px]
                       active:scale-[0.97]">

                <i class="fas fa-file-export"></i>
                Export RM
            </button>

            <p class="text-xs text-gray-400 mt-1">
                Generate relationship data
            </p>
        </div>

    </div>

</div>


    <!-- ===================== RESULT TABLE ===================== -->
    <div id="fg_table" class="space-y-4 mt-8">

        <div class="flex items-center justify-between">

            <h3 class="text-lg font-semibold text-gray-900">
                Finish Good List
            </h3>

            <span class="text-xs text-gray-400">
                Live Trace Result
            </span>

        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden
                    shadow-md">

            <table id="fg_table_inner" class="min-w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">FG Code</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-left">FG Name</th>
                    </tr>
                </thead>

                <tbody id="fg_table_body" class="divide-y divide-gray-100 bg-white">

<tr id="noDataRow">
    <td colspan="3" class="py-16 text-center">

        <div class="flex flex-col items-center justify-center text-gray-400 space-y-2">

            <i class="fas fa-database text-3xl opacity-40"></i>

            <p class="text-sm font-medium">
                No data available
            </p>

            <p class="text-xs text-gray-400">
                Select a chemical or raw material to display results
            </p>

        </div>

    </td>
</tr>

</tbody>

            </table>

        </div>

    </div>

</div>


</div>
</div>
<style>
    .toggle-btn{
    color:#6b7280;
}

.toggle-btn.active{
    background:white;
    color:#111827;
    box-shadow:0 1px 3px rgba(0,0,0,.12);
}
      /* Zebra stripe */
    #fg_table_inner tbody tr:nth-child(odd) {
        @apply bg-gray-50;
    }
    #fg_table_inner tbody tr:nth-child(even) {
        @apply bg-white;
    }

    /* Hover effect */
    #fg_table_inner tbody tr:hover {
        @apply bg-indigo-100;
    }

    /* shimmer animation */
.shimmer {
    position: relative;
    overflow: hidden;
    background: #f3f4f6;
}

.shimmer::after {
    content: "";
    position: absolute;
    top: 0;
    left: -150%;
    height: 100%;
    width: 150%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,.6),
        transparent
    );
    animation: shimmer 1.3s infinite;
}

@keyframes shimmer {
    100% { left: 150%; }
}

/* toast animation */
.toast-show {
    animation: slideIn .35s ease;
}

@keyframes slideIn {
    from { transform: translateY(20px); opacity:0 }
    to { transform: translateY(0); opacity:1 }
}

/* success check animation */
.checkmark {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawCheck 0.6s forwards ease;
}

@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}

.trace-panel{
    transition: opacity .25s ease, transform .25s ease;
}

.trace-panel.hidden{
    opacity:0;
    transform:translateY(6px);
    pointer-events:none;
}
</style>

@push('scripts')
<script>

    function showToast(message, type='success') {

    let color = type === 'error'
        ? 'bg-red-500'
        : 'bg-emerald-500';

    const toast = $(`
        <div class="${color} text-white px-5 py-3 rounded-xl shadow-lg toast-show flex items-center gap-3">
            <i class="fa-solid ${type==='error' ? 'fa-circle-xmark':'fa-circle-check'}"></i>
            <span>${message}</span>
        </div>
    `);

    $('#toast-container').append(toast);

    setTimeout(()=> {
        toast.fadeOut(300, ()=> toast.remove());
    }, 3500);
}

$(document).ready(function() {

 const tbody = $('#fg_table_body');

    // ===== EMPTY STATE TEMPLATE =====
    const noDataTemplate = `
        <tr>
            <td colspan="3" class="py-16 text-center">
                <div class="flex flex-col items-center text-gray-400 space-y-2">
                    <i class="fas fa-database text-3xl opacity-40"></i>
                    <p class="text-sm font-medium">No data available</p>
                    <p class="text-xs">
                        Select a chemical or raw material to display results
                    </p>
                </div>
            </td>
        </tr>
    `;

    function showNoData() {
        tbody.html(noDataTemplate);
    }

    // tampilkan default saat halaman load
    showNoData();

 $('.toggle-btn').on('click', function () {

        const target = $(this).data('target');

        // active button style
        $('.toggle-btn').removeClass('active');
        $(this).addClass('active');

        // show panel
        $('.trace-panel').addClass('hidden');
        $('#panel-' + target).removeClass('hidden');

    });


// tampilkan nama file
$('#excel-file').on('change', function () {
    const fileName = this.files[0]?.name;
    if(fileName){
        $('#file-name')
            .removeClass('hidden')
            .html('<i class="fa-solid fa-file-excel text-green-600 mr-2"></i>' + fileName);
    }
});


$('#excel-upload-form').on('submit', function(e){
    e.preventDefault();

    let formData = new FormData(this);

    $('#upload-progress-wrapper').removeClass('hidden');
    $('#process-status').removeClass('hidden');
    $('#import-result').addClass('hidden');

    $.ajax({
        xhr: function () {
            let xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    let percent = Math.round((evt.loaded / evt.total) * 100);
                    $('#upload-progress').css('width', percent + '%');
                    $('#progress-percent').text(percent + '%');
                }
            });

            return xhr;
        },
        url: "/fa/excel/import", // route import
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(res){

            $('#process-status').addClass('hidden');

            renderImportResult(res);

            showToast("Import completed successfully");
        },

        error: function(){
            showToast("Import failed", "error");
        }
    });
});

  // Inisialisasi Select2
    $('#cmSelect').select2({
        placeholder: "-- Choose Chemical --",
        allowClear:true,
        width: '100%'
    });

    // load data dari cache
    loadCM();

    function loadCM() {
        $.ajax({
            url: '/fa/excel/cm', // endpoint controller mengembalikan {code, name}
            type: 'GET',
            success: function(data) {
                var cmSelect = $('#cmSelect');
                cmSelect.empty(); // kosongkan dulu
                cmSelect.append('<option></option>'); // placeholder untuk Select2

                var seen = {};

                $.each(data, function(index, item){
                    if(item.code && item.name){
                        var key = item.code + '|' + item.name;
                        if(!seen[key]){
                            seen[key] = true;
                            var newOption = new Option(item.code + ' - ' + item.name, item.code, false, false);
                            cmSelect.append(newOption);
                        }
                    }
                });
                cmSelect.trigger('change');
            },
            error: function(err){
                console.error(err);
            }
        });
    }

     // ketika CM / chemical dipilih
    $('#cmSelect').on('change', function() {
        var cmCode = $(this).val();

      if (!cmCode) {
        showNoData();
        return;
    }

    tbody.html(noDataTemplate);

        // ambil FG dari controller
        $.ajax({
            url: '/fa/excel/fg', // endpoint harus menerima ?cm=CM01
            type: 'GET',
            data: { cm: cmCode },

            success: function(data) {
               if (!data.length) {
                showNoData();
                return;
            }

            tbody.empty();

                // hapus duplikat FG (kode + nama)
                var seen = {};
                var no = 1;

              data.forEach(function(item){
    var key = item.code + '|' + item.name;
    if(!seen[key]){
        seen[key] = true;

        // tentukan warna baris ganjil/genap
        var rowBg = (no % 2 === 1) ? 'bg-gray-50' : 'bg-white';

        var row = '<tr class="'+rowBg+' hover:bg-indigo-100 transition-colors duration-200">'+
            '<td class="px-4 py-2 text-center">'+ no +'</td>'+
            '<td class="px-4 py-2 text-center">'+ item.code +'</td>'+
            '<td class="px-4 py-2 text-left">'+ item.name +'</td>'+
            '</tr>';
        tbody.append(row);
        no++;
    }
});

            },
            error: function(err){
                console.error(err);
            }
        });

    });

     $('#export-button').on('click', function() {
        window.location.href = '/fa/excel/export-cm-fg';
    });

    // Optional: load CM saat halaman ready jika sudah ada data di cache
    loadCM();

     // Inisialisasi Select2
    $('#rmSelect').select2({
        placeholder: "-- Choose Raw Material --",
        allowClear:true,
        width: '100%'
    });

      loadRM();

    function loadRM() {
        
        $.ajax({
            url: '/fa/excel/rm', // endpoint controller mengembalikan {code, name}
            type: 'GET',
            success: function(data) {
                var rmSelect = $('#rmSelect');
                rmSelect.empty(); // kosongkan dulu
                rmSelect.append('<option></option>'); // placeholder untuk Select2

                var seen = {};

                $.each(data, function(index, item){
                    if(item.code && item.name){
                        var key = item.code + '|' + item.name;
                        if(!seen[key]){
                            seen[key] = true;
                            var newOption = new Option(item.code + ' - ' + item.name, item.code, false, false);
                            rmSelect.append(newOption);
                        }
                    }
                });
                rmSelect.trigger('change');
            },
            error: function(err){
                console.error(err);
            }
        });
    }

     // ketika CM / chemical dipilih
    $('#rmSelect').on('change', function() {
        
        var rmCode = $(this).val();

      if (!rmCode) {
        showNoData();
        return;
    }

    tbody.html(noDataTemplate);

        // ambil FG dari controller
        $.ajax({
            url: '/fa/excel/fgrm', // endpoint harus menerima ?cm=CM01
            type: 'GET',
            data: { rm: rmCode },
            success: function(data) {

               if (!data.length) {
                showNoData();
                return;
            }

            tbody.empty();

                // hapus duplikat FG (kode + nama)
                var seen = {};
                var no = 1;

              data.forEach(function(item){
    var key = item.code + '|' + item.name;
    if(!seen[key]){
        seen[key] = true;

        // tentukan warna baris ganjil/genap
        var rowBg = (no % 2 === 1) ? 'bg-gray-50' : 'bg-white';

        var row = '<tr class="'+rowBg+' hover:bg-indigo-100 transition-colors duration-200">'+
            '<td class="px-4 py-2 text-center">'+ no +'</td>'+
            '<td class="px-4 py-2 text-center">'+ item.code +'</td>'+
            '<td class="px-4 py-2 text-left">'+ item.name +'</td>'+
            '</tr>';
        tbody.append(row);
        no++;
    }
});

            },
            error: function(err){
                console.error(err);
            }
        });

    });

     $('#export-button-rm').on('click', function() {
        window.location.href = '/fa/excel/export-rm-fg';
    });

    // Optional: load CM saat halaman ready jika sudah ada data di cache
    loadRM();

    function toggleSelect() {
    var cmVal = $('#cmSelect').val();
    var rmVal = $('#rmSelect').val();

    if (cmVal) {
        // Jika CM terisi → disable RM
        $('#rmSelect').prop('disabled', true);
        $('#rmSelect').val(null).trigger('change'); // reset RM
    } 
    else if (rmVal) {
        // Jika RM terisi → disable CM
        $('#cmSelect').prop('disabled', true);
        $('#cmSelect').val(null).trigger('change'); // reset CM
    } 
    else {
        // Kalau dua-duanya kosong → aktifkan semua
        $('#cmSelect').prop('disabled', false);
        $('#rmSelect').prop('disabled', false);
    }
}


});

function renderImportResult(data){

    let html = `
    <div class="bg-gray-50 border rounded-xl p-5 space-y-4">

        <div class="flex items-center gap-3 text-emerald-600">

            <svg width="28" height="28" viewBox="0 0 24 24">
                <path class="checkmark"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="3"
                    d="M5 13l4 4L19 7"/>
            </svg>

            <span class="font-semibold text-lg">
                Import Successful
            </span>
        </div>

        <div class="grid grid-cols-3 gap-4 text-sm">

            <div class="bg-white p-3 rounded-lg border">
                <p class="text-gray-400">Total Rows</p>
                <p class="font-semibold">${data.total_rows}</p>
            </div>

            <div class="bg-white p-3 rounded-lg border">
                <p class="text-gray-400">Success</p>
                <p class="font-semibold text-emerald-600">${data.success}</p>
            </div>

            <div class="bg-white p-3 rounded-lg border">
                <p class="text-gray-400">Failed</p>
                <p class="font-semibold text-red-600">${data.failed}</p>
            </div>

        </div>
    `;

    // ===== ERROR LIST =====
    if(data.errors && data.errors.length){

        html += `
        <div class="mt-4">
            <p class="font-semibold text-red-600 mb-2">
                Failed Rows
            </p>

            <div class="max-h-40 overflow-auto border rounded-lg">
        `;

        data.errors.forEach(function(err){
            html += `
                <div class="px-3 py-2 border-b text-sm flex justify-between">
                    <span>Row ${err.row}</span>
                    <span class="text-red-500">${err.message}</span>
                </div>
            `;
        });

        html += `</div></div>`;
    }

    // ✅ PENUTUP DIV UTAMA (INI YANG HILANG)
    html += `</div>`;

    $('#import-result')
        .html(html)
        .removeClass('hidden');
}


</script>
@endpush
@endsection
