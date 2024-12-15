<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPengadaanController extends Controller
{
    public function index()
    {
        $detail_pengadaans = DB::select(' SELECT * FROM view_detail_pengadaan');
        return view('detail_pengadaan.index', compact('detail_pengadaans'));
    }


    public function destroy($id)
    {
        DB::delete('DELETE FROM detail_pengadaan WHERE iddetail_pengadaan = ?', [$id]);
        return redirect()->route('detail_pengadaan.index')->with('success', 'Detail Pengadaan berhasil dihapus.');
    }
}
