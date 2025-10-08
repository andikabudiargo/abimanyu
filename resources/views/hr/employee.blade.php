@extends('layouts.app')

@section('title', 'Employee Information')
@section('page-title', 'Employee Information')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Employee Information')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg">

    <div class="mb-4">
        <a href="{{ route('hr.create-employee.index') }}">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Employee</button>
        </a>
    </div>

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
    <tr class="">
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2 text-center"></td>
        <td class="border px-4 py-2 text-center"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
        <td class="border px-4 py-2 text-center space-x-2"></td>
    </tr>

</tbody>
        </table>
    </div>
</div>
@endsection
