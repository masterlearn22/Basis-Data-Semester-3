<!-- resources/views/detail_retur/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Detail Retur</h2>

        <form action="{{ route('detail_retur.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idretur">Retur</label>
                <select class="form-control" id="idretur" name="idretur">
                    @foreach($returs as $retur)
                        <option value="{{ $retur->idretur }}">{{ $retur->idretur }}</option>
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
                <label for="iddetail_penerimaan">Detail Penerimaan</label>
                <select class="form-control" id="iddetail_penerimaan" name="iddetail_penerimaan">
                    @foreach($detail_penerimaans as $detail_penerimaan)
                        <option value="{{ $detail_penerimaan->iddetail_penerimaan }}">{{ $detail_penerimaan->iddetail_penerimaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="alasan">Alasan</label>
                <input type="text" class="form-control" id="alasan" name="alasan" required>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Detail Retur</button>
        </form>
    </div>
@endsection
