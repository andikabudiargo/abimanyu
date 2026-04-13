@extends('layouts.app')

@section('title', 'Register Asset')
@section('page-title', 'Register Asset')
@section('breadcrumb-item', 'Asset Management')
@section('breadcrumb-active', 'Register Asset')

@section('content')

<div class="w-full bg-white shadow-sm rounded-2xl overflow-hidden mb-6">

    {{-- ── PAGE HEADER ── --}}
    <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                <i data-feather="box" class="w-4 h-4 text-indigo-600"></i>
            </div>
            <div>
                <h1 class="text-base font-semibold text-gray-800">Register New Asset</h1>
                <p class="text-xs text-gray-400 mt-0.5">Fill in the details below to add a new asset to the system</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
            <i data-feather="edit-3" class="w-3 h-3"></i> Draft
        </span>
    </div>

    <form id="asset-form" action="" method="POST" enctype="multipart/form-data">
        @csrf

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
                            class="relative border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 cursor-pointer bg-gray-50 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all duration-200 aspect-square overflow-hidden">

                            <div id="dropzoneContent" class="flex flex-col items-center justify-center gap-2 px-4 text-center pointer-events-none">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i data-feather="image" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <p class="text-xs font-medium text-gray-500">Drop image here</p>
                                <p class="text-[11px] text-gray-400">JPEG, PNG — max 50MB</p>
                                <span class="mt-1 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg shadow-sm pointer-events-auto">
                                    Browse File
                                </span>
                            </div>

                            <img id="previewImage" src="" alt="Preview"
                                class="hidden absolute inset-0 w-full h-full object-cover">

                            <button type="button" id="resetPhoto"
                                class="hidden absolute top-2 right-2 z-10 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition">
                                <i data-feather="x" class="w-3.5 h-3.5"></i>
                            </button>

                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>

                    {{-- Quick info box --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-2">
                        <p class="text-xs font-semibold text-blue-700 flex items-center gap-1.5">
                            <i data-feather="info" class="w-3.5 h-3.5"></i> Quick Guide
                        </p>
                        <ul class="text-[11px] text-blue-600 space-y-1 list-disc list-inside">
                            <li>Asset Number from Accounting</li>
                            <li>Fill warranty in months</li>
                            <li>Personal use requires assignee</li>
                            <li>Other types require a location</li>
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
                                        placeholder="e.g. AST-2024-001"
                                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition" />
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
                                        placeholder="e.g. Lenovo ThinkPad X1"
                                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition" />
                                </div>
                            </div>

                            {{-- Asset Type --}}
                            <div>
                                <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Asset Type <span class="text-red-500">*</span>
                                </label>
                                <select name="asset_type" id="asset_type"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Asset Type --</option>
                                    <option value="Laptop">Laptop / Notebook</option>
                                    <option value="Desktop">Desktop / PC</option>
                                    <option value="Server">Server</option>
                                    <option value="Printer">Printer</option>
                                    <option value="Scanner">Scanner</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Network">Network Device</option>
                                    <option value="Storage">Storage / NAS</option>
                                    <option value="UPS">UPS</option>
                                    <option value="Smartphone">Smartphone</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Software">Software / License</option>
                                    <option value="Peripheral">Peripheral</option>
                                </select>
                            </div>

                            {{-- Owned Category --}}
                            <div>
                                <label for="acquistion_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Owned Category <span class="text-red-500">*</span>
                                </label>
                                <select name="acquistion_type" id="acquistion_type"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Owned Category --</option>
                                    <option value="Purchased New">Purchased New</option>
                                    <option value="Purchased Secondhand">Purchased Secondhand</option>
                                    <option value="Rented">Rented from Supplier</option>
                                    <option value="Loaned">Loaned from Vendor</option>
                                    <option value="Other">Other</option>
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
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Supplier --</option>
                                </select>
                            </div>

                            {{-- Purchase Date --}}
                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Purchase Date
                                </label>
                                <input type="date" name="purchase_date" id="purchase_date"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400 transition" />
                            </div>

                            {{-- Warranty --}}
                            <div>
                                <label for="warranty" class="block text-sm font-medium text-gray-700 mb-1">
                                    Warranty
                                </label>
                                <div class="relative">
                                    <input type="number" name="warranty" id="warranty" min="0"
                                        placeholder="0"
                                        class="w-full pl-3 pr-16 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400 transition" />
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-gray-400 pointer-events-none">months</span>
                                </div>
                            </div>

                            {{-- Condition --}}
                            <div class="md:col-span-2">
                                <label for="conditions" class="block text-sm font-medium text-gray-700 mb-1">
                                    Condition
                                </label>
                                <select name="conditions" id="conditions"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Condition --</option>
                                    <option value="Good">Good</option>
                                    <option value="Broken but still usable">Broken but still usable</option>
                                    <option value="Damaged and cannot be used">Damaged and can't be used</option>
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
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Assignment Type --</option>
                                    <option value="Personal">Personal Use</option>
                                    <option value="Department">Department Use</option>
                                    <option value="Shared">Shared Use</option>
                                    <option value="Spare">Spare</option>
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
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose PIC --</option>
                                </select>
                                </div>
                            </div>

                            {{-- Location (Department/Shared/Spare) --}}
                            <div id="locationGroup" class="hidden">
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location <span class="text-red-500">*</span>
                                </label>
                                <select name="location" id="location"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    @php
                                        $locations = [
                                            'Ruang General Affair', 'Ruang HR', 'Ruang Server',
                                            'Pantry', 'Lobby Lt.1', 'R. Accounting', 'R. Purchasing & Marketing', 'Resepsionis',
                                            'Ruang Office LT.1', 'Ruang Office LT.2',
                                            'Ruang Bima', 'Ruang Arjuna', 'Ruang Srikandi', 'Ruang Yudhistira',
                                        ];
                                    @endphp
                                    <option value="">-- Choose Location --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Location Update (same trigger as locationGroup) --}}
                            <div id="locationUpdateGroup" class="hidden">
                                <label for="location_update" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location Update
                                </label>
                                <select name="location_update" id="location_update"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                    <option value="">-- Choose Updated Location --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Fill if asset location has been updated from initial placement.
                                </p>
                            </div>

                            <div id="assignedToDept" class="hidden col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Person in Charge (PIC) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <i data-feather="user" class="w-4 h-4 text-gray-400"></i>
                                    </span>
                                     <input type="text" name="assigned_to" id="assigned_to"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400">
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" id="note" rows="3"
                            placeholder="Any additional information about this asset..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-indigo-400 resize-none transition"></textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── FOOTER ACTIONS ── --}}
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('it.assets.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition shadow-sm">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back
            </a>

            <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
                <i data-feather="save" class="w-4 h-4"></i> Save Asset
            </button>
        </div>

    </form>
</div>

{{-- ── STYLES ── --}}
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
    border-color: #818cf8;
    box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2);
}
.select2-dropdown {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    font-size: 14px;
}
.select2-results__option--highlighted {
    background-color: #eef2ff !important;
    color: #4338ca !important;
}
</style>

