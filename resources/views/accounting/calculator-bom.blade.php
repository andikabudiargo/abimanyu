@extends('layouts.app')

@section('title', 'BOM Calculator')
@section('page-title', 'BOM CALCULATOR')
@section('breadcrumb-item', 'Production')
@section('breadcrumb-active', 'BOM Calculator')

@section('content')

<div class="space-y-6">

  <!-- Header -->
  <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow rounded-xl p-6">
    <h2 class="text-2xl font-bold text-white tracking-wide">BOM Calculator</h2>
    <p class="text-indigo-100 text-sm">Bill of Material Estimation</p>
  </div>


    <div class="bg-white shadow-md rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Import Data Excel (BOM, SJ, LPB)
        </h2>
<div id="upload-message" class="mt-4 mb-4"></div>
        <form id="excel-upload-form" action="{{ route('fa.cabom.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-600 mb-2">Upload Excel File</label>
        <input type="file" name="file" id="excel-file" class="w-full border rounded-lg p-2 @error('file') border-red-500 @enderror" required>
        <p class="text-xs text-gray-500 mt-1">
            Format: Excel (.xlsx)
        </p>
    </div>

    <div class="flex justify-end">
        <button ID="excel-upload-btn" type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Import
        </button>
    </div>
</form>
    </div>

 <!-- Parent Wrapper -->
<div class="bg-white p-6 rounded-xl space-y-6">

  <!-- Navigation Tabs -->
  <div class="flex border-b border-gray-200 mb-6">
    <button 
      class="px-4 py-2 -mb-px border-b-2 border-indigo-500 font-semibold text-indigo-600 focus:outline-none"
      id="tab-fg"
      onclick="showTab('fg')">
      Konsumsi Berdasarkan FG
    </button>
    <button 
      class="px-4 py-2 -mb-px border-b-2 border-transparent text-gray-600 hover:text-indigo-600 hover:border-indigo-500 font-semibold focus:outline-none"
      id="tab-cm"
      onclick="showTab('cm')">
      Konsumsi Berdasarkan CM
    </button>
  </div>

   <div id="content-fg">
<!-- Select Finish Goods -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <!-- FG Selection -->
 <div class="bg-white shadow-md border-l-4 border-indigo-500 rounded-xl p-6 space-y-4">
  
  <!-- Select Finish Goods -->
  <div>
    <label for="fg_code" class="block text-sm font-medium text-gray-900 font-bold mb-2">
      Select Finish Goods
    </label>
    <select id="fg_code"
      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
      <option value="">-- Choose Finish Goods --</option>
      {{-- Option akan diisi via JS/AJAX --}}
    </select>
  </div>

  <!-- Select Periode -->
  <!-- Select Periode + Tahun -->
  <div class="flex flex-col sm:flex-row gap-4">
    <!-- Periode -->
    <div class="w-full sm:w-1/2">
      <label for="periode_fg" class="block text-sm font-medium text-gray-900 font-bold mb-2">
        Select Periode
      </label>
      <select id="periode_fg"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
        <option value="">-- Choose Periode --</option>
        <option value="1">Januari</option>
        <option value="2">Februari</option>
        <option value="3">Maret</option>
        <option value="4">April</option>
        <option value="5">Mei</option>
        <option value="6">Juni</option>
        <option value="7">Juli</option>
        <option value="8">Agustus</option>
        <option value="9">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
      </select>
    </div>

    <!-- Tahun -->
    <div class="w-full sm:w-1/2">
      <label for="tahun_chemical" class="block text-sm font-medium text-gray-900 font-bold mb-2">
        Select Tahun
      </label>
      <select id="tahun_fg"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
        <option value="">-- Choose Tahun --</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
      </select>
    </div>
  </div>


</div>


  <!-- FG Info Box -->
  <div id="fg_info" class="bg-white shadow-md border-l-4 border-emerald-500 rounded-xl p-6">
  <h4 class="text-gray-900 font-bold mb-4">Finish Goods Info</h4>

  <div class="grid grid-cols-1 gap-x-4 gap-y-2 text-sm text-gray-600">
    <div class="flex justify-between">
      <span class="font-medium">Bill of Material Number:</span>
      <span id="fg_bom_number">-</span>
    </div>
    <div class="flex justify-between">
      <span class="font-medium">Customer:</span>
      <span id="fg_customer">-</span>
    </div>
    <div class="flex justify-between">
      <span class="font-medium">Latest Price:</span>
      <span id="fg_price" class="text-emerald-600 font-semibold">-</span>
    </div>
    <div class="flex justify-between">
      <span class="font-medium">Average Price:</span>
      <span id="avg_fg" class="text-emerald-600 font-semibold">-</span>
    </div>
  </div>
