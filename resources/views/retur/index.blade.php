<!-- resources/views/retur/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Retur</h2>

        <a href="{{ route('retur.create') }}" class="mb-3 btn btn-primary">Add Retur</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Penerimaan</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returs as $retur)
                    <tr>
                        <td>{{ $retur->idpenerimaan }}</td>
                        <td>{{ $retur->username }}</td>
                        <td>
                            <a href="{{ route('retur.edit', $retur->idretur) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('retur.destroy', $retur->idretur) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this retur?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 row">
            <div class="col-12">
                <h3>Detail Retur</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Retur</th>
                            <th>Detail Penerimaan</th>
                            <th>Alasan</th>
                            <th>Jumlah</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail_returs as $detail_retur)
                            <tr>
                                <td>{{ $detail_retur->idretur }}</td>
                                <td>{{ $detail_retur->iddetail_penerimaan }}</td>
                                <td>{{ $detail_retur->alasan }}</td>
                                <td>{{ $detail_retur->jumlah }}</td>
                                <td>
                                    <a href="{{ route('detail_retur.edit', $detail_retur->iddetail_retur) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('detail_retur.destroy', $detail_retur->iddetail_retur) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this detail retur?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection