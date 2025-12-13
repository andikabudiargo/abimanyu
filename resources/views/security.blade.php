@extends('layouts.app-security')

@section('title', 'Catering Management')
@section('page-title', 'DASHBOARD CATERING')
@section('breadcrumb-item', 'Security')
@section('breadcrumb-active', 'Catering Management')

@section('content')
 <div id="homePanel" class="p-6 bg-white from-gray-50 to-white rounded-xl shadow-sm mt-6 animate-fadeIn">

    <!-- HEADER -->
    <div class="text-left mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
            Selamat Datang,
        </h1>

        <p class="text-sm text-gray-500 mt-2 tracking-wide">
            Security Services PT. Abimanyu Sekar Nusantara.
        </p>

        <!-- GARIS HIASAN -->
        <div class="mx-auto w-full h-1 bg-teal-600 rounded mt-3"></div>
    </div>

     <div class="mb-6">
    <div class="flex items-center mb-3">
      <div class="w-1.5 h-5 bg-teal-600 rounded mr-2"></div>
      <h3 class="text-sm font-semibold text-gray-800">Pilih Layanan</h3>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚻</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Verifikasi Identitas Tamu</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚶</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Verifikasi Surat Izin Keluar</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚗</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Verifikasi Surat Dinas Luar</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">👷</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Patrol</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📦</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Pencatatan Barang Masuk</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">☕</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Estimasi Porsi Catering</span>
      </a>
    </div>
  </div>

   

    </div>

</div>


 <div id="cateringPanel" class="p-4 bg-white rounded-t-lg mt-4 mb-12 animate-fadeIn">
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


 

 <!-- =============================== -->
<!-- Filter Awal & Akhir Absen Masuk -->
<!-- =============================== -->
<div class="flex flex-col gap-4 mb-6">

  <!-- Awal Absen Masuk -->
  <div class="flex flex-col">
    <label class="text-sm font-medium text-gray-600 mb-1">
      Awal Absen Masuk
    </label>
    <input
      type="datetime-local"
      id="filterStartDatetime"
      class="w-full sm:w-96 border border-gray-300 rounded-md px-2 py-1 text-sm
             focus:ring focus:ring-blue-200 focus:outline-none"
    >
  </div>

  <!-- Akhir Absen Masuk -->
  <div class="flex flex-col">
    <label class="text-sm font-medium text-gray-600 mb-1">
      Akhir Absen Masuk
    </label>
    <input
      type="datetime-local"
      id="filterEndDatetime"
      class="w-full sm:w-96 border border-gray-300 rounded-md px-2 py-1 text-sm
             focus:ring focus:ring-blue-200 focus:outline-none"
    >
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

<div id="barangPanel" class="p-4 bg-white rounded-t-lg mt-2 animate-fadeIn">

 <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Sistem Pengecekan Barang</h2>
    <p class="text-xs text-gray-500 mt-1">Keamanan Dimulai dari Pintu Masuk.</p>
    <div class="w-14 h-1 bg-teal-600 rounded mt-2"></div>
  </div>
  <!-- === SEARCH BAR + ICONS === -->
<div class="flex items-center gap-3 mb-4">

    <!-- SEARCH INPUT -->
    <div class="relative flex-1">
        <input
            type="text"
            id="searchInput"
            placeholder="Search barang..."
            class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-12 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >
        <i data-feather="search" class="absolute left-3 top-2.5 w-5 h-5 text-gray-500"></i>

        <!-- FILTER ICON -->
        <button id="openFilterBtn"
            class="absolute right-3 top-2.5 text-gray-600 hover:text-blue-600">
            <i data-feather="sliders" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- SELECT MODE -->
    <button id="selectModeBtn"
        class="p-2 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600">
        <i data-feather="check-square" class="w-5 h-5"></i>
    </button>

    <!-- EDIT MODE -->
    <button id="editModeBtn"
        class="p-2 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600">
        <i data-feather="edit-3" class="w-5 h-5"></i>
    </button>

