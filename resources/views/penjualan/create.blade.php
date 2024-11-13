<!-- resources/views/penjualan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Penjualan</h2>

        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="subtotal_awal">Subtotal Awal</label>
                <input type="number" class="form-control" id="subtotal_awal" name="subtotal_awal" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="subtotal_akhir">Subtotal Akhir</label>
                <input type="number" class="form-control" id="subtotal_akhir" name="subtotal_akhir" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="ppn">PPN</label>
                <input type="number" class="form-control" id="ppn" name="ppn" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="idmargin_penjualan">Margin Penjualan</label>
                <select class="form-control" id="idmargin_penjualan" name="idmargin_penjualan">
                    @foreach($margin_penjualans as $margin_penjualan)
                        <option value="{{ $margin_penjualan->idmargin_penjualan }}">{{ $margin_penjualan->persen }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser">
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Penjualan</button>
        </form>
    </div>
@endsection
