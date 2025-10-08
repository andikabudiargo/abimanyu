@extends('layouts.app')

@section('title', 'Ticket Management')
@section('page-title', 'DASHBOARD TICKET')
@section('breadcrumb-item', 'Helpdesk')
@section('breadcrumb-active', 'Ticket Management')

@section('content')

 <div class="p-6 bg-white rounded-lg shadow-md mb-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800 mb-2 sm:mb-0">Ticket Overview</h2>
      <p class="text-sm text-gray-500">
        Total <span class="font-bold text-gray-800">{{ $completionPercentage }}%</span> tickets completed this month 😎
      </p>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-200 mb-4"></div>

    <!-- Grid Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
      <!-- Total Ticket -->
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-purple-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="percent" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Total</p>
          <p class="text-xl font-bold text-gray-800">{{ $totalTickets }}</p>
        </div>
      </div>

      <!-- Pending -->
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-yellow-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="pause" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Pending</p>
          <p class="text-xl font-bold text-gray-800">{{ $openTickets }}</p>
        </div>
      </div>

      <!-- Approved -->
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-green-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="check-circle" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Approved</p>
          <p class="text-xl font-bold text-gray-800">{{ $approvedTickets }}</p>
        </div>
      </div>

      <!-- Work in Progress -->
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-blue-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="tool" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">WIP</p>
          <p class="text-xl font-bold text-gray-800">{{ $wipTickets }}</p>
        </div>
      </div>

      <!-- Closed -->
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-teal-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="coffee" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Closed</p>
          <p class="text-xl font-bold text-gray-800">{{ $closedTickets }}</p>
        </div>
      </div>

      <!-- Overdue
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-red-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="calendar" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Overdue</p>
          <p class="text-xl font-bold text-gray-800">{{ $overdueTickets }}</p>
        </div>
      </div>-->
    </div> 

  <!-- Progress Bar -->
  <div class="mt-6">
    <p class="text-gray-600 text-sm mb-2">Tickets Completed This Month</p>
    <div class="w-full bg-gray-200 rounded-full h-4">
      <div class="bg-green-500 h-4 rounded-full" style="width: {{ $completionPercentage }}%;"></div>
    </div>
    <p class="text-right text-gray-500 text-xs mt-1">{{ $completedThisMonth }} / {{ $totalThisMonth }}</p>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
    <!-- Kolom kiri: Ticket Assigned + Top 5 Issues -->
    <div class="flex flex-col gap-2">
          <!-- Top 5 Issues -->
        <div class="bg-white p-6 rounded-2xl shadow-md h-full">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">Top 5 Issues</h2>
                <span class="text-xs text-gray-500">Based on ticket count</span>
            </div>
            <div class="border-t border-gray-200 mb-4"></div>
            <div class="space-y-4">
                @foreach($topCategories as $cat)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-700">
                                {{ $cat->category ?? $cat->name }}
                            </span>
                            <span class="text-xs text-gray-600">
                                {{ $cat->total ?? $cat->tickets_count }} Ticket
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 relative overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500"
                                 style="width: {{ (($cat->total ?? $cat->tickets_count) / $topCategories->max('total')) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Ticket Assigned -->
        <div class="bg-white p-6 rounded-2xl shadow-md h-full">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">Ticket Assigned</h2>
                <span class="text-xs text-gray-500">Based on ticket assign</span>
            </div>
            <div class="border-t border-gray-200 mb-4"></div>
            <div class="w-48 h-48 mx-auto mb-4">
                <canvas id="DonutChart" class="w-full h-full"></canvas>
            </div>

            <div class="flex items-center justify-between bg-blue-500 p-2 rounded-lg shadow-sm">
                <!-- Department IT -->
                <div class="flex-1 text-center">
                    <p class="text-white text-xs">Information & Technology</p>
                    <p class="text-2xl font-bold text-green-400">
                        +{{ $assignTickets->where('department_name','Information & Technology')->first()->total ?? 0 }}
                    </p>
                </div>

                <div class="w-px bg-gray-300 mx-4 h-12"></div>

                <!-- Department Maintenance -->
                <div class="flex-1 text-center">
                    <p class="text-white text-xs">Maintenance</p>
                    <p class="text-2xl font-bold text-yellow-400">
                        +{{ $assignTickets->where('department_name','Maintenance')->first()->total ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Kolom kanan: Bar Chart -->
    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-md h-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Tickets by Department</h2>
            <span class="text-xs text-gray-500">Based on department request</span>
        </div>
        <div class="border-t border-gray-200 mb-4"></div>
        <canvas id="deptChart" height="200"></canvas>
    </div>
</div>

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Ticket</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Ticket Number</label>
                <input id="filter-ticket-number" type="text" name="ticket_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
    <input id="filter-date" type="text" name="date"  placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
    
</div>

            <div>
    <label for="filter-category" class="block text-sm mb-1 font-medium text-gray-700">Category</label>
    <select id="filter-category" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
       
    </select>

    
</div>

<!-- Status -->
<div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Work in Progress">Work in Progress</option>
        <option value="On Hold">On Hold</option>
        <option value="Done">Done</option>
        <option value="Closed">Closed</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>

<!-- Department -->
<div>
    <label for="filter-department" class="block text-sm mb-1 font-medium text-gray-700">Department</label>
    <select id="filter-department" name="department" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
        @endforeach
    </select>
</div>

@php
    $userDepartments = Auth::user()->departments->pluck('name')->toArray();
@endphp

@if(in_array('Maintenance', $userDepartments) || in_array('Information & Technology', $userDepartments))
<div>
    <label for="filter-teknisi" class="block text-sm mb-1 font-medium text-gray-700">Teknisi</label>
    <select id="filter-teknisi" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Andika Budiargo">Andika Budiargo</option>
        <option value="Ivan Jovian">Ivan Jovian</option>
        <option value="Iwan Kuswandi">Iwan Kuswandi</option>
    </select>
</div>
@endif
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('it.ticket.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold">Ticket List</h2>
    <table id="ticket-table" class="w-full text-sm text-left">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Ticket Number</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2 w-28 !text-center">Status</th>
                    <th class="px-4 py-2 text-center">Priority</th>
                    <th class="px-4 py-2">Department</th>
                    <th class="px-4 py-2">Request by</th>
                    <th class="px-4 py-2">Request at</th>
                    <th class="px-4 py-2">Approved by</th>
                    <th class="px-4 py-2">Approved at</th>
                    <th class="px-4 py-2">Assign by</th>
                    <th class="px-4 py-2">Assign at</th>
                    <th class="px-4 py-2">Done at</th>
                    <th class="px-4 py-2">Closed at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- DataTables akan mengisi tbody --}}
            </tbody>
        </table>
</div>
<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-2xl transform transition-all scale-95">
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-red-100 text-red-600 rounded-full">
               <i data-feather="alert-triangle"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Reject Ticket</h2>
        </div>

        <form id="rejectForm" class="space-y-4">
            @csrf
             <input type="hidden" name="ticket_id" id="reject_ticket_id">
            <!-- Reason -->
            <div>
                <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">
                    Reason for Rejection
                </label>
                <textarea 
                    name="rejected_reason" 
                    id="rejected_reason" 
                    rows="4" 
                    required
                   placeholder="e.g. Duplicate request, not under IT scope, issue already resolved, invalid request details..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve future requests.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="closeRejectModal()"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-white hover:bg-gray-100 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition"
                >
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<div id="processModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Process Ticket</h3>
        <form id="processForm" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" id="process_ticket_id">

            {{-- Due Date --}}
            <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="datetime-local" name="due_date" id="due_date" placeholder="Set Due Date"
                    class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            </div>

           {{-- Priority --}}
<div class="mb-4">
    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
    <select name="priority" id="priority"
        class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
        <option value="Low" class="text-green-600">Low</option>
        <option value="Medium" class="text-blue-600">Medium</option>
        <option value="Urgent" class="text-yellow-600">Urgent</option>
        <option value="Critical" class="text-red-600">Critical</option>
    </select>
</div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeProcessModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Submit</button>
            </div>
        </form>
    </div>
</div>


<!-- Need Purchase Modal -->
<div id="holdModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded w-full max-w-md">
    <h2 class="text-lg font-bold mb-4">Hold Ticket</h2>
    <form id="holdForm">
         @csrf
      <input type="hidden" name="ticket_id" id="hold_ticket_id">
      <div class="mb-4">
       <label for="hold_reason" class="block text-sm font-medium text-gray-700 mb-2">Hold Reason</label>
<select name="hold_reason" id="hold_reason" class="w-full px-3 py-2 border rounded" required>
    <option value="">-- Select Reason --</option>
    <option value="Waiting for Purchase">Waiting for Purchase</option>
    <option value="Waiting for Vendor">Waiting for Vendor</option>
    <option value="Other">Custom Reason</option>
</select>

<div id="custom_hold_reason_container" class="mt-2 hidden">
    <label for="custom_hold_reason" class="block text-sm font-medium text-gray-700">Other Reason</label>
    <textarea name="custom_hold_reason" id="custom_hold_reason" rows="3" class="w-full border rounded px-3 py-2"></textarea>
</div>

      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeHoldModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
      </div>
    </form>
  </div>
</div>
<!-- Modal -->
<div id="holdReasonModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md relative">
    <!-- Header -->
    <div class="flex items-center justify-between border-b pb-3 mb-4">
      <div class="flex items-center gap-2">
        <i data-feather="alert-circle" class="text-yellow-500 w-6 h-6"></i>
        <h3 class="text-lg font-semibold text-gray-800">Ticket On Hold</h3>
      </div>
      <button onclick="closeHoldReasonModal()" class="text-gray-400 hover:text-red-600">
        <i data-feather="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Content -->
    <div class="space-y-3 text-sm text-gray-700">
      <div>
        <span class="font-semibold">Reason:</span>
        <span id="modal_hold_reason" class="ml-1 text-gray-900"></span>
      </div>
      <div>
        <span class="font-semibold">Hold At:</span>
        <span id="modal_hold_start" class="ml-1 text-gray-900"></span>
      </div>
      <div>
        <span class="font-semibold">Duration:</span>
        <span id="modal_hold_duration" class="ml-1 text-gray-900"></span>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end mt-6">
      <button onclick="closeHoldReasonModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow">
        Close
      </button>
    </div>
  </div>
</div>


<!-- Modal -->
<div id="doneModal" class="hidden fixed z-50 inset-0 bg-black bg-opacity-40 flex items-center justify-center">
  <div class="bg-white p-6 rounded-md shadow-md w-full max-w-lg">
    <h3 class="text-lg font-bold mb-4">Mark as Done</h3>
    <form id="doneForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="ticket_id" id="modal_ticket_id">
      
      <label class="block mb-2">Corrective Action</label>
      <textarea name="corrective_action" required class="w-full border p-2 rounded mb-3"></textarea>

      <label class="block mb-2">Evidence (optional)</label>
     <input 
    type="file" 
    name="evidence[]" 
    accept="image/*" 
    capture="environment" 
    multiple
    class="w-full border p-2 rounded mb-3"
/>

      <div class="text-right">
        <button type="button" onclick="hideDoneModal()" class="bg-gray-400 px-4 py-2 text-white rounded mr-2">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal -->
   

<div id="closeModal" class="hidden fixed z-50 inset-0 bg-black bg-opacity-40 flex items-center justify-center">
  <div class="bg-white p-6 rounded-md shadow-md w-full max-w-lg">
    <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-green-100 text-green-600 rounded-full">
               <i data-feather="check-circle"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Close Ticket</h2>
        </div>
    <form id="closeForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="ticket_id" id="close_ticket_id">
      
       <div>
                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">
                    Feedback
                </label>
                <textarea 
                    name="feedback" 
                    id="feedback" 
                    rows="4" 
                   placeholder="write your feedback here..."
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 p-3 text-sm resize-y transition"
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">Please be specific to help us improve.</p>
            </div>

      <div class="text-right">
        <button type="button" onclick="hideCloseModal()" class="bg-gray-400 px-4 py-2 text-white rounded mr-2 hover:bg-gray-600">Cancel</button>
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Closed</button>
      </div>
    </form>
  </div>
</div>



{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#ticket-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#ticket-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}


/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* Non-Tailwind CSS */
#ticket-table td,
#ticket-table th {
    white-space: nowrap;
}


/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    position: relative;
    z-index: 1;
    margin-left: 10px;
}


/* Ukuran tombol collection (export) */
.dt-button.buttons-collection {
    font-size: 0.875rem; /* text-sm */
    padding: 0.4rem 1rem;
}

.dt-button-down-arrow {
    display: none !important;
}

div.dt-button-collection {
    top: 100% !important;
    margin-top: 0.5rem !important; /* Jarak dari tombol */
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 9999 !important;
}


/* Dropdown Export agar tampil di bawah */
div.dt-button-collection {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    margin-top: 0.5rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 10000;
}

/* Item dropdown */
div.dt-button-collection .dt-button {
    color: #1f2937;
    padding: 0.5rem 1rem;
    text-align: left;
    width: 100%;
}

div.dt-button-collection .dt-button:hover {
    background-color: #dfe0e0ff;
}


/* 🧭 Spacing */
#ticket-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#ticket-table th, #ticket-table td {
    border: none !important;
}

.select2-container {
    width: 100% !important;
}


 .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 4px 10px !important;
        border: 1px solid #d1d5db !important; /* gray-300 */
        border-radius: 0.375rem !important; /* rounded-md */
        font-size: 1rem !important; /* text-base */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px;
    }

</style>
<script>
flatpickr("#due_date", {
    enableTime: true,
    noCalendar: false,
    dateFormat: "Y-m-d H:i",
    time_24hr: true
});
    const processedData = @json($processedTickets);

