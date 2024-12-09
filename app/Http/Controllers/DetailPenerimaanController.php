<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPenerimaanController extends Controller
{
    public function index()
    {
        // Mengambil semua detail penerimaan menggunakan query SQL murni
        $detail_penerimaans = DB::select('SELECT * FROM view_detail_penerimaan');

        return view('detail_penerimaan.index', compact('detail_penerimaans'));
    }

    public function create(Request $request)
    {
        // Ambil data penerimaan dan barang
        $penerimaans = DB::select('SELECT * FROM penerimaan');
        $barangs = DB::select('SELECT idbarang, nama, harga FROM barang');

        // Kirim data ke view
        return view('detail_penerimaan.create', compact('penerimaans', 'barangs'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        // Validasi input
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|numeric',
            'idbarang' => 'required|numeric',
            'jumlah_terima' => 'required|numeric|min:1',
        ]);

        try {
            // Panggil stored procedure untuk membuat detail penerimaan
            $result = DB::select('CALL sp_create_detail_penerimaan(?, ?, ?)', [
                $request->input('idpenerimaan'),
                $request->input('idbarang'),
                $request->input('jumlah_terima')
            ]);

            // Ambil ID detail penerimaan yang baru saja dibuat
            $idDetailPenerimaan = $result[0]->iddetail_penerimaan;

            return redirect()->route('detail_penerimaan.index', ['idpenerimaan' => $request->input('idpenerimaan')])
                ->with('success', 'Detail Penerimaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('detail_penerimaan.create')
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Hapus data berdasarkan id
        DB::delete('DELETE FROM detail_penerimaan WHERE iddetail_penerimaan = ?', [$id]);

        return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil dihapus.');
    }
}
