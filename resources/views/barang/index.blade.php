<!-- resources/views/barang/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Barang</h2>

        <a href="{{ route('barang.create') }}" class="mb-3 btn btn-success">Tambah Barang</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $barang)
                <tr>   
                    <td>{{ $barang->jenis }}</td>
                    <td>{{ $barang->nama }}</td>
                    <td>{{ $barang->nama_satuan }}</td> <!-- Pastikan ini menggunakan nama_satuan -->
                    <td>{{  number_format($barang->harga) }}</td>
                    <td>{{ $barang->status }}</td>
                    <td>
                        <a href="{{ route('barang.edit', $barang->idbarang) }}" class="btn btn-warning">Edit</a>
            
                        <form action="{{ route('barang.destroy', $barang->idbarang) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this barang?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            
            </tbody>
        </table>
    </div>
@endsection
