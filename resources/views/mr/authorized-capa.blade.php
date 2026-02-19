@extends('layouts.app')

@section('title', 'CAPA Authorized')
@section('page-title', 'CAPA AUTHORIZED')
@section('breadcrumb-item', 'CAPA Management')
@section('breadcrumb-active', 'CAPA Authorized')
@section('content')

@php
    $rca = $capa->actions->firstWhere('type', 'RCA');
    $ca  = $capa->actions->firstWhere('type', 'CA');
    $pa  = $capa->actions->firstWhere('type', 'PA');
@endphp

 <div class="flex flex-col md:flex-row gap-4">
<div class="w-full md:w-1/3 bg-white rounded-xl border border-gray-200 shadow-sm">

    <!-- HEADER -->
    <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
            Audit Assignment
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Auditor & Auditee Information
        </p>
</div>

        <!-- AUDITOR -->
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-800">Auditor</h3>
            <p class="text-xs text-gray-500 mb-3">Assigned audit team</p>

            <div class="flex flex-col gap-2">
                @if($capa->auditors && $capa->auditors->count() > 0)
                    @foreach($capa->auditors as $auditor)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50 shadow-sm">
                            <i class="fa fa-user text-indigo-500 text-lg"></i>
                            <div class="flex flex-col">
                                <span class="text-gray-800 font-medium">{{ $auditor->users->name ?? '-' }}</span>
                                <span class="text-xs text-gray-500">
    @if($auditor->users && $auditor->users->departments && $auditor->users->departments->count() > 0)
        {{ $auditor->users->departments->pluck('name')->join(', ') }}
    @else
        -
    @endif
</span>

                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-gray-400 italic text-sm">No auditors assigned</span>
                @endif
            </div>
        </div>

        <!-- AUDITEE -->
        <div class="px-5 py-4">
            <h3 class="text-sm font-medium text-gray-800">Auditee</h3>
            <p class="text-xs text-gray-500 mb-3">Department involved in the audit</p>

           @if($capa->dept_id && $capa->departemen)
    <div class="p-3 rounded-lg border border-gray-100 bg-gray-50 shadow-sm flex flex-col gap-2">
        <!-- Department -->
        <div class="flex items-center gap-2">
            <i class="fa fa-building text-indigo-500 text-sm"></i>
            <span class="font-medium text-gray-800">Department:</span>
             <span class="text-gray-700">{{ $capa->department_display }}</span>
        </div>

        <!-- Representative -->
        <div class="flex items-center gap-2">
            <i class="fa fa-user-tie text-indigo-500 text-sm"></i>
            <span class="font-medium text-gray-800">Dept. Representative:</span>
            <span class="text-gray-700">{{ $capa->representative->name ?? '-' }}</span>
        </div>
    </div>
@else
    <span class="text-gray-400 italic text-sm">No department assigned</span>
@endif

        </div>
<div class="max-w-xl mx-auto border-t border-gray-200 p-4">

  <!-- Header -->
  <h3 class="text-sm font-medium text-gray-800">Commentary</h3>
  <p class="text-xs text-gray-500 mb-6">Review Comment from Management Representative</p>

  <!-- COMMENTS CONTAINER -->
  <div id="comments-list" class="space-y-6">

    @forelse($capa->comments as $comment)

      <div class="flex relative old-comment"
           data-id="{{ $comment->id }}"
           data-user-id="{{ $comment->user_id }}">

        <!-- Avatar -->
        <div class="flex flex-col items-center mr-4">

          <div class="w-10 h-10 rounded-full border-2 border-gray-300 overflow-hidden">
            <img
              src="{{ $comment->user->photo ?? asset('img/default.png') }}"
              class="w-full h-full object-cover">
          </div>

          <div class="flex-1 w-px bg-gray-300 mt-1"></div>

        </div>

        <!-- Content -->
        <div class="flex-1">

          <div class="flex items-center justify-between text-sm">

            <div>
              <span class="text-gray-900 font-semibold">
                {{ $comment->user->name }}
              </span>

              <span class="font-medium text-gray-400 ml-1">
                commented
              </span>

              <span class="text-xs text-gray-400 block">
                {{ $comment->created_at->diffForHumans() }}
              </span>
            </div>

          </div>

          <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 text-sm comment-text">
            {{ $comment->comment }}
          </div>

        </div>

      </div>

    @empty

      <p class="text-sm italic text-gray-400">
        No comment yet.
      </p>

    @endforelse

  </div> <!-- END comments-list -->


  <!-- Add Comment -->
  <div class="mt-6">

    <label for="new-comment"
      class="block text-sm font-medium text-gray-700 mb-1">
      Add Comment
    </label>

    <textarea
      id="new-comment"
      rows="3"
      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
      placeholder="Type your comment..."></textarea>

    <button
      id="add-comment-btn"
      class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">

      Comment
    </button>

  </div>

