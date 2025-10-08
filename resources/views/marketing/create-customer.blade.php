@extends('layouts.app')

@section('title', 'Add New Customer')
@section('page-title', 'Add New Customer')
@section('breadcrumb-item', 'Customer Management')
@section('breadcrumb-active', 'Create Customer')

@section('content')
<!--<div class="w-full bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Upload Multiple Supplier</h2>
    @if(session('success'))
    <div class="text-green-600 mt-2">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="text-red-600 mt-2">{{ session('error') }}</div>
@endif

    <div class="bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-6">
    <h3 class="text-lg font-medium mb-2">Upload via Excel (.xlsx)</h3>
    <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4">
         @csrf
      <input type="file" name="csv_file" accept=".xlsx" class="flex-1 border border-gray-300 rounded p-2">
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Upload</button>
    </form>
    <p class="text-sm text-gray-500 mt-2">
      Download template Excel: <a href="" class="text-blue-600 underline" download>Download Template</a>
    </p>
  </div>
</div>-->

<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-6">
  <!-- 🔝 Step Navigation -->
  <div class="flex justify-between items-center border-b pb-2 text-sm font-medium">
    <!-- Step 1 -->
    <button type="button" 
      class="step-tab flex flex-col items-center sm:flex-row sm:space-x-2 px-4 py-2 border-b-2 border-indigo-600 text-indigo-600 transition duration-300 ease-in-out"
      data-step="0">
      <i class="fas fa-info-circle text-lg"></i>
      <span class="hidden sm:inline">Base Information</span>
    </button>

    <!-- Step 2 -->
    <button type="button" 
      class="step-tab flex flex-col items-center sm:flex-row sm:space-x-2 px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition duration-300 ease-in-out"
      data-step="1">
      <i class="fas fa-map-marker-alt text-lg"></i>
      <span class="hidden sm:inline">Address</span>
    </button>

    <!-- Step 3 -->
    <button type="button" 
      class="step-tab flex flex-col items-center sm:flex-row sm:space-x-2 px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition duration-300 ease-in-out"
      data-step="2">
      <i class="fas fa-phone text-lg"></i>
      <span class="hidden sm:inline">Contact</span>
    </button>

    <!-- Step 4 -->
    <button type="button" 
      class="step-tab flex flex-col items-center sm:flex-row sm:space-x-2 px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition duration-300 ease-in-out"
      data-step="3">
      <i class="fas fa-credit-card text-lg"></i>
      <span class="hidden sm:inline">Payment & Terms</span>
    </button>
  </div>


    <form id="supplier-form" action="" method="POST">
    @csrf
      <!-- 🔢 Nomor Referensi -->
        <!-- STEP 1 -->
    <div class="step">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Name <small class="text-red-600">*</small></label>
        <input type="text" name="supplier_name" id="supplier_name"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
        <label for="initial" class="block text-sm font-medium text-gray-700 mb-1">Initial <small class="text-red-600">*</small></label>
        <input type="text" name="initial" id="initial"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div class="flex items-center h-full pt-6">
        <input type="checkbox" name="as_customer" value="1" checked class="form-checkbox text-indigo-600 mr-2">
        <label for="as_customer" class="text-sm text-gray-700">EPTE</label>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
           <div>
        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category <small class="text-red-600">*</small></label>
       <select name="category" id="category"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Category --</option>
            <option value="Raw Material">Raw Material</option>
            <option value="Chemical">Chemical</option>
            <option value="Consumable">Consumable</option>
            <option value="Other">Other</option>
          </select>
        </div>
          <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
        <input type="date" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>
         <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="coa_hutang" class="block text-sm font-medium text-gray-700 mb-1">Account Receivable</label>
       <select name="coa_hutang" id="coa_hutang"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Account Receivable --</option>
          </select>
        </div>
        <div>
        <label for="coa_retur" class="block text-sm font-medium text-gray-700 mb-1">Account Sales Revenue</label>
       <select name="coa_retur" id="coa_retur"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Account Sales Revenue --</option>
            <option value="Retur">Retur</option>
            <option value="Transit">Transit</option>
          </select>
        </div>
        </div>
    </div>


         <!-- STEP 2 -->
    <div class="step hidden">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Billing Address 1</label>
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
       <select name="provinsi" id="provinsi"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Provinsi --</option>
          </select>
        </div>
        <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
       <select name="city" id="city"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kota/Kabupaten --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <div>
        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
       <select name="kecamatan" id="kecamatan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div>
        <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
       <select name="kelurahan" id="kelurahan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kelurahan --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div class="col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Billing Address 2</label>
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
       <select name="provinsi" id="provinsi"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Provinsi --</option>
          </select>
        </div>
        <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
       <select name="city" id="city"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kota/Kabupaten --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <div>
        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
       <select name="kecamatan" id="kecamatan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div>
        <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
       <select name="kelurahan" id="kelurahan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kelurahan --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div class="col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Delivery Address 1</label>
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
       <select name="provinsi" id="provinsi"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Provinsi --</option>
          </select>
        </div>
        <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
       <select name="city" id="city"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kota/Kabupaten --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <div>
        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
       <select name="kecamatan" id="kecamatan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div>
        <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
       <select name="kelurahan" id="kelurahan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kelurahan --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div class="col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Delivery Address 2</label>
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
       <select name="provinsi" id="provinsi"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Provinsi --</option>
          </select>
        </div>
        <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
       <select name="city" id="city"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kota/Kabupaten --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <div>
        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
       <select name="kecamatan" id="kecamatan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kecamatan --</option>
          </select>
        </div>
        <div>
        <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
       <select name="kelurahan" id="kelurahan"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih Kelurahan --</option>
          </select>
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
        <input type="text" name="postal_code" id="postal_code"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
      </div>
    </div>

       <!-- STEP 3 -->
    <div class="step hidden">
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
        <input type="text" name="contact_person" id="contact_person"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
        <input type="text" name="telephone" id="telephone"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
        <label for="mobile_phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile Phone</label>
        <input type="text" name="mobile_phone" id="mobile_phone"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <div>
        <label for="fax" class="block text-sm font-medium text-gray-700 mb-1">Fax</label>
        <input type="text" name="fax" id="fax"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="text" name="email" id="email"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>
    </div>

         <!-- STEP 4 -->
    <div class="step hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="npwp_number" class="block text-sm font-medium text-gray-700 mb-1">Credit Limit</label>
        <input type="text" name="npwp_number" id="npwp_number"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
        <label for="npwp_name" class="block text-sm font-medium text-gray-700 mb-1">Credit Term</label>
        <input type="text" name="npwp_name" id="npwp_name"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
