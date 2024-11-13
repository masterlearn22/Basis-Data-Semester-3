<!-- resources/views/detail_pengadaan/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Detail Pengadaan</h2>

        <form action="{{ route('detail_pengadaan.update', $detail_pengadaan->iddetail_pengadaan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idpengadaan">Pengadaan</label>
                <select class="form-control" id="idpengadaan" name="idpengadaan">
                    @foreach($pengadaans as $pengadaan)
                        <option value="{{ $pengadaan->idpengadaan }}" {{ $detail_pengadaan->idpengadaan == $pengadaan->idpengadaan ? 'selected' : '' }}>
                            {{ $pengadaan->idpengadaan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ $detail_pengadaan->idbarang == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="harga_satuan">Harga Satuan</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" value="{{ $detail_pengadaan->harga_satuan }}" required>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $detail_pengadaan->jumlah }}" required>
            </div>

            <div class="form-group">
                <label for="sub_total">Sub Total</label>
                <input type="number" class="form-control" id="sub_total" name="sub_total" value="{{ $detail_pengadaan->sub_total }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Detail Pengadaan</button>
        </form>
    </div>
@endsection
