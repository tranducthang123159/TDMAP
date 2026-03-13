<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOtpActive
{

public function handle(Request $request, Closure $next)
{

if (Auth::check()) {

$user = Auth::user();

/* admin xóa OTP */

if (!$user->otp_code) {

Auth::logout();

$request->session()->invalidate();
$request->session()->regenerateToken();

return redirect('/login')
->withErrors([
'email'=>'Phiên đăng nhập đã bị thu hồi. Vui lòng đăng nhập lại.'
]);

}

}

return $next($request);

}

}