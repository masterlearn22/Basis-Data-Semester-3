<!-- resources/views/margin_penjualan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Margin Penjualan</h2>

        <a href="{{ route('margin_penjualan.create') }}" class="btn btn-primary mb-3">Add Margin Penjualan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Persen</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($margin_penjualans as $margin_penjualan)
                    <tr>
                        <td>{{ $margin_penjualan->persen }} %</td>
                        <td>
                            <a href="{{ route('margin_penjualan.edit', $margin_penjualan->idmargin_penjualan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('margin_penjualan.destroy', $margin_penjualan->idmargin_penjualan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this margin penjualan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
