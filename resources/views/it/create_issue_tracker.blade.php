@extends('layouts.app')

@section('title', 'Request Perbaikan Fasilitas')
@section('page-title', 'Request Perbaikan Fasilitas')
@section('breadcrumb-item', 'Facility Issue Management')
@section('breadcrumb-active', 'Request Perbaikan Fasilitas')
@section('content')
<div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- ===== 1/3 Card Identitas Pemohon ===== -->
    <div class="bg-white shadow-md rounded-xl p-4 space-y-4">
       <h3 class="flex items-center text-lg font-semibold text-gray-700 mb-3">
    <i data-feather="user" class="w-5 h-5 text-gray-700 mr-2"></i>
    Identitas Pemohon
</h3>

    <div class="bg-gray-50 p-5 rounded-xl shadow-sm">
    <div class="flex items-center space-x-4 mb-4">
        <!-- Avatar -->
        <div class="w-14 h-14 bg-indigo-100 text-indigo-600 flex items-center justify-center rounded-full text-xl font-semibold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <!-- Nama & Departemen -->
        <div>
            <p class="text-gray-900 font-semibold text-lg">{{ $user->name }}</p>
            <p class="text-gray-700 text-sm flex items-center space-x-1">
                <i data-feather="briefcase" class="w-4 h-4 text-indigo-500"></i>
                <span>{{ optional($user->departments->first())->name ?? '-' }}</span>
            </p>
            <p class="text-gray-700 text-sm flex items-center space-x-1">
                <i data-feather="mail" class="w-4 h-4 text-blue-500"></i>
                <span>{{ $user->email ?? $user->phone ?? '-' }}</span>
            </p>
        </div>
    </div>

    <div class="border-t border-gray-200 mt-2 pt-4 space-y-3">
        <div class="flex items-start sm:items-center space-x-3">
            <i data-feather="user" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
            <div class="text-sm">
                <span class="font-medium text-gray-800">Nama Pemohon:</span>
                <span class="text-gray-700">{{ $user->name }}</span>
            </div>
        </div>

        <div class="flex items-start sm:items-center space-x-3">
            <i data-feather="briefcase" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
            <div class="text-sm">
                <span class="font-medium text-gray-800">Departemen:</span>
                <span class="text-gray-700">{{ optional($user->departments->first())->name ?? '-' }}</span>
            </div>
        </div>

        <div class="flex items-start sm:items-center space-x-3">
            <i data-feather="mail" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
            <div class="text-sm">
                <span class="font-medium text-gray-800">Kontak:</span>
                <span class="text-gray-700">{{ $user->email ?? $user->phone ?? '-' }}</span>
            </div>
        </div>

        <div class="flex items-start sm:items-center space-x-3">
            <i data-feather="calendar" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
            <div class="text-sm">
                <span class="font-medium text-gray-800">Tanggal Permintaan:</span>
               <span class="text-gray-700">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>
</div>

    </div>

   <!-- ===== 2/3 Card Detail Permintaan ===== -->
<div class="bg-white shadow-md rounded-xl p-4 md:p-6 space-y-4 md:col-span-2">
  <h3 class="flex items-center text-lg font-semibold text-gray-700 mb-3">
    <i data-feather="info" class="w-5 h-5 text-gray-700 mr-2"></i>
    Detail Permintaan
  </h3>

  <form id="issue-form" enctype="multipart/form-data">
    @csrf

    <!-- Grid utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

      <!-- Lokasi Area -->
      <div>
        <label for="location_area" class="block text-sm font-medium text-gray-700 mb-1">
          Lokasi Area Perbaikan <small class="text-red-600">*</small>
        </label>
        <textarea
          name="location_area" id="location_area" rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
          placeholder="Masukkan lokasi area perbaikan..." required></textarea>
      </div>

      <!-- Jenis Fasilitas -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Jenis Fasilitas / Area <small class="text-red-600">*</small>
        </label>
        <div class="flex flex-wrap -mx-1">
          @foreach (['Gedung','Listrik','Air/Plumbing','AC','Furniture','Keamanan','Kebersihan','Lainnya'] as $type)
          <label class="inline-flex items-center w-1/2 sm:w-1/3 px-1 mb-2">
            <input type="radio" name="request_type" value="{{ $type }}" class="form-radio text-indigo-600" required>
            <span class="ml-2 text-gray-700 text-sm">{{ $type }}</span>
          </label>
          @endforeach
        </div>
      </div>

      <!-- Deskripsi -->
      <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
          Deskripsi Kerusakan / Kebutuhan <small class="text-red-600">*</small>
        </label>
        <textarea
          name="description" id="description" rows="4"
          class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
          placeholder="Tuliskan deskripsi masalah atau kebutuhan di sini..."
        ></textarea>
      </div>

      <!-- Upload Foto -->
      <div>
        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
          Unggah Foto Kerusakan <small class="text-red-600">*</small>
        </label>
        <input type="file" name="attachment" id="attachment"
               class="w-full border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500
                      file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
        <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF, XLSX, DOCX. Max total: 20MB.</p>
        <ul id="selectedFilesList" class="mt-2 list-disc list-inside text-sm text-gray-700"></ul>
      </div>

      <!-- Urgensi -->
      <div>
        <label for="urgency" class="block text-sm font-medium text-gray-700 mb-1">
          Urgensi Permintaan <small class="text-red-600">*</small>
        </label>
        <select id="urgency" name="urgency" required
                class="w-full px-3 py-2 border border-gray-300 text-sm rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">-- Pilih Urgensi --</option>
          <option value="normal" class="text-green-500" selected>Normal / Rutin</option>
          <option value="segera" class="text-yellow-500">Segera</option>
          <option value="darurat" class="text-red-500">Darurat</option>
        </select>
      </div>

      <!-- Rekomendasi -->
      <div class="md:col-span-2">
        <label for="recommendation" class="block text-sm font-medium text-gray-700 mb-1">
          Rekomendasi / Saran Pemohon <small class="text-gray-400">(Opsional)</small>
        </label>
        <textarea name="recommendation" id="recommendation" rows="3"
                  class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                  placeholder="Masukkan rekomendasi atau saran tambahan (opsional)..."></textarea>
      </div>

    </div>

    <hr class="my-4">

    <!-- Buttons -->
    <div class="flex flex-col sm:flex-row justify-start items-stretch sm:items-center gap-3 mt-4">
      <a href="{{ route('it.issue.index') }}"
         class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow w-full sm:w-32 text-center">
         ← Back
      </a>

      <button type="submit" id="submitBtn"
              class="flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow w-full sm:w-32 text-center">
              <i data-feather="save" class="h-4 w-4"></i> Save
      </button>
    </div>

  </form>
</div>

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
$('#issue-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("it.issue.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'Request succesfully submitted!');
                setTimeout(() => {
                    // ✅ Redirect ke halaman index
                    window.location.href = '{{ route("it.issue.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal submit request.');
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