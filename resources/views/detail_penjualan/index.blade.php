<!-- resources/views/detail_penjualan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Detail Penjualan</h2>

        <a href="{{ route('detail_penjualan.create') }}" class="btn btn-primary mb-3">Add Detail Penjualan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Penjualan</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail_penjualans as $detail_penjualan)
                    <tr>
                        <td>{{ $detail_penjualan->idpenjualan }}</td>
                        <td>{{ $detail_penjualan->nama }}</td>
                        <td>{{ $detail_penjualan->jumlah }}</td>
                        <td>{{ $detail_penjualan->harga }}</td>
                        <td>
                            <a href="{{ route('detail_penjualan.edit', $detail_penjualan->iddetail_penjualan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('detail_penjualan.destroy', $detail_penjualan->iddetail_penjualan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this detail penjualan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
