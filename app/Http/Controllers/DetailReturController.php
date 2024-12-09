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
        $returs = DB::SELECT('SELECT retur.* FROM retur');
        $barangs = DB::SELECT('SELECT barang.* FROM barang');
        $detail_penerimaans = DB::SELECT('SELECT detail_penerimaan.* FROM detail_penerimaan');
        return view('detail_retur.create', compact('returs', 'barangs', 'detail_penerimaans'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idretur' => 'required|numeric',
            'iddetail_penerimaan' => 'required|numeric',
            'alasan' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
        ]);

        try {
            // Panggil stored procedure untuk membuat detail retur
            $result = DB::select('CALL sp_create_detail_retur(?, ?, ?, ?)', [
                $request->input('idretur'),
                $request->input('iddetail_penerimaan'),
                $request->input('alasan'),
                $request->input('jumlah')
            ]);

            // Ambil ID detail retur yang baru saja dibuat
            $idDetailRetur = $result[0]->iddetail_retur;

            return redirect()->route('detail_retur.index', ['idretur' => $request->input('idretur')])
                ->with('success', 'Detail Retur berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('detail_retur.create')
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $detail_retur = DB::select('SELECT * FROM detail_retur WHERE iddetail_retur = ?', [$id]);
        $returs = DB::SELECT('SELECT retur.* FROM retur');
        $barangs = DB::SELECT('SELECT barang.* FROM barang');
        $detail_penerimaans = DB::SELECT('SELECT detail_penerimaan.* FROM detail_penerimaan');
        if (!$detail_retur) {
            return redirect()->route('detail_retur.index')->with('error', 'Detail Retur tidak ditemukan.');
        }
        $detail_retur = $detail_retur[0];
        return view('detail_retur.edit', compact('detail_retur', 'returs', 'barangs', 'detail_penerimaans'));
    }

    public function update(Request $request, $iddetail_retur)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idretur' => 'nullable|exists:retur,idretur',
            'iddetail_penerimaan' => 'nullable|exists:detail_penerimaan,iddetail_penerimaan',
            'alasan' => 'nullable|string',
            'jumlah' => 'nullable|numeric'
        ]);
        
       // dd($request->all());
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_detail_retur(?, ?, ?, ?, ?) AS result', [
                $iddetail_retur,
                $validatedData['idretur'],
                $validatedData['iddetail_penerimaan'],
                $validatedData['alasan'],
                $validatedData['jumlah']
            ]);

         return redirect()->route('detail_retur.index')->with('success', 'Detail Retur berhasil ditambahkan.');
        
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_retur WHERE iddetail_retur = ?', [$id]);
        return redirect()->route('detail_retur.index')->with('success', 'Detail Retur berhasil dihapus.');
    }
}
