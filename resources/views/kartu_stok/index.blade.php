@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Kartu Stok</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah Masuk</th>
                    <th>Jumlah Keluar</th>
                    <th>Stok</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kartu_stoks as $kartu_stok)
                    <tr>
                        <td>{{ $kartu_stok->nama }}</td>
                        <td>{{ $kartu_stok->jenis_transaksi }}</td>
                        <td>{{ $kartu_stok->masuk }}</td>
                        <td>{{ $kartu_stok->keluar }}</td>
                        <td>{{ $kartu_stok->stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
