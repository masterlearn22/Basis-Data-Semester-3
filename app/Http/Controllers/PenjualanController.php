<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = DB::select('SELECT * FROM view_penjualan');
        $detail_penjualans = DB::select('SELECT * FROM view_detail_penjualan');
        $margin_penjualans = DB::select('SELECT * FROM margin_penjualan');
        $users = DB::SELECT('SELECT * FROM users');
        $barangs_query = "
        SELECT 
            b.idbarang, 
            b.nama, 
            COALESCE((
                SELECT SUM(masuk) - SUM(keluar)
                FROM kartu_stok 
                WHERE idbarang = b.idbarang
            ), 0) as stock
        FROM barang b
    ";
    $barangs = DB::select($barangs_query);
        return view('penjualan.index', compact('penjualans','detail_penjualans','margin_penjualans', 'users','barangs'));
    }

    public function create()
    {
        $margin_penjualans = DB::select('SELECT * FROM margin_penjualan');
        $users = DB::SELECT('SELECT * FROM users');
        $barangs_query = "
        SELECT 
            b.idbarang, 
            b.nama, 
            COALESCE((
                SELECT SUM(masuk) - SUM(keluar)
                FROM kartu_stok 
                WHERE idbarang = b.idbarang
            ), 0) as stock
        FROM barang b
    ";
    $barangs = DB::select($barangs_query);
        return view('penjualan.create', compact('margin_penjualans', 'users','barangs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idmargin_penjualan' => 'required|exists:margin_penjualan,idmargin_penjualan',
            'iduser' => 'required|exists:users,iduser',
            'idbarang' => 'required|exists:barang,idbarang',
            'jumlah' => 'required|integer|min:1'
        ]);
    

            // Panggil stored procedure untuk menyimpan penjualan
            $idpenjualan = DB::select('CALL sp_create_penjualan(?, ?, @p_idpenjualan)', [
                $request->input('idmargin_penjualan'),
                $request->input('iduser')
            ]);
    
            // Ambil ID penjualan yang baru saja dibuat
            $result = DB::select('SELECT @p_idpenjualan AS idpenjualan')[0];
            $idpenjualan = $result->idpenjualan;
    
            // Panggil stored procedure untuk menyimpan detail penjualan
            DB::select('CALL sp_create_detail_penjualan(?, ?, ?)', [
                $idpenjualan,
                $request->input('idbarang'),
                $request->input('jumlah')
            ]);
    
            return redirect()->route('penjualan.index')
                ->with('success', 'Penjualan berhasil ditambahkan.');
    
    }


    public function destroy($id)
    {
        DB::delete('DELETE FROM penjualan WHERE idpenjualan = ?', [$id]);
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
