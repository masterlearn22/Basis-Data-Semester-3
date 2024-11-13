<!-- resources/views/users/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New User</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="idrole">Role</label>
                <select class="form-control" id="idrole" name="idrole">
                    @foreach($roles as $role) <!-- Ganti $users menjadi $roles -->
                        <option value="{{ $role->idrole }}">{{ $role->nama_role }}</option>
                    @endforeach
                </select>
            </div>
            

            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>
@endsection
