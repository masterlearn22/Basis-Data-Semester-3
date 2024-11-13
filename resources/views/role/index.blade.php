<!-- resources/views/role/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Roles</h2>

        <a href="{{ route('role.create') }}" class="btn btn-primary mb-3">Add Role</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->nama_role }}</td>
                        <td>
                            <a href="{{ route('role.edit', $role->idrole) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('role.destroy', $role->idrole) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
