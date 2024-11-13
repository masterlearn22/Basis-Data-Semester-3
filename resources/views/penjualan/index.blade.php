<!-- resources/views/penjualan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penjualan</h2>

        <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-3">Add Penjualan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subtotal Awal</th>
                    <th>Subtotal Akhir</th>
                    <th>PPN</th>
                    <th>Margin Penjualan</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $penjualan)
                    <tr>
                        <td>{{ $penjualan->subtotal_awal }}</td>
                        <td>{{ $penjualan->subtotal_akhir }}</td>
                        <td>{{ $penjualan->ppn }}</td>
                        <td>{{ $penjualan->persen }}</td>
                        <td>{{ $penjualan->username }}</td>
                        <td>
                            <a href="{{ route('penjualan.edit', $penjualan->idpenjualan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('penjualan.destroy', $penjualan->idpenjualan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this penjualan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
