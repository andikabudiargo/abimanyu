@extends('layouts.app')

@section('title', 'Setting')
@section('page-title', 'Setting')
@section('breadcrumb-item', 'Conversion')
@section('breadcrumb-active', 'Setting')

@section('content')

{{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Conversion Value History</h2>
        <button id="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow text-sm">
            + Add New Conversion Value
</button>
    </div>
    <hr>
    <div class="w-full overflow-x-auto" id="conversion-scroll-wrapper">
    <table id="conversion-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Effective Date</th>
                    <th class="px-4 py-2">Conversion Value</th>
                    <th class="px-4 py-2">Created by</th>
                    <th class="px-4 py-2">Created at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
    </div>
</div>

<!-- 🌟 MODAL CREATE WAREHOUSE -->
<!-- BACKDROP -->
<div id="createModal"
     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300">

  <!-- MODAL CARD -->
  <div class="relative w-full max-w-lg rounded-2xl
              bg-white/95 backdrop-blur-xl
              shadow-[0_20px_60px_rgba(0,0,0,0.25)]
              border border-slate-200
              p-8 animate-[fadeIn_.25s_ease]">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h3 class="text-xl font-semibold text-slate-800 tracking-tight">
          Add Conversion Value
        </h3>
        <p class="text-sm text-slate-500 mt-1">
          Update financial conversion parameter
        </p>
      </div>

      <!-- CLOSE ICON -->
      <button id="closeModalIcon"
        class="w-9 h-9 flex items-center justify-center rounded-lg
               text-slate-400 hover:text-red-500
               hover:bg-slate-100 transition">
        ✕
      </button>
    </div>

    <!-- FORM -->
    <form id="createConversionForm" class="space-y-5">
@csrf
      <!-- Conversion Value -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
          Conversion Value
        </label>

        <input type="text" name="value"
          class="w-full rounded-xl border border-slate-300
                 bg-slate-50 px-4 py-3
                 text-slate-800
                 focus:bg-white
                 focus:border-indigo-500
                 focus:ring-4 focus:ring-indigo-100
                 transition-all outline-none"
          placeholder="e.g. 141000"
          required>
      </div>

      <!-- Effective Date -->
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
          Effective Date
        </label>

        <input type="date" name="effective_date"
         min="{{ date('Y-m-d') }}"
          class="w-full rounded-xl border border-slate-300
                 bg-slate-50 px-4 py-3
                 focus:bg-white
                 focus:border-indigo-500
                 focus:ring-4 focus:ring-indigo-100
                 transition-all outline-none"
          required>
      </div>

      <!-- ACTION BUTTON -->
      <div class="flex justify-end gap-3 pt-4">

        <button type="button" id="closeModalBtn"
          class="px-5 py-2.5 rounded-xl
                 border border-slate-300
                 text-slate-600 font-medium
                 hover:bg-slate-100
                 transition">
          Cancel
        </button>

        <button type="submit" id='submitBtn'
          class="px-6 py-2.5 rounded-xl font-semibold text-white
                 bg-gradient-to-r from-indigo-600 to-blue-600
                 hover:from-indigo-700 hover:to-blue-700
                 shadow-lg shadow-indigo-200
                 transition-all duration-200
                 active:scale-[0.98]">
          Save
        </button>

      </div>
    </form>
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

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#conversion-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}

</style>

<script>
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


$('#createConversionForm').off('submit').on('submit', function (e) {
    e.preventDefault();

      const $form = $(this);
    const $btn = $('#submitBtn');

    // Disable tombol untuk mencegah klik ganda
    $btn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
    const originalText = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    const formData = new FormData(this);

    $.ajax({
        url: '{{ route ("marketing.setting.store") }}',
        method: 'POST', // Laravel butuh _method PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'Conversion Value successfully Saved!');
                setTimeout(() => {
                    window.location.href = '{{ route("marketing.setting.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal perbaharui Conversion Value.');
                  $btn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').html(originalText);
            }
        },
        error: function (err) {
            console.error(err.responseText);
            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
            showToast('error', msg);
            $btn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').html(originalText);
        }
    });
});

  const openModalBtn = document.getElementById('openCreateModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const closeModalIcon = document.getElementById('closeModalIcon');
  const createModal = document.getElementById('createModal');

  openModalBtn.addEventListener('click', () => {
    createModal.classList.remove('hidden');
  });

  [closeModalBtn, closeModalIcon].forEach(btn => {
    btn.addEventListener('click', () => {
      createModal.classList.add('hidden');
    });
  });


  $(document).ready(function () {
    const table = $('#conversion-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
      ajax: '{{ route("marketing.setting.data") }}',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"f>>rt<"flex justify-between items-center"ip>',
      columns: [
         { data: 'effective_date', name: 'effective_date' },
        { data: 'value', name: 'value' },
        { data: 'created_by', name: 'created_by' },
        { data: 'created_at', name: 'created_at' },
      ]
    });
  });
  let openDropdown = null;

function toggleDropdown(id) {
  const dropdown = document.getElementById(id);

  // Tutup dropdown lain
  if (openDropdown && openDropdown !== dropdown) {
    openDropdown.classList.add('hidden');
  }

  dropdown.classList.toggle('hidden');
  openDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function (e) {
  if (openDropdown && !openDropdown.contains(e.target)) {
    const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
    if (!isTrigger) {
      openDropdown.classList.add('hidden');
      openDropdown = null;
    }
  }
  });
</script>
@endpush


@endsection