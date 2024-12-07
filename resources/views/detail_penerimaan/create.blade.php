@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Detail Penerimaan</h2>

        <!-- Tambahkan form GET untuk submit otomatis ketika barang diubah -->
        <form action="{{ route('detail_penerimaan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="idpenerimaan">Penerimaan</label>
                <select class="form-control" id="idpenerimaan" name="idpenerimaan">
                    @foreach($penerimaans as $penerimaan)
                        <option value="{{ $penerimaan->idpenerimaan }}">{{ $penerimaan->idpenerimaan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="idbarang">Barang</label>
                <!-- Event onchange untuk submit form ketika barang dipilih -->
                <select class="form-control" id="idbarang" name="idbarang" onchange="updateHarga()">
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" {{ request('idbarang') == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tampilkan harga barang, hanya sebagai informasi -->
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="text" class="form-control" id="harga" name="harga" value="{{ $selectedBarang->harga }}" readonly>
            </div>

            <div class="form-group">
                <label for="jumlah_terima">Jumlah Terima</label>
                <input type="number" class="form-control" id="jumlah_terima" name="jumlah_terima" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Detail Penerimaan</button>
        </form>
    </div>

    <script>
        function updateHarga() {
            const idBarang = document.getElementById('idbarang').value;

            // Lakukan AJAX untuk mendapatkan harga barang berdasarkan idBarang
            fetch(`/barang/${idBarang}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Perbarui field harga satuan
                    if (data && data.harga) {
                        document.getElementById('harga').value = data.harga;
                    } else {
                        document.getElementById('harga').value = ''; // Atau pesan lain jika harga tidak ditemukan
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
