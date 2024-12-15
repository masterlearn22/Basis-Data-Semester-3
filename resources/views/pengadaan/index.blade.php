<!-- resources/views/pengadaan/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pengadaan</h2>

    <!-- Tombol Tambah Pengadaan -->
    <button type="button" class="mb-3 btn btn-primary" data-toggle="modal" data-target="#createPengadaanModal">
        Tambah Pengadaan
    </button>

    <!-- Tabel Pengadaan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>User</th>
                <th>Status</th>
                <th>Total Nilai</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($pengadaans as $p)
            <tr>
                <td>{{ $p->idpengadaan }}</td>
                <td>{{ $p->nama_vendor }}</td>
                <td>{{ $p->username }}</td>
                <td>
                    @switch($p->status)
                        @case(0)
                            <span class="badge badge-warning">Belum </span>
                            @break
                        @case(1)
                            <span class="badge badge-primary">Selesai</span>
                            @break
                    @endswitch
                </td>
                <td>Rp {{ number_format($p->total_nilai) }}</td>
                
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 row">
        <div class="col-12">
            <h3>Detail Pengadaan</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID Pengadaan</th>
                        <th>Barang</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail_pengadaans as $detail_pengadaan)
                        <tr>
                            <td>{{ $detail_pengadaan->idpengadaan }}</td>
                            <td>{{ $detail_pengadaan->nama }}</td>
                            <td>Rp {{ number_format($detail_pengadaan->harga_satuan) }}</td>
                            <td>{{ $detail_pengadaan->jumlah }}</td>
                            <td>Rp {{ number_format($detail_pengadaan->sub_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Pengadaan -->
    <div class="modal fade" id="createPengadaanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengadaan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formPengadaan" method="POST" action="{{ route('pengadaan.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vendor</label>
                                    <select name="idvendor" class="form-control" required>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="iduser" class="form-control" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->iduser }}">{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                            <select name="barang[]" class="form-control" required>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->idbarang }}">{{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PPN (%)</label>
                                            <input type="number" name="ppn" class="form-control" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jumlah</label>
                                            <input type="number" name="jumlah[]" class="form-control" required min="1">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Tambah Barang
    $('#btnTambahBarang').click(function() {
        var barisBarang = $('.barang-row:first').clone();
        barisBarang.find('select').prop('selectedIndex', 0);
        barisBarang.find('input[type="number"]').val('');
        $('#containerBarang').append(barisBarang);
    });

    // Hapus Barang
    $(document).on('click', '.btn-hapus-barang', function() {
        if ($('.barang-row').length > 1) {
            $(this).closest('.barang-row').remove();
        } else {
            alert('Minimal satu barang');
        }
    });

    // Submit Form via AJAX
    $('#formPengadaan').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#createPengadaanModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menyimpan: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endpush
@endsection