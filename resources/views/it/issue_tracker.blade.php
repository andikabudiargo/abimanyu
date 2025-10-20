@extends('layouts.app')

@section('title', 'Facility Issue Management')
@section('page-title', 'DASHBOARD ISSUE')
@section('breadcrumb-item', 'Helpdesk')
@section('breadcrumb-active', 'Facility Issue Management')

@section('content')
<!-- Sementara
<div class="p-6 bg-white rounded-lg shadow-md mb-4">
   
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-800 mb-2 sm:mb-0">Request Overview</h2>
      <p class="text-sm text-gray-500">
        Total <span class="font-bold text-gray-800">%</span> tickets completed this month 😎
      </p>
    </div>

  
    <div class="border-t border-gray-200 mb-4"></div>

   
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
    
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-purple-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="percent" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Total</p>
          <p class="text-xl font-bold text-gray-800"></p>
        </div>
      </div>

    
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-yellow-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="pause" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Pending</p>
          <p class="text-xl font-bold text-gray-800"></p>
        </div>
      </div>

     
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-green-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="check-circle" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Approved</p>
          <p class="text-xl font-bold text-gray-800"></p>
        </div>
      </div>

     
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-blue-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="tool" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Checked</p>
          <p class="text-xl font-bold text-gray-800"></p>
        </div>
      </div>

     
      <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition">
        <div class="p-3 bg-teal-500 text-white shadow-md rounded-lg mr-3">
          <i data-feather="coffee" class="h-5 w-5"></i>
        </div>
        <div>
          <p class="text-gray-600 text-sm">Authorized</p>
          <p class="text-xl font-bold text-gray-800"></p>
        </div>
      </div>
    </div>

  <div class="mt-6">
    <p class="text-gray-600 text-sm mb-2">Waktu rata-rata penyelesaian</p>
    <div class="w-full bg-gray-200 rounded-full h-4">
      <div class="bg-green-500 h-4 rounded-full" style="width: %;"></div>
    </div>
    <p class="text-right text-gray-500 text-xs mt-1"></p>
  </div>
</div>-->



<div class="p-6 mb-4 bg-white rounded-xl shadow">
     <h2 class="text-2xl font-bold text-gray-800 mb-6">Monthly Issue Report</h2>

   <!-- Filter -->
<form id="monthlyForm" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
        <select name="month" id="monthSelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach($months as $m => $label)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
        <select name="year" id="yearSelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
</form>

    <!-- Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Closed Issues -->
        <div class="p-6 bg-green-50 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 underline">Total Perbaikan Yang Sudah Selesai</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $closedCount }} Perbaikan</p>
                </div>
                <div class="bg-green-100 text-green-600 w-12 h-12 flex items-center justify-center rounded-full">
                    ✅
                </div>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="p-6 bg-blue-50 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 underline">Total Estimasi Biaya yang Dianggarkan</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-800">Rp {{ number_format($totalCost,0,',','.') }}</p>
                    @if($totalIssuesCount > 0) <p class="text-sm text-gray-400 mt-1">Dari {{ $totalIssuesCount }} perbaikan</p> @endif
                </div>
                <div class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-full">
                    💰
                </div>
            </div>
        </div>

        <!-- Top Category -->
        <div class="p-6 bg-purple-50 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-medium text-gray-500 underline">Paling Sering Bermasalah Bulan Ini</h3>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $topCategory ?? '-' }}</p>
            @if($topCategory)
                <p class="text-sm text-gray-400 mt-1">Bermasalah {{ $topCategoryCount }} kali</p>
            @endif
        </div>
        <div class="bg-purple-100 text-purple-600 w-12 h-12 flex items-center justify-center rounded-full">
            🏷️
        </div>
    </div>
</div>


</div>
</div>

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Request</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filter-request-number" class="block text-sm mb-1 font-medium text-gray-700">Request Number</label>
                <input id="filter-request-number" type="text" name="request_number" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
    <label for="filter-date" class="block text-sm mb-1 font-medium text-gray-700">Closed Date</label>
    <input id="filter-date" type="text" name="date"  placeholder="YYYY-MM-DD to YYYY-MM-DD" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
    