</div>
<div class="w-full h-[calc(100vh-150px)] overflow-y-auto overflow-x-auto">
    <table id="barangTable" class="w-full border-collapse whitespace-nowrap">

        <thead class="sticky top-0 bg-blue-500 text-white">
            <tr>
                <th class="p-2 text-left">Tanggal</th>
                <th class="p-2 text-left">Jenis Barang</th>
                <th class="p-2 text-center">Jam Kedatangan</th>
                <th class="p-2 text-center">Jam Keluar</th>
                <th class="p-2 text-center">No. Kendaraan</th>
                <th class="p-2 text-center">Identitas</th>
                <th class="p-2 text-left">Perusahaan/Alamat</th>
                <th class="p-2 text-left">Nama Pengirim</th>
                <th class="p-2 text-left">Nama Penerima</th>
                <th class="p-2 text-center">Bukti Surat Jalan</th>
            </tr>
        </thead>

        <tbody id="barangBody"></tbody>
    </table>
</div>


<!-- 🌟 Floating Add Button -->
<button id="barangAdd"
  class="fixed bottom-20 right-4 bg-blue-600 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center hover:bg-blue-700 transform hover:scale-110 transition-all duration-300 z-50">
  <i data-feather="plus" class="w-6 h-6"></i>
</button>
<!-- 🌟 OVERLAY -->
<div id="sidebarOverlay"
  class="fixed inset-0 bg-black bg-opacity-40 hidden z-80 transition-opacity"></div>

<!-- 🌟 SIDEBAR -->
<div id="sidebarForm"
  class="fixed top-0 right-0 h-full w-full lg:w-[50%] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-70 flex flex-col">

  <!-- ⭐ HEADER -->
  <div class="p-6 border-b flex justify-between items-center bg-white sticky top-0 z-10">
    <h2 class="text-xl font-semibold">Tambah Barang</h2>
    <button id="closeSidebar" class="text-gray-500 hover:text-gray-700">
      <i data-feather="x" class="w-5 h-5"></i>
    </button>
  </div>

  <!-- ⭐ CONTENT (SCROLLABLE) -->
  <div class="flex-1 overflow-y-auto p-6 space-y-6">

    <form id="formInput" class="space-y-6">
      @csrf
<input type="hidden" name="security_good_id" id="security_good_id" value="{{ $securityGood->id ?? '' }}">

     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
      <label class="block text-sm font-medium mb-1">Jenis Barang</label>
      <select name="jenis_barang" class="w-full border rounded-lg p-2">
        <option value="">-- Pilih Jenis Barang --</option>
        <option value="Raw Material (Part)">Raw Material (Part)</option>
        <option value="Pembelian Barang (PO)">Pembelian Barang (PO)</option>
        <option value="Peminjaman Barang Sementara (PBS)">Peminjaman Barang Sementara (PBS)</option>
        <option value="Lainnya">Lainnya</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Tanggal Kedatangan</label>
      <input type="date" name="tanggal" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Kedatangan</label>
      <input type="time" name="jam_masuk" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Keluar</label>
      <input type="time" name="jam_keluar" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Perusahaan/Alamat Pengirim</label>
      <input type="text" name="perusahaan" class="w-full border rounded-lg p-2">
    </div>

      <div>
      <label class="block text-sm font-medium mb-1">No. Kendaraan (Plat Nomor)</label>
      <input type="text" name="nomor_kendaraan" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Identitas Pengirim</label>
      <select name="identitas" class="w-full border rounded-lg p-2">
        <option value="">-- Pilih Jenis Identitas --</option>
        <option value="KTP">KTP</option>
        <option value="ID Card">ID Card</option>
        <option value="SIM">SIM</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Nama Pengirim</label>
      <input type="text" name="nama_pengirim" class="w-full border rounded-lg p-2">
    </div>


    <div>
      <label class="block text-sm font-medium mb-1">Nama Penerima (Pengecekan)</label>
      <input type="text" name="nama_penerima" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Upload Foto Surat Jalan</label>
      <input type="file" name="surat_jalan" accept="image/*" class="w-full border rounded-lg p-1.5">
    </div>

</div>

      <!-- 🔶 SECTION ITEM LIST -->
    <div class="mt-4">
  <h3 class="text-lg font-semibold mb-3">Daftar Barang</h3>
