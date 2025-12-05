<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengingat Pergantian APD</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; margin:0; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#4a4a4a" style="color:#ffffff; font-size:18px; font-weight:bold; padding:20px;">
                            <h1 style="margin:0; font-size:24px;">PT. ABIMANYU SEKAR NUSANTARA</h1>
                            <p style="margin:5px 0 0 0; font-size:14px;">Leading of Innovation & Technology</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:20px;">
                            <h2 style="margin-top:0; color:#333333; font-size:20px; display:flex; align-items:center;">
                                <span style="font-size:24px; margin-right:8px;">🔔</span>
                                Pengingat Pergantian APD
                            </h2>

                            <p style="color:#555555; font-size:14px; margin-top:5px;">
                                Berikut daftar APD yang perlu diganti dalam 2 bulan ke depan:
                            </p>

                            <table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse; margin-top:15px; border-color:#dddddd;">
                                <thead>
                                    <tr style="background-color:#f0f0f0; color:#333333;">
                                        <th align="left">No.</th>
                                        <th align="left">Nama Karyawan</th>
                                        <th align="left">Department</th>
                                        <th align="left">APD</th>
                                        <th align="left">Tanggal Pergantian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['department'] }}</td>
                                        <td>{{ $item['apd_name'] }}</td>
                                        <td>{{ $item['due'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px; font-size:12px; color:#777777; line-height:1.5;">
                            Email ini dikirim otomatis oleh Abimanyu Live System.<br><br>
                            Best regards,<br>
                            IT Department<br>
                            PT. Abimanyu Sekar Nusantara
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
