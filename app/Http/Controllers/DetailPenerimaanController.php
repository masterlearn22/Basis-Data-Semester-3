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

    public function destroy($id)
    {
        // Hapus data berdasarkan id
        DB::delete('DELETE FROM detail_penerimaan WHERE iddetail_penerimaan = ?', [$id]);

        return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil dihapus.');
    }
}
