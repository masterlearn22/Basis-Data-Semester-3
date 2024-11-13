<!-- resources/views/satuan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Satuan</h2>

        <form action="{{ route('satuan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_satuan">Nama Satuan</label>
                <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Satuan</button>
        </form>
    </div>
@endsection
