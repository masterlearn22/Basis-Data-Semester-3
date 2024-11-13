<!-- resources/views/detail_retur/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Detail Retur</h2>

        <form action="{{ route('detail_retur.update', $detail_retur->iddetail_retur) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idretur">Retur</label>
                <select class="form-control" id="idretur" name="idretur">
                    @foreach($returs as $retur)
                        <option value="{{ $retur->idretur }}" {{ $detail_retur->idretur == $retur->idretur ? 'selected' : '' }}>
                            {{ $retur->idretur }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ $detail_retur->idbarang == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iddetail_penerimaan">Detail Penerimaan</label>
                <select class="form-control" id="iddetail_penerimaan" name="iddetail_penerimaan">
                    @foreach($detail_penerimaans as $detail_penerimaan)
                        <option value="{{ $detail_penerimaan->iddetail_penerimaan }}" {{ $detail_retur->iddetail_penerimaan == $detail_penerimaan->iddetail_penerimaan ? 'selected' : '' }}>
                            {{ $detail_penerimaan->iddetail_penerimaan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="alasan">Alasan</label>
                <input type="text" class="form-control" id="alasan" name="alasan" value="{{ $detail_retur->alasan }}" required>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $detail_retur->jumlah }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Detail Retur</button>
        </form>
    </div>
@endsection
