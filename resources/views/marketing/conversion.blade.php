@extends('layouts.app')

@section('title', 'Conversion Management')
@section('page-title', 'CONVERSION MANAGEMENT')
@section('breadcrumb-item', 'Conversion')
@section('breadcrumb-active', 'Conversion Management')

@section('content')


{{-- 📄 TABEL --}}
 <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Conversion Management</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
    <label for="filter-order-type" class="block text-sm mb-1 font-medium text-gray-700">Periode</label>
    <select id="filter-order-type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- tambahkan sesuai kebutuhan -->
    </select>
</div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
              <a href="{{ route('marketing.conversion.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-4">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">
                Conversion Performance
            </h2>
            <p class="text-xs text-slate-400 tracking-wide">
                Monthly Overview
            </p>
        </div>

        <select id="yearFilter"
            class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-400">
            @for($y = date('Y'); $y >= date('Y')-5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

  <div class="grid grid-cols-4 gap-8">

    <!-- Chart Area -->
    <div class="col-span-3">
        <canvas id="conversionChart" height="110"></canvas>
    </div>

    <!-- Metrics Panel -->
    <div class="col-span-1 flex flex-col justify-between">

        <div class="border-b border-slate-100 pb-4">
            <p class="text-xs text-slate-400 uppercase tracking-widest">
                Total Conversion
            </p>
            <h3 id="grandTotalConversion"
                class="text-2xl font-bold text-slate-800 tabular-nums text-right">
                0
            </h3>
        </div>

        <div class="border-b border-slate-100 py-4">
            <p class="text-xs text-slate-400 uppercase tracking-widest">
                Total Estimated Profit
            </p>
            <h3 id="grandTotalProfit"
                class="text-2xl font-bold text-slate-800 tabular-nums text-right">
                0
            </h3>
        </div>

        <div class="border-b border-slate-100 py-4">
            <p class="text-xs text-slate-400 uppercase tracking-widest">
                Total Qty Delivery
            </p>
            <h3 id="grandTotalQty"
                class="text-2xl font-bold text-slate-800 tabular-nums text-right">
                0
            </h3>
        </div>

        <div class="pt-4">
            <p class="text-xs text-slate-400 uppercase tracking-widest">
                Total Customer
            </p>
            <h3 id="totalCustomer"
                class="text-2xl font-bold text-slate-800 tabular-nums text-right">
                0
            </h3>
        </div>

    </div>

</div>

</div>

<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Conversion List</h2>
    <div class="bg-white rounded-xl">
    <table id="conversion-table" class="w-max text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2 !text-center">Action</th>
                    <th class="px-4 py-2 !text-center">Conversion Number</th>
                    <th class="px-4 py-2 !text-center">Year</th>
                    <th class="px-4 py-2 !text-left">Month</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2 !text-right">Qty Delivery</th>
                    <th class="px-4 py-2 !text-right">Total Conversion</th>
                    <th class="px-4 py-2 !text-right">Estimated Profit</th>
                    <th class="px-4 py-2 !text-left">Note</th>
                    <th class="px-4 py-2 !text-left">Created by</th>
                    <th class="px-4 py-2 !text-left">Created at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
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

/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    margin-left: 10px;
}

/* 🧭 Spacing */
#conversion-table_wrapper {
    margin-top: 1rem;
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

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#conversion-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}

.select2-container {
    width: 100% !important;
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

/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    position: relative;
    z-index: 1;
    margin-left: 10px;
}


/* Ukuran tombol collection (export) */
.dt-button.buttons-collection {
    font-size: 0.875rem; /* text-sm */
    padding: 0.4rem 1rem;
}

.dt-button-down-arrow {
    display: none !important;
}

div.dt-button-collection {
    top: 100% !important;
    margin-top: 0.5rem !important; /* Jarak dari tombol */
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 9999 !important;
}


/* Dropdown Export agar tampil di bawah */
div.dt-button-collection {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    margin-top: 0.5rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 10000;
}

/* Item dropdown */
div.dt-button-collection .dt-button {
    color: #1f2937;
    padding: 0.5rem 1rem;
    text-align: left;
    width: 100%;
}

div.dt-button-collection .dt-button:hover {
    background-color: #dfe0e0ff;
}


