<!-- resources/views/pengadaan/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Pengadaan</h2>
        
        <form action="{{ route('pengadaan.store') }}" method="POST">
            @csrf
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
                        <label>user</label>
                        <select name="iduser" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->iduser }}">{{ $user->username }}</option>
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
            </div>
        
            <div class="card">
                <div class="card-header">
                    <h4>Detail Barang</h4>
                    <button type="button" id="addBarang" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
                <div class="card-body" id="barangContainer">
                    <div class="row barang-row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Barang</label>
                                <select name="barang[]" class="form-control" required>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->idbarang }}">{{ $barang->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah[]" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger form-control remove-barang">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <button type="submit" class="btn btn-success">Simpan Pengadaan</button>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            // Tambah baris barang
            $('#addBarang').click(function() {
                var newRow = $('.barang-row:first').clone();
                
                // Reset select dan input
                newRow.find('select').prop('selectedIndex', 0);
                newRow.find('input[type="number"]').val('');
                
                // Tambahkan ke container
                $('#barangContainer').append(newRow);
            });
        
            // Hapus baris barang
            $(document).on('click', '.remove-barang', function() {
                // Pastikan minimal satu baris tetap ada
                if ($('.barang-row').length > 1) {
                    $(this).closest('.barang-row').remove();
                } else {
                    alert('Minimal harus ada satu barang');
                }
            });
        });
        </script>
@endsection
