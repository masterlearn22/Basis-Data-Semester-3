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
        $users = DB::select('SELECT iduser, username FROM users');
        return view('pengadaan.create', compact('vendors', 'users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idvendor' => 'required|numeric',
            'ppn' => 'required|numeric',
            'iduser' => 'required|numeric',
            'status' => 'required|in:0,1',  // Menyesuaikan status (0 atau 1)
        ]);

        // Menghitung subtotal_nilai dari detail_pengadaan berdasarkan idpengadaan yang dipilih
        $subtotal_nilai = DB::select(
            '
            SELECT SUM(sub_total) AS subtotal_nilai
            FROM detail_pengadaan
            WHERE idpengadaan = ?',
            [$request->input('idpengadaan')]
        ); // Default ke 0 jika tidak ada hasil
        $subtotal_nilai = $subtotal_nilai[0]->subtotal_nilai ?? 0;
        // Menghitung total nilai dengan PPN
        $total_nilai = $subtotal_nilai +(( $request->input('ppn')/100) *$subtotal_nilai);

        DB::statement('
            INSERT INTO pengadaan 
            (idvendor, subtotal_nilai, total_nilai, ppn, iduser, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())', [
            $request->input('idvendor'),
            $subtotal_nilai,  // Menggunakan subtotal_nilai yang sudah dihitung
            $total_nilai,
            $request->input('ppn'),
            $request->input('iduser'),
            $request->input('status'),
        ]);


        return redirect()->route('pengadaan.index')->with('success', 'Pengadaan berhasil ditambahkan.');
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


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'idvendor' => 'required|numeric',
            'ppn' => 'required|numeric',
            'iduser' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        // Menghitung subtotal_nilai dari detail_pengadaan berdasarkan idpengadaan yang sama
        $subtotal_nilai = DB::select(
            '
            SELECT SUM(sub_total) AS subtotal_nilai
            FROM detail_pengadaan
            WHERE idpengadaan = ?',
            [$id]
        ); // Default ke 0 jika tidak ada hasil
        $subtotal_nilai = $subtotal_nilai[0]->subtotal_nilai ?? 0;
        // Menghitung total nilai dengan PPN
        $total_nilai = $subtotal_nilai +(( $request->input('ppn')/100) *$subtotal_nilai);

        DB::statement('
            UPDATE pengadaan 
            SET idvendor = ?, subtotal_nilai = ?, total_nilai = ?, ppn = ?, iduser = ?, status = ?, updated_at = NOW() 
            WHERE idpengadaan = ?', [
            $request->input('idvendor'),
            $subtotal_nilai,
            $total_nilai,
            $request->input('ppn'),
            $request->input('iduser'),
            $request->input('status'),
            $id,
        ]);
        return redirect()->route('pengadaan.index')->with('success', 'Pengadaan berhasil diperbarui.');
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
