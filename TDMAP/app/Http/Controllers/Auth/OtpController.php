<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpController extends Controller
{

public function form()
{
return view('auth.verify-otp');
}

public function verify(Request $request)
{

$request->validate([
'otp' => 'required'
]);

$user = Auth::user();

/* OTP sai */

if ($user->otp_code != $request->otp) {

Auth::logout();

return redirect('/login')
->withErrors(['email'=>'OTP không đúng']);

}

/* OTP hết hạn */

if ($user->otp_expire && Carbon::now()->gt($user->otp_expire)) {

return redirect('/verify-otp')
->with('error','OTP đã hết hạn, vui lòng gửi lại mã');

}

/* xác minh */

$user->email_verified_at = now();
$user->otp_code = null;
$user->otp_expire = null;
$user->save();

return redirect('/')
->with('success','Xác minh thành công');

}

/* ======================
GỬI LẠI OTP
====================== */

public function resend()
{

$user = Auth::user();

/* tạo OTP mới */

$otp = rand(100000,999999);

$user->update([

'otp_code'=>$otp,
'otp_expire'=>Carbon::now()->addMinutes(5)

]);

/* gửi mail */

Mail::raw("Mã OTP mới của bạn là: $otp", function ($message) use ($user) {

$message->to($user->email)
->subject('OTP mới xác minh tài khoản');

});

return back()->with('success','OTP mới đã gửi vào email');

}

}