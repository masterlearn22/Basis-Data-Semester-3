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
        $margin_penjualans=DB::select('SELECT * FROM margin_penjualan');
        $users = DB::SELECT('SELECT * FROM users');
        return view('penjualan.create',compact('margin_penjualans','users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subtotal_nilai' => 'required|numeric',
            'idmargin_penjualan' => 'required|numeric',
            'iduser' => 'required|numeric',
        ]);
    
        try {
            // Panggil stored procedure untuk menyimpan data penjualan
            DB::statement('CALL sp_create_penjualan(?, ?, ?)', [
                $request->input('subtotal_nilai'),
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

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'subtotal_awal' => 'required|numeric',
            'ppn' => 'required|numeric',
            'total_nilai' => 'required|numeric',
            'idvendor' => 'required|numeric',
        ]);

        DB::update('UPDATE penjualan SET subtotal_awal = ?, subtotal_akhir = ?, ppn = ?, idvendor = ?, updated_at = NOW() WHERE idpenjualan = ?', [
            $request->input('subtotal_awal'),
            $request->input('subtotal_akhir'),
            $request->input('ppn'),
            $request->input('idvendor'),
            $id,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM penjualan WHERE idpenjualan = ?', [$id]);
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
