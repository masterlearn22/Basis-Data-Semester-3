<!-- resources/views/satuan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Daftar Satuan</h2>

        <a href="{{ route('satuan.create') }}" class="btn btn-success mb-3">Tambah Satuan</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Satuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($satuans as $satuan)
                    <tr>
                        <td>{{ $satuan->idsatuan }}</td>
                        <td>{{ $satuan->nama_satuan }}</td >
                        <td>{{ $satuan->status }}</td>
                        <td>
                            <a href="{{ route('satuan.edit', $satuan->idsatuan) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('satuan.destroy', $satuan->idsatuan) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
