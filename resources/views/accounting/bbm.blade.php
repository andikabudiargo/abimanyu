@extends('layouts.app')

@section('title', 'Fuel Calculator')
@section('page-title', 'FUEL CALCULATOR')
@section('breadcrumb-item', 'Supporting Tools')
@section('breadcrumb-active', 'Fuel Calculator')

@section('content')

<div class="flex flex-col md:flex-row gap-2 bg-gray-100">

  <!-- === CARD ARMADA === -->
<div class="md:w-3/5 w-full bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
  <div class="mb-6">
    <!-- Bagian Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <!-- Kiri: Icon besar + Judul -->
      <div class="flex items-center gap-4">
        <div class="bg-blue-100 p-4 rounded-2xl flex items-center justify-center">
          <i class="fa-solid fa-truck text-4xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Data Armada</h2>
          <p class="text-sm text-gray-500">Kelola daftar armada yang tersedia</p>
          <div class="w-20 h-1 bg-blue-600 rounded mt-2"></div>
        </div>
      </div>

      <!-- Tombol responsif -->
      <button onclick="openModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-2 flex items-center justify-center gap-2 shadow-md transition">
        <i class="fa-solid fa-plus"></i>
        <span class="hidden md:inline-block font-medium text-sm">Tambah Armada</span>
      </button>
    </div>
  </div>

  <!-- === LIST ARMADA SCROLLABLE === -->
  <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto pr-2">
    @forelse($armadas as $armada)
      <div class="py-4 px-3 flex justify-between items-center rounded-xl transition even:bg-gray-50 odd:bg-white hover:bg-blue-50">
        <!-- Kiri: Icon + Info Armada -->
        <div class="flex items-center gap-3">
          <div class="bg-blue-100 p-3 rounded-xl shadow-sm">
            <i class="fa-solid {{ $armada->icon ?? 'fa-truck' }} text-blue-600 text-xl"></i>
          </div>
          <div>
            <p class="font-semibold text-gray-800 text-sm md:text-base">
              {{ $armada->nama_armada }}
            </p>
            <p class="text-xs text-gray-500">
              {{ $armada->rasio }} • {{ $armada->nama_bbm ?? '-' }}
            </p>
            <p class="text-xs text-gray-600 mt-1">
              <i class="fa-solid fa-money-bill-1-wave mr-1 text-blue-500"></i>
              Spare:
              <span class="font-semibold">
                Rp {{ number_format($armada->spare, 0, ',', '.') }}
              </span>
            </p>
          </div>
        </div>

        <!-- Kanan: Tombol Aksi -->
        <div class="flex gap-3">
          <button class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <div class="w-px h-8 bg-gray-300"></div>
          <button class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    @empty
      <p class="text-center text-gray-500 py-4">Belum ada data armada.</p>
    @endforelse
  </div>
</div>


  <!-- === CARD BBM === -->
  <div class="md:w-2/5 w-full bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
   <div class="mb-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
    <!-- Kiri: Icon besar + Judul -->
    <div class="flex items-center gap-4 flex-1">
      <div class="bg-green-100 p-4 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-gas-pump text-4xl text-green-600"></i>
      </div>
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Data BBM</h2>
        <p class="text-sm text-gray-500">Kelola BBM yang dipakai armada</p>
        <div class="w-20 h-1 bg-green-600 rounded mt-2"></div>
      </div>
    </div>

    <!-- Tombol responsif -->
    <button onclick="openBbmModal()"
      class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-3 flex items-center justify-center gap-2 shadow-md transition duration-200
             w-full md:w-auto"
    >
      <i class="fa-solid fa-plus text-lg"></i>
      <span class="font-medium text-sm md:hidden">Tambah BBM</span>
    </button>
  </div>
