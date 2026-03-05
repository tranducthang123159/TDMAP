<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hệ thống quản lý địa chính</title>


<style>
*{box-sizing:border-box;}

body{
    margin:0;
    min-height:100vh;
    font-family:'Segoe UI', sans-serif;
    background:
    linear-gradient(rgba(10,25,47,0.85), rgba(10,25,47,0.85)),
    url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
    background-size:cover;
    background-position:center;
}

/* GRID overlay */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background-image:
    linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size:40px 40px;
    pointer-events:none;
}

/* WRAPPER */
.page-wrapper{
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* PHẦN LOGIN */
.main-content{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}

/* Card */
.login-card{
    width:100%;
    max-width:420px;
    padding:40px;
    border-radius:20px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(20px);
    box-shadow:0 30px 60px rgba(0,0,0,0.5);
    color:white;
    animation:fadeIn 0.6s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

.logo{
    text-align:center;
    font-size:36px;
    margin-bottom:10px;
}

.title{
    text-align:center;
    font-size:20px;
    font-weight:700;
    letter-spacing:1px;
}

.subtitle{
    text-align:center;
    font-size:13px;
    opacity:0.7;
    margin-bottom:25px;
}

.input-group{
    margin-bottom:15px;
}

.input-group input{
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,0.2);
    background:rgba(255,255,255,0.1);
    color:white;
    font-size:14px;
}

.input-group input:focus{
    outline:none;
    border-color:#38bdf8;
    box-shadow:0 0 8px rgba(56,189,248,0.6);
}

.login-btn{
    width:100%;
    padding:13px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#00c6ff,#0072ff);
    font-weight:600;
    font-size:15px;
    transition:0.3s;
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(0,114,255,0.4);
}

.footer-text{
    margin-top:18px;
    text-align:center;
    font-size:13px;
    opacity:0.8;
}

/* MOBILE */
@media(max-width:480px){

    .main-content{
        align-items:flex-start;
        padding-top:40px;
    }

    .login-card{
        padding:25px;
        border-radius:16px;
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
@include('components.header')
@if ($errors->any())
<div style="
background:#ff4d4f;
padding:12px;
border-radius:10px;
margin-bottom:15px;
font-size:14px;
text-align:center;
">
{{ $errors->first() }}
</div>
@endif
<div class="page-wrapper">

    <!-- LOGIN -->
    <div class="main-content">
        <div class="login-card">

            <div class="logo">🛰</div>
            <div class="title">HỆ THỐNG ĐỊA CHÍNH GIS</div>
            <div class="subtitle">Quản lý đất đai & quy hoạch số</div>

<form method="POST" action="{{ route('login') }}">
@csrf

@if ($errors->any())
<div style="
background:#ff4d4f;
padding:12px;
border-radius:10px;
margin-bottom:15px;
font-size:14px;
text-align:center;
">
{{ $errors->first() }}
</div>
@endif

<div class="input-group">
<input type="email"
name="email"
placeholder="Email đăng nhập"
value="{{ old('email') }}"
required>
</div>

<div class="input-group">
<input type="password"
name="password"
placeholder="Mật khẩu"
required>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;margin-bottom:15px;">

<label style="display:flex;align-items:center;gap:5px;">
<input type="checkbox" name="remember">
Ghi nhớ đăng nhập
</label>

@if (Route::has('password.request'))
<a href="{{ route('password.request') }}"
style="color:#38bdf8;text-decoration:none;">
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

    <!-- FOOTER -->
    @include('components.footer')

</div>

</body>
</html>