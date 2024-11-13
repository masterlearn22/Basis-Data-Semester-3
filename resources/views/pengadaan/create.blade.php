<!-- resources/views/pengadaan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Pengadaan</h2>
        
        <form action="{{ route('pengadaan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="idvendor">Vendor</label>
                <select class="form-control" id="idvendor" name="idvendor" required>
                    <option value="">Pilih Vendor</option>
                    @foreach($vendors as $vendor) <!-- Menggunakan $vendors -->
                        <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser" required>
                    <option value="">Pilih User</option>
                    @foreach($users as $user) <!-- Menggunakan $users -->
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label for="ppn">PPN</label>
                <input type="number" class="form-control" id="ppn" name="ppn" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="0">Tidak Aktif</option>
                    <option value="1">Aktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Pengadaan</button>
        </form>
    </div>
@endsection
