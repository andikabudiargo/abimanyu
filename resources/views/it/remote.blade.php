@extends('layouts.app')

@section('title', 'Remote Access Panel')
@section('page-title', 'DASHBOARD REMOTE ACCESS PANEL')
@section('breadcrumb-item', 'IT Network & Access')
@section('breadcrumb-active', 'Remote Access Panel')

@section('content')
<div class="min-h-screen bg-white shadow-md rounded-2xl py-10 px-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-cyan-600 tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-laptop-code text-cyan-500"></i>
                Remote Access Panel
            </h1>
            <p class="text-gray-600 mt-1">Manage and connect to all remote clients securely.</p>

            <div class="w-full md:w-20 h-1.5 bg-cyan-600 rounded-full mt-4"></div>
        </div>

        <a href="#"
           class="px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white shadow rounded-lg 
                  flex items-center gap-2 transition-all">
           <i class="fa-solid fa-plus"></i> Add Client
        </a>
    </div>

    <!-- SEARCH -->
    <div class="flex justify-end mb-6 relative w-full md:w-1/3 ml-auto">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
        <input type="text"
               placeholder="Search PC / User…"
               class="w-full pl-10 pr-4 py-2 bg-white text-gray-700 border border-gray-300
                      focus:border-cyan-500 rounded-xl shadow-sm outline-none transition-all">
    </div>

    <!-- GRID LIST -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">

@foreach ($clients as $client)
<div class="relative p-6 rounded-2xl border border-white/60 shadow-lg 
            bg-gradient-to-br from-blue-500 via-blue-800 to-indigo-500
            backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 
            hover:shadow-xl hover:shadow-cyan-300/40">

  <div class="grid grid-cols-[1fr_auto] items-center mb-6 gap-3">

    <!-- NAME -->
    <h2 class="text-xl font-bold text-white flex items-center gap-2 break-words">
        <i class="fa-solid fa-desktop text-white"></i>
        {{ $client->employee->name }}
    </h2>

    <!-- SOFTWARE BADGE -->
    <span class="text-xs bg-yellow-300 text-gray-900 px-3 py-1 font-semibold rounded-full 
                inline-flex items-center gap-1 w-fit">
        <i class="fa-solid fa-code-branch"></i>
        {{ ucfirst($client->software) }}
    </span>

</div>



    <!-- INFO -->
    <div class="space-y-2 text-white text-sm">

        <div class="flex justify-between items-center">
            <span class="text-white flex items-center gap-2">
                <i class="fa-solid fa-id-badge text-teal-300"></i>
                Remote ID:
            </span>
            <span class="font-semibold">{{ $client->remote_id }}</span>
        </div>

       <div class="flex justify-between items-center">
    <span class="text-white flex items-center gap-2">
        <i class="fa-solid fa-key text-yellow-300"></i>
        Password:
    </span>

    <div class="flex items-center gap-2">
        <!-- PASSWORD TEXT -->
        <span id="pwText" class="font-semibold tracking-wider">•••••••</span>

        <!-- TOGGLE BUTTON -->
        <button id="togglePw" class="text-yellow-300 hover:text-white">
            <i class="fa-solid fa-eye"></i>
        </button>
    </div>
</div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="mt-6 flex justify-between">

        @php
            $url = "ultraviewer://{$client->remote_id}/{$client->remote_password}";
        @endphp

        <a href="{{ $url }}"
           class="px-5 py-2 bg-cyan-500 text-white font-semibold rounded-xl 
                  hover:bg-cyan-600 shadow hover:shadow-lg hover:shadow-cyan-300/40
                  flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plug"></i> Connect
        </a>

        <a href="#"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 
                  flex items-center gap-2 transition-all">
            <i class="fa-solid fa-pen-to-square"></i> Edit
        </a>

    </div>

</div>
@endforeach


</div>

</div>
<script>
    const pwText = document.getElementById('pwText');
    const toggle = document.getElementById('togglePw');

    // Masukkan password asli dari Laravel
    const realPw = "{{ $client->remote_password }}";
    let visible = false;

    toggle.addEventListener('click', () => {
        visible = !visible;

        if (visible) {
            pwText.textContent = realPw;
            toggle.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
        } else {
            pwText.textContent = '•••••••';
            toggle.innerHTML = '<i class="fa-solid fa-eye"></i>';
        }
    });
</script>
@endsection
