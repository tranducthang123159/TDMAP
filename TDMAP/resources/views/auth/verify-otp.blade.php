<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý địa chính</title>
   <img src="{{ asset('images/logo.png') }}" alt="Tài Đỗ Map" class="site-logo">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(10, 25, 47, 0.85), rgba(10, 25, 47, 0.85)),
                url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
            background-size: cover;
            background-position: center;
        }

        /* GRID */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* WRAPPER */

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* CONTENT */

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* LOGIN CARD */

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            color: white;
            animation: fadeIn .6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            opacity: .7;
            margin-bottom: 25px;
        }

        /* INPUT */

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }

        .input-group input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 8px rgba(56, 189, 248, .6);
        }

        /* BUTTON */

        .login-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: .3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 114, 255, .4);
        }

        /* FOOTER */

        .footer-text {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            opacity: .8;
        }

        /* ALERT */

        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            animation: fadeIn .4s;
        }

        .alert-success {
            background: #22c55e;
        }

        .alert-error {
            background: #ff4d4f;
        }

        /* LOADING */

        #loadingOverlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-box {
            background: white;
            padding: 25px 35px;
            border-radius: 12px;
            text-align: center;
            color: #333;
            font-weight: 600;
        }

        .spinner {
            width: 35px;
            height: 35px;
            border: 4px solid #ddd;
            border-top: 4px solid #0072ff;
            border-radius: 50%;
            margin: 0 auto 10px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* MOBILE */

        @media(max-width:480px) {

            .main-content {
                align-items: flex-start;
                padding-top: 40px;
            }

            .login-card {
                padding: 25px;
                border-radius: 16px;
            }

            .logo {
                font-size: 30px;
            }

            .title {
                font-size: 18px;
            }

            .subtitle {
                font-size: 12px;
            }

        }

        /* OTP INPUT */

        form input {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.12);
            color: white;
            font-size: 16px;
            text-align: center;
            letter-spacing: 4px;
            transition: .3s;
        }

        form input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 2px;
        }

        form input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 8px rgba(56, 189, 248, .6);
            background: rgba(255, 255, 255, 0.18);
        }

        /* BUTTON */

        form button {
            width: 100%;
            margin-top: 15px;
            padding: 13px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 114, 255, .4);
        }

        /* ERROR */

        .error {
            margin-top: 10px;
            color: #ff6b6b;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>

    @include('components.header')

    <div class="page-wrapper">

        <div class="main-content">

            <div class="login-card">

                <div class="logo">🛰</div>
                <div class="title">HỆ THỐNG ĐỊA CHÍNH GIS</div>
                <div class="subtitle">Quản lý đất đai & quy hoạch số</div>




                {{-- ERROR --}}

                <h2 style="text-align:center;margin-bottom:10px;">Xác minh email</h2>

                {{-- SUCCESS --}}
                @if(session('success'))
                    <div class="alert alert-success" id="alertBox">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ERROR --}}
                @if(session('error'))
                    <div class="alert alert-error" id="alertBox">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}" onsubmit="showLoading()">
                    @csrf

                    <input type="text" name="otp" placeholder="Nhập mã OTP" maxlength="6" required>

                    {{-- VALIDATE ERROR --}}
                    @error('otp')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <button type="submit">Xác minh</button>

                    {{-- COUNTDOWN --}}
                    <p style="margin-top:10px;text-align:center;font-size:13px;opacity:.8;">
                        Mã hết hạn sau: <span id="countdown">05:00</span>
                    </p>

                    <p style="margin-top:10px; text-align:center;">
                        Chưa nhận mã?
                        <a href="{{ route('otp.resend') }}">Gửi lại OTP</a>
                    </p>
                </form>

            </div>
        </div>

        @include('components.footer')

    </div>


    <!-- LOADING -->
    <div id="loadingOverlay">

        <div class="loading-box">

            <div class="spinner"></div>
            Đang đăng nhập...

        </div>

    </div>


    <script>

        function showLoading() {

            document.getElementById("loadingOverlay").style.display = "flex";

        }


        /* AUTO HIDE ALERT */

        setTimeout(() => {

            let box = document.getElementById("alertBox");

            if (box) {

                box.style.opacity = "0";

                setTimeout(() => box.remove(), 500);

            }

        }, 3000);

    </script>

    <script>
        let time = 300; // 5 phút

        let countdown = setInterval(() => {

            let m = Math.floor(time / 60);
            let s = time % 60;

            document.getElementById("countdown").innerText =
                `${m}:${s < 10 ? '0' : ''}${s}`;

            if (time <= 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerText = "Hết hạn";
            }

            time--;

        }, 1000);
    </script>
</body>

</html>