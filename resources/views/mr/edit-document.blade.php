@extends('layouts.app')

@section('title', 'Edit Document - ' . $document->document_number)
@section('page-title', 'EDIT DOCUMENT')
@section('breadcrumb-item', 'Document Archive')
@section('breadcrumb-active', 'Edit Document')

@section('content')

@php
    $isResubmit = in_array($document->status, ['Returned by SPV', 'Returned by MR']);
    $status     = $document->status ?? 'Submitted';

    $badgeClass = match($status) {
        'Submitted'           => 'bg-gray-100 text-gray-600 border border-gray-300',
        'Resubmitted'         => 'bg-indigo-100 text-indigo-600 border border-indigo-300',
        'Returned by SPV' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Returned by MR'  => 'bg-amber-50 text-amber-700 border border-amber-200',
        default           => 'bg-gray-100 text-gray-600 border border-gray-300',
    };
@endphp

<div class="flex flex-col md:flex-row gap-2">

  {{-- ============ LEFT SIDEBAR ============ --}}
  <div class="w-full md:w-1/3 bg-white rounded-xl border border-gray-200 shadow-sm mb-4">

    <!-- HEADER -->
    <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
            Department Destination
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Departemen yang dituju
        </p>
    </div>

    <form id="doc-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- DEPARTMENT -->
        <div class="p-5 space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-800">Department</h3>
                <p class="text-xs text-gray-500">department dedicated to executing document content</p>
            </div>

            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Department</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr>
                            <td class="px-3 py-2">
                                <select id="department" name="department_id" required
                                    class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ $document->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TEMPLATE DOWNLOAD -->
        <div class="p-5 space-y-4">
            <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-4">
                <h3 class="font-semibold mb-2">Download Template Dokumen :</h3>
                <ul class="list-none space-y-1 mb-4 text-sm">
                    <li>* Download Template Form:
                        <a href="{{ asset('blank/BLANK FORM.xlsx') }}" download class="text-blue-600 hover:underline">BLANK FORM.xlsx</a>
                    </li>
                    <li>* Download Template SOP:
                        <a href="{{ asset('blank/BLANK SOP.xlsx') }}" download class="text-blue-600 hover:underline">BLANK SOP.xlsx</a>
                    </li>
                    <li>* Download Template Instruksi Kerja General:
                        <a href="{{ asset('blank/BLANK IK.xlsx') }}" download class="text-blue-600 hover:underline">BLANK IK.xlsx</a>
                    </li>
                    <li>* Download Template Instruksi Kerja Produksi / Quality:
                        <a href="{{ asset('blank/BLANK IK FOR PRODUKSI DAN QUALITY.xlsx') }}" download class="text-blue-600 hover:underline">BLANK IK PRODUKSI / QUALITY.xlsx</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ============ RIGHT MAIN FORM ============ --}}
        {{-- (form tag wraps both sides) --}}
    </form>
  </div>

  {{-- ============ RIGHT CONTENT ============ --}}
  <div class="w-full md:w-2/3 bg-white shadow-lg rounded-2xl p-8 space-y-8 mb-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start border-b pb-4">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 tracking-tight">
                {{ $isResubmit ? 'Resubmit Document' : 'Edit Document' }}
            </h1>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-gray-400 text-sm"></i>
                {{ $isResubmit
                    ? 'Revise and resubmit your returned document'
                    : 'Update your draft document before submission' }}
            </p>
        </div>
        <div class="flex sm:items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                {{ $status }}
            </span>
        </div>
    </div>

    {{-- Return note if resubmit --}}
    @if($isResubmit)
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
        <i data-feather="alert-circle" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Document was returned</p>
           @if($document->returned_reason ?? false)
    <p class="text-sm text-amber-700 mt-0.5">
        {{ collect($document->returned_reason)->last() }}
    </p>
