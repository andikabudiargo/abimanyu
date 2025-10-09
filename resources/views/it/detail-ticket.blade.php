@extends('layouts.app')

@section('title', 'Detail Ticket')
@section('page-title', 'Detail Ticket')
@section('breadcrumb-item', 'Ticket')
@section('breadcrumb-active', 'Detail Ticket')

@section('content')
@php
    $due = \Carbon\Carbon::parse($ticket->due_date);
    $closed = \Carbon\Carbon::parse($ticket->done_at);
    $overdue = $closed->greaterThan($due);
    $overdueDuration = $overdue ? $closed->diff($due)->format('%d days %h hours %i minutes') : null;

          $status = $ticket->status ?? '';
$colorClass = match($status) {
    'Pending'    => 'text-gray-600 bg-gray-200',
    'Approved' => 'text-white bg-green-600',
    'Work in Progress' => 'text-white bg-blue-600',
    'On Hold' => 'text-white bg-yellow-600',
    'Done'    => 'text-white bg-green-600',
    'Closed' => 'text-white bg-teal-600',
    'Rejected' => 'text-white bg-red-600',
    default    => 'text-gray-700 bg-gray-100',
};
    
@endphp

<div class="w-full bg-white shadow-md rounded-xl p-4 md:p-6 space-y-4 mb-2">
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">

    <!-- Ticket Number -->
    <div class="order-1 md:order-2 mt-0 mb-2 md:mb-0">
      <span class="inline-block px-2 py-1 rounded mr-2 text-sm md:text-base">
        #{{ $ticket->ticket_number }}
      </span>
    </div>

    <!-- Title + Status -->
    <div class="order-2 md:order-1 w-full md:max-w-[70%]">
      <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 uppercase flex flex-wrap items-center gap-2 break-words">
        {{ $ticket->title }}
        <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
          {{ strtoupper($ticket->status ?? '-') }}
        </span>
      </h1>
    </div>

</div>



 <div class="flex flex-col md:flex-row text-sm text-gray-600 mb-6">
    <span class="inline-flex items-center mb-2 md:mb-0 md:mr-4">
        <i data-feather="user" class="w-4 h-4 mr-1"></i> {{ $ticket->requestor->name ?? 'Unknown' }}
    </span>
    <span class="inline-flex items-center">
        <i data-feather="calendar" class="w-4 h-4 mr-1"></i> {{ $ticket->created_at->format('d M Y H:i') }} WIB
    </span>
</div>

<hr class=" border border-gray-900">

<div class="flex flex-col md:flex-row gap-6 mb-2">
  <!-- MAIN CONTENT: PO Info + Items -->
  <div class="w-full md:w-2/3 flex flex-col space-y-6">
    
    <!-- Purchase Order Information -->
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-4 md:p-6">
       <div class="flex flex-row flex-wrap justify-between items-center mb-6 md:mb-8">
    <h3 class="text-lg md:text-xl font-semibold text-gray-700">
        Ticket Information
    </h3>
    @php
        $statusClasses = [
            'Low' => 'bg-green-600 text-white',
            'Medium' => 'bg-blue-600 text-white',
            'Urgent' => 'bg-yellow-600 text-white',
            'Critical' => 'bg-red-600 text-white',
        ];
        $statusClass = $statusClasses[$ticket->priority] ?? 'bg-gray-100 text-gray-800';
    @endphp
    <span class="inline-block {{ $statusClass }} px-2 py-1 rounded-lg text-sm mt-2 md:mt-0">
        {{ $ticket->priority }}
    </span>
</div>


       <div class="text-sm mb-6 md:mb-8">
        <div class="mb-4 md:mb-6">
          <div class="text-gray-500 font-medium mb-1">Category</div>
          <div class="text-gray-800">{{ $ticket->category->description ?? 'No Category' }}</div>
        </div>

        <div class="text-gray-500 font-medium mb-2">Attachment File</div>
        @if($ticket->attachments->count() > 0)
            @foreach($ticket->attachments as $attachment)
                @php
                    $filename = basename($attachment->path);
                    $extension = pathinfo($attachment->path, PATHINFO_EXTENSION);
                @endphp
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-gray-100 p-3 rounded shadow-sm mb-4">
                    <div class="mb-2 md:mb-0">
                        <p class="text-sm font-medium text-gray-800">{{ $filename }}</p>
                        <p class="text-xs text-gray-500">Format: .{{ $extension }}</p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ asset('storage/'.$attachment->path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                            <i data-feather="eye" class="w-4 h-4 mr-1"></i> Watch
                        </a>
                        <a href="{{ asset('storage/'.$attachment->path) }}" download class="inline-flex items-center text-green-600 hover:underline">
                            <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-gray-500 italic">No Attachment</p>
        @endif
      </div>

       <h3 class="text-lg md:text-xl font-semibold mb-4">Description Issue</h3>
       <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
          {{ $ticket->description ?? 'No Description' }}
       </div>

      <h3 class="text-lg md:text-xl font-semibold text-gray-700 mb-4 mt-6">Internal Notes</h3>
      <hr class="mb-4">
@php
    $hasNotes = $ticket->holds->count() > 0 || 
                in_array($ticket->status, ['Done', 'Closed', 'Rejected']);
@endphp

@if(!$hasNotes)
    <p class="text-gray-500 italic text-center">No Notes Added</p>
@endif
      {{-- Timeline entries --}}
      @foreach ($ticket->holds as $hold)
         <div class="flex flex-col md:flex-row items-start md:items-center space-x-0 md:space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
              <img src="{{ $ticket->process->avatar ? asset('storage/' . $ticket->process->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full mb-2 md:mb-0" alt="Avatar">
              <div>
                  <div class="text-sm font-semibold">{{ $ticket->process->name ?? '-' }}</div>
                  <div class="text-xs text-gray-500">On Hold • {{ \Carbon\Carbon::parse($hold->created_at)->format('d M Y H:i') }}</div>
                  @if($hold->reason)
                      <div class="mt-1 text-sm text-gray-700"><strong>Hold Reason:</strong> {{ $hold->reason }}</div>
                  @endif
              </div>
          </div>
      @endforeach

    {{-- Done --}}
    @if($ticket->status === 'Done' || $ticket->status === 'Closed' )
       <div class="flex flex-col md:flex-row items-start md:items-center space-x-0 md:space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
             <img src="{{ $ticket->process->avatar ? asset('storage/' . $ticket->process->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
            <div>
                <div class="font-semibold">{{ $ticket->process->name }}</div>
                <div class="text-xs text-gray-500">Done • {{ \Carbon\Carbon::parse($ticket->done_at)->format('d M Y H:i') }}</div>

                @if($ticket->corrective_action)
                    <div class="mt-2 text-sm text-gray-700"><strong>Corrective Action:</strong> {{ $ticket->corrective_action }}</div>
                @endif

             @if($ticket->evidences->count() > 0)
    <div class="mt-2 text-sm text-gray-700"><strong>Evidence:</strong></div>
    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
        @foreach($ticket->evidences as $evidence)
            <a href="{{ asset('storage/'.$evidence->path) }}" data-lightbox="evidence">
                <img src="{{ asset('storage/'.$evidence->path) }}" 
                     alt="Evidence Image" 
                     class="max-w-full h-auto rounded shadow border border-gray-200">
            </a>
        @endforeach
    </div>
@endif

            </div>
        </div>
@endif

{{-- Closed --}}
    @if($ticket->status === 'Closed')
        <div class="flex flex-col md:flex-row items-start md:items-center space-x-0 md:space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
             <img src="{{ $ticket->requestor->avatar ? asset('storage/' . $ticket->requestor->avatar) : asset('img/avatar-dummy.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
            <div>
                <div class="font-semibold">{{ $ticket->requestor->name }}</div>
                <div class="text-xs text-gray-500">Closed • {{ \Carbon\Carbon::parse($ticket->closed_at)->format('d M Y H:i') }}</div>

                @if($ticket->feedback)
                    <div class="mt-2 text-sm text-gray-700"><strong>Feedback:</strong> {{ $ticket->feedback }}</div>
                @endif

            </div>
        </div>
@endif

    {{-- Reject --}}
    @if($ticket->status === 'Rejected' && $ticket->rejected_reason)
        <div class="flex flex-col md:flex-row items-start md:items-center space-x-0 md:space-x-3 mb-4 border border-gray-400 rounded-xl p-4">
            <img src="{{ $ticket->reject->avatar ? asset('storage/' . $ticket->process->avatar) : asset('img/avatar-dummy.png') }}" alt="Avatar" class="w-8 h-8 rounded-full">
            <div>
                <div class="font-semibold">{{ $ticket->reject->name ?? 'Unknown' }}</div>
                <div class="text-xs text-gray-500">Rejected • {{ \Carbon\Carbon::parse($ticket->rejected_at)->format('d M Y H:i') }}</div>
                <div class="mt-1 text-sm text-red-600"><strong>Reason:</strong> {{ $ticket->rejected_reason }}</div>
            </div>
        </div>
    @endif
</div>
  </div>

  <!-- SIDEBAR: Order History + Summary -->
  <div class="w-full md:w-1/3 flex flex-col space-y-6">
    <div class="border border-gray-200 bg-white shadow-md rounded-xl p-4 md:p-6">
      <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg md:text-xl font-semibold text-gray-700">Ticket Timeline</h3>
        <i data-feather="clock" class="text-gray-700 w-5 h-5"></i>
      </div>
      <hr class="my-4">

       @php
            $hasTimeline = $ticket->approved || $ticket->processed_by || $ticket->due_date || $ticket->holds->count() || $ticket->done_at || $ticket->closed_at;
        @endphp

        @if($hasTimeline)
    {{-- Approved --}}
    @if($ticket->approved)
        <div>
            <div class="text-xs text-green-400 uppercase font-semibold mb-1">Approved</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                <span>{{ $ticket->approved->name }} Approved</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($ticket->approved_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Assigned --}}
    @if($ticket->processed_by)
        <div>
            <div class="text-xs text-blue-400 uppercase font-semibold mb-1">Assign</div>
            <div class="flex items-center space-x-2">
                <i data-feather="tool" class="w-4 h-4 text-blue-500"></i>
                <span>{{ $ticket->process->name ?? '-' }} Process the Ticket</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($ticket->processed_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

      {{-- Due Date --}}
@if($ticket->due_date)
    <div>
        <div class="text-xs text-pink-400 uppercase font-semibold mb-1">
            Due Date
        </div>
        <div class="flex items-center space-x-2">
                <i data-feather="calendar" class="w-4 h-4 text-pink-400"></i>
                <span>{{ $ticket->process->name ?? '-' }} set Due Date</span>
            </div>
        <div class="flex items-center space-x-2 mb-4">
            <i data-feather="clock" class="w-4 h-4"></i>
            <span>{{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y H:i') }}</span>
        </div>
    </div>
@endif

 {{-- On Hold & Resume Timeline --}}
    @foreach($ticket->holds as $hold)
        <div>
            <div class="text-xs text-yellow-500 uppercase font-semibold mb-1">On Hold</div>
             <div class="flex items-center space-x-2">
                <i data-feather="pause-circle" class="w-4 h-4 text-yellow-500"></i>
                <span>{{ $ticket->process->name ?? '-' }} Hold Ticket!</span>
            </div>
            <div class="flex items-center space-x-2 mb-4">
                <i data-feather="clock" class="w-4 h-4"></i>
                <span>{{ \Carbon\Carbon::parse($hold->start_at)->format('d M Y H:i') }}</span>
            </div>
            @if($hold->end_at)
                <div class="text-xs text-purple-500 uppercase font-semibold mb-1 mt-3">Resume</div>
                 <div class="flex items-center space-x-2">
                <i data-feather="play-circle" class="w-4 h-4 text-purple-500"></i>
                <span>{{ $ticket->process->name ?? '-' }} Resume Ticket</span>
            </div>
                <div class="flex items-center space-x-2 mb-4">
                    <i data-feather="clock" class="w-4 h-4"></i>
                    <span>{{ \Carbon\Carbon::parse($hold->end_at)->format('d M Y H:i') }}</span>
                </div>
            @endif
        </div>
    @endforeach

   {{-- Done --}}
@if($ticket->done_at)
    <div>
        <div class="text-xs text-green-500 uppercase font-semibold mb-1">Done</div>

        @if($overdue)
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="alert-circle" class="w-4 h-4 text-red-500"></i>
                <span class="text-red-500">Overdue by {{ $overdueDuration }}</span>
            </div>
        @else
            <div class="flex items-center space-x-2 mt-1">
                <i data-feather="check" class="w-4 h-4 text-green-500"></i>
                <span class="text-green-500">Done on time</span>
            </div>
        @endif

        <div class="flex items-center space-x-2 mb-4">
            <i data-feather="clock" class="w-4 h-4 "></i>
            <span>{{ \Carbon\Carbon::parse($ticket->done_at)->format('d M Y H:i') }}</span>
        </div>
    </div>
@endif


    {{-- Closed --}}
    @if($ticket->closed_at)
        <div>
            <div class="text-xs text-teal-400 uppercase font-semibold mb-1">Closed</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check" class="w-4 h-4 text-teal-400"></i>
                <span> {{ $ticket->requestor->name ?? 'Unknown' }} Close Ticket</span>
            </div>
            <div class="flex items-center space-x-2 mb-4 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($ticket->closed_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif
    {{-- Durasi Ticket --}}
@if($ticket->closed_at)
    @php
        $approve = \Carbon\Carbon::parse($ticket->approved_at);
        $done = \Carbon\Carbon::parse($ticket->done_at);
        $duration = $approve->diffForHumans($done, [
            'parts' => 3, // tampilkan maksimal 3 unit (misalnya: "2 hari 3 jam 5 menit")
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
        ]);
    @endphp
    <div class="pt-4 p-4 bg-indigo-50 rounded mt-4">
        <div class="text-xs  text-gray-500 uppercase font-semibold mb-1">Ticket Duration</div>
        <div class="flex items-center space-x-2">
            <i data-feather="clock" class="w-4 h-4 text-gray-400"></i>
            <span>{{ $duration }}</span>
        </div>
    </div>
@endif
  @else
            {{-- Jika belum ada timeline --}}
            <div class="text-sm text-gray-500 italic text-center py-6">
                No Timeline Added Yet
            </div>
        @endif
</div>
  </div>
    </div>
<hr>
  <div class="flex flex-wrap justify-start gap-2 mt-4">
    <a href="{{ route('it.ticket.index') }}" 
       class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
       <i data-feather="arrow-left" class="w-4 h-4 inline"></i> Back
    </a>

    @if($ticket->status == 'Pending' &&
        auth()->user()->hasRole('Manager Special Access') &&
        auth()->user()->hasDepartment('Information & Technology'))
        <button onclick="approveTicket({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
            <i data-feather="check-circle" class="w-4 h-4 inline"></i> Approve
        </button>
        <button onclick="rejectTicket({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-red-600 text-white rounded">
            <i data-feather="x-circle" class="w-4 h-4 inline"></i> Reject
        </button>
    @endif

    @if($ticket->status == 'Approved' &&
        auth()->user()->hasDepartment($ticket->category->department->name))
        <button onclick="openProcessModal({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-yellow-500 text-white rounded">
            <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i> Process
        </button>
    @endif

    @if($ticket->status == 'Work in Progress' && auth()->id() === $ticket->processed_by)
        <button onclick="openHoldModal({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-yellow-600 text-white rounded">
            <i data-feather="pause-circle" class="w-4 h-4 inline"></i> Hold Ticket
        </button>
        <button onclick="showDoneModal({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
            <i data-feather="check-circle" class="w-4 h-4 inline"></i> Mark as Done
        </button>
    @endif

    @if($ticket->status == 'On Hold' && auth()->id() === $ticket->processed_by)
        <button onclick="resumeTicket({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-purple-600 text-white rounded">
            <i data-feather="play-circle" class="w-4 h-4 inline"></i> Resume Ticket
        </button>
    @endif

    @if($ticket->status == 'Done' && auth()->id() === $ticket->request_by)
        <button onclick="showCloseModal({{ $ticket->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-green-500 text-white rounded">
            <i data-feather="check-circle" class="w-4 h-4 inline"></i> Close Ticket
        </button>
    @endif
</div>



<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Reject Ticket</h2>
        <form id="rejectForm" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" id="reject_ticket_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Reject Reason</label>
                <textarea name="rejected_reason" id="reject_reason" rows="3" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 text-white bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
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
<select name="hold_reason" id="hold_reason" class="w-full px-3 py-2 border rounded">
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
        <button type="button" onclick="hideDoneModal()" class="bg-gray-400 px-4 text-white py-2 rounded mr-2">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
      </div>
    </form>
  </div>
</div>

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

@push('scripts')
<script>
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

flatpickr("#due_date", {
    enableTime: true,
    noCalendar: false,
    dateFormat: "Y-m-d H:i",
    time_24hr: true
});

    function rejectTicket(id) {
        document.getElementById('reject_ticket_id').value = id;
        document.getElementById('reject_reason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
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

   // ✅ Approve Ticket
function approveTicket(ticketId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Yakin ingin approve ticket ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${ticketId}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message || 'Ticket approved!',
                    showConfirmButton: false,
                    timer: 2000
                });
                setTimeout(() => location.reload(), 1200);
            }).fail(function () {
                Swal.fire('Error', 'Gagal approve.', 'error');
            });
        }
    });
}

