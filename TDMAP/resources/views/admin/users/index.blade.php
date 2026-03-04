

<div class="container">
    <h3>Quản lý người dùng</h3>

    <a href="{{ route('users.create') }}"
       class="btn btn-primary mb-3">Thêm user</a>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Role</th>
            <th>Hành động</th>
        </tr>

        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @foreach($user->roles as $role)
                    <span class="badge bg-info">
                        {{ $role->name }}
                    </span>
                @endforeach
            </td>
            <td>
                <a href="{{ route('users.edit',$user->id) }}"
                   class="btn btn-warning btn-sm">Sửa</a>

                <form action="{{ route('users.destroy',$user->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                        Xóa
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    {{ $users->links() }}
</div>