@extends('layouts.app')

@section('title', 'Create Position')
@section('page-title', 'CREATE POSITION')
@section('breadcrumb-item', 'Job Position')
@section('breadcrumb-active', 'Create Position')
@section('content')
<form id="doc-form" enctype="multipart/form-data">
  @csrf
  <!-- DIV UTAMA CONTAINER -->
  <div class="bg-white shadow-md rounded-lg p-4 space-y-4">
    
    <!-- Flex container kiri + kanan -->
   
      <!-- LEFT CONTENT (2/3) -->
      <div class="w-full md:w-full bg-white rounded-xl p-4 space-y-4">
        <h2 class="text-lg font-semibold text-gray-700">Create New Position</h2>
        <!-- INPUTS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department<small class="text-red-600">*</small></label>
           <select name="document_type" id="document_type" required
        class="w-full px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
  <option value="">-- Choose Department --</option>
  <option value="Form">Form</option>
  <option value="Work Instructions">Work Instructions</option>
  <option value="Standard">Standard</option>
  <option value="SOP">SOP</option>
</select>
            </div>

              <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Placement<small class="text-red-600">*</small></label>
           <select name="document_type" id="document_type" required
        class="w-full px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
  <option value="">-- Choose Type --</option>
  <option value="Office">Office</option>
  <option value="Operator">Operator</option>
</select>
            </div>
        </div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
    <div class="col-span-2">
    <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">
        Position Name <small class="text-red-600">*</small>
    </label>
    <input type="text" name="document_number" id="document_number"
           class="w-full px-3 py-2 border border-gray-300 text-sm p-2 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />

</div>

</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
    <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Level/Grade</label>
            <input type="text" name="current_version" id="version" placeholder="Default Version 00 if not filled. . ." value="00"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Standard Salary</label>
            <input type="text" name="current_version" id="version" placeholder="Default Version 00 if not filled. . ." value="00"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
            <input type="text" name="current_version" id="version" placeholder="Default Version 00 if not filled. . ." value="00"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Experience</label>
            <input type="text" name="current_version" id="version" placeholder="Default Version 00 if not filled. . ." value="00"
                   class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
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
    $(function() {
    let qualificationCount = 0;

    function updateIndexes() {
        $('#qualifications-container .index').each(function(i) {
            $(this).text(i + 1);
        });
    }

    function createValueInput(objectType) {
        if(objectType === 'education') {
            const select = $('<select class="border border-gray-300 rounded px-2 py-1 w-full"></select>');
            ['SD','SMP','SMA/SMK','D3','S1','S2','S3'].forEach(level => {
                select.append(`<option value="${level}">${level}</option>`);
            });
            return select;
        } else if(objectType === 'age') {
            const input = $('<input type="range" min="18" max="65" value="25" class="w-full">');
            const output = $('<span>25</span>');
            input.on('input', function() {
                output.text($(this).val());
            });
            const container = $('<div class="flex items-center space-x-2"></div>');
            container.append(input).append(output);
            return container;
        } else if(objectType === 'experience') {
            return $('<input type="number" min="0" class="border border-gray-300 rounded px-2 py-1 w-full">');
        } else if(objectType === 'skill') {
            return $('<input type="text" class="border border-gray-300 rounded px-2 py-1 w-full">');
        }
        return $('<span></span>');
    }

    $('#add-qualification').click(function() {
        qualificationCount++;
        const $clone = $('#qualification-template').clone().removeAttr('id').removeClass('hidden');

        $('#qualifications-container').append($clone);
        updateIndexes();

        const $selectObject = $clone.find('.object');
        const $valueContainer = $clone.find('.value-container');

        $selectObject.change(function() {
            $valueContainer.empty();
            const input = createValueInput($(this).val());
            $valueContainer.append(input);
        });

        $clone.find('.remove-qualification').click(function() {
            $clone.remove();
            updateIndexes();
        });
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


$(function() {
    function updateIndexes() {
        $('#jobdesc-container .index').each(function(i) {
            $(this).text(i + 1);
        });
    }

    $('#add-jobdesc').click(function() {
        const $clone = $('#jobdesc-template').clone().removeAttr('id').removeClass('hidden');
        $('#jobdesc-container').append($clone);
        updateIndexes();

        $clone.find('.remove-jobdesc').click(function() {
            $clone.remove();
            updateIndexes();
        });
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



</script>

@endpush

@endsection