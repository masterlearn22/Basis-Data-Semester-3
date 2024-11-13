@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Detail Pengadaan</h2>

        <form action="{{ route('detail_pengadaan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpengadaan">Pengadaan</label>
                <select class="form-control" id="idpengadaan" name="idpengadaan">
                    @foreach($pengadaan as $pengadaanItem)  <!-- Menggunakan nama yang berbeda -->
                        <option value="{{ $pengadaanItem->idpengadaan }}">{{ $pengadaanItem->idpengadaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang">
                    @foreach($barang as $barangItem)  <!-- Menggunakan nama yang berbeda -->
                        <option value="{{ $barangItem->idbarang }}">{{ $barangItem->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Detail Pengadaan</button>
        </form>
    </div>
@endsection
