<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị trang đăng ký
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản
     */
  public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','string','email','max:255','unique:users'],
        'password' => ['required','confirmed',Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
    ]);

    $otp = rand(100000,999999);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'otp_code' => $otp,
        'otp_expire' => Carbon::now()->addMinutes(5)
    ]);

    Mail::raw("Mã xác minh tài khoản của bạn là: $otp", function ($message) use ($user) {
        $message->to($user->email)
        ->subject('Xác minh tài khoản');
    });

    Auth::login($user);

    return redirect()->route('otp.form');
}
}