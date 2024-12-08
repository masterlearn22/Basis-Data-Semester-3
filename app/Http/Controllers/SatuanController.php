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
        $satuan = $satuan[0];
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $idsatuan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_satuan' => 'required|string|max:45',
            'status' => 'required|in:0,1'
        ]);

        // Panggil fungsi update dari database
        $result = DB::select('SELECT fn_update_satuan(?, ?, ?) AS result', [
            $idsatuan,
            $validatedData['nama_satuan'],
            $validatedData['status']
        ])[0]->result;

        // Cek hasil update
        if ($result === 1) {
            return redirect()->route('satuan.index')
                ->with('success', 'Satuan berhasil diupdate');
        } elseif ($result === -2) {
            return back()->with('error', 'Nama satuan sudah ada')
                ->withInput();
        } else {
            return back()->with('error', 'Gagal update satuan')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM satuan WHERE idsatuan = ?', [$id]);
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
