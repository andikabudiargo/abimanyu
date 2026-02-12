@extends('layouts.app')

@section('title', 'CAPA Verification')
@section('page-title', 'CAPA VERIFICATION')
@section('breadcrumb-item', 'CAPA Management')
@section('breadcrumb-active', 'CAPA Verification')
@section('content')
 <div class="flex flex-col md:flex-row gap-3">
<div class="w-full md:w-1/3 bg-white rounded-xl border border-gray-200 shadow-sm mb-4">

    <!-- HEADER -->
    <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
            Audit Assignment
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Auditor & Auditee Information
        </p>
    </div>

    <form id="capa-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- AUDITOR -->
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-800">Auditor</h3>
            <p class="text-xs text-gray-500 mb-3">Assigned audit team</p>

            <div class="flex flex-col gap-2">
                @if($capa->auditors && $capa->auditors->count() > 0)
                    @foreach($capa->auditors as $auditor)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50 shadow-sm">
                            <i class="fa fa-user text-indigo-500 text-lg"></i>
                            <div class="flex flex-col">
                                <span class="text-gray-800 font-medium">{{ $auditor->users->name ?? '-' }}</span>
                                <span class="text-xs text-gray-500">
    @if($auditor->users && $auditor->users->departments && $auditor->users->departments->count() > 0)
        {{ $auditor->users->departments->pluck('name')->join(', ') }}
    @else
        -
    @endif
</span>

                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-gray-400 italic text-sm">No auditors assigned</span>
                @endif
            </div>
        </div>

        <!-- AUDITEE -->
        <div class="px-5 py-4">
            <h3 class="text-sm font-medium text-gray-800">Auditee</h3>
            <p class="text-xs text-gray-500 mb-3">Department involved in the audit</p>

           @if($capa->dept_id && $capa->departemen)
    <div class="p-3 rounded-lg border border-gray-100 bg-gray-50 shadow-sm flex flex-col gap-2">
        <!-- Department -->
        <div class="flex items-center gap-2">
            <i class="fa fa-building text-indigo-500 text-sm"></i>
            <span class="font-medium text-gray-800">Department:</span>
             <span class="text-gray-700">{{ $capa->department_display }}</span>
        </div>

        <!-- Representative -->
        <div class="flex items-center gap-2">
            <i class="fa fa-user-tie text-indigo-500 text-sm"></i>
            <span class="font-medium text-gray-800">Dept. Representative:</span>
            <span class="text-gray-700">{{ $capa->representative->name ?? '-' }}</span>
        </div>
    </div>
@else
    <span class="text-gray-400 italic text-sm">No department assigned</span>
@endif

        </div>

</div>


<div class="w-full md:w-2/3 bg-white shadow-lg rounded-2xl p-8 space-y-8 mb-4">

  <div class="flex flex-col gap-3
            sm:flex-row sm:justify-between sm:items-start
            border-b pb-4">

    <!-- Title -->
    <div class="min-w-0">
        <h1 class="flex items-center gap-2
                   text-xl sm:text-2xl
                   font-semibold text-gray-800 tracking-tight">
            CAPA Review & Verification
        </h1>

        <p class="flex items-center gap-2
                  text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
            Corrective & Preventive Action Verification
        </p>
    </div>

    <!-- Status Badge -->
   <!-- Status Badge -->
<div class="flex sm:items-center">
    <span class="inline-flex items-center justify-center gap-1.5
        px-3 py-1 text-sm font-semibold rounded-full
        bg-yellow-100 text-yellow-800 border border-yellow-300
        w-fit sm:w-28">
        <i class="fa-regular fa-check-circle text-xs text-yellow-600"></i>
        Open
    </span>
</div>


</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
       <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        CAPA Number <small class="text-red-600">*</small>
    </label>
    <input type="text" name="capa_number" id="capa_number"
           placeholder="Enter CAPA Number (e.g., CAPA-2026-001)"
           class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
           required />
</div>

         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Report Date <small class="text-red-600">*</small>
          </label>
          <input type="date" name="report_date" id="report_date"
                 class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
</div>
     
   <!-- SECTION: Audit Information -->
<section class="space-y-8">

    <!-- Source -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Source of the Finding <span class="text-red-600">*</span>
        </label>

        <div class="flex flex-wrap gap-2">
            <label class="flex items-center gap-2 px-4 py-2 border rounded-lg
                bg-indigo-50 border-indigo-200 text-indigo-700 cursor-pointer
                hover:bg-indigo-100 transition">
                <input type="radio" name="source" value="Audit" checked class="hidden">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span class="text-sm font-medium">Audit</span>
            </label>

            <span class="flex items-center gap-2 px-4 py-2 border rounded-lg
                text-gray-400 bg-gray-50 cursor-not-allowed">
                <i class="fa-regular fa-comment-dots text-xs"></i>
                Complain
            </span>

            <span class="flex items-center gap-2 px-4 py-2 border rounded-lg
                text-gray-400 bg-gray-50 cursor-not-allowed">
                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                Non-Conformity
            </span>

            <span class="flex items-center gap-2 px-4 py-2 border rounded-lg
                text-gray-400 bg-gray-50 cursor-not-allowed">
                <i class="fa-solid fa-people-arrows text-xs"></i>
                Management Review
            </span>
        </div>
    </div>

   <div class="flex flex-col gap-2">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Category <span class="text-red-600">*</span>
    </label>

    <div class="flex flex-wrap gap-2">
        <!-- CRITICAL -->
        <input type="radio" name="category" id="cat_critical" value="Critical"
            class="peer/critical hidden"
            {{ ($capa->category ?? '') === 'Critical' ? 'checked' : '' }}>
        <label for="cat_critical"
            class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
                   text-gray-700 hover:bg-gray-50
                   peer-checked/critical:bg-red-50
                   peer-checked/critical:border-red-300
                   peer-checked/critical:text-red-700">
            Critical
        </label>

        <!-- MAJOR -->
        <input type="radio" name="category" id="cat_major" value="Major"
            class="peer/major hidden"
            {{ ($capa->category ?? '') === 'Major' ? 'checked' : '' }}>
        <label for="cat_major"
            class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
                   text-gray-700 hover:bg-gray-50
                   peer-checked/major:bg-yellow-50
                   peer-checked/major:border-yellow-300
                   peer-checked/major:text-yellow-700">
            Major
        </label>

        <!-- MINOR -->
        <input type="radio" name="category" id="cat_minor" value="Minor"
            class="peer/minor hidden"
            {{ ($capa->category ?? '') === 'Minor' ? 'checked' : '' }}>
        <label for="cat_minor"
            class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
                   text-gray-700 hover:bg-gray-50
                   peer-checked/minor:bg-blue-50
                   peer-checked/minor:border-blue-300
                   peer-checked/minor:text-blue-700">
            Minor
        </label>

        <!-- OBSERVATION -->
        <input type="radio" name="category" id="cat_observation" value="Observation"
            class="peer/observation hidden"
            {{ ($capa->category ?? '') === 'Observation' ? 'checked' : '' }}>
        <label for="cat_observation"
            class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
                   text-gray-700 hover:bg-gray-50
                   peer-checked/observation:bg-green-50
                   peer-checked/observation:border-green-300
                   peer-checked/observation:text-green-700">
            Observation
        </label>
    </div>