<div class="overflow-x-auto">
  <table class="min-w-max text-sm" id="itemTable">
    <thead class="bg-blue-500 text-white">
      <tr>
        <th class="border p-2 w-48">Nama Barang</th>
        <th class="border p-2 w-24">Jumlah</th>
        <th class="border p-2 w-24">Foto</th>
        <th class="border p-2 w-32">Kondisi</th>
        <th class="border p-2">Catatan</th>
        <th class="border p-2 w-24">#</th>
      </tr>
    </thead>
    <tbody id="itemBody"></tbody>
  </table>
</div>


  <button type="button"
    id="addRow"
    class="mt-3 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-all">
    + Tambah Item
  </button>

</div>


    </form>

  </div>

  <!-- ⭐ FOOTER -->
  <div class="p-4 border-t bg-white sticky bottom-0 z-10">
    <button form="formInput"
      class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-all">
      Simpan
    </button>
  </div>

</div>


<!-- 🌟 SIDEBAR -->
<div id="filterSidebar"
  class="fixed top-0 right-0 h-full w-[50%] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-70 flex flex-col">

  <!-- ⭐ HEADER -->
  <div class="p-6 border-b flex justify-between items-center bg-white sticky top-0 z-10">
    <h2 class="text-xl font-semibold">Filter Barang</h2>
    <button id="closeFilterBtn" class="text-gray-500 hover:text-gray-700">
      <i data-feather="x" class="w-5 h-5"></i>
    </button>
  </div>

  <!-- ⭐ CONTENT (SCROLLABLE) -->
  <div class="flex-1 overflow-y-auto p-6 space-y-6">

    <form id="formInput" class="space-y-6">
      @csrf
<input type="hidden" name="security_good_id" id="security_good_id" value="{{ $securityGood->id ?? '' }}">

     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="col-span-2">
      <label class="block text-sm font-medium mb-1">Jenis Barang</label>
      <select name="jenis_barang" class="w-full border rounded-lg p-2">
        <option value="">-- Pilih Jenis Barang --</option>
        <option value="Raw Material (Part)">Raw Material (Part)</option>
        <option value="Pembelian Barang (PO)">Pembelian Barang (PO)</option>
        <option value="Peminjaman Barang Sementara (PBS)">Peminjaman Barang Sementara (PBS)</option>
        <option value="Lainnya">Lainnya</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Tanggal Kedatangan</label>
      <input type="date" name="tanggal" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Kedatangan</label>
      <input type="time" name="jam_masuk" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Keluar</label>
      <input type="time" name="jam_keluar" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Perusahaan/Alamat Pengirim</label>
      <input type="text" name="perusahaan" class="w-full border rounded-lg p-2">
    </div>

      <div>
      <label class="block text-sm font-medium mb-1">No. Kendaraan (Plat Nomor)</label>
      <input type="text" name="nomor_kendaraan" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Identitas Pengirim</label>
      <select name="identitas" class="w-full border rounded-lg p-2">
        <option value="">-- Pilih Jenis Identitas --</option>
        <option value="KTP">KTP</option>
        <option value="ID Card">ID Card</option>
        <option value="SIM">SIM</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Nama Pengirim</label>
      <input type="text" name="nama_pengirim" class="w-full border rounded-lg p-2">
    </div>


    <div>
      <label class="block text-sm font-medium mb-1">Nama Penerima (Pengecekan)</label>
      <input type="text" name="nama_penerima" class="w-full border rounded-lg p-2">
    </div>

   

</div>


    </form>

  </div>

  <!-- ⭐ FOOTER -->
  <div class="p-4 border-t bg-white sticky bottom-0 z-10">
    <button form="formInput"
      class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-all">
      Filter
    </button>
  </div>

</div>


