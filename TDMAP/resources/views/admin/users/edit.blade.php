@extends('admin.layout.header')

@section('title')
Sửa người dùng
@endsection

@section('content')

<div class="container mt-4">
    <h3 class="mb-3">Sửa người dùng</h3>

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tên</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ $user->roles->first() && $user->roles->first()->name == $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Cập nhật</button>

        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            Quay lại
        </a>

    </form>
</div>

@endsection