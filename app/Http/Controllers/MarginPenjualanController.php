<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarginPenjualanController extends Controller
{
    public function index()
    {
        $margin_penjualans = DB::select('SELECT * FROM margin_penjualan');
        return view('margin_penjualan.index', compact('margin_penjualans'));
    }

    public function create()
    {
        return view('margin_penjualan.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'persen' => 'required|numeric|min:0|max:100',
        ]);

        try {
            // Panggil stored procedure untuk membuat margin penjualan
            $result = DB::select('CALL sp_create_margin_penjualan(?)', [
                $request->input('persen')
            ]);

            // Ambil ID margin penjualan yang baru saja dibuat
            $idMarginPenjualan = $result[0]->idmargin_penjualan;

            return redirect()->route('margin_penjualan.index')
                ->with('success', 'Margin Penjualan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('margin_penjualan.create')
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $margin_penjualan = DB::select('SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);

        if (!$margin_penjualan) {
            return redirect()->route('margin_penjualan.index')->with('error', 'Margin Penjualan tidak ditemukan.');
        }
        $margin_penjualan = $margin_penjualan[0];
        return view('margin_penjualan.edit', compact('margin_penjualan'));
    }

    public function update(Request $request, $idmargin_penjualan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpenjualan' => 'required|exists:penjualan,idpenjualan',
            'margin' => 'required|numeric|min:0'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_margin_penjualan(?, ?, ?) AS result', [
                $idmargin_penjualan,
                $validatedData['idpenjualan'],
                $validatedData['margin']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('margin_penjualan.index')
                    ->with('success', 'Margin Penjualan berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update margin penjualan')
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
        DB::delete('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
        return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil dihapus.');
    }
}
