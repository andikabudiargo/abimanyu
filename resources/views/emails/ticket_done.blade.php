<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket Done</title>
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
 <tr>
                        <td>
                           <h2>Your Ticket Has Been Done ✅</h2>
                        </td>
  <!-- Tombol lihat detail -->
                    <tr>
                     <td>
Dear {{ $ticket->requestor->name }},<br><br>
 <p>Your ticket request has been <strong>done</strong> with the following details:</p>

   <ul>
    <li><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</li>
    <li><strong>Subject:</strong> {{ $ticket->title }}</li>
    <li><strong>Assign by:</strong> {{ $ticket->process->name }}</li>
    <li><strong>Solution:</strong> {{ $ticket->corrective_action }}</li>
</ul>


    <p style="margin-top:20px; font-style: italic; color:#555;">
       Thank you for your patience. Kindly click on the ticket to close it as soon as possible.
    </p>
</td>
                    </tr>
                    <tr>
                      <td align="center" style="padding-top: 20px;">
    <!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" 
        href="{{ url('/facility/booking-room/index') }}" style="height:45px;v-text-anchor:middle;width:200px;" 
        arcsize="10%" strokecolor="#007bff" fillcolor="#007bff">
        <w:anchorlock/>
        <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">
            View Booking
        </center>
    </v:roundrect>
    <![endif]-->
    <!--[if !mso]><!-- -->
    <a href="{{ url('/it/ticket/index') }}" target="_blank"
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
