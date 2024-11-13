<!-- resources/views/margin_penjualan/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Margin Penjualan</h2>

        <form action="{{ route('margin_penjualan.update', $margin_penjualan->idmargin_penjualan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="persen">Persen</label>
                <input type="number" class="form-control" id="persen" name="persen" value="{{ $margin_penjualan->persen }}" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Margin Penjualan</button>
        </form>
    </div>
@endsection
