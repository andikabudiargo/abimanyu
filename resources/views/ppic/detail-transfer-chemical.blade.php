@extends('layouts.app')

@section('title', 'Detail Transfer Chemical')
@section('page-title', 'DETAIL TRANSFER CHEMICAL')
@section('breadcrumb-item', 'PPIC')
@section('breadcrumb-active', 'Detail Transfer Chemical')

@section('content')
<div class="space-y-4">

  {{-- Header Card --}}
  <div class="w-full bg-white shadow-md rounded-xl px-8 space-y-4 pt-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between border-b border-gray-200 pb-3 mb-6 -mx-8 px-8 gap-4">

  <!-- TEXT -->
  <div>
    <h2 class="text-base font-semibold text-slate-800 tracking-wide">
      Transfer Chemical
    </h2>
    <p class="text-sm text-gray-500">
      Rincian supply dan return chemical
    </p>
  </div>

    <div class="grid grid-cols-2 md:flex md:items-center
                gap-3 md:gap-8 text-sm">

        <!-- Operator -->
        <div class="text-left md:text-right">
            <p class="text-gray-400 text-[10px] md:text-xs uppercase tracking-wide">
                Oleh
            </p>
            <p class="font-semibold text-gray-700 text-sm">
                {{ $transfer->createdBy->name ?? '-' }}
            </p>
        </div>

        <!-- Created At -->
        <div class="text-left md:text-right">
            <p class="text-gray-400 text-[10px] md:text-xs uppercase tracking-wide">
                Dibuat
            </p>
            <p class="font-semibold text-gray-700 text-sm">
                {{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y H:i') }}
            </p>
        </div>

    </div>


</div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Transfer Date</p>
        <p class="text-sm font-semibold text-gray-800">
         {{ $transfer->transfer_date->format('d/m/Y') }}
        </p>
      </div>

      <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Location From</p>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-800">
          <i class="fa-solid fa-warehouse text-indigo-400 text-xs"></i>
          {{ $transfer->location_from }}
        </span>
      </div>

      <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Location To</p>
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-800">
          <i class="fa-solid fa-warehouse text-green-400 text-xs"></i>
          {{ $transfer->location_to }}
        </span>
      </div>

    </div>

    @if($transfer->note)
    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Note</p>
      <p class="text-sm text-gray-700 whitespace-pre-line">{{ $transfer->note }}</p>
    </div>
    @endif

  </div>

  {{-- Chemical List + Summary --}}
  <div class="w-full bg-white shadow-md rounded-xl p-8 space-y-4">

    <div class="flex items-center gap-2 border-b border-gray-200 pb-2 mb-4">
      <p class="text-sm font-semibold text-slate-800">List Chemical</p>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 bg-white">
      <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-600">Code</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-600">Description</th>
            <th class="px-4 py-3 text-right font-semibold text-gray-600">Min Package</th>
            <th class="px-4 py-3 text-right font-semibold text-gray-600">Qty</th>
            <th class="px-4 py-3 text-center font-semibold text-gray-600">Condition</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($transfer->items as $i => $item)
          <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition">
            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $item->article->article_code ?? '-' }}
            </td>
            <td class="px-4 py-3 text-gray-700">
              {{ $item->article->description ?? '-' }}
            </td>
            <td class="px-4 py-3 text-right text-gray-700">
              {{ $item->article->min_package ?? '-' }}
              <span class="text-gray-400 text-xs ml-1">{{ $item->article->unit ?? '' }}</span>
            </td>
            <td class="px-4 py-3 text-right font-semibold text-gray-900">
              {{ $item->qty }}
              <span class="text-gray-400 text-xs ml-1">{{ $item->unit }}</span>
            </td>
            <td class="px-4 py-3 text-center">
              @if($item->condition === 'Utuh')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                  <i class="fa-solid fa-circle-check text-[10px]"></i> Utuh
                </span>
              @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                  <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Tidak Utuh
                </span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="block md:hidden space-y-3">
      @foreach($transfer->items as $i => $item)
      <div class="border border-gray-200 rounded-lg p-4 bg-white space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-400 font-medium">#{{ $i + 1 }}</span>
          @if($item->condition === 'Utuh')
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
              <i class="fa-solid fa-circle-check text-[10px]"></i> Utuh
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
              <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Tidak Utuh
            </span>
          @endif
        </div>
        <p class="text-sm font-semibold text-gray-900">{{ $item->article->article_code ?? '-' }}</p>
        <p class="text-xs text-gray-500">{{ $item->article->description ?? '-' }}</p>
        <div class="flex justify-between text-xs text-gray-500 pt-1 border-t border-gray-100">
          <span>Min Package: <strong>{{ $item->article->min_package ?? '-' }} {{ $item->article->unit ?? '' }}</strong></span>
          <span>Qty: <strong class="text-gray-900">{{ $item->qty }} {{ $item->unit }}</strong></span>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Transfer Summary --}}
    <div class="mt-6 flex justify-start">
      <div class="w-full md:w-[480px] border border-gray-200 bg-white rounded-md pb-6 overflow-hidden">

        <div class="flex items-center gap-2 border-b border-gray-200 py-3 px-4 mb-4">
          <i class="fa-solid fa-file text-indigo-700 text-sm"></i>
          <h2 class="text-base font-semibold text-indigo-700 tracking-wide">Transfer Summary</h2>
        </div>

        {{-- Location Flow --}}
        <div class="mx-4 mb-4 flex items-center justify-between gap-2
                    bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
          <div class="flex flex-col items-center text-center min-w-[100px]">
            <i class="fa-solid fa-warehouse text-indigo-500 text-lg mb-1"></i>
            <span class="text-xs font-medium text-indigo-700">{{ $transfer->location_from }}</span>
          </div>
          <div class="flex-1 flex flex-col items-center gap-1">
            <i class="fa-solid fa-arrow-right text-indigo-400 text-xl"></i>
          </div>
          <div class="flex flex-col items-center text-center min-w-[100px]">
            <i class="fa-solid fa-warehouse text-indigo-500 text-lg mb-1"></i>
            <span class="text-xs font-medium text-indigo-700">{{ $transfer->location_to }}</span>
          </div>
        </div>

        {{-- Stats --}}
        @php
          $utuh  = $transfer->items->where('condition', 'Utuh')->count();
          $sisa  = $transfer->items->where('condition', 'Tidak Utuh')->count();
          $total = $transfer->items->count();
        @endphp
        <div class="divide-y divide-gray-100 text-sm px-2">
          <div class="flex justify-between px-4 py-2">
            <span class="text-gray-500">Chemical Utuh</span>
            <span class="font-semibold text-gray-900">
              {{ $utuh }} <span class="text-gray-400 font-normal text-xs">item</span>
            </span>
          </div>
          <div class="flex justify-between px-4 py-2">
            <span class="text-gray-500">Chemical Tidak Utuh / Sisa</span>
            <span class="font-semibold text-gray-900">
              {{ $sisa }} <span class="text-gray-400 font-normal text-xs">item</span>
            </span>
          </div>
          <div class="flex justify-between px-4 py-2 bg-gray-50 rounded-b">
            <span class="text-gray-700 font-medium">Total Chemical</span>
            <span class="font-bold text-indigo-700">
              {{ $total }} <span class="text-indigo-400 font-normal text-xs">item</span>
            </span>
          </div>
        </div>

      </div>
    </div>

    {{-- Meta --}}
    <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col md:flex-row gap-1 text-xs text-gray-400">
      <a href=""
      class="inline-flex w-full sm:w-24 justify-center items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded shadow">
      <i data-feather="printer" class="h-4 w-4"></i> Print
    </a>
      <a href=""
      class="inline-flex w-full sm:w-24 justify-center items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded shadow">
      <i data-feather="edit-2" class="h-4 w-4"></i> Edit
    </a>

    <a href="{{ route('ppic.tfcm1.index') }}"
      class="inline-flex w-full sm:w-24 justify-center items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded shadow">
      <i data-feather="arrow-left" class="h-4 w-4"></i> Back
    </a>
    </div>

  </div>
</div>

<style>
  table th, table td {
    vertical-align: middle;
  }
</style>
@endsection