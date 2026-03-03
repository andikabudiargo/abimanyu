<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CAPA Reminder Mail</title>
</head>
<body style="margin:0; padding:0; font-family: 'Open Sans', Arial, sans-serif; background-color:#f4f6f9;">

@php
    // Resize fisik logo sebelum encode, misal versi 70x50
    $logoPath = '/home/abimany3/public_html/img/logo-kecil.png';
    $logoData = base64_encode(file_get_contents($logoPath));
@endphp

<!-- Container utama -->
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:30px 0;">
    <tr>
        <td align="center">

            <!-- Box email -->
            <table width="700" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 0 20px rgba(0,0,0,0.05);">

                <!-- Header dengan background -->
                <tr>
                    <td align="center" style="background-color:#1f2937; padding:15px;">
                        <img src="data:image/png;base64,{{ $logoData }}" 
                             alt="Company Logo" 
                             style="height:50px; width:70px; display:block;">
                    </td>
                </tr>

                <!-- Konten -->
                <tr>
                    <td style="padding:30px; color:#374151; font-size:14px; line-height:1.6;">

                        <h3 style="color:#1f2937; margin-bottom:20px;">⚠️ CAPA Due Date Reminder</h3>

                        <p>Dear {{ $action->capa->representative->name ?? 'Department Representative' }},</p>

                        <p>This is an automated reminder regarding the following CAPA action:</p>

                        <!-- Detail CAPA -->
                        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:14px; margin:15px 0; background:#f9fafb; border-radius:5px;">
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

                        <!-- Reminder Status -->
                        <p>Reminder Status: <strong style="color:#dc2626;">{{ $dayStatus }}</strong></p>

                        <p>Kindly ensure necessary actions, including submission of supporting documents, are taken before the deadline.</p>

                        <hr style="border:none; border-top:1px solid #e5e7eb; margin:20px 0;">

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
            <!-- End Box email -->

        </td>
    </tr>
</table>
<!-- End Container utama -->

</body>
</html>