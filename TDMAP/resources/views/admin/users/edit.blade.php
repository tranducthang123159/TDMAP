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

{{-- ================= OTP ================= --}}
@php
$otp = $user->otp_code ? str_pad($user->otp_code,6,'0',STR_PAD_LEFT) : '';
$digits = $otp ? str_split($otp) : ['', '', '', '', '', ''];
@endphp

<div class="mb-3">
<label>OTP xác minh</label>

<div style="display:flex;gap:3px">
@for($i = 0; $i < 6; $i++)
<input class="otp-box" name="otp{{ $i+1 }}" maxlength="1" value="{{ $digits[$i] }}">
@endfor
</div>

<button type="button"
class="btn btn-sm btn-secondary mt-2"
onclick="randomOTP()">
Random OTP
</button>

<small class="text-muted d-block mt-1">
Để trống nếu không muốn thay đổi OTP
</small>

</div>

{{-- ================= ROLE ================= --}}

<div class="mb-3">

<label>Quyền hiện tại:</label>
<p><b>{{ $user->roles->first()?->name ?? 'Chưa có' }}</b></p>

<label>
<input type="checkbox" id="changeRole">
 Đổi quyền
</label>

<select name="role" id="roleSelect" class="form-control mt-2" disabled>

<option value="">-- Chọn quyền --</option>

@foreach($roles as $role)
<option value="{{ $role->name }}">
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

// random OTP
function randomOTP(){
    let otp = Math.floor(100000 + Math.random()*900000).toString();

    for(let i=0;i<6;i++){
        document.getElementsByName('otp'+(i+1))[0].value = otp[i];
    }
}

// bật/tắt đổi role
document.getElementById('changeRole').addEventListener('change', function(){
    document.getElementById('roleSelect').disabled = !this.checked;
});

</script>

@endsection