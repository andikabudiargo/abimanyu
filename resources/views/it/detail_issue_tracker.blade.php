@extends('layouts.app')

@section('title', 'Detail Request')
@section('page-title', 'Detail Request')
@section('breadcrumb-item', 'Ticket')
@section('breadcrumb-active', 'Detail Ticket')

@section('content')

@php
    $status = $request->status;
    $statusColor = match($status) {
        'Pending' => 'bg-gray-100 text-gray-800',
        'Rejected' => 'bg-red-500 text-gray-100',
        'Approved' => 'bg-yellow-500 text-gray-100',
        'Checked' => 'bg-orange-500 text-gray-100',
        'Verified' => 'bg-blue-500 text-gray-100',
        'Authorized' => 'bg-purple-500 text-gray-100',
        'Done' => 'bg-green-500 text-gray-100',
        'Closed' => 'bg-teal-500 text-gray-100',
        default => 'bg-blue-100 text-blue-800',
    };

     $urgency = ucfirst($request->urgency); // Huruf pertama kapital
    $urgencyColor = match(strtolower($request->urgency)) {
        'normal' => ' text-green-500',
        'segera' => ' text-yellow-500',
        'darurat' => ' text-red-500',
        default => 'text-gray-800',
    };
@endphp

<div class="w-full bg-white shadow-md rounded-xl p-4 md:p-6 space-y-4 mb-2">
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
    <!-- Title + Status -->
    <div class="order-2 md:order-1 w-full md:max-w-[70%]">
      <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 uppercase flex flex-wrap items-center gap-2 break-words">
         #{{ $request->request_number }}
        <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $statusColor }}">
    {{ $status }}
</span>
      </h1>
    </div>

</div>



 <div class="flex flex-row text-sm text-gray-600 mb-6 gap-3">
    <span class="inline-flex items-center">
        <i data-feather="user" class="w-4 h-4 mr-1"></i> {{ $request->creator->name }}
    </span>
    <span class="inline-flex items-center">
        <i data-feather="calendar" class="w-4 h-4 mr-1"></i> {{ $request->created_at }}
    </span>
</div>

<hr class=" border border-gray-600">

<div class="flex flex-col md:flex-row gap-6 mb-2">
  <!-- MAIN CONTENT: PO Info + Items -->
  <div class="w-full md:w-2/3 border border-gray-200 bg-white shadow-md rounded-xl p-4 md:p-6">
    
    <!-- Purchase Order Information -->

       <div class="flex flex-row flex-wrap justify-between items-center mb-6 md:mb-8">
    <h3 class="text-lg md:text-xl font-semibold text-gray-700">
        Informasi Dasar
    </h3>
   
   <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $urgencyColor }}">
    {{ $urgency }}
</span>
</div>


       <div class="text-sm mb-6 md:mb-8">
        <div class="text-sm mb-6 md:mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <div class="text-gray-500 font-medium mb-1">Lokasi Area Perbaikan</div>
        <div class="text-gray-800 uppercase">{{ $request->location_area }}</div>
    </div>

    <div>
        <div class="text-gray-500 font-medium mb-1">Jenis Fasilitas / Area</div>
        <div class="text-gray-800 uppercase">{{ $request->request_type }}</div>
    </div>
</div>


        <div class="text-gray-500 font-medium mb-2">Lampiran Bukti Kerusakan</div>

