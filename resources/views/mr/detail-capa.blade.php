@extends('layouts.app')

@section('title', 'CAPA Detail')
@section('page-title', 'CAPA DETAIL')
@section('breadcrumb-item', 'CAPA Management')
@section('breadcrumb-active', 'CAPA Detail')
@section('content')

@php
    $rca = $capa->actions->firstWhere('type', 'RCA');
    $ca  = $capa->actions->firstWhere('type', 'CA');
    $pa  = $capa->actions->firstWhere('type', 'PA');
    $caClosed = ($ca->status ?? '') === 'Closed';
    $paClosed = ($pa->status ?? '') === 'Closed';
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
         <div class="flex items-center gap-2">
            <i class="fa fa-user-tie text-indigo-500 text-sm"></i>
            <span class="font-medium text-gray-800">Dept. Representative 2:</span>
            <span class="text-gray-700">{{ $capa->representative2->name ?? '-' }}</span>
            
        </div>
    </div>
@else
    <span class="text-gray-400 italic text-sm">No department assigned</span>
@endif

        </div>
<div class="max-w-xl mx-auto border border-gray-200 p-4">

  <!-- Header -->
  <h3 class="text-sm font-medium text-gray-800">Commentary</h3>
  <p class="text-xs text-gray-500 mb-6">Review Comment from Management Representative</p>

  <!-- COMMENTS CONTAINER -->
  <div id="comments-list" class="space-y-6">

    @forelse($capa->comments as $comment)

      <div class="flex relative"
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
            Corrective & Preventive Action Detail
        </h1>

        <p class="flex items-center gap-2
                  text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
            Check CAPA detailed Information
        </p>
    </div>

<div class="flex sm:items-center">
    @php
        if ($capa->status == 'Draft') {
            $bg     = 'bg-gray-100';
            $text   = 'text-gray-800';
            $border = 'border-gray-300';
            $icon   = 'fa-pen-to-square';
            $iconColor = 'text-gray-600';

        } elseif ($capa->status == 'Open') {
            $bg     = 'bg-yellow-100';
            $text   = 'text-yellow-800';
            $border = 'border-yellow-300';
            $icon   = 'fa-paste';
            $iconColor = 'text-yellow-600';

        } elseif ($capa->status == 'Verified') {
            $bg     = 'bg-blue-100';
            $text   = 'text-blue-800';
            $border = 'border-blue-300';
            $icon   = 'fa-clipboard-check';
            $iconColor = 'text-blue-600';

        } elseif ($capa->status == 'Submitted') {
            $bg     = 'bg-purple-100';
            $text   = 'text-purple-800';
            $border = 'border-purple-300';
            $icon   = 'fa-user-check';
            $iconColor = 'text-purple-600';

        } elseif (in_array($capa->status, ['Returned for Evidence', 'Returned for Action'])) {
            $bg     = 'bg-red-100';
            $text   = 'text-red-800';
            $border = 'border-red-300';
            $icon   = 'fa-rotate-left';
            $iconColor = 'text-red-600';

            } elseif ($capa->status == 'Authorized') {
            $bg     = 'bg-green-100';
            $text   = 'text-green-800';
            $border = 'border-green-300';
            $icon   = 'fa-thumbs-up';
            $iconColor = 'text-green-600';

            } elseif ($capa->status == 'Closed') {
            $bg     = 'bg-teal-100';
            $text   = 'text-teal-800';
            $border = 'border-teal-300';
            $icon   = 'fa-lock';
            $iconColor = 'text-teal-600';

            } elseif ($capa->status == 'In Progress') {
            $bg = 'bg-orange-100';
            $text = 'text-orange-800';
            $border = 'border-orange-300';
            $icon = 'fa-arrows-spin';
            $iconColor = 'text-orange-600';

        } else {
            // Default
            $bg     = 'bg-green-100';
            $text   = 'text-green-800';
            $border = 'border-green-300';
            $icon   = 'fa-lock';
            $iconColor = 'text-green-600';
        }
    @endphp

    <span class="inline-flex items-center justify-center gap-1.5
        px-3 py-1 text-sm font-semibold rounded-full
        {{ $bg }} {{ $text }} {{ $border }} border
        w-fit">

        <i class="fa-solid {{ $icon }} text-xs {{ $iconColor }}"></i>

        {{ $capa->status }}
    </span>
</div>



</div>

   
<!-- HEADER INFO -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

@if(in_array($capa->status, ['Returned for Evidence', 'Returned for Action']))
    <div class="col-span-2 mb-4 text-sm text-yellow-600 bg-yellow-50 p-3 border border-yellow-600 rounded">

        ⚠️ This CAPA was returned by MR. Please revise and resubmit according to the comments provided.
    </div>
@endif

  
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
        <div class="px-5 py-3 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-blue-700">
                Corrective Action (CA)
            </h3>

            @if($caClosed)
        <span class="flex items-center gap-1
            text-xs font-semibold px-3 py-1 rounded-full
            bg-emerald-100 text-emerald-700 border border-emerald-200">

            <i class="fa-solid fa-circle-check text-[10px]"></i>
            Closed
        </span>
    @else
        <span class="text-xs px-3 py-1 rounded-full
            bg-amber-100 text-amber-700 border border-amber-200">
            Open
        </span>
    @endif
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

                        @php
    $caExt = pathinfo($capa->ca->supporting_document, PATHINFO_EXTENSION);
    $caFilename = ($capa->capa_number ?: 'CAPA') . '_CA.' . $caExt;
@endphp
<a href="{{ asset('capa_document/'.$capa->id.'/'.$capa->ca->supporting_document) }}"
   download="{{ $caFilename }}"
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
        <div class="px-5 py-3 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-blue-700">
                Preventive Action (PA)
            </h3>

            @if($paClosed)
        <span class="flex items-center gap-1
            text-xs font-semibold px-3 py-1 rounded-full
            bg-emerald-100 text-emerald-700 border border-emerald-200">

            <i class="fa-solid fa-circle-check text-[10px]"></i>
            Closed
        </span>
    @else
        <span class="text-xs px-3 py-1 rounded-full
            bg-amber-100 text-amber-700 border border-amber-200">
            Open
        </span>
    @endif
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

                        @php
    $paExt = pathinfo($capa->pa->supporting_document, PATHINFO_EXTENSION);
    $paFilename = ($capa->capa_number ?: 'CAPA') . '_PA.' . $paExt;
@endphp
<a href="{{ asset('capa_document/'.$capa->id.'/'.$capa->pa->supporting_document) }}"
   download="{{ $paFilename }}"
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




<!-- EVIDENCE 
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

      </div>-->

      <hr class="my-4">

      <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

        @if($capa->status === 'Authorized')
    <button type="submit" id="submitBtn"
        class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
        <i class="fa-solid fa-check-circle"></i>
        Closed
    </button>
@endif

      </div>

</div>
    </div>

 <!-- Modal Overlay -->
<div id="capaModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 transform transition-transform duration-300 scale-95 relative animate-bounceIn">
        
        <!-- Close Button -->
        <button id="closeCapaModal" class="absolute top-4 right-4 text-gray-400 hover:text-red-700 text-3xl font-bold">&times;</button>
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6 border-b border-gray-200 pb-4">
            <h2 class="text-xl font-semibold text-gray-800">Management Representative Statement</h2>
        </div>
        
       <!-- Chat Bubbles -->
<div class="flex flex-col gap-6">

    <!-- CAPA Needed Bubble -->
    <div class="flex flex-col self-start max-w-xl transition transform duration-500 animate-fadeIn">
        <!-- Header: icon + MR -->
        <div class="flex items-center gap-2 mb-1">
            <div class="w-8 h-8 bg-green-100 text-green-700 flex items-center justify-center rounded-full">
                <!-- User Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7.966 7.966 0 0112 15c2.028 0 3.886.78 5.303 2.051M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-semibold text-gray-700">Management Representative New CAPA Needed?</span>
        </div>
        
        <!-- Bubble -->
        @if($capa->new_capa_needed == 'yes')
        <div class="bg-red-50 text-red-800 p-4 rounded-xl shadow-sm font-medium">
            CAPA ini memerlukan CAPA baru. Alasannya <span class="font-semibold">{{ $capa->new_capa_reason ?? '-' }}</span>
        </div>
        @else
        <div class="bg-green-50 text-green-800 p-4 rounded-xl shadow-sm font-medium">
            CAPA ini tidak memerlukan CAPA baru.
        </div>
        @endif

        <!-- Timestamp -->
        <div class="text-xs text-gray-400 mt-1 text-right">
            {{ $capa->authorized_at ? \Carbon\Carbon::parse($capa->authorized_at)->format('d-m-Y H:i') : '-' }}
        </div>
    </div>

    <!-- MR Statement Bubble -->
    @if($capa->mr_statement)
    <div class="flex flex-col self-start max-w-xl transition transform duration-500 animate-fadeIn">
        <!-- Header: icon + MR -->
        <div class="flex items-center gap-2 mb-1">
            <div class="w-8 h-8 bg-green-100 text-green-700 flex items-center justify-center rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7.966 7.966 0 0112 15c2.028 0 3.886.78 5.303 2.051M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-semibold text-gray-700">Management Representative Verification</span>
        </div>

        <!-- Bubble -->
        <div class="bg-green-50 text-green-800 p-4 rounded-xl shadow-sm font-medium">
            {{ $capa->mr_statement }}
        </div>

        <!-- Timestamp -->
        <div class="text-xs text-gray-400 mt-1 text-right">
            {{ $capa->authorized_at ? \Carbon\Carbon::parse($capa->authorized_at)->format('d-m-Y H:i') : '-' }}
        </div>
    </div>
    @endif

</div>


        <!-- Footer -->
        <div class="mt-6 text-center">
            <button id="closeCapaModalBtn" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Close
            </button>
        </div>
    </div>
</div>
<style>
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

@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

.animate-bounceIn {
  animation: bounceIn 0.6s ease forwards;
}

.animate-bounceIn.delay-200 {
  animation-delay: 0.2s;
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
        <div class="flex relative" data-id="${comment.id}" data-user-id="${comment.user_id}">
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

            <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 text-sm">
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
            alert('Comment cannot be empty');
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



$('#submitBtn').click(function(){

    const $submitBtn = $(this); // 🔥 ini solusi paling aman

    // Disable button
    $submitBtn.prop('disabled', true).text('Closing...');

    const url = "{{ route('mr.capa.approve', ':id') }}"
                    .replace(':id', capaId);

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            capa_id: capaId,
        },
        success: function(res){

            if(res.success){

                showToast('success', res.message || 'CAPA successfully Closed!');

                setTimeout(() => {
                    window.location.href = '{{ route("mr.capa.index") }}';
                }, 2000);

            }

        },
        error: function(err){

            console.error(err.responseText);

            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat close CAPA.';

            showToast('error', msg);

            // Aktifkan lagi kalau gagal
            $submitBtn.prop('disabled', false).text('Closed');

        }
    });
});

$(document).ready(function(){

    @if($capa->status === 'Authorized')
        $('#capaModal').fadeIn(200).css('display','flex').addClass('scale-100');
    @endif

    $('#closeCapaModal, #closeCapaModalBtn').click(function(){
        $('#capaModal').fadeOut(200).removeClass('scale-100');
    });

});




</script>

@endpush

@endsection