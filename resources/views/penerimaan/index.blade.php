@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penerimaan</h2>

        <a href="{{ route('penerimaan.create') }}" class="mb-3 btn btn-primary">Add Penerimaan</a>

        <table class="table">
            <thead>
                <tr>
                    <th>ID Penerimaan</th>
                    <th>Pengadaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penerimaans as $penerimaan)
                <tr>
                    <td>{{ $penerimaan->idpenerimaan }}</td>
                    <td>{{ $penerimaan->idpengadaan }}</td>
                    <td>
                        @if($penerimaan->status == 0)
                            <span>Belum Diapprove</span>
                        @else
                            <span class="badge badge-success">Sudah Diapprove</span>
                        @endif
                    </td>
                    <td>
                        @if($penerimaan->status == 0)
                            <form action="{{ route('penerimaan.approve', $penerimaan->idpenerimaan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 row">
            <div class="col-12">
                <h3>Detail Penerimaan</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Penerimaan</th>
                            <th>Barang</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah Terima</th>
                            <th>Sub Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail_penerimaans as $detailPenerimaan)
                            <tr>
                                <td>{{ $detailPenerimaan->idpenerimaan }}</td>
                                <td>{{ $detailPenerimaan->nama_barang }}</td>
                                <td>{{ number_format($detailPenerimaan->harga_satuan, 2) }}</td>
                                <td>{{ $detailPenerimaan->jumlah_terima }}</td>
                                <td>{{ number_format($detailPenerimaan->sub_total, 2) }}</td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection