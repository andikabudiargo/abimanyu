<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAPA Reminder</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding:0; background-color:#0a0f1e; font-family:'DM Sans', Arial, sans-serif;">

@php
    $logoPath = public_path('img/logo-kecil.png');
    $logoData = base64_encode(file_get_contents($logoPath));

    $dueDate = $action->due_date ? \Carbon\Carbon::parse($action->due_date) : null;
    $today = \Carbon\Carbon::today();
    $diffDays = $dueDate ? $today->diffInDays($dueDate, false) : null;

    if ($diffDays === null) {
        $urgencyColor = '#64748b';
        $urgencyBg    = '#1e293b';
        $urgencyLabel = 'N/A';
        $urgencyIcon  = '📋';
    } elseif ($diffDays > 0) {
        $urgencyColor = '#f59e0b';
        $urgencyBg    = '#1c1a0e';
        $urgencyLabel = "Due in {$diffDays} day(s)";
        $urgencyIcon  = '⏳';
    } elseif ($diffDays === 0) {
        $urgencyColor = '#ef4444';
        $urgencyBg    = '#1f0f0f';
        $urgencyLabel = 'Due Today';
        $urgencyIcon  = '🔴';
    } else {
        $absDiff = abs($diffDays);
        $urgencyColor = '#dc2626';
        $urgencyBg    = '#1f0a0a';
        $urgencyLabel = "Overdue by {$absDiff} day(s)";
        $urgencyIcon  = '🚨';
    }
@endphp

