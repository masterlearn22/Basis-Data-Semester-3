<!-- resources/views/pengadaan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Pengadaan</h2>
        <a href="{{ route('pengadaan.create') }}" class="mb-3 btn btn-primary">Add Pengadaan</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pengadaan</th>
                    <th>Vendor</th>
                    <th>User</th>
                    <th>Subtotal Nilai</th>
                    <th>PPN</th>
                    <th>Total Nilai</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengadaans as $pengadaan)
                    <tr>
                        <td>{{ $pengadaan->idpengadaan }}</td>
                        <td>{{ $pengadaan->nama_vendor }}</td>
                        <td>{{ $pengadaan->username }}</td>
                        <td>{{ $pengadaan->subtotal_nilai }}</td>
                        <td>{{ $pengadaan->ppn }} %</td>
                        <td>{{ $pengadaan->total_nilai }}</td>
                        <td>{{ $pengadaan->status }}</td>
                        <td>
                            <a href="{{ route('pengadaan.edit', $pengadaan->idpengadaan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('pengadaan.destroy', $pengadaan->idpengadaan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this pengadaan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
