<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
     * Xử lý đăng nhập
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // 🔴 Kiểm tra email đã xác thực chưa
$user = Auth::user();

/* chưa xác minh */

if (!$user->email_verified_at) {

return redirect('/verify-otp');

}

/* chưa có OTP */

if (!$user->otp_code) {

Auth::logout();

return back()->withErrors([
'email' => 'Tài khoản chưa kích hoạt OTP'
]);

}

        return redirect('/')
            ->with('success', 'Bạn đã đăng nhập thành công!');
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