// ✅ Reject Ticket
$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let ticketId = $('#reject_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/reject`, data, function (res) {
        if (res.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.message,
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire('Failed', res.message, 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'An error occurred.', 'error');
    });
});

// ✅ Hold Ticket
$('#holdForm').on('submit', function (e) {
    e.preventDefault();

    let $form = $(this);
    let $submitBtn = $form.find('button[type="submit"]');

    $submitBtn.prop('disabled', true).text('Processing...');

    let ticketId = $('#hold_ticket_id').val(); 
    let data = $form.serialize();

    $.post(`/it/ticket/${ticketId}/hold`, data, function (res) {
        if (res.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.message,
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => window.location.href = "/it/ticket/index", 1200);
        } else {
            Swal.fire('Failed', res.message, 'error');
            $submitBtn.prop('disabled', false).text('Submit');
        }
    }).fail(function () {
        Swal.fire('Error', 'An error occurred.', 'error');
        $submitBtn.prop('disabled', false).text('Submit');
    });
});

// ✅ Process Ticket
$('#processForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let action = form.attr('action');
    let data = form.serialize();

    $.post(action, data, function (res) {
        if (res.success) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Ticket is now in progress!',
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire('Failed', res.message, 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'An error occurred while processing.', 'error');
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

// ✅ Resume Ticket
function resumeTicket(id) {
    Swal.fire({
        title: 'Resume Ticket?',
        text: 'Resume this ticket and continue progress?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Resume'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/it/ticket/${id}/resume`, {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                if (res.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Ticket resumed to In Progress.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Swal.fire('Failed', res.message, 'error');
                }
            }).fail(function () {
                Swal.fire('Error', 'An error occurred while resuming the ticket.', 'error');
            });
        }
    });
}

// ✅ Done Ticket
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
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Ticket marked as Done!',
            showConfirmButton: false,
            timer: 2000
        });
        hideDoneModal();
        setTimeout(() => location.reload(), 1200);
      },
      error: function(err) {
        Swal.fire('Error', err.responseJSON.error || 'Terjadi kesalahan', 'error');
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
             setTimeout(() => location.reload(), 1200);
        },
        error: function(err) {
            showToast('error', err.responseJSON?.error || 'Terjadi kesalahan');
        }
    });
});

</script>
@endpush

@endsection