const labels = processedData.map(item => item.processed_name);
const data = processedData.map(item => item.total);

const ctxDonut = document.getElementById('DonutChart').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            label: 'Tickets Processed',
            data: data,
            backgroundColor: [
                '#4CAF50','#FF9800','#2196F3','#E91E63','#9C27B0',
                '#FF5722','#00BCD4','#FFC107','#8BC34A','#3F51B5'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a,b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
  const ctxDept  = document.getElementById('deptChart').getContext('2d');

new Chart(ctxDept , {
    type: 'bar',
    data: {
        labels: @json($deptTickets->map(fn($d) => $d->department_name)),
        datasets: [{
            label: 'Total Tickets',
            data: @json($deptTickets->map(fn($d) => $d->total)),
            backgroundColor: 'rgba(37, 99, 235, 0.7)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { font: { size: 12 } } },
            y: { 
                beginAtZero: true,
                ticks: {
                    stepSize: 10,  // jarak antar nilai
                    callback: function(value) {
                        return value; // tampilkan angka tanpa koma
                    }
                },
                suggestedMax: 100 // agar maksimum Y-axis 100
            }
        }
    }
});


    const prioritySelect = document.getElementById('priority');

    function updatePriorityColor() {
        const value = prioritySelect.value;
        prioritySelect.classList.remove('text-green-600', 'text-blue-600', 'text-yellow-600', 'text-red-600');

        if (value === 'Low') {
            prioritySelect.classList.add('text-green-600');
        } else if (value === 'Medium') {
            prioritySelect.classList.add('text-blue-600');
        } else if (value === 'Urgent') {
            prioritySelect.classList.add('text-yellow-600');
        } else if (value === 'Critical') {
            prioritySelect.classList.add('text-red-600');
        }
    }

    // Set warna awal & update saat berubah
    updatePriorityColor();
    prioritySelect.addEventListener('change', updatePriorityColor);


function rejectTicket(ticketId) {
    $('#reject_ticket_id').val(ticketId);
    $('#rejectModal').removeClass('hidden');
    setTimeout(() => {
        $('#rejectModal .modal-content').removeClass('scale-95 opacity-0');
    }, 10);
}

function closeRejectModal() {
    $('#rejectModal .modal-content').addClass('scale-95 opacity-0');
    setTimeout(() => {
        $('#rejectModal').addClass('hidden');
    }, 200);
}


  function showHoldReason(reason, start, duration) {
    document.getElementById('modal_hold_reason').innerText = reason;
    document.getElementById('modal_hold_start').innerText = start;
    document.getElementById('modal_hold_duration').innerText = duration;
    document.getElementById('holdReasonModal').classList.remove('hidden');
  }

  function closeHoldReasonModal() {
    document.getElementById('holdReasonModal').classList.add('hidden');
  }

    document.getElementById('hold_reason').addEventListener('change', function () {
    const container = document.getElementById('custom_hold_reason_container');
    if (this.value === 'Other') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
});

    function openProcessModal(ticketId) {
    $('#process_ticket_id').val(ticketId);
    $('#processForm').attr('action', `/it/ticket/${ticketId}/process`);
    $('#processModal').removeClass('hidden');
}

function closeProcessModal() {
    $('#processModal').addClass('hidden');
}

function openHoldModal(ticketId) {
    document.getElementById('hold_ticket_id').value = ticketId;
    document.getElementById('holdModal').classList.remove('hidden');
}

function closeHoldModal() {
    document.getElementById('holdModal').classList.add('hidden');
}

function showDoneModal(ticketId) {
    document.getElementById('modal_ticket_id').value = ticketId;
    document.getElementById('doneModal').classList.remove('hidden');
  }

  function hideDoneModal() {
    document.getElementById('doneModal').classList.add('hidden');
  }

  function showCloseModal(ticketId) {
    document.getElementById('close_ticket_id').value = ticketId;
    document.getElementById('closeModal').classList.remove('hidden');
  }

  function hideCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
  }

  function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type, // success, error, info, warning
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// ========== FORM SUBMITS ========== //

$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let ticketId = $('#reject_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/reject`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
            $('#ticket-table').DataTable().ajax.reload(null, false);
            closeRejectModal();
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
    });
});

