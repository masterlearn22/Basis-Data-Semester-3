<!-- resources/views/retur/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Retur</h2>

        <form action="{{ route('retur.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpenerimaan">Penerimaan</label>
                <select class="form-control" id="idpenerimaan" name="idpenerimaan">
                    @foreach($penerimaans as $penerimaan)
                        <option value="{{ $penerimaan->idpenerimaan }}">{{ $penerimaan->idpenerimaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser">
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Retur</button>
        </form>
    </div>
@endsection