@if($request->attachment)
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-gray-100 p-3 rounded shadow-sm mb-4">
        <div class="mb-2 md:mb-0">
            <p class="text-sm font-medium text-gray-800">{{ basename($request->attachment) }}</p>
            <p class="text-xs text-gray-500">{{ $request->attachment }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ asset($request->attachment) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                <i data-feather="eye" class="w-4 h-4 mr-1"></i> Watch
            </a>
            <a href="{{ asset($request->attachment) }}" download class="inline-flex items-center text-green-600 hover:underline">
                <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
            </a>
        </div>
    </div>
@else
    <p class="text-gray-500 italic">No Attachment</p>
@endif

       
      </div>

       <h4 class="text-gray-700 font-semibold mb-2">Description Issue</h4>
       <div class="border border-gray-400 rounded-lg shadow-md p-6 mb-6">
   <!-- Description -->
<div class="flex items-center gap-3 mb-4">
    <span class="text-green-500 text-sm">📝</span>
    <div>
        <h3 class="text-sm font-semibold">Deskripsi Kerusakan / Kebutuhan</h3>
        <p class="text-gray-700 mt-1">{{ $request->description }}</p>
    </div>
</div>

<!-- Recommendation -->
<div class="flex items-center gap-3">
    <span class="text-yellow-500 text-sm">💡</span>
    <div>
        <h3 class="text-sm font-semibold">Rekomendasi / Saran Pemohon</h3>
        <p class="text-gray-700 mt-1">{{ $request->recommendation }}</p>
    </div>
</div>

</div>

<!-- NAVBAR (horizontal, hover teal) -->
<nav class="mt-4 flex justify-start space-x-8 p-2 border-b border-gray-200">
  <!-- Pemeriksaan -->
  <a href="#pengecekan" class="group flex items-center space-x-2 text-gray-600 transition-colors relative pb-2">
    <i data-feather="check-circle" class="w-5 h-5 transition-colors group-hover:text-teal-600"></i>
    <span class="text-sm font-medium transition-colors group-hover:text-teal-600 hidden md:inline">Pemeriksaan Awal</span>
    <span class="absolute bottom-0 left-0 w-0 h-[2px] bg-teal-600 transition-all duration-300 group-hover:w-full"></span>
  </a>

  <!-- Hasil -->
  <a href="#pelaksanaan" class="group flex items-center space-x-2 text-gray-600 transition-colors relative pb-2">
    <i data-feather="file-text" class="w-5 h-5 transition-colors group-hover:text-teal-600"></i>
    <span class="text-sm font-medium transition-colors group-hover:text-teal-600 hidden md:inline">Pelaksanaan Pekerjaan</span>
    <span class="absolute bottom-0 left-0 w-0 h-[2px] bg-teal-600 transition-all duration-300 group-hover:w-full"></span>
  </a>
</nav>




<div id="pemeriksaan">
    @if(in_array($request->status, ['Pending', 'Approved', 'Rejected']))
        <div class="mt-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 font-semibold rounded-md">
            ⚠ Belum Dilakukan Pemeriksaan
        </div>
    @else
        <div class="text-sm mb-6 md:mb-8 mt-6">
            <div class="text-sm mb-6 md:mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-gray-500 font-medium mb-1">Nama Petugas</div>
                    <div class="text-gray-800 uppercase">{{ $request->checker->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-gray-500 font-medium mb-1">Tanggal Pemeriksaan</div>
                    <div class="text-gray-800 uppercase">{{ \Carbon\Carbon::parse($request->checked_at)->format('d-m-Y') }}</div>
                </div>
            </div>

            <div class="text-sm mb-6 md:mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-gray-500 font-medium mb-1">Hasil Pemeriksaan</div>
                    <div class="text-gray-800 uppercase">{{ $request->check_result}}</div>
                </div>

                <div>
                    <div class="text-gray-500 font-medium mb-1">Estimasi Waktu Pengerjaan</div>
                    <div class="text-gray-800 uppercase">{{ $request->duration_work}} Hari</div>
                </div>
            </div>

            <h4 class="text-gray-700 font-semibold mb-2">Rekomendasi Tindakan</h4>
            <div class="border border-gray-400 rounded-lg shadow-md p-6 mb-6">
                {{$request->recommended_action}}
            </div>

           <!-- Estimasi Material -->
@if($request->materials && $request->materials->count() > 0)
<div class="flex flex-col">
    <h3 class="text-gray-800 font-semibold mb-3 flex items-center gap-2">
        Estimasi Material
    </h3>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <thead>
                <tr class="bg-gray-50 text-gray-700 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Material</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Qty</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        {{ empty($material->vendor) ? 'Note' : 'Vendor' }}
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Satuan</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold">Subtotal</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @php $total = 0; @endphp
                @foreach($request->materials as $index => $material)
                    @php $total += $material->subtotal; @endphp
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 text-gray-700 text-sm">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-800 font-medium text-sm">{{ $material->material }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $material->qty }} {{ $material->uom }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">
                            {{ !empty($material->vendor) ? $material->vendor : 'Pakai Stok Internal' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-sm">
                            Rp {{ number_format($material->price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-800 font-semibold text-sm">
                            Rp {{ number_format($material->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="bg-gray-50 border-t border-gray-200">
                    <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700 text-sm">
                        Estimasi Biaya:
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-green-600 text-sm">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

        </div>
    @endif
</div>


<div id="pelaksanaan">

    @if(!in_array($request->status, ['Done', 'Closed']))
        <div class="mt-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 font-semibold rounded-md">
            ⚠ Pekerjaan Masih Dalam Proses
        </div>
         @else

    <div class="text-sm mb-6 md:mb-8 mt-6">
        <div class="text-sm mb-6 md:mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-gray-500 font-medium mb-1">Pelaksana / Vendor</div>
                <div class="text-gray-800 uppercase">{{ $request->assigned_by }}</div>
            </div>
        </div>

        <div class="text-sm mb-6 md:mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-gray-500 font-medium mb-1">Tanggal Mulai</div>
                <div class="text-gray-800 uppercase">{{ \Carbon\Carbon::parse($request->work_start)->format('d-m-Y') }}</div>
            </div>
            <div>
                <div class="text-gray-500 font-medium mb-1">Tanggal Selesai</div>
                <div class="text-gray-800 uppercase">{{ \Carbon\Carbon::parse($request->work_end)->format('d-m-Y') }}</div>
            </div>
        </div>

        @php
            $start = \Carbon\Carbon::parse($request->work_start);
            $end = \Carbon\Carbon::parse($request->work_end);
            $actualDays = $start->diffInDays($end) + 1; // termasuk hari pertama
        @endphp

        @if($actualDays > $request->duration_work)
            <div class="mt-4 p-4 bg-red-100 border-l-4 border-red-600 text-red-700 font-semibold rounded-md">
                ⚠ Peringatan: Durasi aktual <span class="font-bold">{{ $actualDays }} Hari</span> melebihi estimasi <span class="font-bold">{{ $request->duration_work }} Hari</span>!
            </div>
        @endif
    </div>

    <!-- Catatan Hasil Pekerjaan -->
    <div class="mb-6">
        <h4 class="text-gray-700 font-semibold mb-2">Catatan Hasil Pekerjaan:</h4>
        <div class="border border-gray-400 rounded-lg shadow-md p-6 mb-6">
            {{ $request->note_done ?? '-' }}
        </div>
    </div>

    <!-- Foto Before & After -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="flex flex-col">
            <h4 class="text-gray-700 font-semibold mb-2 flex items-center gap-2">
                <i data-feather="camera" class="w-4 h-4"></i>
                Foto Sebelum
            </h4>
            @if($request->evidence_before)
                <img src="{{ asset('storage/'.$request->photo_before) }}" 
                     alt="Foto Sebelum" 
                     class="rounded-xl shadow-md border border-gray-100 hover:scale-[1.02] transition-transform duration-300">
            @else
                <div class="text-gray-500 text-sm italic">Belum ada foto sebelum</div>
            @endif
        </div>

        <div class="flex flex-col">
            <h4 class="text-gray-700 font-semibold mb-2 flex items-center gap-2">
                <i data-feather="camera-off" class="w-4 h-4"></i>
                Foto Sesudah
            </h4>
            @if($request->evidence_after)
                <img src="{{ asset('storage/'.$request->photo_after) }}" 
                     alt="Foto Sesudah" 
                     class="rounded-xl shadow-md border border-gray-100 hover:scale-[1.02] transition-transform duration-300">
            @else
                <div class="text-gray-500 text-sm italic">Belum ada foto sesudah</div>
            @endif
        </div>
    </div>
     @endif
</div>
       

  </div>

  <!-- SIDEBAR: Verifikasi & Evaluasi -->
<div class="w-full md:w-1/3 bg-white shadow-md border border-gray-200 rounded-xl p-4 md:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg md:text-xl font-semibold text-gray-700">Verifikasi & Evaluasi</h3>
        <i data-feather="briefcase" class="text-gray-700 w-5 h-5"></i>
    </div>

    <!-- Kesesuaian Hasil Pengerjaan -->
    <div class="mb-4">
        <div class="text-gray-500 font-medium mb-1">Kesesuaian Hasil Pengerjaan</div>
        <div class="text-gray-800 uppercase">
            {{ $request->work_verification ?? 'Belum Ditentukan' }}
        </div>
    </div>
<!-- Persetujuan Hasil Perbaikan -->
<div class="mb-4">
    <div class="text-gray-500 font-medium mb-1">Persetujuan Hasil Perbaikan</div>
    <div class="uppercase font-semibold">
        @if(isset($request->confirmation))
            @if($request->confirmation == 1)
                <span class="text-gray-800">Disetujui</span>
            @elseif($request->confirmation == 0)
                <span class="text-gray-800">Tidak Disetujui</span>
            @else
                <span class="text-gray-800">Belum Dinilai</span>
            @endif
        @else
            <span class="text-gray-800">Belum Dinilai</span>
        @endif
    </div>
</div>


    <!-- Rating Bintang -->
  <div class="mb-4">
    <div class="text-gray-500 font-medium mb-1">Rating</div>

    @php
        $rating = $request->rating ?? 0;
        $ratingText = [
            1 => 'Tidak Puas',
            2 => 'Kurang Puas',
            3 => 'Cukup Puas',
            4 => 'Puas',
            5 => 'Sangat Puas',
        ];
    @endphp

    <div class="flex items-center space-x-2 text-yellow-400 text-4xl">
        @for ($i = 1; $i <= 5; $i++)
            <div class="relative group">
                @if($i <= $rating)
                    <i class="fas fa-star cursor-pointer"></i>
                @else
                    <i class="far fa-star cursor-pointer text-gray-300"></i>
                @endif

                <!-- Tooltip -->
                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-10">
                    {{ $ratingText[$i] }}
                </div>
            </div>
        @endfor
    </div>
</div>



    <!-- Catatan Evaluasi -->
    <div>
        <div class="text-gray-500 font-medium mb-1">Catatan Evaluasi</div>
        <div class="border border-gray-300 rounded-lg p-3 text-gray-700 text-sm">
            {{ $request->feedback ?? '-' }}
        </div>
    </div>





      <hr class="my-4">

      <div class="flex items-center justify-between">
    <h3 class="text-lg md:text-xl font-semibold text-gray-700">Request Timeline</h3>
    <i data-feather="clock" class="text-gray-700 w-5 h-5"></i>
  </div>
 <hr class="my-4">

    @php
    $hasTimeline = $request->approved || $request->checked_by || $request->verification_by || $request->authorized_by || $request->done_at || $request->closed_at;
@endphp

@if($hasTimeline)
    {{-- Approved --}}
    @if($request->approved_by)
        <div>
            <div class="text-xs text-yellow-400 uppercase font-semibold mb-1">Approved</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-yellow-500"></i>
                <span>{{ $request->approver->name ?? '-' }} Approved</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->approved_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Checked --}}
    @if($request->checked_by)
        <div>
            <div class="text-xs text-blue-400 uppercase font-semibold mb-1">Checked</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check" class="w-4 h-4 text-blue-500"></i>
                <span>{{ $request->checker->name ?? '-' }} Checked</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->checked_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Verification --}}
    @if($request->verification_by)
        <div>
            <div class="text-xs text-orange-400 uppercase font-semibold mb-1">Verification</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-orange-500"></i>
                <span>{{ $request->verifier->name ?? '-' }} Verified</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->verification_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Authorized --}}
    @if($request->authorized_by)
        <div>
            <div class="text-xs text-purple-400 uppercase font-semibold mb-1">Authorized</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-purple-500"></i>
                <span>{{ $request->authorizer->name ?? '-' }} Authorized</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->authorized_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Authorized --}}
    @if($request->done_by)
        <div>
            <div class="text-xs text-green-400 uppercase font-semibold mb-1">Done</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                <span>{{ $request->finisher->name ?? '-' }} Mark Request as Done</span>
            </div>
            <div class="flex items-center space-x-2 mt-1 mb-4">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->done_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    @endif

    {{-- Closed --}}
    @if($request->closed_at)
        <div>
            <div class="text-xs text-teal-400 uppercase font-semibold mb-1">Closed</div>
            <div class="flex items-center space-x-2">
                <i data-feather="check" class="w-4 h-4 text-teal-400"></i>
                <span>{{ $request->closer->name ?? 'Unknown' }} Close Request</span>
            </div>
            <div class="flex items-center space-x-2 mb-4 mt-1">
                <i data-feather="clock" class="w-4 h-4 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::parse($request->closed_at)->format('d M Y H:i') }}</span>
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
<hr>
  <div class="flex flex-wrap justify-start gap-2 mt-4">
    <a href="{{ route('it.issue.index') }}" 
       class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
       <i data-feather="arrow-left" class="w-4 h-4 inline"></i> Back
    </a>
@php
    $userDepartments = auth()->user()->departments->pluck('id')->toArray();
    $creatorDepartments = $request->creator->departments->pluck('id')->toArray();
    $sameDepartment = count(array_intersect($userDepartments, $creatorDepartments)) > 0;
@endphp

@if(
    $request->status == 'Pending' &&
    (
        auth()->user()->hasRole('Manager Special Access') ||
        auth()->user()->hasRole('Supervisor Special Access')
    ) &&
    $sameDepartment
)
        <button onclick="approveRequest({{ $request->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-green-600 text-white rounded">
            <i data-feather="check-circle" class="w-4 h-4 inline"></i> Approve
        </button>
        <button onclick="rejectRequest({{ $request->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-red-600 text-white rounded">
            <i data-feather="x-circle" class="w-4 h-4 inline"></i> Reject
        </button>
    @endif

   @if(
    $request->status == 'Approved' &&
    auth()->user()->departments->contains('name', 'General Affair')
)
    <button onclick="openCheckingModal({{ $request->id }})" 
        class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-700">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i> Checking
    </button>
@endif
@if(
    $request->status == 'Checked' &&
    auth()->user()->departments->contains('name', 'General Affair') &&
    (
        auth()->user()->hasRole('Supervisor Special Access') ||
        auth()->user()->hasRole('Manager Special Access')
    )
)
    <button onclick="verificationRequest({{ $request->id }})" 
        class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i> Verified
    </button>
     <button onclick="rejectRequest({{ $request->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-red-600 text-white rounded">
            <i data-feather="x-circle" class="w-4 h-4 inline"></i> Reject
        </button>
@endif
@if(
    $request->status == 'Verified' &&
    auth()->user()->departments->contains('name', 'General Affair') &&
    (
        auth()->user()->hasRole('Supervisor Special Access') ||
        auth()->user()->hasRole('Manager Special Access')
    )
)
    <button onclick="authorizedRequest({{ $request->id }})" 
        class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-700">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i> Authorized
    </button>
     <button onclick="rejectRequest({{ $request->id }})" 
            class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-red-600 text-white rounded">
            <i data-feather="x-circle" class="w-4 h-4 inline"></i> Reject
        </button>
@endif
@if(
    $request->status == 'Authorized' &&
   $request->checked_by == auth()->id()
)
    <button onclick="openDoneModal({{ $request->id }})" 
        class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
        <i data-feather="refresh-ccw" class="w-4 h-4 inline"></i> Done
    </button>
@endif
@if(
    $request->status == 'Done' &&
    $request->created_by == auth()->id()
)
    <button onclick="showCloseModal({{ $request->id }})" 
        class="w-full md:w-auto text-center flex gap-2 items-center px-4 py-2 bg-teal-500 text-white rounded hover:bg-teal-700">
        <i data-feather="lock" class="w-4 h-4 inline"></i> Closed
    </button>
@endif


  
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
                    class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-500 text-white hover:bg-gray-100 transition"
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

@push('scripts')
<script>
   $(document).ready(function() {
    // Sembunyikan semua panel awalnya
    $('#pemeriksaan, #pelaksanaan').hide();

    // Tampilkan panel pertama (pemeriksaan) saat load
    $('#pemeriksaan').show();

    // Tandai navbar pertama sebagai aktif
    $('nav a').removeClass('border-teal-600 text-teal-600');
    $('nav a[href="#pengecekan"]').addClass('border-teal-600 text-teal-600');

    // Event klik navbar
    $('nav a').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href'); // #pengecekan atau #hasil

        // Sembunyikan semua panel
        $('#pemeriksaan, #pelaksanaan').hide();

        // Tampilkan panel yang diklik
        if(target === '#pengecekan') {
            $('#pemeriksaan').fadeIn();
        } else if(target === '#pelaksanaan') {
            $('#pelaksanaan').fadeIn();
        }

        // Scroll smooth ke panel
        $('html, body').animate({
            scrollTop: $(target === '#pengecekan' ? '#pemeriksaan' : '#pelaksanaan').offset().top
        }, 500);

        // Update navbar aktif
        $('nav a').removeClass('border-teal-600 text-teal-600');
        $(this).addClass('border-teal-600 text-teal-600');
    });
});
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
               location.reload();
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
                location.reload();
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
                 location.reload();
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui request.');
            });
        }
    });
}

   function showCloseModal(requestId) {
    console.log('Modal Closed dibuka untuk ID:', requestId);
    $('#closed_request_id').val(requestId);
    $('#closedModal').removeClass('hidden').show(); // 👈 tambahkan .show() supaya pasti tampil
}

  function hideCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
  }

   // ✅ Approve Ticket
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
                 setTimeout(() => location.reload(), 1200);
            }).fail(function() {
                showToast('error', 'Terjadi kesalahan saat menyetujui request.');
            });
        }
    });
}

// ✅ Reject Ticket
$('#rejectForm').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let requestId = $('#reject_request_id').val();
    let data = form.serialize();

    $.post(`/it/issue-tracker/${requestId}/reject`, data, function (res) {
        if (res.success) {
            showToast('success', res.message);
             closeRejectModal();
           setTimeout(() => location.reload(), 1200);
           
        } else {
            showToast('error', "Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        showToast('error', 'An error occurred.');
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
                closeModal('doneModal');
                setTimeout(() => location.reload(), 1200);
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
                closeModal('closedModal');
                setTimeout(() => location.reload(), 1200);
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

</script>
@endpush

@endsection