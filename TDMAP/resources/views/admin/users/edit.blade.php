
<div class="container">
    <h3>Sửa user</h3>

    <form method="POST"
          action="{{ route('users.update',$user->id) }}">
        @csrf
        @method('PUT')

        <input type="text" name="name"
               value="{{ $user->name }}"
               class="form-control mb-2">

        <input type="email" name="email"
               value="{{ $user->email }}"
               class="form-control mb-2">

        <select name="role" class="form-control mb-2">
            @foreach($roles as $role)
                <option value="{{ $role->name }}"
                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-primary">
            Cập nhật
        </button>
    </form>
</div>
