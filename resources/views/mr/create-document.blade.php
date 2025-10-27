@extends('layouts.app')

@section('title', 'Create Document')
@section('page-title', 'CREATE DOCUMENT')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Create Document')
@section('content')
<form id="doc-form" enctype="multipart/form-data">
  @csrf
  <!-- DIV UTAMA CONTAINER -->
    
    <!-- Flex container kiri + kanan -->
    <div class="flex flex-col md:flex-row">
      <!-- LEFT CONTENT (2/3) -->
      <div class="w-full md:w-2/3 bg-white p-4 space-y-4 border border-gray-800">
   <div class="w-full bg-white border-b border-gray-800 p-4 flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-6">
  <!-- Logo -->
  <div class="flex-shrink-0">
    <img src="{{ asset('img/logo-2.jpg') }}" alt="Company Logo" class="h-12 sm:h-16 w-auto">
  </div>

  <!-- Divider vertikal untuk desktop -->
  <div class="hidden sm:block border-l border-gray-300 h-16"></div>


  <!-- Judul -->
  <div class="text-center sm:text-left">
    <h2 class="text-base sm:text-lg font-semibold text-gray-700">
      FORM PENGAJUAN PEMBUATAN DAN PERUBAHAN DOKUMEN
      <span class="hidden sm:block text-sm text-gray-400 italic mt-1">
        SUBMISSION FORM MAKING AND CHANGING DOCUMENTS
      </span>
    </h2>
  </div>
</div>
        <!-- INPUTS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-6">

          <div class="col-span-2">
  <label class="block text-sm font-medium text-gray-700 mb-2">
    Jenis Dokumen / Document Type <small class="text-red-600">*</small>
  </label>

  <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-12">
    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="document_type" value="Form"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Form</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="document_type" value="Work Instructions"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Work Instructions</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="document_type" value="Standard"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Standard</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="document_type" value="SOP"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">SOP</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
  <input type="radio" name="document_type" value="other" id="otherRadio"
         class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
  <span class="text-sm text-gray-700">Other</span>
</label>

<!-- Input untuk ketik sendiri, default disembunyikan -->
<input type="text" name="otherInput" id="otherInput" placeholder="Ketik tipe dokumen"
       class="border-b border-gray-300 rounded w-full sm:w-auto hidden
              focus:outline-none focus:ring-0">

  </div>
</div>

<div class="col-span-2">
  <label class="block text-sm font-medium text-gray-700 mb-2">
    Jenis Pengajuan / Submission Type <small class="text-red-600">*</small>
  </label>

  <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-12">
    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="remark" value="New Release"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Baru / New Release</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="remark" value="Revision"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Revisi / Revision</span>
    </label>

    <label class="flex items-center space-x-2 w-full sm:w-auto">
      <input type="radio" name="remark" value="Obsolete"
        class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
      <span class="text-sm text-gray-700">Kadaluwarsa / Obsolete</span>
    </label>
  </div>
</div>


          <div class="col-span-2">
            <label for="4m" class="block text-sm font-medium text-gray-700 mb-1">
             Lampiran 4M / 4M Attachment <small>(Opsional)</small>
            </label>
            <input type="file" name="4m" id="4m"
                   class="w-full border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <small>Allowed: PDF, XLSX, Docs. Max total: 5MB/File.</small>
          </div>
        </div>
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
<div class="relative group">
    <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">
        Document Number <small class="text-red-600">*</small>
    </label>
    <input type="text" name="document_number" id="document_number"
           class="w-full px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

    <small id="lastDocNote" class="text-gray-500 text-xs"></small>
</div>
<div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Revisi / Version Number</label>
            <input type="text" name="current_version" id="version" placeholder="Default Version 00 if not filled. . ." value="00"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
 </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen / Document Title<small class="text-red-600"> *</small></label>
            <input type="text" name="title" id="title"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>
          <div class="col-span-2">
            <label for="reason" class="block text-sm font-medium text-gray-700">Alasan Pengajuan / Reason For Submission</label>
            <textarea name="reason" id="reason" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
          </div>
        </div>
         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
  <div class="w-full">
    <label for="reason_before" class="block text-sm font-medium text-gray-700">
      Sebelum Perubahan / Before Changes
    </label>
    <textarea name="reason_before" id="reason_before" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
  </div>

  <div class="w-full">
    <label for="reason_after" class="block text-sm font-medium text-gray-700">
      Setelah Perubahan / After Changes
    </label>
    <textarea name="reason_after" id="reason_after" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
  </div>
</div>
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
          <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-4">
   <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
        Document File <small class="text-red-600">*</small>
    </label>
      <input type="file" name="file" id="file" accept=".xlsx" class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
    <p class="text-xs text-gray-500 mt-1">Allowed: PDF, XLSX, DOCX. Max total: 5MB/File.</p>
  </div>
        </div>
      </div>

     <!-- RIGHT SIDEBAR (1/3) -->
<div class="w-full md:w-1/3 bg-white border border-gray-800 p-4 space-y-4">
 <div class="flex flex-col sm:flex-row items-center sm:items-start p-4 gap-4 border-b border-gray-800">
   <!-- Icon besar -->
  <div class="flex-shrink-0 text-green-600 text-6xl sm:text-4xl">
    <i class="fa fa-copy"></i>
  </div>
  <!-- Teks -->
  <div class="text-center sm:text-left flex-1">
    <h2 class="text-base sm:text-lg font-semibold text-gray-700">
      LEMBAR PERMOHONAN SALINAN
      <span class="sm:block text-sm text-gray-400 italic mt-1">
        APPLICATION FOR COPIES
      </span>
    </h2>
  </div>

 
</div>



  <!-- Labels -->
<div class="flex gap-2 font-semibold text-gray-700 border-b border-gray-300 pb-2 mb-2">
    <span class="flex-1">Department</span>
    <span class="w-20 text-center">Qty</span>
</div>

<!-- Container Departemen -->
<ul id="copies-container" class="divide-y divide-gray-200 border border-gray-300 rounded overflow-hidden">

  <!-- Departemen 1 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-bullhorn w-5 text-indigo-500"></i>
      <span class="flex-1 px-2 py-1">Marketing</span>
      <input type="number" name="copies[0][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[0][department_name]" value="Marketing">
  </li>

  <!-- Departemen 2 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-shopping-cart w-5 text-green-500"></i>
      <span class="flex-1 px-2 py-1">Purchasing</span>
      <input type="number" name="copies[1][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[1][department_name]" value="Purchasing">
  </li>

  <!-- Departemen 3 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-lightbulb w-5 text-yellow-500"></i>
      <span class="flex-1 px-2 py-1">Improvement</span>
      <input type="number" name="copies[2][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[2][department_name]" value="Improvement">
  </li>

  <!-- Departemen 4 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-cogs w-5 text-purple-500"></i>
      <span class="flex-1 px-2 py-1">Engineering New Product</span>
      <input type="number" name="copies[3][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[3][department_name]" value="Engineering New Product">
  </li>

  <!-- Departemen 5 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-industry w-5 text-red-500"></i>
      <span class="flex-1 px-2 py-1">Engineering Production</span>
      <input type="number" name="copies[4][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[4][department_name]" value="Engineering Production">
  </li>

  <!-- Departemen 6 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-truck w-5 text-blue-500"></i>
      <span class="flex-1 px-2 py-1">Produksi</span>
      <input type="number" name="copies[5][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[5][department_name]" value="Produksi">
  </li>

  <!-- Departemen 7 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-check-circle w-5 text-green-700"></i>
      <span class="flex-1 px-2 py-1">Quality</span>
      <input type="number" name="copies[6][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[6][department_name]" value="Quality">
  </li>

  <!-- Departemen 8 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-users-cog w-5 text-teal-500"></i>
      <span class="flex-1 px-2 py-1">HRGA & IT</span>
      <input type="number" name="copies[7][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[7][department_name]" value="HRGA & IT">
  </li>

  <!-- Departemen 9 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-project-diagram w-5 text-orange-500"></i>
      <span class="flex-1 px-2 py-1">PPIC</span>
      <input type="number" name="copies[8][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[8][department_name]" value="PPIC">
  </li>

  <!-- Departemen 10 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-shield-alt w-5 text-gray-600"></i>
      <span class="flex-1 px-2 py-1">HSE</span>
      <input type="number" name="copies[9][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[9][department_name]" value="HSE">
  </li>

  <!-- Departemen 11 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-tools w-5 text-indigo-700"></i>
      <span class="flex-1 px-2 py-1">Maintenance</span>
      <input type="number" name="copies[10][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[10][department_name]" value="Maintenance">
  </li>

  <!-- Departemen 12 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-file-alt w-5 text-purple-700"></i>
      <span class="flex-1 px-2 py-1">Management System</span>
      <input type="number" name="copies[11][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[11][department_name]" value="Management System">
  </li>

  <!-- Departemen 13 -->
  <li class="flex items-center gap-2 p-2 bg-gray-50 even:bg-white hover:bg-gray-100 transition">
      <i class="fas fa-coins w-5 text-yellow-700"></i>
      <span class="flex-1 px-2 py-1">Finance & Accounting</span>
      <input type="number" name="copies[12][qty]" class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-lg" min="0" value="0">
      <input type="hidden" name="copies[12][department_name]" value="Finance & Accounting">
  </li>

</ul>


 <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-4">
  <h3 class="font-semibold mb-2">Download Template Dokumen :</h3>
  <ul class="list-none space-y-1 mb-4">
    <li>
        * Download Template Form: 
        <a href="{{ asset('blank/BLANK FORM.xlsx') }}" download class="text-blue-600 hover:underline">
            BLANK FORM.xlsx
        </a>
    </li>
    <li>
        * Download Template Instruksi Kerja: 
        <a href="{{ asset('blank/BLANK IK.xlsx') }}" download class="text-blue-600 hover:underline">
            BLANK IK.xlsx
        </a>
    </li>
    <li>
        * Download Template SOP: 
        <a href="{{ asset('blank/BLANK SOP.xlsx') }}" download class="text-blue-600 hover:underline">
            BLANK SOP.xlsx
        </a>
    </li>
  </ul>
</div>
  </div>
    </div>
    <!-- DIV TOMBOL DI BAWAH KEDUA FORM -->
<div class="w-full flex justify-start items-center p-4 bg-white border border-gray-800 gap-2">
  <a href="{{ route('mr.doc.index') }}" 
     class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
    ← Back
  </a>
  <button type="submit" id="submitBtn"
     class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
    <i data-feather="save" class="h-4 w-4"></i>
    Submit
  </button>
</div>

  
</form>

<style>
    /* Supaya select2 full width */
.select2-container {
  width: 100% !important;
}

/* Supaya tinggi sama dengan input Tailwind */
.select2-container .select2-selection--single {
  height: 42px !important; /* total tinggi */
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
    const otherRadio = document.getElementById('otherRadio');
  const otherInput = document.getElementById('otherInput');

  // Toggle input text ketika opsi Other dipilih atau tidak
  document.querySelectorAll('input[name="document_type"]').forEach(radio => {
    radio.addEventListener('change', () => {
      if (otherRadio.checked) {
        otherInput.classList.remove('hidden');
      } else {
        otherInput.classList.add('hidden');
      }
    });
  });

    document.addEventListener('DOMContentLoaded', function () {
  $(document).ready(function () {
    $('#select_dept').select2({
      placeholder: "-- Choose Department --",
      width: '100%',
      allowClear: true
    });
  });

    
  });

  $(function() {
  const templates = {
    'Form': {
      file: '/templates/template-form.docx',
      text: 'Download Template Form'
    },
    'Work Instructions': {
      file: '/templates/template-work-instruction.docx',
      text: 'Download Template Work Instructions'
    },
    'Standard': {
      file: '/templates/template-standard.docx',
      text: 'Download Template Standard'
    },
    'SOP': {
      file: '/templates/template-sop.docx',
      text: 'Download Template SOP'
    }
  };

  $('#document_type').on('change', function() {
    const type = $(this).val();
    const link = $('#templateLink');

    if (templates[type]) {
      link.attr('href', templates[type].file)
          .text(templates[type].text)
          .removeClass('hidden');
    } else {
      link.addClass('hidden').attr('href', '#').text('');
    }
  });
});

    
$('#doc-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("mr.doc.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'Document succesfully submitted!');
                setTimeout(() => {
                    // ✅ Redirect ke halaman index
                    window.location.href = '{{ route("mr.doc.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal submit dokumen.');
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

let rowIndex = 1;

document.getElementById('add-copy-row').addEventListener('click', function() {
    const container = document.getElementById('copies-container');

    let options = `@foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach`;

    const newRow = document.createElement('div');
    newRow.classList.add('flex', 'items-center', 'gap-2');
    newRow.setAttribute('data-row-index', rowIndex);
    newRow.innerHTML = `
        <select name="copies[${rowIndex}][department_id]" class="select_dept flex-1 border border-gray-300 rounded px-2 py-1">
            <option value="">-- Select Department --</option>
            ${options}
        </select>
        <input type="number" name="copies[${rowIndex}][qty]" class="w-20 border border-gray-300 rounded px-2 py-1 text-lg text-center" min="0" value="1">
        <button type="button" class="remove-row text-red-500 px-2 py-1 rounded hover:bg-red-600 hover:text-white">×</button>
    `;
    container.appendChild(newRow);

    // Inisialisasi select2 hanya untuk select baru
    $(newRow).find('.select_dept').select2({
        placeholder: "-- Choose Department --",
        width: '100%',
        allowClear: true
    });

    rowIndex++;
});

// remove row
document.getElementById('copies-container').addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-row')){
        e.target.parentElement.remove();
    }
});

$('#document_type').on('change', function() {
    let type = $(this).val();

    if (!type) {
        $('#lastDocNote').text('');
        return;
    }

    $.get("{{ route('mr.doc.lastNumber') }}", { document_type: type }, function(res) {
    if (res.last) {
        $('#lastDocNote').text(
            "Last Document Number: " + res.last.document_number + ""
        );
    } else {
        $('#lastDocNote').text("No document found for this type.");
    }
});

});



</script>

@endpush

@endsection