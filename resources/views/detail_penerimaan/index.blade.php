<!-- resources/views/detail_penerimaan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Detail Penerimaan</h2>

        <a href="{{ route('detail_penerimaan.create') }}" class="mb-3 btn btn-primary">Add Detail Penerimaan</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Penerimaan</th>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Jumlah Terima</th>
                    <th>Sub Total</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail_penerimaans as $detail_penerimaan)
                    <tr>
                        <td>{{ $detail_penerimaan->idpenerimaan }}</td>
                        <td>{{ $detail_penerimaan->nama_barang }}</td>
                        <td>{{ $detail_penerimaan->harga_satuan}}</td>
                        <td>{{ $detail_penerimaan->jumlah_terima }}</td>
                        <td>{{ $detail_penerimaan->sub_total }}</td>
                        <td class="text-center">
                            <form action="{{ route('detail_penerimaan.destroy', $detail_penerimaan->iddetail_penerimaan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this detail penerimaan?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection