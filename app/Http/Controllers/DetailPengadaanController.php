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
            'jumlah' => 'required|numeric',
        ]);

        // Mengambil harga satuan dari tabel barang berdasarkan idbarang
        $barang = DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$request->input('idbarang')]);
        $harga_satuan = $barang[0]->harga;

        // Menghitung subtotal berdasarkan jumlah * harga satuan
        $sub_total = $request->input('jumlah') * $harga_satuan;

        
        DB::statement('
        INSERT INTO detail_pengadaan (idpengadaan, idbarang, harga_satuan, jumlah, sub_total) 
        VALUES (?, ?, ?, ?, ?)', [
            $request->input('idpengadaan'),
            $request->input('idbarang'),
            $harga_satuan,  // Harga satuan dari tabel barang
            $request->input('jumlah'),
            $sub_total,  // Subtotal yang sudah dihitung
        ]);
        
        $subtotal_nilai = DB::select('
            SELECT SUM(sub_total) AS subtotal_nilai
            FROM detail_pengadaan
            WHERE idpengadaan = ?',
            [$request->input('idpengadaan')]
        );

        DB::statement('
        INSERT INTO pengadaan (subtotal_nilai) 
        VALUES (?)', [
        $subtotal_nilai[0]->subtotal_nilai ?? 0 // Akses nilai subtotal_nilai
    ]);
    

        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil ditambahkan.');
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

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'idpengadaan' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);

        // Mengambil harga satuan dari tabel barang berdasarkan idbarang
        $barang = DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$request->input('idbarang')]);
        $harga_satuan = $barang[0]->harga;

        // Menghitung subtotal berdasarkan jumlah * harga satuan
        $sub_total = $request->input('jumlah') * $harga_satuan;

        DB::update('
            UPDATE detail_pengadaan 
            SET idpengadaan = ?, idbarang = ?, harga_satuan = ?, jumlah = ?, sub_total = ?
            WHERE iddetail_pengadaan = ?', [
            $request->input('idpengadaan'),
            $request->input('idbarang'),
            $harga_satuan,  // Harga satuan dari tabel barang
            $request->input('jumlah'),
            $sub_total,  // Subtotal yang sudah dihitung
            $id,
        ]);

        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);
        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil dihapus.');
    }
}