</div>



      <!-- DETAIL OF INFORMATION (EDITABLE) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Detail of Information <small class="text-red-600">*</small>
          </label>
          <input type="text" name="detail_of_information" id="detail_of_information"
                 value="{{ $capa->detail_of_information }}"
                 class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>

        <!-- PROBLEM (EDITABLE) -->
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Problem <small class="text-red-600">*</small></label>
          <textarea name="problem" id="problem" rows="5"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
          >{{ $capa->problem }}</textarea>
        </div>
      </div>

      <hr class="my-4">

      <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

         <button type="submit" id="submitBtn"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
            <i class="fa-solid fa-clipboard-check"></i>
           Verification
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
$('#capa-form').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $('#submitBtn');

    const capaNumber = $('#capa_number').val().trim();

    if (!capaNumber) {
        Swal.fire('Warning', 'CAPA Number wajib diisi!', 'warning');
        return;
    }

    // Disable tombol
    $btn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
    const originalText = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Checking...');

    /* ===============================
       STEP 1: CEK DUPLIKAT DULU
    =============================== */

    $.ajax({
        url: '{{ route("mr.capa.checkNumber") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            capa_number: capaNumber
        },
        success: function (check) {

            /* ===============================
               JIKA DUPLIKAT → STOP
            =============================== */

            if (check.exists) {

                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate CAPA Number',
                    text: 'Nomor CAPA sudah terdaftar!',
                });

                // Restore button
                $btn.prop('disabled', false)
                    .removeClass('opacity-50 cursor-not-allowed')
                    .html(originalText);

                $('#capa_number').focus();

                return;
            }

            /* ===============================
               JIKA AMAN → LANJUT SUBMIT
            =============================== */

            submitForm($form, $btn, originalText);

        },
        error: function () {

            Swal.fire('Error', 'Gagal cek CAPA Number!', 'error');

            $btn.prop('disabled', false)
                .removeClass('opacity-50 cursor-not-allowed')
                .html(originalText);
        }
    });
});


/* ===============================
   FUNCTION SUBMIT ASLI
=============================== */

function submitForm($form, $btn, originalText) {

    const formData = new FormData($form[0]);

    $.ajax({
        url: '{{ route("mr.capa.verified.save", $capa->id) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {

            if (res.success) {

                showToast('success', res.message || 'CAPA successfully Verified!');

                setTimeout(() => {
                    window.location.href = '{{ route("mr.capa.index") }}';
                }, 2000);

            } else {

                showToast('error', res.message || 'Gagal verifikasi.');

                restoreBtn($btn, originalText);
            }
        },

        error: function (err) {

            console.error(err.responseText);

            showToast('error', 'Terjadi kesalahan sistem.');

            restoreBtn($btn, originalText);
        }
    });
}


/* ===============================
   BUTTON RESTORE
=============================== */

function restoreBtn($btn, text) {

    $btn.prop('disabled', false)
        .removeClass('opacity-50 cursor-not-allowed')
        .html(text);
}



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

$('#department').on('change', function () {
    let deptId = $(this).val();

    let $repSelect = $('#representative');
    let $auditeeList = $('#auditee-list');

    // Reset
    $repSelect.html('<option value="">Loading...</option>');
    $auditeeList.html('<p class="text-gray-400">Loading...</p>');

    if (!deptId) {
        $repSelect.html('<option value="">-- Choose Dept. Representative --</option>');
        $auditeeList.html('<p class="text-gray-400">Choose department first...</p>');
        return;
    }

    $.ajax({
        url: `/it/departments/${deptId}/users`,
        type: 'GET',
        dataType: 'json',
        success: function (users) {

            // Isi representative
            $repSelect.html('<option value="">-- Choose Dept. Representative --</option>');

            if (users.length === 0) {
                $repSelect.html('<option value="">No staff found</option>');
                $auditeeList.html('<p class="text-red-500">No users found in this department.</p>');
                return;
            }

            $.each(users, function (i, user) {
                $repSelect.append(`<option value="${user.id}">${user.name}</option>`);
            });

            // Isi list auditee
            let html = '';
            $.each(users, function (i, user) {
                html += `
                    <div class="flex items-center gap-2 border-b py-1">
                        <span class="w-6 h-6 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-full">
                            ${i + 1}
                        </span>
                        <span>${user.name}</span>
                    </div>
                `;
            });
            $auditeeList.html(html);
        },
        error: function () {
            $repSelect.html('<option value="">Error loading data</option>');
            $auditeeList.html('<p class="text-red-500">Failed to load auditee list.</p>');
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