</div>

            <div>
    <label for="filter-request-type" class="block text-sm mb-1 font-medium text-gray-700">Jenis Fasilitas</label>
    <select id="filter-request-type" multiple class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Gedung">Gedung</option>
        <option value="Listrik">Listrik</option>
        <option value="Air">Air / Plumbing</option>
        <option value="AC">AC</option>
        <option value="Furniture">Furniture</option>
        <option value="Keamanan">Keamanan</option>
        <option value="Kebersihan">Kebersihan</option>
        <option value="Lainnya">Lainnya</option>
       
    </select>
</div>

<div>
    <label for="filter-urgency" class="block text-sm mb-1 font-medium text-gray-700">Urgensi</label>
    <select id="filter-urgency" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="normal">Normal / Rutin</option>
        <option value="segera">Segera</option>
        <option value="darurat">Darurat</option>
    </select>
</div>

<!-- Status -->
<div>
    <label for="filter-status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
    <select id="filter-status" class="select2 w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-min-options="5">
        <option value="">-- All --</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Checked">Checked</option>
        <option value="Verified">Verified</option>
        <option value="Authorized">Authorized</option>
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
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('it.issue.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold">Request List</h2>
    <table id="issue-table" class="w-full text-sm text-left ">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider whitespace-nowrap">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Nomor Request</th>
                    <th class="px-4 py-2">Jenis Fasilitas</th>
                    <th class="px-4 py-2">Lokasi Perbaikan</th>
                    <th class="px-4 py-2 text-center">Urgensi</th>
                    <th class="px-4 py-2 !text-center">Status</th>
                    <th class="px-4 py-2">Departemen</th>
                    <th class="px-4 py-2">Pemohon</th>
                    <th class="px-4 py-2">Tanggal Permintaan</th>
                    <th class="px-4 py-2">Disetujui Oleh</th>
                    <th class="px-4 py-2">Tanggal Disetujui</th>
                    <th class="px-4 py-2">Diperiksa Oleh</th>
                    <th class="px-4 py-2">Tanggal Diperiksa</th>
                    <th class="px-4 py-2">Diverifikasi Oleh</th>
                    <th class="px-4 py-2">Tanggal Verifikasi</th>
                    <th class="px-4 py-2">Disahkan Oleh</th>
                    <th class="px-4 py-2">Tanggal Disahkan</th>
                    <th class="px-4 py-2">Diselesaikan Oleh</th>
                    <th class="px-4 py-2">Tanggal Selesai</th>
                    <th class="px-4 py-2">Dikonfirmasi Oleh</th>
                    <th class="px-4 py-2">Tanggal Konfirmasi</th>
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
            <h2 class="text-xl font-semibold text-gray-800">Reject Request</h2>
        </div>

        <form id="rejectForm" class="space-y-4">
            @csrf
             <input type="hidden" name="request_id" id="reject_request_id">
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

<!-- Modal Pengecekan Awal -->
<div id="initialCheckModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 overflow-y-auto max-h-[70vh] max-w-3xl">
       <h3 class="text-lg font-semibold mb-4 flex items-center">
    <i data-feather="check-circle" class="w-5 h-5 mr-2"></i> Pengecekan Awal
</h3>


        <form id="initialCheckForm">
            @csrf
  <input type="hidden" name="request_id" id="checking_request_id">
            <!-- Diterima Oleh & Tanggal Pemeriksaan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diterima Oleh</label>
                    <input type="text" value="{{ Auth::user()->name }}" readonly
                           class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan</label>
                    <input type="text"value="{{ now()->translatedFormat('d F Y') }}" readonly
                           class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
                </div>
           

            <!-- Hasil Pemeriksaan -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasil Pemeriksaan</label>
                <select name="check_result" required
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Pilih Hasil --</option>
                    <option value="Internal Repair">Internal Repair</option>
                    <option value="Vendor Luar">Vendor Luar</option>
                    <option value="Ganti Material">Ganti Material</option>
                </select>
            </div>
              <!-- Durasi Pengerjaan -->
           <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Pengerjaan</label>
    <div class="relative w-full">
        <input type="number" name="duration_work" class="w-full border border-gray-300 rounded px-3 py-2 pr-12" placeholder="0">
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">HARI</span>
    </div>
</div>

             </div>
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Material</label>
    <table class="w-full border border-gray-300 rounded mb-2" id="materialTable">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">No</th>
                <th class="border px-2 py-1">Material</th>
                <th class="border px-2 py-1">Qty</th>
                <th class="border px-2 py-1">UOM</th>
                <th class="border px-2 py-1">Vendor</th>
                <th class="border px-2 py-1">Harga Satuan</th>
                <th class="border px-2 py-1">Subtotal</th>
                <th class="border px-2 py-1">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1 text-center">1</td>
                <td class="border px-2 py-1"><input type="text" name="material[]" class="w-full px-2 py-1 border rounded"></td>
                <td class="border px-2 py-1"><input type="number" name="qty[]" class="w-full px-2 py-1 border rounded qty" value="0"></td>
                <td class="border px-2 py-1"><input type="text" name="uom[]" class="w-full px-2 py-1 border rounded"></td>
                <td class="border px-2 py-1"><input type="text" name="vendor[]" class="w-full px-2 py-1 border rounded"></td>
                <td class="border px-2 py-1"><input type="number" name="price[]" class="w-full px-2 py-1 border rounded price" value="0"></td>
                <td class="border px-2 py-1 subtotal text-right">Rp. 0</td>
                <td class="border px-2 py-1 text-center">
                    <button type="button" class="removeRow text-red-600 font-bold">×</button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-semibold">
                <td colspan="6" class="text-right px-2 py-1">Estimasi Biaya</td>
                <td id="totalCost" class="text-right px-2 py-1">Rp. 0</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <button type="button" id="addRowBtn" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">+ Tambah Material</button>
</div>


          

            <!-- Rekomendasi Tindakan -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Tindakan</label>
                <textarea name="recommended_action" placeholder="Catatan Teknis GA" rows="4"
                          class="w-full px-3 py-2 border rounded"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('initialCheckModal')" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Done -->
<div id="doneModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center">
    <div class="bg-white w-full max-w-2xl p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            <i data-feather="check-square" class="inline w-5 h-5 mr-2 text-green-500"></i>
            Penyelesaian Pekerjaan (Done)
        </h2>

        <form id="doneForm" enctype="multipart/form-data">
             @csrf
            <input type="hidden" name="id" id="done_id">
            <input type="hidden" id="duration_work" value="3"><!-- Contoh estimasi 3 hari -->

            <!-- Pelaksana -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pelaksana</label>
                <input type="text" name="assigned_by" id="assigned_by"
                       class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
            </div>

            <!-- Tanggal Mulai dan Selesai -->
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="work_start" id="work_start"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="work_end" id="work_end"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>

            <!-- Durasi -->
            <div id="durationInfo" class="text-sm text-gray-600 mb-3 hidden"></div>

            <!-- Catatan -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Hasil Pekerjaan</label>
                <textarea name="note_done" id="note_done"
                          class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"></textarea>
            </div>

            <!-- Upload Foto -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Before</label>
                    <input type="file" name="evidence_before" id="evidence_before" accept="image/*"
                           class="w-full border rounded-lg px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto After</label>
                    <input type="file" name="evidence_after" id="evidence_after" accept="image/*"
                           class="w-full border rounded-lg px-2 py-1">
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="closeDoneModal"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Batal</button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal -->
   

<!-- Modal Closed -->
<div id="closedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg w-11/12 md:w-2/3 lg:w-1/2 p-6 overflow-y-auto max-h-[80vh]">
        <div class="mb-6">
 <div class="flex items-center gap-2">
   <span class="text-2xl">😊</span>
  <h2 class="text-2xl font-bold text-gray-900">Feedback Pengerjaan</h2>
</div>
<p class="text-xs text-gray-500 mt-1">
  Mohon berikan penilaian terhadap hasil pengerjaan agar kami dapat terus meningkatkan kualitas layanan.
</p>
    <div class="w-24 h-1 bg-teal-600 rounded mt-2"></div>
  </div>

        <form id="closedForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="request_id" id="closed_request_id">

            <!-- Radio Verification -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Verifikasi Hasil</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="work_verification" value="Sesuai" class="form-radio" required>
                        <span class="ml-2">Sesuai</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="work_verification" value="Tidak Sesuai" class="form-radio" required>
                        <span class="ml-2">Tidak Sesuai</span>
                    </label>
                </div>
            </div>

           <!-- Persetujuan Pemohon -->
<div class="mb-4">
  <label class="block text-sm font-medium text-gray-700 mb-2">
    Setuju dengan hasil perbaikan?
  </label>
  <div class="flex gap-6">
    <label class="inline-flex items-center">
      <input type="radio" name="confirmation" value="1" class="form-radio text-green-600" required>
      <span class="ml-2 text-gray-700">Ya</span>
    </label>
    <label class="inline-flex items-center">
      <input type="radio" name="confirmation" value="0" class="form-radio text-red-600" required>
      <span class="ml-2 text-gray-700">Tidak</span>
    </label>
  </div>
</div>

           <!-- Rating Bintang -->
<!-- Rating & Feedback -->
<div class="mb-6">
    <!-- Header -->
    <h3 class="text-lg font-semibold flex items-center gap-2 mb-2">
        <span>Apakah Anda puas dengan kinerja kami?</span>
        <span class="text-yellow-400 text-xl">⭐</span>
    </h3>

    <!-- Durasi pengerjaan -->
    <p class="text-sm text-gray-600 mb-3">
        Durasi pengerjaan: 
        <span id="workDuration">
            <!-- JS akan menghitung dari work_start & work_end -->
        </span>
    </p>
    <div id="starRating" class="flex gap-3 cursor-pointer text-yellow-400 relative justify-start">
        <div class="relative group">
            <i class="star w-12 h-12" data-value="1" data-label="Tidak Puas" data-feather="star"></i>
            <span class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs px-2 py-1 rounded pointer-events-none">
                Tidak Puas
            </span>
        </div>
        <div class="relative group">
            <i class="star w-12 h-12" data-value="2" data-label="Kurang Puas" data-feather="star"></i>
            <span class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs px-2 py-1 rounded pointer-events-none">
                Kurang Puas
            </span>
        </div>
        <div class="relative group">
            <i class="star w-12 h-12" data-value="3" data-label="Cukup Puas" data-feather="star"></i>
            <span class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs px-2 py-1 rounded pointer-events-none">
                Cukup Puas
            </span>
        </div>
        <div class="relative group">
            <i class="star w-12 h-12" data-value="4" data-label="Puas" data-feather="star"></i>
            <span class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs px-2 py-1 rounded pointer-events-none">
                Puas
            </span>
        </div>
        <div class="relative group">
            <i class="star w-12 h-12" data-value="5" data-label="Sangat Puas" data-feather="star"></i>
            <span class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs px-2 py-1 rounded pointer-events-none">
                Sangat Puas
            </span>
        </div>
    </div>
    <input type="hidden" name="rating" id="ratingValue" required>
</div>
            <!-- Catatan Evaluasi -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Evaluasi</label>
                <textarea name="feedback" rows="4" placeholder="Masukkan catatan evaluasi"
                          class="w-full px-3 py-2 border rounded"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('closedModal')" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#issue-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#issue-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Non-Tailwind CSS */
#issue-table td,
#issue-table th {
    white-space: nowrap;
}

