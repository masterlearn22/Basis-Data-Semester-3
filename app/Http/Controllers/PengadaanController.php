<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = DB::select('SELECT * FROM view_pengadaan');
        $detail_pengadaans = DB::select('SELECT * FROM view_detail_pengadaan');
        
        // Tambahkan data untuk dropdown di modal
        $vendors = DB::select('SELECT idvendor, nama_vendor FROM vendor');
        $barangs = DB::select('SELECT idbarang, nama FROM barang');
        $users = DB::select('SELECT iduser, username FROM users');
    
        return view('pengadaan.index', compact(
            'pengadaans', 
            'detail_pengadaans', 
            'vendors', 
            'barangs', 
            'users'
        ));
    }



    public function create()
    {
        $vendors = DB::select('SELECT idvendor, nama_vendor FROM vendor');
        $barangs = DB::select('SELECT idbarang, nama FROM barang');
        $users = DB::select('SELECT iduser, username FROM users');
        return view('pengadaan.create', compact('vendors','barangs', 'users'));
    }

public function store(Request $request)
{
    DB::beginTransaction();
        // Buat pengadaan
        $pengadaan = DB::select('CALL sp_create_pengadaan(?, ?, ?)', [
            $request->idvendor,
            $request->ppn,
            $request->iduser
        ])[0];

        // Loop untuk setiap barang
        foreach ($request->barang as $key => $idbarang) {
            DB::select('CALL sp_create_detail_pengadaan(?, ?, ?)', [
                $pengadaan->idpengadaan,
                $idbarang,
                $request->jumlah[$key]
            ]);
        }   

        DB::commit();
        return redirect()->route('pengadaan.index')->with('success', 'Pengadaan berhasil dibuat');
   
}

    public function destroy($id)
    {
        // Ambil idpengadaan dari detail yang dihapus
        $detail_pengadaan = DB::select('SELECT idpengadaan FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);
        $idpengadaan = $detail_pengadaan[0]->idpengadaan ?? null;

        // Hapus detail pengadaan
        DB::delete('DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);

        // Perbarui subtotal_nilai di pengadaan

        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil dihapus.');
    }
}
