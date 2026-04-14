@extends('layouts.app')

@section('title', 'CAPA Submit')
@section('page-title', 'CAPA SUBMIT')
@section('breadcrumb-item', 'CAPA Management')
@section('breadcrumb-active', 'CAPA Submit')
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

    <form id="capa-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
<div class="max-w-xl mx-auto border border-gray-200 p-4">

  <!-- Header -->
  <h3 class="text-sm font-medium text-gray-800">Commentary</h3>
  <p class="text-xs text-gray-500 mb-6">
    Review Comment from Management Representative
  </p>

  <!-- Comments List -->
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

          <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 text-sm">

            {{ $comment->comment }}

          </div>

        </div>

      </div>

    @empty

      <p class="text-sm text-gray-400">
        No comments yet.
      </p>

    @endforelse

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
            Corrective & Preventive Action Submit
        </h1>

        <p class="flex items-center gap-2
                  text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
             CAPA Supporting Document & Evidence Submitted
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

            
        } elseif ($capa->status == 'Approved') {
            $bg     = 'bg-lime-100';
            $text   = 'text-lime-800';
            $border = 'border-lime-300';
            $icon   = 'fa-clipboard-check';
            $iconColor = 'text-lime-600';

        } elseif ($capa->status == 'Submitted') {
            $bg     = 'bg-purple-100';
            $text   = 'text-purple-800';
            $border = 'border-purple-300';
            $icon   = 'fa-user-check';
            $iconColor = 'text-purple-600';

        } elseif ($capa->status == 'Returned for Evidence') {
            $bg     = 'bg-red-100';
            $text   = 'text-red-800';
            $border = 'border-red-300';
            $icon   = 'fa-rotate-left';
            $iconColor = 'text-red-600';
        
        } elseif ($capa->status == 'Returned for Action') {
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

   
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

 @if($capa->status == 'Returned for Evidence')
<div class="col-span-2 mb-4 text-sm text-yellow-600 bg-yellow-50 p-2 border border-yellow-600 rounded">
 ⚠️ This CAPA was returned by MR. Please revise and resubmit according to the comments provided.
</div>
@endif

</div>

<div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

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
<div class="col-span-2 space-y-8 mb-8">

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
 <form id="capa-form" enctype="multipart/form-data">
      @csrf
      @method('PUT')
<!-- CA & PA -->
<div class="grid grid-cols-1 col-span-2 gap-6 mb-6">

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

           <div class="pt-3 space-y-3">

<p class="text-[11px] text-gray-400 uppercase tracking-wide">
    Supporting Document
</p>

@if($caClosed)

    <!-- LOCKED STATE -->
    <div class="flex items-center justify-between
        px-4 py-3 rounded-lg
        bg-gray-100 border border-gray-200">

        <div class="flex items-center gap-2 text-gray-600 text-sm">

            <i class="fa-solid fa-lock"></i>

            <span class="font-medium">
                Upload Locked (Action Closed)
            </span>

        </div>

        @if(!empty($capa->ca?->supporting_document))
            <a href="{{ asset('capa_document/'.$ca->capa_id.'/'.$ca->supporting_document) }}"
               target="_blank"
               class="px-3 py-1 text-xs rounded bg-gray-700 text-white hover:bg-gray-800">

                View File
            </a>
        @endif

    </div>

@else

    {{-- Jika ADA File --}}
    @if(!empty($capa->ca?->supporting_document))

        <div id="ca-document-area" class="flex items-center justify-between
                    px-3 py-2 border rounded-md bg-green-50 text-sm">

            <div class="flex items-center gap-2 text-green-700 truncate">

                <i class="fa-solid fa-file-lines"></i>

                <span class="font-medium truncate">
                    {{ basename($capa->ca->supporting_document) }}
                </span>

            </div>

            <div class="flex items-center gap-2">

                <!-- Download -->
               <a href="{{ asset('capa_document/'.$ca->capa_id.'/'.$ca->supporting_document) }}"
       target="_blank"
       class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">
        <i class="fa-solid fa-download"></i>
    </a>

                <!-- Delete -->
               <button type="button"
    onclick="deleteDocument('{{ $capa->id }}','CA')"
    class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">
    <i class="fa-solid fa-trash"></i>
</button>

            </div>

        </div>

    {{-- Jika TIDAK ADA File → Baru Muncul Upload --}}
    @else
     <div class="flex flex-col gap-1.5 w-full">

        <div class="flex items-center gap-2 text-gray-700 w-full">

            <i class="fa-solid fa-file-lines text-indigo-500 flex-shrink-0"></i>

            <input type="file"
                   name="ca_supporting"
                   accept=".pdf,.doc,.docx,.xls,.xlsx"
                   class="flex-1 text-sm
                          border rounded-md
                          file:px-3 file:py-1.5
                          file:bg-indigo-50 file:text-indigo-700
                          file:border-0">

        </div>

         <!-- File requirement info -->
            <div class="flex items-center gap-1.5 px-1">
                <i class="fa-solid fa-circle-info text-[10px] text-gray-400"></i>
                <p class="text-[10px] text-gray-400 leading-relaxed">
                    Accepted formats: <span class="font-semibold text-gray-500">PDF, DOC, DOCX, XLS, XLSX</span>
                    &nbsp;·&nbsp; Max size: <span class="font-semibold text-gray-500">10 MB</span>
                    &nbsp;·&nbsp; Ensure document is legible and complete before submission.
                </p>
            </div>

        </div>

    @endif
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
           <div class="pt-3 space-y-3">

    <p class="text-[11px] text-gray-400 uppercase tracking-wide">
        Supporting Document
    </p>

    @php
        $pa = $capa->pa;
    @endphp

    @if($paClosed)

    <!-- LOCKED STATE -->
    <div class="flex items-center justify-between
        px-4 py-3 rounded-lg
        bg-gray-100 border border-gray-200">

        <div class="flex items-center gap-2 text-gray-600 text-sm">

            <i class="fa-solid fa-lock"></i>

            <span class="font-medium">
                Upload Locked (Action Closed)
            </span>

        </div>

        @if(!empty($capa->pa?->supporting_document))
            <a href="{{ asset('capa_document/'.$pa->capa_id.'/'.$pa->supporting_document) }}"
               target="_blank"
               class="px-3 py-1 text-xs rounded bg-gray-700 text-white hover:bg-gray-800">

                View File
            </a>
        @endif

    </div>

@else


    {{-- Jika ADA File --}}
    @if(!empty($pa?->supporting_document))

        <div id="pa-document-area" class="flex items-center justify-between
                    px-3 py-2 border rounded-md bg-green-50 text-sm">

            <div class="flex items-center gap-2 text-green-700 truncate">

                <i class="fa-solid fa-file-lines"></i>

                <span class="font-medium truncate">
                    {{ basename($pa->supporting_document) }}
                </span>

            </div>

            <div class="flex items-center gap-2">

                <!-- Download -->
                <a href="{{ asset('capa_document/'.$pa->capa_id.'/'.$pa->supporting_document) }}"
       target="_blank"
       class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">
        <i class="fa-solid fa-download"></i>
    </a>


                <!-- Delete -->

                  <button type="button"
    onclick="deleteDocument('{{ $capa->id }}','PA')"
    class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">
    <i class="fa-solid fa-trash"></i>
</button>


            </div>

        </div>


    {{-- Jika TIDAK ADA File → Upload --}}
    @else

      <div class="flex flex-col gap-1.5 w-full">

            <div class="flex items-center gap-2 text-gray-700 w-full">
                <i class="fa-solid fa-file-lines text-indigo-500 flex-shrink-0"></i>
                <input type="file"
                       name="pa_supporting"
                       accept=".pdf,.doc,.docx,.xls,.xlsx"
                       class="flex-1 text-sm
                              border rounded-md
                              file:px-3 file:py-1.5
                              file:bg-indigo-50 file:text-indigo-700
                              file:border-0">
            </div>

            <!-- File requirement info -->
            <div class="flex items-center gap-1.5 px-1">
                <i class="fa-solid fa-circle-info text-[10px] text-gray-400"></i>
                <p class="text-[10px] text-gray-400 leading-relaxed">
                    Accepted formats: <span class="font-semibold text-gray-500">PDF, DOC, DOCX, XLS, XLSX</span>
                    &nbsp;·&nbsp; Max size: <span class="font-semibold text-gray-500">10 MB</span>
                    &nbsp;·&nbsp; Ensure document is legible and complete before submission.
                </p>
            </div>

        </div>
    @endif
    @endif

</div>



        </div>
    </div>

</div>


 

       
<!-- EVIDENCE UPLOAD 
<div class="col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Existing Evidences
    </label>

    <div id="old-evidences"
         class="flex flex-wrap gap-3 mb-4">

        @forelse($capa->evidences as $evidence)

            @php
                $imgUrl = asset('evidence_capa/'.$capa->id.'/'.$evidence->file_name);
            @endphp

            <div class="evidence-item relative group border rounded-lg overflow-hidden shadow-sm
                        w-32 h-32 bg-gray-100"
                 data-id="{{ $evidence->id }}">

                {{-- Clickable Image --}}
                <a href="{{ $imgUrl }}" target="_blank" class="block cursor-zoom-in">

                    <img src="{{ $imgUrl }}"
                         class="w-full h-full object-cover
                                transition-all duration-300
                                group-hover:scale-110">

                    {{-- Overlay --}}
                    <div class="absolute inset-0
                                bg-black/40
                                flex items-center justify-center
                                opacity-0
                                group-hover:opacity-100
                                transition-all duration-300">

                        <div class="flex items-center gap-2
                                    px-4 py-2
                                    bg-white/90
                                    rounded-full
                                    text-gray-800
                                    text-sm font-medium
                                    shadow-lg
                                    transform translate-y-3 scale-95
                                    opacity-0
                                    group-hover:opacity-100
                                    group-hover:translate-y-0
                                    group-hover:scale-100
                                    transition-all duration-300">

                            <i class="fa fa-eye text-indigo-600"></i>
                            <span>Preview</span>
                        </div>

                    </div>

                </a>

                {{-- Delete Button --}}
                <button type="button"
                    class="delete-evidence absolute top-1 right-1
                           bg-red-600 text-white text-xs px-2 py-1 rounded
                           opacity-0 group-hover:opacity-100
                           transition-all duration-200 z-10"
                    data-id="{{ $evidence->id }}"
                    data-url="{{ route('mr.capa.evidence.destroy', $evidence->id) }}">

                    <i class="fa fa-trash"></i>
                </button>

            </div>

        @empty

            <p class="text-sm text-gray-400 italic flex items-center gap-2">
    <i class="fa fa-folder-open"></i>
    No Evidence Added
</p>
        @endforelse

    </div>
</div>



<div class="col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Evidence Upload <small class="text-red-600">*</small>
    </label>
    <p class="text-xs text-gray-500 mb-2">
        Upload image files only (JPG, JPEG, PNG)
    </p>

    <input
        type="file"
        id="evidence_files"
        name="evidence_files[]"
        multiple
        accept="image/jpeg,image/png"
        class="block w-full text-sm text-gray-700
               file:mr-4 file:py-2 file:px-4
               file:rounded-lg file:border-0
               file:text-sm file:font-semibold
               file:bg-indigo-50 file:text-indigo-700
               hover:file:bg-indigo-100
               border border-gray-300 rounded-md"
    />

    <ul id="selected-files"
        class="mt-3 grid grid-cols-1 gap-3 text-sm">
    </ul>
</div>

      </div>-->

      <hr class="my-4">

      <div class="flex justify-start items-center gap-2 mt-4">
         <a href="{{ route('mr.capa.index') }}" 
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
           ← Back
         </a>

         <button type="submit" id="submitBtn"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
            <i class="fa-solid fa-send"></i>
           Submit
         </button>
      </div>

    </form>



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
</style>
@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.getElementById('evidence_files');
    const fileList  = document.getElementById('selected-files');

    if (!fileInput || !fileList) {
        console.error('Evidence upload element not found');
        return;
    }

    let filesArray = [];

    function formatSize(bytes) {
        return bytes < 1024 * 1024
            ? Math.round(bytes / 1024) + ' KB'
            : (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function renderFileList() {
        fileList.innerHTML = '';

        filesArray.forEach((file, index) => {
            const ext = file.name.split('.').pop().toUpperCase();

            const li = document.createElement('li');
            li.className =
                'flex gap-3 items-center bg-gray-50 border border-gray-200 ' +
                'rounded-lg p-3 shadow-sm';

            // thumbnail
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-14 h-14 object-cover rounded border';
            li.appendChild(img);

            const info = document.createElement('div');
            info.className = 'flex flex-col overflow-hidden';

            const name = document.createElement('span');
            name.className = 'font-medium text-gray-700 truncate';
            name.textContent = file.name;

            const meta = document.createElement('span');
            meta.className = 'text-xs text-gray-500';
            meta.textContent = `${ext} • ${formatSize(file.size)}`;

            info.appendChild(name);
            info.appendChild(meta);

            li.appendChild(info);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.className =
                'ml-auto text-red-500 text-xl font-bold hover:text-red-700';
            removeBtn.onclick = () => {
                filesArray.splice(index, 1);
                renderFileList();
            };

            li.appendChild(removeBtn);
            fileList.appendChild(li);
        });
    }

    fileInput.addEventListener('change', function () {
        filesArray = Array.from(this.files).filter(file =>
            ['image/jpeg', 'image/png'].includes(file.type)
        );

        renderFileList();
    });

});

$('#capa-form').off('submit').on('submit', function (e) {
    e.preventDefault();

      const $form = $(this);
    const $btn = $('#submitBtn');

    // Disable tombol untuk mencegah klik ganda
    $btn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
    const originalText = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    const formData = new FormData(this);

    $.ajax({
        url: '{{ route("mr.capa.submitted.save", $capa->id) }}',
        method: 'POST', // Laravel butuh _method PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message || 'CAPA successfully Submitted!');
                setTimeout(() => {
                    window.location.href = '{{ route("mr.capa.index") }}';
                }, 2000);
            } else {
                showToast('error', res.message || 'Gagal submit CAPA.');
                  $btn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').html(originalText);
            }
        },
        error: function (err) {
            console.error(err.responseText);
            const msg = err.responseJSON?.message || 'Terjadi kesalahan saat submit.';
            showToast('error', msg);
            $btn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').html(originalText);
        }
    });
});


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

