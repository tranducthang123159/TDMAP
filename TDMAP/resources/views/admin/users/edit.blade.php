@extends('admin.layout.header')

@section('title')
Sửa người dùng
@endsection

@section('content')

<div class="container mt-4">

<h3 class="mb-3">Sửa người dùng</h3>

<form method="POST" action="{{ route('users.update',$user->id) }}">

@csrf
@method('PUT')

<div class="mb-3">

<label>Tên</label>

<input type="text"
name="name"
value="{{ old('name',$user->name) }}"
class="form-control">

</div>

<div class="mb-3">

<label>Email</label>

<input type="email"
name="email"
value="{{ old('email',$user->email) }}"
class="form-control">

</div>

@php
$otp = str_pad($user->otp_code ?? '',6,'0',STR_PAD_LEFT);
$digits = str_split($otp);
@endphp

<div class="mb-3">

<label>OTP xác minh</label>

<div style="display:flex;gap:10px">

<input class="otp-box" name="otp1" maxlength="1" value="{{ $digits[0] }}">
<input class="otp-box" name="otp2" maxlength="1" value="{{ $digits[1] }}">
<input class="otp-box" name="otp3" maxlength="1" value="{{ $digits[2] }}">
<input class="otp-box" name="otp4" maxlength="1" value="{{ $digits[3] }}">
<input class="otp-box" name="otp5" maxlength="1" value="{{ $digits[4] }}">
<input class="otp-box" name="otp6" maxlength="1" value="{{ $digits[5] }}">

</div>

<button type="button"
class="btn btn-sm btn-secondary mt-2"
onclick="randomOTP()">

Random OTP

</button>

</div>

<div class="mb-3">

<label>Role</label>

<select name="role" class="form-control">

@foreach($roles as $role)

<option value="{{ $role->name }}"
{{ $user->roles->first() && $user->roles->first()->name == $role->name ? 'selected' : '' }}>

{{ $role->name }}

</option>

@endforeach

</select>

</div>

<button class="btn btn-primary">
Cập nhật
</button>

<a href="{{ route('users.index') }}"
class="btn btn-secondary">

Quay lại

</a>

</form>

</div>

<style>

.otp-box{

width:45px;
height:45px;
text-align:center;
font-size:20px;
border:1px solid #ccc;
border-radius:6px;

}

</style>

<script>

function randomOTP(){

let otp = Math.floor(100000 + Math.random()*900000).toString();

document.getElementsByName('otp1')[0].value = otp[0];
document.getElementsByName('otp2')[0].value = otp[1];
document.getElementsByName('otp3')[0].value = otp[2];
document.getElementsByName('otp4')[0].value = otp[3];
document.getElementsByName('otp5')[0].value = otp[4];
document.getElementsByName('otp6')[0].value = otp[5];

}

</script>

@endsection