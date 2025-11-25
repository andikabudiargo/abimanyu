@extends('layouts.app-security')

@section('title', 'Catering Management')
@section('page-title', 'DASHBOARD CATERING')
@section('breadcrumb-item', 'Security')
@section('breadcrumb-active', 'Catering Management')

@section('content')
 <div id="cateringPanel" class="p-4 bg-white rounded-t-lg mt-4 animate-fadeIn">
 <div class="relative overflow-hidden rounded-2xl shadow-lg p-6 mb-8 text-white text-center bg-gradient-to-br from-blue-500 via-blue-300 to-blue-500 transform transition-all hover:scale-[1.02] hover:shadow-2xl">
  <!-- Efek Cahaya Bergerak -->
  <div class="absolute inset-0 bg-gradient-to-t from-white/10 to-transparent opacity-30 blur-2xl"></div>

  <!-- Konten Utama -->
  <div class="relative z-10 flex flex-col items-center">
    <!-- ANGKA COUNT -->
    <span id="cateringCount" class="text-7xl font-extrabold mb-1 drop-shadow-lg">0</span>
    <span class="text-base font-semibold tracking-wide uppercase text-white/90">Porsi</span>

    <hr class="w-full border-t-2 border-white my-4">

    <!-- TEKS DAN ICON -->
    <div class="flex justify-center items-center gap-2 text-sm font-medium text-white/90">
      <i data-feather="coffee" class="w-5 h-5"></i>
      <span>Estimasi Pemesanan Catering</span>
    </div>
  </div>

  <!-- Efek Lingkaran Glow -->
  <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
</div>


 

 <!-- Section Filter -->
<div class="flex flex-col mb-4 space-y-4">

  <!-- Filter Tanggal -->
  <div class="flex flex-col">
    <label for="filterDate" class="text-sm font-medium text-gray-600 mb-1">
      Pilih Tanggal:
    </label>
    <input 
      type="date" 
      id="filterDate" 
      class="w-full sm:w-96 border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring focus:ring-blue-200 focus:outline-none"
    >
  </div>

  <!-- Filter Jam -->
  <div class="flex flex-col space-y-2">
    <div class="flex flex-col">
      <label for="filterTimeStart" class="text-sm font-medium text-gray-600 mb-1">
        Jam Dari:
      </label>
      <input 
        type="time" 
        id="filterTimeStart"
        class="w-full sm:w-96 border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring focus:ring-blue-200 focus:outline-none"
      >
    </div>

    <div class="flex flex-col">
      <label for="filterTimeEnd" class="text-sm font-medium text-gray-600 mb-1">
        Jam Sampai:
      </label>
      <input 
        type="time" 
        id="filterTimeEnd"
        class="w-full sm:w-96 border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring focus:ring-blue-200 focus:outline-none"
      >
    </div>
  </div>

  <!-- Tombol Filter -->
   <button 
      id="btnFilterCatering"
      class="w-full sm:w-48 bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition"
    >
      Tarik Data Absen
    </button>

</div>



 <div class="overflow-x-auto overflow-y-auto mt-12 mb-12">
    <table class="min-w-full table-fixed text-sm text-gray-700 whitespace-nowrap">
    <thead class="bg-blue-500 text-white sticky top-0 z-10 shadow-sm">
  <tr class="">
    <th class="py-3 px-4 text-center text-sm font-medium uppercase tracking-wider">#</th>
    <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Nama</th>
    <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">NIK</th>
    <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Jam Masuk</th>
    <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Jam Keluar</th>
  </tr>
</thead>

      <tbody id="cateringTableBody" class="divide-y divide-gray-200 bg-white border-b border-gray-200">
        <tr>
          <td colspan="5">No Data Available</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Floating Bottom Navigation (Mobile Only) -->
<nav class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-lg z-50">
  <div class="flex justify-around">

    <!-- Home -->
    <a href="#home"
       class="nav-item flex flex-col items-center justify-center py-2 w-full text-gray-500 hover:text-blue-500 transition-colors duration-200">
      <i data-feather="home" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Home</span>
    </a>

    <!-- Visitor -->
    <a href="#visitor"
       class="nav-item flex flex-col items-center justify-center py-2 w-full text-gray-500 hover:text-blue-500 transition-colors duration-200">
      <i data-feather="user" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Visitor</span>
    </a>

    <!-- Izin -->
    <a href="#izin"
       class="nav-item flex flex-col items-center justify-center py-2 w-full text-gray-500 hover:text-blue-500 transition-colors duration-200">
      <i data-feather="clock" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Izin</span>
    </a>

    <!-- Catering -->
    <a href="#catering"
       class="nav-item flex flex-col items-center justify-center py-2 w-full text-gray-500 hover:text-blue-500 transition-colors duration-200">
      <i data-feather="coffee" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Catering</span>
    </a>

  </div>
</nav>

<!-- 🌟 Floating Add Button -->
<button id="fabAdd"
  class="fixed bottom-20 right-4 bg-blue-600 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center hover:bg-blue-700 transform hover:scale-110 transition-all duration-300 z-50">
  <i data-feather="plus" class="w-6 h-6"></i>
