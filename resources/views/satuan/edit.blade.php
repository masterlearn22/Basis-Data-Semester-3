<!-- resources/views/satuan/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Satuan</h2>

        <form action="{{ route('satuan.update', $satuan->idsatuan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_satuan">Nama Satuan</label>
                <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" value="{{ $satuan->nama_satuan }}" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="{{ $satuan->status }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Satuan</button>
        </form>
    </div>
@endsection
