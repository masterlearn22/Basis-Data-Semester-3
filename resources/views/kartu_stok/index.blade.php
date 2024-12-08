@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Kartu Stok</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah Masuk</th>
                    <th>Jumlah Keluar</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kartu_stoks as $kartu_stok)
                    <tr>
                        <td>{{ $kartu_stok->nama_barang }}</td>
                        <td>{{ $kartu_stok->total_masuk }}</td>
                        <td>{{ $kartu_stok->total_keluar }}</td>
                        <td>{{ $kartu_stok->stok_saat_ini}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
