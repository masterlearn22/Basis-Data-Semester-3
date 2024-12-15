<!-- resources/views/retur/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Retur</h2>

        <!-- Ganti link dengan button modal -->
        <button type="button" class="mb-3 btn btn-primary" data-toggle="modal" data-target="#createReturModal">
            Add Retur
        </button>

        <!-- Tabel Retur dan Detail Retur tetap sama -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Penerimaan</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returs as $retur)
                    <tr>
                        <td>{{ $retur->idpenerimaan }}</td>
                        <td>{{ $retur->username }}</td>
                        <td>
                            <a href="{{ route('retur.edit', $retur->idretur) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('retur.destroy', $retur->idretur) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this retur?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal Create Retur -->
        <div class="modal fade" id="createReturModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Retur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="returForm" action="{{ route('retur.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="idpenerimaan">Penerimaan</label>
                                <select class="form-control" id="idpenerimaan" name="idpenerimaan" required>
                                    @foreach ($penerimaans as $penerimaan)
                                        <option value="{{ $penerimaan->idpenerimaan }}">{{ $penerimaan->idpenerimaan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="iduser">User</label>
                                <select class="form-control" id="iduser" name="iduser" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required
                                    min="1">
                            </div>

                            <div class="form-group">
                                <label for="alasan">Alasan</label>
                                <input type="text" class="form-control" id="alasan" name="alasan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Retur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                    @foreach ($detail_returs as $detail_retur)
                        <tr>
                            <td>{{ $detail_retur->idretur }}</td>
                            <td>{{ $detail_retur->iddetail_penerimaan }}</td>
                            <td>{{ $detail_retur->alasan }}</td>
                            <td>{{ $detail_retur->jumlah }}</td>
                            <td>
                                <a href="{{ route('detail_retur.edit', $detail_retur->iddetail_retur) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            // Submit Form via AJAX
            $('#returForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Tutup modal
                        $('#createReturModal').modal('hide');

                        // Refresh halaman atau update tabel
                        location.reload();
                    },
                    error: function(xhr) {
                        // Tampilkan error
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        alert(errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