</div>
</div>

 <!-- Raw Material Table -->
<div id="rm_table" class="mt-8">
  <h3 class="text-lg font-semibold text-gray-800 mb-4">Raw Material Consumption</h3>

  <div class="overflow-x-auto shadow rounded-xl border border-gray-200">
    <table id="rm_table_inner" class="min-w-full text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 uppercase text-xs tracking-wider">
        <tr>
          <th class="px-4 py-3 text-left">RM Code</th>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-center">Qty BOM</th>
          <th class="px-4 py-3 text-center">UoM</th>
          <th class="px-4 py-3 text-right">Price</th>
          <th class="px-4 py-3 text-right">Consumption</th>
          <th class="px-4 py-3 text-right">Qty Sales</th>
          <th class="px-4 py-3 text-right">Total</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white">
        {{-- Data AJAX --}}
      </tbody>
      <tfoot class="bg-gray-50 font-semibold border-t">
        <tr>
          <td colspan="5" class="px-4 py-3 text-right">Sub-Total</td>
          <td id="subtotal_consumption" class="px-4 py-3 text-right text-indigo-600 font-bold">0</td>
          <td></td>
          <td id="subtotal_total" class="px-4 py-3 text-right text-emerald-600 font-bold">0</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Chemical Consumption Table -->
<div id="chemical_table" class="mt-10">
  <h3 class="text-lg font-semibold text-gray-800 mb-4">Chemical Consumption</h3>

  <div class="overflow-x-auto shadow rounded-xl border border-gray-200">
    <table id="chemical_table_inner" class="min-w-full text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 uppercase text-xs tracking-wider">
        <tr>
          <th class="px-4 py-3 text-left">CM Code</th>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-center">Qty BOM</th>
          <th class="px-4 py-3 text-center">UoM</th>
          <th class="px-4 py-3 text-right">Price</th>
          <th class="px-4 py-3 text-right">Consumption</th>
          <th class="px-4 py-3 text-right">Qty Sales</th>
          <th class="px-4 py-3 text-right">Total</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white">
        {{-- Data AJAX --}}
      </tbody>
      <tfoot class="bg-gray-50 font-semibold border-t">
        <tr>
          <td colspan="5" class="px-4 py-3 text-right">Sub-Total</td>
          <td id="subtotal_chem_consumption" class="px-4 py-3 text-right text-indigo-600 font-bold">0</td>
          <td></td>
          <td id="subtotal_chem_total" class="px-4 py-3 text-right text-emerald-600 font-bold">0</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Card Summary -->
<div class="mt-6 bg-white shadow rounded-2xl border border-gray-200 p-6">
  <h4 class="text-gray-800 font-semibold mb-4">Summary</h4>
  
  <div class="grid grid-cols-2 divide-x divide-gray-200 text-sm">
    
    <!-- Left: Consumption -->
    <div class="pr-4 space-y-2">
      <div class="flex justify-between">
        <span>RM Consumption:</span>
        <span id="summary_rm_consumption" class="font-semibold text-indigo-600">0</span>
      </div>
      <div class="flex justify-between">
        <span>CM Consumption:</span>
        <span id="summary_cm_consumption" class="font-semibold text-indigo-600">0</span>
      </div>
      <hr class="border-gray-200">
      <div class="flex justify-between font-bold">
        <span class="text-gray-900">Grand Consumption:</span>
        <span id="summary_grand_consumption" class="font-bold text-indigo-600">0</span>
      </div>
    </div>
    
    <!-- Right: Total -->
    <div class="pl-4 space-y-2">
      <div class="flex justify-between">
        <span>RM Total:</span>
        <span id="summary_rm" class="font-semibold text-emerald-600">Rp 0</span>
      </div>
      <div class="flex justify-between">
        <span>CM Total:</span>
        <span id="summary_cm" class="font-semibold text-emerald-600">Rp 0</span>
      </div>
      <hr class="border-gray-200">
      <div class="flex justify-between font-bold">
        <span class="text-gray-900">Grand Total:</span>
        <span id="summary_grand_total" class="font-bold text-emerald-600">Rp 0</span>
      </div>
    </div>
    
  </div>
