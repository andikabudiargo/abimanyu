<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OTP Verification</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>

<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:'DM Sans', Arial, sans-serif;">

@php
    $logoPath = public_path('img/logo-kecil.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
@endphp

<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8; padding:40px 0;">
<tr>
<td align="center">

<!-- Main Card -->
<table width="640" cellpadding="0" cellspacing="0"
style="background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; box-shadow:0 4px 24px rgba(0,0,0,0.07);">

<!-- Top Accent -->
<tr>
<td style="background:linear-gradient(90deg,#0ea5e9,#6366f1,#8b5cf6); height:5px;"></td>
</tr>

<!-- Header -->
<tr>
<td style="padding:24px 36px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
<table width="100%">
<tr>

<td>
@if($logoData)
<img src="data:image/png;base64,{{ $logoData }}" style="height:40px;">
@else
<span style="font-weight:700; font-size:16px; color:#0f172a;">
PT. Abimanyu Sekar Nusantara
</span>
@endif
</td>

<td align="right">
<span style="
display:inline-block;
background:#eef2ff;
color:#4338ca;
border:1px solid #c7d2fe;
font-family:'DM Mono', monospace;
font-size:11px;
padding:5px 12px;
border-radius:20px;
letter-spacing:0.06em;">
SECURE OTP
</span>
</td>

</tr>
</table>
</td>
</tr>

<!-- Hero -->
<tr>
<td style="padding:32px 36px 24px; border-bottom:1px solid #f1f5f9;">

<p style="margin:0 0 6px; font-size:11px; letter-spacing:0.12em; text-transform:uppercase; color:#94a3b8; font-weight:600;">
Authentication Required
</p>

<h1 style="margin:0 0 14px; font-size:22px; font-weight:700; color:#0f172a;">
Kode Verifikasi Login
</h1>

<p style="margin:0; font-size:14px; color:#475569; line-height:1.8;">
Halo <strong style="color:#0f172a;">{{ $userName }}</strong>,<br><br>
Gunakan kode OTP berikut untuk melanjutkan proses login ke sistem.
Kode ini bersifat rahasia dan hanya berlaku sementara.
</p>

</td>
</tr>

<!-- OTP SECTION -->
<tr>
<td style="padding:28px 36px 0;">

<p style="margin:0 0 12px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#94a3b8; font-weight:600;">
Verification Code
</p>

<table width="100%" cellpadding="0" cellspacing="0"
style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; text-align:center;">

<tr>
<td style="padding:28px 16px; background:#f8fafc;">
<span style="
font-family:'DM Mono', monospace;
font-size:34px;
letter-spacing:10px;
font-weight:600;
color:#0f172a;">
{{ $otp }}
</span>
</td>
</tr>

<tr>
<td style="padding:12px 16px; font-size:12px; color:#64748b;">
Berlaku selama <strong>{{ $expiry }} menit</strong>
</td>
</tr>

</table>

</td>
</tr>

<!-- NOTICE -->
<tr>
<td style="padding:24px 36px;">

<table width="100%" style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px;">
<tr>
<td style="padding:14px 18px; font-size:13px; color:#991b1b; line-height:1.7;">
<strong>Warning:</strong>
Jangan pernah membagikan kode ini kepada siapapun, termasuk pihak internal perusahaan.
</td>
</tr>
</table>

</td>
</tr>

<!-- Divider -->
<tr>
<td style="padding:0 36px;">
<div style="border-top:1px solid #f1f5f9;"></div>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="padding:20px 36px 28px; background:#f8fafc;">
<table width="100%">

<tr>

<td>
<p style="margin:0 0 2px; font-size:12px; color:#94a3b8;">System Notification</p>
<p style="margin:0; font-size:13px; font-weight:700; color:#334155;">IT Department</p>
<p style="margin:0; font-size:12px; color:#94a3b8;">PT. Abimanyu Sekar Nusantara</p>
</td>

<td align="right">
<p style="margin:0; font-size:11px; color:#cbd5e1; font-family:'DM Mono', monospace; line-height:1.8;">
Secure Auth System<br>
{{ now()->format('d M Y · H:i') }} WIB
</p>
</td>

</tr>

</table>
</td>
</tr>

<!-- Bottom Accent -->
<tr>
<td style="background:linear-gradient(90deg,#8b5cf6,#6366f1,#0ea5e9); height:4px;"></td>
</tr>

</table>

<!-- Sub footer -->
<table width="640" style="margin-top:16px;">
<tr>
<td align="center">
<p style="font-size:11px; color:#94a3b8; line-height:1.8;">
Email ini dikirim otomatis oleh sistem. Jangan membalas email ini.
</p>
</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>