/* Pastikan pembungkus utama flex */
.mobile-flex-wrapper {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem; /* jarak antar elemen */
}

/* Search filter tetap auto width */
.dataTables_filter {
  width: auto !important;
  display: flex !important;
  align-items: center;
  flex-wrap: nowrap;
}

/* 🎯 Fine-tuning posisi sejajar Search dan Export */
@media (max-width: 768px) {
  .dataTables_filter {
    align-items: center !important;
  }

  .dataTables_filter input {
    height: 38px !important;
    margin-top: 2px; /* sedikit naik agar sejajar */
  }

  .dt-buttons .dt-button {
     height: 38px !important;
  line-height: 38px;
    padding-top: 0;
    padding-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

/* 🔍 Extra tuning khusus layar sempit banget (≤414px, iPhone XR/SE) */
@media (max-width: 414px) {
  .dataTables_filter input {
    max-width: 120px;
  }

  .dt-buttons .dt-button {
    font-size: 0.8rem;
    padding: 0.35rem 0.75rem;
  }
}


/* Input search */
.dataTables_filter input {
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 6px 10px;
  margin-left: 10px;
  width: 150px; /* sesuaikan */
   margin-top: 2px; /* sedikit naik agar sejajar */
}

/* Tombol export */
.dt-buttons {
  display: flex !important;
  align-items: center;
  width: auto !important;
  position: relative;
  z-index: 1;
  margin-left: 10px;
}

/* 📱 Mobile adjustment */
@media (max-width: 768px) {
  .mobile-flex-wrapper {
    flex-wrap: nowrap; /* biar sejajar */
    justify-content: space-between;
  }

  .dataTables_filter,
  .dt-buttons {
    flex: 1 1 auto;
    display: flex !important;
    width: auto !important;
  }

  /* Ratakan tinggi dan sejajarkan posisi vertikal */
.dataTables_filter label {
  display: flex;
  align-items: center; /* ini penting agar sejajar vertikal */
  margin-bottom: 0 !important; /* hilangkan margin default */
}

.dataTables_filter input {
  height: 38px; /* samakan tinggi dengan tombol Export */
  margin: 0 0 0 8px; /* jarak kiri sedikit */
  line-height: 1.2;
}

.dt-buttons .dt-button {
  height: 38px; /* samakan tinggi dengan input */
  display: flex;
  align-items: center;
  justify-content: center;
}

  .dataTables_filter label span {
    display: none; /* hilangkan teks Search */
  }

  .dt-buttons {
    justify-content: flex-end;
    margin-left: 0;
  }
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

    /* Pagination DataTables modern */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 9999px; /* lingkaran penuh */
    border: 1px solid transparent;
    min-width: 2.5rem;
    min-height: 2.5rem;
    padding: 0.25rem 0;
    margin: 0 0.25rem;
    text-align: center;
    line-height: 2rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #3b82f6; /* Tailwind blue-500 */
    color: white !important;
    border-color: #2563eb; /* blue-600 */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #2563eb; /* active biru gelap */
    color: white !important;
    border-color: #2563eb;
}


</style>
<script>
    // Tambah Row Material
$('#addRowBtn').on('click', function() {
    let tableBody = $('#materialTable tbody');
    let rowCount = tableBody.children().length + 1;
    let newRow = `<tr>
        <td class="border px-2 py-1 text-center">${rowCount}</td>
        <td class="border px-2 py-1"><input type="text" name="material[]" class="w-full px-2 py-1 border rounded"></td>
        <td class="border px-2 py-1"><input type="number" name="qty[]" class="w-full px-2 py-1 border rounded qty" value="0"></td>
        <td class="border px-2 py-1"><input type="text" name="uom[]" class="w-full px-2 py-1 border rounded"></td>
        <td class="border px-2 py-1"><input type="text" name="vendor[]" class="w-full px-2 py-1 border rounded"></td>
        <td class="border px-2 py-1"><input type="number" name="price[]" class="w-full px-2 py-1 border rounded price" value="0"></td>
        <td class="border px-2 py-1 subtotal text-right">Rp 0</td>
        <td class="border px-2 py-1 text-center">
            <button type="button" class="removeRow text-red-600 font-bold">×</button>
        </td>
    </tr>`;
    tableBody.append(newRow);
});

// Hapus baris
$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
    updateRowNumbers();
    calculateTotal();
});

