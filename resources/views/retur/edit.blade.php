<!-- resources/views/retur/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Retur</h2>

        <form action="{{ route('retur.update', $retur->idretur) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idpenerimaan">Penerimaan</label>
                <select class="form-control" id="idpenerimaan" name="idpenerimaan">
                    @foreach($penerimaans as $penerimaan)
                        <option value="{{ $penerimaan->idpenerimaan }}" {{ $retur->idpenerimaan == $penerimaan->idpenerimaan ? 'selected' : '' }}>
                            {{ $penerimaan->idpenerimaan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control" id="iduser" name="iduser">
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}" {{ $retur->iduser == $user->iduser ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Retur</button>
        </form>
    </div>
@endsection
