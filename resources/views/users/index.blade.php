<!-- resources/views/users/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Users</h2>

        <a href="{{ route('users.create') }}" class="mb-3 btn btn-primary">Add User</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->nama_role }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user->iduser) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('users.destroy', $user->iduser) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
