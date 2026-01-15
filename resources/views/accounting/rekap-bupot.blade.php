@extends('layouts.app')

@section('title', 'Automated Report BuPot')
@section('page-title', 'AUTOMATED REPORT BUPOT')
@section('breadcrumb-item', 'Supporting Tools')
@section('breadcrumb-active', 'Automated Report BuPot')

@section('content')

<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">

    <h2 class="text-lg font-semibold text-gray-700">Automated Report BuPot</h2>

    <!-- GUIDE / PANDUAN -->
    <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-900">
        <p class="font-semibold mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i>
            Panduan Penggunaan:
        </p>

        <ul class="list-disc pl-6 space-y-1">
            <li>Upload hanya file PDF Bukti Potong dari Coretax.</li>
            <li>Anda dapat memilih lebih dari satu file sekaligus.</li>
            <li>Jika salah pilih File, Gunakan tombol ❌ untuk menghapus file sebelum submit.</li>
            <li>Pastikan semua dokumen benar sebelum klik Generate.</li>
            <li>Klik Clear untuk menghapus seluruh dokumen yang dipilih.</li>
        </ul>
    </div>

    <form id="uploadForm" enctype="multipart/form-data" method="POST">
    @csrf

    <div class="flex flex-col md:flex-row gap-6">

        <!-- KOLOM 1: INPUT FILE -->
        <div class="md:flex-1 flex flex-col">

            <label class="font-semibold mb-2 block text-gray-700 tracking-wide">
                Choose PDF Files
            </label>

            <div class="relative border border-gray-400 bg-[#fdf9f3] rounded-xl 
                        shadow-[4px_4px_0_0_rgba(0,0,0,0.35)]
                        h-[300px] flex flex-col items-center justify-center
                        text-center px-6 transition-all duration-200 
                        hover:bg-[#f8f3ea] cursor-pointer">

                <!-- ICON -->
                <i class="fa-solid fa-file-arrow-up text-5xl text-purple-500 mb-4"></i>

                <!-- TEXT -->
                <p class="font-semibold text-gray-700">Click or Drag & Drop</p>
                <p class="text-sm text-gray-500">PDF files only</p>

                <!-- INPUT FILE Overlay -->
                <input 
                    id="pdfInput"
                    type="file"
                    name="pdf_files[]" 
                    accept="application/pdf"
                    multiple
                    class="absolute inset-0 opacity-0 cursor-pointer"
                >
            </div>
        </div>

        <!-- KOLOM 2: LIST FILE -->
        <div class="md:flex-1 flex flex-col">

            <div class="flex items-center justify-between mb-2">
                <label class="font-semibold text-gray-700 tracking-wide">
                    List Selected Files
                </label>

                <span id="fileCount" 
                    class="text-sm bg-[#e8e0d4] text-gray-700 px-2 rounded shadow-[2px_2px_0_0_rgba(0,0,0,0.3)]">
                    0 files
                </span>
            </div>

            <div 
    id="fileList"
    class="border border-gray-400 bg-[#fdf9f3] rounded-lg p-3 
           h-[300px] overflow-y-auto space-y-2 
           shadow-[4px_4px_0_0_rgba(0,0,0,0.35)]
           items-center justify-center"
>
    <div id="noFilesMessage" class="text-center text-purple-500 mt-12">
        <i class="fa-regular fa-folder-open text-4xl mb-3"></i>
        <p class="font-medium">No files selected</p>
        <p class="text-xs">Your uploaded PDF list will appear here</p>
    </div>
</div>

        </div>
    </div>



       <div class="flex flex-col gap-4 mt-4">
    <!-- BUTTON GENERATE -->
    <button
        id="generateBtn"
        type="submit"
        class="w-full bg-gray-900 text-white py-2 font-semibold tracking-wide 
               hover:bg-gray-700 transition shadow-[5px_5px_0_0_rgba(0,0,0,0.3)]"
    >
        Generate
    </button>

    <!-- BUTTON CLEAR -->
    <button
        id="clearFormBtn"
        type="button"
        class="w-full bg-gray-200 text-gray-800 py-2 font-semibold tracking-wide 
               hover:bg-gray-300 transition shadow-[5px_5px_0_0_rgba(0,0,0,0.3)]"
    >
        Clear
    </button>
</div>


    </form>
</div>

@push('scripts')

<script>
let selectedFiles = [];

$(document).on('change', '#pdfInput', function(e) {
    const files = Array.from(e.target.files);

    // gabungkan file baru dengan yang sudah ada
    selectedFiles = [...selectedFiles, ...files];

    renderFileList();
});

// render list dokumen
function renderFileList() {
    const container = $('#fileList');
    container.empty(); // kosongkan daftar dulu

    if (selectedFiles.length === 0) {
    $('#fileList').html('');
    $('#noFilesMessage').removeClass('hidden');
    return;
}


    selectedFiles.forEach((file, index) => {
        const ext = file.name.split('.').pop().toUpperCase();

        const item = `
            <div class="flex items-center justify-between bg-gray-50 border px-3 py-2 rounded">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                    <div>
                        <p class="font-medium text-gray-800">${file.name}</p>
                        <span class="text-xs text-gray-500">${ext} File</span>
                    </div>
                </div>

                <button data-index="${index}" 
                        class="remove-file text-red-600 font-bold text-lg px-2 hover:text-red-800">
                    ❌
                </button>
            </div>
        `;
        container.append(item);
    });

    updateFileCount();
    syncInputFiles();
}

// tombol x
$(document).on('click', '.remove-file', function() {
    const index = $(this).data('index');
    selectedFiles.splice(index, 1);

    renderFileList();
});

// update counter
function updateFileCount() {
    const count = selectedFiles.length;
    const label = count === 1 ? "file" : "files";

    $('#fileCount').text(`${count} ${label}`);
}

// sync ulang input file, karena FileList tidak bisa di-modify langsung
function syncInputFiles() {
    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file => dataTransfer.items.add(file));

    $('#pdfInput')[0].files = dataTransfer.files;
}

