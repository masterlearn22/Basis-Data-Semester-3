<!-- resources/views/penjualan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penjualan</h2>

        <a href="{{ route('penjualan.create') }}" class="mb-3 btn btn-primary">Add Penjualan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subtotal Nilai</th>
                    <th>PPN</th>
                    <th>Margin Penjualan</th>
                    <th>Total Nilai</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $penjualan)
                    <tr>
                        <td>{{ $penjualan->subtotal_nilai }}</td>
                        <td>{{ $penjualan->ppn }}</td>
                        <td>{{ $penjualan->persen }}%</td>
                        <td>{{ $penjualan->total_nilai }}</td>
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
