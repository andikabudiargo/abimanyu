@extends('layouts.app')
@section('title', 'PPN Calculator')
@section('page-title', 'PPN Calculator')
@section('breadcrumb-item', 'Accounting')
@section('breadcrumb-active', 'PPN Calculator')

@section('content')
<!-- Main Content -->
   <main class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row gap-6">

      <!-- Kalkulator Card -->
      <div class="w-full md:w-1/3 bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-700">Kalkulator Coretax</h2>
        </div>

        <div class="space-y-6">

          <!-- DPP -->
          <div>
            <label for="dpp" class="block text-sm font-semibold text-gray-600">DPP</label>
            <input type="text" id="dpp" oninput="formatInputRupiah(event); hitung();"
              placeholder="Contoh: 10000000"
              class="w-full mt-2 bg-transparent border-b-2 border-gray-200 focus:outline-none focus:border-blue-500 py-2 text-gray-800 text-lg placeholder-gray-400 transition">
          </div>

          <!-- DPP Nilai Lain -->
          <div>
            <label for="dppNilaiLain" class="block text-sm font-semibold text-gray-600">DPP Nilai Lain (11/12)</label>
            <input type="text" id="dppNilaiLain" readonly
              class="w-full mt-2 bg-gray-100 border-none py-2 px-3 rounded text-gray-700 shadow-inner focus:border border-blue-500">
          </div>

          <!-- PPN -->
          <div>
            <label for="ppn" class="block text-sm font-semibold text-gray-600">PPN (12%)</label>
            <input type="text" id="ppn" readonly
              class="w-full mt-2 bg-gray-100 border-none py-2 px-3 rounded text-gray-700 shadow-inner focus:border border-blue-500">
          </div>

          <!-- Clear Button -->
          <button onclick="clearData()"
            class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
            Clear
          </button>
        </div>
      </div>

      <!-- Data NPWP -->
      <div class="w-full md:w-2/3 bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-700">Data NPWP</h2>
          <span class="text-sm text-gray-400">Informasi Pajak</span>
        </div>

        <div class="space-y-4 text-gray-700 text-sm">
          <div class="flex justify-between border-b pb-2">
            <span class="font-medium">Nama</span>
            <span class="text-right">PT Contoh Sukses</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-medium">NPWP</span>
            <span class="text-right">01.234.567.8-999.000</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-medium">Alamat</span>
            <span class="text-right text-sm">Jl. Contoh No. 88, Jakarta</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-medium">KLU</span>
            <span class="text-right">62090</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">PKP</span>
            <span class="text-right">Terdaftar</span>
          </div>
        </div>
      </div>

    </div>
  </main>


  @endsection