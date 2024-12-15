<!-- resources/views/penjualan/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>List of Penjualan</h2>

        <!-- Ganti link dengan button modal -->
        <button type="button" class="mb-3 btn btn-primary" data-toggle="modal" data-target="#createPenjualanModal">
            Add Penjualan
        </button>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Penjualan</th>
                    <th>Margin Penjualan</th>
                    <th>Total Nilai</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $penjualan)
                    <tr>
                        <td>{{ $penjualan->idpenjualan }}</td>
                        <td>{{ $penjualan->persen }}%</td>
                        <td>Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</td>
                        <td>{{ $penjualan->username }}</td>
                        <td>
                            <a href="{{ route('penjualan.edit', $penjualan->idpenjualan) }}" class="btn btn-warning">Edit</a>

                            <form action="{{ route('penjualan.destroy', $penjualan->idpenjualan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this penjualan?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 row">
            <div class="col-12">
                <h3>Detail Penjualan</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Penjualan</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>SubTotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail_penjualans as $detail_penjualan)
                            
                            <tr>
                                <td>{{ $detail_penjualan->idpenjualan }}</td>
                                <td>{{ $detail_penjualan->nama }}</td>
                                <td>{{ $detail_penjualan->jumlah }}</td>
                                <td>Rp {{ number_format($detail_penjualan->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail_penjualan->subtotal, 0, ',', '.') }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

       <!-- Modal Create Penjualan -->
       <div class="modal fade" id="createPenjualanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="penjualanForm" action="{{ route('penjualan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="idmargin_penjualan">Margin Penjualan (%)</label>
                            <select class="form-control" id="idmargin_penjualan" name="idmargin_penjualan" required>
                                @foreach($margin_penjualans as $margin_penjualan)
                                    <option value="{{ $margin_penjualan->idmargin_penjualan }}">
                                        {{ $margin_penjualan->persen }}%
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="iduser">User</label>
                            <select class="form-control" id="iduser" name="iduser" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                Detail Barang 
                                <button type="button" id="btnTambahBarang" class="float-right btn btn-primary btn-sm">
                                    + Tambah Barang
                                </button>
                            </div>
                            <div class="card-body" id="containerBarang">
                                <div class="row barang-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Barang</label>
                                            <select name="idbarang[]" class="form-control barang-select" required>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->idbarang }}" 
                                                            data-stock="{{ $barang->stock ?? 0 }}">
                                                        {{ $barang->nama }} (Stok: {{ $barang->stock ?? 0 }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jumlah</label>
                                            <input type="number" name="jumlah[]" class="form-control jumlah-input" 
                                                   required min="1" max="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger form-control btn-hapus-barang">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection