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
        $penerimaans = DB::select('SELECT penerimaan.* FROM penerimaan');
        $users = DB::select('SELECT users.* FROM users');
        return view('retur.create', compact('penerimaans', 'users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|numeric',
            'iduser' => 'required|numeric',
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
            'idpenerimaan' => 'required|exists:penerimaan,idpenerimaan',
            'iduser' => 'required|exists:users,iduser',
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_retur(?, ?, ?, ?) AS result', [
                $idretur,
                $validatedData['idpenerimaan'],
                $validatedData['iduser'],
                $validatedData['jumlah']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('retur.index')
                    ->with('success', 'Retur berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update retur')
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Tangani error yang mungkin terjadi
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM retur WHERE idretur = ?', [$id]);
        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus.');
    }
}