</style>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
$(document).ready(function(){

 let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"

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

    $('#conversion-table').DataTable({
        processing: true,
        serverSide: true,
        autowidth:false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
    if (!$('#conversion-table').parent().hasClass('scroll-wrapper')) {
        $('#conversion-table').wrap('<div class="scroll-wrapper overflow-x-auto"></div>');
    }
},
        ajax: '/marketing/conversion/datatable',
         lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
       buttons: [
    {
        extend: 'collection',
        text: '<i class="fas fa-download mr-2"></i>Export',
        className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy mr-2"></i>Copy',
            },
            {
                extend: 'excelHtml5',
                filename: 'QC_Inspection_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'QC_Inspection_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]// kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'QC_Inspection_ ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            }
        ]
    }
],
        columns: [
             { data: 'action', name: 'action', orderable: false, searchable: false },
            {
    data: 'conversion_number',
    className: 'text-center',
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            let status = (row.status || '').toString().toLowerCase();
            let colorClass = '';

            if (status === 'draft') {
                colorClass = 'bg-gray-200 text-gray-700';
            } 
            else if (status === 'approved') {
                colorClass = 'bg-yellow-100 text-yellow-700';
            } 
            else if (status === 'posted') {
                colorClass = 'bg-green-100 text-green-700';
            } 
            else {
                colorClass = 'bg-slate-100 text-slate-700';
            }

            return `
                <span class="inline-flex items-center justify-center
                             w-32 px-3 py-1 rounded-full text-xs font-semibold
                             ${colorClass}">
                    ${data ?? '-'}
                </span>
            `;
        }

        return data;
    }
},
            { data: 'year' },
           {
    data: 'month',
    className: 'text-left',
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            const months = [
                '', 
                'Januari', 
                'Februari', 
                'Maret', 
                'April', 
                'Mei', 
                'Juni',
                'Juli', 
                'Agustus', 
                'September', 
                'Oktober', 
                'November', 
                'Desember'
            ];

            let monthNumber = parseInt(data) || 0;

            return months[monthNumber] ?? '-';
        }

        return data;
    }
},
            {
    data: 'status',
    className: 'text-center',
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            let status = (data || '').toString().toLowerCase();
            let colorClass = '';

            if (status === 'draft') {
                colorClass = 'bg-gray-200 text-gray-700';
            } 
            else if (status === 'approved') {
                colorClass = 'bg-yellow-100 text-yellow-700';
            } 
            else if (status === 'posted') {
                colorClass = 'bg-green-100 text-green-700';
            }

            return `
                <span class="inline-flex items-center justify-center
                             w-24 px-3 py-1 rounded-full text-xs font-semibold
                             ${colorClass}">
                    ${data}
                </span>
            `;
        }

        return data;
    }
},
           {
    data: 'total_qty',
    className: 'text-right',
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            let number = parseFloat(data) || 0;

            // Hilangkan desimal (dibulatkan)
            let formatted = Math.round(number).toLocaleString('id-ID');

            return '<span class="tabular-nums">'
                    + formatted +
                   '</span>';
        }

        return data;
    }
},
           {
    data: 'total_conversion',
    className: 'text-right',
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            let number = parseFloat(data) || 0;
            let formatted = number.toLocaleString('id-ID');

            return '<span class="font-bold text-green-600 tabular-nums">'
                    + formatted +
                   '</span>';
        }

        return data;
    }
},

   {
    data: 'estimated_profit',
    className: 'text-right', // Tailwind align kanan
    render: function (data, type, row) {

        if (type === 'display' || type === 'filter') {

            let number = parseFloat(data) || 0;
            let formatted = number.toLocaleString('id-ID');

            return '<span class="font-semibold text-gray-600 tabular-nums">'
        + formatted +
       '</span>';
        }

        return data;
    }
},
             {
                data: 'note',
                
            },
            {
                data: 'created_by',
               
            },
             {
                data: 'created_at',
               
            },
            
        ]
        
    });

   let ctx = document.getElementById('conversionChart').getContext('2d');
let chart;

function loadChart(year) {

    fetch("{{ route('marketing.conversion.chart') }}?year=" + year)
        .then(res => res.json())
        .then(res => {

            document.getElementById('grandTotalConversion')
                .innerText = new Intl.NumberFormat('id-ID')
                .format(res.grand_total_conversion);

            document.getElementById('grandTotalProfit')
                .innerText = new Intl.NumberFormat('id-ID')
                .format(res.grand_total_profit);

            document.getElementById('grandTotalQty')
                .innerText = new Intl.NumberFormat('id-ID')
                .format(res.grand_total_qty);

            document.getElementById('totalCustomer')
                .innerText = res.total_customer;

            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: res.months,
                    datasets: [{
                        data: res.totals,
                        borderColor: '#1e293b',
                        backgroundColor: 'rgba(30,41,59,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#1e293b',
                        fill: true
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },

                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('id-ID')
                                        .format(context.raw);
                                }
                            }
                        },

                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            color: '#0f172a',
                            font: {
                                weight: '600',
                                size: 10
                            },
                            formatter: function(value) {
                                return value > 0
                                    ? new Intl.NumberFormat('id-ID').format(value)
                                    : '';
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(100,116,139,0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

        });
}

loadChart(new Date().getFullYear());

document.getElementById('yearFilter')
    .addEventListener('change', function() {
        loadChart(this.value);
    });

     feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
  
});
</script>
@endpush


@endsection