$('#department').on('change', function () {
    let deptId = $(this).val();

    let $repSelect = $('#representative');
    let $auditeeList = $('#auditee-list');

    // Reset
    $repSelect.html('<option value="">Loading...</option>');
    $auditeeList.html('<p class="text-gray-400">Loading...</p>');

    if (!deptId) {
        $repSelect.html('<option value="">-- Choose Dept. Representative --</option>');
        $auditeeList.html('<p class="text-gray-400">Choose department first...</p>');
        return;
    }

    $.ajax({
        url: `/it/departments/${deptId}/users`,
        type: 'GET',
        dataType: 'json',
        success: function (users) {

            // Isi representative
            $repSelect.html('<option value="">-- Choose Dept. Representative --</option>');

            if (users.length === 0) {
                $repSelect.html('<option value="">No staff found</option>');
                $auditeeList.html('<p class="text-red-500">No users found in this department.</p>');
                return;
            }

            $.each(users, function (i, user) {
                $repSelect.append(`<option value="${user.id}">${user.name}</option>`);
            });

            // Isi list auditee
            let html = '';
            $.each(users, function (i, user) {
                html += `
                    <div class="flex items-center gap-2 border-b py-1">
                        <span class="w-6 h-6 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-full">
                            ${i + 1}
                        </span>
                        <span>${user.name}</span>
                    </div>
                `;
            });
            $auditeeList.html(html);
        },
        error: function () {
            $repSelect.html('<option value="">Error loading data</option>');
            $auditeeList.html('<p class="text-red-500">Failed to load auditee list.</p>');
        }
    });
});

$(document).ready(function () {

    console.log('✅ Attachment script loaded');

    let currentFiles = [];

    const $attachmentsInput   = $('#attachments');
    const $selectedFilesList = $('#selectedFilesList');

    // Kalau element tidak ada → stop (biar halaman lain aman)
    if (!$attachmentsInput.length || !$selectedFilesList.length) {
        console.warn('⚠️ Attachment element not found, script skipped');
        return;
    }

    // Icon berdasarkan ekstensi
    function getFileIcon(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();

        switch (ext) {
            case 'pdf': return 'file-text';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': return 'image';
            case 'xlsx':
            case 'xls': return 'file';
            case 'doc':
            case 'docx': return 'file-text';
            default: return 'file';
        }
    }

    // Format size
    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';

        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Render list
    function renderFileList() {

        // Sync ke input.files
        const dt = new DataTransfer();

        currentFiles.forEach(file => {
            dt.items.add(file);
        });

        $attachmentsInput[0].files = dt.files;

        // Render UI
        $selectedFilesList.html('');

        currentFiles.forEach((file, index) => {

            const item = `
                <li class="flex items-center justify-between mb-2 p-2 bg-white shadow-sm rounded-lg border border-gray-200">

                    <div class="flex items-center gap-3">
                        <i data-feather="${getFileIcon(file.name)}"
                           class="w-5 h-5 text-gray-500"></i>

                        <div class="flex flex-col">
                            <span class="text-gray-800 font-medium">${file.name}</span>
                            <span class="text-xs text-gray-500">${formatBytes(file.size)}</span>
                        </div>
                    </div>

                    <button type="button"
                            class="remove-file text-red-500 ml-2"
                            data-index="${index}">
                        <i data-feather="trash-2" class="w-4 h-4"></i>
                    </button>

                </li>
            `;

            $selectedFilesList.append(item);

        });

        feather.replace();
    }

    // Input change
    $attachmentsInput.on('change', function () {

        const files = Array.from(this.files);

        currentFiles = currentFiles.concat(files);

        renderFileList();

    });

    // Delete file (delegation, aman)
    $(document).on('click', '.remove-file', function () {

        const index = $(this).data('index');

        currentFiles.splice(index, 1);

        renderFileList();

    });

});

