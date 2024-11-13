@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Penerimaan</h2>

        <form action="{{ route('penerimaan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpengadaan">Pengadaan</label>
                <select class="form-control" id="idpengadaan" name="idpengadaan" required>
                    @foreach($pengadaans as $pengadaan)
                        <option value="{{ $pengadaan->idpengadaan }}">{{ $pengadaan->idpengadaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" required>
            </div>

             <!-- Tambahan kolom untuk memilih nama penerima berdasarkan iduser -->
             <div class="form-group">
                <label for="iduser">Nama Penerima</label>
                <select class="form-control" id="iduser" name="iduser" required>
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Penerimaan</button>
        </form>
    </div>
@endsection