$('#holdForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let ticketId = $('#hold_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/hold`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
             $('#ticket-table').DataTable().ajax.reload(null, false);
             closeHoldModal();
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
    });
});

$('#processForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let action = form.attr('action');
    let data = form.serialize();

    $.post(action, data, function (res) {
        if (res.success) {
            showToast('success', 'Ticket is now in progress!');
             $('#ticket-table').DataTable().ajax.reload(null, false);
             closeProcessModal();
        } else {
            showToast('error', res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred while processing.');
    });
});

$('#doneForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const ticketId = $('#modal_ticket_id').val();

    $.ajax({
        url: `/it/ticket/${ticketId}/done`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            showToast('success', res.message);
            hideDoneModal();
             $('#ticket-table').DataTable().ajax.reload(null, false);
        },
        error: function(err) {
            showToast('error', err.responseJSON?.error || 'Terjadi kesalahan');
        }
    });
});

$('#closeForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const ticketId = $('#close_ticket_id').val();

    $.ajax({
        url: `/it/ticket/${ticketId}/close`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            showToast('success', res.message);
            hideCloseModal();
             $('#ticket-table').DataTable().ajax.reload(null, false);
        },
        error: function(err) {
            showToast('error', err.responseJSON?.error || 'Terjadi kesalahan');
        }
    });
});