// Update subtotal & total cost
$(document).on('input', '.qty, .price', function() {
    let row = $(this).closest('tr');
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let price = parseFloat(row.find('.price').val()) || 0;
    let subtotal = qty * price;
    row.find('.subtotal').text(formatRupiah(subtotal));
    calculateTotal();
});

// Hitung total keseluruhan
function calculateTotal() {
    let total = 0;
    $('#materialTable tbody tr').each(function() {
        let subtotalText = $(this).find('.subtotal').text().replace(/[^0-9,-]/g, '');
        let subtotal = parseFloat(subtotalText) || 0;
        total += subtotal;
    });
    $('#totalCost').text(formatRupiah(total));
}

// Update nomor urut baris
function updateRowNumbers() {
    $('#materialTable tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

// Format angka ke Rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

  const $checkResult = $('select[name="check_result"]');
    const $materialSection = $('#materialTable').closest('div.mb-4'); // ambil wrapper div tabel

    function toggleMaterialTable() {
        if ($checkResult.val() === 'Internal Repair') {
            $materialSection.hide();
        } else {
            $materialSection.show();
        }
    }

    // Jalankan saat pertama kali halaman load (kalau edit data)
    toggleMaterialTable();

    // Jalankan setiap kali dropdown berubah
    $checkResult.on('change', toggleMaterialTable);

    function closeModal(id) {
        $('#' + id).addClass('hidden');
    }
function openCheckingModal(requestId) {
    // Set id request ke input hidden
    document.getElementById('checking_request_id').value = requestId;

    // Tampilkan modal
    document.getElementById('initialCheckModal').classList.remove('hidden');
}

function showCloseModal(requestId) {
    console.log('Modal Closed dibuka untuk ID:', requestId);
    $('#closed_request_id').val(requestId);
    $('#closedModal').removeClass('hidden').show(); // 👈 tambahkan .show() supaya pasti tampil
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

 $(document).ready(function () {
    feather.replace(); // render icons

    let rating = 0;

    const stars = $('#starRating .star');

    // Highlight stars function
    function highlightStars(value) {
        stars.each(function () {
            const starVal = $(this).data('value');
            if (starVal <= value) {
                $(this).addClass('fill-current text-yellow-400').removeClass('text-gray-300');
            } else {
                $(this).removeClass('fill-current text-yellow-400').addClass('text-gray-300');
            }
        });
    }

    // Hover effect
    stars.on('mouseenter', function () {
        const value = $(this).data('value');
        highlightStars(value);
    });

    $('#starRating').on('mouseleave', function () {
        highlightStars(rating);
    });

    // Click to set rating
    stars.on('click', function () {
        rating = $(this).data('value');
        $('#ratingValue').val(rating);
        highlightStars(rating);
    });

    // Tooltip hover
    $('#starRating .group').hover(
        function () {
            $(this).find('.tooltip').removeClass('hidden');
        },
        function () {
            $(this).find('.tooltip').addClass('hidden');
        }
    );

    // Set awal semua abu
    highlightStars(0);
});

flatpickr("#due_date", {
    enableTime: true,
    noCalendar: false,
    dateFormat: "Y-m-d H:i",
    time_24hr: true
});

function rejectRequest(ticketId) {
    $('#reject_request_id').val(ticketId);
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
    let requestId = $('#reject_request_id').val();
    let data = form.serialize();

    $.post(`/it/issue-tracker/${requestId}/reject`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
            $('#issue-table').DataTable().ajax.reload(null, false);
            closeRejectModal();
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
    });
});

$('#initialCheckForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let id = $('#checking_request_id').val(); // ambil ID dari hidden input
    let url = '/it/issue-tracker/' + id + '/checking'; // sesuaikan route sesuai controller kamu
    let data = form.serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message);

                // Reload tabel jika pakai DataTable
                if ($('#issue-table').length) {
                    $('#issue-table').DataTable().ajax.reload(null, false);
                }

                // Tutup modal & reset form
                closeModal('initialCheckModal');
                form[0].reset();
            } else {
                showToast('error', res.message || 'Gagal menyimpan data.');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast('error', 'Terjadi kesalahan saat menyimpan pengecekan awal.');
        }
    });
});


