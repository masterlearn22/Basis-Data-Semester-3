<!-- resources/views/vendor/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Vendors</h2>

        <a href="{{ route('vendor.create') }}" class="btn btn-primary mb-3">Add Vendor</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Vendor</th>
                    <th>Badan Hukum</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->nama_vendor }}</td>
                        <td>{{ $vendor->badan_hukum }}</td>
                        <td>{{ $vendor->status }}</td>
                        <td>
                            <a href="{{ route('vendor.edit', $vendor->idvendor) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('vendor.destroy', $vendor->idvendor) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vendor?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
