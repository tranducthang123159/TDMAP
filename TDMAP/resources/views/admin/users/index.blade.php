@extends('admin.layout.header')

@section('title')
Danh sách người dùng
@endsection

@section('content')

<div class="container-fluid mt-3">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Danh sách người dùng</h5>
        </div>

        <div class="card-body p-0">

            <table class="table table-bordered table-hover mb-0 text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="d-none d-lg-table-cell">Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($users as $user)

                <tr>
                    <td>{{ $user->id }}</td>

                    <td>{{ $user->name }}</td>

                    <td class="d-none d-md-table-cell">
                        {{ $user->email }}
                    </td>

                    <td class="d-none d-lg-table-cell">
                        @foreach($user->roles as $role)

                        <span class="badge badge-info">
                            {{ $role->name }}
                        </span>

                        @endforeach
                    </td>
<td>

@if($user->email_verified_at)

<span class="badge bg-success">
Đã xác minh
</span>

@else

<span class="badge bg-danger">
Chưa xác minh
</span>

@endif

</td>
                    <td>
                        <a href="{{ route('users.edit',$user->id) }}"
                           class="btn btn-warning btn-sm">
                           Sửa
                        </a>
                    </td>

                </tr>

                @endforeach

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection