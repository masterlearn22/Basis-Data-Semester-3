<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    // Menampilkan daftar penerimaan
    public function index()
    {
        // Query SQL untuk mengambil data penerimaan, beserta informasi vendor dan user
        $penerimaans = DB::select(' SELECT * FROM view_penerimaan');
        $detail_penerimaans = DB::select('SELECT * FROM view_detail_penerimaan');

        return view('penerimaan.index', compact('penerimaans','detail_penerimaans'));
    }

    // Menampilkan form untuk membuat penerimaan baru
    public function create()
    {
        // Query untuk mengambil data pengadaan dan barang
        $pengadaans = DB::select('SELECT * FROM pengadaan');
        $barangs = DB::select('SELECT * FROM barang');
        $users = DB::select('SELECT * FROM users');

        return view('penerimaan.create', compact('pengadaans', 'barangs', 'users'));
    }

    public function approvePenerimaan($idpenerimaan)
{
    //dd($idpenerimaan);

        // Panggil stored procedure
        DB::select('CALL sp_approve_penerimaan(?)', [$idpenerimaan]);

        $detailPengadaan = DB::select("
    SELECT idbarang, jumlah 
    FROM detail_pengadaan 
    WHERE idpengadaan = (
        SELECT idpengadaan 
        FROM penerimaan 
        WHERE idpenerimaan = ?
    )
", [$idpenerimaan]);
        //dd($idpenerimaan);
        foreach ($detailPengadaan as $detail) {
            DB::select('CALL sp_create_detail_penerimaan(?, ?, ?)', [
                $idpenerimaan, 
                $detail->idbarang, 
                $detail->jumlah
            ]);
        }
        return redirect()->back()->with('success', 'Penerimaan berhasil diapprove');
}
}