@endif
            <p class="text-xs text-amber-600 mt-1">Please revise accordingly and save to resubmit.</p>
        </div>
    </div>
    @endif

    <form id="doc-form-main" enctype="multipart/form-data">
        @csrf
        @method('PUT')

    <section class="space-y-6">

        {{-- DOCUMENT TYPE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Document Type <span class="text-red-600">*</span>
            </label>

            @php
                $knownTypes = ['Form', 'Work Instructions', 'Standard', 'SOP'];
                $currentType = $document->document_type ?? 'Form';
                $isOtherType = !in_array($currentType, $knownTypes);
            @endphp

            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-6">
                @foreach($knownTypes as $type)
                <label class="flex items-center space-x-2">
                    <input type="radio" name="document_type" value="{{ $type }}" class="docType"
                        {{ (!$isOtherType && $currentType === $type) ? 'checked' : '' }}>
                    <span>{{ $type }}</span>
                </label>
                @endforeach

                <label class="flex items-center space-x-2">
                    <input type="radio" name="document_type" value="other" id="otherRadio"
                        {{ $isOtherType ? 'checked' : '' }}>
                    <span>Other</span>
                </label>
            </div>

            <input type="text" name="document_type_other" id="otherInput"
                value="{{ $isOtherType ? $currentType : '' }}"
                placeholder="Determine your own document type. e.g: Manual Instruction, Modul, etc..."
                class="{{ $isOtherType ? '' : 'hidden' }} mt-3 w-full px-3 py-2.5 border rounded-lg shadow-sm">
        </div>

        {{-- SUBMISSION TYPE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Submission Type <span class="text-red-600">*</span>
            </label>

            <div class="flex flex-wrap gap-2">
                @foreach(['New Release', 'Revision', 'Obsolete'] as $subType)
                @php
                    $peerId = str_replace(' ', '_', strtolower($subType));
                    $colorMap = [
                        'New Release' => 'green',
                        'Revision'    => 'yellow',
                        'Obsolete'    => 'red',
                    ];
                    $c = $colorMap[$subType];
                @endphp
                <input type="radio" name="submission_type" id="{{ $peerId }}"
                    value="{{ $subType }}" class="peer/{{ $peerId }} hidden"
                    {{ ($document->submission_type ?? 'New Release') === $subType ? 'checked' : '' }}>
                <label for="{{ $peerId }}"
                    class="px-4 py-2 border rounded-lg cursor-pointer text-sm font-medium transition
                           text-gray-700 hover:bg-gray-50
                           peer-checked/{{ $peerId }}:bg-{{ $c }}-50
                           peer-checked/{{ $peerId }}:border-{{ $c }}-300
                           peer-checked/{{ $peerId }}:text-{{ $c }}-700">
                    {{ $subType }}
                </label>
                @endforeach
            </div>
        </div>

        {{-- DOCUMENT NUMBER + REVISION --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Document Number <span class="text-red-600">*</span>
                </label>

                {{-- New Release → text input (readonly since editing) --}}
                <input type="text" id="doc_number_input" name="document_number"
                    value="{{ $document->document_number }}"
                    class="w-full px-3 py-2.5 border rounded-lg bg-gray-50 {{ $document->submission_type !== 'New Release' ? 'hidden' : '' }}"
                    {{ $document->submission_type === 'New Release' ? 'readonly' : '' }}>

                {{-- Revision / Obsolete → select --}}
                <select id="doc_number_select" name="document_number_select"
                    class="{{ $document->submission_type === 'New Release' ? 'hidden' : '' }} w-full px-3 py-2.5 border rounded-lg">
                    <option value="">Select Document</option>
                    {{-- Filled via AJAX --}}
                </select>

                <small id="last_doc_info" class="text-gray-500 {{ $document->submission_type === 'New Release' ? 'hidden' : '' }}">
                    Last Document Number: <span id="last_doc_value">{{ $document->document_number }}</span>
                </small>
            </div>

            <div id="revision_group" class="{{ $document->submission_type === 'Revision' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Revision No</label>
                <input type="text" name="revision_number"
                    value="{{ $document->revision_number }}"
                    class="w-full px-3 py-2.5 border rounded-lg">
            </div>
        </div>

        {{-- DOCUMENT TITLE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Document Title <span class="text-red-600">*</span>
            </label>
            <input type="text" name="document_title"
                value="{{ $document->document_title }}"
                class="w-full px-3 py-2.5 border rounded-lg">
        </div>

        {{-- REASON --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Reason for Submission
            </label>
            <textarea name="reason" rows="4" class="w-full border rounded px-3 py-2">{{ $document->reason }}</textarea>
        </div>

        {{-- BEFORE / AFTER CHANGES (Revision only) --}}
        <div id="changes_group" class="{{ $document->submission_type === 'Revision' ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Before Changes</label>
                <textarea name="before_change" rows="4" class="w-full border rounded px-3 py-2">{{ $document->before_change }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">After Changes</label>
                <textarea name="after_change" rows="4" class="w-full border rounded px-3 py-2">{{ $document->after_change }}</textarea>
            </div>
        </div>

        {{-- 4M QUESTION --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                4M Attachment Needed?
            </label>
          <label>
    <input type="radio" name="need_4m" value=0
        {{ !$document->need_4m ? 'checked' : '' }}> No
</label>

<label class="ml-4">
    <input type="radio" name="need_4m" value=1
        {{ $document->need_4m ? 'checked' : '' }}> Yes
</label>
        </div>

        {{-- MAIN DOCUMENT FILE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
              <div id="file_4m_group" class="col-span-2 bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-4 {{ $document->need_4m ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    4M Attachment File <small class="text-red-600">*</small>
                </label>

                {{-- Existing file --}}
                @if($document->file_4m_path)
                <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-3 py-2 mb-3">
                    <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-bold flex-shrink-0">
                        {{ strtoupper(pathinfo($document->file_4m_path, PATHINFO_EXTENSION)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ basename($document->file_4m_path) }}</p>
                        <p class="text-xs text-gray-400">Current file – upload below to replace</p>
                    </div>
                    <a href="{{ asset('document/4m/' . $document->file_4m_path) }}" download
                       class="text-xs text-blue-600 hover:underline flex-shrink-0">
                        <i data-feather="download" class="w-3.5 h-3.5 inline"></i> Download
                    </a>
                </div>
                @endif

                <input type="file" name="file_4m_path" id="file"
                    class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <p class="text-xs text-gray-500 mt-1">Allowed: PDF, XLSX, DOCX. Max: 5MB/file.
                    @if($document->file_4m_path)
                        Leave empty to keep current file.
                    @endif
                </p>
            </div>

            <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Document File <small class="text-red-600">*</small>
                </label>

                {{-- Existing file --}}
                @if($document->file_path)
                <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-3 py-2 mb-3">
                    <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-bold flex-shrink-0">
                        {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ basename($document->file_path) }}</p>
                        <p class="text-xs text-gray-400">Current file – upload below to replace</p>
                    </div>
                    <a href="{{ asset('document/' . $document->file_path) }}" download
                       class="text-xs text-blue-600 hover:underline flex-shrink-0">
                        <i data-feather="download" class="w-3.5 h-3.5 inline"></i> Download
                    </a>
                </div>
                @endif

                <input type="file" name="file_path" id="file"
                    class="w-full border border-gray-300 rounded shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <p class="text-xs text-gray-500 mt-1">Allowed: PDF, XLSX, DOCX. Max: 5MB/file.
                    @if($document->file_path)
                        Leave empty to keep current file.
                    @endif
                </p>
            </div>
        </div>

    </section>

    {{-- ACTION BUTTONS --}}
    <div class="flex justify-start items-center gap-2 mt-4">
        <a href="{{ route('mr.doc.detail', $document->id) }}"
           class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
            ← Back
        </a>

        <button type="submit" id="submitBtn"
            class="flex items-center justify-center gap-2 px-5 py-2
                   {{ $isResubmit ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }}
                   text-white rounded shadow">
            @if($isResubmit)
                <i data-feather="refresh-ccw" class="w-4 h-4"></i> Resubmit
            @else
                <i data-feather="save" class="w-4 h-4"></i> Save Changes
            @endif
        </button>
    </div>

    </form>
  </div>
</div>

<!-- MODAL -->
<div id="deptModal" class="fixed inset-0 hidden items-center justify-center z-50">

    <!-- overlay -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

    <!-- modal -->
    <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6 animate-scaleIn">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i data-feather="copy" class="w-4 h-4"></i>
                    Copy Share Document
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Distribute document copies to selected departments
                </p>
            </div>

            <button type="button"
                class="cancelModal w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition">
                ✕
            </button>
        </div>

        <!-- BODY -->
        <div id="deptContainer"
            class="space-y-3 max-h-72 overflow-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300">

          

            {{-- EDIT MODE --}}
            @foreach($document->copies ?? [] as $copy)
            <div class="flex gap-2 items-center dept-row">
                <select name="share_dept[]"
                    class="select-dept flex-1 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $copy->department_id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="share_qty[]" min="1" value="{{ $copy->qty }}"
                    class="w-20 border border-gray-300 rounded-xl px-2 py-2 text-center text-sm focus:ring-2 focus:ring-indigo-500">

                <button type="button"
                    class="removeRow w-8 h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-50 transition">
                    ✕
                </button>
            </div>
            @endforeach

              {{-- DEFAULT ROW --}}
            <div class="flex gap-2 items-center dept-row">
                <select name="share_dept[]"
                    class="select-dept flex-1 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>

                <input type="number" name="share_qty[]" min="1" value="1"
                    class="w-20 border border-gray-300 rounded-xl px-2 py-2 text-center text-sm focus:ring-2 focus:ring-indigo-500">

                <button type="button"
                    class="removeRow w-8 h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-50 transition">
                    ✕
                </button>
            </div>

        </div>

        <!-- ADD BUTTON -->
        <button type="button" id="addRow"
            class="mt-4 inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
            <i data-feather="plus" class="w-4 h-4"></i>
            Add Department
        </button>

        <!-- FOOTER -->
        <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
            <button type="button"
                class="cancelModal px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
                Cancel
            </button>

            <button type="button" id="confirmSave"
                class="px-5 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition">
                Save Changes
            </button>
        </div>
    </div>
</div>

<style>
.select2-container { width: 100% !important; }
.select2-container .select2-selection--single {
    height: 42px !important;
    display: flex !important;
    align-items: center !important;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0 0.75rem !important;
}
.select2-container .select2-selection__rendered {
    padding-left: 0 !important;
    padding-right: 0 !important;
    line-height: 42px !important;
    font-size: 15px;
    color: #374151;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
    right: 0.75rem;
}
</style>

@push('scripts')
<script>

     function initSelect2Dept() {
    $('.select-dept').select2({
        width: '100%',
        placeholder: '-- Choose Department --',
        allowClear: true,
        dropdownParent: $('#deptModal') // 🔥 wajib untuk modal
    });
}

$(document).ready(function () {

initSelect2Dept();
    // ========================
    // SELECT2
    // ========================
    $('#department').select2({
        width: '100%',
        placeholder: '-- Select Department --',
        allowClear: true
    });

    feather.replace();

    // ========================
    // SUBMISSION TYPE TOGGLE
    // ========================
    $("input[name='submission_type']").on("change", function () {
        const val = $(this).val();

        if (val === "New Release") {
            $("#doc_number_input").removeClass("hidden").attr('readonly', true);
            $("#doc_number_select").addClass("hidden");
            $("#revision_group, #changes_group").addClass("hidden");
            $("#last_doc_info").addClass("hidden");
        }

        if (val === "Revision") {
            $("#doc_number_input").addClass("hidden");
            $("#doc_number_select").removeClass("hidden");
            $("#revision_group, #changes_group").removeClass("hidden");
            loadDocumentNumber('Revision');
        }

        if (val === "Obsolete") {
            $("#doc_number_input").addClass("hidden");
            $("#doc_number_select").removeClass("hidden");
            $("#revision_group, #changes_group").addClass("hidden");
            loadDocumentNumber('Obsolete');
        }
    });

    // ========================
    // DOCUMENT TYPE → load doc numbers
    // ========================
    function loadDocumentNumber(submissionType) {
        const docType = $('input[name="document_type"]:checked').val();

        $.ajax({
            url: '/mr/get-document-number',
            type: 'GET',
            data: { document_type: docType },
            success: function (data) {
                const $select = $('#doc_number_select');
                const $info   = $('#last_doc_info');
                const $value  = $('#last_doc_value');

                $select.html('<option value="">Select Document</option>');

                if (data.length > 0) {
                    $.each(data, function (i, doc) {
                        const selected = doc.document_number === '{{ $document->document_number }}' ? 'selected' : '';
                        $select.append(`<option value="${doc.document_number}" ${selected}>${doc.document_number}</option>`);
                    });
                    $info.removeClass('hidden');
                    $value.text(data[0].document_number);
                } else {
                    $info.addClass('hidden');
                }
            }
        });
    }

    // Trigger on doc type change
    $('.docType').on('change', function () {
        const subType = $('input[name="submission_type"]:checked').val();
        if (subType !== 'New Release') loadDocumentNumber(subType);
    });

    // Init load if revision/obsolete
    const initSubType = $('input[name="submission_type"]:checked').val();
    if (initSubType !== 'New Release') loadDocumentNumber(initSubType);

    // ========================
    // OTHER DOC TYPE
    // ========================
    $('.docType, #otherRadio').on('change', function () {
        if ($('#otherRadio').is(':checked')) {
            $('#otherInput').removeClass('hidden').focus();
        } else {
            $('#otherInput').addClass('hidden').val('');
        }
    });

    // ========================
    // 4M TOGGLE
    // ========================
   $("input[name='need_4m']").on("change", function () {
    if ($(this).val() === "1") {
        $("#file_4m_group").removeClass("hidden");
    } else {
        $("#file_4m_group").addClass("hidden");
    }
});

    // ========================
    // MODAL HELPERS
    // ========================
    function toggleRemoveBtn() {
        const rows = $('.dept-row');
        if (rows.length === 1) {
            rows.find('.removeRow').prop('disabled', true).addClass('opacity-40');
        } else {
            rows.find('.removeRow').prop('disabled', false).removeClass('opacity-40');
        }
    }

  $('#addRow').on('click', function () {
    let row = `
    <div class="flex gap-2 items-center dept-row">
        <select name="share_dept[]"
            class="select-dept flex-1 border border-gray-300 rounded-xl px-3 py-2 text-sm">
            <option value=""></option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>

        <input type="number" name="share_qty[]" min="1" value="1"
            class="w-20 border border-gray-300 rounded-xl px-2 py-2 text-center text-sm">

        <button type="button"
            class="removeRow w-8 h-8 flex items-center justify-center rounded-full text-red-500 hover:bg-red-50">
            ✕
        </button>
    </div>
    `;

    $('#deptContainer').append(row);

    // 🔥 init select2 hanya untuk yang baru
    $('#deptContainer .select-dept').last().select2({
        width: '100%',
        placeholder: '-- Choose Department --',
        allowClear: true,
        dropdownParent: $('#deptModal')
    });
});

$(document).on('click', '.removeRow', function () {
    $(this).closest('.dept-row').remove();
    toggleRemoveBtn();
});

// tombol X
$('.cancelModal').on('click', closeModal);

// klik backdrop (area gelap)
$('#deptModal').on('click', function (e) {
    if (e.target === this) {
        closeModal();
    }
});

function closeModal() {
    $('#deptModal').addClass('hidden').removeClass('flex');
    finalSubmit = false;
}


    function validateDeptForm() {
        let valid = true;
        $('.dept-row').each(function () {
            const dept = $(this).find('select').val();
            const qty  = parseInt($(this).find('input').val());
            if (!dept) { showToast('error', 'Departemen harus dipilih!'); valid = false; return false; }
            if (!qty || qty <= 0) { showToast('error', 'Qty harus lebih dari 0!'); valid = false; return false; }
        });
        return valid;
    }

    function appendDeptToFormData(formData) {
        $('select[name="share_dept[]"]').each(function (i) {
            formData.append(`share_dept[${i}]`, $(this).val());
        });
        $('input[name="share_qty[]"]').each(function (i) {
            formData.append(`share_qty[${i}]`, $(this).val());
        });
    }

    // ========================
    // CONFIRM SAVE (modal)
    // ========================
    let finalSubmit = false;

    $('#confirmSave').on('click', function (e) {
        e.preventDefault();
        if (!validateDeptForm()) return;
        finalSubmit = true;
        $('#deptModal').addClass('hidden').removeClass('flex');
        $('#doc-form-main').submit();
    });

    // ========================
    // FORM SUBMIT
    // ========================
    $('#doc-form-main').off('submit').on('submit', function (e) {
        e.preventDefault();

        if (!finalSubmit) {
            $('#deptModal').removeClass('hidden').addClass('flex');
            toggleRemoveBtn();
            return;
        }

        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).html('<i data-feather="loader" class="w-4 h-4 animate-spin"></i> Saving...');
        feather.replace();

        const formData = new FormData(this);

        // DOCUMENT NUMBER
        let docNumber = !$('#doc_number_input').hasClass('hidden')
            ? $('#doc_number_input').val()
            : $('#doc_number_select').val();

        if (!docNumber) {
            showToast('error', 'Document number wajib diisi!');
            $btn.prop('disabled', false);
            finalSubmit = false;
            return;
        }

        formData.delete('document_number');
        formData.append('document_number', docNumber);

        // DOCUMENT TYPE
        let selectedType = $('input[name="document_type"]:checked').val();
        if (selectedType === 'other') {
            const otherVal = $('#otherInput').val().trim();
            if (!otherVal) {
                showToast('error', 'Isi document type lainnya!');
                $btn.prop('disabled', false);
                finalSubmit = false;
                return;
            }
            formData.set('document_type', otherVal);
        } else {
            formData.set('document_type', selectedType);
        }

        // DEPARTMENT (from sidebar form)
        const deptId = $('#department').val();
        if (deptId) formData.set('department_id', deptId);

        // SHARE DEPT
        appendDeptToFormData(formData);

        const need4m = $('input[name="need_4m"]:checked').val() || 0 ;
formData.set('need_4m', need4m);

if (need4m === 1) {
    showToast('error', 'Pilih Need 4M!');
    $btn.prop('disabled', false);
    finalSubmit = false;
    return;
}

const isResubmit = {{ $isResubmit ? 'true' : 'false' }};
formData.set('is_resubmit', isResubmit ? 1 : 0);

        // AJAX
        $.ajax({
            url: '{{ route("mr.doc.update", $document->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    showToast('success', res.message || 'Document updated successfully!');
                    setTimeout(() => {
                        window.location.href = '{{ route("mr.doc.detail", $document->id) }}';
                    }, 2000);
                } else {
                    showToast('error', res.message || 'Gagal update dokumen.');
                    $btn.prop('disabled', false);
                    finalSubmit = false;
                }
            },
            error: function (err) {
                const msg = err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
                showToast('error', msg);
                $btn.prop('disabled', false);
                finalSubmit = false;
            }
        });
    });

    // ========================
    // TOAST
    // ========================
    function showToast(icon, title) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: icon,
            title: title
        });
    }

    toggleRemoveBtn();
});
</script>
@endpush

@endsection