<!-- === SIDEBAR FILTER (KANAN) === -->
<div id="filterSideba"
     class="fixed top-0 right-0 w-72 h-full bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">

    <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Filter Barang</h2>
        <button id="closeFilteBtn" class="text-gray-600 hover:text-red-500">
            <i data-feather="x" class="w-6 h-6"></i>
        </button>
    </div>

    <div class="p-4">
        <!-- Tambahkan filter yang Anda perlukan -->
        <label class="block mb-1 text-sm">Jenis Barang</label>
        <select class="w-full border rounded-lg p-2 mb-4">
            <option value="">-- Semua Barang --</option>
            <option value="Raw Material (Part)">Raw Material (Part)</option>
            <option value="Pembelian Barang (PO)">Pembelian Barang (PO)</option>
            <option value="Peminjaman Barang Sementara (PBS)">Peminjaman Barang Sementara (PBS)</option>
            <option value="Lainnya">Lainnya</option>
        </select>

         <div>
      <label class="block text-sm font-medium mb-1">Tanggal Kedatangan</label>
      <input type="date" name="tanggal" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Kedatangan</label>
      <input type="time" name="jam_masuk" class="w-full border rounded-lg p-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Jam Keluar</label>
      <input type="time" name="jam_keluar" class="w-full border rounded-lg p-2">
    </div>

        <label class="block mb-1 text-sm">Status Stok</label>
        <select class="w-full border rounded-lg p-2">
            <option>Semua</option>
            <option>Stok Tersedia</option>
            <option>Habis</option>
        </select>
    </div>
</div>

</div>

<!-- Floating Bottom Navigation (Mobile Only) -->
<nav id="bottomNav" class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-lg z-50">
  <div class="relative flex justify-around items-center">

    <!-- Visitor -->
    <a href="#visitor"
       class="nav-item flex flex-col items-center justify-center py-3 w-full text-gray-500 hover:text-blue-500 transition duration-200">
      <i data-feather="user" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Visitor</span>
    </a>

    

    <!-- Izin (beri jarak dari Home) -->
    <a href="#izin"
       class="nav-item mx-4 flex flex-col items-center justify-center py-3 w-full text-gray-500 hover:text-blue-500 transition duration-200">
      <i data-feather="clock" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Izin</span>
    </a>

    <!-- === HOME (ABSOLUTE FLOATING BUTTON) === -->
    <div class="absolute -top-7 left-1/2 -translate-x-1/2">
      <a href="#home" class="flex flex-col items-center justify-center">
        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center shadow-xl border-4 border-white">
          <i data-feather="home" class="w-8 h-8 text-white"></i>
        </div>
      
      </a>
    </div>

    <!-- Barang (beri jarak dari Home) -->
    <a href="#barang"
       class="nav-item mx-4 flex flex-col items-center justify-center py-3 w-full text-gray-500 hover:text-blue-500 transition duration-200">
      <i data-feather="package" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Barang</span>
    </a>

    <!-- Catering -->
    <a href="#catering"
       class="nav-item flex flex-col items-center justify-center py-3 w-full text-gray-500 hover:text-blue-500 transition duration-200">
      <i data-feather="coffee" class="w-5 h-5 mb-1"></i>
      <span class="text-xs font-medium">Catering</span>
    </a>

  </div>
</nav>





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

 let globalData = []; // SIMPAN DATA GLOBAL

// ======================================================
//  LOAD DATA AWAL
// ======================================================
function loadBarang() {
    $.ajax({
        url: "{{ route('barang.list') }}",
        method: "GET",
        success: function(res) {
            if (!res.success) return;

            globalData = res.data; // SIMPAN DATA GLOBAL
            renderBarang(globalData); // RENDER PERTAMA
        }
    });
}

function renderBarang(data) {
          let rows = "";
let lastDate = "";

// Urutkan dari terbaru ke terlama
data.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));


