<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOtpActive
{
    public function handle(Request $request, Closure $next)
    {

        if(Auth::check()){

            $user = Auth::user();

            // chưa xác minh OTP
            if(!$user->email_verified_at){

                return redirect('/verify-otp');

            }

        }

        return $next($request);
    }
}