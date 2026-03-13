<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success','Tạo user thành công');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user','roles'));
    }

public function update(Request $request, User $user)
{

$request->validate([
'name' => 'required',
'email' => 'required|email',
'role' => 'required'
]);

/* ghép OTP */

$otp =
$request->otp1 .
$request->otp2 .
$request->otp3 .
$request->otp4 .
$request->otp5 .
$request->otp6;

$user->update([

'name' => $request->name,
'email' => $request->email,
'otp_code' => $otp

]);

$user->syncRoles([$request->role]);

return redirect()
->route('users.index')
->with('success','Cập nhật thành công');

}

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success','Đã xóa');
    }
}