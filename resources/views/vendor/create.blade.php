<!-- resources/views/vendor/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Vendor</h2>

        <form action="{{ route('vendor.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_vendor">Nama Vendor</label>
                <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" required>
            </div>

            <div class="form-group">
                <label for="badan_hukum">Badan Hukum</label>
                <input type="text" class="form-control" id="badan_hukum" name="badan_hukum" required>
            </div>

            <div class="form-group">
                <label for="status">status</label>
                <input type="text" class="form-control" id="status" name="status" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Vendor</button>
        </form>
    </div>
@endsection