@push('scripts')
<script>
$(document).ready(function () {

    feather.replace();

    // ========================
    // IMAGE UPLOAD / DROPZONE
    // ========================
    const $dropzone       = $('#dropzone');
    const $dropContent    = $('#dropzoneContent');
    const $previewImg     = $('#previewImage');
    const $resetBtn       = $('#resetPhoto');
    const $photoInput     = $('#photo');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $previewImg.attr('src', e.target.result).removeClass('hidden');
            $dropContent.addClass('hidden');
            $resetBtn.removeClass('hidden');
            $dropzone.removeClass('border-dashed').addClass('border-transparent');
        };
        reader.readAsDataURL(file);
    }

    function resetPreview() {
        $previewImg.attr('src', '').addClass('hidden');
        $dropContent.removeClass('hidden');
        $resetBtn.addClass('hidden');
        $photoInput.val('');
        $dropzone.addClass('border-dashed').removeClass('border-transparent');
    }

    $photoInput.on('change', function () { showPreview(this.files[0]); });
    $resetBtn.on('click', function (e) { e.stopPropagation(); resetPreview(); });

    $dropzone.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('border-indigo-400 bg-indigo-50/50');
    }).on('dragleave', function () {
        $(this).removeClass('border-indigo-400 bg-indigo-50/50');
    }).on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('border-indigo-400 bg-indigo-50/50');
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
    $('#assignment_type').on('change', function () {
        const val = $(this).val();

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
    });

    // ========================
    // FORM SUBMIT
    // ========================
    $('#asset-form').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).html(
            '<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving...'
        );

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('it.assets.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: res.message || 'Asset registered!',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true
                    });
                    setTimeout(() => { window.location.href = "{{ route('it.assets.index') }}"; }, 2000);
                } else {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'error',
                        title: res.message || 'Failed to save asset.',
                        showConfirmButton: false, timer: 3000
                    });
                    $btn.prop('disabled', false).html('<i data-feather="save" class="w-4 h-4 inline"></i> Save Asset');
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
                $btn.prop('disabled', false).html('<i data-feather="save" class="w-4 h-4 inline"></i> Save Asset');
                feather.replace();
            }
        });
    });

});
</script>
@endpush

@endsection