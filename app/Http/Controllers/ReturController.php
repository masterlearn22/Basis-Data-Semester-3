<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    public function index()
    {
        $returs = DB::select('SELECT * FROM view_retur');
        $detail_returs = DB::select('SELECT * FROM view_detailretur');
        return view('retur.index', compact('returs','detail_returs'));
    }

    public function create()
    {
        $barangs = DB::SELECT('SELECT barang.* FROM barang');
        $detail_penerimaans = DB::SELECT('SELECT detail_penerimaan.* FROM detail_penerimaan');
        $penerimaans = DB::select('SELECT penerimaan.* FROM penerimaan');
        $users = DB::select('SELECT users.* FROM users');
        return view('retur.create', compact('penerimaans', 'users','barangs','detail_penerimaans'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|numeric|exists:penerimaan,idpenerimaan',
            'iduser' => 'required|numeric|exists:users,iduser',
            'jumlah' => 'required|numeric|min:1',
            'alasan' => 'required|string|max:255',
        ]);
    
        // Panggil prosedur untuk membuat retur
        $result = DB::select('CALL sp_create_retur(?, ?)', [
            $validatedData['idpenerimaan'],
            $validatedData['iduser']
        ]);
    
        // Ambil ID retur dari hasil prosedur
        $idretur = $result[0]->idretur;
    
        // Panggil stored procedure untuk membuat detail retur
        $resultDetail = DB::select('CALL sp_create_detail_retur(?, ?, ?)', [
            $idretur,
            $validatedData['jumlah'],
            $validatedData['alasan']
        ]);
    
        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $retur = DB::select('SELECT * FROM retur WHERE idretur = ?', [$id]);
        $penerimaans = DB::select('SELECT penerimaan.* FROM penerimaan');
        $users = DB::select('SELECT users.* FROM users');

        if (!$retur) {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan.');
        }
        $retur = $retur[0];
        return view('retur.edit', compact('retur', 'penerimaans', 'users'));
    }

    public function update(Request $request, $idretur)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpenerimaan' => 'nullable|exists:penerimaan,idpenerimaan',
            'iduser' => 'nullable|exists:users,iduser',
        ]);
            // Panggil fungsi update dengan parameter dari validasi
            $result
             = DB::select('SELECT fn_update_retur(?, ?, ?) AS result', [
                $idretur,
                $validatedData['idpenerimaan'],
                $validatedData['iduser'],
            ]);
        
        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan.');

      
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM retur WHERE idretur = ?', [$id]);
        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus.');
    }
}
