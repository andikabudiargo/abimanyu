@extends('layouts.app')

@section('title', 'Asset Detail')
@section('page-title', 'Asset Detail')
@section('breadcrumb-item', 'Asset Management')
@section('breadcrumb-active', 'Asset Detail')

@section('content')

<div class="w-full space-y-6 mb-6">

    {{-- ── PAGE HEADER CARD ── --}}
    <div class="w-full bg-white shadow-sm rounded-2xl overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <i data-feather="box" class="w-4 h-4 text-indigo-600"></i>
                </div>
                <div>
                    <h1 class="text-base font-semibold text-gray-800">{{ $asset->asset_name }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Asset Number: <span class="font-medium text-gray-600">{{ $asset->asset_number }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Status Badge --}}
                @php
                    $conditionMap = [
                        'Good'                         => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'check-circle'],
                        'Broken but still usable'      => ['bg-amber-50  text-amber-700  border-amber-200',   'alert-triangle'],
                        'Damaged and cannot be used'   => ['bg-red-50    text-red-700    border-red-200',     'x-circle'],
                    ];
                    [$condClass, $condIcon] = $conditionMap[$asset->conditions] ?? ['bg-gray-100 text-gray-500 border-gray-200', 'minus'];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border {{ $condClass }}">
                    <i data-feather="{{ $condIcon }}" class="w-3 h-3"></i> {{ $asset->conditions ?? 'Unknown' }}
                </span>

                <a href="{{ route('it.assets.edit', $asset->id) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition">
                    <i data-feather="edit-2" class="w-4 h-4"></i> Edit
                </a>

                <a href="{{ route('it.assets.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition shadow-sm">
                    <i data-feather="arrow-left" class="w-4 h-4"></i> Back
                </a>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- ══ LEFT: Image + Quick Stats ══ --}}
                <div class="lg:col-span-1 flex flex-col gap-4">

                    {{-- Asset Image --}}
                    <div class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50 aspect-square flex items-center justify-center">
                        @if($asset->photo)
                            <img src="{{ asset('storage/' . $asset->photo) }}"
                                 alt="{{ $asset->asset_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i data-feather="image" class="w-12 h-12"></i>
                                <span class="text-xs">No Image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Asset Type</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $asset->asset_type ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Category</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $asset->acquistion_type ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Assignment</span>
                            <span class="text-xs font-semibold text-indigo-600">{{ $asset->assignment_type ?? '—' }}</span>
                        </div>
                        @if($asset->warranty)
                        <div class="flex items-center justify-between bg-amber-50 rounded-xl px-4 py-3 border border-amber-100">
                            <span class="text-xs text-amber-600 font-medium">Warranty</span>
                            <span class="text-xs font-semibold text-amber-700">{{ $asset->warranty }} months</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ══ RIGHT: Detail Fields ══ --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Section: Basic Information --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="w-5 h-px bg-gray-200 inline-block"></span>
                            Basic Information
                            <span class="flex-1 h-px bg-gray-100 inline-block"></span>
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-detail-field icon="hash"    label="Asset Number" :value="$asset->asset_number" />
                            <x-detail-field icon="tag"     label="Asset Name"   :value="$asset->asset_name" />
                            <x-detail-field icon="layers"  label="Asset Type"   :value="$asset->asset_type" />
                            <x-detail-field icon="package" label="Owned Category" :value="$asset->acquistion_type" />
                        </div>
                    </div>

                    {{-- Section: Purchase & Supplier --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="w-5 h-px bg-gray-200 inline-block"></span>
                            Purchase & Supplier
                            <span class="flex-1 h-px bg-gray-100 inline-block"></span>
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-detail-field icon="truck"    label="Supplier" :value="$asset->supplier?->code . ' - ' . $asset->supplier?->name" />
                            </div>
                            <x-detail-field icon="calendar"    label="Purchase Date" :value="$asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : null" />
                            <x-detail-field icon="shield"      label="Warranty" :value="$asset->warranty ? $asset->warranty . ' months' : null" />
                            <div class="md:col-span-2">
                                <x-detail-field icon="activity" label="Condition"  :value="$asset->conditions" />
                            </div>
                        </div>
                    </div>

                    {{-- Section: Assignment --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="w-5 h-px bg-gray-200 inline-block"></span>
                            Assignment
                            <span class="flex-1 h-px bg-gray-100 inline-block"></span>
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-detail-field icon="briefcase" label="Assignment Type" :value="$asset->assignment_type" />
                            </div>

                            @if($asset->assignment_type === 'Personal')
                                <div class="md:col-span-2">
                                    <x-detail-field icon="user" label="Assigned To" :value="$asset->assignedUser?->name" />
                                </div>
                            @else
                                <x-detail-field icon="map-pin"  label="Location"        :value="$asset->location" />
                                <x-detail-field icon="navigation" label="Location Update" :value="$asset->location_update" />
                                <div class="md:col-span-2">
                                    <x-detail-field icon="users" label="Person in Charge (PIC)" :value="$asset->pic" />
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Note --}}
                    @if($asset->note)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="w-5 h-px bg-gray-200 inline-block"></span>
                            Note
                            <span class="flex-1 h-px bg-gray-100 inline-block"></span>
                        </p>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm text-gray-600 leading-relaxed">
                            {{ $asset->note }}
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ── FOOTER META ── --}}
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
            <span class="flex items-center gap-1.5">
                <i data-feather="clock" class="w-3.5 h-3.5"></i>
                Created: {{ $asset->created_at->format('d M Y, H:i') }}
            </span>
            @if($asset->updated_at && $asset->updated_at != $asset->created_at)
            <span class="flex items-center gap-1.5">
                <i data-feather="edit" class="w-3.5 h-3.5"></i>
                Last updated: {{ $asset->updated_at->format('d M Y, H:i') }}
            </span>
            @endif
        </div>
    </div>

</div>

{{-- ── Blade Component: x-detail-field ── --}}
{{-- resources/views/components/detail-field.blade.php --}}
{{-- 
    @props(['icon' => 'info', 'label' => '', 'value' => null])
    <div>
        <label class="block text-xs font-medium text-gray-400 mb-1">{{ $label }}</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-feather="{{ $icon }}" class="w-4 h-4 text-gray-300"></i>
            </span>
            <div class="w-full pl-9 pr-3 py-2.5 border border-gray-100 rounded-lg text-sm text-gray-700 bg-gray-50">
                {{ $value ?? '—' }}
            </div>
        </div>
    </div>
--}}

@push('scripts')
<script>
$(document).ready(function () {
    feather.replace();
});
</script>
@endpush

@endsection