@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Penjualan</h2>

        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="idmargin_penjualan">Margin Penjualan (%)</label>
                <select class="form-control" id="idmargin_penjualan" name="idmargin_penjualan" required>
                    @foreach($margin_penjualans as $margin_penjualan)
                        <option value="{{ $margin_penjualan->idmargin_penjualan }}">
                            {{ $margin_penjualan->persen }}%
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser" required>
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang" required>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}">
                            {{ $barang->nama }} (Stok: {{ $barang->stock ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" 
                       min="1" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Penjualan</button>
        </form>
    </div>
@endsection