// ========== ACTIONS ========== //

function resumeTicket(id) {
    Swal.fire({
        title: 'Resume Ticket?',
        text: 'Resume this ticket and continue progress?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Resume'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${id}/resume`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                if (res.success) {
                    showToast('success', 'Ticket resumed to In Progress.');
                     $('#ticket-table').DataTable().ajax.reload(null, false);
                } else {
                    showToast('error', res.message || 'Failed to resume ticket.');
                }
            }).fail(function (err) {
                console.error(err.responseText);
                showToast('error', 'An error occurred while resuming the ticket.');
            });
        }
    });
}

function closeTicket(id) {
    Swal.fire({
        title: 'Close Ticket?',
        text: 'Are you sure you want to close this ticket?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Close'
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${id}/close`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                if (res.success) {
                    showToast('success', 'Ticket has been closed successfully.');
                     $('#ticket-table').DataTable().ajax.reload(null, false);
                } else {
                    showToast('error', res.message || 'Failed to close ticket.');
                }
            }).fail(function () {
                showToast('error', 'Server error while closing the ticket.');
            });
        }
    });
}

function approveTicket(id, ticketNumber) {

    Swal.fire({
        title: 'Approve Ticket?',
        html: `Approve this Ticket: <strong>${ticketNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Ticket has been Approved: ' + res.ticket_number);
                 $('#ticket-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui tiket.');
            });
        }
    });
}

 function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/it/ticket/${id}/destroy`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.fire('Dihapus!', res.message, 'success');
                    $('#ticket-table').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal!', 'Tidak dapat menghapus ticket.', 'error');
                }
            });
        }
    });
}

