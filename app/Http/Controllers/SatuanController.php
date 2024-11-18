<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = DB::select('SELECT * FROM satuan');
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_satuan' => 'required',
            'status' => 'required|numeric',
        ]);
    
        DB::statement('CALL sp_create_satuan(?, ?)', [
            $request->input('nama_satuan'),
            $request->input('status'),
        ]);
    
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
    }
    

    public function edit($id)
    {
        $satuan = DB::select('SELECT * FROM satuan WHERE idsatuan = ?', [$id]);

        if (!$satuan) {
            return redirect()->route('satuan.index')->with('error', 'Satuan tidak ditemukan.');
        }
        $satuan=$satuan[0];
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_satuan' => 'required',
            'status' => 'required|numeric',
        ]);

        DB::update('UPDATE satuan SET nama_satuan = ?, status = ? WHERE idsatuan = ?', [
            $request->input('nama_satuan'),
            $request->input('status'),
            $id,
        ]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM satuan WHERE idsatuan = ?', [$id]);
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
