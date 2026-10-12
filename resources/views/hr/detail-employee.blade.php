@extends('layouts.app')

@section('title', 'Detail Employee')
@section('page-title', 'Detail Employee')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg">

    <div class="mb-4">
        <a href="{{ route('hr.employee.index') }}" class="text-blue-600 hover:underline">← Kembali ke List</a>
    </div>

    <h1 class="text-lg font-semibold mb-6">{{ $employee->name ?: '-' }}</h1>

    {{-- Heading seksi meniru gaya tab aktif pada form create-employee. --}}
    <h2 class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">Personal & Account</h2>

    <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-8">
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

    <h2 class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">Job Information</h2>

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

    {{-- Aksi bawah: tombol Edit mengikuti gaya + Add Employee pada halaman list. --}}
    <div class="mt-6 flex items-center gap-4">
        <a href="{{ route('hr.employee.edit', $employee) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Edit</a>
        <a href="{{ route('hr.employee.index') }}" class="text-blue-600 hover:underline">Kembali</a>
    </div>
</div>
@endsection
