<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký tài khoản - Hệ thống địa chính</title>

<style>

*{box-sizing:border-box;}

body{
margin:0;
min-height:100vh;
font-family:'Segoe UI',sans-serif;

background:
linear-gradient(rgba(10,25,47,0.88), rgba(10,25,47,0.88)),
url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');

background-size:cover;
background-position:center;
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
}

/* WRAPPER */

.page-wrapper{
min-height:100vh;
display:flex;
flex-direction:column;
}

/* MAIN */

.main-content{
flex:1;
display:flex;
align-items:center;
justify-content:center;
padding:40px 20px;
}

/* CARD */

.register-card{

width:100%;
max-width:460px;

padding:40px;

border-radius:20px;

background:rgba(255,255,255,0.08);

backdrop-filter:blur(20px);

box-shadow:0 30px 60px rgba(0,0,0,0.5);

color:white;

animation:fadeIn 0.6s ease;

}

@keyframes fadeIn{

from{
opacity:0;
transform:translateY(20px);
}

to{
opacity:1;
transform:translateY(0);
}

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
}

.subtitle{
text-align:center;
font-size:13px;
opacity:0.7;
margin-bottom:25px;
}

.input-group{
margin-bottom:16px;
position:relative;
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
border-color:#00c6ff;
box-shadow:0 0 8px rgba(0,198,255,0.6);
}

/* Password Eye */

.eye{

position:absolute;

right:12px;

top:50%;

transform:translateY(-50%);

cursor:pointer;

}

/* Password strength */

.strength-bar{

height:6px;

width:0%;

background:red;

border-radius:6px;

margin-top:6px;

transition:0.3s;

}

.strength-text{
font-size:12px;
opacity:0.8;
}

/* BUTTON */

.register-btn{

width:100%;

padding:13px;

border:none;

border-radius:14px;

background:linear-gradient(135deg,#00c6ff,#0072ff);

font-weight:600;

font-size:15px;

cursor:pointer;

transition:0.3s;

}

.register-btn:hover{

transform:translateY(-2px);

box-shadow:0 10px 20px rgba(0,114,255,0.4);

}

.link-text{

margin-top:18px;

text-align:center;

font-size:13px;

opacity:0.9;

}

.error-box{

background:#ff4d4f;

padding:12px;

border-radius:10px;

margin-bottom:15px;

font-size:14px;

text-align:center;

}

/* MOBILE */

@media(max-width:480px){

.main-content{
align-items:flex-start;
padding-top:40px;
}

.register-card{
padding:25px;
border-radius:16px;
}

.logo{
font-size:30px;
}

.title{
font-size:18px;
}

}

</style>

</head>

<body>

@include('components.header')

<div class="page-wrapper">

<div class="main-content">

<div class="register-card">

<div class="logo">🧭</div>

<div class="title">ĐĂNG KÝ TÀI KHOẢN</div>

<div class="subtitle">Hệ thống quản lý đất đai & quy hoạch số</div>

<form method="POST" action="{{ route('register') }}">

@csrf

@if ($errors->any())

<div class="error-box">

<ul style="margin:0;padding:0;list-style:none;">

@foreach ($errors->all() as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

</div>

@endif


<div class="input-group">

<input type="text"

name="name"

placeholder="Họ và tên"

value="{{ old('name') }}"

required>

</div>


<div class="input-group">

<input type="email"

name="email"

placeholder="Email"

value="{{ old('email') }}"

required>

</div>


<div class="input-group">

<input type="password"

name="password"

id="password"

placeholder="Mật khẩu (ít nhất 8 ký tự)"

required>

<span class="eye" onclick="togglePassword()">👁</span>

<div class="strength-bar" id="strength-bar"></div>

<div class="strength-text" id="strength-text"></div>

</div>


<div class="input-group">

<input type="password"

name="password_confirmation"

placeholder="Xác nhận mật khẩu"

required>

</div>


<button type="submit" class="register-btn">

🚀 Tạo tài khoản

</button>

<div class="link-text">

Đã có tài khoản?

<a href="{{ route('login') }}" style="color:#00c6ff;text-decoration:none;">

Đăng nhập ngay

</a>

</div>

</form>

</div>

</div>

@include('components.footer')

</div>

<script>

function togglePassword(){

let password = document.getElementById("password");

if(password.type === "password"){

password.type = "text";

}else{

password.type = "password";

}

}

const passwordInput = document.getElementById("password");

const bar = document.getElementById("strength-bar");

const text = document.getElementById("strength-text");

passwordInput.addEventListener("input",function(){

let val = passwordInput.value;

let strength = 0;

if(val.length >= 8) strength++;

if(/[A-Z]/.test(val)) strength++;

if(/[0-9]/.test(val)) strength++;

if(/[^A-Za-z0-9]/.test(val)) strength++;

switch(strength){

case 0:

bar.style.width="0%";
text.innerHTML="";

break;

case 1:

bar.style.width="25%";
bar.style.background="red";
text.innerHTML="Mật khẩu yếu";

break;

case 2:

bar.style.width="50%";
bar.style.background="orange";
text.innerHTML="Mật khẩu trung bình";

break;

case 3:

bar.style.width="75%";
bar.style.background="#00c6ff";
text.innerHTML="Mật khẩu khá mạnh";

break;

case 4:

bar.style.width="100%";
bar.style.background="limegreen";
text.innerHTML="Mật khẩu mạnh";

break;

}

});

</script>

</body>
</html>