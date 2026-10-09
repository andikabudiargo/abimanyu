@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg">

    <div class="mb-4">
        <a href="{{ route('hr.employee.create') }}">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Employee</button>
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300 shadow rounded-lg">
           <thead class="bg-gray-100 text-gray-700">
    <tr>
        <th class="border px-4 py-2 text-center">Action</th>
        <th class="border px-4 py-2 text-left">Subcon</th>
        <th class="border px-4 py-2 text-left">NIK</th>
        <th class="border px-4 py-2 text-left">Name</th>
        <th class="border px-4 py-2 text-left">Status</th>
        <th class="border px-4 py-2 text-left">Departement</th>
        <th class="border px-4 py-2 text-center">Position</th>
        <th class="border px-4 py-2 text-center">Division</th>
        <th class="border px-4 py-2 text-center">Join Date</th>
        <th class="border px-4 py-2 text-center">End Date</th>
        <th class="border px-4 py-2 text-center">Gender</th>
        <th class="border px-4 py-2 text-center">Phone Number</th>
    </tr>
</thead>
<tbody>
    @forelse($employees as $employee)
    <tr>
        <td class="border px-4 py-2 text-center">
            <span class="text-gray-400 cursor-not-allowed text-sm" title="Fitur kontrak belum tersedia">Kontrak</span>
        </td>
        <td class="border px-4 py-2">{{ $employee->employee_type }}</td>
        <td class="border px-4 py-2">{{ $employee->nik }}</td>
        <td class="border px-4 py-2">{{ $employee->name }}</td>
        <td class="border px-4 py-2 text-center">{{ $employee->status }}</td>
        <td class="border px-4 py-2">{{ $employee->departments->pluck('name')->join(', ') }}</td>
        <td class="border px-4 py-2 text-center">{{ $employee->positions->pluck('name')->join(', ') }}</td>
        {{-- Kolom Division tidak ditampilkan karena tabel employees tidak memiliki kolom division. --}}
        <td class="border px-4 py-2 text-center">-</td>
        <td class="border px-4 py-2 text-center">{{ optional($employee->join_date)->format('d-m-Y') ?? '-' }}</td>
        {{-- Kolom End Date tidak ditampilkan karena tabel employees tidak memiliki kolom end date. --}}
        <td class="border px-4 py-2 text-center">-</td>
        <td class="border px-4 py-2 text-center">{{ $employee->gender ?: '-' }}</td>
        <td class="border px-4 py-2 text-center">{{ $employee->phone ?: '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="12" class="border px-4 py-2 text-center">Belum ada data employee.</td>
    </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>
@endsection
