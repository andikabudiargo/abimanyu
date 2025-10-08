@extends('layouts.app')

@section('title', 'Create Department')
@section('page-title', 'Create Department')
@section('breadcrumb-item', 'Department')
@section('breadcrumb-active', 'Create Department')
@section('content')
<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New Department</h2>
    <form id="ticket-form">
      <!-- 🔢 Nomor Referensi -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="relative group">
        <label for="ticket_number" class="block text-sm font-medium text-gray-700 mb-1">Department Code<small class="text-red-600"> *</small></label>
        <input type="text" name="ticket_number" id="ticket_number"
               class="w-full px-3 py-2 bg-gray-200 text-white border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Automatic" readonly />
        </div>
         <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Department Type<small class="text-red-600"> *</small></label>
        <select name="category" required
        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    <option value="">-- Choose Type --</option>
     <option value="Hardware">Manufacture</option>
    <option value="Hardware">Commercial</option>
</select>

        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Department Name<small class="text-red-600"> *</small></label>
        <input type="text" name="title" id="title"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
        <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Initial<small class="text-red-600"> *</small></label>
        <input type="text" name="title" id="title"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required />
        </div>
      </div>
      <hr>
      <div class="flex justify-start items-center gap-2 mt-4">
        <button id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Reset</button>
        <button id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
      </div>
    </form>
</div>

<!-- 🌟 MODAL CREATE WAREHOUSE -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h3 class="text-lg font-semibold mb-4">Create Department</h3>

    <form id="createWarehouseForm">
      <div class="grid grid-cols-2 gap-4 mb-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">Department Code</label>
          <input type="text" name="code" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300" required>
        </div>
         <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Department Type</label>
           <select name="type" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
            <option value="">-- Choose Type --</option>
            <option value="Manufacture">Manufacture</option>
            <option value="Commercial">Commercial</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Department Name</label>
          <input type="text" name="pic" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Initial</label>
          <textarea name="note" class="w-full border px-3 py-2 rounded focus:ring focus:ring-indigo-300"></textarea>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
      </div>
    </form>

    <!-- ❌ Tombol Close di pojok -->
    <button id="closeModalIcon" class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl">&times;</button>
  </div>
</div>
@endsection