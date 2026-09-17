@extends('layouts.app')

@section('title', 'Document Portal')
@section('page-title', 'DOCUMENT PORTAL')
@section('breadcrumb-item', 'Document Management')
@section('breadcrumb-active', 'Document Portal')

@section('content')

<style>
*, *::before, *::after { box-sizing: border-box; }

.c-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; }
.c-card-header { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }
.c-card-body { padding: 16px; }
.c-section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; }

.f-input {
    width: 100%; padding: 8px 11px; border: 1px solid #d1d5db; border-radius: 4px;
    font-size: 13px; color: #111827; background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s; line-height: 1.4;
}
.f-input:focus { border-color: #1e3a5f; box-shadow: 0 0 0 2px rgba(30,58,95,.10); }
.f-input::placeholder { color: #9ca3af; }

.btn {
    display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px;
    border-radius: 4px; font-size: 12px; font-weight: 500;
    border: 1px solid transparent; cursor: pointer; transition: all .15s; white-space: nowrap;
}
.btn-primary   { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
.btn-primary:hover { background: #162d4a; color: #fff; }
.btn-secondary { background: #fff; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #f9fafb; }
.btn-danger    { background: #fff; color: #991b1b; border-color: #fca5a5; }
.btn-danger:hover { background: #fef2f2; }
.btn.is-active { background: #f0f4f9; border-color: #1e3a5f; color: #1e3a5f; }
.btn svg { width: 13px; height: 13px; flex-shrink: 0; }

.doc-row {
    display: flex; align-items: center; gap: 10px; padding: 9px 10px; width: 100%;
    border: 1px solid #e5e7eb; border-radius: 5px; background: #fff; cursor: pointer;
    text-align: left; transition: border-color .15s, background .15s;
}
.doc-row:hover { border-color: #9ca3af; background: #f9fafb; }
.doc-row.is-selected { border-color: #1e3a5f; background: #f0f4f9; }
.doc-row.is-obsolete { opacity: .6; }
.doc-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.doc-icon svg { width: 15px; height: 15px; }

.type-purple { background: #f3ecfb; color: #6b21a8; }
.type-green  { background: #ecfdf5; color: #166534; }
.type-amber  { background: #fffbeb; color: #92400e; }
.type-blue   { background: #eff6ff; color: #1e40af; }
.type-gray   { background: #f3f4f6; color: #374151; }

.badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 999px; }

.tab-btn {
    padding: 8px 4px; margin-right: 16px; font-size: 12px; font-weight: 500; color: #9ca3af;
    background: none; border: none; border-bottom: 2px solid transparent; cursor: pointer;
}
.tab-btn.is-active { color: #1e3a5f; border-bottom-color: #1e3a5f; }
.tab-panel { display: none; }
.tab-panel.is-active { display: block; }

.history-row { display: flex; gap: 10px; }
.history-dot { width: 8px; height: 8px; border-radius: 50%; background: #1e3a5f; margin-top: 5px; flex-shrink: 0; }
.history-line { flex: 1; width: 1px; background: #e5e7eb; margin: 2px 0; }
.file-chip {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 4px; padding: 6px 9px; margin: 4px 0;
}
.file-chip a { color: #6b7280; }
.file-chip a:hover { color: #1e3a5f; }

.review-table { width: 100%; }
.review-table td { padding: 7px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; vertical-align: top; }
.review-table tr:last-child td { border-bottom: none; }
.review-table .r-key { color: #6b7280; width: 160px; }
.review-table .r-val { color: #111827; font-weight: 500; }

.empty-state { text-align: center; color: #9ca3af; font-size: 13px; padding: 60px 20px; }
</style>

<div class="c-card mb-4" style="padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
    <div>
        <h1 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">Document Portal</h1>
        <p style="font-size: 12px; color: #6b7280; margin: 3px 0 0;">Cari, lihat, dan kelola seluruh dokumen yang sudah terpublikasi</p>
    </div>
    <a href="{{ route('mr.doc.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Dokumen Baru
    </a>
</div>

<div class="c-card mb-4" style="padding: 14px 16px;">
    <div class="flex flex-wrap gap-2">
        <input type="text" id="searchInput" class="f-input" placeholder="Cari judul atau nomor dokumen..." style="flex: 1; min-width: 220px;">
        <select id="filterDept" class="f-input" style="width: 220px;">
            <option value="">Semua Departemen</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
        <select id="filterType" class="f-input" style="width: 180px;">
            <option value="">Semua Tipe</option>
            <option value="SOP">SOP</option>
            <option value="Form">Form</option>
            <option value="Work Instructions">Work Instructions</option>
            <option value="Standard">Standard</option>
            <option value="other">Other</option>
        </select>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-4">

    <div class="w-full lg:w-2/5">
        <div class="c-card">
            <div class="c-card-header" style="justify-content: space-between;">
                <span class="c-section-label">Daftar Dokumen</span>
                <span id="docCount" class="text-xs text-gray-400"></span>
            </div>
            <div id="docList" class="c-card-body" style="display: flex; flex-direction: column; gap: 6px; max-height: 640px; overflow-y: auto;">
                <div class="empty-state">Memuat dokumen...</div>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-3/5">
        <div class="c-card">
            <div class="c-card-body" id="detailBody">
                <div class="empty-state">Pilih dokumen di sebelah kiri untuk melihat detail.</div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
const TYPE_STYLE = {
    'SOP': { icon: 'clipboard', cls: 'type-purple' },
    'Form': { icon: 'file-text', cls: 'type-green' },
    'Work Instructions': { icon: 'tool', cls: 'type-amber' },
    'Standard': { icon: 'bookmark', cls: 'type-blue' },
};
function typeStyle(type) {
    return TYPE_STYLE[type] || { icon: 'file', cls: 'type-gray' };
}

function esc(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

let ALL_DOCS = [];
let selectedNumber = null;
let activeTab = 'overview';
let currentDetail = null;

function loadDocList() {
    $.ajax({
        url: '{{ route("mr.doc.portal.data") }}',
        method: 'GET',
        data: {
            q: $('#searchInput').val(),
            department_id: $('#filterDept').val(),
            document_type: $('#filterType').val(),
        },
        success: function (docs) {
            ALL_DOCS = docs;
            renderList();
        }
    });
}

function renderList() {
    const $list = $('#docList');
    $('#docCount').text(ALL_DOCS.length ? ALL_DOCS.length + ' dokumen' : '');

    if (!ALL_DOCS.length) {
        $list.html('<div class="empty-state">Tidak ada dokumen yang cocok.</div>');
        return;
    }

    $list.html(ALL_DOCS.map(function (d) {
        const s = typeStyle(d.document_type);
        const selected = d.document_number === selectedNumber ? ' is-selected' : '';
        const obsolete = !d.is_active ? ' is-obsolete' : '';
        const statusBadge = !d.is_active
            ? '<span class="badge type-gray" style="margin-left:6px;">Obsolete</span>' : '';

        return '<button type="button" class="doc-row' + selected + obsolete + '" data-number="' + esc(d.document_number) + '">'
            + '<span class="doc-icon ' + s.cls + '"><i data-feather="' + s.icon + '"></i></span>'
            + '<span style="flex:1; min-width:0;">'
            +   '<span style="display:block; font-size:12px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + esc(d.document_number) + statusBadge + '</span>'
            +   '<span style="display:block; font-size:11px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + esc(d.document_title || '-') + '</span>'
            + '</span>'
            + '<span style="font-size:11px; color:#9ca3af; flex-shrink:0;">v' + (d.current_version || '00') + '</span>'
            + '</button>';
    }).join(''));

    feather.replace();

    $('.doc-row').on('click', function () {
        selectedNumber = $(this).data('number').toString();
        $('.doc-row').removeClass('is-selected');
        $(this).addClass('is-selected');
        activeTab = 'overview';
        loadDetail(selectedNumber);
    });
}

const HISTORY_URL_TEMPLATE = '{{ route("mr.doc.portal.history", ["number" => "__NUM__"]) }}';

function loadDetail(number) {
    $('#detailBody').html('<div class="empty-state">Memuat detail...</div>');

    $.ajax({
        url: HISTORY_URL_TEMPLATE.replace('__NUM__', encodeURIComponent(number)),
        method: 'GET',
        success: function (res) {
            currentDetail = res;
            renderDetail();
        }
    });
}

function fileExt(url) {
    if (!url) return '';
    return url.split('.').pop().split('?')[0].toLowerCase();
}

function previewFrame(url) {
    if (!url) {
        return '<div class="empty-state" style="padding:40px 20px;">File tidak tersedia untuk dipreview.</div>';
    }
    const ext = fileExt(url);
    const officeExt = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    if (ext === 'pdf') {
        return '<iframe src="' + url + '" style="width:100%; height:420px; border:1px solid #e5e7eb; border-radius:4px;"></iframe>';
    }
    if (officeExt.includes(ext)) {
        return '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(url) + '" style="width:100%; height:420px; border:1px solid #e5e7eb; border-radius:4px;"></iframe>'
            + '<p style="font-size:11px; color:#9ca3af; margin-top:6px;">Jika preview tidak muncul, file mungkin belum bisa diakses publik. <a href="' + url + '" target="_blank" style="color:#1e3a5f;">Buka file asli</a>.</p>';
    }
    return '<div class="empty-state" style="padding:40px 20px;">Tipe file ini belum didukung untuk preview. <a href="' + url + '" target="_blank">Buka / unduh file</a>.</div>';
}

function reviseUrl(mode) {
    const d = currentDetail.document;
    const params = new URLSearchParams({ mode: mode, number: d.document_number, type: d.document_type || '' });
    return '{{ route("mr.doc.create") }}?' + params.toString();
}

function renderDetail() {
    const d = currentDetail ? currentDetail.document : null;
    const history = currentDetail ? currentDetail.history : [];
    const $body = $('#detailBody');

    if (!d) {
        $body.html('<div class="empty-state">Riwayat dokumen tidak ditemukan.</div>');
        return;
    }

    const latest = history[history.length - 1] || {};
    const statusBadge = d.is_active
        ? '<span class="badge" style="background:#f0fdf4; color:#166534;">Published</span>'
        : '<span class="badge" style="background:#f3f4f6; color:#6b7280;">Obsolete</span>';

    let actions = '<a href="' + latest.file_url + '" target="_blank" class="btn btn-secondary"><i data-feather="eye"></i>Preview</a>'
        + '<a href="' + latest.file_url + '" download class="btn btn-secondary"><i data-feather="download"></i>Download</a>';

    if (d.is_active) {
        actions += '<a href="' + reviseUrl('revision') + '" class="btn btn-primary"><i data-feather="edit-3"></i>Revisi Dokumen</a>'
            + '<a href="' + reviseUrl('obsolete') + '" class="btn btn-danger"><i data-feather="archive"></i>Jadikan Obsolete</a>';
    }

    const tabs = [['overview', 'Overview'], ['preview', 'Preview'], ['history', 'Riwayat Versi']];
    const tabBar = tabs.map(function (t) {
        return '<button type="button" class="tab-btn' + (activeTab === t[0] ? ' is-active' : '') + '" data-tab="' + t[0] + '">' + t[1] + '</button>';
    }).join('');

    let overviewHtml = '<table class="review-table">'
        + '<tr><td class="r-key">Departemen</td><td class="r-val">' + esc(d.department || '-') + '</td></tr>'
        + '<tr><td class="r-key">Tipe Dokumen</td><td class="r-val">' + esc(d.document_type || '-') + '</td></tr>'
        + '<tr><td class="r-key">Versi Saat Ini</td><td class="r-val">v' + esc(d.current_version || '00') + '</td></tr>'
        + '<tr><td class="r-key">Diajukan Oleh</td><td class="r-val">' + esc(d.submitted_by) + ' &middot; ' + esc(d.submitted_at) + '</td></tr>'
        + '<tr><td class="r-key">Direvisi Terakhir Oleh</td><td class="r-val">' + esc(d.revised_by) + ' &middot; ' + esc(d.revised_at) + '</td></tr>'
        + '</table>';

    let previewHtml = previewFrame(latest.file_url);

    let historyHtml = history.slice().reverse().map(function (h, i) {
        const isLast = i === history.length - 1;
        let filesHtml = '<div class="file-chip"><span style="font-size:11px; color:#374151; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + esc(h.file_name || '-') + '</span>'
            + '<span><a href="' + h.file_url + '" target="_blank" title="Preview"><i data-feather="eye" style="width:13px;height:13px;"></i></a> '
            + '<a href="' + h.file_url + '" download title="Download"><i data-feather="download" style="width:13px;height:13px;"></i></a></span></div>';

        if (h.file_4m_url) {
            filesHtml += '<div class="file-chip"><span style="font-size:11px; color:#374151;">Lampiran 4M</span>'
                + '<span><a href="' + h.file_4m_url + '" target="_blank" title="Preview"><i data-feather="eye" style="width:13px;height:13px;"></i></a> '
                + '<a href="' + h.file_4m_url + '" download title="Download"><i data-feather="download" style="width:13px;height:13px;"></i></a></span></div>';
        }

        return '<div class="history-row">'
            + '<div style="display:flex; flex-direction:column; align-items:center;"><span class="history-dot"></span>' + (isLast ? '' : '<span class="history-line"></span>') + '</div>'
            + '<div style="padding-bottom:16px; flex:1; min-width:0;">'
            +   '<div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;"><span style="font-size:13px; font-weight:600; color:#111827;">v' + esc(h.version) + '</span>'
            +   '<span class="badge" style="background:#eff6ff; color:#1e40af;">' + esc(h.submission_type) + '</span></div>'
            +   filesHtml
            +   '<p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">' + esc(h.created_by) + ' &middot; ' + esc(h.created_at) + '</p>'
            + '</div></div>';
    }).join('');

    $body.html(
        '<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:6px; flex-wrap:wrap;">'
        +   '<div><p style="font-weight:600; font-size:15px; margin:0; color:#111827;">' + esc(d.document_number) + '</p>'
        +   '<p style="font-size:13px; color:#6b7280; margin:2px 0 0;">' + esc(d.document_title || '-') + '</p></div>'
        +   statusBadge
        + '</div>'
        + '<div style="display:flex; flex-wrap:wrap; gap:6px; margin:12px 0 16px;">' + actions + '</div>'
        + '<div style="border-bottom:1px solid #f3f4f6; margin-bottom:14px;">' + tabBar + '</div>'
        + '<div class="tab-panel' + (activeTab === 'overview' ? ' is-active' : '') + '" id="panel-overview">' + overviewHtml + '</div>'
        + '<div class="tab-panel' + (activeTab === 'preview' ? ' is-active' : '') + '" id="panel-preview">' + previewHtml + '</div>'
        + '<div class="tab-panel' + (activeTab === 'history' ? ' is-active' : '') + '" id="panel-history">' + historyHtml + '</div>'
    );

    feather.replace();

    $('.tab-btn').on('click', function () {
        activeTab = $(this).data('tab');
        renderDetail();
    });
}

$(document).ready(function () {
    loadDocList();

    let searchTimer = null;
    $('#searchInput').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadDocList, 300);
    });
    $('#filterDept, #filterType').on('change', loadDocList);
});
</script>
@endpush