</div>
   </div>


<div id="content-cm" class="hidden">
<!-- Select Finish Goods -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <!-- FG Selection -->
 <div class="bg-white shadow-md border-l-4 border-indigo-500 rounded-xl p-6 space-y-4">

  <!-- Select Chemical -->
  <div>
    <label for="cm_code" class="block text-sm font-medium text-gray-900 font-bold mb-2">
      Select Chemical
    </label>
    <select id="cm_code"
      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
      <option value="">-- Choose Chemical --</option>
      {{-- Option akan diisi via JS/AJAX --}}
    </select>
  </div>

  <!-- Select Periode + Tahun -->
  <div class="flex flex-col sm:flex-row gap-4">
    <!-- Periode -->
    <div class="w-full sm:w-1/2">
      <label for="periode_chemical" class="block text-sm font-medium text-gray-900 font-bold mb-2">
        Select Periode
      </label>
      <select id="periode_chemical"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
        <option value="">-- Choose Periode --</option>
        <option value="1">Januari</option>
        <option value="2">Februari</option>
        <option value="3">Maret</option>
        <option value="4">April</option>
        <option value="5">Mei</option>
        <option value="6">Juni</option>
        <option value="7">Juli</option>
        <option value="8">Agustus</option>
        <option value="9">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
      </select>
    </div>

    <!-- Tahun -->
    <div class="w-full sm:w-1/2">
      <label for="tahun_chemical" class="block text-sm font-medium text-gray-900 font-bold mb-2">
        Select Tahun
      </label>
      <select id="tahun_chemical"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
        <option value="">-- Choose Tahun --</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
      </select>
    </div>
  </div>

</div>



  <!-- FG Info Box -->
  <div id="fg_info" class="bg-white shadow-md border-l-4 border-emerald-500 rounded-xl p-6">
  <h4 class="text-gray-900 font-bold mb-4">Chemical Info</h4>

  <div class="grid grid-cols-1 gap-x-4 gap-y-2 text-sm text-gray-600">
    <div class="flex justify-between">
      <span class="font-medium">CM Article:</span>
      <span id="cm_name">-</span>
    </div>
    <div class="flex justify-between">
      <span class="font-medium">Supplier:</span>
      <span id="cm_customer">-</span>
    </div>
    <div class="flex justify-between">
      <span class="font-medium">Latest Price:</span>
      <span id="cm_price" class="text-emerald-600 font-semibold">-</span>
    </div>
     <div class="flex justify-between">
      <span class="font-medium">Average Price:</span>
      <span id="cm_avg_price" class="text-emerald-600 font-semibold">-</span>
    </div>
  </div>
</div>
</div>

 <!-- Raw Material Table -->
<div id="fg_table" class="mt-8">
   <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
    <!-- Judul kiri -->
    <h3 class="text-lg font-semibold text-gray-800 mb-2 md:mb-0">
      Finish Goods Consumption
    </h3>

    <!-- Tombol kanan -->
    <a href="{{ route('fa.cabom.export') }}" 
       class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-colors duration-200"
       target="_blank">
       <span class="mr-2">Export Excel</span>
       <i data-feather="download"></i>
    </a>
  </div>

  <div class="overflow-x-auto shadow rounded-xl border border-gray-200">
    <table id="fg_table_inner" class="min-w-full text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 uppercase text-xs tracking-wider">
        <tr>
          <th class="px-4 py-3 text-left">FG Code</th>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-center">Qty BOM</th>
          <th class="px-4 py-3 text-center">UoM</th>
          <th class="px-4 py-3 text-right">Consumption</th>
          <th class="px-4 py-3 text-right">Qty Sales</th>
          <th class="px-4 py-3 text-right">Total</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white">
        {{-- Data AJAX --}}
      </tbody>
      <tfoot>
  <tr>
    <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-900">TOTAL CONSUMPTION</td>
    <td id="total_consumption" class="px-4 py-3 text-right text-green-600 font-bold">0</td>
  </tr>
  <tr>
    <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-900">TOTAL PEMBELIAN</td>
    <td id="total_buy" class="px-4 py-3 text-right text-indigo-600 font-bold">0</td>
  </tr>
  <tr>
    <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-900">CONTROL (Consumption - Pembelian)</td>
    <td id="total_diff" class="px-4 py-3 text-right text-red-600 font-bold">0</td>
  </tr>
  <tr>
    <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-900">PERCENTAGE (%)</td>
    <td id="total_percentage" class="px-4 py-3 text-right text-purple-600 font-bold">0%</td>
  </tr>