data.forEach(item => {

    // Jika tanggal berubah → tampilkan header tanggal
    if (item.tanggal !== lastDate) {
        rows += `
            <tr class="bg-gray-100">
                <td colspan="10" class="p-2 font-bold text-gray-700 border-l">
                   ${item.tanggal}
                </td>
            </tr>
        `;
        lastDate = item.tanggal;
    }

   rows += `
   <tr class="border-b accordion-row cursor-pointer" data-id="${item.id}">
        <!-- Tanggal (normal) -->
        <td class="p-2 font-semibold bordder-l">${item.tanggal ?? '-'}</td>

        <!-- Jenis Barang (UPPERCASE) -->
        <td class="p-2 text-gray-900 font-bold">
            ${(item.jenis_barang ?? '-').toUpperCase()}
        </td>

      <!-- JAM MASUK (text-center) -->
<td class="p-2 text-left">
    <i class="fa-regular fa-clock text-green-600 mr-1 text-lg"></i>
    <span class="text-green-700 font-semibold">
        ${item.jam_masuk ? item.jam_masuk.substring(0,5) : '-'} WIB
    </span>
</td>

<!-- JAM KELUAR (text-center) -->
<td class="p-2 text-left">
    <i class="fa-regular fa-clock text-red-600 mr-1 text-lg"></i>
    <span class="text-red-700 font-semibold">
        ${item.jam_keluar ? item.jam_keluar.substring(0,5) : '-'} WIB
    </span>
</td>


        <!-- Nomor Kendaraan (UPPERCASE, left) -->
        <td class="p-2 text-left">
            <i class="fa-solid fa-car text-indigo-900 text-lg mr-2"></i>
            <span class="text-indigo-900 font-semibold">
                ${(item.nomor_kendaraan ?? '-').toUpperCase()}
            </span>
        </td>

        <!-- Identitas (UPPERCASE, left) -->
        <td class="p-2 text-left">
            <i class="fa-solid fa-id-card text-indigo-900 text-lg mr-2"></i>
            <span class="text-indigo-900 font-semibold">
                ${(item.identitas ?? '-').toUpperCase()}
            </span>
        </td>

        <!-- Perusahaan (UPPERCASE, left) -->
        <td class="p-2 text-left">
            <i class="fa-solid fa-building text-indigo-900 text-lg mr-2"></i>
            <span class="text-indigo-900 font-semibold">
                ${(item.perusahaan ?? '-').toUpperCase()}
            </span>
        </td>

        <!-- Pengirim (UPPERCASE, left) -->
        <td class="p-2 text-left">
            <i class="fa-solid fa-user text-indigo-900 text-md mr-2"></i>
            <span class="text-indigo-900 font-semibold">
                ${(item.nama_pengirim ?? '-').toUpperCase()}
            </span>
        </td>

        <!-- Penerima (UPPERCASE, left) -->
        <td class="p-2 text-left">
            <i class="fa-solid fa-user text-indigo-900 text-md mr-2"></i>
            <span class="text-indigo-900 font-semibold">
                ${(item.nama_penerima ?? '-').toUpperCase()}
            </span>
        </td>

            <td class="p-2 text-center">
                ${item.surat_jalan ? `
                <div class="flex items-center justify-center gap-2">

                    <!-- WATCH (hanya tampil saat desktop) -->
                    <button class="watchBtn bg-blue-500 text-white px-2 py-1 rounded text-xs hidden md:inline-block"
                            data-img="/surat_jalan/${item.surat_jalan}">
                        Watch
                    </button>

                   <!-- DOWNLOAD -->
<a href="/surat_jalan/${item.surat_jalan}"
   download
   class="bg-green-600 text-white px-2 py-1 rounded text-xs">
   Download
</a>

                </div>
                ` : '-'}
            </td>
        </tr>
 <!-- ====== DETAIL (ITEMS) ====== -->
                <tr class="accordion-detail hidden bg-gray-50" data-id="${item.id}">
                    <td colspan="10" class="p-4">

                        <div class="grid grid-cols-1 gap-4">
                `;

         // ====== LOOP ITEMS (1 BARIS PER BARANG, KOMPAK & MODERN) ======
item.items.forEach(d => {
    rows += `
        <div class="flex bg-yellow-50 rounded-xl items-center gap-4 border p-2">

            <!-- FOTO BARANG -->
            <img src="/barang_item/${d.foto}" 
                 class="w-20 h-20 object-cover rounded shadow">

            <!-- INFORMASI BARANG -->
            <div class="flex-1 grid grid-cols-1 gap-2">

                <p class="text-sm">
                    <span class="font-semibold text-gray-900">Nama Barang:</span>
                    ${d.nama_barang ?? '-'}
                </p>

                <p class="text-sm">
                    <span class="font-semibold text-gray-900">Jumlah:</span>
                    ${d.jumlah ?? '-'} pcs
                </p>

                 <p class="text-sm">
                    <span class="font-semibold text-gray-900">Kondisi:</span>
                    ${d.kondisi ?? '-'}
                </p>

                <p class="text-sm">
                    <span class="font-semibold text-gray-900">Catatan:</span>
                    ${d.catatan ?? '-'}
                </p>
            </div>
        </div>
    `;
});



                rows += `
                        </div>
                    </td>
                </tr>


`;
});

$("#barangBody").html(rows);
feather.replace();
}

// ======================================================
//  SEARCH FILTER (REALTIME)
// ======================================================
document.getElementById("searchInput").addEventListener("input", function () {
    const keyword = this.value.toLowerCase();

    const filtered = globalData.filter(row => {
        const parentMatch = JSON.stringify(row).toLowerCase().includes(keyword);

        const itemMatch = row.items.some(d =>
            (d.nama_barang ?? "").toLowerCase().includes(keyword) ||
            (d.kondisi ?? "").toLowerCase().includes(keyword) ||
            (d.catatan ?? "").toLowerCase().includes(keyword)
        );

        return parentMatch || itemMatch;
    });

    renderBarang(filtered); // TANPA AJAX ULANG
});



// FIRST LOAD
loadBarang();


$(document).on('click', '.accordion-row', function () {
    const id = $(this).data('id');
    const detailRow = $(`.accordion-detail[data-id="${id}"]`);
    detailRow.toggleClass('hidden');
});

$(document).ready(function () {
  
loadBarang()
    feather.replace();

    // Fungsi OPEN sidebar
    function openSidebar() {
        $("#sidebarForm").removeClass("translate-x-full");
        $("#sidebarOverlay").removeClass("hidden");
        $("#bottomNav").hide(); // sembunyikan bottom nav
         $("#barangAdd").hide(); // sembunyikan bottom nav
    }

    // Fungsi CLOSE sidebar
    function closeSidebar() {
        $("#sidebarForm").addClass("translate-x-full");
        $("#sidebarOverlay").addClass("hidden");
        $("#bottomNav").show(); // tampilkan lagi
         $("#barangAdd").show(); // sembunyikan bottom nav
    }

    // Trigger open
    $("#barangAdd, #openSidebar").on("click", openSidebar);

    // Trigger close
    $("#closeSidebar, #sidebarOverlay").on("click", closeSidebar);

});




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

   $("#btnFilterCatering").on("click", function () {

    let startDatetime = $("#filterStartDatetime").val();
    let endDatetime   = $("#filterEndDatetime").val();

    // VALIDASI INPUT
    if (!startDatetime || !endDatetime) {
        Swal.fire({
            icon: "warning",
            title: "Filter belum lengkap",
            text: "Silakan isi Awal dan Akhir Absen Masuk terlebih dahulu."
        });
        return;
    }

    if (startDatetime > endDatetime) {
        Swal.fire({
            icon: "error",
            title: "Rentang waktu tidak valid",
            text: "Akhir absen tidak boleh lebih awal dari awal absen."
        });
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
            start_datetime: startDatetime,
            end_datetime: endDatetime
        },
        success: function (res) {

            if (!res || res.length === 0) {
                Swal.fire({
                    icon: "info",
                    title: "Data tidak ditemukan",
                    text: "Tidak ada data absen pada rentang waktu tersebut."
                });
                return;
            }

            $("#cateringTableBody").empty();

            res.forEach((row, index) => {
                let inTime  = row.in_time || "-";
                let outTime = row.out_time || "-";

                let rowClass = index % 2 === 0 ? "bg-white" : "bg-gray-50";

                $("#cateringTableBody").append(`
                    <tr class="${rowClass}">
                        <td class="py-2 px-3 text-center">${index + 1}</td>
                        <td class="py-2 px-3">${row.name}</td>
                        <td class="py-2 px-3">${row.nik}</td>
                        <td class="py-2 px-3">${inTime}</td>
                        <td class="py-2 px-3">${outTime}</td>
                    </tr>
                `);
            });

            $("#cateringCount").text(res.length);
        }
    });

});


});


});

// === Sidebar Filter ===
document.getElementById('openFilterBtn').onclick = () => {
    document.getElementById('filterSidebar').classList.remove('translate-x-full');
};

document.getElementById('closeFilterBtn').onclick = () => {
    document.getElementById('filterSidebar').classList.add('translate-x-full');
};

// === SELECT MODE ===
let selectMode = false;
document.getElementById('selectModeBtn').onclick = () => {
    selectMode = !selectMode;
    const rows = document.querySelectorAll('#barangTable tbody tr');

    if (selectMode) {
        rows.forEach(r => {
            r.classList.add("cursor-pointer");
            r.onclick = () => r.classList.toggle("bg-blue-100");
        });
    } else {
        rows.forEach(r => {
            r.classList.remove("cursor-pointer", "bg-blue-100");
            r.onclick = null;
        });
    }
};

// === EDIT MODE ===
let editMode = false;
document.getElementById('editModeBtn').onclick = () => {
    editMode = !editMode;
    const cells = document.querySelectorAll('#barangTable tbody td');

    if (editMode) {
        cells.forEach(c => c.setAttribute("contenteditable", "true"));
    } else {
        cells.forEach(c => c.removeAttribute("contenteditable"));
    }
};

$(document).ready(function () {
// --- SEMBUNYIKAN SEMUA PANEL DI AWAL ---
$("#homePanel").show();
$("#barangPanel").hide();
$("#cateringPanel").hide();

function openPanel(panelId, buttonAnchor) {
    // Sembunyikan semua panel TERMASUK home
    $("#homePanel, #barangPanel, #cateringPanel").hide();

    // Tampilkan panel yang dipilih
    $(panelId).fadeIn(200);

    // Reset semua tombol nav
    $(".nav-item")
        .removeClass("text-blue-600 font-semibold")
        .addClass("text-gray-500");

    // Highlight tombol aktif
    $(buttonAnchor)
        .removeClass("text-gray-500")
        .addClass("text-blue-600 font-semibold");

    feather.replace();
}

// --- EVENT CLICK NAVBAR ---
$("a[href='#barang']").click(function () {
    openPanel("#barangPanel", this);
});

$("a[href='#catering']").click(function () {
    openPanel("#cateringPanel", this);
});

$("a[href='#home']").click(function () {
    // Sembunyikan semua panel
    $("#barangPanel, #cateringPanel").hide();

    // Tampilkan home
    $("#homePanel").fadeIn(200);

    // Styling tombol
    $(".nav-item")
        .removeClass("text-blue-600 font-semibold")
        .addClass("text-gray-500");

    $(this)
        .removeClass("text-gray-500")
        .addClass("text-blue-600 font-semibold");

    feather.replace();
});


});
$(document).ready(function () {

    feather.replace();

    // Tambah Row Item
    $("#addRow").on("click", function () {
       let row = `
<tr>
    <td class="border p-2 w-48">
        <input type="text" name="nama_barang[]" class="w-full border rounded p-1">
    </td>

    <td class="border p-2 w-20">
        <input type="number" name="jumlah[]" class="w-full border rounded p-1" min="1">
    </td>

    <td class="border p-2 w-28">
        <input type="file" name="foto[]" class="w-full border rounded p-1">
    </td>

    <td class="border p-2 w-32">
        <select name="kondisi[]" class="w-full border rounded p-1">
            <option value="Normal">Normal</option>
            <option value="Rusak">Rusak</option>
        </select>
    </td>

    <td class="border p-2 w-40">
        <input type="text" name="catatan[]" class="w-full border rounded p-1">
    </td>

    <td class="border p-2 text-center w-14">
        <button type="button" class="text-red-500 deleteRow">x</button>
    </td>
</tr>
`;

        $("#itemBody").append(row);
    });

    // Hapus Row
    $(document).on("click", ".deleteRow", function () {
        $(this).closest("tr").remove();
    });

    // 🔥 DEFAULT TAMBAH 1 BARIS SAAT LOAD
    $("#addRow").click();

    $("#formInput").submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('store.barang') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (res) {
            Swal.close();

            if (res.success) {

              let securityGoodId = res.id;   // ⬅ ID dari parent

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                });

                $("#formInput")[0].reset();
                $("#itemBody").empty();
                $("#addRow").click();
                loadBarang()
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message
                });
            }
        },
        error: function (xhr) {
            Swal.close();

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message ?? "Terjadi kesalahan"
            });
        }
    });
});

});

  </script>
@endpush


@endsection