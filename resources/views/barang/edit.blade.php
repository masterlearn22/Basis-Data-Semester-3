<!-- resources/views/barang/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Barang</h2>

        <form action="{{ route('barang.update', $barang->idbarang) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama">Jenis </label>
                <input type="text" class="form-control" id="jenis" name="jenis" value="{{ $barang->jenis }}" required>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" value="{{ $barang->nama }}" required>
            </div>

            <div class="form-group">
                <label for="idsatuan">Satuan</label>
                <select class="form-control" id="idsatuan" name="idsatuan">
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->idsatuan }}" {{ $barang->idsatuan == $satuan->idsatuan ? 'selected' : '' }}>
                            {{ $satuan->nama_satuan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" value="{{ $barang->harga }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="number" class="form-control" id="status" name="status" value="{{ $barang->status }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Barang</button>
        </form>
    </div>
@endsection
