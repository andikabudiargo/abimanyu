@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@push('styles')
<style>
    /* Tabel wajib memakai lebar penuh area konten (termasuk saat sidebar ditutup).
       DataTables kadang menyisakan lebar kosong bila kolom dihitung dari lebar awal. */
    #employee-table.dataTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    /* DataTables membawa border header hitam (#111); diganti abu tipis sesuai bahasa app. */
    #employee-table.dataTable thead th,
    #employee-table.dataTable.no-footer {
        border-bottom: 1px solid #d1d5db; /* gray-300 */
    }

    /* Garis pemisah baris tipis agar data mudah dipindai. */
    #employee-table.dataTable tbody td {
        border-top: 1px solid #e5e7eb; /* gray-200 */
    }

    /* Zebra baris (selang-seling) — dulu melekat sebagai class Tailwind odd:/even: pada
       tiap <tr> hardcoded; sekarang baris digenerate DataTables, jadi diatur lewat CSS.
       Diletakkan SEBELUM aturan hover agar hover tetap menang saat specificity seri. */
    #employee-table.dataTable tbody > tr.even > td {
        background-color: #f3f4f6; /* gray-100, sama dengan tabel APD di halaman detail */
    }

    /* Hover baris: biru muda (blue-50), menggantikan efek gelap bawaan template. */
    #employee-table.dataTable tbody tr:hover > td {
        background-color: #eff6ff; /* blue-50 */
    }

    /* Pagination: meniru pagination default DataTables 1.13.6 yang dipakai halaman lain
       (department/supplier) — kotak kecil, jarak rapat, tanpa warna. Versi ini sengaja
       monokrom (putih/abu) dan TIDAK memakai hover hitam bawaan DataTables (#111).
       Selector diawali #employee-table_wrapper (id wrapper buatan DataTables) karena CSS
       DataTables dimuat di footer, yaitu SETELAH @stack('styles') di head — tanpa id,
       specificity-nya seri dan aturan CDN yang menang. */
    #employee-table_wrapper .dataTables_paginate .paginate_button {
        box-sizing: border-box;
        display: inline-block;
        min-width: 1.5rem;
        padding: 0.375rem 0.75rem; /* kecil & rapat, tinggi total ±35px */
        margin-left: 2px; /* jarak antar tombol rapat seperti default DataTables */
        text-align: center;
        font-size: 0.875rem;
        line-height: 1.5;
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.25rem;
        background: #ffffff; /* shorthand: menimpa gradient hitam DataTables (background-image) */
        color: #374151 !important; /* gray-700 */
        cursor: pointer;
    }

    /* Hover: latar abu sangat muda, teks tetap gelap (bukan biru, bukan hitam). */
    #employee-table_wrapper .dataTables_paginate .paginate_button:hover,
    #employee-table_wrapper .dataTables_paginate .paginate_button:focus {
        background: #f9fafb !important; /* gray-50, shorthand agar gradient CDN ikut tertimpa */
        border-color: #d1d5db !important; /* gray-300 */
        color: #374151 !important; /* gray-700 */
        box-shadow: none !important;
    }

    /* Halaman aktif: tanpa blok warna, hanya border & teks yang lebih tegas. */
    #employee-table_wrapper .dataTables_paginate .paginate_button.current,
    #employee-table_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #ffffff !important;
        border-color: #111827 !important; /* gray-900 */
        color: #111827 !important; /* gray-900 */
        font-weight: 500;
        box-shadow: none !important;
    }

    /* Tombol non-aktif (Previous/Next di ujung): abu, tanpa latar & border. */
    #employee-table_wrapper .dataTables_paginate .paginate_button.disabled,
    #employee-table_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: transparent !important;
        border-color: transparent !important;
        color: #9ca3af !important; /* gray-400 */
        cursor: not-allowed;
        box-shadow: none !important;
    }

    /* "..." diringkas spasinya agar tidak melebar seperti default (0 1em). */
    #employee-table_wrapper .dataTables_paginate .ellipsis {
        padding: 0.375rem 0.25rem;
        color: #6b7280; /* gray-500 */
    }

    /* Tinggi kontrol filter disamakan (38px) dengan input & tombol Tailwind:
       select2 mengganti <select> dengan markup sendiri, jadi tingginya diatur di sini. */
    .select2-container--default .select2-selection--single {
        height: 2.375rem; /* 38px */
        border: 1px solid #d1d5db; /* gray-300, sama dengan input */
        border-radius: 0.5rem; /* rounded-lg */
        background-color: #ffffff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem; /* 36px = 38px dikurangi border atas/bawah */
        font-size: 0.875rem; /* text-sm */
        padding-left: 0.75rem; /* px-3 */
        color: #111827;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.25rem; /* sejajar dengan area rendered */
    }

    /* Kontrol "Show entries" & teks info disamakan dengan bahasa form app. */
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.5rem;
        padding: 0.25rem 1.5rem 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length {
        font-size: 0.875rem;
        color: #4b5563; /* gray-600 */
    }
