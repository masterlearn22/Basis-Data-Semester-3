<!-- resources/views/detail_penjualan/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Detail Penjualan</h2>

        <form action="{{ route('detail_penjualan.update', $detail_penjualan->iddetail_penjualan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idpenjualan">Penjualan</label>
                <select class="form-control" id="idpenjualan" name="idpenjualan">
                    @foreach($penjualans as $penjualan)
                        <option value="{{ $penjualan->idpenjualan }}" {{ $detail_penjualan->idpenjualan == $penjualan->idpenjualan ? 'selected' : '' }}>
                            {{ $penjualan->idpenjualan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ $detail_penjualan->idbarang == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $detail_penjualan->jumlah }}" required>
            </div>

            <div class="form-group">
                <label for="harga_satuan">Harga Satuan</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" value="{{ $detail_penjualan->harga_satuan }}" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Detail Penjualan</button>
        </form>
    </div>
@endsection
