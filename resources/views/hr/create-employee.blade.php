@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg" x-data="{ activeTab: 'personal' }">
    {{-- Navigation Buttons --}}
    <div class="mb-6 space-x-2">
        <button @click="activeTab = 'personal'" :class="{ 'bg-blue-600 text-white': activeTab === 'personal' }" class="px-4 py-2 border rounded hover:bg-blue-600 hover:text-white transition">Personal & Account</button>
        <button @click="activeTab = 'job'" :class="{ 'bg-blue-600 text-white': activeTab === 'job' }" class="px-4 py-2 border rounded hover:bg-blue-600 hover:text-white transition">Job Information</button>
        <button @click="activeTab = 'training'" :class="{ 'bg-blue-600 text-white': activeTab === 'training' }" class="px-4 py-2 border rounded hover:bg-blue-600 hover:text-white transition">Training History</button>
        <button @click="activeTab = 'apd'" :class="{ 'bg-blue-600 text-white': activeTab === 'apd' }" class="px-4 py-2 border rounded hover:bg-blue-600 hover:text-white transition">APD Checklist</button>
    </div>

    <form action="" method="POST">
        @csrf

       {{-- Personal & Account Information --}}
<div x-show="activeTab === 'personal'" class="space-y-12">

   <!-- PERSONAL INFORMATION -->
<div class="mb-10">
    <div class="flex items-center text-gray-700 text-lg font-semibold mb-6">
        <i data-feather="user" class="w-4 h-4 mr-2 text-gray-500"></i>
        <span>Personal Information</span>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name*</label>
            <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
             <select name="gender" class="w-full border rounded px-3 py-2 bg-white shadow-sm" required>
        <option value="">-- Select Gender --</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
           <select name="gender" class="w-full border rounded px-3 py-2 bg-white shadow-sm" required>
        <option value="">-- Select Religion --</option>
        <option value="Male">Islam</option>
        <option value="Female">Katolik</option>
        <option value="Female">Protestan</option>
        <option value="Female">Hindu</option>
        <option value="Female">Buddha</option>
        <option value="Female">Konghucu</option>
        <option value="Female">Lainnya</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
            <input type="date" name="birth_date" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Place</label>
            <input type="text" name="birth_place" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
            <input type="text" name="nationality" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Education</label>
            <select name="gender" class="w-full border rounded px-3 py-2 bg-white shadow-sm" required>
        <option value="">-- Select Education --</option>
        <option value="Male">SMK/SMA</option>
        <option value="Female">D3</option>
        <option value="Female">S1</option>
        <option value="Female">S2</option>
        <option value="Female">S3</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
            <select name="gender" class="w-full border rounded px-3 py-2 bg-white shadow-sm" required>
        <option value="">-- Select Status --</option>
        <option value="Male">Lajang</option>
        <option value="Female">Menikah</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Children</label>
            <input type="number" name="children" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
    </div>

   <div class="mt-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <textarea name="address" rows="3" class="w-full border rounded-lg px-3 py-2 shadow-sm resize-none focus:outline-none focus:ring focus:border-blue-500"></textarea>
</div>


    <div class="grid grid-cols-2 gap-6 mt-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Add Picture</label>
        <input type="file" name="picture" class="w-full text-sm border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
    </div>
</div>

<hr class="my-10">

<!-- ACCOUNT INFORMATION -->
<div>
    <div class="flex items-center text-gray-700 text-lg font-semibold mb-6">
        <i data-feather="file-text" class="w-4 h-4 mr-2 text-gray-500"></i>
        <span>Account Information</span>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Family Card Number</label>
            <input type="text" name="kk" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ID Card Number</label>
            <input type="text" name="nik" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PTKP Category</label>
            <input type="text" name="ptkp" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BPJS Ketenagakerjaan</label>
            <input type="text" name="bpjs_tk" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">BPJS Kesehatan</label>
            <input type="text" name="bpjs_kes" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NPWP Number</label>
            <input type="text" name="npwp" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
            <input type="text" name="bank" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Bank Name</label>
            <input type="text" name="bank_name" class="w-full border rounded-lg px-3 py-2 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Bank Number</label>
            <input type="text" name="bank_account" class="w-full border rounded-lg px-3 py-2 shadow-sm">
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

    <div class="grid grid-cols-3 gap-6 mb-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subcon*</label>
            <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIK*</label>
             <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Departement</label>
           <select name="gender" class="w-full border rounded px-3 py-2 bg-white shadow-sm" required>
        <option value="">-- Select Religion --</option>
        <option value="Male">Islam</option>
        <option value="Female">Katolik</option>
        <option value="Female">Protestan</option>
        <option value="Female">Hindu</option>
        <option value="Female">Buddha</option>
        <option value="Female">Konghucu</option>
        <option value="Female">Lainnya</option>
    </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
            <input type="date" name="birth_date" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
            <input type="text" name="birth_place" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Group Schedule</label>
            <input type="text" name="nationality" class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-500">
        </div>
    </div>

<!-- ACCOUNT INFORMATION -->
 <!-- Join Date -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
        <input type="date" name="join_date" class="w-full border rounded-lg px-3 py-2 shadow-sm">
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
        <div x-show="activeTab === 'training'" x-cloak class="grid grid-cols-3 gap-4">
    <div>
        <label class="block font-medium">Training Title</label>
        <input type="text" name="training_title[]" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block font-medium">Training Date</label>
        <input type="date" name="training_date[]" class="w-full border rounded px-3 py-2">
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
            <input type="text" name="apd_name[]" class="w-full border rounded px-2 py-1 shadow-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Given Date</label>
            <input type="date" name="apd_date[]" class="w-full border rounded px-2 py-1 shadow-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Accepted?</label>
            <input type="checkbox" name="apd_checklist[]" class="mt-2">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Replacement Cycle</label>
            <input type="text" name="apd_cycle[]" placeholder="e.g. 1 year / 6 months" class="w-full border rounded px-2 py-1 shadow-sm">
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
            <select name="contract_type[]" class="w-full border rounded-lg px-2 py-1 shadow-sm" onchange="handleContractTypeChange(this)">
                <option value="OJT">OJT</option>
                <option value="Contract ${nextContractNumber}">Contract ${nextContractNumber}</option>
                <option value="Permanent">Permanent</option>
            </select>
        </div>

        <div class="date-fields">
            <label class="block text-xs text-gray-600 mb-1">Start Date</label>
            <input type="date" name="contract_start[]" class="w-full border rounded-lg px-2 py-1 shadow-sm">
        </div>

        <div class="date-fields">
            <label class="block text-xs text-gray-600 mb-1">End Date</label>
            <input type="date" name="contract_end[]" class="w-full border rounded-lg px-2 py-1 shadow-sm">
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
                    <input type="text" name="sk_number[]" class="w-full border rounded-lg px-2 py-1 shadow-sm">
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
