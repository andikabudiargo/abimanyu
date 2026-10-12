@extends('layouts.app')

@section('title', 'Detail Employee')
@section('page-title', 'Detail Employee')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg" x-data="{ activeTab: 'personal' }">

    <div class="mb-4">
        <a href="{{ route('hr.employee.index') }}" class="text-blue-600 hover:underline">← Kembali ke List</a>
    </div>

    <h1 class="text-lg font-semibold mb-6">{{ $employee->name ?: '-' }}</h1>

    {{-- Navigation Buttons --}}
    <div class="mb-6 space-x-2">
        {{-- hover: pada binding tab aktif menahan warna biru penuh saat kursor di atasnya,
             agar tab aktif tidak memudar mengikuti hover tab nonaktif. --}}
        <button @click="activeTab = 'personal'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'personal' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">Personal & Account</button>
        <button @click="activeTab = 'job'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'job' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">Job Information</button>
        <button @click="activeTab = 'apd'" :class="{ 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white': activeTab === 'apd' }" class="px-4 py-2 border rounded hover:bg-blue-50 hover:text-blue-600 transition">APD Distribution</button>
    </div>

    {{-- Personal & Account --}}
    <div x-show="activeTab === 'personal'">
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Full Name</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->name ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">NIK</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->nik ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Gender</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->gender ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Religion</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->religion ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Birth Place</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->birth_place ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Birth Date</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ optional($employee->birth_date)->format('d-m-Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Nationality</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->nationality ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Last Education</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->last_education ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Marital Status</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->marriage_status ? 'Menikah' : 'Lajang' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Dependents</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->dependents ?: '-' }}</dd>
            </div>
            <div class="md:col-span-3">
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Address</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->address ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Phone</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->phone ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Email</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->email ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">KTP Number</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->ktp_number ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">KK Number</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->kk_number ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">BPJS TK</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->bpjs_tk ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">BPJS KS</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->bpjs_ks ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">NPWP</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->npwp_number ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Bank Number</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->bank_number ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Bank Account Name</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->bank_account_name ?: '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Job Information --}}
    <div x-show="activeTab === 'job'" x-cloak>
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Subcon</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->employee_type ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Departement</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->departments->pluck('name')->join(', ') ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Position</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->positions->pluck('name')->join(', ') ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Join Date</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ optional($employee->join_date)->format('d-m-Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wider">Status</dt>
                <dd class="text-sm text-gray-900 mt-1">{{ $employee->status ?: '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- APD Distribution (read-only): data korelasi dari relasi Employee::distributions()
         (hasMany APDDistributionItem dengan kolom receiver = employees.id).
         distribution_date bertipe string → di-parse lewat Carbon (konvensi codebase). --}}
    <div x-show="activeTab === 'apd'" x-cloak>
        @php
            // Urutkan terbaru: distribution_date desc; fallback created_at bila tanggal distribusi kosong.
            // distribution_date bertipe string (tanpa cast) → parse Carbon agar perbandingan seragam.
            $distribusiApd = $employee->distributions->sortByDesc(function ($item) {
                $dist = optional($item->distribution)->distribution_date ?? optional($item->distribution)->created_at;
                return $dist ? \Carbon\Carbon::parse($dist) : '';
            });
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300">
                <thead>
                    <tr class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                        <th class="px-4 py-2 border whitespace-nowrap">APD</th>
                        <th class="px-4 py-2 border whitespace-nowrap">Qty</th>
                        <th class="px-4 py-2 border whitespace-nowrap">Lifetime</th>
                        <th class="px-4 py-2 border whitespace-nowrap">Tanggal Penerimaan</th>
                        <th class="px-4 py-2 border whitespace-nowrap">Diberikan oleh</th>
                        <th class="px-4 py-2 border whitespace-nowrap">Tanggal Pergantian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($distribusiApd as $item)
                    @php
                        // distribution_date bertipe string → Carbon::parse (konvensi sama dengan facility/apd).
                        $tanggalTerima = optional($item->distribution)->distribution_date
                            ? \Carbon\Carbon::parse($item->distribution->distribution_date)->format('d-m-Y')
                            : '-';

                        // Tanggal pergantian: distribution_date + lifetime (bulan);
                        // lifetime 0/kosong dianggap tidak ada → tampilkan '-'.
                        $lifetime = (int) optional($item->apd)->lifetime;
                        $tanggalPergantian = null;
                        if ($lifetime > 0 && optional($item->distribution)->distribution_date) {
                            $tanggalPergantian = \Carbon\Carbon::parse($item->distribution->distribution_date)->addMonthsNoOverflow($lifetime);
                        }

                        // Status penggantian untuk badge: 'lewat' bila sudah lewat, 'dekat' bila <= 2 bulan lagi.
                        $statusPergantian = null;
                        if ($tanggalPergantian) {
                            if ($tanggalPergantian->isPast()) {
                                $statusPergantian = 'lewat';
                            } elseif (now()->diffInMonths($tanggalPergantian, false) <= 2) {
                                $statusPergantian = 'dekat';
                            }
                        }
                    @endphp
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-50">
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ optional($item->apd)->name ?: '-' }}
                            <span class="text-gray-500">Kondisi {{ optional($item->apd)->conditions ?: '-' }}</span>
                        </td>
                        <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $item->qty }} {{ optional($item->apd)->uom }}</td>
                        <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $lifetime ?: '-' }}{{ $lifetime ? ' Bulan' : '' }}</td>
                        <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $tanggalTerima }}</td>
                        <td class="border px-4 py-2 whitespace-nowrap">{{ optional(optional($item->distribution)->creator)->name ?: '-' }}</td>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ optional($tanggalPergantian)->format('d-m-Y') ?? '-' }}
                            @if ($statusPergantian === 'lewat')
                            <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-700">Lewat</span>
                            @elseif ($statusPergantian === 'dekat')
                            <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Segera</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="border px-4 py-8 text-center whitespace-nowrap text-gray-500">Belum menerima APD</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aksi bawah: selalu terlihat di luar tab. Tombol Edit mengikuti gaya + Add Employee pada halaman list. --}}
    <div class="mt-6 flex items-center gap-4">
        <a href="{{ route('hr.employee.edit', $employee) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Edit</a>
        <a href="{{ route('hr.employee.index') }}" class="text-blue-600 hover:underline">Kembali</a>
    </div>
</div>
@endsection
