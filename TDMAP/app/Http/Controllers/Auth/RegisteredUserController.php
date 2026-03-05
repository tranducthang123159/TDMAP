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
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],

            'password' => [
                'required',
                'confirmed',

                Password::min(8) // ít nhất 8 ký tự
                    ->letters() // có chữ
                    ->numbers() // có số
                    ->mixedCase() // chữ hoa chữ thường
                    ->symbols() // ký tự đặc biệt
            ]

        ],[

            // thông báo tiếng việt

            'name.required' => 'Vui lòng nhập họ và tên.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.letters' => 'Mật khẩu phải chứa chữ cái.',
            'password.mixed' => 'Mật khẩu phải có chữ hoa và chữ thường.',
            'password.numbers' => 'Mật khẩu phải chứa số.',
            'password.symbols' => 'Mật khẩu phải chứa ký tự đặc biệt.',

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

      return redirect()->route('verification.notice');
    }
}