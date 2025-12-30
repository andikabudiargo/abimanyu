<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
        }
        .card {
            background: rgba(255,255,255,0.05);
            padding: 40px 60px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        #timer {
            font-size: 2rem;
            font-weight: 700;
            color: #00ffea;
        }
        @keyframes fadeIn {
            from {opacity:0; transform: translateY(-20px);}
            to {opacity:1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    
    <div class="card">
        <h1>E-STO Belum Tersedia</h1>
        <p>Akan terbuka dalam <span id="timer"></span></p>
    </div>

    <script>
        let days = {{ $days }};
        let hours = {{ $hours }};
        let minutes = {{ $minutes }};
        let seconds = {{ $seconds }};
        const timerEl = document.getElementById('timer');

        function updateTimer(){
            timerEl.textContent = `${days} hari ${hours} jam ${minutes} menit ${seconds} detik`;

            if(seconds > 0){
                seconds--;
            } else {
                if(minutes > 0){
                    minutes--;
                    seconds = 59;
                } else {
                    if(hours > 0){
                        hours--;
                        minutes = 59;
                        seconds = 59;
                    } else {
                        if(days > 0){
                            days--;
                            hours = 23;
                            minutes = 59;
                            seconds = 59;
                        } else {
                            // Countdown selesai, reload halaman
                            location.reload();
                        }
                    }
                }
            }
        }

        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>
