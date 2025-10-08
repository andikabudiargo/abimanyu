@extends('layouts.app')

@section('title', 'Register Asset')
@section('page-title', 'Register Asset')
@section('breadcrumb-item', 'Asset Management')
@section('breadcrumb-active', 'Register Asset')

@section('content')

<div class="w-full bg-white shadow-md rounded-xl p-8 space-y-8">
  <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2 border-b pb-3">
    <i data-feather="box" class="w-5 h-5 relative top-[1px]"></i>
    Register New Assets
  </h2>

  <form id="asset-form" action="" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf

    <!-- Container utama -->
<div class="bg-white space-y-4">

  <!-- Grid utama: Foto + Form -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Upload Foto -->
    <div class="w-full">
      <label class="block text-sm font-medium text-gray-700 mb-2">Asset Image</label>

      <!-- Dropzone -->
      <div id="dropzone"
        class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center text-gray-500 cursor-pointer bg-gray-50 hover:bg-gray-100 transition relative aspect-square">
        
        <i data-feather="upload-cloud" class="w-10 h-10 mb-3"></i>
        <p class="text-sm">Choose a file or drag & drop it here</p>
        <p class="text-xs text-gray-400">JPEG, PNG formats, up to 50MB</p>
        
        <button type="button"
          class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded shadow hover:bg-indigo-700">
          Browse File
        </button>
        
        <input type="file" id="photo" name="photo" accept="image/*"
          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
      </div>

      <!-- Preview -->
      <div id="previewContainer" class="relative mt-4 hidden">
        <img id="preview" src="" alt="Preview"
          class="w-48 h-48 object-cover rounded-lg shadow border hidden">
        
        <!-- Tombol Reset -->
        <button type="button" id="resetPhoto" 
  class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 z-10">
  <i data-feather="x" class="w-4 h-4"></i>
</button>

      </div>
    </div>

   <!-- Form Detail & Info Tambahan -->
<!-- Form Detail & Info Tambahan -->
<div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

 <div>
    <label for="asset_number" class="block text-sm font-medium text-gray-700 mb-1">
      Asset Number <small class="text-red-600">*</small>
    </label>
    <input type="text" name="asset_number" id="asset_number" placeholder="Asset Number from Accounting..."
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400" />
  </div>

  <!-- Asset Name -->
  <div>
    <label for="asset_name" class="block text-sm font-medium text-gray-700 mb-1">
      Asset Name <small class="text-red-600">*</small>
    </label>
    <input type="text" name="asset_name" id="asset_name"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400" />
  </div>

  <!-- Asset Type -->
  <div>
    <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-1">
      Asset Type <small class="text-red-600">*</small>
    </label>
    <select name="asset_type" id="asset_type"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Asset Type --</option>
      <option value="Laptop">Laptop / Notebook</option>
      <option value="Desktop">Desktop / PC</option>
      <option value="Server">Server</option>
      <option value="Printer">Printer</option>
      <option value="Scanner">Scanner</option>
      <option value="Monitor">Monitor</option>
      <option value="Network">Network Device</option>
      <option value="Storage">Storage / NAS</option>
      <option value="UPS">UPS</option>
      <option value="Smartphone">Smartphone</option>
      <option value="Tablet">Tablet</option>
      <option value="Software">Software / License</option>
      <option value="Peripheral">Peripheral</option>
    </select>
  </div>

  <!-- Serial Number -->
   <div>
    <label for="acquistion_type" class="block text-sm font-medium text-gray-700 mb-1">
      Owned Category <small class="text-red-600">*</small>
    </label>
    <select name="acquistion_type" id="acquistion_type"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Owned Category --</option>
      <option value="Purchased">Purchased New</option>
      <option value="Purchased">Purchased Secondhand</option>
      <option value="Rented">Rented from Supplier</option>
      <option value="Loaned">Loaned from Vendor</option>
      <option value="Other">Other</option>
    </select>
  </div>

  <!-- Supplier -->
  <div>
    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">
      Supplier
    </label>
    <select name="supplier_id" id="supplier_id"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Supplier --</option>
    </select>
  </div>

  <!-- Purchase Date -->
  <div>
    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">
      Purchase Date
    </label>
    <input type="date" name="purchase_date" id="purchase_date"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400" />
  </div>

  <!-- Warranty -->
  <div>
    <label for="warranty" class="block text-sm font-medium text-gray-700 mb-1">
      Warranty (months)
    </label>
    <input type="number" name="warranty" id="warranty" min="0"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400" />
  </div>

  <!-- Assignment Type -->
  <div>
    <label for="assignment_type" class="block text-sm font-medium text-gray-700 mb-1">
      Assignment Type <small class="text-red-600">*</small>
    </label>
    <select name="assignment_type" id="assignment_type"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Assignment Type --</option>
      <option value="Personal">Personal Use</option>
      <option value="Department">Department Use</option>
      <option value="Shared">Shared Use</option>
      <option value="Spare">Spare</option>
    </select>
  </div>

  <!-- Assigned To (hidden by default) -->
  <div id="assignedToGroup" class="hidden">
    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
      Assigned To
    </label>
    <input type="text" id="assigned_to" name="assigned_to"
      class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2" />
  </div>

  <!-- Location (hidden by default) -->
  <div id="locationGroup" class="hidden">
    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
      Location
    </label>
    <select name="location" id="location"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Location --</option>
      <option value="Ruang General Affair">Ruang General Affair</option>
      <option value="Ruang HR">Ruang HR</option>
      <option value="Ruang Server">Ruang Server</option>
      <option value="Pantry">Pantry</option>
      <option value="Lobby Lt.1">Lobby Lt.1</option>
      <option value="Lobby Lt.2">Lobby Lt.2</option>
      <option value="Resepsionis">Resepsionis</option>
      <option value="Ruang Office LT.1">Ruang Office LT.1</option>
      <option value="Ruang Office LT.2">Ruang Office LT.2</option>
      <option value="Ruang Bima">Ruang Bima</option>
      <option value="Ruang Arjuna">Ruang Arjuna</option>
    </select>
  </div>

  <!-- Condition -->
  <div>
    <label for="conditions" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
    <select name="conditions" id="conditions"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
      <option value="">-- Choose Condition --</option>
      <option value="Good">Good</option>
      <option value="Broken but still usable">Broken but still usable</option>
      <option value="Damaged and cannot be used">Damaged and can't be used</option>
    </select>
  </div>

</div>

<!-- Note (full width, bawah grid) -->
<div class="lg:col-span-3 mt-4">
  <label for="note" class="block text-sm font-medium text-gray-700 mb-1">
    Note
  </label>
  <textarea name="note" id="note" rows="3"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400"></textarea>
</div>


  </div>

  <!-- Tombol Submit -->
  <div class="flex justify-end items-center border-t pt-5 gap-3">
    <button type="button" id="resetBtn"
      class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
      ← Back
    </button>
    <button type="submit" id="submitBtn"
      class="px-5 py-2 flex items-center justify-center gap-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
      <i data-feather="save" class="h-4 w-4"></i> Save
    </button>
  </div>
</div>


<style>
    input::placeholder {
  font-size: 10px;
  color: #9ca3af; /* Tailwind gray-400 */
}

/* Select2 full width */
.select2-container {
  width: 100% !important;
}

/* Tinggi sama dengan input Tailwind */
.select2-container .select2-selection--single {
  height: 42px; /* sesuaikan dengan py-2 px-3 input */
  border: 1px solid #d1d5db; /* border-gray-300 */
  border-radius: 0.5rem; /* rounded-lg */
  padding: 5px 10px;
  display: flex;
  align-items: center;
}

/* Hilangkan border biru default select2 */
.select2-container--default .select2-selection--single:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgb(99 102 241 / 0.5); /* ring-indigo-400 */
}

/* Panah select2 */
.select2-container--default .select2-selection__arrow {
  height: 100%;
  right: 10px;
}

</style>
{{-- Scripts --}}
<script>
$(document).ready(function() {

    const assignedToGroup = document.getElementById('assignedToGroup');
  const locationGroup = document.getElementById('locationGroup');
  const $photoInput = $("#photo");
  const $dropzone = $("#dropzone");

 function showPreview(file) {
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
   reader.onload = function (e) {
  $dropzone.find("i, p, button, #previewImage, #resetPhoto").remove();

  $dropzone.prepend(`
    <img src="${e.target.result}" 
         alt="Preview" 
         class="w-full h-full object-cover rounded-lg" id="previewImage">
    <button type="button" id="resetPhoto" 
      class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 z-10">
      <i data-feather="x" class="w-4 h-4"></i>
    </button>
  `);
$dropzone.find("svg.feather").remove(); // bersihkan duplikat svg lama
  feather.replace($dropzone.get(0)); // <— penting!
    };
    reader.readAsDataURL(file);
  }
}

  // Event pilih file
  $(document).on("change", "#photo", function () {
    const file = this.files[0];
    showPreview(file);
  });
$(document).on("click", "#resetPhoto", function () {
  $("#previewImage").remove();
  $(this).remove();
  $dropzone.prepend(`
    <i data-feather="upload-cloud" class="w-10 h-10 mb-3"></i>
    <p class="text-sm">Choose a file or drag & drop it here</p>
    <p class="text-xs text-gray-400">JPEG, PNG formats, up to 50MB</p>
    <button type="button"
      class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded shadow hover:bg-indigo-700">
      Browse File
    </button>
  `);

  // reset input file agar bisa pilih ulang
  $("#photo").val("");
  $dropzone.find("svg.feather").remove(); // bersihkan duplikat svg lama
  feather.replace($dropzone.get(0));
});


  // Drag & Drop
  $dropzone.on("dragover", function (e) {
    e.preventDefault();
    $(this).addClass("border-indigo-500 bg-indigo-50");
  });

  $dropzone.on("dragleave", function () {
    $(this).removeClass("border-indigo-500 bg-indigo-50");
  });

  $dropzone.on("drop", function (e) {
    e.preventDefault();
    $(this).removeClass("border-indigo-500 bg-indigo-50");
    const file = e.originalEvent.dataTransfer.files[0];
    showPreview(file);
  });

  $('#assignment_type').on('change', function () {
    const val = $(this).val(); // ambil value dari select2

    if (val === 'Personal') {
      assignedToGroup.classList.remove('hidden');
      locationGroup.classList.add('hidden');
    } else if (val) {
      assignedToGroup.classList.add('hidden');
      locationGroup.classList.remove('hidden');
    } else {
      assignedToGroup.classList.add('hidden');
      locationGroup.classList.add('hidden');
    }
  });

    // Assignment Type
  $('#asset_type').select2({
    placeholder: "-- Choose Asset Type --",
    allowClear: true,
    width: '100%'
  });
 // Assignment Type
  $('#assignment_type').select2({
    placeholder: "-- Choose Assignment Type --",
    allowClear: true,
    width: '100%'
  });

  // Location
  $('#location').select2({
    placeholder: "-- Choose Location --",
    allowClear: true,
    width: '100%'
  });

  // Condition
  $('#condition').select2({
    placeholder: "-- Choose Condition --",
    allowClear: true,
    width: '100%'
  });
// === Supplier Select ===
    $('#supplier_id').select2({
        placeholder: "-- Choose Supplier --",
        allowClear: true,
        ajax: {
            url: "{{ route('purchasing.supplier.select') }}", // route untuk ambil data supplier
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: `${item.code} - ${item.name}`
                        }
                    })
                };
            },
            cache: true
        }
    });

    $('#assigned_to').select2({
        placeholder: "-- Assigned to --",
        allowClear: true,
        ajax: {
            url: "{{ route('setting.user.select') }}", // route untuk ambil data supplier
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    })
                };
            },
            cache: true
        }
    });
    

  $("#asset-form").on("submit", function (e) {
  e.preventDefault();
 console.log($('#photo')[0].files); // harus ada file jika user pilih
  let formData = new FormData(this);

  $.ajax({
    url: "{{ route('it.assets.store') }}",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "info",
        title: "Uploading...",
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: (toast) => {
          Swal.showLoading();
        }
      });
    },
    success: function (res) {
      if (res.success) {
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "success",
          title: res.message,
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });

        setTimeout(() => {
          window.location.href = "{{ route('it.assets.index') }}";
        }, 2000);
      }
    },
    error: function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.errors) {
        let errors = xhr.responseJSON.errors;
        let msg = Object.values(errors).map(e => e.join(", ")).join("<br>");

        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "error",
          title: "Validation Error",
          html: msg,
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true
        });
      } else {
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "error",
          title: "Oops...",
          text: "Something went wrong!",
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      }
    }
  });
});


});
</script>

@endsection

