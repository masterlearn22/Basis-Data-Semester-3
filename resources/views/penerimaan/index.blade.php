@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penerimaan</h2>

        <a href="{{ route('penerimaan.create') }}" class="mb-3 btn btn-primary">Add Penerimaan</a>


        <table class="table">
            <thead>
                <tr>
                    <th>ID Penerimaan</th>
                    <th>Pengadaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penerimaans as $penerimaan)
                <tr>
                    <td>{{ $penerimaan->idpenerimaan }}</td>
                    <td>{{ $penerimaan->idpengadaan}}</td>
                    <td>
                        @if($penerimaan->status == 0)
                            <span>Belum Diapprove</span>
                        @else
                            <span class="badge badge-success">Sudah Diapprove</span>
                        @endif
                    </td>
                    <td>
                        @if($penerimaan->status == 0)
                            <form action="{{ route('penerimaan.approve', $penerimaan->idpenerimaan) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
