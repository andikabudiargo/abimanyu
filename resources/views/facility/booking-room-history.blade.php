@extends('layouts.app')

@section('title', 'History Booking')
@section('page-title', 'DASHBOARD HISTORY BOOKING')
@section('breadcrumb-item', 'Booking Meeting Room')
@section('breadcrumb-active', 'History Booking')

@section('content')

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter History Booking</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
    <input id="filter-date" type="text" name="date"  placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
    
</div>

            <div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
       <option value="Waiting Approval">Waiting Approval</option>
       <option value="Booked">Booked</option>
       <option value="Cancelled">Cancelled</option>
    </select>

    
</div>

<!-- Status -->
<div>
    <label for="filter-room" class="block text-sm mb-1 font-medium text-gray-700">Room</label>
    <select id="filter-room" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
    </select>
</div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <a href="{{ route('facility.booking-room.index') }}" class="w-24 text-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow">Back</a>
            <button type="submit" class="w-24 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold">Booking History</h2>
    <table id="history-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Time</th>
                    <th class="px-4 py-2">Room</th>
                    <th class="px-4 py-2 w-28 !text-center">Status</th>
                    <th class="px-4 py-2">Request by</th>
                    <th class="px-4 py-2">Request at</th>
                    <th class="px-4 py-2">Purpose</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Approved by</th>
                    <th class="px-4 py-2">Approved at</th>
                    <th class="px-4 py-2">Cancel by</th>
                    <th class="px-4 py-2">Cancel at</th>
                    <th class="px-4 py-2">Cancel Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
</div>
{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#history-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#history-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}


/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* Non-Tailwind CSS */
#history-table td,
#history-table th {
    white-space: nowrap;
}


/* Pastikan pembungkus utama flex */
.mobile-flex-wrapper {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem; /* jarak antar elemen */
}

/* Search filter tetap auto width */
.dataTables_filter {
  width: auto !important;
  display: flex !important;
  align-items: center;
  flex-wrap: nowrap;
}

/* 🎯 Fine-tuning posisi sejajar Search dan Export */
@media (max-width: 768px) {
  .dataTables_filter {
    align-items: center !important;
  }

  .dataTables_filter input {
    height: 38px !important;
    margin-top: 2px; /* sedikit naik agar sejajar */
  }

  .dt-buttons .dt-button {
     height: 38px !important;
  line-height: 38px;
    padding-top: 0;
    padding-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

/* 🔍 Extra tuning khusus layar sempit banget (≤414px, iPhone XR/SE) */
@media (max-width: 414px) {
  .dataTables_filter input {
    max-width: 120px;
  }

  .dt-buttons .dt-button {
    font-size: 0.8rem;
    padding: 0.35rem 0.75rem;
  }
}


/* Input search */
.dataTables_filter input {
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 6px 10px;
  margin-left: 10px;
  width: 150px; /* sesuaikan */
   margin-top: 2px; /* sedikit naik agar sejajar */
}

/* Tombol export */
.dt-buttons {
  display: flex !important;
  align-items: center;
  width: auto !important;
  position: relative;
  z-index: 1;
  margin-left: 10px;
}

/* 📱 Mobile adjustment */
@media (max-width: 768px) {
  .mobile-flex-wrapper {
    flex-wrap: nowrap; /* biar sejajar */
    justify-content: space-between;
  }

  .dataTables_filter,
  .dt-buttons {
    flex: 1 1 auto;
    display: flex !important;
    width: auto !important;
  }

  /* Ratakan tinggi dan sejajarkan posisi vertikal */
.dataTables_filter label {
  display: flex;
  align-items: center; /* ini penting agar sejajar vertikal */
  margin-bottom: 0 !important; /* hilangkan margin default */
}

.dataTables_filter input {
  height: 38px; /* samakan tinggi dengan tombol Export */
  margin: 0 0 0 8px; /* jarak kiri sedikit */
  line-height: 1.2;
}

.dt-buttons .dt-button {
  height: 38px; /* samakan tinggi dengan input */
  display: flex;
  align-items: center;
  justify-content: center;
}

  .dataTables_filter label span {
    display: none; /* hilangkan teks Search */
  }

  .dt-buttons {
    justify-content: flex-end;
    margin-left: 0;
  }
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


/* 🧭 Spacing */
#history-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#history-table th, #history-table td {
    border: none !important;
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

</style>
<script>
flatpickr("#filter-date", {
    mode: "range",
    dateFormat: "Y-m-d",
    onClose: function(selectedDates, dateStr, instance) {
        if (dateStr.includes(" to ")) {
            let dates = dateStr.split(" to ");
            let startDate = dates[0];
            let endDate = dates[1];

            // reload datatable dengan parameter baru
            $('#bookingTable').DataTable().ajax.url('/bookings?start_date=' + startDate + '&end_date=' + endDate).load();
        } else {
            // kalau cuma pilih 1 tanggal
            $('#bookingTable').DataTable().ajax.url('/bookings?start_date=' + dateStr + '&end_date=' + dateStr).load();
        }
    }
});

let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("facility.booking-room.data") }}',
            data: function (d) {
                d.status = $('#filter-status').val();
                 let dateRange = $('#filter-date').val();
    if (dateRange.includes(" to ")) {
        let dates = dateRange.split(" to ");
        d.start_date = dates[0];
        d.end_date = dates[1];
    } else if (dateRange) {
        d.start_date = dateRange;
        d.end_date = dateRange;
    }
                d.room = $('#filter-room').val();
            }
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
         dom: `
    <'flex flex-col md:flex-row justify-between items-center mb-4'
        <'flex flex-wrap items-center space-x-2 mb-2 md:mb-0'l>
        <'flex flex-wrap items-center space-x-2'f B>
    >
    rt
    <'flex flex-col md:flex-row justify-between items-center mt-4'
        <'text-sm text-gray-500 mb-2 md:mb-0'i>
        <'flex flex-wrap items-center space-x-2'p>
    >
`,
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
                filename: 'Data_Booking_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Data_Booking_Ruangan_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Data Booking Ruangan ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            },
        ]
    },

    
    
],
      columns: [
        { data: 'booking_date', name: 'booking_date', orderable: false },
    { data: 'time', name: 'time', orderable: false },
    { data: 'room_id', name: 'room_id', orderable: false },
    { data: 'status', name: 'status', className: 'text-center', orderable: false },
    { data: 'created_by', name: 'created_by', orderable: false },
    { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false },
    { data: 'purpose', name: 'purpose', orderable: false },
    { data: 'description', name: 'description', orderable: false },
    { data: 'approved_by', name: 'approved_by', orderable: false },
    { data: 'approved_at', name: 'approved_at', orderable: false },
    { data: 'cancel_by', name: 'cancel_by', orderable: false },
    { data: 'cancel_at', name: 'cancel_at', orderable: false },
    { data: 'cancel_reason', name: 'cancel_reason', orderable: false },
       
      ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

  $(document).ready(function() {
    // Load room list untuk filter
    $.ajax({
        url: "{{ route('facility.room.select') }}",
        method: "GET",
        success: function(res) {
            let $roomSelect = $("#filter-room");
            $roomSelect.empty().append('<option value="">-- All --</option>');
            res.forEach(function(room) {
                $roomSelect.append('<option value="' + room.id + '">' + room.name + '</option>');
            });
        }
    });
});

  </script>
@endpush


@endsection