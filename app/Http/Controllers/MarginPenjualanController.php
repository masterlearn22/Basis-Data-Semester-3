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
            'status' =>'required'
        ]);
            // Panggil stored procedure untuk membuat margin penjualan
            $result = DB::select('CALL sp_create_margin_penjualan(?,?)', [
                $request->input('persen'),
                $request->input('status')
            ]);

            return redirect()->route('margin_penjualan.index')
                ->with('success', 'Margin Penjualan berhasil ditambahkan.');
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
            'persen' => 'nullable|numeric|min:0',
            'status'=>'nullable'
        ]);


            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_margin_penjualan(?,?,?) AS result', [
                $idmargin_penjualan,
                $validatedData['persen'],
                $validatedData['status']
            ]);

            return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil ditambahkan.'); 
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
        return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil dihapus.');
    }
}
