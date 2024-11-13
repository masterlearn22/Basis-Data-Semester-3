<!-- resources/views/detail_penjualan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Detail Penjualan</h2>

        <form action="{{ route('detail_penjualan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpenjualan">Penjualan</label>
                <select class="form-control" id="idpenjualan" name="idpenjualan">
                    @foreach($penjualans as $penjualan)
                        <option value="{{ $penjualan->idpenjualan }}">{{ $penjualan->idpenjualan }}</option>
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
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>

            <div class="form-group">
                <label for="harga_satuan">Harga Satuan</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Detail Penjualan</button>
        </form>
    </div>
@endsection