</div>
</div>



<div class="w-full md:w-2/3 bg-white shadow-md rounded-xl p-6 space-y-6 mb-4">
    <div class="flex flex-col gap-3
            sm:flex-row sm:justify-between sm:items-start
            border-b pb-4">

    <!-- Title -->
    <div class="min-w-0">
        <h1 class="flex items-center gap-2
                   text-xl sm:text-2xl
                   font-semibold text-gray-800 tracking-tight">
            CAPA Review & Authorized
        </h1>

        <p class="flex items-center gap-2
                  text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
            Corrective & Preventive Action Authorized
        </p>
    </div>

    <!-- Status Badge -->
   <!-- Status Badge -->
<div class="flex sm:items-center">
    <span class="inline-flex items-center justify-center gap-1.5
        px-3 py-1 text-sm font-semibold rounded-full
        bg-purple-100 text-purple-800 border border-purple-300
        w-fit sm:w-28">
        <i class="fa-regular fa-check-circle text-xs text-purple-600"></i>
        Submitted
    </span>
</div>


</div>

   
<!-- HEADER INFO -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
  
      <!-- CAPA Number -->
<div class="p-3 rounded-xl border">
    <div class="flex items-center justify-between">

        <span class="text-sm font-semibold text-blue-700">
            CAPA No.
        </span>

        <span class="text-sm font-semibold
            {{ empty($capa->capa_number) ? 'text-gray-400 italic' : 'text-gray-800' }}">

            {{ $capa->capa_number ?: 'Not Verified Yet' }}

        </span>

    </div>
</div>


<!-- Report Date -->
<div class="p-3 rounded-xl border">
    <div class="flex items-center justify-between">

        <span class="text-sm font-semibold text-blue-700">
            Report Date
        </span>

        <span class="text-sm font-semibold
            {{ empty($capa->report_date) ? 'text-gray-400 italic' : 'text-gray-800' }}">

            {{ $capa->report_date
                ? \Carbon\Carbon::parse($capa->report_date)->format('d M Y')
                : 'Not Verified Yet'
            }}

        </span>

    </div>
</div>

</div>

<!-- SOURCE & CATEGORY -->
<div class="space-y-6 mb-8">

  <div>
    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Source of Finding</p>

    <div class="flex flex-wrap gap-2">

        @php
            $sources = [
                'Audit' => 'fa-magnifying-glass',
                'Complain' => 'fa-comment-dots',
                'Non-Conformity' => 'fa-triangle-exclamation',
                'Management Review' => 'fa-people-arrows',
            ];
        @endphp

        @foreach($sources as $key => $icon)

            @php
                $active = ($capa->source_of_finding ?? '') === $key;
            @endphp

            <span
                class="flex items-center gap-2 px-4 py-2 border rounded-lg text-sm font-medium
                {{ $active
                    ? 'bg-indigo-50 border-indigo-300 text-indigo-700'
                    : 'bg-gray-50 border-gray-200 text-gray-400' }}
                cursor-not-allowed">

                <i class="fa-solid {{ $icon }} text-xs"></i>
                {{ $key }}

            </span>

        @endforeach

    </div>
  </div>

   <div>
    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Category</p>

    <div class="flex flex-wrap gap-2">

        @php
            $categories = [
                'Critical' => 'bg-red-50 border-red-300 text-red-700',
                'Major' => 'bg-yellow-50 border-yellow-300 text-yellow-700',
                'Minor' => 'bg-blue-50 border-blue-300 text-blue-700',
                'Observation' => 'bg-green-50 border-green-300 text-green-700',
            ];
        @endphp

        @foreach($categories as $key => $style)

            @php
                $active = ($capa->category ?? '') === $key;
            @endphp

            <span
                class="px-4 py-2 border rounded-lg text-sm font-medium cursor-not-allowed
                {{ $active
                    ? $style
                    : 'bg-gray-50 border-gray-200 text-gray-400' }}">

                {{ $key }}

            </span>

        @endforeach

    </div>
  </div>

</div>


<!-- DETAIL, PROBLEM, RCA -->
<div class="space-y-6 mb-6">

  <!-- Detail of Information -->
  <fieldset class="p-4 border rounded-md">
    <legend class="px-2 text-sm font-semibold text-blue-700">
      Detail of Information
    </legend>

    <p class="mt-2 text-md text-gray-800 leading-relaxed">
      {{ $capa->detail_of_information ?? '-' }}
    </p>
  </fieldset>

  <!-- Problem Statement -->
  <fieldset class="p-4 border rounded-md">
    <legend class="px-2 text-sm font-semibold text-blue-700">
      Problem Statement
    </legend>

    <p class="mt-2 text-md text-gray-800 leading-relaxed">
      {{ $capa->problem ?? '-' }}
    </p>
  </fieldset>

 <!-- RCA -->
  <fieldset class="p-4 border rounded-md">
    <legend class="px-2 text-sm font-semibold text-blue-700">
      Root Cause Analysis
    </legend>

    <p class="mt-2 text-md text-gray-800 leading-relaxed">
      {{ $rca->description ?? '-' }}
    </p>
  </fieldset>
</div>

<!-- CA & PA -->
<div class="grid grid-cols-1 gap-6 mb-6">

    <!-- ================= CA ================= -->
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-3 border-b bg-gray-50 flex items-center gap-2">
            <h3 class="text-sm font-semibold text-blue-700">
                Corrective Action (CA)
            </h3>
        </div>

        <!-- Body -->
        <div class="p-5 space-y-4">

            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Description (Rowspan Effect) -->
                <div class="md:col-span-3 row-span-2">

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        Description
                    </p>

                    <div class="h-full bg-gray-50 border rounded-md p-3
                                text-sm text-gray-800 whitespace-pre-line
                                leading-relaxed">

                        {{ $ca->description ?? '-' }}

                    </div>

                </div>

                <!-- PIC -->
                <div>

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        PIC
                    </p>

                    <div class="border rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-800">
                        {{ $ca?->picUser?->name ?? '-' }}
                    </div>

                </div>

                <!-- Due Date -->
                <div>

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        Due Date
                    </p>

                    <div class="border rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-800">

                        {{ $ca?->due_date
                            ? \Carbon\Carbon::parse($ca->due_date)->format('d M Y')
                            : '-' }}

                    </div>

                </div>

            </div>

            <!-- Document -->
            <div class="pt-3 space-y-2">

                <p class="text-[11px] text-gray-400 uppercase tracking-wide">
                    Supporting Document
                </p>

                @if($capa->ca?->supporting_document)

                    <div class="flex items-center justify-between
                                px-3 py-2 border rounded-md bg-gray-50 text-sm">

                        <div class="flex items-center gap-2 text-gray-700 truncate">

                            <i class="fa-solid fa-file-lines text-indigo-500"></i>

                            <span class="truncate max-w-[320px]">
                                {{ $capa->ca->supporting_document }}
                            </span>

                        </div>

                        <a href="{{ asset('capa_document/'.$capa->id.'/'.$capa->ca->supporting_document) }}"
                           download
                           class="text-indigo-600 hover:text-indigo-800">

                            <i class="fa-solid fa-download"></i>

                        </a>

                    </div>

                @else

                    <p class="text-xs text-gray-400 italic">
                        No document uploaded.
                    </p>

                @endif

            </div>

        </div>
    </div>


    <!-- ================= PA ================= -->
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-3 border-b bg-gray-50 flex items-center gap-2">
            <h3 class="text-sm font-semibold text-blue-700">
                Preventive Action (PA)
            </h3>
        </div>

        <!-- Body -->
        <div class="p-5 space-y-4">

            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Description -->
                <div class="md:col-span-3 row-span-2">

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        Description
                    </p>

                    <div class="h-full bg-gray-50 border rounded-md p-3
                                text-sm text-gray-800 whitespace-pre-line
                                leading-relaxed">

                        {{ $pa->description ?? '-' }}

                    </div>

                </div>

                <!-- PIC -->
                <div>

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        PIC
                    </p>

                    <div class="border rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-800">
                        {{ $pa?->picUser?->name ?? '-' }}
                    </div>

                </div>

                <!-- Due Date -->
                <div>

                    <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">
                        Due Date
                    </p>

                    <div class="border rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-800">

                        {{ $pa?->due_date
                            ? \Carbon\Carbon::parse($pa->due_date)->format('d M Y')
                            : '-' }}

                    </div>

                </div>

            </div>

            <!-- Document -->
            <div class="pt-3 space-y-2">

                <p class="text-[11px] text-gray-400 uppercase tracking-wide">
                    Supporting Document
                </p>

                @if($capa->pa?->supporting_document)

                    <div class="flex items-center justify-between
                                px-3 py-2 border rounded-md bg-gray-50 text-sm">

                        <div class="flex items-center gap-2 text-gray-700 truncate">

                            <i class="fa-solid fa-file-lines text-indigo-500"></i>

                            <span class="truncate max-w-[320px]">
                                {{ $capa->pa->supporting_document }}
                            </span>

                        </div>

                        <a href="{{ asset('capa_document/'.$capa->id.'/'.$capa->pa->supporting_document) }}"
                           download
                           class="text-indigo-600 hover:text-indigo-800">

                            <i class="fa-solid fa-download"></i>

                        </a>

                    </div>

                @else

                    <p class="text-xs text-gray-400 italic">
                        No document uploaded.
                    </p>

                @endif

            </div>

        </div>
    </div>

