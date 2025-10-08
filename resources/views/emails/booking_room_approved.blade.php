<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Room Approved</title>
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
                           <h2>Your Room Booking Has Been Approved ✅</h2>
                        </td>
  <!-- Tombol lihat detail -->
                    <tr>
                     <td>
Dear {{ $booking->creator->name }},<br><br>
 <p>Your booking request has been <strong>approved</strong> with the following details:</p>

   <ul>
    <li><strong>Room:</strong> {{ $booking->room->name }}</li>
    <li><strong>Date:</strong> {{ $booking->booking_date }}</li>
    <li><strong>Time:</strong> {{ $booking->start_time }} - {{ $booking->end_time }} WIB</li>
    <li><strong>Purpose:</strong> {{ $booking->purpose }}</li>
    <li><strong>Description:</strong> {{ $booking->description }}</li>
</ul>


    <p style="margin-top:20px; font-style: italic; color:#555;">
        ⚠️ Please be prepared to use the room at the scheduled time.  
        If there are any changes, kindly cancel your booking in advance.
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
    <a href="{{ url('/facility/booking-room/index') }}" target="_blank"
       style="display:inline-block; background-color:#007bff; color:#ffffff; font-family:Arial,sans-serif; font-size:16px; font-weight:bold; text-decoration:none; padding:12px 25px; border-radius:5px;">
        View Booking
    </a>
    <!--<![endif]-->
</td>

                    </tr>

                    <tr>
                        <td style="padding-top: 30px; font-size: 12px; color: #777777;">
                           Best regards ,<br>
                           General Affair Department <br>
                           PT. Abimanyu Sekar Nusantara
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
