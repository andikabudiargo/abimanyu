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

    {{-- Kolom Division & End Date tidak ditampilkan karena tidak ada kolomnya di tabel employees. --}}
    {{-- Kolom Departement, Position, Join Date, Gender, Phone Number tidak ditampilkan di list —
         kelengkapan data ada di halaman Detail (hr.employee.show). --}}
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300 shadow rounded-lg">
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
            <tbody>
                @forelse($employees as $employee)
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-50">
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->nik }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->name }}</td>
                    <td class="border px-4 py-2 whitespace-nowrap">{{ $employee->employee_type }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">{{ $employee->status }}</td>
                    <td class="border px-4 py-2 text-center whitespace-nowrap">
                        {{-- Ikon+teks aksi feather: eye=Detail, edit-2=Edit, trash-2=Hapus (dirender global feather.replace()). --}}
                        <a href="{{ route('hr.employee.show', $employee) }}" class="text-blue-600 hover:underline" title="Detail"><i data-feather="eye" class="w-4 h-4 inline-block align-middle"></i> Detail</a>
                        <a href="{{ route('hr.employee.edit', $employee) }}" class="text-blue-600 hover:underline ml-3" title="Edit"><i data-feather="edit-2" class="w-4 h-4 inline-block align-middle"></i> Edit</a>
                        {{-- Pola DepartmentController: form spoofing DELETE + confirm() native. --}}
                        <form action="{{ route('hr.employee.destroy', $employee) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Yakin ingin hapus employee ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" title="Hapus"><i data-feather="trash-2" class="w-4 h-4 inline-block align-middle"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-50">
                    <td colspan="6" class="border px-4 py-8 text-center whitespace-nowrap">
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