let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#ticket-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX:true,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
},
       ajax: {
            url: '{{ route("it.ticket.data") }}',
            data: function (d) {
                d.ticket_number = $('#filter-ticket-number').val();
                d.status = $('#filter-status').val();
                d.processed_by = $('#filter-teknisi').val();
                d.category = $('#filter-category').val();
                d.date = $('#filter-date').val();
                d.department = $('#filter-department').val();
            }
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
       buttons: [
    {
        extend: 'collection',
        text: '<i class="fas fa-download mr-2"></i>Export',
        className: 'bg-blue-600 text-white px-4 py-1 text-sm rounded shadow-sm flex items-center',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy mr-2"></i>Copy',
            },
            {
                extend: 'excelHtml5',
                filename: 'Helpdesk_Ticket_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'Helpdesk_Ticket_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                orientation: 'landscape',
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 8;  // header tabel
        doc.defaultStyle.fontSize = 7;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'Helpdesk Ticket ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            },
             {
    text: '<i class="fas fa-chart-pie mr-1" style="font-size: 12px;"></i>Report',
    className: 'text-sm rounded shadow-sm flex items-center',
    action: function (e, dt, node, config) {
        window.open('/it/ticket/report', '_blank'); // ganti sesuai URL
    }
},
        ]
    },

    
    
],
      columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'ticket_number', name: 'ticket_number',  orderable: false },
        { data: 'category', name: 'category', orderable: false },
        { data: 'title', name: 'title', orderable: false },
        { data: 'status', name: 'status',  className: 'text-center', orderable: false },
        { data: 'priority', name: 'priority', className: 'text-center', orderable: false },
        { data: 'department', name: 'requestor.departments.name', orderable: false }, // relasi pivot
        { data: 'request_by', name: 'requestor.name',orderable: false },
        { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false },
        { data: 'approved_by', name: 'approved.name', orderable: false },
        { data: 'approved_at', name: 'approved_at',  className: 'text-center', orderable: false },
        { data: 'processed_by', name: 'process.name', orderable: false },
        { data: 'processed_at', name: 'processed_at',  className: 'text-center', orderable: false },
        { data: 'done_at', name: 'done_at',  className: 'text-center', orderable: false },
        { data: 'closed_at', name: 'closed_at',  className: 'text-center', orderable: false },
      ]
    });
    feather.replace(); // ⬅️ Ini untuk memastikan ikon feather muncul ulang setiap render
       // Trigger filter saat tombol Search ditekan
        $('#filter-form').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
  });

  $(document).ready(function () {
    $('.select2').select2({
        width: 'resolve', // atau '100%' bisa juga
        theme: 'default' // biar tidak override tailwind terlalu banyak
    });

    // Sinkronisasi tinggi agar konsisten dengan input biasa
    $('.select2').on('select2:open', function (e) {
        $('.select2-container--default .select2-selection--single').css({
            'height': '38px', // sama dengan input
            'padding': '4px 10px', // padding input
            'border': '1px solid #d1d5db', // warna border tailwind gray-300
            'border-radius': '0.375rem' // rounded-md
        });
    });
});


 let openDropdown = null;

function toggleDropdown(id, event) {
  const trigger = event.currentTarget;
  const existingDropdown = document.getElementById('global-dropdown');

  // Hapus dropdown lama jika ada
  if (existingDropdown) {
    existingDropdown.remove();
    if (openDropdown === id) {
      openDropdown = null;
      return;
    }
  }

  // Ambil isi dropdown dari elemen tersembunyi
  const dropdownTemplate = document.getElementById(id);
  if (!dropdownTemplate) return;

  // Buat dropdown baru
  const newDropdown = document.createElement('div');
  newDropdown.id = 'global-dropdown';
  newDropdown.className = 'absolute z-[9999] w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700';
  newDropdown.innerHTML = dropdownTemplate.innerHTML;
  document.body.appendChild(newDropdown);

  // Hitung posisi tombol
  const rect = trigger.getBoundingClientRect();
  newDropdown.style.position = 'fixed';
  newDropdown.style.top = `${rect.bottom + 4}px`;
  newDropdown.style.left = `${rect.left}px`;

  openDropdown = id;
}

// Tutup saat klik di luar
document.addEventListener('click', function (e) {
  const dropdown = document.getElementById('global-dropdown');
  if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button[data-dropdown-id]')) {
    dropdown.remove();
    openDropdown = null;
  }
});




  // Inisialisasi Flatpickr
flatpickr("#filter-date", {
    mode: "range",
    dateFormat: "Y-m-d"
});

// Event submit form filter
$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    $('#ticket-table').DataTable().ajax.reload();
});


document.addEventListener("DOMContentLoaded", function () {
    fetch('/it/category/dropdown')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('filter-category');
            select.innerHTML = ''; // Kosongkan dulu

            // Tambahkan placeholder
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '-- All --';
            placeholder.selected = true;
            select.appendChild(placeholder);

            data.forEach(group => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = group.label;

                group.options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.id;
                    opt.textContent = option.description; // Tampilkan deskripsi
                    optgroup.appendChild(opt);
                });

                select.appendChild(optgroup);
            });
        })
        .catch(error => {
            console.error('Gagal memuat kategori:', error);
        });
});
  </script>
@endpush


@endsection