$(document).on('click', '.delete-evidence', function (e) {

    e.preventDefault();

    let url = $(this).data('url');
    let button = $(this);

    Swal.fire({
        title: 'Yakin hapus?',
        text: "File & data akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        console.log('🟡 Swal result:', result);

        if (result.isConfirmed) {


            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {

                    Swal.fire(
                        'Evidence Berhasil Terhapus!',
                        res.message,
                        'success'
                    );

                    button.closest('.evidence-item').remove();
                },
                error: function (xhr, status, error) {

                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus',
                        'error'
                    );
                }
            });

       
        }

    });

});

function deleteDocument(capaId, type) {

    Swal.fire({
        title: 'Delete Document?',
        text: `This ${type} file will be permanently removed`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'

    }).then((result) => {

        if (!result.isConfirmed) return;

        // Loading popup
        Swal.fire({
            title: 'Deleting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

       $.ajax({
    url: "{{ route('mr.capa.document.delete', ['capa'=>'__ID__','type'=>'__TYPE__']) }}"
        .replace('__ID__', capaId)
        .replace('__TYPE__', type),

    type: 'POST',
    data: {
        _token: '{{ csrf_token() }}',
        _method: 'DELETE'
    },

            success: function (res) {

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Document has been removed',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {

                  location.reload();

                });
            },

            error: function (xhr) {

                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: xhr.responseJSON?.message ?? 'Delete failed'
                });
            }

        });

    });
}


</script>

@endpush

@endsection