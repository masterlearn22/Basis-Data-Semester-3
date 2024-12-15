<!-- resources/views/users/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Users</h2>

        <a href="{{ route('users.create') }}" class="mb-3 btn btn-primary">Add User</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->nama_role }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user->iduser) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('users.destroy', $user->iduser) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p>
        ada perubahan yang ingin saya lakukan dari mulai sp dan trigger, 
        jadi di bagian pengadaan yaitu saat saya membuat pengadaan memasukkan barang, 
        jumlah, dan ppn, nanti secara otomatis akan menghitung  yaitu
         subtotal_nilai= jumlah * harga_satuan barang, dan total_nilai= subtotal_nilai + (ppn/subtotal_nilai), 
         saat pengadaan di insert dia akan masuk detail_pengadaan dan penerimaan statusnya 0, 
         di penerimaan akan ada action button yang jika di klik akan secara otomatis merubah status pengadaan tadi menjadi 1,
          dan akan masuk detail_penerimaan, pengadaan yang sudah berubah statusnya menjadi 1, 
          akan masuk secara otomatis ke kartu stok sebagai stok masuk
    </p>
@endsection
