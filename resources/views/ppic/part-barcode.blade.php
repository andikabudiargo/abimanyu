@extends('layouts.app')

@section('title', 'Automated Generate Barcode Part')
@section('page-title', 'AUTOMATED GENERATE BARCODE PART')
@section('breadcrumb-item', 'Logistic')
@section('breadcrumb-active', 'Automated Generate Barcode Part')

@section('content')

<div class="w-full bg-white shadow-md rounded-xl p-6 space-y-4 mb-4">

    <h2 class="text-lg font-semibold text-gray-700">Automated Generate Barcode Part</h2>

    <!-- GUIDE / PANDUAN -->
    <div class="mb-8 p-4 bg-yellow-500 border border-gray-900 rounded-xl text-sm text-yellow-900 shadow-[5px_5px_0_0_rgba(0,0,0,0.8)] hover-shake">
        <p class="font-bold mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i>
            Panduan Penggunaan:
        </p>

        <ul class="list-disc pl-6 space-y-1">
            <li>Satukan semua gambar part ke dalam satu file Zip</li>
            <li>Upload zip ke sistem.</li>
            <li>Jika salah pilih File, Gunakan tombol ❌ untuk menghapus file sebelum submit.</li>
            <li>Pastikan dokumen zip benar sebelum klik Generate.</li>
            <li>Klik Clear untuk menghapus seluruh zip yang dipilih.</li>
        </ul>
    </div>

    <form id="uploadForm" enctype="multipart/form-data" method="POST">
    @csrf

  <div class="flex flex-col md:flex-row gap-6">

    <!-- KOLOM 1: INPUT FILE -->
    <div class="md:flex-1 flex flex-col">
        <div class="bg-blue-400 rounded-xl shadow-[4px_4px_0_0_rgba(0,0,0,0.8)] border-2 border-gray-900">

            <!-- HEADER -->
            <div class="px-6 py-3 border-b border-gray-300">
                <label class="font-semibold text-white tracking-wide">
                    Choose Zip Files
                </label>
            </div>

            <!-- BODY / DROPZONE -->
            <div class="relative h-[300px] flex flex-col items-center bg-[#fdf9f3] rounded-b-xl justify-center text-center px-6 transition-all duration-200 hover:bg-[#fdf0d6] cursor-pointer">
                <i class="fa-solid fa-file-arrow-up text-5xl text-pink-500 mb-4"></i>
                <p class="font-semibold text-gray-700">Klik atau Drag & Drop File Disini.</p>
                <p class="text-sm text-gray-500">Only Zip File</p>

                <!-- DUMMY BUTTON -->
                <div class="px-4 py-2 mt-2 w-48 bg-white text-black font-semibold hover:bg-gray-50 border border-black transition-colors shadow-[2px_2px_0_0_rgba(0,0,0,0.8)]">
                    Pilih File Zip
                </div>

                <!-- INPUT FILE Overlay -->
                <input 
                    id="pdfInput"
                    type="file"
                    name="zip_files" 
                   accept=".zip"
                    multiple
                    class="absolute inset-0 opacity-0 cursor-pointer"
                >
            </div>
        </div>
    </div>

    <!-- KOLOM 2: LIST FILE -->
    <div class="md:flex-1 flex flex-col">
        <div class="bg-pink-400 rounded-xl shadow-[4px_4px_0_0_rgba(0,0,0,0.8)] border-2 border-gray-900">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-3">
                <label class="font-semibold text-white tracking-wide">
                    List Selected Files
                </label>
                <span id="fileCount" class="text-sm bg-white text-black px-2 border border-black shadow-[2px_2px_0_0_rgba(0,0,0,0.8)]">
                    0 files
                </span>
            </div>

            <!-- BODY -->
            <div id="fileList" class="border border-gray-400 bg-[#fdf9f3] rounded-b-xl h-[300px] overflow-y-auto space-y-4 px-6 transition-all hover:bg-[#fdf0d6] duration-200 py-4 flex flex-col justify-center">
                <div id="noFilesMessage" class="text-center mt-auto mb-auto">
                    <i class="fa-regular fa-folder-open text-4xl text-blue-500 mb-3"></i>
                    <p class="font-medium text-gray-900">Belum ada File yang dipilih, Nih.</p>
                    <p class="text-xs text-gray-400">Your uploaded Zip list will appear here</p>
                </div>
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