$('#clearFormBtn').on('click', function () {

    // kosongkan array file pilihan
    selectedFiles = [];

    // kosongkan input file
    $('#pdfInput').val('');

    // render ulang daftar
    renderFileList();

    // update badge jumlah file
    $('#fileCount').text('0 files');

    // tampilkan kembali pesan "No files selected"
    $('#noFilesMessage').removeClass('hidden');

    // berikan efek kecil agar terasa interaktif
    Swal.fire({
        icon: 'info',
        title: 'Cleared!',
        text: 'Semua file yang dipilih telah dibersihkan.',
        timer: 1200,
        showConfirmButton: false
    });
});

$('#uploadForm').on('submit', function(e) {
    e.preventDefault();

    // TAMPILKAN LOADING
    $('#generateBtn').prop('disabled', true).text('AI Sedang merekap Data...');

    let formData = new FormData(this);

    $.ajax({
    url: "{{ route('fa.bupot.process') }}",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    xhrFields: { responseType: 'blob' },

    beforeSend: function() {
        Swal.fire({
            title: 'Processing...',
            text: 'Mengolah PDF dan mengekspor Excel',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    success: function(blob) {

        Swal.close(); // tutup loading

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'File Excel berhasil dibuat dan siap diunduh.',
            timer: 1800,
            showConfirmButton: false
        });

        const fileName = "rekap_bukti_potong_" + Date.now() + ".xlsx";

        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = fileName;
        link.click();

        $('#generateBtn').prop('disabled', false).text('Generate');
    },

    error: function(err) {
        Swal.close(); // tutup loading

        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Terjadi error saat memproses PDF. Periksa kembali file Anda.',
        });

        $('#generateBtn').prop('disabled', false).text('Generate');
    }
});



});

</script>

@endpush
@endsection
