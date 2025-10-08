@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-active', 'Dashboard')

@section('content')
<div class="min-h-screen overflow-hidden">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Kiri: Card + Ticket --}}
    <div class="md:col-span-2 space-y-6">
       {{-- User Card --}}
<div class="bg-white shadow-md overflow-hidden">
    <!-- Cover -->
 <div class="relative h-32 bg-cover bg-center"
     style="background-image: url('{{ asset('img/bg.jpeg') }}');">
    <!-- Logo -->
  <img src="{{ asset('img/logo.png') }}" 
     class="absolute inset-0 w-full h-full object-contain p-4" 
     alt="Logo">

</div>


    <div class="relative flex items-center px-6 pb-6">
        <!-- Avatar -->
        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
            class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg -mt-20" alt="Avatar">

        <!-- User Info -->
        <div class="ml-6 mt-3 flex-1">
            <h2 class="text-2xl font-semibold text-gray-800">{{ Auth::user()->name }}</h2>
            <p class="text-gray-600">{{ Auth::user()->departments->pluck('name')->join(', ') ?: '-' }}</p>
            <p class="text-gray-500">PT. Abimanyu Sekar Nusantara</p>

            <!-- Email & Phone -->
            <div class="flex flex-wrap gap-3 mt-3 text-sm">
                <span class="px-3 py-1 bg-gray-100 rounded-lg text-gray-700 shadow-sm flex items-center gap-2">
    <i data-feather="mail" class="w-4 h-4"></i>
   {{ optional(Auth::user())->email ?? 'No Email Added' }}
</span>

            </div>
        </div>

    </div>
</div>
    </div>

   <div class="bg-white shadow-md p-6">
   <!-- Responsive Agenda Header (mobile-first) -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <!-- Left: icon + title -->
  <div class="flex items-start sm:items-center gap-3 min-w-0">
    <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
      📅
    </span>

    <div class="min-w-0">
      <h2 class="text-base sm:text-lg font-semibold text-gray-800 leading-tight truncate">
        Your Agenda
      </h2>
      <!-- subtitle only on larger screens -->
      <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">
        You have <span class="font-medium text-gray-700">3</span> items today
      </p>
    </div>
  </div>

  <!-- Right: action (mobile: full-width icon-only, desktop: icon + text) -->
  <div class="w-full sm:w-auto flex items-center gap-2">
    <button
    onclick="document.getElementById('todoModal').classList.remove('hidden')"
      id="btnAddAgenda"
      aria-label="Add agenda"
      title="Add agenda"
      class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-teal-500 rounded-lg hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200 transition">
      <i data-feather="plus" class="w-4 h-4"></i>
      <!-- hidden on mobile, visible on sm+ -->
      <span class="hidden sm:inline">Add</span>
    </button>
  </div>
</div>
    <hr class="mb-4">
{{-- Daftar To Do --}}
 {{-- Scrollable container --}}
    <div class="max-h-48 overflow-y-auto">
@if($todos->count())
<ul class="space-y-4">
    @foreach($todos as $todo)
    @php
        $isOverdue = !$todo->done && now()->gt(\Carbon\Carbon::parse($todo->agenda_time));
    @endphp
    <li class="relative flex flex-col  border border-gray-200 rounded-lg shadow-md bg-white rounded-lg shadow-sm transition hover:shadow-md
        {{ $isOverdue ? 'bg-red-50' : 'bg-white' }}">

        {{-- Header: Checkbox + Task title + Delete --}}
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center gap-3">
                <input type="checkbox"
                       class="todo-toggle h-5 w-5"
                       data-id="{{ $todo->id }}"
                       {{ $todo->done ? 'checked' : '' }}>

                <span class="text-gray-800 font-semibold {{ $todo->done ? 'line-through text-gray-400' : '' }}" id="task-text-{{ $todo->id }}">
                    {{ $todo->task }}
                </span>
            </div>

          <form action="{{ route('todo.destroy', $todo->id) }}" method="POST" class="todo-delete-form" data-id="{{ $todo->id }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-500 hover:text-red-700 px-2 py-1 rounded-md transition">
        <i data-feather="trash-2"></i>
    </button>
</form>

        </div>

        {{-- Body: scrollable --}}
        <div class="p-4">
            {{-- Description --}}
            @if(!empty($todo->description))
            <p class="text-gray-600 text-sm mb-3">
                {{ $todo->description }}
            </p>
            @endif

            {{-- Bottom bar: Reschedule + Agenda time + Overdue + Invited users --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Reschedule button --}}
                   {{-- Reschedule button --}}
@if($todo->done == 0)
    <button 
        class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 transition"
        onclick="openRescheduleModal({{ $todo->id }})">
        Reschedule
    </button>
@endif


                    {{-- Agenda time badge --}}
                    <span class="flex items-center gap-2 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-sm font-medium">
                        <i data-feather="calendar" class="w-5 h-5"></i>
                        {{ \Carbon\Carbon::parse($todo->agenda_time)->format('d M Y H:i') }}
                    </span>

                    {{-- Overdue indicator --}}
                    @if($isOverdue)
                        <span class="text-red-600 font-semibold">• Overdue</span>
                    @endif
                </div>

                {{-- Invited users icon --}}
                @if($todo->users->count())
                <div class="relative group">
                    <i data-feather="users" class="w-6 h-6 text-gray-500 cursor-pointer"></i>
                    {{-- Tooltip --}}
                    <div class="absolute right-0 mt-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md p-2 shadow-lg z-10">
                        Invited: {{ $todo->users->pluck('name')->implode(', ') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </li>
    @endforeach
</ul>
    
@else
<p class="text-gray-500 mt-4 text-center">There is no agenda today</p>
@endif
</div>




   </div>
</div>


<!-- Reschedule Modal -->
<div id="rescheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-lg font-semibold mb-4">Reschedule Agenda</h2>
        
        <form id="rescheduleForm">
            @csrf
            <input type="hidden" name="todo_id" id="rescheduleTodoId">

            <label for="newAgendaTime" class="block text-sm font-medium text-gray-700 mb-1">New Date & Time</label>
            <input type="datetime-local" id="newAgendaTime" name="agenda_time" class="w-full border border-gray-300 rounded-md p-2 mb-4">

            <div class="flex justify-end gap-2">
                <button type="button" class="bg-gray-300 text-white px-4 py-2 rounded hover:bg-gray-400" onclick="closeRescheduleModal()">Cancel</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
            </div>
        </form>
    </div>
</div>


   @php
    \Carbon\Carbon::setLocale('id');
@endphp

@if($activeBookings->count() || $ticketsToApprove->count() || $documentsToReview->count())
    <div class="bg-white rounded-xl shadow p-6 mt-6">
        <!-- Main Header -->
  <div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Action Center</h2>
    <p class="text-xs text-gray-500 mt-1">Quick access to approve or reject submission.</p>
    <div class="w-14 h-1 bg-teal-600 rounded mt-2"></div>
  </div>


        <!-- Booking Approval -->
        @if($activeBookings->count() > 0)
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    {{ Auth::user()->roles->pluck('name')->contains('Admin GA') ? 'Waiting Approval Bookings' : 'Your Active Room Bookings' }}
                </h2>
                <ul class="space-y-3">
                    @foreach($activeBookings as $booking)
                        <li class="bg-white border border-gray-200 rounded-lg shadow p-4 hover:shadow-md transition flex flex-col gap-2">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <i data-feather="calendar" class="w-10 h-10 text-teal-600"></i>
                                </div>
                                <div>
                                    <div class="text-teal-600 font-semibold">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d M Y') }}
                                        | {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
                                    </div>
                                    <div class="text-gray-600 text-sm leading-relaxed">
                                        <span class="font-medium text-gray-800">{{ $booking->creator->name }}</span>
                                        booked <span class="font-medium">{{ $booking->room->name }}</span>
                                        for {{ $booking->purpose }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                @if(Auth::user()->roles->pluck('name')->contains('Admin GA') && $booking->status === 'Waiting Approval')
                                    <button class="approveBtn w-24 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                            data-id="{{ $booking->id }}">
                                        Approve
                                    </button>
                                @endif
                                <button class="cancelBtn w-24 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700"
                                        data-id="{{ $booking->id }}">
                                    Cancel
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif


     <!-- Ticket Approval -->
        @if ($ticketsToApprove->count())
            <div class="mb-6">
      <button type="button" 
    onclick="toggleAccordion(this)" 
    aria-expanded="false"
    class="w-full text-left flex justify-between items-center px-6 py-4 
           text-gray-800 font-semibold border bg-teal-600 text-white group">

    <!-- Left Content -->
    <div class="flex items-center gap-3">
        <div>
            <i data-feather="feather" class="w-5 h-5"></i>
        </div>
        <span class="text-lg font-medium tracking-wide">
            @if($userRoleLabel === 'approve')
                Tickets Need Approval 
                (<span class="ticket-counter">{{ $ticketsToApprove->count() }}</span>)
            @elseif($userRoleLabel === 'process')
                Tickets Need to Be Processed 
                (<span class="ticket-counter">{{ $ticketsToApprove->count() }}</span>)
            @endif
        </span>
    </div>

    <!-- Chevron -->
   <i data-feather="chevron-down" class="chevron-icon w-5 h-5 transition-transform duration-300"></i>

</button>





        <div class="accordion-content hidden mt-4 max-h-[500px] overflow-y-auto border-t border-gray-200 pt-4">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                    <tr>
                        <th class="px-4 py-2">Subject</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Requested By</th>
                        <th class="px-4 py-2">Requested At</th>
                        <th class="px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ticketsToApprove as $ticket)
                        <tr id="ticket-row-{{ $ticket->id }}" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $ticket->title }}</td>
                            <td class="px-4 py-2"> {{ $ticket->category->description ?? 'No Category' }}</td>
                            <td class="px-4 py-2">{{ optional($ticket->requestor)->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{  $ticket->created_at }}</td>
                            <td class="px-4 py-2 text-center flex justify-center gap-2">
                                <a href="{{ route('it.ticket.show', $ticket->id) }}"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Detail
                                </a>

                                @if($userRoleLabel === 'approve')
                                    <button onclick="approveTicket({{ $ticket->id }})"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button onclick="rejectTicket({{ $ticket->id }})"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                @elseif($userRoleLabel === 'process')
                                    <button onclick="openProcessModal({{ $ticket->id }})"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                        Process
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

 <!-- Document Approval -->
        @if ($documentsToReview->count())
            <div class="mb-6">
       <button type="button" 
    onclick="toggleAccordion(this)" 
    aria-expanded="false"
    class="w-full text-left flex justify-between items-center px-6 py-4 
           text-gray-800 font-semibold border bg-teal-600 text-white group">

    <!-- Left Content -->
    <div class="flex items-center gap-3">
        <div>
            <i data-feather="feather" class="w-5 h-5"></i>
        </div>
                <span class="text-lg font-semibold tracking-wide">
                    {{ $documentSectionTitle }}
                    (<span class="document-counter">{{ $documentsToReview->count() }}</span>)
                </span>
            </div>

            <svg class="w-5 h-5 transform transition-transform duration-300 group-[.active]:rotate-180" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="accordion-content hidden mt-4 max-h-[500px] overflow-y-auto border-t border-gray-200 pt-4">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                    <tr>
                        <th class="px-4 py-2">Doc Number</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Created by</th>
                        <th class="px-4 py-2">Created at</th>
                        <th class="px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentsToReview as $doc)
                        <tr id="document-row-{{ $doc->id }}" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $doc->document_number }}</td>
                            <td class="px-4 py-2">{{ $doc->title }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">
                                    {{ $doc->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($doc->revisions->last()->requestor)->name ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($doc->revisions->last())->created_at }}
                            </td>
                            <td class="px-4 py-2 text-center flex justify-center gap-2">
                                <a href="{{ route('mr.doc.show', $doc->id) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Detail
                                </a>

                                {{-- Jika Supervisor → tampilkan tombol Approve / Reject --}}
                                @if($documentSectionTitle === 'Documents Submission Need Your Approval')
                                    <button onclick="approveDOC({{ $doc->id }})"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button onclick="rejectDOC({{ $doc->id }})"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                @endif

                                {{-- Jika MR → tampilkan tombol Review --}}
                                @if($documentSectionTitle === 'Documents Need Your Review')
                                    <button onclick="reviewDOC({{ $doc->id }})"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                        Review
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
</div>
@endif

<div class="bg-white shadow-md mt-4 overflow-hidden">
  <!-- NAVIGATION TABS ala Dribbble -->
 <div class="border-b border-gray-200 bg-gray-50">
  <nav class="flex gap-6 overflow-x-auto px-6">
    <!-- Information Center -->
    <button 
      class="tab-btn relative flex items-center gap-2 py-4 text-sm font-medium text-teal-600 border-b-2 border-teal-600"
      data-tab="information">
      <i data-feather="info" class="w-5 h-5"></i>
       <span class="hidden md:inline">Information Center</span>
    </button>

    <!-- Schedule -->
    <button 
      class="tab-btn relative flex items-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-teal-600"
      data-tab="schedule">
      <i data-feather="calendar" class="w-5 h-5"></i>
       <span class="hidden md:inline">Schedule</span>
    </button>

    <!-- Performance -->
    <button 
      class="tab-btn relative flex items-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-teal-600"
      data-tab="performance">
      <i data-feather="bar-chart-2" class="w-5 h-5"></i>
       <span class="hidden md:inline">Performance</span>
    </button>

    <!-- Company -->
    <button 
      class="tab-btn relative flex items-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-teal-600"
      data-tab="company">
      <i data-feather="briefcase" class="w-5 h-5"></i>
       <span class="hidden md:inline">Company</span>
    </button>
  </nav>
</div>

<!-- TAB CONTENT -->
<div class="tab-content" id="information">

  <!-- Personal Notifications -->
  <div class="bg-white p-6 w-full mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
      <div class="flex items-start sm:items-center gap-3 min-w-0">
        <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
          🔔
        </span>
        <div class="min-w-0">
          <h2 class="text-base sm:text-lg font-semibold text-gray-800 leading-tight">
            Personal Notifications
          </h2>
          <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">
            Notifications specific to you
          </p>
        </div>
      </div>
      <div class="w-full sm:w-auto">
      </div>
    </div>
    <hr>

    <ul class="mt-4 space-y-3">
        <li class="p-3 bg-gray-50 text-center text-gray-500 rounded">No Personal Notifications</li>
    </ul>
  </div>

  <!-- Company Announcements -->
  <div class="bg-white shadow-md p-6 w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
      <div class="flex items-start sm:items-center gap-3 min-w-0">
        <span class="flex-shrink-0 w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
          📢
        </span>
        <div class="min-w-0">
          <h2 class="text-base sm:text-lg font-semibold text-gray-800 leading-tight">
            Company Announcements
          </h2>
          <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">
            Latest news & updates for everyone
          </p>
        </div>
      </div>
      <div class="w-full sm:w-auto">
        <button
          id="btnAdd"
          aria-label="Add announcement"
          class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-teal-500 rounded-lg hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200 transition">
          <i data-feather="plus" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Add Announcement</span>
        </button>
      </div>
    </div>
    <hr>

    <ul class="mt-4 space-y-3">
      @forelse($announcements as $announcement)
        <li class="p-3 bg-gray-100 rounded hover:bg-gray-200 transition duration-200">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-2 font-semibold text-gray-800 text-lg">
              @if($announcement->category === 'Event')
                <i data-feather="calendar" class="text-green-500 w-4 h-4"></i>
              @elseif($announcement->category === 'Information')
                <i data-feather="info" class="text-blue-500 w-4 h-4"></i>
              @elseif($announcement->category === 'Warning')
                <i data-feather="alert-triangle" class="text-yellow-500 w-4 h-4"></i>
              @else
                <i data-feather="speaker" class="text-gray-500 w-4 h-4"></i>
              @endif
              {{ $announcement->title }}
            </div>
            <div class="text-xs text-gray-400">
              {{ $announcement->createdBy->name ?? '-' }} - {{ $announcement->createdBy->departments->first()->name ?? '-' }}
            </div>
          </div>

          @php
            $fullText = $announcement->description ?? '';
            $shortText = \Illuminate\Support\Str::limit(strip_tags($fullText), 100, '...');
          @endphp

          <div class="text-gray-600 text-sm mt-1">
            <div class="announcement-desc">
              {!! strlen(strip_tags($fullText)) > 100 ? $shortText : $fullText !!}
            </div>
            @if(strlen(strip_tags($fullText)) > 100)
              <button type="button" class="text-blue-500 text-xs ml-2 read-more-btn">Read more</button>
              <div class="hidden full-text mt-1">{!! $fullText !!}</div>
            @endif
          </div>

          @if($announcement->attachments->count())
            <div class="mt-2 flex flex-wrap gap-2">
              @foreach($announcement->attachments as $file)
                <a href="{{ Storage::url($file->filename) }}" target="_blank"
                  class="px-2 py-1 bg-white border rounded shadow text-xs text-blue-600 hover:bg-blue-50">
                  {{ $file->path }}
                </a>
              @endforeach
            </div>
          @endif
        </li>
      @empty
        <li class="p-3 bg-gray-50 text-center text-gray-500 rounded">No Announcement Today</li>
      @endforelse
    </ul>
  </div>

</div>


  <div class="tab-content hidden" id="schedule">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
    <!-- Fleet Schedule Today -->
  <div class="bg-white rounded-2xl shadow p-4">
 <div class="bg-blue-500 rounded-lg mb-4 text-white p-4 shadow-md flex items-center gap-3">
    <i data-feather="truck" class="w-6 h-6"></i>
  <h2 class="text-lg text-white font-semibold">Fleet Schedule Today</h2>
</div>

  <div class="relative">
    <!-- Garis vertikal -->
    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

    <!-- Item Timeline -->
    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-blue-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">09:30 WIB (Grand Max)</p>
        <p class="text-sm text-gray-800">Mengantar Part ke Frina</p>
      </div>
    </div>

    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-blue-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">10:00 WIB (CDD ASN)</p>
        <p class="text-sm text-gray-800">Pick up Raw Material</p>
      </div>
    </div>

    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-blue-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">12:00 WIB (Luxio)</p>
        <p class="text-sm text-gray-800">Meeting ke PT. Autoplastik Indonesia</p>
      </div>
    </div>
  </div>
   <!-- Tombol Booking Now -->
  <div class="mt-4 text-center">
    <a href="/fleet-booking"
      class="w-full inline-block bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
      Booking Now
    </a>
  </div>
</div>




  <!-- Room Meeting Schedule Today -->
<div class="bg-white rounded-2xl shadow p-4">
  <div class="bg-green-500 rounded-lg mb-4 text-white p-4 shadow-md flex items-center gap-3">
    <i data-feather="home" class="w-6 h-6"></i>
    <h2 class="text-lg text-white font-semibold">Room Meeting Schedule Today</h2>
  </div>

  <div id="timelineContainer" class="relative">
    <!-- Timeline akan diisi jQuery -->
  </div>
  <div class="mt-4 text-center">
    <a href="{{ route('facility.booking-room.index') }}"
      class="w-full inline-block bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
      Booking Now
    </a>
  </div>
</div>

<!-- Fleet Schedule Today -->
  <div class="bg-white rounded-2xl shadow p-4">
 <div class="bg-yellow-500 rounded-lg mb-4 text-white p-4 shadow-md flex items-center gap-3">
    <i data-feather="coffee" class="w-6 h-6"></i>
  <h2 class="text-lg text-white font-semibold">Catering Menu Today</h2>
</div>

  <div class="relative">
    <!-- Garis vertikal -->
    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

    <!-- Item Timeline -->
    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-yellow-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">12:00 WIB</p>
        <p class="text-sm text-gray-800">Telor Ceplok Balado</p>
      </div>
    </div>

    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-yellow-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">18:00 WIB</p>
        <p class="text-sm text-gray-800">Ikan Tongkol</p>
      </div>
    </div>

    <div class="flex items-start mb-6 relative">
      <div class="absolute left-2 top-1 w-4 h-4 bg-yellow-500 rounded-full border-4 border-white"></div>
      <div class="ml-10">
        <p class="text-xs text-gray-500">00:00 WIB</p>
        <p class="text-sm text-gray-800">Ayam Goreng</p>
      </div>
    </div>
  </div>
 <div class="mt-4 flex justify-center gap-3">
    <button class="w-28 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      Eat
    </button>
    <button class="w-28 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      Skip
    </button>
  </div>
</div>
</div>
  </div>
  <div class="tab-content hidden" id="performance">
    <div class="bg-white shadow rounded-xl p-6">📊 Performance Content here</div>
  </div>
  <div class="tab-content hidden" id="company">
    <div class="bg-white shadow rounded-xl p-6">🏢 Company Content here</div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mt-4">
  <!-- Main Header -->
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Employee Self Service</h2>
    <p class="text-xs text-gray-500 mt-1">Quick access to all employee requests & services</p>
    <div class="w-14 h-1 bg-teal-600 rounded mt-2"></div>
  </div>

  <!-- HR & Attendance -->
  <div class="mb-6">
    <div class="flex items-center mb-3">
      <div class="w-1.5 h-5 bg-teal-600 rounded mr-2"></div>
      <h3 class="text-sm font-semibold text-gray-800">HR & Attendance</h3>
    </div>
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">⏰</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Overtime</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📅</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Day Off</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚶</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Exit Permit</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">👋</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Resign</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">👷</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Employee Request</span>
      </a>
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🛫</div>
        <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Business Trip</span>
      </a>
    </div>
  </div>

   <!-- HR & Attendance -->
  <div class="mb-6">
    <div class="flex items-center mb-3">
      <div class="w-1.5 h-5 bg-teal-600 rounded mr-2"></div>
      <h3 class="text-sm font-semibold text-gray-800">Facility & Services</h3>
    </div>
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
      <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📐</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">ATK Request</span>
    </a>
       <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🦺</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">APD Request</span>
    </a>
     <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚗</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Car Booking</span>
    </a>
       <a href="{{ route('facility.booking-room.index') }}" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🏢</div>
     <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Meeting Room</span>
    </a>
       <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">💼</div>
     <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Facility Request</span>
    </a>
       <a href="{{ route('facility.alo.index') }}" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">💻</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Asset Loan</span>
    </a>
        <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🛍</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Supplies Usage</span>
    </a>
         <a href="{{ route('it.ticket.create') }}" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🛠️</div>
      <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Helpdesk</span>
    </a>
  </div>
   </div>

 <div class="mb-6">
    <div class="flex items-center mb-3">
      <div class="w-1.5 h-5 bg-teal-600 rounded mr-2"></div>
      <h3 class="text-sm font-semibold text-gray-800">Document & Communication</h3>
    </div>
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
      <a href="{{ route('mr.doc.index') }}" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📄</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Document Submission</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📝</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Memo</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">📋</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">General Delivery Note</span>
    </a>
     <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚧</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Work Permit</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🚻</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Visitor Form</span>
    </a>
  </div>
  </div>

   <div class="mb-6">
    <div class="flex items-center mb-3">
      <div class="w-1.5 h-5 bg-teal-600 rounded mr-2"></div>
      <h3 class="text-sm font-semibold text-gray-800">Purchase & Finance</h3>
    </div>
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
    <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">💳</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Reimbursement</span>
    </a>
     <a href="#" class="flex flex-col items-center justify-center p-3 sm:p-4 bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition">
        <div class="text-xl sm:text-2xl mb-1">🛒</div>
       <span class="text-[11px] sm:text-xs font-medium text-gray-700 text-center">Purchase Request</span>
    </a>
  </div>
  </div>
</div>




{{-- Company Documents --}}
<!--<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
<div class="bg-white shadow-md rounded-xl p-6 max-h-80 overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">📄 Company Documents</h2>
    </div>
    <hr>

   
        <ul class="mt-4 space-y-3">
         
                <li class="flex items-center justify-between p-3 bg-gray-100 rounded hover:bg-gray-200 transition duration-200">
                    <div class="flex items-center gap-2">
                        <i data-feather="file-text" class="text-gray-600"></i>
                        <span class="font-medium text-gray-700"></span>
                    </div>
                    <div class="flex gap-2">
                        <a href="" 
                           class="px-3 py-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600">Download</a>
                        <a href="" 
                           class="px-3 py-1 text-xs text-white bg-green-500 rounded hover:bg-green-600">View</a>
                    </div>
                </li>
            
        </ul>
   
</div>
<div class="bg-white shadow-md rounded-xl p-6 max-h-80 overflow-y-auto">
    <div class="flex gap-2 items-center mb-4">
       <i data-feather="edit-3" class="text-gray-800"></i>  <h2 class="text-xl font-semibold text-gray-800">Company Survey</h2>
    </div>
    <hr>

   
        <ul class="mt-4 space-y-3">
         
                <li class="flex items-center justify-between p-3 bg-gray-100 rounded hover:bg-gray-200 transition duration-200">
                    <div class="flex items-center gap-2">
                        <i data-feather="file-text" class="text-gray-600"></i>
                        <span class="font-medium text-gray-700"></span>
                    </div>
                    <div class="flex gap-2">
                        <a href="" 
                           class="px-3 py-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600">Download</a>
                        <a href="" 
                           class="px-3 py-1 text-xs text-white bg-green-500 rounded hover:bg-green-600">View</a>
                    </div>
                </li>
            
        </ul>
   
</div>
</div>-->



<!-- Modal -->
<div id="announcementModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-1/2 max-h-[90vh] flex flex-col transform transition-all scale-95">
        <!-- Header -->
        <div class="flex justify-between items-center border-b p-4 bg-teal-400 rounded-t-xl">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-feather="bell" class="w-5 h-5"></i> Add Announcement
            </h2>
            <button id="closeModal" class="text-white hover:text-red-400 text-xl">&times;</button>
        </div>

        <!-- Body dengan scroll -->
        <div class="p-5 space-y-4 overflow-y-auto" style="max-height: calc(90vh - 80px);">
            <form id="announcementForm" method="post" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                        <select name="category" class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-300">
                            <option value="Information">Information</option>
                            <option value="Event">Event</option>
                            <option value="Warning">Caution/Warning</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Announcement Display Duration</label>
                        <input type="text" id="display-date" name="display_date" class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-300" required placeholder="YYYY-MM-DD to YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-300" placeholder="Enter title" required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-300" rows="5" placeholder="Enter description"></textarea>
                </div>

                <!-- Attachments -->
                <div class="col-span-2 mb-4">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                    <input type="file" name="attachments[]" id="attachments" multiple class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF, XLSX, Docs. Max file: 5MB.</p>
                </div>

               <!-- Recipients -->
<div>
    <label class="block mb-1 text-sm font-medium text-gray-700">Share to</label>
    <select id="recipients" name="recipients[]" class="recipients border rounded-lg p-2 w-full focus:ring focus:ring-blue-300" multiple>
        <option value="all">All</option>
        <?php foreach($departments as $department): ?>
            <option value="<?= $department->id ?>"><?= $department->name ?></option>
        <?php endforeach; ?>
    </select>
</div>


                <!-- Actions -->
               <div class="flex justify-end gap-2 pt-2 border-t">
    <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
        <i data-feather="share" class="h-4 w-4"></i>
        <span>Post</span>
    </button>
</div>
            </form>
        </div>
    </div>
</div>



<!-- Modal -->
<div id="todoModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Add New Agenda</h2>
        
        <form action="{{ route('todo.store') }}" method="POST">
            @csrf

            <!-- Nama Tugas -->
            <input type="text" name="task" placeholder="What would you do?"
                class="w-full mb-4 px-4 py-2 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-400"
                required>

            <!-- Waktu Agenda -->
            <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
            <input type="datetime-local" name="agenda_time"
                class="w-full mb-4 px-4 py-2 border rounded border-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-400"
                required>

            <!-- Pilihan siapa yang akan diundang -->
            <label class="block text-sm font-medium text-gray-700 mb-1">Invite Other:</label>
            <select name="user_ids[]" id="user_ids" multiple
                class="user_ids w-full mb-4 select2 px-4 py-2 border rounded border-gray-300">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                    onclick="document.getElementById('todoModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Processing Ticket -->
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
                    class="px-4 py-2 text-white rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition"
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

@if($ticketsToClose->count() > 0)
<div id="ticketsDoneModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-40">
    <div id="ticketsDoneModalContent"
         class="bg-white rounded-xl shadow-xl p-6 w-2/5 max-h-[70vh] overflow-auto animate__animated">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Your Tickets are Done!</h3>
            <button id="btnCloseTicketsModal" class="text-red-500 hover:text-red-800 text-lg font-bold">&times;</button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 text-gray-700 font-medium">
                    <tr>
                        <th class="border px-3 py-2">No.</th>
                        <th class="border px-3 py-2">Subject</th>
                        <th class="border px-3 py-2">Done by</th>
                        <th class="border px-3 py-2">Done At</th>
                        <th class="border px-3 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketsToClose as $index => $ticket)
                   <tr id="done-row-{{ $ticket->id }}" class="border-b hover:bg-gray-50">
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $ticket->title ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $ticket->process->name ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $ticket->done_at ?? '-' }}</td>
                        <td class="border px-3 py-2">
                            
                                <button type="button" onclick="closeTicket({{ $ticket->id }})" class="bg-green-500 z-50 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                                    Close Ticket
                                </button>
                           
                        </td>
                    </tr>
                    
                    @endforeach
                </tbody>
            </table>

        </div>

        <!-- Footer -->
        <div class="mt-4 text-right">
            <button id="btnCloseTicketsModalFooter" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-400 transition">Close</button>
        </div>
    </div>
</div>
@endif




  <div  id="closeModal" class="hidden bg-white p-6 rounded-md shadow-md w-full max-w-lg z-50">
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
        <button type="button" onclick="hideCloseModal()" class="bg-gray-200 px-4 py-2 text-white rounded mr-2 hover:bg-gray-400">Cancel</button>
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Closed</button>
      </div>
    </form>
  </div>


 <!-- Modal -->
<div id="scheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-4xl p-6 relative animate-fadeIn">
   <!-- Header -->
<div class="flex justify-between bg-green-500 items-center mb-4 border-b rounded-lg px-3 py-2">
  <div class="flex items-center gap-3"> 
    <i data-feather="home" class="w-6 h-6 text-white"></i> 
    <h2 class="text-2xl font-semibold text-white">Meeting Room Schedule</h2>
  </div>
  <div>
    <span id="currentDate" class="text-white text-sm"></span>
  </div>
</div>


    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-white">
            <th class="border-b border-l border-r px-3 py-2 text-gray-800 text-center">Time</th>
            <th class="border-b border-l border-r px-3 py-2 text-gray-800 text-center">Ruang Bima</th>
            <th class="border-b border-l border-r px-3 py-2 text-gray-800 text-center">Ruang Arjuna</th>
          </tr>
        </thead>
        <tbody id="scheduleBody">
          <!-- Jadwal akan di-render oleh jQuery -->
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <div class="flex justify-end mt-4">
      <button id="closeScheduleModal" class="bg-gray-300 hover:bg-gray-500 text-gray-800 px-4 py-2 rounded-lg shadow transition">Close</button>
    </div>
  </div>
</div>

@if($cancelledBookings->count() > 0)
<div id="cancelBookingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div id="cancelBookingModalContent" 
         class="bg-white rounded-lg shadow-lg w-[600px] max-w-2xl p-8 transform transition-all duration-300 opacity-0 scale-75">
        
        <h2 class="text-2xl font-bold text-red-600 mb-4 flex items-center gap-2">
            <i data-feather="x-circle" class="w-6 h-6"></i>
            Booking Cancelled
        </h2>

        {{-- Ambil cancel booking terbaru --}}
        @php $cancel = $cancelledBookings->first(); @endphp

        <div class="space-y-3 text-gray-700">
            <p>
                Your booking for 
                <strong class="text-gray-900">{{ $cancel->room->name }}</strong> 
                on 
                <span class="font-medium">{{ \Carbon\Carbon::parse($cancel->booking_date)->translatedFormat('l, d M Y') }}</span>, 
                <span class="font-medium">{{ \Carbon\Carbon::parse($cancel->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($cancel->end_time)->format('H:i') }} WIB</span> 
                has been <span class="text-red-600 font-semibold">cancelled</span> by General Affairs Admin.
            </p>

            <p class="bg-red-50 border border-red-200 rounded p-3 text-red-700">
                <span class="font-semibold">Reason:</span> {{ $cancel->cancel_reason }}
            </p>
        </div>

        <div class="mt-6 flex justify-end">
            <button id="btnCloseCancelBookingModal"
            data-cancel-id="{{ $cancel->id }}" 
                class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                Yes, I Understand
            </button>
        </div>
    </div>
</div>
@endif



@push('scripts')
<script>
flatpickr("#due_date", {
    enableTime: true,
    noCalendar: false,
    dateFormat: "Y-m-d H:i",
    time_24hr: true
});
    
        function openRescheduleModal(todoId){
            $('#rescheduleTodoId').val(todoId);
            $('#rescheduleModal').removeClass('hidden');
        }

        function closeRescheduleModal(){
        $('#rescheduleModal').addClass('hidden');
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

        flatpickr("#display-date", {
  mode: "range",
  dateFormat: "Y-m-d",
  minDate: new Date(),  // start dari hari ini
  allowInput: true
});

 $(document).ready(function() {
        $('#recipients').select2({
            placeholder: '-- Select Departments --',
            width: '100%'
        });
    });

   document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.read-more-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        let parent = button.closest('.text-gray-600');
        let shortDesc = parent.querySelector('.announcement-desc');
        let fullText = parent.querySelector('.full-text');

        if (button.dataset.expanded === "true") {
            // Saat di klik Read Less
            shortDesc.innerHTML = fullText.dataset.short; // kembalikan ke versi pendek
            button.innerText = "Read More";
            button.dataset.expanded = "false";
        } else {
            // Simpan versi pendek dulu supaya bisa dikembalikan
            fullText.dataset.short = shortDesc.innerHTML;
            shortDesc.innerHTML = fullText.innerHTML; // tampilkan full HTML
            button.innerText = "Read Less";
            button.dataset.expanded = "true";
        }
    });
});

$(document).ready(function() {
    $('.todo-delete-form').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let id = form.data('id');

        // SweetAlert confirm
        Swal.fire({
            title: 'Hapus Agenda?',
            text: "Apakah Anda yakin ingin menghapus agenda ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'DELETE',
                    data: form.serialize(),
                    success: function(res) {
                        if(res.success) {
                            // Hapus item dari DOM
                            form.closest('li').remove();

                            // Toast notifikasi sukses
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        });
                    }
                });
            }
        });
    });
});

    
// Submit form via AJAX
$(document).ready(function() {
$('#rescheduleForm').on('submit', function(e){
    e.preventDefault();
    let todoId = $('#rescheduleTodoId').val();
    let agendaTime = $('#newAgendaTime').val();
    let token = $('input[name="_token"]').val();

    if(!agendaTime){
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select a new date & time.',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    $.ajax({
        url: `/todo/${todoId}/reschedule`,
        type: 'POST',
        data: { _token: token, agenda_time: agendaTime },
        success: function(res){
            if(res.success){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                // update badge agenda_time di DOM
                closeRescheduleModal();
                 setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message,
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan server.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
});
});


 document.querySelectorAll('.todo-toggle').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.id;
            const taskText = document.getElementById('task-text-' + taskId);

            // Toggle class sementara
            if(this.checked) {
                taskText.classList.add('line-through', 'text-gray-400');
            } else {
                taskText.classList.remove('line-through', 'text-gray-400');
            }

            // Kirim ke server supaya tersimpan
            fetch(`/todos/${taskId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(res => res.json())
              .then(data => {
                  // Optional: sync class jika server berbeda
                  if(data.done) {
                      taskText.classList.add('line-through', 'text-gray-400');
                  } else {
                      taskText.classList.remove('line-through', 'text-gray-400');
                  }
              });
        });
    });

     ClassicEditor
    .create(document.querySelector('#description'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
            'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'mediaEmbed', '|',
            'undo', 'redo', 'code'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        },
        fontSize: {
            options: [ 8, 10, 12, 14, 'default', 18, 20, 24, 28, 32, 36 ],
            supportAllValues: true // supaya bisa tulis manual seperti '18px'
        },
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        image: {
            toolbar: [ 'imageTextAlternative', 'imageStyle:full', 'imageStyle:side' ]
        },
        mediaEmbed: {
            previewsInData: true
        }
    })
    .then(editor => {
        document.querySelector('#announcementForm').addEventListener('submit', function (e) {
            document.querySelector('#description').value = editor.getData();
            if (!editor.getData().trim()) {
                e.preventDefault();
                alert('Description is required!');
            }
        });
    })
    .catch(error => {
        console.error(error);
    });

});


document.getElementById('btnAdd').addEventListener('click', function() {
    document.getElementById('announcementModal').classList.remove('hidden');
});
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('announcementModal').classList.add('hidden');
});


    // Submit form via jQuery Ajax
    $('#announcementForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('announcements.store') }}",
            type: 'POST',
            data: formData,
            processData: false, // jangan ubah data menjadi query string
            contentType: false, // biar multipart/form-data
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success){
                    alert('Announcement saved successfully!');
                    $('#announcementModal').addClass('hidden');
                    $('#announcementForm')[0].reset();
                } else {
                    alert('Error: ' + (response.error || 'Something went wrong.'));
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An unexpected error occurred.');
            }
        });
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
    
 function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector(".chevron-icon");

    content.classList.toggle("hidden");
    if (icon) {
        icon.classList.toggle("rotate-180");
    }
}




   function approveTicket(ticketId, TicketNumber) {
    Swal.fire({
        title: 'Approve Ticket?',
        text: "Are you sure you want to approve this ticket?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${ticketId}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                // ✅ res tersedia di sini
                showToast('success', 'Ticket has been Approved: ' + res.ticket_number);
                // Hapus row sesuai ID
                $(`#ticket-row-${ticketId}`).remove();
                // Update counter
                updateTicketCounter();
            }).fail(function (err) {
                showToast('error', 'Failed to Approve Ticket.');
                console.error(err.responseText);
            });
        }
    });
}

$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let ticketId = $('#reject_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/reject`, data, function (res) {
        if (res.success) {
            showToast('success', 'Ticket has been Rejected: ' + res.ticket_number);
                // Hapus row sesuai ID
                $(`#ticket-row-${ticketId}`).remove();
                // Update counter
                updateTicketCounter();
                 $('#rejectModal').addClass('hidden');  // <-- pastikan id modal = rejectModal
        } else {
            showToast('error', "Failed: " + res.message);
        }
    })
});

// Fungsi untuk update counter setelah row dihapus
function updateTicketCounter() {
    let count = $('tbody tr').length;
    $('.ticket-counter').text(count);
}


      function openProcessModal(ticketId) {
    $('#process_ticket_id').val(ticketId);
    $('#processForm').attr('action', `/it/ticket/${ticketId}/process`);
    $('#processModal').removeClass('hidden');
}

function closeProcessModal() {
    $('#processModal').addClass('hidden');
}

$('#processForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let ticketId = $('#process_ticket_id').val(); // ambil ID dari input hidden
    let data = form.serialize(); // ambil data form

    $.post(`/it/ticket/${ticketId}/process`, data, function (res) {
        if (res.success) {
            showToast('success', 'Ticket has been Processed: ' + res.ticket_number);
            $(`#ticket-row-${ticketId}`).remove();
            updateTicketCounter();
            $('#processModal').addClass('hidden'); // tutup modal
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        showToast('error', 'Error processing ticket.');
        console.error(err.responseText);
    });
});

     // Tutup modal jika klik di luar kotaknya
    window.addEventListener('click', function (e) {
        const modal = document.getElementById('todoModal');
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    document.querySelectorAll('.todo-toggle').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const todoId = this.dataset.id;
        const isChecked = this.checked;

        fetch(`/todo/${todoId}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }).then(response => response.json())
          .then(data => {
            const textEl = document.getElementById(`task-text-${todoId}`);
            if (data.done) {
                textEl.classList.add('line-through', 'text-gray-500');
            } else {
                textEl.classList.remove('line-through', 'text-gray-500');
            }
        });
    });
});

  $(document).ready(function () {
        $('#user_ids').select2({
            placeholder: "Choose Person ...",
            width: '100%'
        });
    });



     function approveDOC(docId, docNumber) {
    Swal.fire({
        title: 'Approve Document Submission?',
        text: "Are you sure you want to approve this document submission?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${docId}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document submission has been Approved: ' + res.document_number);
                // Hapus row sesuai ID
                $(`#document-row-${docId}`).remove();
                // Update counter
                updateDocumentCounter();
            }).fail(function (err) {
                showToast('error', 'Failed to Approve Document.');
                console.error(err.responseText);
            });
        }
    });
}

function reviewDOC(docId, docNumber) {
    Swal.fire({
        title: 'Review Document Submission?',
        text: "Are you sure you want to review this document submission?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Review',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/mr/document/${docId}/review`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                // ✅ res tersedia di sini
                showToast('success', 'Document submission has been Review: ' + res.document_number);
                // Hapus row sesuai ID
                $(`#document-row-${docId}`).remove();
                // Update counter
                updateDocumentCounter();
            }).fail(function (err) {
                showToast('error', 'Failed to Review Document.');
                console.error(err.responseText);
            });
        }
    });
}

$(document).ready(function() {
    // Delay muncul modal (opsional, misal 1 detik)
    setTimeout(function() {
        $('#ticketsDoneModal').removeClass('hidden');

        // trigger animasi scale-up
       $('#ticketsDoneModalContent')
        .removeClass('opacity-0')
        .addClass('animate__bounceIn'); // atau animate__bounceIn
    }, 500); // 1000ms = 1 detik

    // Tombol close modal
    $('#btnCloseTicketsModal, #btnCloseTicketsModalFooter').click(function() {
        // animasi scale-down sebelum disembunyikan
        $('#ticketsDoneModalContent').removeClass('scale-100 opacity-100').addClass('scale-75 opacity-0');
        setTimeout(function() {
            $('#ticketsDoneModal').addClass('hidden');
        }, 300); // sama dengan duration transition
    });
});

function showCloseModal(ticketId) {
   $('#close_ticket_id').val(ticketId);
    $('#closeModal').removeClass('hidden');
  }

  function hideCloseModal() {
    $('#closeModal').addClass('hidden');
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
                      $(`#done-row-${id}`).remove();
                } else {
                    showToast('error', res.message || 'Failed to close ticket.');
                }
            }).fail(function () {
                showToast('error', 'Server error while closing the ticket.');
            });
        }
    });
}

$(document).ready(function() {
    const bookings = [
        { room: 'Bima', hour: 9 },
        { room: 'Arjuna', hour: 11 },
        { room: 'Bima', hour: 15 }
    ];

    const rooms = ['Bima', 'Arjuna'];
    const hours = Array.from({length: 9}, (_, i) => i + 8); // 8 - 16

    function renderSchedule() {
        const $body = $('#scheduleBody');
        $body.empty();

        hours.forEach(hour => {
            const $row = $('<tr></tr>');
            $row.append(`<td class="border-b border-l border-r px-3 py-2 text-center font-medium text-gray-700">${hour}:00 - ${hour+1}:00</td>`);

            rooms.forEach(room => {
                const booked = bookings.some(b => b.room === room && b.hour === hour);
                const $cell = $('<td></td>')
                    .addClass(`border px-3 py-2 p-6 text-center font-medium transition
                               ${booked ? 'bg-red-400 text-white' : 'bg-green-600 text-white hover:bg-green-800 cursor-pointer'}`)
                    .text(booked ? 'Booked' : 'Available');
                $row.append($cell);
            });

            $body.append($row);
        });
    }

    function updateCurrentDate() {
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        $('#currentDate').text(today.toLocaleDateString('id-ID', options));
    }

    $('#openScheduleModal').click(function() {
        renderSchedule();
        updateCurrentDate();
        $('#scheduleModal').removeClass('hidden');
    });

    $('#closeScheduleModal').click(function() {
        $('#scheduleModal').addClass('hidden');
    });
});

$(document).ready(function() {

    // Contoh data booking user
    const myBookings = [
        { id: 1, room: 'Bima', hour: '09:00 - 10:00', purpose: 'Client Meeting' },
        { id: 2, room: 'Arjuna', hour: '11:00 - 12:00', purpose: 'Team Sync' },
        { id: 3, room: 'Bima', hour: '15:00 - 16:00', purpose: 'Project Review' }
    ];

    function renderMyBookings() {
        const $body = $('#myBookingBody');
        $body.empty();

        myBookings.forEach(b => {
            const $row = $('<tr></tr>');
            $row.append(`<td class="border-b px-3 py-2 text-center font-medium text-gray-700">${b.room}</td>`);
            $row.append(`<td class="border-b px-3 py-2 text-center font-medium text-gray-700">${b.hour}</td>`);
            $row.append(`<td class="border-b px-3 py-2 text-left font-medium text-gray-700">${b.purpose}</td>`);

            const $action = $('<td class="border-b px-3 py-2 text-center"></td>');
            const $cancelBtn = $('<button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md shadow transition">Cancel</button>');

            $cancelBtn.click(function() {
                if(confirm(`Cancel booking ${b.room} at ${b.hour}?`)) {
                    const index = myBookings.findIndex(x => x.id === b.id);
                    if(index !== -1) myBookings.splice(index, 1);
                    renderMyBookings();
                    alert('Booking canceled');
                    // TODO: lakukan AJAX ke backend untuk hapus booking di DB
                }
            });

            $action.append($cancelBtn);
            $row.append($action);
            $body.append($row);
        });
    }
});

$(document).ready(function () {
    loadTimeline();

    function loadTimeline() {
        $.ajax({
            url: '/facility/booking-room/today-schedule',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                renderTimeline(res);
            },
            error: function (err) {
                console.error(err);
                $("#timelineContainer").html(
                    `<p class="text-gray-400 italic text-center">Gagal memuat data</p>`
                );
            }
        });
    }

    function renderTimeline(bookings) {
        $("#timelineContainer").empty();

        if (bookings.length === 0) {
            $("#timelineContainer").html(
                `<p class="text-gray-400 italic text-center">Tidak ada booking hari ini</p>`
            );
            return;
        }

        // garis vertikal
        $("#timelineContainer").append(
            `<div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>`
        );

        bookings.forEach(b => {
            let item = `
              <div class="flex items-start mb-6 relative">
                <div class="absolute left-2 top-1 w-4 h-4 bg-green-500 rounded-full border-4 border-white"></div>
                <div class="ml-10">
                  <p class="text-xs text-gray-500">${b.start_time} - ${b.end_time} WIB (${b.room.name})</p>
                  <p class="text-sm text-gray-800">${b.purpose}</p>
                  <p class="text-xs text-gray-500">${b.creator.name}</p>
                </div>
              </div>
            `;
            $("#timelineContainer").append(item);
        });
    }
});

window.currentUserRoles = @json(Auth::user()->roles->pluck('name')); 

// Handle Approve button
$(document).on("click", ".approveBtn", function () {
    const id = $(this).data("id");
    const $li = $(this).closest("li"); // simpan referensi <li>

    Swal.fire({
        title: "Approve Booking?",
        text: "Approve this Booking?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Approve",
        cancelButtonText: "Back"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('facility.booking-room.approve') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Approved!",
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Hapus hanya baris li yang diapprove
                        $li.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Terjadi kesalahan saat approve booking", "error");
                }
            });
        }
    });
});



 // Cancel booking
$(document).on('click', '.cancelBtn', function() {
    const id = $(this).data('id');
     const $li = $(this).closest("li"); // <-- ini yang hilang

    // cek role user dari variabel global
    const isAdminGA = (window.currentUserRoles.includes("Admin GA"));

    if (isAdminGA) {
        // Admin GA → wajib isi alasan cancel
        Swal.fire({
            title: 'Cancel this booking?',
            input: 'textarea',
            inputLabel: 'Reason for cancellation',
            inputPlaceholder: 'Enter the reason here...',
            inputAttributes: {
                'aria-label': 'Reason for cancellation'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Back',
            inputValidator: (value) => {
                if (!value) {
                    return 'Reason is required!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/facility/booking-room/cancel/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        cancel_reason: result.value
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                         $li.fadeOut(300, function () {
                            $(this).remove();
                         });
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to cancel booking',
                        });
                    }
                });
            }
        });

    } else {
        // User biasa → langsung confirm cancel
        Swal.fire({
            title: 'Cancel this booking?',
            text: "This booking will be cancelled and cannot be recovered.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/facility/booking-room/cancel/' + id,
                    type: 'POST',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                         $li.fadeOut(300, function () {
                            $(this).remove();
                         });
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to cancel booking',
                        });
                    }
                });
            }
        });
    }
});

$(document).ready(function () {
    if ($("#cancelBookingModal").length > 0) {
        const cancelId = $("#cancelBookingModal").data("cancel-id"); 
        const lastSeenId = localStorage.getItem("lastCancelSeenId");

        if (cancelId != lastSeenId) {
            // tampilkan modal
            setTimeout(function () {
                $('#cancelBookingModalContent')
                    .removeClass('opacity-0 scale-75')
                    .addClass('opacity-100 scale-100');
            }, 500);
        } else {
            $("#cancelBookingModal").remove();
        }
    }

    // Tombol close modal
    $('#btnCloseCancelBookingModal').click(function () {
        $('#cancelBookingModalContent')
            .removeClass('scale-100 opacity-100')
            .addClass('scale-75 opacity-0');

        setTimeout(function () {
            $('#cancelBookingModal').hide();
        }, 300);

        // Simpan ID booking cancel terakhir
        const cancelId = $("#cancelBookingModal").data("cancel-id");
        localStorage.setItem("lastCancelSeenId", cancelId);
    });
});

 const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach(btn => {
    btn.addEventListener("click", function() {
      const target = this.getAttribute("data-tab");

      // reset semua button
      tabButtons.forEach(b => {
        b.classList.remove("text-teal-600","border-b-2","border-teal-600");
        b.classList.add("text-gray-600");
      });

      // aktifkan button yang diklik
      this.classList.add("text-teal-600","border-b-2","border-teal-600");
      this.classList.remove("text-gray-600");

      // sembunyikan semua konten
      tabContents.forEach(c => c.classList.add("hidden"));

      // tampilkan konten sesuai target
      document.getElementById(target).classList.remove("hidden");
    });
  });

</script>
@endpush



@endsection
