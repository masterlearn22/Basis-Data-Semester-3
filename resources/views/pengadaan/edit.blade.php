@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Pengadaan</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengadaan.update', $pengadaan->idpengadaan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="idvendor">Vendor</label>
                <select class="form-control @error('idvendor') is-invalid @enderror" id="idvendor" name="idvendor">
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->idvendor }}" 
                            {{ old('idvendor', $pengadaan->idvendor) == $vendor->idvendor ? 'selected' : '' }}>
                            {{ $vendor->nama_vendor }}
                        </option>
                    @endforeach
                </select>
                @error('idvendor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="iduser">User</label>
                <select class="form-control @error('iduser') is-invalid @enderror" id="iduser" name="iduser">
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}" 
                            {{ old('iduser', $pengadaan->iduser) == $user->iduser ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
                @error('iduser')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="subtotal_awal">Subtotal Nilai</label>
                <input type="number" 
                       class="form-control @error('subtotal_awal') is-invalid @enderror" 
                       id="subtotal_awal" 
                       name="subtotal_awal" 
                       value="{{ old('subtotal_awal', $pengadaan->subtotal_nilai) }}" 
                       required>
                @error('subtotal_awal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="ppn">PPN (%)</label>
                <input type="number" 
                       class="form-control @error('ppn') is-invalid @enderror" 
                       id="ppn" 
                       name="ppn" 
                       value="{{ old('ppn', $pengadaan->ppn) }}" 
                       required>
                @error('ppn')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                    <option value="1" {{ old('status', $pengadaan->status) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status', $pengadaan->status) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="total_nilai">Total Nilai (Preview)</label>
                <input type="number" 
                       class="form-control" 
                       id="total_nilai" 
                       value="{{ $pengadaan->total_nilai }}" 
                       readonly>
            </div>

            <button type="submit" class="btn btn-primary">Update Pengadaan</button>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            function calculateTotal() {
                let subtotal = parseFloat($('#subtotal_awal').val()) || 0;
                let ppn = parseFloat($('#ppn').val()) || 0;
                let total = subtotal * (1 + (ppn / 100));
                $('#total_nilai').val(total.toFixed(2));
            }

            $('#subtotal_awal, #ppn').on('input', calculateTotal);
        });
    </script>
    @endpush
@endsection