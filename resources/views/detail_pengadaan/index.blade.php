<!-- resources/views/detail_pengadaan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Detail Pengadaan</h2>

        <a href="{{ route('detail_pengadaan.create') }}" class="btn btn-primary mb-3">Add Detail Pengadaan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pengadaan</th>
                    <th>Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Sub Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail_pengadaans as $detail_pengadaan)
                    <tr>
                        <td>{{ $detail_pengadaan->idpengadaan }}</td>
                        <td>{{ $detail_pengadaan->nama }}</td>
                        <td>{{ $detail_pengadaan->harga_satuan }}</td>
                        <td>{{ $detail_pengadaan->jumlah }}</td>
                        <td>{{ $detail_pengadaan->sub_total }}</td>
                        <td>
                            <a href="{{ route('detail_pengadaan.edit', $detail_pengadaan->iddetail_pengadaan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('detail_pengadaan.destroy', $detail_pengadaan->iddetail_pengadaan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this detail pengadaan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
