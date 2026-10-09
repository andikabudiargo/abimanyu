@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
@php
    // Tahap 1 (kepercayaan form): tab awal mengikuti error validasi pertama dari server.
    // Jika error berasal dari field tab Job Information, tab itu yang dibuka agar pesan error
    // langsung terlihat tanpa user berpindah tab manual.
    $tabAwal = 'personal';
    $fieldTabJob = ['employee_type', 'nik', 'department', 'division', 'position_id', 'group_schedule', 'join_date'];
    if (in_array($errors->keys()[0] ?? null, $fieldTabJob, true)) {
        $tabAwal = 'job';
    }
@endphp
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg" x-data="{ activeTab: '{{ $tabAwal }}' }">
    {{-- Navigation Buttons --}}
    <div class="mb-6 space-x-2">
        {{-- hover: pada binding tab aktif menahan warna biru penuh saat kursor di atasnya,
             agar tab aktif tidak memudar mengikuti hover tab nonaktif. --}}
        <button @click="activeTab = 'personal'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'personal' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">Personal & Account</button>
        <button @click="activeTab = 'job'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'job' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">Job Information</button>
        <button @click="activeTab = 'training'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'training' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">Training History</button>
        <button @click="activeTab = 'apd'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'apd' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">APD Checklist</button>
    </div>

    {{-- Blok error validasi server: diletakkan tepat di atas form agar pesan gagal validasi langsung terlihat --}}
    @if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $pesanError)
                <li>{{ $pesanError }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- novalidate: validasi server adalah satu-satunya sumber kebenaran, agar error tampil
         sebagai blok merah di atas form alih-alih diblokir senyap oleh browser. --}}
    <form action="{{ route('hr.employee.store') }}" method="POST" novalidate>
        @csrf

       {{-- Personal & Account Information --}}
<div x-show="activeTab === 'personal'" class="space-y-12">

   <!-- PERSONAL INFORMATION -->
<div class="mb-10">
    <div class="flex items-center text-gray-700 text-lg font-semibold mb-6">
        <i data-feather="user" class="w-4 h-4 mr-2 text-gray-500"></i>
        <span>Personal Information</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name<small class="text-red-600">*</small></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('name') border-red-500 @enderror" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
             <select name="gender" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('gender') border-red-500 @enderror">
        <option value="">-- Select Gender --</option>
        <option value="Male" @selected(old('gender') == 'Male')>Male</option>
        <option value="Female" @selected(old('gender') == 'Female')>Female</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
           <select name="religion" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('religion') border-red-500 @enderror">
        <option value="">-- Select Religion --</option>
        <option value="Islam" @selected(old('religion') == 'Islam')>Islam</option>
        <option value="Katolik" @selected(old('religion') == 'Katolik')>Katolik</option>
        <option value="Protestan" @selected(old('religion') == 'Protestan')>Protestan</option>
        <option value="Hindu" @selected(old('religion') == 'Hindu')>Hindu</option>
        <option value="Buddha" @selected(old('religion') == 'Buddha')>Buddha</option>
        <option value="Konghucu" @selected(old('religion') == 'Konghucu')>Konghucu</option>
        <option value="Lainnya" @selected(old('religion') == 'Lainnya')>Lainnya</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('birth_date') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Place</label>
            <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('birth_place') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
            <input type="text" name="nationality" value="{{ old('nationality') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('nationality') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Education</label>
            <select name="last_education" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('last_education') border-red-500 @enderror">
        <option value="">-- Select Education --</option>
        <option value="SMK/SMA" @selected(old('last_education') == 'SMK/SMA')>SMK/SMA</option>
        <option value="D3" @selected(old('last_education') == 'D3')>D3</option>
        <option value="S1" @selected(old('last_education') == 'S1')>S1</option>
        <option value="S2" @selected(old('last_education') == 'S2')>S2</option>
        <option value="S3" @selected(old('last_education') == 'S3')>S3</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status<small class="text-red-600">*</small></label>
            <select name="marital_status" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('marital_status') border-red-500 @enderror" required>
        <option value="">-- Select Status --</option>
        <option value="Lajang" @selected(old('marital_status') == 'Lajang')>Lajang</option>
        <option value="Menikah" @selected(old('marital_status') == 'Menikah')>Menikah</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Children</label>
            <input type="number" name="dependents" value="{{ old('dependents') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('dependents') border-red-500 @enderror">
        </div>
    </div>

   <div class="mt-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <textarea name="address" rows="3" class="w-full border rounded-lg px-3 py-2 shadow-sm resize-none focus:outline-none focus:ring focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