<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0f1e; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- Main Card -->
            <table width="640" cellpadding="0" cellspacing="0" style="background:#111827; border-radius:16px; overflow:hidden; border:1px solid #1e2d45;">

                <!-- Top accent bar -->
                <tr>
                    <td style="background:linear-gradient(90deg, #0ea5e9 0%, #6366f1 50%, #8b5cf6 100%); height:4px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- Header -->
                <tr>
                    <td style="padding:28px 36px 24px; background:#0d1424; border-bottom:1px solid #1e2d45;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;">
                                    @if(file_exists($logoPath))
                                    <img src="data:image/png;base64,{{ $logoData }}"
                                         alt="ASN Logo"
                                         style="height:40px; display:block;">
                                    @endif
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="
                                        display:inline-block;
                                        background:{{ $urgencyBg }};
                                        color:{{ $urgencyColor }};
                                        border:1px solid {{ $urgencyColor }}40;
                                        font-family:'DM Mono', monospace;
                                        font-size:11px;
                                        font-weight:500;
                                        letter-spacing:0.08em;
                                        padding:5px 12px;
                                        border-radius:20px;
                                        text-transform:uppercase;
                                    ">{{ $urgencyIcon }} {{ $urgencyLabel }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Hero section -->
                <tr>
                    <td style="padding:32px 36px 24px; border-bottom:1px solid #1e2d45;">
                        <p style="margin:0 0 6px; font-size:11px; letter-spacing:0.12em; text-transform:uppercase; color:#4b6a8a; font-weight:500;">AUTOMATED NOTIFICATION</p>
                        <h1 style="margin:0 0 16px; font-size:22px; font-weight:600; color:#f1f5f9; letter-spacing:-0.01em;">CAPA Due Date Reminder</h1>
                        <p style="margin:0; font-size:14px; color:#94a3b8; line-height:1.7;">
                            Dear <strong style="color:#e2e8f0;">{{ $action->capa->representative->name ?? 'Department Representative' }}</strong>,<br>
                            The following Corrective &amp; Preventive Action item requires your immediate attention.
                            Please review the details below and ensure all required steps are completed before the deadline.
                        </p>
                    </td>
                </tr>

                <!-- CAPA Core Details -->
                <tr>
                    <td style="padding:28px 36px 0;">
                        <p style="margin:0 0 14px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#4b6a8a; font-weight:500;">Action Details</p>

                        <!-- Detail rows -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #1e2d45; border-radius:10px; overflow:hidden;">

                            <tr style="background:#0d1424;">
                                <td width="38%" style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; border-bottom:1px solid #1e2d45;">CAPA No.</td>
                                <td style="padding:13px 16px; font-size:13px; color:#e2e8f0; font-family:'DM Mono', monospace; border-bottom:1px solid #1e2d45;">
                                    {{ $action->capa->capa_number ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; border-bottom:1px solid #1e2d45;">Action Type</td>
                                <td style="padding:13px 16px; border-bottom:1px solid #1e2d45;">
                                    <span style="display:inline-block; background:#1e3a5f; color:#38bdf8; font-size:11px; font-weight:600; padding:3px 10px; border-radius:4px; letter-spacing:0.06em;">
                                        {{ $action->type }}
                                    </span>
                                </td>
                            </tr>

                            <tr style="background:#0d1424;">
                                <td style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; border-bottom:1px solid #1e2d45;">Description</td>
                                <td style="padding:13px 16px; font-size:13px; color:#cbd5e1; line-height:1.6; border-bottom:1px solid #1e2d45;">
                                    {{ $action->description ?? '—' }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; border-bottom:1px solid #1e2d45;">Due Date</td>
                                <td style="padding:13px 16px; font-size:13px; color:#e2e8f0; font-family:'DM Mono', monospace; font-weight:500; border-bottom:1px solid #1e2d45;">
                                    {{ $dueDate ? $dueDate->format('d M Y') : '—' }}
                                </td>
                            </tr>

                            <tr style="background:#0d1424;">
                                <td style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase;">Status</td>
                                <td style="padding:13px 16px;">
                                    <span style="display:inline-block; background:#14290a; color:#4ade80; font-size:11px; font-weight:600; padding:3px 10px; border-radius:4px; letter-spacing:0.06em; border:1px solid #16632240;">
                                        {{ strtoupper($action->status) }}
                                    </span>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <!-- Problem & Detail of Information -->
                @if(!empty($action->capa->problem) || !empty($action->capa->detail_of_information))
                <tr>
                    <td style="padding:24px 36px 0;">
                        <p style="margin:0 0 14px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#4b6a8a; font-weight:500;">Background Information</p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #1e2d45; border-radius:10px; overflow:hidden;">

                            @if(!empty($action->capa->problem))
                            <tr style="background:#0d1424;">
                                <td width="38%" style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; border-bottom:1px solid #1e2d45; vertical-align:top;">Problem</td>
                                <td style="padding:13px 16px; font-size:13px; color:#cbd5e1; line-height:1.7; border-bottom:1px solid #1e2d45;">
                                    {{ $action->capa->problem }}
                                </td>
                            </tr>
                            @endif

                            @if(!empty($action->capa->detail_of_information))
                            <tr>
                                <td style="padding:13px 16px; font-size:12px; color:#64748b; font-weight:500; letter-spacing:0.04em; text-transform:uppercase; vertical-align:top;">Detail of Information</td>
                                <td style="padding:13px 16px; font-size:13px; color:#cbd5e1; line-height:1.7;">
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
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f1e2e; border:1px solid #1e3a5f; border-radius:10px; padding:0;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0; font-size:13px; color:#7dd3fc; line-height:1.7;">
                                        <strong style="color:#38bdf8;">Action Required:</strong>
                                        Please ensure that all necessary corrective or preventive steps — including submission of supporting documents — are completed before the due date.
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
                        <div style="border-top:1px solid #1e2d45; font-size:0;">&nbsp;</div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:20px 36px 28px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <p style="margin:0 0 2px; font-size:12px; color:#475569;">Best Regards,</p>
                                    <p style="margin:0; font-size:13px; font-weight:600; color:#94a3b8;">IT Department</p>
                                    <p style="margin:0; font-size:12px; color:#334155;">PT. Abimanyu Sekar Nusantara</p>
                                </td>
                                <td align="right" style="vertical-align:bottom;">
                                    <p style="margin:0; font-size:11px; color:#1e3a5f; font-family:'DM Mono', monospace;">
                                        CAPA Management System<br>
                                        {{ now()->format('d M Y · H:i') }} WIB
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Bottom accent bar -->
                <tr>
                    <td style="background:linear-gradient(90deg, #8b5cf6 0%, #6366f1 50%, #0ea5e9 100%); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

            </table>
            <!-- End Main Card -->

            <!-- Sub-footer -->
            <table width="640" cellpadding="0" cellspacing="0" style="margin-top:16px;">
                <tr>
                    <td align="center">
                        <p style="margin:0; font-size:11px; color:#1e3a5f; line-height:1.8;">
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