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
            'status' => 'required|in:0,1',
        ]);

        // Panggil stored procedure untuk membuat pengadaan
        $result = DB::select('CALL sp_create_pengadaan(?, ?, ?, ?)', [
            $request->input('idvendor'),
            $request->input('ppn'),
            $request->input('iduser'),
            $request->input('status')
        ]);

        // Ambil ID pengadaan yang baru saja dibuat
        $idpengadaan = $result[0]->idpengadaan;

        return redirect()->route('detail_pengadaan.create', ['idpengadaan' => $idpengadaan])
            ->with('success', 'Pengadaan berhasil dibuat. Silakan tambahkan detail pengadaan.');
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