// Buka modal dan isi ID
    window.openDoneModal = function (id) {
        $('#done_id').val(id);
        $('#doneModal').removeClass('hidden').hide().fadeIn(200);
        feather.replace();
    };

    // Tutup modal
    $('#closeDoneModal').on('click', function () {
        $('#doneModal').fadeOut(200, function () {
            $(this).addClass('hidden');
            $('#doneForm')[0].reset();
            $('#durationInfo').addClass('hidden').text('');
        });
    });

    // Hitung durasi otomatis
    $('#start_date, #end_date').on('change', function () {
        let start = new Date($('#start_date').val());
        let end = new Date($('#end_date').val());
        let estimasi = parseInt($('#duration_work').val());
        if (isNaN(start) || isNaN(end)) return;

        let durasi = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        if (durasi > 0) {
            $('#durationInfo')
                .removeClass('hidden')
                .text(`Durasi kerja: ${durasi} hari.`);

            if (durasi > estimasi) {
                $('#durationInfo')
                    .append(` ⚠️ <span class="text-red-600 font-semibold">Melebihi estimasi ${estimasi} hari!</span>`);
            }
        } else {
            $('#durationInfo').removeClass('hidden').text('Tanggal selesai harus setelah tanggal mulai.');
        }
    });

    // Submit form
    $('#doneForm').on('submit', function (e) {
        e.preventDefault();

       let form = $(this);
       let id = $('#done_id').val(); // ambil ID dari hidden input
       let url = '/it/issue-tracker/' + id + '/done'; // sesuaikan route sesuai controller kamu
       let data = form.serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message);

                // Reload tabel jika pakai DataTable
                if ($('#issue-table').length) {
                    $('#issue-table').DataTable().ajax.reload(null, false);
                }

                // Tutup modal & reset form
                closeModal('doneForm');
                form[0].reset();
            } else {
                showToast('error', res.message || 'Gagal menyimpan data.');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast('error', 'Terjadi kesalahan saat menyimpan pengecekan awal.');
        }
    });
});


 // Submit form
    $('#closedForm').on('submit', function (e) {
        e.preventDefault();

       let form = $(this);
       let id = $('#closed_id').val(); // ambil ID dari hidden input
       let url = '/it/issue-tracker/' + id + '/closed'; // sesuaikan route sesuai controller kamu
       let data = form.serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function (res) {
            if (res.success) {
                showToast('success', res.message);

                // Reload tabel jika pakai DataTable
                if ($('#issue-table').length) {
                    $('#issue-table').DataTable().ajax.reload(null, false);
                }

                // Tutup modal & reset form
                closeModal('closedForm');
                form[0].reset();
            } else {
                showToast('error', res.message || 'Gagal menyimpan data.');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast('error', 'Terjadi kesalahan saat menyimpan pengecekan awal.');
        }
    });
});


