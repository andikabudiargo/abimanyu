@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg">

    <div class="mb-4">
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

    {{-- Kolom Division tidak ditampilkan karena tabel employees tidak memiliki kolom division. --}}
    {{-- Kolom End Date tidak ditampilkan karena tabel employees tidak memiliki kolom end date. --}}
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300 shadow rounded-lg">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Action</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Subcon</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">NIK</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Name</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Status</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Departement</th>
                    <th class="px-4 py-2 text-left whitespace-nowrap">Position</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Join Date</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Gender</th>
                    <th class="px-4 py-2 text-center whitespace-nowrap">Phone Number</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-50">
                    <td class="border px-4 py-2 text-center whitespace-nowrap">
                        <span class="text-gray-400 cursor-not-allowed text-sm" title="Fitur kontrak belum tersedia">Kontrak</span>
                        |
                        <a href="{{ route('hr.employee.edit', $employee) }}" class="text-blue-600 hover:underline">Edit</a>
                        |
                        {{-- Pola DepartmentController: form spoofing DELETE + confirm() native. --}}
                        <form action="{{ route('hr.employee.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin hapus employee ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->employee_type }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->nik }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->name }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $employee->status }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->departments->pluck('name')->join(', ') }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->positions->pluck('name')->join(', ') }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ optional($employee->join_date)->format('d-m-Y') ?? '-' }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $employee->gender ?: '-' }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $employee->phone ?: '-' }}</td>
                </tr>
                @empty
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-50">
                    <td colspan="10" class="border px-4 py-8 text-center whitespace-nowrap">
                        <div class="flex flex-col items-center gap-3">
                            <i data-feather="inbox" class="w-8 h-8 text-gray-400"></i>
                            <span class="text-gray-500">Belum ada data employee</span>
                            <a href="{{ route('hr.employee.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Employee</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
