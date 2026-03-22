<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>

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
                linear-gradient(rgba(10,25,47,0.88), rgba(10,25,47,0.88)),
                url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
            background-size:cover;
            background-position:center;
            background-attachment:scroll;
            position:relative;
        }

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

        .page-wrapper{
            min-height:100vh;
            display:flex;
            flex-direction:column;
            position:relative;
            z-index:1;
        }

        .main-content{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:46px 20px 60px;
        }

        .register-card{
            width:100%;
            max-width:470px;
            padding:36px 30px;
            border-radius:24px;
            background:rgba(255,255,255,0.10);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.14);
            box-shadow:0 30px 60px rgba(0,0,0,0.48);
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

        .input-group{
            margin-bottom:16px;
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
            border-color:#00c6ff;
            background:rgba(255,255,255,0.14);
            box-shadow:0 0 0 4px rgba(0,198,255,.14);
        }

        .eye{
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            font-size:16px;
            opacity:.75;
            user-select:none;
            transition:.2s ease;
        }

        .eye:hover{
            opacity:1;
            transform:translateY(-50%) scale(1.08);
        }

        .is-invalid{
            border-color:#ff4d4f !important;
            box-shadow:0 0 0 4px rgba(255,77,79,.12);
        }

        .text-error{
            color:#ff7b7d;
            font-size:12px;
            margin-top:6px;
            display:block;
            font-weight:600;
        }

        .register-btn{
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
            margin-top:4px;
        }

        .register-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 16px 28px rgba(0,114,255,.38);
        }

        .link-text{
            margin-top:18px;
            text-align:center;
            font-size:13px;
            color:rgba(255,255,255,.86);
        }

        .link-text a{
            color:#67e8f9;
            text-decoration:none;
            font-weight:700;
        }

        .link-text a:hover{
            text-decoration:underline;
        }

        .guland-footer,
        header{
            position:relative;
            z-index:1;
        }

        @media(max-width:480px){
            .main-content{
                align-items:flex-start;
                padding:34px 16px 46px;
            }

            .register-card{
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
        }
    </style>
</head>

<body>

    <div class="page-wrapper">

        @include('components.header')

        <div class="main-content">
            <div class="register-card">

                <div class="logo">🧭</div>
                <div class="title">ĐĂNG KÝ TÀI KHOẢN</div>
                <div class="subtitle">Hệ thống quản lý đất đai</div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- NAME -->
                    <div class="input-group">
                        <input
                            type="text"
                            name="name"
                            placeholder="Họ và tên"
                            value="{{ old('name') }}"
                            class="@error('name') is-invalid @enderror"
                        >
                        @error('name')
                            <small class="text-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- EMAIL -->
                    <div class="input-group">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Email (@gmail.com)"
                            value="{{ old('email') }}"
                            class="@error('email') is-invalid @enderror"
                        >

                        @error('email')
                            <small class="text-error">{{ $message }}</small>
                        @enderror

                        <small id="email-error" class="text-error"></small>
                    </div>

                    <!-- PHONE -->
                    <div class="input-group">
                        <input
                            type="text"
                            name="phone"
                            placeholder="Số điện thoại"
                            value="{{ old('phone') }}"
                            class="@error('phone') is-invalid @enderror"
                        >
                        @error('phone')
                            <small class="text-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- PASSWORD -->
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Mật khẩu"
                            class="@error('password') is-invalid @enderror"
                        >
                        <span class="eye" onclick="togglePassword()">👁</span>

                        @error('password')
                            <small class="text-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- CONFIRM -->
                    <div class="input-group">
                        <input
                            type="password"
                            id="password2"
                            name="password_confirmation"
                            placeholder="Xác nhận mật khẩu"
                        >
                        <span class="eye" onclick="togglePassword2()">👁</span>
                    </div>

                    <button type="submit" class="register-btn">🚀 Tạo tài khoản</button>

                    <div class="link-text">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}">Đăng nhập</a>
                    </div>
                </form>

            </div>
        </div>

        @include('components.footer')

    </div>

    <script>
        function togglePassword(){
            let input = document.getElementById("password");
            input.type = input.type === "password" ? "text" : "password";
        }

        function togglePassword2(){
            let input = document.getElementById("password2");
            input.type = input.type === "password" ? "text" : "password";
        }

        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');

        emailInput.addEventListener('input', function () {
            let val = this.value.toLowerCase();

            emailError.innerHTML = "";
            this.classList.remove('is-invalid');

            const fixes = {
                '@gmai.com':'@gmail.com',
                '@gmial.com':'@gmail.com',
                '@gmail.con':'@gmail.com',
                '@gmail.co':'@gmail.com',
            };

            for (let wrong in fixes) {
                if (val.endsWith(wrong)) {
                    emailError.innerHTML = `Sai đuôi (${wrong}) → phải là @gmail.com`;
                    this.classList.add('is-invalid');
                    return;
                }
            }

            if (val && !val.endsWith('@gmail.com')) {
                emailError.innerHTML = "Chỉ chấp nhận @gmail.com";
                this.classList.add('is-invalid');
            }
        });
    </script>

</body>
</html>