<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAPA Reminder</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:'DM Sans', Arial, sans-serif;">

@php
    $logoPath = public_path('img/logo-kecil.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

    $dueDate  = $action->due_date ? \Carbon\Carbon::parse($action->due_date)->startOfDay() : null;
    $today    = \Carbon\Carbon::today();
    $diffDays = $dueDate ? (int) $today->diffInDays($dueDate, false) : null;

    if ($diffDays === null) {
        $urgencyColor    = '#64748b';
        $urgencyBg       = '#f1f5f9';
        $urgencyBorder   = '#cbd5e1';
        $urgencyLabel    = 'No Due Date';
        $urgencyIcon     = '📋';
    } elseif ($diffDays > 0) {
        $urgencyColor    = '#d97706';
        $urgencyBg       = '#fffbeb';
        $urgencyBorder   = '#fcd34d';
        $urgencyLabel    = "Due in {$diffDays} day(s)";
        $urgencyIcon     = '⏳';
    } elseif ($diffDays === 0) {
        $urgencyColor    = '#dc2626';
        $urgencyBg       = '#fef2f2';
        $urgencyBorder   = '#fca5a5';
        $urgencyLabel    = 'Due Today';
        $urgencyIcon     = '🔴';
    } else {
        $absDiff         = abs($diffDays);
        $urgencyColor    = '#b91c1c';
        $urgencyBg       = '#fef2f2';
        $urgencyBorder   = '#f87171';
        $urgencyLabel    = "Overdue by {$absDiff} day(s)";
        $urgencyIcon     = '🚨';
    }
@endphp

<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- Main Card -->
            <table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; box-shadow:0 4px 24px rgba(0,0,0,0.07);">

                <!-- Top accent bar -->
                <tr>
                    <td style="background:linear-gradient(90deg, #0369a1 0%, #4f46e5 60%, #7c3aed 100%); height:5px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- Header -->
                <tr>
                    <td style="padding:24px 36px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;">
                                    @if($logoData)
                                        <img src="data:image/png;base64,{{ $logoData }}"
                                             alt="ASN Logo"
                                             style="height:42px; display:block;">
                                    @else
                                        <span style="font-size:16px; font-weight:700; color:#0f172a; letter-spacing:-0.02em;">PT. Abimanyu Sekar Nusantara</span>
                                    @endif
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="
                                        display:inline-block;
                                        background:{{ $urgencyBg }};
                                        color:{{ $urgencyColor }};
                                        border:1.5px solid {{ $urgencyBorder }};
                                        font-family:'DM Mono', monospace;
                                        font-size:11px;
                                        font-weight:500;
                                        letter-spacing:0.06em;
                                        padding:5px 13px;
                                        border-radius:20px;
                                        text-transform:uppercase;
                                    ">{{ $urgencyIcon }} {{ $urgencyLabel }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Hero -->
                <tr>
                    <td style="padding:32px 36px 24px; border-bottom:1px solid #f1f5f9;">
                        <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.12em; text-transform:uppercase; color:#94a3b8; font-weight:600;">Automated Notification · CAPA Management</p>
                        <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#0f172a; letter-spacing:-0.02em;">CAPA Due Date Reminder</h1>
                        <p style="margin:0; font-size:14px; color:#475569; line-height:1.8;">
                            Dear <strong style="color:#0f172a;">{{ $action->capa->representative->name ?? 'Department Representative' }}</strong>,<br>
                            The following Corrective &amp; Preventive Action item requires your attention.
                            Please review the details and ensure all required steps are completed before the deadline.
                        </p>
                    </td>
                </tr>

                <!-- Action Details -->
                <tr>
                    <td style="padding:28px 36px 0;">
                        <p style="margin:0 0 12px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#94a3b8; font-weight:600;">Action Details</p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">

                            <tr style="background:#f8fafc;">
                                <td width="36%" style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">CAPA No.</td>
                                <td style="padding:13px 16px; font-size:13px; color:#0f172a; font-family:'DM Mono', monospace; font-weight:500; border-bottom:1px solid #f1f5f9;">
                                    {{ $action->capa->capa_number ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">Action Type</td>
                                <td style="padding:13px 16px; border-bottom:1px solid #f1f5f9;">
                                    <span style="display:inline-block; background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:700; padding:3px 10px; border-radius:5px; letter-spacing:0.08em; border:1px solid #bfdbfe;">
                                        {{ $action->type }}
                                    </span>
                                </td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #f1f5f9; vertical-align:top;">Description</td>
                                <td style="padding:13px 16px; font-size:13px; color:#334155; line-height:1.7; border-bottom:1px solid #f1f5f9;">
                                    {{ $action->description ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">Due Date</td>
                                <td style="padding:13px 16px; font-size:13px; color:#0f172a; font-family:'DM Mono', monospace; font-weight:600; border-bottom:1px solid #f1f5f9;">
                                    {{ $dueDate ? $dueDate->format('d M Y') : '—' }}
                                </td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase;">Status</td>
                                <td style="padding:13px 16px;">
                                    <span style="display:inline-block; background:#f0fdf4; color:#15803d; font-size:11px; font-weight:700; padding:3px 10px; border-radius:5px; letter-spacing:0.08em; border:1px solid #bbf7d0;">
                                        {{ strtoupper($action->status) }}
                                    </span>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <!-- Background Information -->
                @if(!empty($action->capa->problem) || !empty($action->capa->detail_of_information))
                <tr>
                    <td style="padding:24px 36px 0;">
                        <p style="margin:0 0 12px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#94a3b8; font-weight:600;">Background Information</p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">

                            @if(!empty($action->capa->problem))
                            <tr style="background:#f8fafc;">
                                <td width="36%" style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #f1f5f9; vertical-align:top;">
                                    Problem
                                </td>
                                <td style="padding:13px 16px; font-size:13px; color:#334155; line-height:1.7; border-bottom:1px solid #f1f5f9;">
                                    {{ $action->capa->problem }}
                                </td>
                            </tr>
                            @endif

                            @if(!empty($action->capa->detail_of_information))
                            <tr>
                                <td style="padding:13px 16px; font-size:11px; color:#94a3b8; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; vertical-align:top;">
                                    Detail of Information
                                </td>
                                <td style="padding:13px 16px; font-size:13px; color:#334155; line-height:1.7;">
                                    {{ $action->capa->detail_of_information }}
                                </td>
                            </tr>
                            @endif

                        </table>
                    </td>
                </tr>
                @endif

                <!-- CTA Notice -->
                <tr>
                    <td style="padding:24px 36px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0; font-size:13px; color:#1e40af; line-height:1.8;">
                                        <strong>⚡ Action Required:</strong>
                                        Ensure all corrective or preventive steps — including submission of supporting documents — are completed before the due date.
                                        Failure to act may result in escalation to your department supervisor.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Divider -->
                <tr>
                    <td style="padding:0 36px;">
                        <div style="border-top:1px solid #f1f5f9; font-size:0;">&nbsp;</div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:20px 36px 28px; background:#f8fafc;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:bottom;">
                                    <p style="margin:0 0 2px; font-size:12px; color:#94a3b8;">Best Regards,</p>
                                    <p style="margin:0; font-size:13px; font-weight:700; color:#334155;">IT Department</p>
                                    <p style="margin:0; font-size:12px; color:#94a3b8;">PT. Abimanyu Sekar Nusantara</p>
                                </td>
                                <td align="right" style="vertical-align:bottom;">
                                    <p style="margin:0; font-size:11px; color:#cbd5e1; font-family:'DM Mono', monospace; text-align:right; line-height:1.8;">
                                        CAPA Management System<br>
                                        {{ now()->format('d M Y · H:i') }} WIB
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Bottom bar -->
                <tr>
                    <td style="background:linear-gradient(90deg, #7c3aed 0%, #4f46e5 50%, #0369a1 100%); height:4px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

            </table>

            <!-- Sub-footer -->
            <table width="640" cellpadding="0" cellspacing="0" style="margin-top:16px;">
                <tr>
                    <td align="center">
                        <p style="margin:0; font-size:11px; color:#94a3b8; line-height:1.8;">
                            This message was generated automatically by the CAPA Management System.<br>
                            Please do not reply to this email. For inquiries, contact your IT administrator.
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>