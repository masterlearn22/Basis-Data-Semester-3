@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penerimaan</h2>

        <a href="{{ route('penerimaan.create') }}" class="btn btn-primary mb-3">Add Penerimaan</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pengadaan</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Actions</th>    
                </tr>
            </thead>
            <tbody>
                @foreach($penerimaans as $penerimaan)
                <tr>
                    <td>{{ $penerimaan->idpengadaan }}</td>
                    <td>{{ $penerimaan->username ?? 'No User' }}</td> 
                    <td>{{ $penerimaan->status}}</td>  
                    <td>
                        <a href="{{ route('penerimaan.edit', $penerimaan->idpenerimaan) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('penerimaan.destroy', $penerimaan->idpenerimaan) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this penerimaan?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
                   
            </tbody>
        </table>
    </div>
@endsection