</div>




<!-- EVIDENCE -->
<div class="bg-white border rounded-xl shadow-sm p-5">
   <p class="text-sm font-semibold text-blue-700">List Evidences</p>

  @if($capa->evidences->count())
    <ul class="divide-y">
      @foreach($capa->evidences as $evidence)
        <li class="py-3 flex items-center gap-4">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
            <i class="fa-solid fa-file-image text-indigo-600"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">
              {{ $evidence->file_name }}
            </p>
            <p class="text-xs text-gray-400 uppercase">
              {{ pathinfo($evidence->file_name, PATHINFO_EXTENSION) }}
            </p>
          </div>
          <a href="{{ asset('evidence_capa/'.$capa->id.'/'.$evidence->file_name) }}"
             target="_blank"
             class="text-sm font-semibold text-indigo-600 hover:underline">
            View
          </a>
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-sm text-gray-400 italic">No evidence attached</p>
  @endif

      </div>

      <hr class="my-4">

      <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

         <button id="returnBtn" data-id="{{ $capa->id }}"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-orange-700 hover:bg-orange-800 text-white rounded shadow">
            <i class="fa-solid fa-rotate-left"></i>
           Return
         </button>

         <button id="submitBtn"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
            <i class="fa-solid fa-thumbs-up"></i>
           Authorized
         </button>
      </div>

</div>
    </div>

 <!-- MODAL -->
