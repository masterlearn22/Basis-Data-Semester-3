<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = DB::select('SELECT * FROM view_penjualan');
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $margin_penjualans = DB::select('SELECT * FROM margin_penjualan');
        $users = DB::SELECT('SELECT * FROM users');
        return view('penjualan.create', compact('margin_penjualans', 'users'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'idmargin_penjualan' => 'required|numeric',
            'iduser' => 'required',
        ]);

        try {
            // Panggil stored procedure untuk menyimpan data penjualan
            DB::statement('CALL sp_create_penjualan(?, ?)', [
                $request->input('idmargin_penjualan'),
                $request->input('iduser'),
            ]);

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.create')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }



    public function edit($id)
    {
        // Ambil data penjualan dengan mengambil elemen pertama
        $penjualan = DB::select('SELECT * FROM penjualan WHERE idpenjualan = ?', [$id])[0];

        // Ambil data margin penjualan dan users
        $margin_penjualans = DB::select('SELECT * FROM margin_penjualan');
        $users = DB::select('SELECT * FROM users');

        if (!$penjualan) {
            return redirect()->route('penjualan.index')->with('error', 'Penjualan tidak ditemukan.');
        }

        // Kirim semua variabel yang diperlukan ke view
        return view('penjualan.edit', compact('penjualan', 'margin_penjualans', 'users'));
    }

    public function update(Request $request, $idpenjualan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idmargin_penjualan'=>'nullable',
            'iduser' => 'nullable|exists:users,iduser'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_penjualan(?, ?, ?) AS result', [
                $idpenjualan,
                $validatedData['idmargin_penjualan'],
                $validatedData['iduser']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('penjualan.index')
                    ->with('success', 'Penjualan berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update penjualan')
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Tangani error yang mungkin terjadi
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM penjualan WHERE idpenjualan = ?', [$id]);
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
