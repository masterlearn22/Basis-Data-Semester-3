@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Detail Penjualan</h2>

        <form action="{{ route('detail_penjualan.update', $detail->iddetail_penjualan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idpenjualan">Penjualan</label>
                <select class="form-control" id="idpenjualan" name="idpenjualan">
                    @foreach($penjualans as $penjualan)
                        <option value="{{ $penjualan->idpenjualan }}" {{ $detail->idpenjualan == $penjualan->idpenjualan ? 'selected' : '' }}>
                            {{ $penjualan->idpenjualan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}">{{ $barang->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $detail->jumlah }}" required>
            </div>

            <div class="form-group">
                <label for="harga">Harga Satuan</label>
                <input type="number" class="form-control" id="harga" name="harga" value="{{ $detail->harga }}" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Detail Penjualan</button>
        </form>
    </div>
@endsection