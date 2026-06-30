<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>CAPA Report</title>

    <style>

        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #222;
        }

        h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            height: 60px;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
        }

        .doc-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
        }

        .section-title {
            background: #f2f2f2;
            padding: 6px;
            font-weight: bold;
            border: 1px solid #999;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table th,
        table td {
            border: 1px solid #999;
            padding: 6px 8px;
            vertical-align: top;
        }

        table th {
            background: #efefef;
            font-weight: bold;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
}

    </style>
</head>
<body>

<!-- ================= HEADER ================= -->

<table width="100%" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <!-- Logo -->
         <td rowspan="4" style="width: 150px; vertical-align: middle; text-align: center;">
            @if($logo)
    <img src="{{ $logo }}" alt="Logo" style="width:140px;">
        @endif 
        </td>

        <!-- Title -->
        <td rowspan="3" colspan="4" style="text-align: center; font-weight: bold; font-size: 23px;">
            CORRECTIVE AND PREVENTIVE ACTION REPORT
        </td>

        <!-- Doc Info -->
        <td style="font-weight: bold;">Doc. No</td>
        <td>:</td>
        <td>MSS-02.03-FM</td>
    </tr>
    <tr>
       
        <td style="font-weight: bold;">Date Used</td>
        <td>:</td>
        <td>23 March 2019</td>
    </tr>
    <tr>
        
        <td style="font-weight: bold;">Rev. No</td>
        <td>:</td>
        <td>02</td>
    </tr>
    <tr>
         <td colspan="4" style="text-align: center; font-size: 12px;">
            PT ABIMANYU SEKAR NUSANTARA
        </td>
        <td style="font-weight: bold;">Page</td>
        <td>:</td>
        <td>1/1</td>
    </tr>
</table>

<!-- ================= BASIC INFO ================= -->

<table border="1" width="100%" cellpadding="5" cellspacing="0">
    <tr>
        <th width="25%">No.</th>
        <td colspan="3">{{ $capa->capa_number }}</td>
        
    </tr>
    <tr>
        <th width="25%">Report Date</th>
        <td colspan="3">{{ $capa->report_date ? \Carbon\Carbon::parse($capa->report_date)->format('d-m-Y') : '-' }}</td>
    </tr>

    <tr>
         <th colspan="2" style="border-bottom:none;">Source of The Finding</th>
        <th colspan="2" style="border-bottom:none;">Category</th>
    </tr>

    <tr>
        <td style="border-bottom:none;">Audit</td>
        <td style="border-bottom:none;">
             @if($capa->source_of_finding == 'Audit')
            ✔
        @else
            ☐
        @endif
        </td>

        <td style="border-bottom:none;">Critical</td>
        <td style="border-bottom:none;">
            @if($capa->category == 'Critical')
            ✔
        @else
            ☐
        @endif
        </td>
    </tr>

    <tr>
        <td style="border-bottom:none;">Complain</td>
        <td style="border-bottom:none;">
             @if($capa->source_of_finding == 'Complain')
            ✔
        @else
            ☐
        @endif
        </td>

        <td style="border-bottom:none;">Minor</td>
        <td style="border-bottom:none;">
             @if($capa->category == 'Minor')
            ✔
        @else
            ☐
        @endif
        </td>
    </tr>

    <tr>
        <td style="border-bottom:none;">Non conformity</td>
        <td style="border-bottom:none;">
             @if($capa->source_of_finding == 'Non Conformity')
            ✔
        @else
            ☐
        @endif
        </td>

        <td style="border-bottom:none;">Major</td>
        <td style="border-bottom:none;">
            @if($capa->category == 'Major')
            ✔
        @else
            ☐
        @endif
        </td>
    </tr>

    <tr>
        <td>Management Review</td>
        <td>
             @if($capa->source_of_finding == 'Management Review')
            ✔
        @else
            ☐
        @endif
        </td>

        <td>Observation</td>
        <td>
             @if($capa->category == 'Observation')
            ✔
        @else
            ☐
        @endif
        </td>
    </tr>
      <tr>
        <th width="25%">Department</th>
        <td width="25%">{{ $capa->department_display }}</td>
        <th width="25%">Auditor</th>
        <td width="25%">
          @if($capa->auditors && $capa->auditors->count())

    {{ 
        $capa->auditors
            ->map(fn($a) => $a->users?->name)
            ->filter()
            ->implode(', ')
    }}

@else
    NA
@endif
</td>
    </tr>
<tr>
        <th width="25%">Dept. Representative</th>
        <td width="25%">{{ $capa->representative->name ?? 'NA'}}</td>
        <th width="25%">Auditee</th>
        <td width="25%">{{ $capa->representative->name ?? 'NA' }}</td>
    </tr>
  <tr>
        <th colspan="4" style="text-align:center; background-color:yellow;">Detail of Information</th>
    </tr>
     <tr>
        <td colspan="4" style="text-align:center;">{{ $capa->detail_of_information}}</td>
    </tr>
     <tr>
        <th colspan="4" style="text-align:center; background-color:yellow;">Problem</th>
    </tr>
     <tr>
        <td colspan="4" style="text-align:center;">{{ $capa->problem ?? "No Problem Finding"}}</td>
    </tr>
      <tr>
        <th colspan="4" style="text-align:center; background-color:yellow;">Root Cause Anlysis</th>
    </tr>
     <tr>
        <td colspan="4" style="text-align:center;">{{ $capa->rca?->description}}</td>
    </tr>
     <tr>
        <th colspan="3" width="70%" style="background-color:yellow;">Corrective Action</th>
        <th style="background-color:yellow; text-align:center;">Due Date</th>
    </tr>
    <tr>
        <td colspan="3" rowspan="3" width="70%">{{ $capa->ca?->description }}</td>
        <td style="text-align:center;">{{ $capa->ca?->due_date ? \Carbon\Carbon::parse($capa->ca?->due_date)->format('d-m-Y') : '-' }}</td>
    </tr>
    <tr>
        <th style="background-color:yellow; text-align:center;">PIC</th>
    </tr>
    <tr>
        <td style="text-align:center;">{{ $capa->ca?->picUser->name }}</td>
    </tr>
    <tr>
        <th width="25%" style="background-color:yellow;">Supporting Document</th>
        <td colspan="3">
             @if(!empty($capa->ca?->supporting_document))

        {{ pathinfo($capa->ca->supporting_document, PATHINFO_FILENAME) }}

    @else

        -

    @endif
        </td>
        
    </tr>
    <tr>
        <th colspan="3" width="70%" style="background-color:yellow;">Preventive Action</th>
        <th style="background-color:yellow; text-align:center;">Due Date</th>
    </tr>
    <tr>
        <td colspan="3" rowspan="3" width="70%">{{ $capa->pa?->description }}</td>
       <td style="text-align:center;">
    {{ $capa->pa?->due_date ? \Carbon\Carbon::parse($capa->pa?->due_date)->format('d-m-Y') : '-' }}
</td>
    </tr>
    <tr>
        
        <th style="background-color:yellow; text-align:center;">PIC</th>
    </tr>
    <tr>
        <td style="text-align:center;">{{ $capa->pa?->picUser->name }}</td>
    </tr>
    <tr>
        <th width="25%" style="background-color:yellow;">Supporting Document</th>
       <td colspan="3">

    @if(!empty($capa->pa?->supporting_document))

        {{ pathinfo($capa->pa->supporting_document, PATHINFO_FILENAME) }}

    @else

        -

    @endif

</td>
        
    </tr>
    <tr>
        <th colspan="2" width="60%" style="text-align: center; background-color:yellow; text-align:center;">Verification</th>
        <th colspan="2" style="background-color:yellow; text-align:center;">New CAPA Needed</th>
    </tr>
    <tr>
       <td colspan="2" rowspan="2" width="60%" style="text-align: center; vertical-align: middle;">
    <p>{{ $capa->mr_statement ?? '' }}</p>
    <p style="color: #1E40AF; text-decoration: underline;">
    {{ $capa->authorized_at ? \Carbon\Carbon::parse($capa->authorized_at)->format('d-m-Y') : '-' }}
</p>

</td>


      <td style="text-align: center;">
    @php
        $yesStyle = $capa->new_capa_needed == 'yes' ? '' : 'text-decoration: line-through;';
        $noStyle  = $capa->new_capa_needed == 'no'  ? '' : 'text-decoration: line-through;';
    @endphp

    <b>
        <span style="{{ $yesStyle }}">Yes</span> / 
        <span style="{{ $noStyle }}">No</span>
    </b>
</td>


        <td style="text-align: center;"><b>If yes, why?</b></td>
    </tr>
    <tr>
        <td colspan="2">{{ $capa->new_capa_reason ?? '' }}
        </td>
    </tr>
    <tr>
        <th style="background-color:yellow; text-align:center;">Management Representative</th>
        <th style="background-color:yellow; text-align:center;">Department Representative</th>
        <th colspan="2" style="background-color:yellow; text-align:center;">Status</th>
    </tr>
    <tr>
        <td style="text-align:center;">{{ $capa->authorizedBy->name ?? '-' }}</td>
         <td style="text-align:center;">{{ $capa->representative->name }}</td>
          <td colspan="2" style="font-size:21px; text-align:center;"><strong>{{ $capa->status }}</strong></td>
    </tr>
    
</table>
<!--<p style="font-size:9px; color:#6b7280;">
    <em>*NOTE : New CAPA will be raised if action has been done and the problem not solved</em>
</p>-->

@if(!empty($evidenceImages))

<div style="page-break-before: always;"></div>

<h4 style="text-align:center;">Evidence</h4>

@foreach($evidenceImages as $img)

    @php
        $filename = pathinfo($img['name'], PATHINFO_FILENAME);
    @endphp

    <div style="
        text-align:center;
        margin-bottom:25px;
        page-break-inside: avoid;
    ">

        <img src="{{ $img['src'] }}"
             style="width:500px; max-height:500px;">

        <div style="margin-top:6px; font-size:11px;">
            {{ str_replace('_',' ', $filename) }}
        </div>

    </div>

@endforeach

@endif

{{-- ================= SUPPORTING DOCUMENT CONTENT (DOCX) ================= --}}
@if(!empty($supportingDocxContent))

@foreach($supportingDocxContent as $doc)

<div style="page-break-before: always;"></div>

<table width="100%" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <th colspan="4" style="text-align:center; background-color:yellow;">
            {{ $doc['label'] }} — {{ $doc['name'] }}
        </th>
    </tr>
</table>

<div style="margin-top:10px;">
    @foreach($doc['content'] as $block)

        @if($block['type'] === 'text' && trim($block['value']) !== '')
            <p style="font-size:11px; margin:4px 0;">{{ $block['value'] }}</p>

        @elseif($block['type'] === 'table')
            <table border="1" width="100%" cellpadding="5" cellspacing="0" style="margin:8px 0;">
                @foreach($block['value'] as $row)
                <tr>
                    @foreach($row as $cell)
                    <td style="font-size:10px;">{{ $cell }}</td>
                    @endforeach
                </tr>
                @endforeach
            </table>
        @endif

    @endforeach
</div>

@endforeach

@endif

{{-- ================= SUPPORTING DOCUMENT (FALLBACK LINK) ================= --}}
@if(!empty($supportingDocs))

<div style="page-break-before: always;"></div>

<h4 style="text-align:center;">Other Supporting Documents</h4>

<table border="1" width="100%" cellpadding="5" cellspacing="0">
    <tr>
        <th width="25%">Type</th>
        <th width="40%">File Name</th>
        <th width="35%">Link</th>
    </tr>
    @foreach($supportingDocs as $doc)
    <tr>
        <td>{{ $doc['label'] }}</td>
        <td>{{ $doc['name'] }}</td>
        <td><a href="{{ $doc['url'] }}" target="_blank">{{ $doc['url'] }}</a></td>
    </tr>
    @endforeach
</table>

@endif




@if(request()->routeIs('mr.capa.print'))
<script>
    window.onload = function () {

        // Delay kecil biar layout & image kebaca dulu
        setTimeout(function () {

            window.print();

            // Setelah print / cancel → close tab
            window.onafterprint = function () {
                window.close();
            };

        }, 500);

    };
</script>
@endif

</body>
</html>