</div>


    <div class="grid grid-cols-2 gap-6 mt-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('phone') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('email') border-red-500 @enderror">
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Add Picture</label>
        <input type="file" name="picture" disabled title="Belum dapat diinput (kolom belum tersedia di database)" class="cursor-not-allowed bg-gray-100 text-gray-400 w-full text-sm border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
    </div>
</div>

<hr class="my-10">

<!-- ACCOUNT INFORMATION -->
<div>
    <div class="flex items-center text-gray-700 text-lg font-semibold mb-6">
        <i data-feather="file-text" class="w-4 h-4 mr-2 text-gray-500"></i>
        <span>Account Information</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Family Card Number</label>
            <input type="text" name="kk_number" value="{{ old('kk_number') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('kk_number') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ID Card Number</label>
            <input type="text" name="ktp_number" value="{{ old('ktp_number') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('ktp_number') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PTKP Category</label>
            <input type="text" name="ptkp" value="{{ old('ptkp') }}" disabled title="Belum dapat diinput (kolom belum tersedia di database)" class="cursor-not-allowed bg-gray-100 text-gray-400 w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BPJS Ketenagakerjaan</label>
            <input type="text" name="bpjs_tk" value="{{ old('bpjs_tk') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('bpjs_tk') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BPJS Kesehatan</label>
            <input type="text" name="bpjs_ks" value="{{ old('bpjs_ks') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('bpjs_ks') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NPWP Number</label>
            <input type="text" name="npwp_number" value="{{ old('npwp_number') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('npwp_number') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
            <input type="text" name="bank" value="{{ old('bank') }}" disabled title="Belum dapat diinput (kolom belum tersedia di database)" class="cursor-not-allowed bg-gray-100 text-gray-400 w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Bank Name</label>
            <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('bank_account_name') border-red-500 @enderror">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Bank Number</label>
            <input type="text" name="bank_number" value="{{ old('bank_number') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('bank_number') border-red-500 @enderror">
        </div>
    </div>
</div>
</div>


        {{-- Job Information --}}
       <div x-show="activeTab === 'job'" x-cloak class="space-y-12">
     <!-- JOB INFORMATION -->
