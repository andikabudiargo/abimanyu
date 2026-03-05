@extends('layouts.app')

@section('title', 'Create Conversion')
@section('page-title', 'Create Conversion')
@section('breadcrumb-item', 'Conversion')
@section('breadcrumb-active', 'Create Conversion')

@section('content')
<div class="bg-gradient-to-br from-slate-50 to-white shadow-xl shadow-slate-200/50 rounded-2xl p-8 mb-4 border border-slate-100">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-800 tracking-tight">
                Create Conversion
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Generate monthly official conversion report
            </p>
        </div>

       <div class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold 
            bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100
            shadow-sm shadow-indigo-100/40 tracking-wide">

    <i class="fa-solid fa-pen-to-square text-indigo-500 text-[11px]"></i>
    <span>Status: Draft</span>

</div>
    </div>
 <form id="conversionForm">
        @csrf
    <!-- Form Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
   
        <!-- Conversion Number -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2 tracking-wide">
                Conversion Number
            </label>
            <input 
                type="text" 
                id="conversion_number" 
                placeholder="Auto-generated"
                readonly
                class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-100 text-slate-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                       transition duration-200 ease-in-out">
        </div>

        <!-- Year -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2 tracking-wide">
                Year
            </label>
            <select 
                id="year"
                name="year"
                class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                       transition duration-200 ease-in-out">
            </select>
        </div>

        <!-- Month -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2 tracking-wide">
                Month
            </label>
            <select 
                id="month"
                name="month"
                class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                       transition duration-200 ease-in-out">
            </select>
        </div>

        <!-- Note (colspan 3) -->
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-2 tracking-wide">
                Notes
            </label>
            <textarea
            id="notes"
            name="notes"
                rows="3"
                placeholder="Optional remarks, explanation, or adjustments for this report..."
                class="w-full px-3 py-3 text-sm border border-slate-200 rounded-xl bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                       transition duration-200 ease-in-out resize-none"></textarea>
        </div>

    </div>

</div>

<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">List Delivery</h2>
    <div id="reportInfo" class="hidden mb-4">
    <div class="text-sm text-slate-600">
        Here is the delivery data for 
        <span id="reportPeriod" class="font-semibold text-slate-800"></span>
    </div>
</div>
    <div class="bg-white rounded-xl">
    <table id="conversion-table" class="w-max text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Article</th>
                    <th class="px-4 py-2 !text-right">Qty Delivery</th>
                    <th class="px-4 py-2 !text-right">Conversion</th>
                    <th class="px-4 py-2 !text-right">Price</th>
                    <th class="px-4 py-2 !text-right">Grand Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
    <div class="flex justify-end mt-6">
    <div id="summary-card"
         class="w-full md:w-96 bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
    </div>
</div>
<div class="flex justify-start items-center gap-2 mt-4 border-t border-gray-400 pt-8">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

         <button type="submit" id="submitBtn"
        class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
        <i class="fa-solid fa-floppy-disk"></i>
        Save
    </button>
</form>
</div>
</div>

        </div>


    

{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#conversion-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#conversion-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* 🧭 Spacing */
#conversion-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#conversion-table th, #conversion-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#conversion-table td, #conversion-table th {
    white-space: nowrap;
}

.select2-container {
    width: 100% !important;
}

.dataTables_scrollHeadInner,
.dataTables_scrollHeadInner table {
    width: 100% !important;
}

.dataTables_scrollBody {
    overflow-x: auto !important;
}


 .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 4px 10px !important;
        border: 1px solid #d1d5db !important; /* gray-300 */
        border-radius: 0.375rem !important; /* rounded-md */
        font-size: 1rem !important; /* text-base */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px;
    }

</style>

<script>
$(document).ready(function(){

function formatNumber(value) {
    if (value === null || value === undefined || value === '') return '0.00';

    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon,
        title: title
    });
}

 const $year = $('#year');
const $month = $('#month');

    

    const startYear = 2022; // <-- ubah sesuai awal data STO
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;

    const months = [
        "Januari","Februari","Maret","April","Mei","Juni",
        "Juli","Agustus","September","Oktober","November","Desember"
    ];

    // =========================
    // GENERATE YEAR
    // =========================
    $year.append('<option value="">-- Pilih Tahun --</option>');

    for (let y = currentYear; y >= startYear; y--) {
        $year.append(`<option value="${y}">${y}</option>`);
    }

    // =========================
    // GENERATE MONTH (selalu 12)
    // =========================
    $month.append('<option value="">-- Pilih Bulan --</option>');

  $.each(months, function (index, monthName) {
    let monthNumber = index + 1;
    $month.append(
        `<option value="${monthNumber}">${monthName}</option>`
    );
});

    // =========================
    // DEFAULT ke bulan & tahun sekarang
    // =========================
    $year.val(currentYear);
   $month.val(currentMonth);

function updateReportInfo() {
    const year = $('#year').val();
    const month = $('#month').val();

    if (year && month) {
        const monthName = months[parseInt(month) - 1];

        $('#reportPeriod').html(`<strong>${monthName} ${year}</strong>`);
        $('#reportInfo').removeClass('hidden');
    } else {
        $('#reportInfo').addClass('hidden');
    }
}

    let conversionData = []; // 🔐 data asli dari backend
    let summary = {};

   let table = $('#conversion-table').DataTable({
        processing: true,
        searching: false,
        paging: false,
        info:false,
        scrollY: '500px',
    scrollX: true,          // WAJIB aktifkan ini
    scrollCollapse: true,
    autoWidth: false,
        ajax: {
            url: "/marketing/conversion/data",
            data: function(d){
                d.year  = $('#year').val();
                d.month = $('#month').val();
            },
             dataSrc: function(json){
        conversionData = json.data; // simpan data asli
        summary = json.summary; // 🔥 SIMPAN DI SINI
        updateSummary(json.summary);

        return json.data;
    }
        },
        columns: [
            {data:'customer'},
            {
                data:null,
                render:function(row){
                    return row.article_code + ' - ' + row.article_desc;
                }
            },
           {
    data: 'delivery_qty',
    className: 'text-end',
},
{
    data: 'conversion',
    className: 'text-end',
    render: function(data, type) {
        if (type === 'display') {
            return '<span style="color:green; font-weight:600;">'
                    + formatNumber(data) +
                   '</span>';
        }
        return data;
    }
},
{
    data: 'price',
    className: 'text-end',
    render: function(data, type) {
        if (type === 'display') {
            return formatNumber(data);
        }
        return data;
    }
},
{
    data: 'grand_total',
    className: 'text-end',
    render: function(data, type) {
        if (type === 'display') {
            return '<span style="color:black; font-weight:600;">'
                    + formatNumber(data) +
                   '</span>';
        }
        return data;
    }
}
        ]
    });

    // =====================
    // APPLY FILTER
    // =====================
   $('#year, #month').on('change', function () {
    updateReportInfo();
    table.ajax.reload();
});

//SUBMIT DATA
$('#conversionForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const $submitBtn = $('#submitBtn');
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    if (!conversionData || conversionData.length === 0) {
        showToast('error', 'Detail conversion tidak boleh kosong.');
        $submitBtn.prop('disabled', false).text('Save');
        return;
    }

    const formData = new FormData(this);

    formData.append('details', JSON.stringify(conversionData));
    formData.append('total_qty', summary.total_qty || 0);
    formData.append('total_conversion', summary.total_conversion || 0);
    formData.append('estimated_profit', summary.total_grand_total || 0);

    $.ajax({
        url: '{{ route("marketing.conversion.store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'Conversion saved successfully.');
                setTimeout(() => {
                    window.location.href = '{{ route("marketing.conversion.index") }}';
                }, 1500);
            } else {
                showToast('error', res.message || 'Gagal menyimpan data.');
                $submitBtn.prop('disabled', false).text('Save');
            }
        },
        error: function (err) {
            const msg = err.responseJSON?.message || 'Terjadi kesalahan sistem.';
            showToast('error', msg);
            $submitBtn.prop('disabled', false).text('Save');
        }
    });
});


function updateSummary(summary) {

    $('#summary-card').html(`
        <h3 class="text-sm font-semibold text-slate-700 mb-4">
            Monthly Summary
        </h3>

        <div class="space-y-2 text-sm">

            <div class="flex justify-between">
                <span>Total Items</span>
                <span class="font-semibold">${summary.total_rows} type of items delivered</span>
            </div>

            <div class="flex justify-between">
                <span>Total Customers</span>
                <span class="font-semibold">${summary.total_customers} customers received</span>
            </div>

            <div class="flex justify-between">
    <span>Total Qty Delivery</span>
    <span class="font-semibold">
        ${Number(summary.total_qty).toLocaleString('id-ID')} pcs
    </span>
</div>

            <div class="flex justify-between text-green-600">
                <span>Total Conversion</span>
                <span class="font-semibold">${formatNumber(summary.total_conversion)}</span>
            </div>

            <div class="flex justify-between text-slate-800">
                <span>Estimated Profit</span>
                <span class="font-bold">Rp. ${formatNumber(summary.total_grand_total)}</span>
            </div>

        </div>
    `);
}
});
</script>
@endpush


@endsection