</div>

 <div class="max-w-3xl mx-auto mt-8 space-y-4">
 @foreach($bbms as $bbm)
  <div class="flex justify-between items-center py-4 border-b border-gray-200 last:border-0">
    <!-- Kiri: Info BBM -->
    <div class="flex items-center gap-4">
      <div class="bg-green-100 text-green-600 p-3 rounded-full">
        <i class="fa-solid fa-gas-pump text-lg"></i>
      </div>
      <div>
        <p class="font-semibold text-gray-800 text-base">{{ $bbm->nama_bbm }}</p>
        <p class="text-xs text-gray-500 tracking-wide">
          Rp {{ number_format($bbm->harga_bbm, 0, ',', '.') }} / Liter
        </p>
      </div>
    </div>

    <!-- Kanan: Tombol Aksi -->
    <div class="flex items-center gap-3">
      <button
        class="text-blue-600 hover:text-white hover:bg-blue-600 p-2 rounded-full transition-colors duration-200"
        title="Edit BBM"
      >
        <i class="fa-solid fa-pen-to-square"></i>
      </button>

      <div class="w-px h-6 bg-gray-300"></div>

      <button
        class="text-red-600 hover:text-white hover:bg-red-600 p-2 rounded-full transition-colors duration-200"
        title="Hapus BBM"
      >
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
@endforeach

</div>



  </div>

</div>

<!-- === FORM PERHITUNGAN === -->
<div class="mt-4 bg-white rounded-3xl shadow-xl p-6 md:p-8 hover:shadow-2xl transition-all duration-300">
   <div class="flex items-center gap-4 mb-6">
      <div class="bg-red-100 p-4 rounded-2xl flex items-center justify-center">
        <i class="fa-solid fa-calculator text-4xl text-red-600"></i>
      </div>
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Kalkulator BBM</h2>
        <p class="text-sm text-gray-500">Hitung Biaya Operasional Kendaraan</p>
        <div class="w-20 h-1 bg-red-600 rounded mt-2"></div>
      </div>
    </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">

    <!-- === KIRI: Armada & Info === -->
    <div class="space-y-4">
    <label class="text-gray-600 text-sm font-medium block">Armada</label>
<select id="armadaSelect"
  class="w-full rounded-xl p-3 border border-gray-200 focus:ring-2 focus:ring-blue-300 bg-white shadow-sm hover:shadow-md transition">
  <option value="">-- Pilih Armada --</option>

   @foreach($armadas as $armada)
    <option 
      value="{{ $armada->id }}" 
      data-bbm="{{ $armada->nama_bbm }}" 
      data-rasio="{{ $armada->rasio }}" 
      data-spare="{{ $armada->spare }}" 
      data-harga="{{ $armada->harga_bbm }}" 
      data-icon="{{ $armada->icon }}">
      {{ $armada->nama_armada }}
    </option>
  @endforeach
</select>

<!-- === Info Armada === -->
<div class="space-y-3 mt-4">
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 flex items-center justify-center bg-blue-50 rounded-xl shadow-sm">
      <i class="fa-solid fa-gas-pump text-blue-600 text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-500 text-xs">Jenis BBM</p>
      <p id="bbmInfo" class="font-bold text-gray-800 text-sm">-</p>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 flex items-center justify-center bg-yellow-50 rounded-xl shadow-sm">
      <i class="fa-solid fa-coins text-yellow-600 text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-500 text-xs">Harga BBM (Rp/Liter)</p>
      <p id="hargaInfo" class="font-bold text-gray-800 text-sm">-</p>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 flex items-center justify-center bg-green-50 rounded-xl shadow-sm">
      <i class="fa-solid fa-road text-green-600 text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-500 text-xs">Rasio (km/liter)</p>
      <p id="rasioInfo" class="font-bold text-gray-800 text-sm">-</p>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 flex items-center justify-center bg-red-50 rounded-xl shadow-sm">
      <i class="fa-solid fa-money-bill-1-wave text-red-600 text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-500 text-xs">Spare (Rp)</p>
      <p id="spareInfo" class="font-bold text-gray-800 text-sm">-</p>
    </div>
  </div>
