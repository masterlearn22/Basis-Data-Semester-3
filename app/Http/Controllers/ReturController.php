<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    public function index()
    {
        $returs = DB::select('SELECT * FROM view_retur');
        return view('retur.index', compact('returs'));
    }

    public function create()
    {
        $penerimaans=DB::select('SELECT penerimaan.* FROM penerimaan');
        $users=DB::select('SELECT users.* FROM users');
        return view('retur.create',compact('penerimaans','users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|numeric',
            'iduser'=> 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);
    
        DB::statement('CALL sp_create_retur(?, ?, ?)', [
            $request->input('idpenerimaan'),
            $request->input('iduser'),
            $request->input('jumlah')
        ]);
    
        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan.');
    }
    

    public function edit($id)
    {
        $retur = DB::select('SELECT * FROM retur WHERE idretur = ?', [$id]);
        $penerimaans=DB::select('SELECT penerimaan.* FROM penerimaan');
        $users=DB::select('SELECT users.* FROM users');

        if (!$retur) {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan.');
        }
        $retur = $retur[0];
        return view('retur.edit', compact('retur','penerimaans','users'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|numeric',
            'iduser'=> 'required',
            'jumlah' => 'required|numeric',
        ]);

        DB::update('UPDATE retur SET idpenerimaan = ?,iduser=?, jumlah = ?, updated_at = NOW() WHERE idretur = ?', [
            $request->input('idpenerimaan'),
            $request->input('iduser'),
            $request->input('jumlah'),
            $id,
        ]);

        return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM retur WHERE idretur = ?', [$id]);
        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus.');
    }
}