</tfoot>

    </table>
  </div>
</div>


   </div>

</div>
</div>


@push('scripts')
<script>
 function showTab(tab) {
  const fgTab = document.getElementById('tab-fg');
  const cmTab = document.getElementById('tab-cm');
  const fgContent = document.getElementById('content-fg');
  const cmContent = document.getElementById('content-cm');

  if(tab === 'fg') {
    fgContent.classList.remove('hidden');
    cmContent.classList.add('hidden');

    fgTab.classList.add('border-indigo-500', 'text-indigo-600');
    fgTab.classList.remove('border-transparent', 'text-gray-600');

    cmTab.classList.remove('border-indigo-500', 'text-indigo-600');
    cmTab.classList.add('border-transparent', 'text-gray-600');
  } else {
    fgContent.classList.add('hidden');
    cmContent.classList.remove('hidden');

    cmTab.classList.add('border-indigo-500', 'text-indigo-600');
    cmTab.classList.remove('border-transparent', 'text-gray-600');

    fgTab.classList.remove('border-indigo-500', 'text-indigo-600');
    fgTab.classList.add('border-transparent', 'text-gray-600');
  }
}

 $(document).ready(function() {
 

  $('#excel-upload-form').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const $btn = $('#excel-upload-btn'); // pastikan tombol punya id ini
    const originalText = $btn.text();
    let dotCount = 0;

    // Ubah tombol menjadi uploading
    $btn.prop('disabled', true);
    $btn.text('Uploading');

    // Animasi titik
    const interval = setInterval(() => {
        dotCount = (dotCount + 1) % 4; // 0..3
        let dots = '.'.repeat(dotCount);
        $btn.text('Uploading' + dots);
    }, 500);

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            clearInterval(interval);
            $btn.prop('disabled', false);
            $btn.text(originalText); // kembalikan text asli

            $('#upload-message').html(`
                <div class="bg-green-100 text-green-700 p-3 rounded-lg">
                    ${response.message} <br>
                </div>
            `);

            // reset input file
            $('#excel-file').val('');

            // kalau Select2 perlu di-refresh
            if (typeof initSelect2 === 'function') {
                initSelect2();
            }
        },
        error: function(xhr) {
            clearInterval(interval);
            $btn.prop('disabled', false);
            $btn.text(originalText);

            let message = 'Terjadi kesalahan';
            if(xhr.responseJSON?.errors?.file) {
                message = xhr.responseJSON.errors.file[0];
            } else if(xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
            }

            $('#upload-message').html(`
                <div class="bg-red-100 text-red-700 p-3 rounded-lg">
                    ${message}
                </div>
            `);
        }
    });
});

    // Inisialisasi Select2 FG