// ========== ACTIONS ========== //

function approveRequest(id, requestNumber) {

    Swal.fire({
        title: 'Approve Request?',
        html: `Approve this Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/issue-tracker/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Request berhasil disetujui: ' + res.request_number);
                 $('#issue-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui request.');
            });
        }
    });
}

function verificationRequest(id, requestNumber) {

    Swal.fire({
        title: 'Verify Request?',
        html: `Verify this Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verified!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/issue-tracker/${id}/verification`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Request berhasil diverifikasi: ' + res.request_number);
                 $('#issue-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui request.');
            });
        }
    });
}

function authorizedRequest(id, requestNumber) {

    Swal.fire({
        title: 'Authorize Request?',
        html: `Authorize this Request: <strong>${requestNumber} </strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Authorize It!',
        cancelButtonText: 'Cancel'
     }).then(result => {
        if (result.isConfirmed) {
            $.post(`/it/issue-tracker/${id}/authorized`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                // ✅ res tersedia di sini
                showToast('success', 'Request berhasil disetujui untuk dijalankan: ' + res.request_number);
                 $('#issue-table').DataTable().ajax.reload(null, false);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui request.');
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
                url: `/it/issue-tracker/${id}/destroy`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.fire('Dihapus!', res.message, 'success');
                    $('#issue-table').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal!', 'Tidak dapat menghapus request.', 'error');
                }
            });
        }
    });
}

