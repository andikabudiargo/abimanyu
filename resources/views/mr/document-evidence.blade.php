<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evidence Lampiran Copies</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .department-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }

        .meta {
            margin-bottom: 6px;
        }

        .evidence-wrapper {
            margin-top: 20px;
            text-align: center;
        }

        .evidence-img {
            max-width: 100%;
            max-height: 700px;
            object-fit: contain;
        }

        .no-evidence {
            margin-top: 60px;
            text-align: center;
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($document->copies as $index => $copy)

    <div class="title">
        LAMPIRAN EVIDENCE DISTRIBUSI DOKUMEN<br>
        <i>DOCUMENT DISTRIBUTION EVIDENCE ATTACHMENT</i>
    </div>

    <div class="department-label">
        Department : {{ $copy->department->name ?? '-' }}
    </div>

    <div class="meta">
        <strong>No. Copy Document :</strong> {{ $index + 1 }}
    </div>

    <div class="meta">
        <strong>Penerima :</strong> {{ $copy->socialized_by ?? '-' }}
    </div>

    <div class="meta">
        <strong>Diterima Tanggal :</strong>
        {{ $copy->socialization_date
            ? \Carbon\Carbon::parse($copy->socialization_date)->format('d-m-Y')
            : '-' }}
    </div>

    @php
    $evidencePath = null;

    if (!empty($copy->evidence_path)) {
        $registration = $copy->registration;

        $docType = strtolower(
            str_replace(' ', '_', $registration->document_type ?? 'document')
        );

        $deptFrom = $registration->department_id ?? 0;

        $fullPath = "/home/abimany3/public_html/documents/{$docType}/{$deptFrom}/" . $copy->evidence_path;

        if (file_exists($fullPath)) {
            $mime = mime_content_type($fullPath);

            $evidencePath = 'data:' . $mime . ';base64,' . base64_encode(
                file_get_contents($fullPath)
            );
        }
    }
@endphp

<div class="evidence-wrapper">
    @if($evidencePath)
        <img
            src="{{ $evidencePath }}"
            class="evidence-img"
            alt="Evidence"
        >
    @else
        <div class="no-evidence">
            Evidence not available
        </div>
    @endif
</div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>