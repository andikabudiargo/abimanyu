@extends('layouts.app')

@section('title', 'Edit Asset')
@section('page-title', 'Edit Asset')
@section('breadcrumb-item', 'Asset Management')
@section('breadcrumb-active', 'Edit Asset')

@section('content')

<div class="w-full bg-white shadow-sm rounded-2xl overflow-hidden mb-6">

    {{-- ── PAGE HEADER ── --}}
    <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <i data-feather="edit-2" class="w-4 h-4 text-amber-600"></i>
            </div>
            <div>
                <h1 class="text-base font-semibold text-gray-800">Edit Asset</h1>
                <p class="text-xs text-gray-400 mt-0.5">
                    Editing: <span class="font-medium text-gray-600">{{ $asset->asset_name }}</span>
                    &mdash; <span class="text-gray-400">{{ $asset->asset_number }}</span>
                </p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
            <i data-feather="edit-3" class="w-3 h-3"></i> Editing
        </span>
    </div>

    <form id="asset-form" action="{{ route('it.assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- ══ LEFT: Image Upload ══ --}}
                <div class="lg:col-span-1 flex flex-col gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Asset Image
                        </label>

                        {{-- Dropzone --}}
                        <div id="dropzone"
                            class="relative border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 cursor-pointer bg-gray-50 hover:border-amber-300 hover:bg-amber-50/30 transition-all duration-200 aspect-square overflow-hidden">

                            <div id="dropzoneContent"
                                class="flex flex-col items-center justify-center gap-2 px-4 text-center pointer-events-none {{ $asset->photo ? 'hidden' : '' }}">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i data-feather="image" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <p class="text-xs font-medium text-gray-500">Drop image here</p>
                                <p class="text-[11px] text-gray-400">JPEG, PNG — max 50MB</p>
                                <span class="mt-1 px-3 py-1.5 bg-amber-500 text-white text-xs font-medium rounded-lg shadow-sm pointer-events-auto">
                                    Browse File
                                </span>
                            </div>

                            <img id="previewImage"
     src="{{ $asset->photo ? asset($asset->photo) : '' }}"
     alt="Preview"
     class="{{ $asset->photo ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover">

                            <button type="button" id="resetPhoto"
                                class="{{ $asset->photo ? '' : 'hidden' }} absolute top-2 right-2 z-10 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition">
                                <i data-feather="x" class="w-3.5 h-3.5"></i>
                            </button>

                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>

                        {{-- Hidden flag to signal photo removal --}}
                        <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                    </div>

                    {{-- Quick info box --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 space-y-2">
                        <p class="text-xs font-semibold text-amber-700 flex items-center gap-1.5">
                            <i data-feather="alert-circle" class="w-3.5 h-3.5"></i> Editing Tips
                        </p>
                        <ul class="text-[11px] text-amber-600 space-y-1 list-disc list-inside">
                            <li>Leave image blank to keep existing</li>
                            <li>Click × to remove current image</li>
                            <li>Warranty is in months</li>
                            <li>Personal use requires assignee</li>
                        </ul>
                    </div>
                </div>

                {{-- ══ RIGHT: Form Fields ══ --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Section: Basic Info --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="w-5 h-px bg-gray-200 inline-block"></span>
                            Basic Information
                            <span class="flex-1 h-px bg-gray-100 inline-block"></span>
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Asset Number --}}
                            <div>
                                <label for="asset_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Asset Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-feather="hash" class="w-4 h-4 text-gray-400"></i>
                                    </span>
                                    <input type="text" name="asset_number" id="asset_number"
                                        value="{{ old('asset_number', $asset->asset_number) }}"
                                        placeholder="e.g. AST-2024-001"
                                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition" />
                                </div>
                            </div>

                            {{-- Asset Name --}}
                            <div>
                                <label for="asset_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Asset Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-feather="tag" class="w-4 h-4 text-gray-400"></i>
                                    </span>
                                    <input type="text" name="asset_name" id="asset_name"
                                        value="{{ old('asset_name', $asset->asset_name) }}"
                                        placeholder="e.g. Lenovo ThinkPad X1"
                                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition" />
                                </div>
                            </div>

                            {{-- Asset Type --}}
                            <div>
                                <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Asset Type <span class="text-red-500">*</span>
                                </label>
                                <select name="asset_type" id="asset_type"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Asset Type --</option>
                                    @foreach(['Laptop','Desktop','Server','CCTV','Printer','Scanner','Monitor','Network','Storage','UPS','Smartphone','Tablet','Software','Peripheral'] as $type)
                                        <option value="{{ $type }}" {{ old('asset_type', $asset->asset_type) === $type ? 'selected' : '' }}>
                                            {{ $type === 'Network' ? 'Network Device' : ($type === 'Storage' ? 'Storage / NAS' : ($type === 'Software' ? 'Software / License' : ($type === 'Laptop' ? 'Laptop / Notebook' : ($type === 'Desktop' ? 'Desktop / PC' : $type)))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Owned Category --}}
                            <div>
                                <label for="acquistion_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Owned Category <span class="text-red-500">*</span>
                                </label>
                                <select name="acquistion_type" id="acquistion_type"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Owned Category --</option>
                                    @foreach(['Purchased New','Purchased Secondhand','Rented','Loaned','Other'] as $acq)
                                        <option value="{{ $acq }}" {{ old('acquistion_type', $asset->acquistion_type) === $acq ? 'selected' : '' }}>
                                            {{ $acq === 'Rented' ? 'Rented from Supplier' : ($acq === 'Loaned' ? 'Loaned from Vendor' : $acq) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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

                            {{-- Supplier --}}
                            <div class="md:col-span-2">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Supplier
                                </label>
                                <select name="supplier_id" id="supplier_id"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Supplier --</option>
                                    @if($asset->supplier)
                                        <option value="{{ $asset->supplier->id }}" selected>
                                            {{ $asset->supplier->code }} - {{ $asset->supplier->name }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            {{-- Purchase Date --}}
                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Purchase Date
                                </label>
                                <input type="date" name="purchase_date" id="purchase_date"
                                    value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400 transition" />
                            </div>

                            {{-- Warranty --}}
                            <div>
                                <label for="warranty" class="block text-sm font-medium text-gray-700 mb-1">
                                    Warranty
                                </label>
                                <div class="relative">
                                    <input type="number" name="warranty" id="warranty" min="0"
                                        value="{{ old('warranty', $asset->warranty) }}"
                                        placeholder="0"
                                        class="w-full pl-3 pr-16 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400 transition" />
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-gray-400 pointer-events-none">months</span>
                                </div>
                            </div>

                            {{-- Condition --}}
                            <div class="md:col-span-2">
                                <label for="conditions" class="block text-sm font-medium text-gray-700 mb-1">
                                    Condition
                                </label>
                                <select name="conditions" id="conditions"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Condition --</option>
                                    @foreach(['Good' => 'Good', 'Broken but still usable' => 'Broken but still usable', 'Damaged and cannot be used' => "Damaged and can't be used"] as $val => $label)
                                        <option value="{{ $val }}" {{ old('conditions', $asset->conditions) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
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

                            {{-- Assignment Type --}}
                            <div class="md:col-span-2">
                                <label for="assignment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Assignment Type <span class="text-red-500">*</span>
                                </label>
                                <select name="assignment_type" id="assignment_type"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Assignment Type --</option>
                                    @foreach(['Personal' => 'Personal Use', 'Department' => 'Department Use', 'Shared' => 'Shared Use', 'Spare' => 'Spare'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('assignment_type', $asset->assignment_type) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Assigned To (Personal) --}}
                            <div id="assignedToGroup" class="hidden">
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                                    Assigned To <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <i data-feather="user" class="w-4 h-4 text-gray-400"></i>
                                    </span>
                                    <select name="assigned_to" id="assigned_to"
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                        <option value="">-- Choose PIC --</option>
                                        @if($asset->assignedUser)
                                            <option value="{{ $asset->assignedUser->id }}" selected>
                                                {{ $asset->assignedUser->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            {{-- Location --}}
                            <div id="locationGroup" class="hidden">
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location <span class="text-red-500">*</span>
                                </label>
                                @php
                                    $locations = [
                                        'Ruang General Affair', 'Ruang HR', 'Ruang Server',
                                        'Pantry', 'Lobby Lt.1', 'R. Accounting', 'R. Purchasing & Marketing', 'Resepsionis',
                                        'Ruang Office LT.1', 'Ruang Office LT.2', 'Plant 1', 'Plant 2', 'R.Engineering-Quality',
                                        'Ruang Bima', 'Ruang Arjuna', 'Ruang Srikandi', 'Ruang Yudhistira',
                                    ];
                                @endphp
                                <select name="location" id="location"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Location --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" {{ old('location', $asset->location) === $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Location Update --}}
                            <div id="locationUpdateGroup" class="hidden">
                                <label for="location_update" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location Update
                                </label>
                                <select name="location_update" id="location_update"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                                    <option value="">-- Choose Updated Location --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" {{ old('location_update', $asset->location_update) === $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Fill if asset location has been updated from initial placement.
                                </p>
                            </div>

                            {{-- PIC --}}
                            <div id="assignedToDept" class="hidden col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Person in Charge (PIC) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="pic" id="pic"
                                    value="{{ old('pic', $asset->pic) }}"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400">
                            </div>

                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" id="note" rows="3"
                            placeholder="Any additional information about this asset..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-amber-400 resize-none transition">{{ old('note', $asset->note) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── FOOTER ACTIONS ── --}}
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('it.assets.index', $asset->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition shadow-sm">
                    <i data-feather="arrow-left" class="w-4 h-4"></i> Cancel
                </a>

                {{-- Delete Button --}}
                <button type="button" id="deleteBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition shadow-sm">
                    <i data-feather="trash-2" class="w-4 h-4"></i> Delete
                </button>
            </div>

            <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow transition">
                <i data-feather="save" class="w-4 h-4"></i> Update Asset
            </button>
        </div>

    </form>

    {{-- Hidden delete form --}}
    <form id="delete-form" action="{{ route('it.assets.index', $asset->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</div>

{{-- ── STYLES (same as create) ── --}}
<style>
input::placeholder,
textarea::placeholder { font-size: 12px; color: #9ca3af; }

.select2-container { width: 100% !important; }
.select2-container .select2-selection--single {
    height: 42px;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 30px;
    font-size: 14px;
    color: #374151;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    right: 10px;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #fbbf24;
    box-shadow: 0 0 0 2px rgb(251 191 36 / 0.2);
}
.select2-dropdown {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    font-size: 14px;
}
.select2-results__option--highlighted {
    background-color: #fffbeb !important;
    color: #b45309 !important;
}
</style>

@push('scripts')
<script>
$(document).ready(function () {

    feather.replace();

    // ========================
    // IMAGE UPLOAD / DROPZONE
    // ========================
    const $dropzone    = $('#dropzone');
    const $dropContent = $('#dropzoneContent');
    const $previewImg  = $('#previewImage');
    const $resetBtn    = $('#resetPhoto');
    const $photoInput  = $('#photo');
    const $removeFlag  = $('#remove_photo');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $previewImg.attr('src', e.target.result).removeClass('hidden');
            $dropContent.addClass('hidden');
            $resetBtn.removeClass('hidden');
            $dropzone.removeClass('border-dashed').addClass('border-transparent');
            $removeFlag.val('0');
        };
        reader.readAsDataURL(file);
    }

    function resetPreview() {
        $previewImg.attr('src', '').addClass('hidden');
        $dropContent.removeClass('hidden');
        $resetBtn.addClass('hidden');
        $photoInput.val('');
        $removeFlag.val('1'); // Signal backend to remove photo
        $dropzone.addClass('border-dashed').removeClass('border-transparent');
    }

    // If existing photo, hide dashed border
    @if($asset->photo)
        $dropzone.removeClass('border-dashed').addClass('border-transparent');
    @endif

    $photoInput.on('change', function () { showPreview(this.files[0]); });
    $resetBtn.on('click', function (e) { e.stopPropagation(); resetPreview(); });

    $dropzone.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('border-amber-400 bg-amber-50/50');
    }).on('dragleave', function () {
        $(this).removeClass('border-amber-400 bg-amber-50/50');
    }).on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('border-amber-400 bg-amber-50/50');
        showPreview(e.originalEvent.dataTransfer.files[0]);
    });

    // ========================
    // SELECT2 INIT
    // ========================
    ['#asset_type', '#acquistion_type', '#assignment_type',
     '#location', '#location_update', '#conditions'].forEach(function (sel) {
        $(sel).select2({ allowClear: true, width: '100%' });
    });

    $('#supplier_id').select2({
        placeholder: '-- Choose Supplier --',
        allowClear: true,
        ajax: {
            url: "{{ route('purchasing.supplier.select') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { id: item.id, text: item.code + ' - ' + item.name };
                    })
                };
            },
            cache: true
        }
    });

    $('#assigned_to').select2({
        placeholder: '-- Assigned to --',
        allowClear: true,
        ajax: {
            url: "{{ route('setting.user.select') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { id: item.id, text: item.name };
                    })
                };
            },
            cache: true
        }
    });

    // ========================
    // ASSIGNMENT TYPE TOGGLE
    // ========================
    function toggleAssignment(val) {
        $('#assignedToGroup').addClass('hidden');
        $('#locationGroup').addClass('hidden');
        $('#locationUpdateGroup').addClass('hidden');
        $('#assignedToDept').addClass('hidden');

        if (val === 'Personal') {
            $('#assignedToGroup').removeClass('hidden');
        } else if (val) {
            $('#locationGroup').removeClass('hidden');
            $('#locationUpdateGroup').removeClass('hidden');
            $('#assignedToDept').removeClass('hidden');
        }
    }

    // Init on load with existing value
    toggleAssignment("{{ old('assignment_type', $asset->assignment_type) }}");

    $('#assignment_type').on('change', function () {
        toggleAssignment($(this).val());
    });

    // ========================
    // DELETE CONFIRM
    // ========================
    $('#deleteBtn').on('click', function () {
        Swal.fire({
            title: 'Delete Asset?',
            text: 'This action cannot be undone. Asset "{{ $asset->asset_name }}" will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#delete-form').submit();
            }
        });
    });

    // ========================
    // FORM SUBMIT (AJAX)
    // ========================
    $('#asset-form').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).html(
            '<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Updating...'
        );

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('it.assets.update', $asset->id) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: res.message || 'Asset updated!',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true
                    });
                    setTimeout(() => {
                        window.location.href = "{{ route('it.assets.index', $asset->id) }}";
                    }, 2000);
                } else {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'error',
                        title: res.message || 'Failed to update asset.',
                        showConfirmButton: false, timer: 3000
                    });
                    $btn.prop('disabled', false).html('<i data-feather="save" class="w-4 h-4 inline"></i> Update Asset');
                    feather.replace();
                }
            },
            error: function (xhr) {
                let msg = 'Something went wrong!';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join('<br>');
                }
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'error',
                    title: 'Validation Error', html: msg,
                    showConfirmButton: false, timer: 4000, timerProgressBar: true
                });
                $btn.prop('disabled', false).html('<i data-feather="save" class="w-4 h-4 inline"></i> Update Asset');
                feather.replace();
            }
        });
    });

});
</script>
@endpush

@endsection