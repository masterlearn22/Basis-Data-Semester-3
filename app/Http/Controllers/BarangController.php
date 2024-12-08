<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data barang
        $barangs = DB::select('SELECT * FROM view_barang');

        // Jika mode bukan 'create', tampilkan daftar barang
        return view('barang.index', compact('barangs'));
    }



    public function create()
    {
        $satuans = db::select('SELECT * FROM SATUAN');
        return view('barang.create', compact('satuans'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'jenis' => 'required',
            'nama' => 'required|string|max:100',
            'status' => 'required|boolean',
            'harga' => 'required|integer',
            'idsatuan' => 'required|integer',
        ]);
    
        DB::statement('CALL sp_create_barang(?, ?, ?, ?, ?)', [
            $validatedData['jenis'],
            $validatedData['nama'],
            $validatedData['status'],
            $validatedData['harga'],
            $validatedData['idsatuan']
        ]);
    
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }
    


    public function edit($id)
    {
        $barang = DB::select('SELECT * FROM barang WHERE idbarang = ?', [$id]);

        if (!$barang) {
            return redirect()->route('barang.index')->with('error', 'Barang tidak ditemukan.');
        }
        $barang = $barang[0];
        $satuans = DB::select('SELECT * FROM satuan');
        return view('barang.edit', compact('barang', 'satuans'));
    }

    public function update(Request $request, $idbarang)
    {
        // Panggil function SQL untuk update
        $result = DB::select('SELECT fn_update_barang(?, ?, ?, ?, ?, ?) AS rows_affected', [
            $idbarang,
            $request->jenis,
            $request->nama,
            $request->status,
            $request->harga,
            $request->idsatuan
        ]);

        // Cek hasil
        $rowsAffected = $result[0]->rows_affected;
        
        if ($rowsAffected > 0) {
            return redirect()->back()->with('success', 'Barang berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Gagal update barang');
        }
    }

    public function destroy($idbarang)
    {
        // Panggil function SQL untuk delete
        $result = DB::select('SELECT fn_delete_barang(?) AS rows_affected', [$idbarang]);
        
        $rowsAffected = $result[0]->rows_affected;
        
        if ($rowsAffected > 0) {
            return redirect()->back()->with('success', 'Barang berhasil dihapus');
        } elseif ($rowsAffected == -1) {
            return redirect()->back()->with('error', 'Barang tidak bisa dihapus karena sudah digunakan dalam transaksi');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus barang');
        }
    }
}
