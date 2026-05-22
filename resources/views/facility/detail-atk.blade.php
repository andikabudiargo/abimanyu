@extends('layouts.app')

@section('title', 'Detail ATK Request')
@section('page-title', 'DETAIL ATK REQUEST')
@section('breadcrumb-item', 'GA Inventory Management')
@section('breadcrumb-active', 'Detail ATK Request')

@section('content')

@php
    $status      = strtolower($atkRequest->status);
    $authUser    = auth()->user();
    $isCreator   = $authUser->id === $atkRequest->created_by;
    $isAdminGA   = $authUser->roles->contains('name', 'Admin GA');

    $statusConfig = [
        'submitted'  => ['label' => 'Submitted',  'dot' => 'bg-blue-500',   'cls' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'approved'   => ['label' => 'Approved',   'dot' => 'bg-emerald-500','cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'rejected'   => ['label' => 'Rejected',   'dot' => 'bg-red-500',    'cls' => 'bg-red-50 text-red-600 border-red-200'],
        'cancelled'  => ['label' => 'Cancelled',  'dot' => 'bg-gray-400',   'cls' => 'bg-gray-50 text-gray-500 border-gray-200'],
        'distributed'=> ['label' => 'Distributed','dot' => 'bg-purple-500', 'cls' => 'bg-purple-50 text-purple-700 border-purple-200'],
        'received'   => ['label' => 'Received',   'dot' => 'bg-teal-500',   'cls' => 'bg-teal-50 text-teal-700 border-teal-200'],
    ];
    $sc = $statusConfig[$status] ?? ['label' => $atkRequest->status, 'dot' => 'bg-gray-400', 'cls' => 'bg-gray-50 text-gray-500 border-gray-200'];
@endphp

{{-- ── Top Action Bar ── --}}
<div class="bg-white border py-2 px-4 border-gray-50 shadow-md rounded rounded-lg flex items-center justify-between mb-4">
    <a href="{{ route('facility.atk.index') }}"
        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
        <i data-feather="arrow-left" class="w-3.5 h-3.5"></i> Kembali
    </a>
    <div class="flex items-center gap-2">
        <button onclick="printRequest()"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-600 hover:bg-gray-50 bg-white transition">
            <i data-feather="printer" class="w-3.5 h-3.5"></i> Print
        </button>

        @if($status === 'submitted' && $isCreator)
        <a href="{{ route('facility.atk.request.edit', $atkRequest->id) }}"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-200 text-xs text-amber-600 hover:bg-amber-50 bg-white transition">
            <i data-feather="edit-2" class="w-3.5 h-3.5"></i> Edit
        </a>
        <button onclick="confirmDelete()"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-xs text-red-600 hover:bg-red-50 bg-white transition">
            <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Hapus
        </button>
        @endif

        @if($status === 'submitted' && $isAdminGA)
        <button onclick="approveRequest()"
            class="flex items-center gap-1.5 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg shadow transition">
            <i data-feather="check-circle" class="w-3.5 h-3.5"></i> Approve
        </button>
        <button onclick="rejectRequest()"
            class="flex items-center gap-1.5 px-4 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg shadow transition">
            <i data-feather="x-circle" class="w-3.5 h-3.5"></i> Reject
        </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ── Kolom Kiri (2/3) ── --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white shadow rounded-xl overflow-hidden">

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800 font-mono">
                    {{ $atkRequest->request_number }}
                </p>
                <p class="text-[11px] text-gray-400">ATK Request</p>
            </div>
        </div>

        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium border {{ $sc['cls'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
            {{ $sc['label'] }}
        </span>
    </div>

    {{-- INFO --}}
    <div class="px-6 py-3 grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3 text-xs">

        <div>
            <p class="text-[10px] text-gray-400 mb-0.5">Pemohon</p>
            <p class="font-medium text-gray-700">{{ $atkRequest->createdBy->name ?? '—' }}</p>
        </div>

        <div>
            <p class="text-[10px] text-gray-400 mb-0.5">Department</p>
            <p class="text-gray-700">{{ $atkRequest->departemen?->name ?? '—' }}</p>
        </div>

        <div>
            <p class="text-[10px] text-gray-400 mb-0.5">Tanggal</p>
            <p class="text-gray-700">
                {{ \Carbon\Carbon::parse($atkRequest->created_at)->translatedFormat('d M Y') }}
            </p>
        </div>

        @if($atkRequest->note)
        <div class="col-span-2 sm:col-span-3">
            <p class="text-[10px] text-gray-400 mb-0.5">Catatan</p>
            <p class="text-xs text-gray-600 bg-gray-50 rounded-md px-3 py-2 border">
                {{ $atkRequest->note }}
            </p>
        </div>
        @endif

    </div>

    {{-- DIVIDER --}}
    <div class="border-t border-gray-100"></div>

    {{-- ITEM HEADER --}}
    <div class="flex items-center justify-between px-6 py-2.5">
        <p class="text-xs font-semibold text-gray-700">Item ATK</p>
        <span class="text-[11px] text-gray-400">
            {{ $atkRequest->items->count() }} item
        </span>
    </div>

    {{-- ITEM LIST --}}
    <div class="divide-y divide-gray-100">
        @forelse($atkRequest->items as $i => $item)
        <div class="flex items-center gap-3 px-6 py-2.5 hover:bg-gray-50 transition">

            {{-- No --}}
            <span class="text-[11px] text-gray-400 w-5 text-center">
                {{ $i + 1 }}
            </span>

            {{-- Foto --}}
            @if($item->atk->photo_url ?? null)
            <img src="{{ $item->atk->photo_url }}"
                 class="w-7 h-7 rounded object-cover border">
            @else
            <div class="w-7 h-7 rounded bg-gray-100 flex items-center justify-center">
                <i data-feather="package" class="w-3 text-gray-300"></i>
            </div>
            @endif

            {{-- Nama --}}
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-800 truncate">
                    {{ $item->atk->name ?? '—' }}
                </p>
                <p class="text-[10px] text-gray-400">
                    {{ $item->atk->uom ?? '—' }}
                </p>
            </div>

            {{-- Qty --}}
            <div class="text-center">
                <p class="text-[10px] text-gray-400">Qty</p>
                <p class="text-xs font-semibold text-blue-600">
                    {{ $item->qty }} {{ $item->atk->uom ?? '-' }}
                </p>
            </div>

        </div>
        @empty
        <div class="px-6 py-8 text-center">
            <i data-feather="inbox" class="w-6 h-6 text-gray-200 mx-auto mb-1"></i>
            <p class="text-xs text-gray-400">Tidak ada item</p>
        </div>
        @endforelse
    </div>

    {{-- FOOTER --}}
    <div class="px-6 py-2.5 border-t bg-gray-50 flex justify-between text-[11px]">
        <span class="text-gray-400">Total</span>
        <span class="font-medium text-gray-700">
            {{ $atkRequest->items->sum('qty') }}
        </span>
    </div>

</div>

    </div>

    {{-- ── Kolom Kanan (1/3) ── --}}
    <div class="space-y-4">

        {{-- Card: Timeline --}}
        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-700">Timeline</p>
            </div>
            <div class="px-5 py-4">
                <ol class="relative border-l border-gray-200 space-y-5 ml-2">

                    {{-- Submitted --}}
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-blue-500 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-700">Submitted</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">
                            {{ $atkRequest->createdBy->name ?? '—' }}
                        </p>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ \Carbon\Carbon::parse($atkRequest->created_at)->format('d M Y, H:i') }}
                        </p>
                    </li>

                    {{-- Approved / Rejected --}}
                    @if($atkRequest->approved_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-700">Approved</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $atkRequest->approvedBy->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ \Carbon\Carbon::parse($atkRequest->approved_at)->format('d M Y, H:i') }}
                        </p>
                    </li>
                    @elseif($atkRequest->rejected_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-red-500 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-700">Rejected</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $atkRequest->rejectedBy->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ \Carbon\Carbon::parse($atkRequest->rejected_at)->format('d M Y, H:i') }}
                        </p>
                        @if($atkRequest->rejected_reason)
                        <p class="text-[11px] text-red-500 mt-1 bg-red-50 rounded px-2 py-1 border border-red-100">
                            {{ $atkRequest->rejected_reason }}
                        </p>
                        @endif
                    </li>
                    @else
                    <li class="ml-4 opacity-40">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-gray-300 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-400">Menunggu Approval</p>
                    </li>
                    @endif

                    {{-- Distributed --}}
                    @if($atkRequest->distributed_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-purple-500 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-700">Distributed</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $atkRequest->distributedBy->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ \Carbon\Carbon::parse($atkRequest->distributed_at)->format('d M Y, H:i') }}
                        </p>
                    </li>
                    @endif

                    {{-- Received --}}
                    @if($atkRequest->received_at)
                    <li class="ml-4">
                        <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-teal-500 border-2 border-white"></div>
                        <p class="text-[11px] font-semibold text-gray-700">Received</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $atkRequest->receivedBy->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ \Carbon\Carbon::parse($atkRequest->received_at)->format('d M Y, H:i') }}
                        </p>
                    </li>
                    @endif

                </ol>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
const requestId     = {{ $atkRequest->id }};
const requestNumber = '{{ $atkRequest->request_number }}';

function approveRequest() {
    Swal.fire({
        title: 'Approve Request?',
        html : `Request <strong>${requestNumber}</strong> akan disetujui.`,
        icon : 'question',
        showCancelButton   : true,
        confirmButtonColor : '#10b981',
        cancelButtonText   : 'Batal',
        confirmButtonText  : 'Ya, Approve',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url    : `/facility/atk/request/${requestId}/approve`,
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: res => {
                Swal.fire({ icon: 'success', title: 'Approved!', text: res.message ?? 'Request disetujui.', confirmButtonColor: '#2563eb' })
                    .then(() => location.reload());
            },
            error: xhr => Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.' }),
        });
    });
}

function rejectRequest() {
    Swal.fire({
        title        : 'Reject Request?',
        html         : `Request <strong>${requestNumber}</strong> akan ditolak.`,
        icon         : 'warning',
        input        : 'textarea',
        inputLabel   : 'Alasan penolakan',
        inputPlaceholder: 'Tulis alasan...',
        inputAttributes : { rows: 3 },
        showCancelButton   : true,
        confirmButtonColor : '#ef4444',
        cancelButtonText   : 'Batal',
        confirmButtonText  : 'Ya, Reject',
        preConfirm: (reason) => {
            if (!reason?.trim()) { Swal.showValidationMessage('Alasan wajib diisi.'); return false; }
            return reason;
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url    : `/facility/atk/request/${requestId}/reject`,
            method : 'POST',
            data   : { reason: result.value, _token: $('meta[name="csrf-token"]').attr('content') },
            success: res => {
                Swal.fire({ icon: 'success', title: 'Rejected!', text: res.message ?? 'Request ditolak.', confirmButtonColor: '#2563eb' })
                    .then(() => location.reload());
            },
            error: xhr => Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.' }),
        });
    });
}

function confirmDelete() {
    Swal.fire({
        title  : 'Hapus Request?',
        html   : `Request <strong>${requestNumber}</strong> akan dihapus permanen.`,
        icon   : 'warning',
        showCancelButton   : true,
        confirmButtonColor : '#ef4444',
        cancelButtonText   : 'Batal',
        confirmButtonText  : 'Ya, Hapus',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url    : `/facility/atk/request/${requestId}`,
            method : 'POST',
            data   : { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
            success: res => {
                Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message ?? 'Request dihapus.', confirmButtonColor: '#2563eb' })
                    .then(() => { window.location.href = '{{ route("facility.atk.index") }}'; });
            },
            error: xhr => Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Tidak dapat menghapus.' }),
        });
    });
}

function printRequest() {
    window.print();
}
</script>
@endpush

@endsection