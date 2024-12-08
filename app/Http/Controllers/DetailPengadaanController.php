<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPengadaanController extends Controller
{
    public function index()
    {
        $detail_pengadaans = DB::select(' SELECT * FROM view_detail_pengadaan');
        return view('detail_pengadaan.index', compact('detail_pengadaans'));
    }

    public function create()
    {
        // Mengambil data pengadaan dan barang secara manual
        $pengadaan = DB::select('SELECT idpengadaan FROM pengadaan');
        $barang = DB::select('SELECT * FROM barang');

        return view('detail_pengadaan.create', compact('pengadaan', 'barang'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idpengadaan' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'jumlah' => 'required|numeric|min:1',
        ]);

        // Panggil stored procedure untuk membuat detail pengadaan
        $result = DB::select('CALL sp_create_detail_pengadaan(?, ?, ?)', [
            $request->input('idpengadaan'),
            $request->input('idbarang'),
            $request->input('jumlah')
        ]);

        // Ambil ID detail pengadaan yang baru saja dibuat
        $idDetailPengadaan = $result[0]->iddetail_pengadaan;

        return redirect()->route('detail_pengadaan.index', ['idpengadaan' => $request->input('idpengadaan')])
            ->with('success', 'Detail Pengadaan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Mengambil detail pengadaan berdasarkan ID
        $detail_pengadaan = DB::select('SELECT * FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);

        if (!$detail_pengadaan) {
            return redirect()->route('detail_pengadaan.index')->with('error', 'Detail Pengadaan tidak ditemukan.');
        }

        $detail_pengadaan = $detail_pengadaan[0];

        // Mengambil data pengadaan dan barang secara manual
        $pengadaans = DB::select('SELECT idpengadaan FROM pengadaan');
        $barangs = DB::select('SELECT idbarang, nama, harga FROM barang');

        return view('detail_pengadaan.edit', compact('detail_pengadaan', 'pengadaans', 'barangs'));
    }

    public function updateDetail(Request $request, $iddetail_pengadaan)
    {
        $validatedData = $request->validate([
            'idpengadaan' => 'nullable|exists:pengadaan,idpengadaan',
            'idbarang' => 'nullable|exists:barang,idbarang',
            'harga_satuan' => 'nullable|numeric',
            'jumlah' => 'nullable|numeric'
        ]);
    
        // Hitung sub total
        $harga_satuan = $validatedData['harga_satuan'] ?? null;
        $jumlah = $validatedData['jumlah'] ?? null;
        $sub_total = $harga_satuan && $jumlah ? $harga_satuan * $jumlah : null;
    
        $result = DB::select('SELECT fn_update_detail_pengadaan(?, ?, ?, ?, ?, ?) AS result', [
            $iddetail_pengadaan,
            $validatedData['idpengadaan'] ?? null,
            $validatedData['idbarang'] ?? null,
            $harga_satuan,
            $jumlah,
            $sub_total
        ])[0]->result;
    
        if ($result > 0) {
            return redirect()->route('detail_pengadaan.index')
                ->with('success', 'Detail Pengadaan berhasil diupdate');
        } else {
            return back()->with('error', 'Gagal update detail pengadaan')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);
        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil dihapus.');
    }
}
