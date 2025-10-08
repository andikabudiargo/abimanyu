@extends('layouts.app')

@section('title', 'Create Ticket')
@section('page-title', 'Create Ticket')
@section('breadcrumb-item', 'Ticket Management')
@section('breadcrumb-active', 'Create Ticket')
@section('content')
<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New Ticket</h2>
    <form id="ticket-form" enctype="multipart/form-data">
      @csrf
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="ticket_number" class="block text-sm font-medium text-gray-700 mb-1">Ticket Number<small class="text-red-600"> *</small></label>
        <input type="text" name="ticket_number" id="ticket_number"
               class="w-full px-3 py-2 bg-gray-200 text-white border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Automatic" readonly />
        </div>
         <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Category <small class="text-red-600">*</small></label>
  <select id="categoryDropdown" name="category_id" required
          class="w-full px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Choose Category --</option>
    <!-- Options loaded by JS -->
  </select>
</div>

      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Subject<small class="text-red-600"> *</small></label>
        <input type="text" name="title" id="title"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
         <div class="col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
      <div class="col-span-2 mb-4">
  <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
    Attachment <small class="text-red-600"> *</small>
  </label>
  <input type="file" name="attachments[]" id="attachments" multiple required
         class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
  <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF, XLSX, Docs. Max total: 20MB.</p>

   <!-- List Selected Files -->
    <div class="mt-2">
        <ul id="selectedFilesList" class="list-disc list-inside text-sm text-gray-700"></ul>
    </div>
</div>
</div>
      <hr>
      <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('it.ticket.index') }}" 
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   ← Back
</a>

<button type="submit" id="submitBtn"
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
   <i data-feather="save" class="h-4 w-4"></i>
   Save
</button>
      </div>
    </form>
</div>
<style>
    /* Supaya select2 full width */
.select2-container {
  width: 100% !important;
}

/* Supaya tinggi sama dengan input Tailwind */
.select2-container .select2-selection--single {
  height: 40px !important; /* total tinggi */
  display: flex !important;
  align-items: center !important;
  border: 1px solid #d1d5db; /* border-gray-300 */
  border-radius: 0.375rem;   /* rounded-md */
  padding: 0 0.75rem !important; /* px-3 */
  line-height: normal !important;
}

/* Hilangkan padding default di dalam text */
.select2-container .select2-selection__rendered {
  padding-left: 0 !important;
  padding-right: 0 !important;
  line-height: 1.5rem !important; /* sama seperti input tailwind text-base */
}


/* Placeholder dan text select2 */
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 42px !important;
  font-size: 15px; /* tailwind text-base */
  color: #374151;  /* tailwind text-gray-700 */
}

/* Tombol dropdown */
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 42px !important;
  right: 0.75rem;
}
</style>
@push('scripts')
<script>
$('#ticket-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("it.ticket.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'Ticket succesfully saved!');
                setTimeout(() => {
                    // ✅ Redirect ke halaman index
                    window.location.href = '{{ route("it.ticket.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal menyimpan ticket.');
            }
        },
        error: function (err) {
            console.error(err.responseText);
            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
            showToast('error', msg);
        }
    });
});


// Fungsi Toast menggunakan SweetAlert2
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon, // 'success', 'error', 'warning', 'info', 'question'
        title: title
    });
}

$(document).ready(function () {
    // Inisialisasi select2
    $('#categoryDropdown').select2({
        placeholder: "-- Choose Category --",
        allowClear: true,
        width: "100%",
        
    });

    // Ambil data kategori via AJAX
    $.ajax({
        url: '/it/category/dropdown',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Kosongkan dulu
            $('#categoryDropdown').empty();

            // Tambahkan placeholder
            $('#categoryDropdown').append(
                $('<option>', { value: '', text: '-- Choose Category --' })
            );

            // Loop group
            $.each(data, function (i, group) {
                let $optgroup = $('<optgroup>', { label: group.label });

                // Loop option
                $.each(group.options, function (j, option) {
                    $optgroup.append(
                        $('<option>', {
                            value: option.id,
                            text: option.description
                        })
                    );
                });

                $('#categoryDropdown').append($optgroup);
            });

            // Refresh select2 biar tampil datanya
            $('#categoryDropdown').trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Gagal memuat kategori:", error);
        }
    });
});

const attachmentsInput = document.getElementById('attachments');
const selectedFilesList = document.getElementById('selectedFilesList');
let currentFiles = []; // Array untuk menyimpan semua file

// Fungsi untuk menampilkan icon berdasarkan ekstensi
function getFileIcon(fileName) {
    const ext = fileName.split('.').pop().toLowerCase();
    switch(ext) {
        case 'pdf': return 'file-text';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif': return 'image';
        case 'xlsx':
        case 'xls': return 'file';
        case 'doc':
        case 'docx': return 'file-text';
        default: return 'file';
    }
}

// Fungsi untuk format size
function formatBytes(bytes) {
    if(bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function renderFileList() {
    // Update input.files sesuai currentFiles
    const dt = new DataTransfer();
    currentFiles.forEach(file => dt.items.add(file));
    attachmentsInput.files = dt.files;

    // Render card per file
    selectedFilesList.innerHTML = '';
    currentFiles.forEach((file, index) => {
        const li = document.createElement('li');
        li.classList.add('flex', 'items-center', 'justify-between', 'mb-2', 'p-2', 'bg-white', 'shadow-sm', 'rounded-lg', 'border', 'border-gray-200');
        li.innerHTML = `
            <div class="flex items-center gap-3">
                <i data-feather="${getFileIcon(file.name)}" class="w-5 h-5 text-gray-500"></i>
                <div class="flex flex-col">
                    <span class="text-gray-800 font-medium">${file.name}</span>
                    <span class="text-xs text-gray-500">${formatBytes(file.size)}</span>
                </div>
            </div>
            <button type="button" class="text-red-500 ml-2" data-index="${index}">
                <i data-feather="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        selectedFilesList.appendChild(li);
    });

    feather.replace();
}

attachmentsInput.addEventListener('change', () => {
    // Tambahkan file baru ke currentFiles
    currentFiles = [...currentFiles, ...Array.from(attachmentsInput.files)];
    renderFileList();
});

selectedFilesList.addEventListener('click', function(e) {
    const btn = e.target.closest('button[data-index]');
    if (!btn) return;

    const index = btn.dataset.index;
    currentFiles.splice(index, 1);

    renderFileList();
});

</script>

@endpush

@endsection