<!-- resources/views/margin_penjualan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Margin Penjualan</h2>

        <form action="{{ route('margin_penjualan.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="persen">Persen</label>
                <input type="number" class="form-control" id="persen" name="persen" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Margin Penjualan</button>
        </form>
    </div>
@endsection
