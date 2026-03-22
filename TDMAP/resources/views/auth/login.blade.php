<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Hệ thống quản lý địa chính</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
        }

        html, body{
            width:100%;
            min-height:100vh;
            overflow-x:hidden !important;
            overflow-y:auto !important;
        }

        body{
            font-family:'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(10,25,47,0.85), rgba(10,25,47,0.85)),
                url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
            background-size:cover;
            background-position:center;
            background-attachment:scroll;
            position:relative;
        }

        /* GRID */
        body::before{
            content:"";
            position:fixed;
            inset:0;
            background-image:
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size:40px 40px;
            pointer-events:none;
            z-index:0;
        }

        /* WRAPPER */
        .page-wrapper{
            position:relative;
            z-index:1;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        /* CONTENT */
        .main-content{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 20px 60px;
        }

        /* LOGIN CARD */
        .login-card{
            width:100%;
            max-width:430px;
            padding:36px 30px;
            border-radius:24px;
            background:rgba(255,255,255,0.10);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.14);
            box-shadow:0 30px 60px rgba(0,0,0,0.45);
            color:white;
            animation:fadeIn .6s ease;
        }

        @keyframes fadeIn{
            from{opacity:0;transform:translateY(20px);}
            to{opacity:1;transform:translateY(0);}
        }

        .logo{
            text-align:center;
            font-size:38px;
            margin-bottom:10px;
        }

        .title{
            text-align:center;
            font-size:22px;
            font-weight:800;
            letter-spacing:.3px;
        }

        .subtitle{
            text-align:center;
            font-size:13px;
            opacity:.75;
            margin:8px 0 26px;
        }

        /* INPUT */
        .input-group{
            margin-bottom:15px;
            position:relative;
        }

        .input-group input{
            width:100%;
            padding:13px 14px;
            padding-right:48px;
            border-radius:14px;
            border:1px solid rgba(255,255,255,0.18);
            background:rgba(255,255,255,0.10);
            color:white;
            font-size:14px;
            transition:.25s ease;
        }

        .input-group input::placeholder{
            color:rgba(255,255,255,.7);
        }

        .input-group input:focus{
            outline:none;
            border-color:#38bdf8;
            background:rgba(255,255,255,0.14);
            box-shadow:0 0 0 4px rgba(56,189,248,.14);
        }

        /* EYE ICON */
        .eye{
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            font-size:16px;
            opacity:.75;
            transition:.2s;
            user-select:none;
        }

        .eye:hover{
            opacity:1;
            transform:translateY(-50%) scale(1.08);
        }

        /* OPTIONS */
        .form-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            font-size:14px;
            margin-bottom:18px;
            flex-wrap:wrap;
        }

        .remember-label{
            display:flex;
            align-items:center;
            gap:7px;
            color:rgba(255,255,255,.92);
        }

        .remember-label input{
            accent-color:#38bdf8;
        }

        .forgot-link{
            color:#7dd3fc;
            text-decoration:none;
            font-weight:600;
        }

        .forgot-link:hover{
            text-decoration:underline;
        }

        /* BUTTON */
        .login-btn{
            width:100%;
            padding:14px;
            border:none;
            border-radius:15px;
            background:linear-gradient(135deg,#00c6ff,#0072ff);
            color:white;
            font-weight:700;
            font-size:15px;
            cursor:pointer;
            transition:.25s ease;
            box-shadow:0 14px 25px rgba(0,114,255,.28);
        }

        .login-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 16px 28px rgba(0,114,255,.38);
        }

        /* FOOTER TEXT */
        .footer-text{
            margin-top:18px;
            text-align:center;
            font-size:13px;
            opacity:.82;
        }

        /* ALERT */
        .alert{
            padding:12px 14px;
            border-radius:12px;
            margin-bottom:15px;
            text-align:center;
            font-size:14px;
            animation:fadeIn .4s;
            font-weight:600;
        }

        .alert-success{
            background:#22c55e;
            color:white;
        }

        .alert-error{
            background:#ef4444;
            color:white;
        }

        /* LOADING */
        #loadingOverlay{
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.6);
            display:none;
            align-items:center;
            justify-content:center;
            z-index:9999;
        }

        .loading-box{
            background:white;
            padding:25px 35px;
            border-radius:16px;
            text-align:center;
            color:#333;
            font-weight:700;
            min-width:220px;
            box-shadow:0 16px 35px rgba(0,0,0,.22);
        }

        .spinner{
            width:35px;
            height:35px;
            border:4px solid #ddd;
            border-top:4px solid #0072ff;
            border-radius:50%;
            margin:0 auto 10px;
            animation:spin 1s linear infinite;
        }

        @keyframes spin{
            to{transform:rotate(360deg);}
        }

        /* IMPORTANT: footer/header đừng đè scroll */
        .guland-footer,
        header{
            position:relative;
            z-index:1;
        }

        /* MOBILE */
        @media(max-width:480px){
            .main-content{
                align-items:flex-start;
                padding:34px 16px 46px;
            }

            .login-card{
                padding:24px 18px;
                border-radius:18px;
            }

            .logo{
                font-size:30px;
            }

            .title{
                font-size:18px;
            }

            .subtitle{
                font-size:12px;
            }

            .form-row{
                font-size:13px;
            }
        }
    </style>
</head>

<body>

    <div class="page-wrapper">

        @include('components.header')

        <div class="main-content">
            <div class="login-card">

                <div class="logo">🛰</div>
                <div class="title">HỆ THỐNG ĐỊA CHÍNH GIS</div>
                <div class="subtitle">Quản lý đất đai & quy hoạch số</div>

                {{-- SUCCESS --}}
                @if(session('success'))
                    <div class="alert alert-success" id="alertBox">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-error" id="alertBox">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" onsubmit="showLoading()">
                    @csrf

                    <div class="input-group">
                        <input type="text" name="email" placeholder="Email hoặc số điện thoại" required>
                    </div>

                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                        <span class="eye" onclick="togglePassword()">👁</span>
                    </div>

                    <div class="form-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember">
                            Ghi nhớ đăng nhập
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Quên mật khẩu?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="login-btn">
                        🔐 Đăng nhập hệ thống
                    </button>
                </form>

                <div class="footer-text">
                    © {{ date('Y') }} Trung tâm Đo đạc & Bản đồ
                </div>

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
        function showLoading(){
            document.getElementById("loadingOverlay").style.display = "flex";
        }

        function togglePassword(){
            let input = document.getElementById("password");
            let eye = document.querySelector(".eye");

            if(input.type === "password"){
                input.type = "text";
                eye.innerHTML = "🙈";
            }else{
                input.type = "password";
                eye.innerHTML = "👁";
            }
        }

        setTimeout(() => {
            let box = document.getElementById("alertBox");
            if(box){
                box.style.transition = "opacity .5s ease";
                box.style.opacity = "0";
                setTimeout(() => box.remove(), 500);
            }
        }, 3000);
    </script>

</body>
</html>