</style>
@endpush

@section('content')
{{-- Wrapper sengaja tanpa class .container: .container Tailwind membatasi max-width (1280px)
     sehingga tabel terlihat kecil dengan padding berlebih saat sidebar ditutup. --}}
<div class="w-full bg-white border rounded-lg p-4">

    {{-- Header halaman: judul sejajar kiri, tombol Add Employee di ujung kanan. --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Employee List</h2>
        <a href="{{ route('hr.employee.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Employee</a>
    </div>

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter tabel satu baris: pencarian + Subcon + Department + tombol aksi sejajar di ujung.
         Nilai form dikirim sebagai parameter tambahan ajax DataTables (bukan reload halaman).
         h-[38px] dipakai di semua kontrol agar tingginya sama dengan select2 (lihat @push('styles')). --}}
    <form id="filter-form" class="mb-4 flex flex-wrap items-end gap-2">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
            <input type="text" name="search_text" id="filter-search" placeholder="Cari nama atau NIK" class="w-full h-[38px] border border-gray-300 rounded-lg px-3 text-sm shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div class="w-full sm:w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Subcon</label>
            <select name="employee_type" id="filter-type" class="w-full h-[38px] border border-gray-300 rounded-lg px-3 text-sm shadow-sm focus:outline-none focus:ring focus:border-blue-500">
                <option value="">-- Semua Subcon --</option>
                @foreach($employeeTypes as $employeeType)
                <option value="{{ $employeeType }}">{{ $employeeType }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-56">
            <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
            <select name="department_id" id="filter-dept" class="w-full h-[38px] border border-gray-300 rounded-lg px-3 text-sm shadow-sm focus:outline-none focus:ring focus:border-blue-500">
                <option value="">-- Semua Department --</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="h-[38px] bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg shadow text-sm">Search</button>
            <button type="button" id="filter-reset" class="h-[38px] bg-gray-500 hover:bg-gray-600 text-white px-4 rounded-lg shadow text-sm">Reset</button>
        </div>
    </form>

    {{-- Kolom Division & End Date tidak ditampilkan karena tidak ada kolomnya di tabel employees. --}}
    {{-- Kolom Departement, Position, Join Date, Gender, Phone Number tidak ditampilkan di list —
         kelengkapan data ada di halaman Detail (hr.employee.show). --}}
    <div class="overflow-x-auto">
        <table id="employee-table" class="w-full min-w-full table-auto border-collapse border border-gray-300 shadow rounded-lg">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2 text-center whitespace-nowrap">No.</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">NIK</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Name</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Subcon</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Status</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Select2 untuk filter dropdown; allowClear agar filter bisa dikosongkan tanpa reload.
    $('#filter-type, #filter-dept').select2({ placeholder: '-- Semua --', allowClear: true, width: '100%' });

    const table = $('#employee-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        // Zebra baris: DataTables menambahkan class "odd"/"even" ke <tr> hasil render,
        // warnanya diatur di blok styles di atas agar seragam dengan tabel APD halaman detail.
        stripeClasses: ['odd', 'even'],
        // Ikon feather pada kolom action baru dirender setelah baris selesai digambar.
        drawCallback: function () { feather.replace(); },
        ajax: { url: '{{ route("hr.employee.data") }}', data: d => Object.assign(d, getFilters()) },
        order: [[2, 'asc']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        // dom tanpa 'f': search bawaan DataTables dihapus karena sudah ada input "Cari nama atau NIK".
        // 'l' (Show entries) di ATAS tabel; 'i' (info) + 'p' (pagination) di BAWAH tabel dalam satu baris.
        dom: '<"flex flex-wrap justify-between items-center gap-2 mb-3"l>rt<"flex flex-wrap justify-between items-center gap-2 mt-3"ip>',
        language: {
            emptyTable: 'Belum ada data employee',
            zeroRecords: 'Data employee tidak ditemukan',
            // Indikator loading dibiarkan kosong: tanpa teks dan tanpa spinner custom.
            processing: '',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nik', name: 'nik' },
            { data: 'name', name: 'name' },
            { data: 'employee_type', name: 'employee_type' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    // Nilai filter form yang ikut dikirim pada setiap request DataTables.
    function getFilters() {
        return {
            search_text: $('#filter-search').val(),
            employee_type: $('#filter-type').val(),
            department_id: $('#filter-dept').val(),
        };
    }

    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.draw();
    });

    $('#filter-reset').on('click', function () {
        $('#filter-search').val('');
        // val(null) + trigger change agar select2 ikut mereset tampilan placeholder.
        $('#filter-type').val(null).trigger('change');
        $('#filter-dept').val(null).trigger('change');
        table.draw();
    });
});
</script>
@endpush