let today = new Date().toISOString().slice(0, 10); // Hasil: "2025-07-21"
 $(document).ready(function () {
    const table = $('#issue-table').DataTable({
        processing: true,
         responsive: true,
        serverSide: true,
        autoWidth: true,
        scrollX:true,
         xhrFields: {
            withCredentials: true  // penting supaya cookie session terkirim
        },
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
    
},

       ajax: {
            url: '{{ route("it.issue.data") }}',
            data: function (d) {
                d.request_number = $('#filter-request-number').val();
                d.status = $('#filter-status').val();
                d.urgency = $('#filter-urgency').val();
                d.request_type = $('#filter-request-type').val();
                d.date = $('#filter-date').val();
                d.department = $('#filter-department').val();
            }
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: `
    <'flex flex-col md:flex-row justify-between items-center mb-4'
        <'flex flex-wrap items-center space-x-2 mb-2 md:mb-0'l>
        <'flex flex-wrap items-center space-x-2'f B>
    >
    rt
    <'flex flex-col md:flex-row justify-between items-center mt-4'
        <'text-sm text-gray-500 mb-2 md:mb-0'i>
        <'flex flex-wrap items-center space-x-2'p>
    >
`,

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
                filename: 'GA_Request_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                text: '<i class="fas fa-file-excel mr-2 text-green-600"></i>Excel',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6,7,8,9,10,11,12,13,14,15,16,17,18,19,20] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                }
            },
            {
                extend: 'pdfHtml5',
                filename: 'GA_Request_' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx
                title: null,
                pageSize: 'A4',
                text: '<i class="fas fa-file-pdf mr-2 text-red-600"></i>PDF',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function(doc) {
        // Ubah font seluruh tabel
        doc.styles.tableHeader.fontSize = 10;  // header tabel
        doc.defaultStyle.fontSize = 8;        // isi tabel
    }
            },
            {
                extend: 'print',
                title: 'GA Request ' + today, // hasil: Laporan_Departemen_2025-07-21.xlsx ,
                text: '<i class="fas fa-print mr-2"></i>Print',
                exportOptions: {
                columns: [1, 2, 3, 4, 5, 6] // kolom yang akan diexport (tanpa kolom ke-5, yaitu Action)
                },
                 customize: function (win) {
        // Kecilkan font tabel
        $(win.document.body).css('font-size', '10px');

        
    }
            }
        ]
    },

    
    
],

