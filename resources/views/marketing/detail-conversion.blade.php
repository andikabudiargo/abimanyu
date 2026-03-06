@extends('layouts.app')

@section('title', 'Detail Conversion')
@section('page-title', 'Detail Conversion')
@section('breadcrumb-item', 'Conversion')
@section('breadcrumb-active', 'Detail Conversion')

@section('content')


<div class="bg-gradient-to-br from-slate-50 to-white shadow-xl shadow-slate-200/50 rounded-2xl p-8 mb-4 border border-slate-100">

    {{-- Top Row: Title + Status Badge --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-800 tracking-tight">
                {{ $conversion->conversion_number }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Monthly official conversion report — detail view
            </p>
        </div>

        @php
            $statusColor = match(strtolower($conversion->status ?? 'draft')) {
                'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-100/40',
                'rejected' => 'bg-red-50 text-red-600 border-red-100 shadow-red-100/40',
                default    => 'bg-indigo-50 text-indigo-600 border-indigo-100 shadow-indigo-100/40',
            };
            $statusIcon = match(strtolower($conversion->status ?? 'draft')) {
                'approved' => 'fa-circle-check',
                'rejected' => 'fa-circle-xmark',
                default    => 'fa-pen-to-square',
            };
        @endphp

        <div class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold rounded-full border shadow-sm tracking-wide {{ $statusColor }}">
            <i class="fa-solid {{ $statusIcon }} text-[11px]"></i>
            <span>Status: {{ ucfirst($conversion->status ?? 'Draft') }}</span>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <div class="bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Conversion Period</p>
            <p class="text-sm font-semibold text-slate-700">
                {{ \Carbon\Carbon::createFromDate($conversion->year, $conversion->month, 1)->format('F Y') }}
            </p>
        </div>

        <div class="bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Created By</p>
            <p class="text-sm font-semibold text-slate-700">{{ $conversion->createdBy->name ?? '-' }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">
                {{ $conversion->created_at ? \Carbon\Carbon::parse($conversion->created_at)->format('d M Y H:i') : '-' }}
            </p>
        </div>

        <div class="col-span-2 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Notes</p>
            <p class="text-sm text-slate-600 leading-snug line-clamp-2">{{ $conversion->note ?? '-' }}</p>
        </div>

    </div>
</div>

<div class="bg-white shadow rounded-xl p-6 mb-4">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-slate-800">Conversion Details</h2>
        <div class="flex items-center gap-2">
            {{-- Search --}}
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                <input id="detail-search" type="text" placeholder="Search article / customer..."
                    class="pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56">
            </div>
            {{-- Export --}}
            <button id="btn-export" onclick="exportToExcel()"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium border border-emerald-500 rounded-lg bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition-all duration-150">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    {{-- Scrollable Table --}}
    <div class="overflow-auto" style="max-height: 500px;">
        <table id="detail-table" class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Article</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2 text-right">Qty Delivery</th>
                    <th class="px-4 py-2 text-right">Fix Conversion</th>
                    <th class="px-4 py-2 text-right">Matome</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="detail-tbody">
                @forelse($details as $row)
                <tr class="detail-row hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-2 text-slate-600">{{ $row->customer ?? '-' }}</td>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $row->article_code ?? '-' }}</td>
                    <td class="px-4 py-2 text-slate-500 text-xs">{{ $row->article_desc ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($row->delivery_qty ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-right">
                        @if($row->matome === null)
                            <span class="text-xs text-amber-500">
                                <i class="ri-error-warning-line"></i> Belum disync
                            </span>
                        @else
                            <span class="text-green-600 font-semibold">
                                {{ rtrim(rtrim(number_format($row->matome, 10, ',', '.'), '0'), ',') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right font-semibold">
                        @if($row->conversion === null)
                            <span class="text-xs text-gray-400">-</span>
                        @else
                            {{ number_format($row->conversion, 2, ',', '.') }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">
                        <i class="fa-solid fa-inbox text-2xl mb-2 block"></i>
                        No data available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

    {{-- Summary Card --}}
    <div class="flex justify-end mt-6">
        <div id="summary-card" class="w-full md:w-96 bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4">Summary</p>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Total Rows</span>
                    <span id="sum-rows" class="text-sm font-bold text-slate-800">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Total Customers</span>
                    <span id="sum-customers" class="text-sm font-bold text-slate-800">-</span>
                </div>
                <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                    <span class="text-xs text-slate-500">Total Qty Delivery</span>
                    <span id="sum-qty" class="text-sm font-bold text-slate-800">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Total Conversion</span>
                    <span id="sum-conversion" class="text-sm font-bold text-indigo-600">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-start items-center gap-2 mt-4 border-t border-gray-200 pt-6">
        <a href="{{ route('marketing.conversion.index') }}"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow text-sm">
            ← Back
        </a>
    </div>

</div>

@push('scripts')
<script>
    // Search filter
    $('#detail-search').on('keyup', function () {
        const keyword = $(this).val().toLowerCase();
        $('.detail-row').each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(keyword));
        });
    });

    // Export Excel
    function exportToExcel() {
        const rows = [['Customer', 'Article Code', 'Description', 'Qty Delivery', 'Fix Conversion', 'Matome']];

        $('.detail-row:visible').each(function () {
            const cols = $(this).find('td');
            rows.push([
                $(cols[0]).text().trim(),
                $(cols[1]).text().trim(),
                $(cols[2]).text().trim(),
                $(cols[3]).text().trim(),
                $(cols[4]).text().trim(),
                $(cols[5]).text().trim(),
            ]);
        });

        let csv = rows.map(r => r.map(c => '"' + c.replace(/"/g, '""') + '"').join(',')).join('\n');
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'conversion-detail-{{ $conversion->conversion_number }}.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush
@endsection