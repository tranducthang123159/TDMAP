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

$otp = $user->otp_code;

if (
    $request->filled('otp1') &&
    $request->filled('otp2') &&
    $request->filled('otp3') &&
    $request->filled('otp4') &&
    $request->filled('otp5') &&
    $request->filled('otp6')
) {
    $otp =
        $request->otp1 .
        $request->otp2 .
        $request->otp3 .
        $request->otp4 .
        $request->otp5 .
        $request->otp6;
}
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
    // ❌ Không cho xóa chính mình
    if ($user->id == auth()->id()) {
        return back()->with('error','Không thể xóa chính mình');
    }

    // ❌ Không cho xóa admin (tuỳ bạn)
    if ($user->hasRole('admin')) {
        return back()->with('error','Không thể xóa tài khoản admin');
    }

    // 🔥 Xóa role trước (cho sạch)
    $user->syncRoles([]);

    // ✅ Xóa user
    $user->delete();

    return back()->with('success','Đã xóa user thành công');
}
}