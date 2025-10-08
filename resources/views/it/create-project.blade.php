@extends('layouts.app')

@section('title', 'Create Project')
@section('page-title', 'Create Project')
@section('breadcrumb-item', 'Project')
@section('breadcrumb-active', 'Create Project')

@section('content')
<div class="flex justify-between items-center mb-6 bg-white px-4 py-3 rounded-2xl shadow w-full">
  <!-- KIRI: Tab Buttons -->
  <div class="flex space-x-3">
    <!-- Aktif -->
    <button onclick="showPanel('stockPanel')" 
            id="stockTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 shadow-sm hover:shadow-md transition">
      <i data-feather="info" class="w-4 h-4"></i>
      <span>Project Details</span>
    </button>

     <button onclick="showPanel('monitoringPanel')" 
            id="monitoringTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 hover:bg-gray-100 hover:text-indigo-600 transition">
      <i data-feather="search" class="w-4 h-4"></i>
      <span>Task Breakdown</span>
    </button>

     <button onclick="showPanel('schedulePanel')" 
            id="historyTab" class="flex items-center gap-2 bg-transparent text-indigo-600 px-5 py-2.5 text-sm font-semibold rounded-full border border-indigo-600 hover:bg-gray-100 hover:text-indigo-600 transition">
      <i data-feather="truck" class="w-4 h-4"></i>
      <span>Cost Breakdown</span>
    </button>
  </div>

  <!-- KANAN: Jam dan Icon -->
  <div class="flex items-center gap-2 text-gray-700 text-sm font-medium">
    <i data-feather="sun" id="time-icon" class="w-5 h-5"></i>
    <span id="current-time">--:--</span>
  </div>
</div>
<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New Project</h2>

    <form id="user-form" enctype="multipart/form-data">
        @csrf
        <!-- Full Name -->
<div class="flex flex-col md:flex-row gap-6">
    <!-- Bagian Kiri: Form Project (2/3) -->
    <div class="w-full md:w-1/3">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Project Number <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
         </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Project Name <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Project Manager<small class="text-red-600">*</small></label>
                <input type="text" name="username" id="username"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
        </div>

        <!-- Username & Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
             <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
             <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Priority <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
            <div class="col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
        </div>
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
             <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Start Date <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
             <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">End Date <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
 <div class="col-span-2 mb-4">
  <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
    Attachment (Optional)
  </label>
  <input type="file" name="attachment" id="attachment"
         class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
  <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF, XLSX, Docs. Max: 2MB.</p>
</div>
        </div>
    </div>
        

         <!-- Task Breakdown Table -->
        <div class="w-full md:w-2/3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Task Breakdown</label>
            <table class="w-full table-auto border rounded shadow-sm text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-2 border">No.</th>
                        <th class="p-2 border">Task</th>
                        <th class="p-2 border">Assigned to</th>
                        <th class="p-2 border">Start</th>
                        <th class="p-2 border">Deadline</th>
                        <th class="p-2 border">Action</th>
                        
                    </tr>
                </thead>
                <tbody id="task-table-body">
                    <!-- Task rows inserted by JS -->
                </tbody>
            </table>

            <!-- Add Task Form -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mt-4">
                <input type="text" placeholder="Task" class="task-input border rounded px-2 py-1 col-span-2" id="task-title">
                <input type="text" placeholder="Assigned To" class="task-input border rounded px-2 py-1 col-span-2" id="task-assigned">
                <input type="date" class="task-input border rounded px-2 py-1 col-span-1" id="task-start">
                <input type="date" class="task-input border rounded px-2 py-1 col-span-1" id="task-deadline">
            </div>
            <button type="button" onclick="addTaskRow()"
                class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Add Task
            </button>

            <div class="mt-6">
  <h3 class="text-md font-semibold text-gray-800 mb-2">Budgeting Proposal</h3>
  <table class="w-full table-auto border text-sm mb-2">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="p-2 border">No.</th>
        <th class="p-2 border">Article</th>
        <th class="p-2 border">Qty</th>
        <th class="p-2 border">UOM</th>
        <th class="p-2 border">Notes</th>
        <th class="p-2 border text-center">Action</th>
      </tr>
    </thead>
    <tbody id="itemTableBody">
      <!-- Rows inserted via JS -->
    </tbody>
  </table>

  <button type="button"
    onclick="addItemRow()"
    class="text-white bg-teal-600 px-3 py-1 rounded hover:bg-teal-700">
    + Add Item
  </button>
</div>

        </div>
</div>
<hr>
        <!-- Submit Button -->
        <div class="mt-6 justify-start">
            <button
                class="px-6 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-700 transition">
                Back
            </button>
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Submit
            </button>
        </div>
    </form>

<script>
    let index = 1;

    function addTaskRow() {
        const title = document.getElementById('task-title').value;
        const assigned = document.getElementById('task-assigned').value;
        const start = document.getElementById('task-start').value;
        const deadline = document.getElementById('task-deadline').value;

        if (!title || !assigned) {
            alert('Task title and assignee required.');
            return;
        }

        const rowId = `task-row-${Date.now()}`;

        const row = `
        <tr id="${rowId}" class="text-gray-700 border">
            <td class="p-2 border text-center">${index++}</td>
            <td class="p-2 border">${title}</td>
            <td class="p-2 border">${assigned}</td>
            <td class="p-2 border">${start}</td>
            <td class="p-2 border">${deadline}</td>
            <td class="p-2 border text-center">
                <button type="button" onclick="deleteTaskRow('${rowId}')" class="text-red-600 hover:text-red-800">
                    <i data-feather="trash-2"></i>
                </button>
            </td>
        </tr>`;

        document.getElementById('task-table-body').insertAdjacentHTML('beforeend', row);

        document.querySelectorAll('.task-input').forEach(input => input.value = '');

        feather.replace(); // Refresh Feather Icons
    }

    function deleteTaskRow(id) {
        const row = document.getElementById(id);
        if (row) {
            row.remove();
            updateIndex();
        }
    }

    function updateIndex() {
        index = 1;
        document.querySelectorAll('#task-table-body tr').forEach(tr => {
            tr.querySelector('td').textContent = index++;
        });
    }
</script>


@endsection