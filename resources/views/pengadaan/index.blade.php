<!-- resources/views/pengadaan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Pengadaan</h2>
        <a href="{{ route('pengadaan.create') }}" class="mb-3 btn btn-primary">Add Pengadaan</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pengadaan</th>
                    <th>Vendor</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Total Nilai</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengadaans as $pengadaan)
                    <tr>
                        <td>{{ $pengadaan->idpengadaan }}</td>
                        <td>{{ $pengadaan->nama_vendor }}</td>
                        <td>{{ $pengadaan->username }}</td>
                        <td>
                            @if($pengadaan->status == 0)
                                <span class="badge badge-warning">Belum Diproses</span>
                            @elseif($pengadaan->status == 1)
                                <span class="badge badge-primary">Sedang Diproses</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($pengadaan->total_nilai) }}</td>
                        <td>
                            <a href="{{ route('pengadaan.edit', $pengadaan->idpengadaan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('pengadaan.destroy', $pengadaan->idpengadaan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this pengadaan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 row">
            <div class="col-12">
                <h3>Detail Pengadaan</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Pengadaan</th>
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
                                <td>Rp {{ number_format($detail_pengadaan->harga_satuan) }}</td>
                                <td>{{ $detail_pengadaan->jumlah }}</td>
                                <td>Rp {{ number_format($detail_pengadaan->sub_total) }}</td>
                                <td>
                                    <a href="{{ route('detail_pengadaan.edit', $detail_pengadaan->iddetail_pengadaan) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('detail_pengadaan.destroy', $detail_pengadaan->iddetail_pengadaan) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this detail pengadaan?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection