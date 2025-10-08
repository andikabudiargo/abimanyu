@extends('layouts.app')

@section('title', 'Chemical Traceability')
@section('page-title', 'Chemical Traceability')
@section('breadcrumb-item', 'Supporting Tools')
@section('breadcrumb-active', 'Chemical Traceability')

@section('content')

<div class="space-y-6">

  <!-- Header -->
  <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow rounded-xl p-6">
    <h2 class="text-2xl font-bold text-white tracking-wide">Chemical Traceability</h2>
    <p class="text-indigo-100 text-sm">Check Finish Good use Chemical</p>
  </div>


    <div class="bg-white shadow-md rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Import Data Excel (BOM)
        </h2>
<div id="upload-message" class="mt-4 mb-4"></div>
        <form id="excel-upload-form" action="" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-600 mb-2">Upload Excel File</label>
        <input type="file" name="file" id="excel-file" class="w-full border rounded-lg p-2 @error('file') border-red-500 @enderror" required>
        <p class="text-xs text-gray-500 mt-1">
            Format: Excel (.xlsx)
        </p>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Import
        </button>
    </div>
</form>
    </div>

 <!-- Parent Wrapper -->
<div class="bg-white p-6 rounded-xl space-y-6">

<div class="bg-white shadow-md border-l-4 border-indigo-500 rounded-xl p-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <!-- Select Chemical (label + select dalam flex-1) -->
    <div class="flex-1">
        <label for="cmSelect" class="block text-sm font-medium text-gray-600 mb-2">
            Select Chemical
        </label>
        <select id="cmSelect"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition h-10 px-3">
            <option value="">-- Choose Chemical --</option>
        </select>
    </div>

    <!-- Export Button -->
    <div class="flex-shrink-0">
        <button id="export-button"
            class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 h-10 flex items-center justify-center">
            Export Excel
        </button>
    </div>
</div>


<div id="fg_table" class="mt-8">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Finish Good List</h3>

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
        <table id="fg_table_inner" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-indigo-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">FG Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">FG Name</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- Data akan diisi melalui AJAX --}}
            </tbody>
        </table>
    </div>
</div>


</div>
</div>
<style>
      /* Zebra stripe */
    #fg_table_inner tbody tr:nth-child(odd) {
        @apply bg-gray-50;
    }
    #fg_table_inner tbody tr:nth-child(even) {
        @apply bg-white;
    }

    /* Hover effect */
    #fg_table_inner tbody tr:hover {
        @apply bg-indigo-100;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {

    $('#excel-upload-form').on('submit', function(e) {
        e.preventDefault(); // mencegah reload halaman

        var formData = new FormData(this);

        $.ajax({
            url: '/fa/excel/upload', // endpoint controller
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#upload-message').html('<span class="text-blue-600">Uploading...</span>');
            },
            success: function(response) {
                if(response.status === 'success'){
                    $('#upload-message').html('<span class="text-green-600">' + response.message + '</span>');

                    // panggil fungsi untuk load CM ke dropdown setelah upload
                    loadCM();
                } else {
                    $('#upload-message').html('<span class="text-red-600">' + response.message + '</span>');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#upload-message').html('<span class="text-red-600">Terjadi kesalahan saat upload.</span>');
            }
        });
    });

  // Inisialisasi Select2
    $('#cmSelect').select2({
        placeholder: "-- Choose Chemical --",
        width: '100%'
    });

    // load data dari cache
    loadCM();

    function loadCM() {
        $.ajax({
            url: '/fa/excel/cm', // endpoint controller mengembalikan {code, name}
            type: 'GET',
            success: function(data) {
                var cmSelect = $('#cmSelect');
                cmSelect.empty(); // kosongkan dulu
                cmSelect.append('<option></option>'); // placeholder untuk Select2

                var seen = {};

                $.each(data, function(index, item){
                    if(item.code && item.name){
                        var key = item.code + '|' + item.name;
                        if(!seen[key]){
                            seen[key] = true;
                            var newOption = new Option(item.code + ' - ' + item.name, item.code, false, false);
                            cmSelect.append(newOption);
                        }
                    }
                });

                // refresh Select2 setelah append option
                cmSelect.trigger('change');
            },
            error: function(err){
                console.error(err);
            }
        });
    }

     // ketika CM / chemical dipilih
    $('#cmSelect').on('change', function() {
        var cmCode = $(this).val();

        var tbody = $('#fg_table_inner tbody');
        tbody.empty(); // kosongkan tabel dulu

        if(!cmCode){
            return; // jika tidak ada yang dipilih, jangan tampilkan apapun
        }

        // ambil FG dari controller
        $.ajax({
            url: '/fa/excel/fg', // endpoint harus menerima ?cm=CM01
            type: 'GET',
            data: { cm: cmCode },
            success: function(data) {
                if(!data.length){
                    tbody.append('<tr><td colspan="3" class="text-center py-2">Tidak ada FG untuk chemical ini</td></tr>');
                    return;
                }

                // hapus duplikat FG (kode + nama)
                var seen = {};
                var no = 1;

              data.forEach(function(item){
    var key = item.code + '|' + item.name;
    if(!seen[key]){
        seen[key] = true;

        // tentukan warna baris ganjil/genap
        var rowBg = (no % 2 === 1) ? 'bg-gray-50' : 'bg-white';

        var row = '<tr class="'+rowBg+' hover:bg-indigo-100 transition-colors duration-200">'+
            '<td class="px-4 py-2">'+ no +'</td>'+
            '<td class="px-4 py-2">'+ item.code +'</td>'+
            '<td class="px-4 py-2">'+ item.name +'</td>'+
            '</tr>';
        tbody.append(row);
        no++;
    }
});

            },
            error: function(err){
                console.error(err);
            }
        });

    });

     $('#export-button').on('click', function() {
        window.location.href = '/fa/excel/export-cm-fg';
    });

    // Optional: load CM saat halaman ready jika sudah ada data di cache
    loadCM();
});



</script>
@endpush
@endsection
