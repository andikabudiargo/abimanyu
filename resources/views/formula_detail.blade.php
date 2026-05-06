{{-- resources/views/formula_detail.blade.php --}}
{{-- Usage: @include('formula_detail', ['formula' => $formula]) --}}

<div class="border-t border-gray-100">

    {{-- ── Tabel Kriteria ── --}}
    <div class="px-4 pt-3 pb-1">
        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Tabel Kriteria Penilaian</div>
    </div>

    <div class="tbl-scroll">
        <table class="w-full border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-400 border-b border-gray-100 w-8">No</th>
                    <th class="px-3 py-2 text-left   text-[10px] font-semibold uppercase tracking-wider text-gray-400 border-b border-gray-100">Item</th>

                    @php
                        /* Kumpulkan semua grade unik dari semua item, urut min_point */
                        $allGrades = $formula->items->flatMap(fn($i) => $i->criteria)
                            ->sortBy('min_point')
                            ->map(fn($c) => $c->grade . ' (' . $c->min_point . '–' . $c->max_point . ')|' . $c->grade)
                            ->unique()
                            ->values();

                        /* Fallback: ambil dari koleksi tanpa relasi jika kosong */
                        if ($allGrades->isEmpty()) {
                            $allGrades = collect(['A (1–2)|A','B (3–4)|B','C (5–6)|C','D (7–8)|D','E (9–10)|E']);
                        }
                    @endphp

                    @foreach($allGrades as $gradeRaw)
                    @php [$gradeLabel] = explode('|', $gradeRaw); @endphp
                    <th class="px-3 py-2 text-center text-[10px] font-semibold text-gray-400 border-b border-gray-100 min-w-[110px]">{{ $gradeLabel }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($formula->items as $idx => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-3 py-3 text-center border-b border-gray-50">
                        <span class="w-5 h-5 rounded-full bg-blue-50 text-[#1e3a5f] text-[10px] font-bold inline-flex items-center justify-center">{{ $idx + 1 }}</span>
                    </td>
                    <td class="px-3 py-3 font-semibold text-gray-800 border-b border-gray-50 uppercase text-[11px] tracking-wide whitespace-nowrap">
                        {{ $item->item_name }}
                    </td>
                    @foreach($allGrades as $gradeRaw)
                    @php
                        [,$gradeKey] = explode('|', $gradeRaw);
                        $criteria = $item->criteria()->firstWhere('grade', $gradeKey);
                    @endphp
                    <td class="px-3 py-3 border-b border-gray-50 align-top min-w-[110px]">
                        @if($criteria)
                        <span class="inline-block text-[9px] font-bold bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded mb-1">{{ $gradeKey }}</span><br>
                        <span class="text-[11px] text-gray-500 leading-snug">{{ $criteria->description }}</span>
                        @else
                        <span class="text-gray-200">—</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Tier Reward ── --}}
    <div class="px-4 pt-4 pb-1 border-t border-gray-100 mt-1">
        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-3">Tier Reward (berdasarkan rata-rata nilai)</div>
        @php $maxR = $formula->tiers->max('reward_amount') ?: 1; @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 pb-4">
            @forelse($formula->tiers->sortBy('min_score') as $tier)
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                <div class="text-[10px] font-semibold text-gray-500 mb-2">
                    Avg {{ $tier->min_score }} – {{ $tier->max_score }}
                </div>
                <div class="h-1 bg-gray-200 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-[#1e3a5f] rounded-full" style="width:{{ round($tier->reward_amount/$maxR*100) }}%"></div>
                </div>
                <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded">
                    Rp {{ number_format($tier->reward_amount, 0, ',', '.') }}
                </span>
            </div>
            @empty
            <div class="col-span-full text-xs text-gray-400 py-4">Belum ada tier reward.</div>
            @endforelse
        </div>
    </div>

</div>