</div>
    </div>

    <!-- === KANAN: Jarak & Total === -->
    <div class="space-y-4">
      <label class="text-gray-600 text-sm font-medium block">Jarak (Km)</label>
      <input id="jarakInput" type="number" placeholder="Masukkan jarak (km)" class="w-full rounded-xl p-3 border border-gray-200 focus:ring-2 focus:ring-purple-300 bg-white shadow-sm hover:shadow-md transition">

      <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 shadow-sm space-y-2">
        <p class="text-gray-500 text-xs font-medium">Total Biaya <span class="text-gray-400 text-xs">(sebelum spare)</span></p>
        <h3 id="totalBBMOutput" class="text-xl md:text-2xl font-bold text-blue-600">Rp 0</h3>

        <p class="text-gray-500 text-xs font-medium">Total Biaya + Spare</p>
        <h3 id="totalAllOutput" class="text-xl md:text-2xl font-bold text-green-700">Rp 0</h3>
      </div>

      <button id="clearBtn" type="button" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-2xl shadow-md transition">
        Clear
      </button>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="armadaModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col max-h-[70vh] overflow-hidden animate-fadeIn relative">

    <!-- Header -->
    <div class="p-6 border-b border-gray-200 relative">
      <h2 class="text-2xl font-bold text-gray-900">Tambah Data Armada</h2>
      <p class="text-sm text-gray-500 mt-1">Lengkapi form di bawah ini untuk menambah data armada</p>
      <div class="w-20 h-1 bg-blue-600 rounded mt-3"></div>

      <!-- Tombol Tutup -->
      <button onclick="closeModal()" class="absolute top-5 right-6 text-red-500 hover:text-red-700 transition">
        <i class="fa-solid fa-xmark text-xl"></i>
      </button>
    </div>

    <!-- Konten Scrollable -->
    <div class="overflow-y-auto p-6 space-y-5 flex-1">
      <!-- Header dengan Icon Preview -->
      <div class="flex flex-col items-center mb-2">
        <div id="previewIconContainer" class="bg-blue-50 p-5 rounded-full shadow-md mb-3 transition-all duration-300">
          <i id="previewIcon" class="fa-solid fa-truck text-blue-600 text-5xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Pilih Icon Armada</h2>
        <p class="text-sm text-gray-500">Pilih salah satu icon kendaraan di bawah ini</p>
      </div>

      <!-- Daftar Icon Armada -->
      <div class="grid grid-cols-6 sm:grid-cols-8 gap-3 max-h-36 overflow-y-auto p-3 border border-gray-200 rounded-xl shadow-inner bg-gray-50">
        <button onclick="setIcon('fa-car')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-car text-xl"></i></button>
        <button onclick="setIcon('fa-truck')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-truck text-xl"></i></button>
        <button onclick="setIcon('fa-truck-pickup')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-truck-pickup text-xl"></i></button>
        <button onclick="setIcon('fa-truck-monster')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-truck-monster text-xl"></i></button>
        <button onclick="setIcon('fa-shuttle-van')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-shuttle-van text-xl"></i></button>
        <button onclick="setIcon('fa-bus')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-bus text-xl"></i></button>
        <button onclick="setIcon('fa-motorcycle')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-motorcycle text-xl"></i></button>
        <button onclick="setIcon('fa-bicycle')" class="p-2 rounded-lg hover:bg-blue-100 transition"><i class="fa-solid fa-bicycle text-xl"></i></button>
      </div>

      <!-- Form Input -->
     <form id="formArmada" class="space-y-4 text-sm">
  <input type="hidden" name="icon" id="selectedIcon" value="fa-truck">

  <div>
    <label class="block text-gray-700 font-medium mb-1">Nama Armada</label>
    <input type="text" name="nama_armada"
      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      placeholder="Contoh: Truk A">
  </div>

  <div>
    <label class="block text-gray-700 font-medium mb-1">Jenis BBM</label>
    <select name="bbm_id"
      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      required>
      <option value="">Pilih BBM</option>
      @foreach($bbms as $bbm)
      <option value="{{ $bbm->id }}">
        {{ $bbm->nama_bbm }} — Rp {{ number_format($bbm->harga_bbm, 0, ',', '.') }}/L
      </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-gray-700 font-medium mb-1">Rasio (Km/L)</label>
    <input type="text" name="rasio"
      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      placeholder="Contoh: 1/11">
  </div>

  <div>
    <label class="block text-gray-700 font-medium mb-1">Spare (Rp)</label>
    <input type="number" name="spare"
      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      placeholder="Contoh: 3500000">
  </div>
