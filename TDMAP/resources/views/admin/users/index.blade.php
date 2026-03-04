<h2>Quản lý người dùng</h2>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Email</th>
        <th>Admin</th>
        <th>Hành động</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
            {{ $user->hasRole('admin') ? 'Có' : 'Không' }}
        </td>
        <td>
            <form method="POST" action="{{ route('admin.users.updateRole', $user) }}">
                @csrf

                <input type="checkbox" name="is_admin"
                    {{ $user->hasRole('admin') ? 'checked' : '' }}>

                <button type="submit">Lưu</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>