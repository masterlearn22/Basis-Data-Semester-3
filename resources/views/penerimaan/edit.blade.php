@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Penerimaan</h2>

        <form action="{{ route('penerimaan.update', ['penerimaan' => $penerimaan->idpenerimaan]) }}" method="POST">

            @csrf
            @method('PUT')
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

            <div class="form-group">
                <label for="iduser">Nama Penerima</label>
                <select class="form-control" id="iduser" name="iduser" required>
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Penerimaan</button>
        </form>
    </div>
@endsection
