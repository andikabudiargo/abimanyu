<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CAPA Reminder Mail</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f6f9; padding:30px; margin:0;">

@php
    $logoPath = '/home/abimany3/public_html/img/logo.png';
    $logoData = base64_encode(file_get_contents($logoPath));
@endphp

<table width="100%" cellpadding="0" cellspacing="0" 
       style="max-width:700px; margin:auto; background:#ffffff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

    <!-- Logo -->
    <tr>
        <td align="center" style="padding-bottom:20px;">
            <img src="data:image/png;base64,{{ $logoData }}" 
                 alt="Company Logo" 
                 style="height:50px; width:auto;">
        </td>
    </tr>

    <!-- Header -->
    <tr>
        <td>
            <h2 style="color:#111827; margin-bottom:10px; font-size:20px; font-weight:600;">
                CAPA Due Date Reminder
            </h2>

            <p style="font-size:14px; color:#374151; margin-bottom:10px;">
                Dear {{ $action->capa->representative->name ?? 'Department Representative' }},
            </p>

            <p style="font-size:14px; color:#374151; margin-bottom:15px;">
                This is an automated reminder regarding the following CAPA action:
            </p>

            <!-- CAPA Info Table -->
            <table width="100%" cellpadding="8" cellspacing="0" 
                   style="border-collapse:collapse; margin:15px 0; font-size:14px; color:#374151;">
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td width="30%" style="font-weight:600;">CAPA No</td>
                    <td>: {{ $action->capa->capa_number ?? '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="font-weight:600;">Action Type</td>
                    <td>: {{ $action->type }}</td>
                </tr>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="font-weight:600;">Description</td>
                    <td>: {{ $action->description ?? '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="font-weight:600;">Due Date</td>
                    <td>: {{ $action->due_date ? \Carbon\Carbon::parse($action->due_date)->format('d-m-Y') : '-' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="font-weight:600;">Status</td>
                    <td>: {{ $action->status }}</td>
                </tr>
            </table>

            <!-- Reminder Status -->
            <p style="font-size:14px; margin:10px 0;">
                Reminder Status: 
                <span style="color:#fff; background:#dc2626; font-weight:700; padding:3px 8px; border-radius:6px;">
                    {{ $dayStatus }}
                </span>
            </p>

            <p style="font-size:14px; color:#374151; margin-top:10px;">
                Kindly ensure necessary actions, including submission of supporting documents, are taken before the deadline.
            </p>

            <p style="font-size:13px; color:#6b7280; margin-top:30px;">
                This email is generated automatically by the CAPA Management System.
            </p>

            <p style="font-size:13px; color:#6b7280;">
                Best Regards,<br>
                IT PT. Abimanyu Sekar Nusantara
            </p>
        </td>
    </tr>

</table>

</body>
</html>