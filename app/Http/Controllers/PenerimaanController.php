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

        return view('penerimaan.index', compact('penerimaans'));
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

    // Menyimpan data penerimaan baru
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpengadaan' => 'required|numeric',
            'status' => 'required|string',
            'iduser' => 'required|numeric'
        ]);

        try {
            // Panggil stored procedure untuk membuat penerimaan
            $result = DB::select('CALL sp_create_penerimaan(?, ?, ?)', [
                $request->input('idpengadaan'),
                $request->input('status'),
                $request->input('iduser')
            ]);

            // Ambil ID penerimaan yang baru saja dibuat
            $idPenerimaan = $result[0]->idpenerimaan;

            return redirect()->route('penerimaan.index')
                ->with('success', 'Penerimaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('penerimaan.create')
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // Menampilkan form untuk mengedit penerimaan
    public function edit($id)
    {
        // Query SQL untuk mengambil data penerimaan berdasarkan id
        $penerimaan = DB::select('SELECT * FROM penerimaan WHERE idpenerimaan = ?', [$id]);

        if (!$penerimaan) {
            return redirect()->route('penerimaan.index')->with('error', 'Penerimaan tidak ditemukan.');
        }

        $pengadaans = DB::select('SELECT * FROM pengadaan');
        $users = DB::select('SELECT * FROM users');

        // Ambil penerimaan pertama dari hasil query
        $penerimaan = $penerimaan[0];

        return view('penerimaan.edit', compact('penerimaan', 'pengadaans', 'users'));
    }

    // Menyimpan perubahan data penerimaan
    public function update(Request $request, $idpenerimaan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpengadaan' => 'required|exists:pengadaan,idpengadaan',
            'tanggal' => 'required|date',
            'total_diterima' => 'required|numeric',
            'status' => 'required|in:0,1',
            'iduser' => 'required|exists:users,iduser'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_penerimaan(?, ?, ?, ?, ?, ?) AS result', [
                $idpenerimaan,
                $validatedData['idpengadaan'],
                $validatedData['tanggal'],
                $validatedData['total_diterima'],
                $validatedData['status'],
                $validatedData['iduser']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('penerimaan.index')
                    ->with('success', 'Penerimaan berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update penerimaan')
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Tangani error yang mungkin terjadi
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Menghapus penerimaan dan detail penerimaan terkait
    public function destroy($id)
    {
        // Query SQL untuk menghapus data detail penerimaan terkait dengan penerimaan
        DB::statement('DELETE FROM detail_penerimaan WHERE idpenerimaan = ?', [$id]);

        // Query SQL untuk menghapus data penerimaan
        DB::statement('DELETE FROM penerimaan WHERE idpenerimaan = ?', [$id]);

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dihapus.');
    }
}
