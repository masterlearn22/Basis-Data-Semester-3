<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPenjualanController extends Controller
{
    public function index()
    {
        $detail_penjualans = DB::select('SELECT * FROM view_detail_pengadaan');
        return view('detail_penjualan.index', compact('detail_penjualans'));
    }

    public function create()
    {
        return view('detail_penjualan.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idpenjualan' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'Jumlah' => 'required|numeric',
            'sub_total' => 'required|numeric',
        ]);

        DB::insert('INSERT INTO detail_penjualan (idpenjualan, idbarang, harga_satuan, Jumlah, sub_total, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())', [
            $request->input('idpenjualan'),
            $request->input('idbarang'),
            $request->input('harga_satuan'),
            $request->input('Jumlah'),
            $request->input('sub_total'),
        ]);

        return redirect()->route('detail_penjualan.index')->with('success', 'Detail Penjualan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $detail_penjualan = DB::select('SELECT * FROM detail_penjualan WHERE iddetail_penjualan = ?', [$id]);

        if (!$detail_penjualan) {
            return redirect()->route('detail_penjualan.index')->with('error', 'Detail Penjualan tidak ditemukan.');
        }

        return view('detail_penjualan.edit', compact('detail_penjualan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'idpenjualan' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'Jumlah' => 'required|numeric',
            'sub_total' => 'required|numeric',
        ]);

        DB::update('UPDATE detail_penjualan SET idpenjualan = ?, idbarang = ?, harga_satuan = ?, Jumlah = ?, sub_total = ?, updated_at = NOW() WHERE iddetail_penjualan = ?', [
            $request->input('idpenjualan'),
            $request->input('idbarang'),
            $request->input('harga_satuan'),
            $request->input('Jumlah'),
            $request->input('sub_total'),
            $id,
        ]);

        return redirect()->route('detail_penjualan.index')->with('success', 'Detail Penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_penjualan WHERE iddetail_penjualan = ?', [$id]);
        return redirect()->route('detail_penjualan.index')->with('success', 'Detail Penjualan berhasil dihapus.');
    }
}
