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
    
        // Jika mode adalah 'create', tampilkan halaman create barang dengan data satuan
        if ($request->query('mode') === 'create') {
            $satuans = DB::select('SELECT * FROM satuan');
            return view('barang.create', compact('satuans'));
        }
    
        // Jika mode bukan 'create', tampilkan daftar barang
        return view('barang.index', compact('barangs'));
    }
    
    

public function create(){
    $satuans = db::select('SELECT * FROM SATUAN');
    return view('barang.create',compact('satuans'));
}
    public function store(Request $request)
    {
        // Memanggil stored procedure
        DB::statement('CALL sp_create_barang(?, ?, ?, ?, ?)');
    
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
        return view('barang.edit', compact('barang','satuans'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'jenis'=>'required',
            'nama' => 'required',
            'status' => 'required',
            'harga' => 'required|numeric',
            'idsatuan'=> 'required'
        ]);

        DB::update('UPDATE barang SET jenis=?, nama = ?, status = ?, harga = ?, idsatuan=? WHERE idbarang = ?',[
            $request->input('jenis'),
            $request->input('nama'),
            $request->input('status'),
            $request->input('harga'),
            $request->input('idsatuan'),
            $id,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM barang WHERE idbarang = ?', [$id]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
