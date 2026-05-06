<!doctype html>
<html lang="id">
<head>
    <title>Suggestion System — PT. Abimanyu Sekar Nusantara</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" href="{{ asset('img/asn-logo-bulat.png') }}" type="image/png" sizes="32x32" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background-color: #f4f1ec;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            background-color: #1a2744;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1;
        }

        .brand img {
            height: 40px;
            width: auto;
        }

        .brand-name {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
            letter-spacing: 0.03em;
            line-height: 1.4;
        }

        .panel-content {
            z-index: 1;
        }

        .panel-eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #e8c97e;
            margin-bottom: 1.25rem;
        }

        .panel-title {
            font-family: 'DM Serif Display', serif;
            font-size: 2.5rem;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .panel-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            line-height: 1.7;
            max-width: 320px;
        }

        .feature-list {
            margin-top: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #e8c97e;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
        }

        .panel-footer {
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            z-index: 1;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background-color: #f4f1ec;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.75rem;
            color: #1a2744;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13px;
            color: #888;
            line-height: 1.6;
        }

        /* Step indicators */
        .steps {
            display: flex;
            gap: 6px;
            margin-bottom: 2rem;
        }

        .step-bar {
            height: 3px;
            border-radius: 99px;
            flex: 1;
            background: #ddd;
            transition: background 0.3s;
        }

        .step-bar.active { background: #1a2744; }
        .step-bar.done { background: #e8c97e; }

        /* Form fields */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 8px;
        }

        .field-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: #1a2744;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .field-group input:focus {
            border-color: #1a2744;
            box-shadow: 0 0 0 3px rgba(26,39,68,0.08);
        }

        .field-group input::placeholder { color: #bbb; }

        .field-group .error-msg {
            font-size: 12px;
            color: #c0392b;
            margin-top: 5px;
        }

        /* OTP input group */
        .otp-inputs {
            display: flex;
            gap: 10px;
        }

        .otp-inputs input {
            flex: 1;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            padding: 14px 8px;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            background: #fff;
            color: #1a2744;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .otp-inputs input:focus {
            border-color: #1a2744;
            box-shadow: 0 0 0 3px rgba(26,39,68,0.08);
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: #1a2744;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover { background: #253561; }
        .btn-primary:active { transform: scale(0.99); }
        .btn-primary:disabled { background: #aaa; cursor: not-allowed; }

        .btn-ghost {
            background: none;
            border: none;
            color: #888;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-ghost:hover { color: #1a2744; }

        .resend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .resend-text {
            font-size: 13px;
            color: #888;
        }

        .resend-btn {
            font-size: 13px;
            font-weight: 600;
            color: #1a2744;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: underline;
        }

        .resend-btn:disabled { color: #bbb; cursor: not-allowed; text-decoration: none; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #ddd;
        }

        .divider-text {
            font-size: 12px;
            color: #aaa;
        }

        .info-box {
            background: #fffdf5;
            border: 1px solid #f0e0a0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #7a6020;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .info-icon {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Loader spinner */
        .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Step transitions */
        .step-view {
            display: none;
            animation: fadeUp 0.3s ease;
        }

        .step-view.active { display: block; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 2rem 1.5rem; align-items: flex-start; padding-top: 3rem; }
        }
    </style>
</head>
<body>

    {{-- ──────────────── LEFT PANEL ──────────────── --}}
    <div class="left-panel">
        <div class="brand">
            <img src="{{ asset('img/asn-logo-bulat.png') }}" alt="ASN Logo">
            <span class="brand-name">PT. Abimanyu Sekar<br>Nusantara</span>
        </div>

        <div class="panel-content">
            <p class="panel-eyebrow">Suggestion System Portal</p>
            <h1 class="panel-title">Inovasi Anda<br>Membangun<br>Perusahaan</h1>
            <p class="panel-desc">Platform pengajuan Suggestion System (SS) bagi seluruh karyawan PT. Abimanyu Sekar Nusantara.</p>

          
        </div>

        <p class="panel-footer">Digitalisasi IT &copy; {{ date('Y') }} PT. Abimanyu Sekar Nusantara</p>
    </div>

    {{-- ──────────────── RIGHT PANEL ──────────────── --}}
    <div class="right-panel">
        <div class="form-wrapper">

            <div class="form-header">
                <h2 id="form-title">Masuk ke Akun</h2>
                <p id="form-subtitle">Gunakan email Anda untuk melanjutkan</p>
            </div>

            {{-- Step indicator --}}
            <div class="steps">
                <div class="step-bar active" id="bar-1"></div>
                <div class="step-bar" id="bar-2"></div>
            </div>

            {{-- Flash messages --}}
            @if (session('status'))
                <div class="info-box">
                    <svg class="info-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="8" cy="8" r="7" stroke="#c8a020" stroke-width="1.5"/>
                        <path d="M8 5v4M8 11v.5" stroke="#c8a020" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- ── STEP 1: Email ── --}}
            <div class="step-view active" id="step-1">
                <form id="email-form" method="POST" action="{{ route('suggestion.otp.send') }}">
                    @csrf

                    <div class="field-group">
                        <label for="email">Email Karyawan</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nama@asn.co.id"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        />
                        @error('email')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="info-box">
                        <svg class="info-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="8" r="7" stroke="#c8a020" stroke-width="1.5"/>
                            <path d="M8 5v4M8 11v.5" stroke="#c8a020" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Kode verifikasi (OTP) akan dikirim ke email Anda. Pastikan menggunakan email yang terdaftar di perusahaan.
                    </div>

                    <button type="submit" class="btn-primary" id="send-btn">
                        <span id="send-label">Kirim Kode Verifikasi</span>
                        <div class="spinner" id="send-spinner" style="display:none;"></div>
                    </button>
                </form>
            </div>

            {{-- ── STEP 2: OTP ── --}}
            <div class="step-view" id="step-2">
             <div class="info-box">
    <div class="info-content">
        
        <span class="info-text">
            Kode 6 digit telah dikirim ke 
            <strong id="email-display">email Anda</strong>.
            Berlaku selama <strong>5 menit</strong>.
        </span>

    </div>
</div>

                <form id="otp-form" method="POST" action="{{ route('suggestion.otp.verify') }}">
                    @csrf
                    <input type="hidden" name="email" id="otp-email-field" />

                    <div class="field-group">
                        <label>Kode OTP</label>
                        <div class="otp-inputs" id="otp-boxes">
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-1" />
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-2" />
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-3" />
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-4" />
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-5" />
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp-6" />
                        </div>
                        <input type="hidden" name="otp" id="otp-value" />
                        @error('otp')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary" id="verify-btn" disabled>
                        <span id="verify-label">Verifikasi & Masuk</span>
                        <div class="spinner" id="verify-spinner" style="display:none;"></div>
                    </button>
                </form>

                <div class="resend-row">
                    <span class="resend-text">Tidak menerima kode?</span>
                    <button class="resend-btn" id="resend-btn" disabled>
                        Kirim ulang (<span id="countdown">60</span>s)
                    </button>
                </div>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">atau</span>
                    <div class="divider-line"></div>
                </div>

                <button class="btn-ghost" onclick="backToStep1()">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Gunakan email lain
                </button>
            </div>

        </div>
    </div>

<script>
    // ── Step navigation ──
    const emailForm   = document.getElementById('email-form');
    const sendBtn     = document.getElementById('send-btn');
    const sendLabel   = document.getElementById('send-label');
    const sendSpinner = document.getElementById('send-spinner');

    let countdownInterval;

    emailForm.addEventListener('submit', function(e) {
        // Show spinner on submit
        sendLabel.style.display   = 'none';
        sendSpinner.style.display = 'block';
        sendBtn.disabled = true;

        // If server redirects back with errors, JS won't run further
        // On success, server should redirect to step-2 OR we handle client-side below for demo
    });

    // If there's an OTP error from server, show step 2 automatically
    @if(session('otp_sent') || $errors->has('otp'))
        goToStep2('{{ session('email_for_otp', old('email', '')) }}');
    @endif

    function goToStep2(email) {
        document.getElementById('step-1').classList.remove('active');
        document.getElementById('step-2').classList.add('active');
        document.getElementById('bar-1').classList.add('done');
        document.getElementById('bar-2').classList.add('active');
        document.getElementById('form-title').textContent    = 'Masukkan Kode OTP';
        document.getElementById('form-subtitle').textContent = 'Cek inbox atau folder spam email Anda';
        document.getElementById('email-display').textContent = email;
        document.getElementById('otp-email-field').value     = email;
        document.getElementById('otp-1').focus();
        startCountdown();
    }

    function backToStep1() {
        document.getElementById('step-2').classList.remove('active');
        document.getElementById('step-1').classList.add('active');
        document.getElementById('bar-2').classList.remove('active');
        document.getElementById('bar-1').classList.remove('done');
        document.getElementById('form-title').textContent    = 'Masuk ke Akun';
        document.getElementById('form-subtitle').textContent = 'Gunakan email kantor Anda untuk melanjutkan';
        clearInterval(countdownInterval);
    }

    // ── OTP input behavior ──
    const otpInputs = document.querySelectorAll('#otp-boxes input');
    const otpValue  = document.getElementById('otp-value');
    const verifyBtn = document.getElementById('verify-btn');

    otpInputs.forEach((input, idx) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val;
            if (val && idx < otpInputs.length - 1) {
                otpInputs[idx + 1].focus();
            }
            syncOTP();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                otpInputs[idx - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((ch, i) => {
                if (otpInputs[i]) otpInputs[i].value = ch;
            });
            syncOTP();
            e.preventDefault();
        });
    });

    function syncOTP() {
        const full = Array.from(otpInputs).map(i => i.value).join('');
        otpValue.value = full;
        verifyBtn.disabled = full.length < 6;
    }

    // ── Countdown & resend ──
    function startCountdown() {
        let sec = 60;
        const resendBtn = document.getElementById('resend-btn');
        const countdown = document.getElementById('countdown');
        resendBtn.disabled = true;

        clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
            sec--;
            countdown.textContent = sec;
            if (sec <= 0) {
                clearInterval(countdownInterval);
                resendBtn.disabled     = false;
                resendBtn.textContent  = 'Kirim ulang kode';
            }
        }, 1000);
    }

    document.getElementById('resend-btn').addEventListener('click', function() {
        const email = document.getElementById('otp-email-field').value;
        this.disabled = true;

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email })
        }).then(() => {
            startCountdown();
            Swal.fire({
                icon: 'success',
                title: 'Kode dikirim ulang',
                text: 'Silakan cek email Anda.',
                confirmButtonColor: '#1a2744',
                timer: 2500,
                showConfirmButton: false
            });
        }).catch(() => {
            this.disabled = false;
        });
    });

    // ── Verify spinner ──
    document.getElementById('otp-form').addEventListener('submit', function() {
        document.getElementById('verify-label').style.display  = 'none';
        document.getElementById('verify-spinner').style.display = 'block';
        document.getElementById('verify-btn').disabled = true;
    });
</script>

</body>
</html>