<div class="mb-10">
    <div class="flex items-center text-gray-700 text-lg font-semibold mb-6">
        <i data-feather="user" class="w-4 h-4 mr-2 text-gray-500"></i>
        <span>Job Information</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subcon<small class="text-red-600">*</small></label>
            <input type="text" name="employee_type" value="{{ old('employee_type') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('employee_type') border-red-500 @enderror" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIK<small class="text-red-600">*</small></label>
             <input type="text" name="nik" value="{{ old('nik') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('nik') border-red-500 @enderror" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Departement</label>
           <select name="department" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('department') border-red-500 @enderror">
        <option value="">-- Select Departement --</option>
        @foreach($departments as $department)
        <option value="{{ $department->id }}" @selected(old('department') == $department->id)>{{ $department->name }}</option>
        @endforeach
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
            <input type="text" name="division" value="{{ old('division') }}" disabled title="Belum dapat diinput (kolom belum tersedia di database)" class="cursor-not-allowed bg-gray-100 text-gray-400 w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
            <select name="position_id" class="w-full border rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('position_id') border-red-500 @enderror">
                <option value="">-- Select Position --</option>
                @foreach($positions as $position)
                <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>{{ $position->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Group Schedule</label>
            <input type="text" name="group_schedule" value="{{ old('group_schedule') }}" disabled title="Belum dapat diinput (kolom belum tersedia di database)" class="cursor-not-allowed bg-gray-100 text-gray-400 w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
    </div>

<!-- ACCOUNT INFORMATION -->
 <!-- Join Date -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
        <input type="date" name="join_date" value="{{ old('join_date') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500 @error('join_date') border-red-500 @enderror">
    </div>

    <!-- Contract History -->
    <div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Contract History</label>
    
    <!-- tempat baris kontrak akan ditambahkan -->
    <div id="contract-rows" class="space-y-2"></div>

    <button type="button" onclick="addContractRow()" class="mt-2 text-blue-600 hover:underline text-sm">
        + Add Contract
    </button>
</div>
</div>
</div>

        {{-- Training History --}}
        <div x-show="activeTab === 'training'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label class="block font-medium">Training Title</label>
        <input type="text" name="training_title[]" value="{{ old('training_title.0') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
    </div>
    <div>
        <label class="block font-medium">Training Date</label>
        <input type="date" name="training_date[]" value="{{ old('training_date.0') }}" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
    </div>
</div>


         {{-- APD Given --}}
<div x-show="activeTab === 'apd'" x-cloak class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">APD Given</label>
    <div id="apd-rows">
        <!-- Baris APD akan ditambahkan di sini -->
    </div>
    <button type="button" onclick="addApdRow()" class="mt-2 text-blue-600 hover:underline text-sm">+ Add APD</button>
</div>



        {{-- Submit Button --}}
        <div class="mt-6">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Save</button>
            <a href="{{ route('hr.employee.index') }}" class="ml-2 px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded inline-block">Cancel</a>
        </div>
    </form>
</div>
@endsection

<script>
let apdIndex = 0;

function addApdRow() {
    apdIndex++;
    const container = document.getElementById('apd-rows');

    const row = document.createElement('div');
    row.className = 'grid grid-cols-6 gap-4 mb-2 apd-row items-end';
    row.setAttribute('data-index', apdIndex);

    row.innerHTML = `
        <div>
            <label class="block text-xs text-gray-600 mb-1">APD Name</label>
            <input type="text" name="apd_name[]" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Given Date</label>
            <input type="date" name="apd_date[]" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Accepted?</label>
            <input type="checkbox" name="apd_checklist[]" class="mt-2">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Replacement Cycle</label>
            <input type="text" name="apd_cycle[]" placeholder="e.g. 1 year / 6 months" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div class="flex items-center pt-5">
            <button type="button" onclick="removeApdRow(this)" class="text-red-600 hover:underline text-sm">Remove</button>
        </div>
    `;

    container.appendChild(row);
}

function removeApdRow(button) {
    const row = button.closest('.apd-row');
    row.remove();
}

// Tambahkan satu baris saat halaman load
window.addEventListener('DOMContentLoaded', () => {
    addApdRow();
});
</script>


   <script>
    function addContractRow() {
    const container = document.getElementById('contract-rows');
    const lastRows = container.querySelectorAll('.contract-row');
    let nextContractNumber = 1;

    // Cek contract terakhir yang sudah diisi
    for (let i = 0; i < lastRows.length; i++) {
        const select = lastRows[i].querySelector('select[name="contract_type[]"]');
        const val = select.value;
        const match = val.match(/Contract (\d+)/);
        if (match) {
            const num = parseInt(match[1]);
            if (!isNaN(num) && num >= nextContractNumber) {
                nextContractNumber = num + 1;
            }
        } else if (val === 'OJT' || val === 'Permanent') {
            continue;
        } else {
            // jika contract belum dipilih
            nextContractNumber = 1;
            break;
        }
    }

    const row = document.createElement('div');
    row.className = 'grid grid-cols-4 gap-4 mb-2 contract-row items-end';
    row.setAttribute('data-index', nextContractNumber);

    row.innerHTML = `
        <div>
            <label class="block text-xs text-gray-600 mb-1">Contract Type</label>
            <select name="contract_type[]" class="w-full border rounded-lg px-3 py-2 shadow-sm" onchange="handleContractTypeChange(this)">
                <option value="OJT">OJT</option>
                <option value="Contract ${nextContractNumber}">Contract ${nextContractNumber}</option>
                <option value="Permanent">Permanent</option>
            </select>
        </div>

        <div class="date-fields">
            <label class="block text-xs text-gray-600 mb-1">Start Date</label>
            <input type="date" name="contract_start[]" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>

        <div class="date-fields">
            <label class="block text-xs text-gray-600 mb-1">End Date</label>
            <input type="date" name="contract_end[]" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>

        <div class="flex items-center pt-5">
            <button type="button" onclick="removeContractRow(this)" class="text-red-600 hover:underline text-sm">Remove</button>
        </div>
    `;

    container.appendChild(row);
}


    function removeContractRow(button) {
        const row = button.closest('.contract-row');
        row.remove();
    }

    function handleContractTypeChange(selectEl) {
        const selected = selectEl.value;
        const row = selectEl.closest('.contract-row');
        const dateFields = row.querySelectorAll('.date-fields');

        if (selected === 'Permanent') {
            dateFields.forEach(el => el.style.display = 'none');

            if (!row.querySelector('.sk-number')) {
                const skDiv = document.createElement('div');
                skDiv.className = 'sk-number';
                skDiv.innerHTML = `
                    <label class="block text-xs text-gray-600 mb-1">SK Number</label>
                    <input type="text" name="sk_number[]" class="w-full border rounded-lg px-3 py-2 shadow-sm">
                `;
                // Insert before the "remove" button column
                row.insertBefore(skDiv, row.children[3]);
            }
        } else {
            dateFields.forEach(el => el.style.display = 'block');
            const sk = row.querySelector('.sk-number');
            if (sk) sk.remove();
        }
    }

    // Add first row on page load
    window.addEventListener('DOMContentLoaded', () => {
        addContractRow();
    });
</script>
