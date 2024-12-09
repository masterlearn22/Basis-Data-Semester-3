@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Detail Penerimaan</h2>

        <!-- Tambahkan form GET untuk submit otomatis ketika barang diubah -->
        <form action="{{ route('detail_penerimaan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpenerimaan">Penerimaan</label>
                <select class="form-control" id="idpenerimaan" name="idpenerimaan">
                    @foreach($penerimaans as $penerimaan)
                        <option value="{{ $penerimaan->idpenerimaan }}">{{ $penerimaan->idpenerimaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <!-- Event onchange untuk submit form ketika barang dipilih -->
                <select class="form-control" id="idbarang" name="idbarang" >
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ request('idbarang') == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label for="jumlah_terima">Jumlah Terima</label>
                <input type="number" class="form-control" id="jumlah_terima" name="jumlah_terima" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Detail Penerimaan</button>
        </form>
    </div>
@endsection