</form>

    </div>

    <!-- Footer Fixed -->
    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-3 shadow-inner">
      <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
        Batal
      </button>
      <button type="submit" form="formArmada" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        Simpan
      </button>
    </div>
  </div>
</div>

<!-- Modal Tambah BBM -->
<div id="bbmModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col max-h-[70vh] overflow-hidden animate-fadeIn relative">

    <!-- Header -->
    <div class="p-6 border-b border-gray-200 relative">
      <h2 class="text-2xl font-bold text-gray-900">Tambah Jenis BBM</h2>
      <p class="text-sm text-gray-500 mt-1">Masukkan nama dan harga BBM yang ingin ditambahkan</p>
      <div class="w-20 h-1 bg-blue-600 rounded mt-3"></div>

      <!-- Tombol Tutup -->
      <button onclick="closeBbmModal()" class="absolute top-5 right-6 text-red-500 hover:text-red-700 transition">
        <i class="fa-solid fa-xmark text-xl"></i>
      </button>
    </div>

    <!-- Konten Scrollable -->
    <div class="overflow-y-auto p-6 space-y-5 flex-1">
      <form id="formBBM" class="space-y-4 text-sm pb-24">
        <div>
          <label class="block text-gray-700 font-medium mb-1">Nama BBM</label>
          <input 
            type="text" 
            name="nama_bbm"
            class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="Contoh: Pertalite"
            required>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Harga per Liter (Rp)</label>
          <input 
            type="number" 
            name="harga_bbm"
            step="0.01"
            class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="Contoh: 13500"
            required>
        </div>
      </form>
    </div>

    <!-- Footer Fixed -->
    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-3 shadow-inner">
      <button type="button" onclick="closeBbmModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
        Batal
      </button>
      <button type="submit" form="formBBM" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        Simpan
      </button>
    </div>
  </div>
</div>

<style>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
  }
</style>
@push('scripts')
<script>
const armadaSelect = document.getElementById('armadaSelect');
const bbmInfo = document.getElementById('bbmInfo');
const hargaInfo = document.getElementById('hargaInfo');
const rasioInfo = document.getElementById('rasioInfo');
const spareInfo = document.getElementById('spareInfo');
const jarakInput = document.getElementById('jarakInput');
const totalBBMOutput = document.getElementById('totalBBMOutput');
const totalAllOutput = document.getElementById('totalAllOutput');
const clearBtn = document.getElementById('clearBtn');
  const iconButtons = document.querySelectorAll(".icon-option");
  const selectedIcon = document.getElementById("selectedIcon");
  const previewIcon = document.getElementById("previewIcon");
  const previewIconContainer = document.getElementById("previewIconContainer");

  function openModal() {
  document.getElementById("armadaModal").classList.remove("hidden");
  document.getElementById("armadaModal").classList.add("flex");
}

function closeModal() {
  document.getElementById("armadaModal").classList.add("hidden");
  document.getElementById("armadaModal").classList.remove("flex");
}

  function openBbmModal() {
    document.getElementById('bbmModal').classList.remove('hidden');
    document.getElementById('bbmModal').classList.add('flex');
  }

  function closeBbmModal() {
    document.getElementById('bbmModal').classList.add('hidden');
    document.getElementById('bbmModal').classList.remove('flex');
  }

function setIcon(icon) {
  const preview = document.getElementById("previewIcon");
  preview.className = `fa-solid ${icon} text-5xl text-blue-600 transition-all`;
}


  // === Pilih Icon ===
  iconButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      iconButtons.forEach((b) => b.classList.remove("ring-2", "ring-blue-500", "bg-blue-50"));
      btn.classList.add("ring-2", "ring-blue-500", "bg-blue-50");

      const icon = btn.dataset.icon;
      selectedIcon.value = icon;

      // Ubah icon preview
      previewIcon.className = `fa-solid ${icon} text-blue-600 text-5xl transition-all duration-300 transform scale-110`;
      previewIconContainer.classList.add("bg-blue-100");
      setTimeout(() => previewIconContainer.classList.remove("bg-blue-100"), 500);
    });
  });


function hitungTotal() {
  const selected = armadaSelect.options[armadaSelect.selectedIndex];
  const jarak = parseFloat(jarakInput.value) || 0;
  const rasioStr = selected.dataset.rasio || "0";
  const harga = parseFloat(selected.dataset.harga) || 0;
  const spare = parseFloat(selected.dataset.spare) || 0;

  // parsing rasio "1/11" => 1 ÷ 11
  let rasioDesimal = 0;
  if (rasioStr.includes('/')) {
    const [num, den] = rasioStr.split('/').map(Number);
    rasioDesimal = num / den;
  } else {
    rasioDesimal = parseFloat(rasioStr);
  }

  if (rasioDesimal && harga && jarak) {
    // hitung dan bulatkan 2 angka di belakang koma
    const totalBBM = parseFloat((rasioDesimal * jarak * harga).toFixed(2));
    const totalAll = parseFloat((totalBBM + spare).toFixed(2));

    totalBBMOutput.textContent = formatRupiah(totalBBM);
    totalAllOutput.textContent = formatRupiah(totalAll);
  } else {
    totalBBMOutput.textContent = 'Rp 0';
    totalAllOutput.textContent = 'Rp 0';
  }
}

// update info armada
armadaSelect.addEventListener('change', () => {
  const selected = armadaSelect.options[armadaSelect.selectedIndex];

  bbmInfo.textContent = selected.dataset.bbm || '-';
  rasioInfo.textContent = selected.dataset.rasio || '-';
  spareInfo.textContent = selected.dataset.spare ? formatRupiah(selected.dataset.spare) : '-';
  hargaInfo.textContent = selected.dataset.harga ? formatRupiah(selected.dataset.harga) : '-';

  hitungTotal();
});

// hitung saat input jarak berubah
jarakInput.addEventListener('input', hitungTotal);

// tombol clear
clearBtn.addEventListener('click', () => {
  armadaSelect.value = '';
  bbmInfo.textContent = '-';
  rasioInfo.textContent = '-';
  spareInfo.textContent = '-';
  hargaInfo.textContent = '-';
  jarakInput.value = '';
  totalBBMOutput.textContent = 'Rp 0';
  totalAllOutput.textContent = 'Rp 0';
});

// format rupiah helper (tanpa desimal Rupiah, hanya integer)
function formatRupiah(angka) {
  return 'Rp ' + angka.toLocaleString('id-ID', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}



 function setIcon(iconClass) {
      // Update hidden input agar ikut dikirim ke backend
      $('#selectedIcon').val(iconClass);

      // Update tampilan preview icon
      $('#previewIcon')
        .removeClass() // hapus semua class sebelumnya
        .addClass('fa-solid ' + iconClass + ' text-blue-600 text-5xl');

      // Animasi kecil untuk efek visual
      $('#previewIconContainer')
        .addClass('ring-2 ring-blue-400')
        .delay(300)
        .queue(function(next) {
          $(this).removeClass('ring-2 ring-blue-400');
          next();
        });
  }

$(document).ready(function() {

    // === FORM TAMBAH BBM ===
    $('#formBBM').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('fa.bbm.store') }}",
            type: "POST",
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#bbmModal').addClass('hidden');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data BBM.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                });
            }
        });
    });


    // === FORM TAMBAH ARMADA ===
    $('#formArmada').on('submit', function(e) {
        e.preventDefault();

        // Kirim seluruh form termasuk file/icon
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('fa.armada.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#armadaModal').addClass('hidden');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data Armada.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                });
            }
        });
    });

});
</script>
@endpush
@endsection
