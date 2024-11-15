<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailReturController extends Controller
{
    public function index()
    {
        $detail_returs = DB::select('SELECT * FROM view_detailretur');
        return view('detail_retur.index', compact('detail_returs'));
    }

    public function create()
    {
        $returs=DB::SELECT('SELECT retur.* FROM retur');
        $barangs=DB::SELECT('SELECT barang.* FROM barang');
        $detail_penerimaans=DB::SELECT('SELECT detail_penerimaan.* FROM detail_penerimaan');
        return view('detail_retur.create',compact('returs','barangs','detail_penerimaans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idretur' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'iddetail_penerimaan'=>'required',
            'alasan' => 'required|string',
            'jumlah' => 'required|numeric',
        ]);

        DB::insert('INSERT INTO detail_retur (idretur, idbarang,iddetail_penerimaan, alasan, jumlah, created_at, updated_at) VALUES (?, ?,?, ?, ?, NOW(), NOW())', [
            $request->input('idretur'),
            $request->input('idbarang'),
            $request->input('iddetail_penerimaan'),
            $request->input('alasan'),
            $request->input('jumlah'),
        ]);

        return redirect()->route('detail_retur.index')->with('success', 'Detail Retur berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $detail_retur = DB::select('SELECT * FROM detail_retur WHERE iddetail_retur = ?', [$id]);
        $returs=DB::SELECT('SELECT retur.* FROM retur');
        $barangs=DB::SELECT('SELECT barang.* FROM barang');
        $detail_penerimaans=DB::SELECT('SELECT detail_penerimaan.* FROM detail_penerimaan');
        if (!$detail_retur) {
            return redirect()->route('detail_retur.index')->with('error', 'Detail Retur tidak ditemukan.');
        }
        $detail_retur=$detail_retur[0];
        return view('detail_retur.edit', compact('detail_retur','returs','barangs','detail_penerimaans'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'idretur' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'iddetail_penerimaan'=>'required',
            'alasan' => 'required|string',
            'jumlah' => 'required|numeric',
        ]);

        DB::update('UPDATE detail_retur SET idretur = ?, idbarang = ?, iddetail_penerimaan=?, alasan = ?, jumlah = ?, updated_at = NOW() WHERE iddetail_retur = ?', [
            $request->input('idretur'),
            $request->input('idbarang'),
            $request->input('iddetail_penerimaan'),
            $request->input('alasan'),
            $request->input('jumlah'),
            $id,
        ]);

        return redirect()->route('detail_retur.index')->with('success', 'Detail Retur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_retur WHERE iddetail_retur = ?', [$id]);
        return redirect()->route('detail_retur.index')->with('success', 'Detail Retur berhasil dihapus.');
    }
}
