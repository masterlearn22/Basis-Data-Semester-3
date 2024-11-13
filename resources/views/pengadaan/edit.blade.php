@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Pengadaan</h2>

        <form action="{{ route('pengadaan.update', $pengadaan->idpengadaan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idvendor">Vendor</label>
                <select class="form-control" id="idvendor" name="idvendor">
                    @foreach($vendors as $vendor)  <!-- Menggunakan $vendors -->
                        <option value="{{ $vendor->idvendor }}" {{ $pengadaan->idvendor == $vendor->idvendor ? 'selected' : '' }}>
                            {{ $vendor->nama_vendor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser">
                    @foreach($users as $user)  <!-- Menggunakan $users -->
                        <option value="{{ $user->iduser }}" {{ $pengadaan->iduser == $user->iduser ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="subtotal_awal">Subtotal Awal</label>
                <input type="number" class="form-control" id="subtotal_awal" name="subtotal_awal" value="{{ $pengadaan->subtotal_nilai }}" required>
            </div>

            <div class="form-group">
                <label for="ppn">PPN</label>
                <input type="number" class="form-control" id="ppn" name="ppn" value="{{ $pengadaan->ppn }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="{{ $pengadaan->status }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Pengadaan</button>
        </form>
    </div>
@endsection
