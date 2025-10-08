@extends('layouts.app')

@section('title', 'Revision Document')
@section('page-title', 'REVISION DOCUMENT')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Revision Document')
@section('content')

 <form id="doc-form" enctype="multipart/form-data">
  @csrf

  <!-- DIV UTAMA CONTAINER -->
  <div class="bg-white shadow-md rounded-lg p-4 space-y-4">
    
    <!-- Flex container kiri + kanan -->
    <div class="flex flex-col md:flex-row gap-4">
      <!-- LEFT CONTENT (2/3) -->
      <div class="w-full md:w-2/3 bg-white rounded-xl p-4 space-y-4">
        <h2 class="text-lg font-semibold text-gray-700">Revision Document</h2>
        <!-- INPUTS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
           <input type="text" name="document_type" id="document_type" value="{{ $doc->document_type }}"
           class="w-full bg-gray-200 px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" readonly />
            </div>

<div class="relative group">
    <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">
        Document Number
    </label>
    <input type="text" name="document_number" id="document_number" value="{{ $doc->document_number }}"
           class="w-full bg-gray-200 px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" readonly />

    <small id="lastDocNote" class="text-gray-500 text-xs"></small>
</div>
<div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Version Number</label>
            <input type="number" name="version" id="version"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                   <small class="text-gray-500 text-xs">Last Version: {{ $doc->current_version ?? 0 }}</small>
          </div>
          
           <div>
            <label for="4m" class="block text-sm font-medium text-gray-700 mb-1">
              4M Attachment <small>(If Needed)</small>
            </label>
            <input type="file" name="4m" id="4m"
                   class="w-full border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <small>Allowed: PDF, XLSX, Docs. Max total: 5MB/File.</small>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
            <input type="text" name="title" id="title" value="{{ $doc->title }}"
                   class="w-full px-3 py-2 border border-gray-300 bg-gray-200 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" readonly />
          </div>
          <div class="col-span-2">
            <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Revision</label>
            <textarea name="reason_revision" id="reason_revision" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
          </div>
          <div class="col-span-2 mb-4">
            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
              Document File
            </label>
            <input type="file" name="file" id="file"
                   class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <p class="text-xs text-gray-500 mt-1">Allowed: PDF, XLSX, Docs. Max total: 5MB/File.</p>
          </div>
        </div>
      </div>

     <!-- RIGHT SIDEBAR (1/3) -->
<div class="w-full md:w-1/3 bg-white rounded-xl p-4 space-y-4">
    <h2 class="text-lg font-semibold text-gray-700 text-left">Application for Copies</h2>

    <!-- Labels -->
    <div class="flex gap-2 font-semibold text-gray-700">
        <span class="flex-1">Department</span>
        <span class="w-20 text-center">Qty</span>
        <span class="w-8"></span> <!-- kosong untuk tombol remove -->
    </div>

    <div id="copies-container" class="space-y-2">
        <!-- row template -->
        <div class="flex items-center gap-2" data-row-index="0">
            <select id="select_dept" name="copies[0][department_id]" class="flex-1 border border-gray-300 rounded px-2 py-1">
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            <input type="number" name="copies[0][qty]" class="w-20 border border-gray-300 rounded px-2 py-1 text-center text-lg" min="0" value="0">
            <button type="button" class="remove-row text-red-500 px-2 py-1 rounded hover:bg-red-600 hover:text-white">×</button>
        </div>
    </div>

    <button type="button" id="add-copy-row" class="mt-2 w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        + Add Row
    </button>
</div>

    </div>
<hr>
    <!-- TOMBOL di bawah kiri + kanan (MASIH DI DALAM CONTAINER UTAMA) -->
    <div class="w-full flex justify-start items-center gap-2 mt-4">
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
    document.addEventListener('DOMContentLoaded', function () {
  $(document).ready(function () {
    $('#select_dept').select2({
      placeholder: "-- Choose Department --",
      width: '100%',
      allowClear: true
    });
  });
  });
// Submit form via AJAX
    $('#doc-form').on('submit', function (e) {
        e.preventDefault();
        let submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true);

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('mr.doc.revision.update', $doc->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                submitBtn.prop('disabled', false);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Revision Submitted!',
                        text: 'Document status updated to Revision.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('mr.doc.show', $doc->id) }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message || 'Please check your input.',
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                submitBtn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                });
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
        <input type="number" name="copies[${rowIndex}][qty]" class="w-20 border border-gray-300 rounded px-2 py-1 text-lg text-center" min="0" value="0">
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
            $('#lastDocNote').text("Last Document Number: " + res.last);
        } else {
            $('#lastDocNote').text("No document found for this type.");
        }
    });
});

</script>

@endpush

@endsection