language: {
            paginate: {
                previous: '←',
                next: '→'
            }
        },

        
      columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'request_number', name: 'request_number',  orderable: false },
        { data: 'request_type', name: 'request_type', orderable: false },
        { data: 'location_area', name: 'location_area', orderable: false },
        { data: 'urgency', name: 'urgency',  className: 'text-center', orderable: false },
        { data: 'status', name: 'status', className: 'text-center', orderable: false },
        { data: 'department', name: 'creator.departments.name', orderable: false }, // relasi pivot
        { data: 'request_by', name: 'creator.name',orderable: false },
        { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false },
        { data: 'approved_by', name: 'approved.name', orderable: false },
        { data: 'approved_at', name: 'approved_at',  className: 'text-center', orderable: false },
        { data: 'checked_by', name: 'checked_by.name', orderable: false },
        { data: 'checked_at', name: 'checked_at',  className: 'text-center', orderable: false },
        { data: 'verification_by', name: 'verification_by',  className: 'text-center', orderable: false },
        { data: 'verification_at', name: 'verification_at',  className: 'text-center', orderable: false },
        { data: 'authorized_by', name: 'authorized_by',  className: 'text-center', orderable: false },
        { data: 'authorized_at', name: 'authorized_at',  className: 'text-center', orderable: false },
        { data: 'done_by', name: 'done_by',  className: 'text-center', orderable: false },
        { data: 'done_at', name: 'done_at',  className: 'text-center', orderable: false },
        { data: 'closed_by', name: 'closed_by',  className: 'text-center', orderable: false },
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
    $('#filter-request-type').select2({
    placeholder: "-- Pilih Jenis Fasilitas --",
    allowClear: true,  // tombol clear
    width: '100%'
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


 // Ambil form dan select
    const form = document.getElementById('monthlyForm');
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect  = document.getElementById('yearSelect');

    // Submit otomatis saat value berubah
    monthSelect.addEventListener('change', () => form.submit());
    yearSelect.addEventListener('change', () => form.submit());

  // Inisialisasi Flatpickr
flatpickr("#filter-date", {
    mode: "range",
    dateFormat: "Y-m-d"
});

// Event submit form filter
$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    $('#issue-table').DataTable().ajax.reload();
});


 // Contoh JS untuk menghitung durasi dari work_start dan work_end
    const workStart = new Date("2025-10-17T08:00:00"); // ganti dengan nilai dari DB
    const workEnd   = new Date("2025-10-17T12:30:00"); // ganti dengan nilai dari DB

    const diffMs = workEnd - workStart; // milidetik
    const diffHrs = Math.floor(diffMs / (1000*60*60));
    const diffMins = Math.floor((diffMs % (1000*60*60)) / (1000*60));

    document.getElementById('workDuration').textContent = `${diffHrs} jam ${diffMins} menit`;


  </script>
@endpush


@endsection