</button>
{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#ticket-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#ticket-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Non-Tailwind CSS */
#ticket-table td,
#ticket-table th {
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


/* 🧭 Spacing */
#ticket-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#ticket-table th, #ticket-table td {
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

    /* Pagination DataTables modern */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 9999px; /* lingkaran penuh */
    border: 1px solid transparent;
    min-width: 2.5rem;
    min-height: 2.5rem;
    padding: 0.25rem 0;
    margin: 0 0.25rem;
    text-align: center;
    line-height: 2rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #3b82f6; /* Tailwind blue-500 */
    color: white !important;
    border-color: #2563eb; /* blue-600 */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #2563eb; /* active biru gelap */
    color: white !important;
    border-color: #2563eb;
}


</style>
<script>
   feather.replace();

$(document).ready(function() {

  // === TAB NAVIGATION ===
  $('.nav-item').on('click', function(e) {
    e.preventDefault();
    $('.nav-item').removeClass('bg-blue-100 text-blue-500').addClass('text-gray-500');
    $(this).addClass('bg-blue-100 text-blue-500').removeClass('text-gray-500');
  });

  $('.nav-link').on('click', function() {
    $('.nav-link').removeClass('bg-blue-50 text-blue-600');
    $(this).addClass('bg-blue-50 text-blue-600');
  });

  // === BUTTON ADD EFFECT ===
  $('#fabAdd').on('click', function() {
    $(this).addClass('scale-90');
    setTimeout(() => $(this).removeClass('scale-90'), 150);

    Swal.fire({
      title: 'Tambah Data Baru',
      text: 'Tombol Add diklik!',
      icon: 'info',
      confirmButtonText: 'OK'
    });
  });

 $(document).ready(function () {

    // Fungsi untuk membuat baris no data
    function showNoData() {
        $("#cateringTableBody").html(`
            <tr>
                <td colspan="5" class="py-4 px-3 text-center text-gray-500">
                    No Data Available
                </td>
            </tr>
        `);
        $("#cateringCount").text("0");
    }

    // Kosongkan tampilan awal
    showNoData();

    // Tombol Filter
    $("#btnFilterCatering").on("click", function () {

        let tanggal = $("#filterDate").val();
        let jamStart = $("#filterTimeStart").val();
        let jamEnd = $("#filterTimeEnd").val();

        // VALIDASI INPUT
        if (!tanggal || !jamStart || !jamEnd) {
            alert("Isi semua filter (Tanggal, Jam Dari, Jam Sampai).");
            return;
        }

        // Reset tabel & jumlah
        $("#cateringTableBody").empty();
        $("#cateringCount").text("0");

        $.ajax({
            url: "{{ route('security.catering') }}",  
            method: "GET",
            dataType: "json",
            data: {
                date: tanggal,
                start: jamStart,
                end: jamEnd
            },
            success: function (res) {

                if (!res || res.length === 0) {
                    showNoData();
                    return;
                }

                // Masukkan data ke tabel dengan odd/even styling
                $("#cateringTableBody").empty();
               res.forEach((row, index) => {
    let inTime = row.in_time || "-";       // Jam masuk
    let outTime = row.out_time || "-";     // Jam keluar
    let statusText = "";
    let statusIcon = "";

    if (inTime > "08:00:00") {
        statusText = "Terlambat";
        statusIcon = `<i class="fa-solid fa-clock text-red-500 mr-1"></i> <span class="text-red-600 font-medium">${statusText}</span>`;
    } else {
        statusText = "Tepat Waktu";
        statusIcon = `<i class="fa-solid fa-clock text-green-500 mr-1"></i> <span class="text-green-600 font-medium">${statusText}</span>`;
    }

    let rowClass = index % 2 === 0 ? "bg-white" : "bg-gray-50";

    $("#cateringTableBody").append(`
        <tr class="${rowClass}">
            <td class="py-2 px-3 text-center">${index + 1}</td>
            <td class="py-2 px-3">${row.name}</td>
            <td class="py-2 px-3">${row.nik}</td>
            <td class="py-2 px-3">${inTime}</td>
            <td class="py-2 px-3">${outTime}</td>  <!-- jam keluar -->
        </tr>
    `);
});


                // Hitung porsi
                $("#cateringCount").text(res.length);
            }
        });

    });

});



  // === SOCKET.IO ===
  const socket = io("http://127.0.0.1:6001");

  socket.on("connect", function() {
    console.log("Terhubung ke server WebSocket.");
  });

  socket.on("disconnect", function() {
    console.warn("Terputus dari server WebSocket.");
  });

  socket.on("new_catering", function(data) {
    console.log("Data baru diterima:", data);

    $('#cateringTableBody').prepend(`
      <tr class="bg-green-100 animate-pulse">
        <td class="py-2 px-3 uppercase">${data.name}</td>
        <td class="py-2 px-3">${data.nik}</td>
        <td class="py-2 px-3">${data.timestamp}</td>
      </tr>
    `);

    // Update counter
    const total = parseInt($('#cateringCount').text()) || 0;
    $('#cateringCount').text(total + 1);

    // SweetAlert2 notification (toast)
    Swal.fire({
      title: 'Karyawan Baru Masuk!',
      text: `${data.name} baru saja absen masuk.`,
      icon: 'success',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true
    });
  });

  // === INITIAL LOAD + AUTO REFRESH ===
  loadCateringData();
});

  </script>
@endpush


@endsection