<style>
    @layer components {
  .swal-bg-blue {
    @apply bg-blue-500 text-white rounded-xl shadow-xl;
  }
}

    @keyframes shake {
  0%, 100% { transform: translateX(0); }
  20% { transform: translateX(-5px); }
  40% { transform: translateX(5px); }
  60% { transform: translateX(-5px); }
  80% { transform: translateX(5px); }
}

.hover-shake:hover {
  animation: shake 0.5s ease-in-out;
}
</style>

@push('scripts')

<script>
let selectedFiles = [];

$(document).on('change', '#pdfInput', function(e) {
    const files = Array.from(e.target.files);

    // gabungkan file baru dengan yang sudah ada
    selectedFiles = [...selectedFiles, ...files];

    renderFileList();
});

function renderFileList() {
    const container = $('#fileList');
    container.empty(); // kosongkan daftar dulu

    if (selectedFiles.length === 0) {
        // Jika kosong → tampilkan pesan
        $('#noFilesMessage').removeClass('hidden');
        container.addClass('flex justify-center'); // aktifkan center
        container.append($('#noFilesMessage'));
        return;
    }

    // Ada file → hapus pesan & hilangkan flex center
    $('#noFilesMessage').addClass('hidden');
    container.removeClass('flex justify-center');

    selectedFiles.forEach((file, index) => {
        const ext = file.name.split('.').pop().toUpperCase();

        const item = `
            <div class="flex items-center justify-between bg-white border border-black px-3 py-2 shadow-[5px_5px_0_0_rgba(0,0,0,0.8)] my-2">
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
    // Kosongkan array file pilihan
    selectedFiles = [];

    // Kosongkan input file
    $('#pdfInput').val('');

    // Render ulang daftar → otomatis menampilkan "No files selected"
    renderFileList();

    // Update badge jumlah file
    $('#fileCount').text('0 files');

    // Berikan efek kecil agar terasa interaktif
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

    $('#generateBtn')
        .prop('disabled', true)
        .text('AI Sedang Generate Barcode...');

    let formData = new FormData(this);

    // ⬇️ HARUS DI LUAR ajax
    let dotCount = 0;
    let loadingInterval;

    $.ajax({
        url: "{{ route('ppic.barcode-part.process') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        xhrFields: { responseType: 'blob' },

        beforeSend: function () {
            Swal.fire({
                title: 'Processing',
                text: 'Santai... Biarkan sistem menyiapkan barcodenya untuk Anda~',
                imageUrl: '/img/task-done.gif',
                imageWidth: 450,
                imageHeight: 300,
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
        popup: 'swal-bg-blue'
    },
                didOpen: () => {
                    loadingInterval = setInterval(() => {
                        dotCount = (dotCount + 1) % 4; // 0–3
                        Swal.getTitle().textContent =
                            'Processing' + '.'.repeat(dotCount);
                    }, 500);
                },
                willClose: () => {
                    clearInterval(loadingInterval); // 🧹 bersih-bersih
                }
            });
        },

        success: function(blob) {
            Swal.close();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File Excel berhasil dibuat dan siap diunduh.',
                timer: 1800,
                showConfirmButton: false
            });

            const fileName = "Konversi Barcode" + Date.now() + ".zip";
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = fileName;
            link.click();

            $('#generateBtn').prop('disabled', false).text('Generate');
        },

        error: function() {
            Swal.close();

            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Terjadi error saat memproses Zip.',
            });

            $('#generateBtn').prop('disabled', false).text('Generate');
        }
    });
});



</script>

@endpush
@endsection
