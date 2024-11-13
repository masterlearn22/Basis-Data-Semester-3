<!-- resources/views/barang/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Barang</h2>

        <form action="{{ route('barang.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama">Jenis </label>
                <input type="text" class="form-control" id="jenis" name="jenis" required>
            </div>

            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>

            <div class="form-group">
                <label for="idsatuan">Satuan</label>
                <select class="form-control" id="idsatuan" name="idsatuan">
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->idsatuan }}">{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="number" class="form-control" id="status" name="status" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Barang</button>
        </form>
    </div>
@endsection
