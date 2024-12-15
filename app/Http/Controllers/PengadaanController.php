<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = DB::select(' SELECT * FROM view_pengadaan');
        return view('pengadaan.index', compact('pengadaans'));
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
    public function edit($id)
    {
        $pengadaan = DB::select('SELECT * FROM pengadaan WHERE idpengadaan = ?', [$id]);

        if (!$pengadaan) {
            return redirect()->route('pengadaan.index')->with('error', 'Pengadaan tidak ditemukan.');
        }

        $pengadaan = $pengadaan[0]; // Ambil objek pengadaan
        $vendors = DB::select('SELECT idvendor, nama_vendor FROM vendor');
        $users = DB::select('SELECT iduser, username FROM users');

        return view('pengadaan.edit', compact('pengadaan', 'vendors', 'users'));
    }


    public function update(Request $request, $idpengadaan)
{
    $validatedData = $request->validate([
        'idvendor' => 'nullable|exists:vendor,idvendor',
        'status' => 'nullable|in:0,1',
        'iduser' => 'nullable|exists:users,iduser',
        'subtotal_awal' => 'nullable|numeric|min:0',
        'ppn' => 'nullable|numeric|min:0|max:100'
    ]);

    // Hitung total nilai
    $subtotal = $validatedData['subtotal_awal'] ?? null;
    $ppn = $validatedData['ppn'] ?? null;
    $total_nilai = $subtotal ? $subtotal * (1 + ($ppn / 100)) : null;

    $result = DB::select('SELECT fn_update_pengadaan(?, ?, ?, ?, ?, ?, ?) AS result', [
        $idpengadaan,
        $validatedData['idvendor'] ?? null,
        $validatedData['status'] ?? null,
        $validatedData['iduser'] ?? null,
        $subtotal,
        $ppn,
        $total_nilai
    ])[0]->result;

    if ($result > 0) {
        return redirect()->route('pengadaan.index')
            ->with('success', 'Pengadaan berhasil diupdate');
    } else {
        return back()->with('error', 'Gagal update pengadaan')
            ->withInput();
    }
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
