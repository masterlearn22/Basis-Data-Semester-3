<!-- resources/views/vendor/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Vendor</h2>

        <form action="{{ route('vendor.update', $vendor->idvendor) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_vendor">Nama Vendor</label>
                <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" value="{{ $vendor->nama_vendor }}" required>
            </div>

            <div class="form-group">
                <label for="badan_hukum">Badan Hukum</label>
                <input type="text" class="form-control" id="badan_hukum" name="badan_hukum" value="{{ $vendor->badan_hukum }}" required>
            </div>

            <div class="form-group">
                <label for="status">status</label>
                <input type="text" class="form-control" id="status" name="status" value="{{ $vendor->status }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Vendor</button>
        </form>
    </div>
@endsection
