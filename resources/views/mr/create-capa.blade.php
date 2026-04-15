@extends('layouts.app')

@section('title', 'Create CAPA')
@section('page-title', 'CREATE CAPA')
@section('breadcrumb-item', 'CAPA Management')
@section('breadcrumb-active', 'Create CAPA')
@section('content')
 <div class="flex flex-col md:flex-row gap-2">
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
    <!-- AUDITOR TABLE -->
    <div class="p-5 space-y-4">

        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-gray-800">
                    Auditor
                </h3>
                <p class="text-xs text-gray-500">
                    Assigned audit team
                </p>
            </div>

            <button type="button"
                onclick="addAuditorRow()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5
                       text-xs font-medium rounded-md
                       bg-indigo-600 text-white
                       hover:bg-indigo-700 transition">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Add Auditor
            </button>
        </div>

        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left w-8">#</th>
                        <th class="px-3 py-2 text-left">Auditor Name</th>
                        <th class="px-3 py-2 text-right w-16">Action</th>
                    </tr>
                </thead>
                <tbody id="auditorTable" class="divide-y">

                    <tr class="auditor-row">
                        <td class="px-3 py-2 text-gray-400">1</td>

                        <td class="px-3 py-2">
                            <select id="auditors" name="auditors[]" required
    class="w-full px-3 py-2 text-sm
           border rounded-md
           focus:ring-2 focus:ring-indigo-500
           focus:border-indigo-500">

    <option value="">Select auditor</option>

    @foreach($users as $user)

        <option value="{{ $user->id }}"
            {{ Auth::id() == $user->id ? 'selected' : '' }}>

            {{ $user->name }}

        </option>

    @endforeach

</select>

                        </td>

                        <td class="px-3 py-2 text-center">
                            <button type="button"
                                onclick="removeRow(this)"
                                class="text-xs text-red-600 hover:text-red-700">
                                  <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- AUDITEE -->
    <div class="px-5 py-4 border-t border-gray-200 bg-white space-y-2">

        <h3 class="text-sm font-medium text-gray-800">
            Auditee
        </h3>
        <p class="text-xs text-gray-500">
            Department involved in the audit
        </p>

        <div id="auditee-list" class="mt-3 space-y-1 text-sm text-gray-600">
            <div class="flex items-center gap-2 text-gray-400 italic">
                <i class="fa-regular fa-circle text-[6px]"></i>
                Choose department first
            </div>
        </div>

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
            Create New CAPA
        </h1>

        <p class="flex items-center gap-2
                  text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
            Corrective & Preventive Action Record
        </p>
    </div>

    <!-- Status Badge -->
    <div class="flex sm:items-center">
        <span class="inline-flex items-center justify-center gap-1.5
            px-3 py-1 text-sm font-semibold rounded-full
            bg-gray-50 text-gray-700 border border-gray-200
            w-fit sm:w-28">
            <i class="fa-regular fa-pen-to-square text-xs text-gray-500"></i>
            Draft
        </span>
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

     <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Category <span class="text-red-600">*</span>
        </label>
   <div class="flex flex-wrap gap-2">
    <!-- CRITICAL -->
    <input type="radio" name="category" id="cat_critical" value="Critical" checked class="peer/critical hidden">
    <label for="cat_critical"
        class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
               text-gray-700 hover:bg-gray-50
               peer-checked/critical:bg-red-50
               peer-checked/critical:border-red-300
               peer-checked/critical:text-red-700">
        Critical
    </label>

    <!-- MAJOR -->
    <input type="radio" name="category" id="cat_major" value="Major" class="peer/major hidden">
    <label for="cat_major"
        class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
               text-gray-700 hover:bg-gray-50
               peer-checked/major:bg-yellow-50
               peer-checked/major:border-yellow-300
               peer-checked/major:text-yellow-700">
        Major
    </label>

    <!-- MINOR -->
    <input type="radio" name="category" id="cat_minor" value="Minor" class="peer/minor hidden">
    <label for="cat_minor"
        class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
               text-gray-700 hover:bg-gray-50
               peer-checked/minor:bg-blue-50
               peer-checked/minor:border-blue-300
               peer-checked/minor:text-blue-700">
        Minor
    </label>

    <!-- OBSERVATION -->
    <input type="radio" name="category" id="cat_observation" value="Observation" class="peer/observation hidden">
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


   <!-- Department & Representative -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- LEFT: Department -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Department <span class="text-red-600">*</span>
        </label>
        <select name="dept_id" id="department" required
            class="w-full px-3 py-2.5 border rounded-lg text-sm shadow-sm
            focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Select department</option>
            <option value="2">HRGAIT</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>


  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Dept. Representative <span class="text-red-600">*</span>
    </label>

    <select name="dept_representative" id="representative"
        class="w-full px-3 py-2.5 border rounded-lg text-sm shadow-sm
        focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option></option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Dept. Representative 2
    </label>

    <select name="dept_representative_2" id="representative2"
        class="w-full px-3 py-2.5 border rounded-lg text-sm shadow-sm
        focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option></option>
    </select>
</div>
    </div>

    <!-- Finding -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Detail of Information <span class="text-red-600">*</span>
        </label>
        <input type="text" name="detail_of_information" required
            class="w-full px-3 py-2.5 border rounded-lg shadow-sm
            focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Problem Statement <span class="text-red-600">*</span>
        </label>
        <textarea name="Problem" rows="5"
            class="w-full px-3 py-2.5 border rounded-lg shadow-sm resize-none
            focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
    </div>

</section>

 <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

         <button type="submit" id="submitBtn"
        class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
        <i class="fa-solid fa-floppy-disk"></i>
        Save
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

$(document).ready(function () {

 $('#department').select2({
        width: '100%',
        placeholder: '-- Select Department --',
        allowClear: true
    });

    $('#representative').select2({
        width: '100%',
        placeholder: '-- Select Dept. Representative --',
        allowClear: true
    });

      $('#representative2').select2({
        width: '100%',
        placeholder: '-- Select Dept. Representative 2 --',
        allowClear: true
    });

     $('#auditors').select2({
        width: '100%',
        placeholder: '-- Select Auditor --',
        allowClear: true
    });

     $('.select2-auditor').select2({
        width: '100%',
        placeholder: '-- Select Auditor --',
        allowClear: true
    });

});

$('#capa-form').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $submitBtn = $('#submitBtn');

    // 🔒 Disable tombol submit untuk mencegah double click
    $submitBtn.prop('disabled', true).text('Saving...');

    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("mr.capa.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'CAPA successfully saved as Draft!');

                // redirect setelah 2 detik
                setTimeout(() => {
                    window.location.href = '{{ route("mr.capa.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal menyimpan CAPA.');
                $submitBtn.prop('disabled', false).text('Save'); // aktifkan lagi
            }
        },
        error: function (err) {
            console.error(err.responseText);
            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
            showToast('error', msg);
            $submitBtn.prop('disabled', false).text('Save'); // aktifkan lagi
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

$('#department').on('change', function () {
    let deptId = $(this).val();
    console.log('DEPT CHANGED:', deptId);

    let $rep1 = $('#representative');
    let $rep2 = $('#representative2');
    let $auditeeList = $('#auditee-list');

    // reset (JANGAN destroy select2)
    $rep1.empty().append('<option></option>');
    $rep2.empty().append('<option></option>');
    $auditeeList.html('<p class="text-gray-400">Loading...</p>');

    if (!deptId) {
        $auditeeList.html('<p class="text-gray-400">Choose department first...</p>');
        
        $rep1.trigger('change');
        $rep2.trigger('change');
        return;
    }

    $.ajax({
        url: `/mr/capa/${deptId}/users`,
        type: 'GET',
        dataType: 'json',
        success: function (users) {

            console.log('USERS RESPONSE:', users);

            $rep1.empty().append('<option></option>');
            $rep2.empty().append('<option></option>');

            if (!users || users.length === 0) {
                $rep1.append('<option disabled>No staff found</option>');
                $rep2.append('<option disabled>No staff found</option>');
                $auditeeList.html('<p class="text-red-500">No users found in this department.</p>');

                $rep1.trigger('change');
                $rep2.trigger('change');
                return;
            }

            // isi dropdown
            $.each(users, function (i, user) {
                $rep1.append(`<option value="${user.id}">${user.name}</option>`);
                $rep2.append(`<option value="${user.id}">${user.name}</option>`);
            });

            // 🔥 INI PENTING: refresh select2 TANPA destroy
            $rep1.trigger('change');
            $rep2.trigger('change');

            // debug
            $rep1.off('change').on('change', function () {
                console.log('REP1 SELECTED:', $(this).val());
            });

            setTimeout(() => {
                console.log('FINAL REP1 VALUE:', $rep1.val());
            }, 500);

            // auditee list
            let html = '';
            $.each(users, function (i, user) {
                html += `
                <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="inline-flex items-center justify-center
                                     w-7 h-7 text-xs font-semibold
                                     rounded-full
                                     bg-gray-100 text-gray-700">
                            ${i + 1}
                        </span>
                        <span class="text-sm text-gray-800 truncate">
                            ${user.name}
                        </span>
                    </div>
                </div>
                `;
            });

            $auditeeList.html(html);
        }
    });
});


// init select2
function initSelect2() {
    $('#representative').select2({
        width: '100%',
        placeholder: '-- Select Dept. Representative --',
        allowClear: true
    });

    $('#representative2').select2({
        width: '100%',
        placeholder: '-- Select Dept. Representative 2 --',
        allowClear: true
    });
}

function addAuditorRow() {
    const tableBody = document.getElementById('auditorTable');
    const rowCount = tableBody.querySelectorAll('.auditor-row').length + 1;

    const row = document.createElement('tr');
    row.classList.add('auditor-row');

    row.innerHTML = `
        <td class="px-3 py-2 text-gray-400">${rowCount}</td>

        <td class="px-3 py-2">
            <select name="auditors[]" required
                class="select2-auditor w-full px-3 py-2 text-sm
                       border rounded-md
                       focus:ring-2 focus:ring-indigo-500
                       focus:border-indigo-500">
                <option value="">Select auditor</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </td>

        <td class="px-3 py-2 text-center">
            <button type="button"
                onclick="removeRow(this)"
                class="text-xs text-red-600 hover:text-red-700">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(row);

    // 🔒 INIT SELECT2 HANYA UNTUK SELECT BARU
    const $newSelect = $(row).find('.select2-auditor');
    $newSelect.select2({
        width: '100%',
        placeholder: '-- Select Auditor --',
        allowClear: true
    });
}


function removeRow(button) {
    const row = button.closest('tr');
    const tableBody = document.getElementById('auditorTable');

    // Prevent removing last row
    if (tableBody.querySelectorAll('.auditor-row').length === 1) {
        alert('At least one auditor is required.');
        return;
    }

    row.remove();
    reindexAuditor();
}

function reindexAuditor() {
    document.querySelectorAll('#auditorTable .auditor-row').forEach((row, index) => {
        row.querySelector('td').textContent = index + 1;
    });
}

</script>

@endpush

@endsection