<div id="returnModal"
class="fixed inset-0 z-[9999] hidden items-center justify-center
bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-md">

  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl p-7 relative animate-fadeIn">

    <!-- Close -->
    <button id="closeModalBtn"
      class="absolute top-4 right-4 text-gray-400 hover:text-red-600 transition text-lg">
      ✕
    </button>

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-2">
      <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 text-lg">
        🔁
      </div>

      <div>
        <h2 class="text-xl font-semibold text-gray-800">
          CAPA Return Decision
        </h2>
        <p class="text-sm text-gray-500">
          Select which evidence remains accepted
        </p>
      </div>
    </div>

    <div class="border-t my-5"></div>

    <p class="text-sm text-gray-600 mb-6 max-w-3xl leading-relaxed">
      Choose how this CAPA should be returned for revision.  
      Your decision determines which evidence must be updated before approval continues.
    </p>

    <!-- DECISION CARDS -->
    <div class="grid md:grid-cols-3 gap-5">

      <!-- ACCEPT CA -->
      <button id="acceptCABtn"
        class="group text-left rounded-xl border border-blue-200 p-5
        hover:border-blue-500 hover:shadow-xl hover:-translate-y-1
        transition duration-200 bg-gradient-to-br from-blue-50 to-white">

        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">📝</span>
          <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-600">
            PARTIAL ACCEPT
          </span>
        </div>

        <h3 class="font-semibold text-gray-800 mb-1">
          Accept Corrective Action
        </h3>

        <p class="text-sm text-gray-600">
          Corrective Action remains valid.  
          Preventive Action evidence must be revised.
        </p>

      </button>


      <!-- ACCEPT PA -->
      <button id="acceptPABtn"
        class="group text-left rounded-xl border border-green-200 p-5
        hover:border-green-500 hover:shadow-xl hover:-translate-y-1
        transition duration-200 bg-gradient-to-br from-green-50 to-white">

        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">📎</span>
          <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-600">
            PARTIAL ACCEPT
          </span>
        </div>

        <h3 class="font-semibold text-gray-800 mb-1">
          Accept Preventive Action
        </h3>

        <p class="text-sm text-gray-600">
          Preventive Action remains valid.  
          Corrective Action evidence must be revised.
        </p>

      </button>


      <!-- REJECT ALL -->
      <button id="rejectAllBtn"
        class="group text-left rounded-xl border border-red-200 p-5
        hover:border-red-500 hover:shadow-xl hover:-translate-y-1
        transition duration-200 bg-gradient-to-br from-red-50 to-white">

        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">⛔</span>
          <span class="text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-600">
            FULL REVISION
          </span>
        </div>

        <h3 class="font-semibold text-gray-800 mb-1">
          Reject All Evidence
        </h3>

        <p class="text-sm text-gray-600">
          Both Corrective and Preventive Action must be revised before AUTHORIZED.
        </p>

      </button>

    </div>

  </div>
</div>

<!-- MR Verification Modal - Enterprise Edition -->
<div
    id="mrVerifyModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-md"
>

    <!-- Main Container -->
    <div
        class="relative bg-white w-full max-w-3xl rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.4)] overflow-hidden"
    >


        <!-- Header -->
        <div class="px-8 py-6 border-b bg-gradient-to-r from-slate-50 to-gray-100">

            <div class="flex items-center justify-between">

                <div>
                    <h3 class="text-xl font-semibold text-gray-900 tracking-wide">
                        Management Review Verification
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        CAPA Authorization & Compliance Assessment
                    </p>
                </div>

                <button
                    id="closeMrModal"
                    class="text-gray-400 hover:text-red-600 text-xl transition"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>

        </div>


        <!-- Body -->
        <div class="px-8 py-7 space-y-8 text-sm text-gray-700 bg-white">

            <!-- Section : New CAPA -->
            <div class="space-y-3">

                <div class="flex items-center gap-2 text-gray-800 font-medium">

                    <i class="fa-solid fa-clipboard-check text-indigo-600"></i>

                    <span>New CAPA Required?</span>

                </div>


                <div class="bg-gray-50 rounded-xl p-4 border">

                    <p class="text-xs text-gray-500 mb-3">
                        Indicate whether a new corrective/preventive action is required.
                    </p>

                    <div class="flex gap-8">

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                name="new_capa_needed"
                                value="yes"
                                class="accent-indigo-600"
                            >
                            <span class="font-medium">Yes, Required</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="radio"
                                name="new_capa_needed"
                                value="no"
                                class="accent-indigo-600"
                            >
                            <span class="font-medium">No, Not Required</span>
                        </label>

                    </div>

                </div>

            </div>


            <!-- Reason -->
            <div
                id="reasonBox"
                class="hidden space-y-2"
            >

                <div class="flex items-center gap-2 text-gray-800 font-medium">

                    <i class="fa-solid fa-circle-exclamation text-amber-500"></i>

                    <span>Justification</span>

                </div>


                <textarea
                    id="newCapaReason"
                    rows="4"
                    class="w-full rounded-xl p-2 border border-gray-300 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="Provide formal justification for initiating a new CAPA..."
                ></textarea>

            </div>


            <!-- MR Statement -->
            <div class="space-y-2">

                <div class="flex items-center gap-2 text-gray-800 font-medium">

                    <i class="fa-solid fa-file-signature text-emerald-600"></i>

                    <span>Management Review Statement</span>

                </div>


                <textarea
                    id="mrStatement"
                    rows="4"
                    class="w-full rounded-xl p-2 border border-gray-300 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    placeholder="Enter formal management verification statement..."
                ></textarea>

            </div>

        </div>


        <!-- Footer -->
        <div class="px-8 py-5 border-t bg-gradient-to-r from-gray-50 to-slate-100">

            <div class="flex justify-between items-center">

                <p class="text-xs text-gray-500">
                    <i class="fa-solid fa-lock mr-1"></i>
                    This action will be permanently recorded
                </p>


                <div class="flex gap-3">

                    <button
                        id="cancelMrVerify"
                        class="px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                    >
                        Cancel
                    </button>

                    <button
                        id="submitMrVerify"
                        class="px-6 py-2 text-sm rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 text-white hover:from-indigo-700 hover:to-blue-700 shadow-lg transition"
                    >
                        Authorize, Now!
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>




