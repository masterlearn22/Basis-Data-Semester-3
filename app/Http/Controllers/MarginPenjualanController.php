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
        $validatedData = $request->validate([
            'persen' => 'required',
        ]);

        DB::insert('INSERT INTO margin_penjualan (persen, created_at, updated_at) VALUES (  ?, NOW(), NOW())', [
            $request->input('persen'),
        ]);

        return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $margin_penjualan = DB::select('SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);

        if (!$margin_penjualan) {
            return redirect()->route('margin_penjualan.index')->with('error', 'Margin Penjualan tidak ditemukan.');
        }
        $margin_penjualan= $margin_penjualan[0];
        return view('margin_penjualan.edit', compact('margin_penjualan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'persen' => 'required',
        ]);

        DB::update('UPDATE margin_penjualan SET  persen = ?, updated_at = NOW() WHERE idmargin_penjualan = ?', [
            $request->input('persen'),
            $id,
        ]);

        return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?', [$id]);
        return redirect()->route('margin_penjualan.index')->with('success', 'Margin Penjualan berhasil dihapus.');
    }
}
