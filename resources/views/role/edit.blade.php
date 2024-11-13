<!-- resources/views/role/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Role</h2>

        <form action="{{ route('role.update', $role->idrole) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_role">Nama Role</label>
                <input type="text" class="form-control" id="nama_role" name="nama_role" value="{{ $role->nama_role }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Role</button>
        </form>
    </div>
@endsection
