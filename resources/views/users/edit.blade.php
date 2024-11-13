<!-- resources/views/users/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit User</h2>

        <form action="{{ route('users.update', $user->iduser) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti password.</small>
            </div>

            <div class="form-group">
                <label for="idrole">Role</label>
                <select class="form-control" id="idrole" name="idrole">
                    @foreach($roles as $role)
                        <option value="{{ $role->idrole }}" {{ $user->idrole == $role->idrole ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
@endsection
