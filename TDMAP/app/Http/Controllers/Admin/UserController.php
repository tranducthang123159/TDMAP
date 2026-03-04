<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ($request->has('is_admin')) {
            $user->assignRole('admin');
        } else {
            $user->removeRole('admin');
        }

        return redirect()->back()->with('success', 'Cập nhật quyền thành công');
    }
}