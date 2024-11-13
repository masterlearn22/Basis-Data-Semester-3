<!-- resources/views/role/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Role</h2>

        <form action="{{ route('role.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_role">Nama Role</label>
                <input type="text" class="form-control" id="nama_role" name="nama_role" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Role</button>
        </form>
    </div>
@endsection