</div>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div>
        <label for="bank_type" class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
       <select name="bank_type" id="bank_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Payment Term --</option>
            <option value="Retur">Cash-on-Delivery (COD)</option>
            <option value="Transit">Cash Before Delivery (CBD)</option>
             <option value="LC">NET</option>
            <option value="Transit">Installment/Partiall</option>
             <option value="LC">Letter of Credit (LC)</option>
          </select>
        </div>
        <div>
        <label for="bank_type" class="block text-sm font-medium text-gray-700 mb-1">Delivery Terms</label>
       <select name="bank_type" id="bank_type"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Delivery Term --</option>
            <option value="Retur">Free on Board (FOB)</option>
            <option value="Transit">Cost, Insurance, and Freight (CIF)</option>
            <option value="Transit">Ex Works (EXW)</option>
            <option value="Transit">Delivered At Place (DAP)</option>
          </select>
        </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
  <label for="top" class="block text-sm font-medium text-gray-700 mb-1">Term of Payment (TOP) 1</label>
  <div class="relative">
    <input type="number" name="top" id="top"
           class="w-full px-3 py-2 pr-16 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
      Days
    </div>
  </div>
        </div>
   <div>
  <label for="top" class="block text-sm font-medium text-gray-700 mb-1">Term of Payment (TOP) 2</label>
  <div class="relative">
    <input type="number" name="top" id="top"
           class="w-full px-3 py-2 pr-16 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
      Days
    </div>
  </div>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="npwp_number" class="block text-sm font-medium text-gray-700 mb-1">NPWP Number</label>
        <input type="text" name="npwp_number" id="npwp_number"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
        <label for="npwp_name" class="block text-sm font-medium text-gray-700 mb-1">NPWP Name</label>
        <input type="text" name="npwp_name" id="npwp_name"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="col-span-2">
        <label for="npwp_address" class="block text-sm font-medium text-gray-700">NPWP Address</label>
        <textarea id="npwp_address" name="npwp_address" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
      </div>
        <div class="col-span-2">
        <label for="bank_type" class="block text-sm font-medium text-gray-700 mb-1">NPPKP Number</label>
       <input type="text" name="branch" id="branch"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
</div>
    </div>


       <!-- 🔘 Navigation Buttons -->
    <div class="flex justify-between items-center border-t pt-5 gap-2 mt-4">
      <button type="button" id="prevBtn"
        class="hidden w-24 bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Previous</button>
      <button type="button" id="nextBtn"
        class="w-24 bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700">Next</button>
      <button type="submit" id="submitBtn"
        class="hidden w-24 bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
    </div>
  </form>
</div>
</div>

<script>
  let currentStep = 0;
  const steps = document.querySelectorAll(".step");
  const tabs = document.querySelectorAll(".step-tab");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const submitBtn = document.getElementById("submitBtn");

  function showStep(n) {
    steps.forEach((step, i) => step.classList.toggle("hidden", i !== n));
    tabs.forEach((tab, i) => {
      tab.classList.toggle("text-indigo-600", i === n);
      tab.classList.toggle("border-indigo-600", i === n);
      tab.classList.toggle("text-gray-500", i !== n);
      tab.classList.toggle("border-transparent", i !== n);
    });

    prevBtn.classList.toggle("hidden", n === 0);
    nextBtn.classList.toggle("hidden", n === steps.length - 1);
    submitBtn.classList.toggle("hidden", n !== steps.length - 1);
  }

  prevBtn.addEventListener("click", () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  nextBtn.addEventListener("click", () => {
    if (currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  });

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      currentStep = parseInt(tab.dataset.step);
      showStep(currentStep);
    });
  });

  showStep(currentStep);
</script>

@endsection