function initSelect2() {
    $('#fg_code').select2({
        placeholder: '-- Choose Finish Goods --',
        width: '100%',
        ajax: {
            url: "{{ route('fa.cabom.fg') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: { more: data.pagination ? data.pagination.more : false }
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });
     $('#cm_code').select2({
        placeholder: '-- Choose Chemical --',
        width: '100%',
        ajax: {
            url: "{{ route('fa.cabom.select-cm') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: { more: data.pagination ? data.pagination.more : false }
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });
}

$('#periode_chemical').select2({
   placeholder: "-- Choose Periode --",
   width: '100%',
   });

   $('#tahun_chemical').select2({
   placeholder: "-- Choose Tahun --",
   width: '100%',
   });

   $('#periode_fg').select2({
   placeholder: "-- Choose Periode --",
   width: '100%',
   });

function formatPrice(num) {
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
}

// --- Event saat pilih FG ---
$('#fg_code').on('select2:select', function(e) {
    const fgCode = $(this).val();
    if (!fgCode) {
        fgInfo.classList.add("hidden");
        fgCustomer.textContent = "-";
        fgBOMNumber.textContent = "-";
        fgPrice.textContent = "-";
        $("#rm_table_inner tbody").empty();
        $("#chemical_table_inner tbody").empty();
        return;
    }

    loadFGData(fgCode); // langsung load data
});

// --- Event saat ganti periode ---
$('#periode_fg').on('change', function() {
    const fgCode = $('#fg_code').val();
    if (fgCode) {
        loadFGData(fgCode); // reload data RM & CM sesuai periode baru
    }
});

// --- Fungsi load data FG, RM, CM ---
function loadFGData(fgCode) {
    const periode = $('#periode_fg').val(); // ambil periode

    // --- Info Box ---
    $.get("{{ route('fa.cabom.get-fg-info') }}", { fg_code: fgCode }, function(data) {
    if (data) {
        $("#fg_customer").text(data.customer || "-");
        $("#fg_bom_number").text(data.bom_number || "-");
        $("#fg_price").text(data.latest_price 
            ? `Rp ${Number(data.latest_price).toLocaleString('id-ID')}` 
            : "-");
        $("#avg_fg").text(data.avg_price 
            ? `Rp ${Number(data.avg_price).toLocaleString('id-ID')}` 
            : "-");
    }
});


    // --- RM Table ---
    $.get("{{ route('fa.cabom.rm') }}", { fg_code: fgCode, periode: periode }, function(res) {
        const tbody = $("#rm_table_inner tbody").empty();
        let subtotalConsumptionRM = 0;
        let subtotalTotalRM = 0;

        res.data.forEach(row => {
            const price = row.price || 0;
            const consumption = row.consumption || 0;
            const total = row.total || 0;

            tbody.append(`
                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
                    <td class="px-4 py-2 text-left">${row.article_rm}</td>
                    <td class="px-4 py-2 text-left">${row.name_rm}</td>
                    <td class="px-4 py-2 text-center">${row.qty_bom}</td>
                    <td class="px-4 py-2 text-center">${row.uom}</td>
                    <td class="px-4 py-2 text-right">${price}</td>
                    <td class="px-4 py-2 text-right">${consumption}</td>
                    <td class="px-4 py-2 text-right">${row.qty_sales}</td>
                    <td class="px-4 py-2 text-right font-semibold">${formatPrice(total)}</td>
                </tr>
            `);

            subtotalConsumptionRM += consumption;
            subtotalTotalRM += total;
        });

        $('#subtotal_consumption').text(subtotalConsumptionRM.toFixed(2));
        $('#subtotal_total').text(formatPrice(subtotalTotalRM));
        $('#rm_table').removeClass('hidden');

        window.subtotalRM = subtotalTotalRM;
        window.consumptionRM = subtotalConsumptionRM;

        updateSummary(subtotalTotalRM, window.subtotalCM || 0, subtotalConsumptionRM, window.consumptionCM || 0);
    });

    // --- Chemical Table ---
    $.get("{{ route('fa.cabom.cm') }}", { fg_code: fgCode, periode: periode }, function(res) {
        const tbody = $("#chemical_table_inner tbody").empty();
        let subtotalConsumptionCM = 0;
        let subtotalTotalCM = 0;

        res.data.forEach(row => {
            const price = row.price || 0;
            const consumption = row.consumption || 0;
            const total = row.total || 0;

            tbody.append(`
                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
                    <td class="px-4 py-2 text-left">${row.article_cm}</td>
                    <td class="px-4 py-2 text-left">${row.name_cm}</td>
                    <td class="px-4 py-2 text-center">${row.qty_bom}</td>
                    <td class="px-4 py-2 text-center">${row.uom}</td>
                    <td class="px-4 py-2 text-right">${price}</td>
                    <td class="px-4 py-2 text-right">${consumption}</td>
                    <td class="px-4 py-2 text-right">${row.qty_sales}</td>
                    <td class="px-4 py-2 text-right font-semibold">${formatPrice(total)}</td>
                </tr>
            `);

            subtotalConsumptionCM += consumption;
            subtotalTotalCM += total;
        });

        $('#subtotal_chem_consumption').text(subtotalConsumptionCM.toFixed(2));
        $('#subtotal_chem_total').text(formatPrice(subtotalTotalCM));
        $('#chemical_table').removeClass('hidden');

        window.subtotalCM = subtotalTotalCM;
        window.consumptionCM = subtotalConsumptionCM;

        updateSummary(window.subtotalRM || 0, subtotalTotalCM, window.consumptionRM || 0, subtotalConsumptionCM);
    });
}


function updateSummary(subtotalRM, subtotalCM, consRM, consCM) {
    const rm = Number(subtotalRM) || 0;
    const cm = Number(subtotalCM) || 0;
    const consRMVal = Number(consRM) || 0;
    const consCMVal = Number(consCM) || 0;

    const grandTotal = rm + cm;
    const grandConsumption = consRMVal + consCMVal;

    // Format
    const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const formatNum = (num) => Number(num).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // Update summary
    document.getElementById('summary_rm_consumption').textContent = formatNum(consRMVal);
    document.getElementById('summary_rm').textContent = formatRp(rm);

    document.getElementById('summary_cm_consumption').textContent = formatNum(consCMVal);
    document.getElementById('summary_cm').textContent = formatRp(cm);

    document.getElementById('summary_grand_consumption').textContent = formatNum(grandConsumption);
    document.getElementById('summary_grand_total').textContent = formatRp(grandTotal);
}

// --- Set default tahun berjalan ---
const currentYear = new Date().getFullYear();
$('#tahun_chemical').val(currentYear); // jika pakai <select> atau <input>

// Saat user memilih Chemical
$('#cm_code').on('select2:select', function(e) {
    const cmCode = $(this).val();
    if (!cmCode) {
        resetChemicalInfo();
        return;
    }
    loadChemicalData(cmCode);
});

// Saat user memilih periode (bulan)
$('#periode_chemical').on('change', function() {
    const cmCode = $('#cm_code').val();
    if (cmCode) {
        loadChemicalData(cmCode);
    }
});

// Saat user memilih tahun
$('#tahun_chemical').on('change', function() {
    const cmCode = $('#cm_code').val();
    if (cmCode) {
        loadChemicalData(cmCode);
    }
});

// --- Fungsi utama untuk load data berdasarkan CM, periode, & tahun ---
function loadChemicalData(cmCode) {
    const periode = $('#periode_chemical').val(); // misal "1" untuk Januari
    const tahun = $('#tahun_chemical').val();     // tahun dipilih

    // --- Get CM Info ---
    $.get("{{ route('fa.cabom.get-cm-info') }}", 
        { cm_code: cmCode, periode: periode, tahun: tahun }, 
        function(data) {
            if (data && data.cm_code) {
                $("#cm_customer").text(data.customer || "-");
                $("#cm_name").text(data.cm_name || "-");
                $("#cm_price").text(data.latest_price ? `Rp ${Number(data.latest_price).toLocaleString('id-ID')}` : "-");
                $("#cm_avg_price").text(data.avg_price ? `Rp ${Number(data.avg_price).toLocaleString('id-ID')}` : "-");
            } else {
                $("#cm_customer, #cm_name, #cm_price, #cm_avg_price").text("-");
            }
        }
    );

    // --- Get Consumption & Table ---
    $.get("{{ route('fa.cabom.cm-table') }}", 
        { cm_code: cmCode, periode: periode, tahun: tahun }, 
        function(res) {
            const data = res.data || [];
            const tbody = $("#fg_table_inner tbody").empty();
            let subtotalCMConsumption = 0;

            data.forEach(row => {
                const total = Number(row.total) || 0;
                tbody.append(`
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
                        <td class="px-4 py-2 text-left">${row.fg_code}</td>
                        <td class="px-4 py-2 text-left">${row.fg_name}</td>
                        <td class="px-4 py-2 text-center">${row.qty_bom}</td>
                        <td class="px-4 py-2 text-center">${row.uom}</td>
                        <td class="px-4 py-2 text-right">${formatPrice(row.consumption)}</td>
                        <td class="px-4 py-2 text-right">${row.qty_sales}</td>
                        <td class="px-4 py-2 text-right font-semibold">${formatPrice(row.total)}</td>
                    </tr>
                `);
                subtotalCMConsumption += total;
            });

            $("#total_consumption").html(formatPrice(subtotalCMConsumption));

            // --- Get Total Buy ---
            $.get("{{ route('fa.cabom.get-cm-total-buy') }}", 
                { cm_code: cmCode, periode: periode, tahun: tahun }, 
                function(res2) {
                    const totalBuy = Number(res2.total_buy) || 0;
                    $("#total_buy").html(formatPrice(totalBuy));

                    const diff = subtotalCMConsumption - totalBuy;
                    const percentage = subtotalCMConsumption > 0
                        ? ((totalBuy / subtotalCMConsumption) * 100).toFixed(2)
                        : 0;

                    $("#total_diff").html(formatPrice(diff));
                    $("#total_percentage").html(percentage + " %");
                }
            );
        }
    );
}


// --- Fungsi format harga ---
function formatPrice(num) {
    return num ? Number(num).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : "0,00";
}



initSelect2()
});


</script>
@endpush
@endsection
