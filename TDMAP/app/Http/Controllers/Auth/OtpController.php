<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Auth::logout();

return redirect('/login')
->withErrors(['email'=>'OTP đã hết hạn']);

}

/* xác minh */

$user->email_verified_at = now();
$user->otp_code = null;
$user->otp_expire = null;
$user->save();

return redirect('/')
->with('success','Xác minh thành công');

}

}