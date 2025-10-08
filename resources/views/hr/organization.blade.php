@extends('layouts.app')

@section('title', 'Organizational Hierarchy')
@section('page-title', 'Organizational Hierarchy')
@section('breadcrumb-item', 'HR')
@section('breadcrumb-active', 'Organization')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white border rounded-lg">

    <div class="mb-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Tambah Posisi</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300 shadow rounded-lg">
           <thead class="bg-gray-100 text-gray-700">
    <tr>
        <th class="border px-4 py-2 text-center">Action</th>
        <th class="border px-4 py-2 text-left">Postion</th>
        <th class="border px-4 py-2 text-left">Name</th>
        <th class="border px-4 py-2 text-left">Departement</th>
        <th class="border px-4 py-2 text-left">Top</th>
        <th class="border px-4 py-2 text-center">Status</th>
        <th class="border px-4 py-2 text-center">Vacant Since</th>
    </tr>
</thead>
<tbody>
    <tr class="">
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2"></td>
        <td class="border px-4 py-2 text-center">
            <span class="px-2 py-1 rounded text-sm font-medium ">
                
            </span>
        </td>
        <td class="border px-4 py-2 text-center">
            
        </td>
        <td class="border px-4 py-2 text-center space-x-2">
            <!-- Aksi tombol -->
        </td>
    </tr>

</tbody>
        </table>
    </div>
</div>
@endsection