<style>
  @keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.25s ease-out;
}
    /* Supaya select2 full width */
.select2-container {
  width: 100% !important;
}

/* Supaya tinggi sama dengan input Tailwind */
.select2-container .select2-selection--single {
  height: 40px !important; /* total tinggi */
  display: flex !important;
  align-items: center !important;
  border: 1px solid #d1d5db; /* border-gray-300 */
  border-radius: 0.375rem;   /* rounded-md */
  padding: 0 0.75rem !important; /* px-3 */
  line-height: normal !important;
}

/* Hilangkan padding default di dalam text */
.select2-container .select2-selection__rendered {
  padding-left: 0 !important;
  padding-right: 0 !important;
  line-height: 1.5rem !important; /* sama seperti input tailwind text-base */
}


/* Placeholder dan text select2 */
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 42px !important;
  font-size: 15px; /* tailwind text-base */
  color: #374151;  /* tailwind text-gray-700 */
}

/* Tombol dropdown */
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 42px !important;
  right: 0.75rem;
}

.swal2-container {
    z-index: 11000 !important;
}

.swal-on-top {
    z-index: 10000 !important;
}


</style>
@push('scripts')
<script>

// Fungsi Toast menggunakan SweetAlert2
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon, // 'success', 'error', 'warning', 'info', 'question'
        title: title
    });
}
  const csrfToken = @json(csrf_token());
const capaId = @json($capa->id);




$(document).ready(function () {


  const currentUser = @json($currentUser);


    // Render comment
    function renderComment(comment){
        return `
        <div class="flex relative new-comment" data-id="${comment.id}" data-user-id="${comment.user_id}">
          <div class="flex flex-col items-center mr-4">
            <div class="w-10 h-10 rounded-full border-2 border-gray-300 overflow-hidden">
              <img src="${comment.photo}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 w-px bg-gray-300 mt-1"></div>
          </div>

          <div class="flex-1">
            <div class="flex items-center justify-between text-sm">
              <div>
                <span class="text-gray-900 font-semibold">${comment.name}</span>
                <span class="font-medium text-gray-400 ml-1">commented</span>
              </div>

              <button class="delete-comment text-red-500 ml-2">
                <i class="fa fa-trash cursor-pointer"></i>
              </button>
            </div>

            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 text-sm comment-text">
                ${comment.comment}
            </div>
          </div>
        </div>
        `;
    }

    // Add comment
    $(document).on('click', '#add-comment-btn', function () {

        const text = $('#new-comment').val().trim();

        if (!text) {
    Swal.fire({
        icon: 'warning',
        title: 'Empty Comment',
        text: 'Comment cannot be empty.',
        confirmButtonText: 'OK'
    });
    return;
}


        const comment = {
            id: Date.now(),
            user_id: currentUser.id,
            name: currentUser.name,
            photo: currentUser.photo,
            comment: text
        };

        $('#comments-list').append(renderComment(comment));

        $('#new-comment').val('');

    });

    // Delete comment
    $(document).on('click', '.delete-comment', function () {

        $(this).closest('[data-id]').remove();

    });

});


    // Ambil data komentar
  function getComments() {
    const comments = [];

    $('#comments-list .new-comment').each(function () {

        const $el = $(this);
        const text = $el.find('.comment-text').text().trim();

        if (text) {
            comments.push({
                user_id: $el.data('user-id'),
                comment: text
            });
        }

    });

    return comments;
}


    
 let selectedCapaId = null;
let $clickedButton = null;
let selectedRevisionType = null; // ⭐ TAMBAHAN

