<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CAPA Reminder Mail</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f6f9; padding:30px;">

    <table width="100%" cellpadding="0" cellspacing="0" 
       style="width:100%; margin:0; background:#ffffff; padding:30px; border-radius:0;">
@php
    $logoPath = '/home/abimany3/public_html/img/logo.png';
    $logoData = base64_encode(file_get_contents($logoPath));
@endphp
        <tr>
    <td align="center" 
        style="padding:20px; background: linear-gradient(90deg, #0f172a, #1e3a8a); 
               border-radius:8px 8px 0 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <img src="data:image/png;base64,{{ $logoData }}" 
             alt="Company Logo" 
             style="height:15px; width:30px;">
    </td>
</tr>

        <tr>
            <td>
                <h3 style="color:#1f2937; margin-bottom:15px;">
                    CAPA Due Date Reminder
                </h3>

                <p style="font-size:14px; color:#374151;">
                    Dear {{ $action->capa->representative->name ?? 'Department Representative' }},
                </p>

                <p style="font-size:14px; color:#374151;">
                    This is an automated reminder regarding the following CAPA action:
                </p>

                <table width="100%" cellpadding="6" cellspacing="0" 
                       style="border-collapse:collapse; margin:15px 0; font-size:14px;">
                    <tr>
                        <td width="30%"><strong>CAPA No</strong></td>
                        <td>: {{ $action->capa->capa_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Action Type</strong></td>
                        <td>: {{ $action->type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Description</strong></td>
                        <td>: {{ $action->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Due Date</strong></td>
                        <td>: {{ $action->due_date ? \Carbon\Carbon::parse($action->due_date)->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>: {{ $action->status }}</td>
                    </tr>
                </table>

               <p style="font-size:14px; color:#374151;">
    Reminder Status: <strong style="color:#dc2626;">{{ $dayStatus }}</strong>
</p>

                <p style="font-size:14px; color:#374151;">
                    Kindly ensure necessary actions, including submission of supporting documents, are taken before the deadline.
                </p>

                <br>

                <p style="font-size:13px; color:#6b7280;">
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