@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Detail Penerimaan</h2>

        <!-- Form untuk memilih barang -->
        <form action="{{ route('detail_penerimaan.update', $detail_penerimaan->iddetail_penerimaan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="idbarang">Barang</label>
                <select class="form-control" id="idbarang" name="idbarang" onchange="updateHarga()">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ $barang->idbarang == $selectedIdBarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <!-- Tampilkan harga barang, hanya sebagai informasi -->
            <div class="form-group">
                <label for="harga_satuan">Harga Satuan</label>
                <input type="text" class="form-control" id="harga_satuan" name="harga_satuan" value="{{ $selectedBarang->harga ?? '' }}" readonly>
            </div>
        </form>
        
        <!-- Form untuk update detail penerimaan -->
        <form action="{{ route('detail_penerimaan.update', ['detail_penerimaan' => $detail_penerimaan->iddetail_penerimaan]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="idbarang" id="hidden_idbarang" value="{{ $selectedIdBarang }}">

            <div class="form-group">
                <label for="jumlah_terima">Jumlah Terima</label>
                <input type="number" class="form-control" id="jumlah_terima" name="jumlah_terima" value="{{ $detail_penerimaan->jumlah_terima }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Detail Penerimaan</button>
        </form>
    </div>

    <script>
        function updateHarga() {
            var idBarang = document.getElementById('idbarang').value;

            // Lakukan AJAX untuk mendapatkan harga barang berdasarkan idBarang
            fetch(`/barang/${idBarang}`)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(function(data) {
                    // Perbarui field harga satuan
                    if (data && data.harga) {
                        document.getElementById('harga_satuan').value = data.harga;
                    } else {
                        document.getElementById('harga_satuan').value = ''; // Atau pesan lain jika harga tidak ditemukan
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
            
            // Update hidden input with the selected barang ID
            document.getElementById('hidden_idbarang').value = idBarang;
        }
    </script>
@endsection