$('#acceptCABtn').on('click', function () {

    selectedRevisionType = 'accept_ca';

    submitReturn($clickedButton, selectedCapaId);
});

$('#acceptPABtn').on('click', function () {

    selectedRevisionType = 'accept_pa';

    submitReturn($clickedButton, selectedCapaId);
});

$('#rejectAllBtn').on('click', function () {

    selectedRevisionType = 'reject_all';

    submitReturn($clickedButton, selectedCapaId);
});


$('#returnBtn').on('click', function () {
    selectedCapaId = $(this).data('id');
    $clickedButton = $(this);

    $('#returnModal').removeClass('hidden').addClass('flex');
});

function closeReturnModal() {
    $('#returnModal').addClass('hidden').removeClass('flex');
}

$('#closeModalBtn').on('click', closeReturnModal);

$('#returnModal').on('click', function (e) {
    if (e.target.id === 'returnModal') {
        closeReturnModal();
    }
});


function submitReturn(btn, capaId) {

    const comments = getComments();

    if (!selectedRevisionType) {
        showToast('error', 'Please select return decision.');
        return;
    }

    if (comments.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Comment Found',
            text: 'Please add at least one comment before returning.',
            confirmButtonText: 'OK',
            customClass: {
            popup: 'swal-on-top'
        },
              backdrop: `
        rgba(0,0,0,0.7)
    `
        });
        return;
    }

    btn.prop('disabled', true).text('Returning...');

    $.ajax({
        url: "{{ route('mr.capa.returnEvidence') }}",
        type: 'POST',
        data: {
            _token: csrfToken,
            capa_id: capaId,
            revision_type: selectedRevisionType, // ⭐ INI YANG DIKIRIM
            comments: comments
        },

        success: function (res) {

            closeReturnModal();

            if (res.success) {
                showToast('success', res.message || 'CAPA successfully returned!');

                setTimeout(() => {
                    window.location.href = "{{ route('mr.capa.index') }}";
                }, 2000);
            }
        },

        error: function (err) {
            console.error(err.responseText);

            const msg = err.responseJSON?.message || 
                        'Terjadi kesalahan saat menyimpan.';

            showToast('error', msg);

            btn.prop('disabled', false).text('Return');
        }
    });
}

function closeReturnModal() {

    $('#returnModal')
        .addClass('hidden')
        .removeClass('flex');

    selectedRevisionType = null;
}


 // Tombol Return utama → buka modal
    $('#submitBtn').on('click', function () {
        $('#mrVerifyModal').removeClass('hidden').addClass('flex');
    });

    // Close modal
    $('#closeMrModal, #cancelMrVerify').on('click', function () {
        $('#mrVerifyModal').addClass('hidden').removeClass('flex');
    });

    // Toggle Reason Box
    $('input[name="new_capa_needed"]').on('change', function () {

        if ($(this).val() === 'yes') {
            $('#reasonBox').slideDown(200).removeClass('hidden');
        } else {
            $('#reasonBox').slideUp(200);
        }

    });

    // Klik luar modal = close
    $('#mrVerifyModal').on('click', function (e) {
        if ($(e.target).is('#mrVerifyModal')) {
            $('#mrVerifyModal').addClass('hidden').removeClass('flex');
        }
    });

$('#submitMrVerify').click(function(){

    const $submitBtn = $(this);
    $submitBtn.prop('disabled', true).text('Authorizing...');

    const url = "{{ route('mr.capa.authorized.save', ':id') }}"
                    .replace(':id', capaId);

    // Ambil value dari modal
    const newCapaNeeded = $('input[name="new_capa_needed"]:checked').val(); // yes/no
    const newCapaReason = $('#newCapaReason').val();
    const mrStatement   = $('#mrStatement').val();

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            capa_id: capaId,
            new_capa_needed: newCapaNeeded,
            new_capa_reason: newCapaReason,
            mr_statement: mrStatement
        },
        success: function(res){
            if(res.success){
                showToast('success', res.message || 'CAPA successfully Authorized!');
                setTimeout(() => {
                    window.location.href = '{{ route("mr.capa.index") }}';
                }, 2000);
            }
        },
        error: function(err){
            console.error(err.responseText);
            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
            showToast('error', msg);
            $submitBtn.prop('disabled', false).text('Authorize');
        }
    });
});


</script>

@endpush

@endsection