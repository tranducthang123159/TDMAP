<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập (EMAIL hoặc SĐT)
     */
    public function store(Request $request): RedirectResponse
    {
        // 🔥 VALIDATE CHUẨN
        $request->validate([
            'email' => [
                'required',
                function ($attr, $value, $fail) {

                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
                    $isPhone = preg_match('/^0[0-9]{9}$/', $value);

                    if (!$isEmail && !$isPhone) {
                        $fail('Nhập email hoặc số điện thoại hợp lệ');
                    }
                }
            ],
            'password' => ['required','string'],
        ]);

        $login = $request->input('email');

        // 🔥 XÁC ĐỊNH LOGIN FIELD
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // 🔐 LOGIN
        if (!Auth::attempt([
            $field => $login,
            'password' => $request->password
        ], $request->boolean('remember'))) {

            return back()->withErrors([
                'email' => 'Email / SĐT hoặc mật khẩu không đúng!',
            ])->withInput();
        }

        // 🔄 REGENERATE SESSION
        $request->session()->regenerate();

        $user = Auth::user();

        // 🔴 CHECK VERIFY OTP
        if (!$user->email_verified_at) {
            return redirect('/verify-otp');
        }

        // ✅ SUCCESS
        return redirect('/')
            ->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Đăng xuất
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Bạn đã đăng xuất thành công!');
    }
}