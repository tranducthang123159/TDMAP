<!DOCTYPE html>
<html>
<head>

<title>Xác minh OTP</title>

<style>

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

.box{
background:white;
padding:40px;
border-radius:10px;
width:350px;
text-align:center;
}

input{
width:100%;
padding:12px;
font-size:18px;
margin-top:10px;
}

button{
margin-top:20px;
padding:12px;
width:100%;
background:#007bff;
color:white;
border:none;
border-radius:6px;
}

.error{
color:red;
}

</style>

</head>

<body>
@include('components.header')
<div class="box">

<h2>Xác minh email</h2>

@if(session('error'))
<p class="error">{{session('error')}}</p>
@endif

<form method="POST" action="{{route('otp.verify')}}">
@csrf

<input type="text" name="otp" placeholder="Nhập OTP">

<button>Xác minh</button>

</form>

</div>
@include('components.footer')
</body>
</html>