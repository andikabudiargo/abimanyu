<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket Baru</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; padding: 20px; border-radius: 5px;">
                    <!-- Logo perusahaan -->
                   <tr>
    <td align="center" bgcolor="#4a4a4a" style="padding: 20px;">
        <img src="https://drive.google.com/uc?export=view&id=1A5osBrOLECFWTwqeuoCr3wTQAtPS1iwO" 
             alt="Logo" 
             width="150" 
             height="auto" 
             style="display: block; max-width: 100%; height: auto;">
    </td>
</tr>

                    <!-- Judul -->
                    <tr>
                        <td>
                            <h2 style="color: #333333;">{{ $ticket->title }}</h2>
                            <p><strong>Request by:</strong> {{ $ticket->requestor->name }}</p>
                            <p><strong>Request at:</strong> {{ $ticket->created_at }}</p>
                            <p><strong>Status:</strong> {{ $ticket->status }}</p>
                            <p><strong>Description:</strong><br>{{ $ticket->description }}</p>
                        </td>
                    </tr>

                    <!-- Tombol lihat detail -->
                    <tr>
                       <td>
Dear IT/Maintenance Team of PT. Abimanyu Sekar Nusantara,<br><br>
Please proceed with the latest ticket request that has been approved by the Manager.
</td>

                    </tr>
                    <tr>
                      <td align="center" style="padding-top: 20px;">
    <!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ url('/it/ticket/detail/'.$ticket->id) }}" style="height:45px;v-text-anchor:middle;width:200px;" arcsize="10%" strokecolor="#007bff" fillcolor="#007bff">
        <w:anchorlock/>
        <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">
            View Ticket
        </center>
    </v:roundrect>
    <![endif]-->
    <!--[if !mso]><!-- -->
    <a href="{{ url('/it/ticket/detail/'.$ticket->id) }}" target="_blank"
       style="display:inline-block; background-color:#007bff; color:#ffffff; font-family:Arial,sans-serif; font-size:16px; font-weight:bold; text-decoration:none; padding:12px 25px; border-radius:5px;">
        View Ticket
    </a>
    <!--<![endif]-->
</td>

                    </tr>

                    <tr>
                        <td style="padding-top: 30px; font-size: 12px; color: #777777;">
                           Best regards ,<br>
                           IT Department <br>
                           PT. Abimanyu Sekar Nusantara
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
