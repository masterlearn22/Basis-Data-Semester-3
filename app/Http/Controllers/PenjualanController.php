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
            'subtotal_awal' => 'required|numeric',
            'subtotal_akhir' => 'required|numeric',
            'ppn' => 'required|numeric',
            'idmargin_penjualan'=>'required',
            'iduser' => 'required|numeric',
        ]);

        DB::insert('INSERT INTO penjualan (subtotal_awal, subtotal_akhir, ppn,idmargin_penjualan, iduser, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())', [
            $request->input('subtotal_awal'),
            $request->input('subtotal_akhir'),
            $request->input('ppn'),
            $request->input('idmargin_penjualan'),
            $request->input('iduser'),
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $penjualan = DB::select('SELECT * FROM penjualan WHERE idpenjualan = ?', [$id]);

        if (!$penjualan) {
            return redirect()->route('penjualan.index')->with('error', 'Penjualan tidak ditemukan.');
        }

        return view('penjualan.edit', compact('penjualan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'subtotal_awal' => 'required|numeric',
            'subtotal_akhir' => 'required|numeric',
            'ppn' => 'required|numeric',
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
