<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPenjualanController extends Controller
{
    public function index()
    {
        $detail_penjualans = DB::select('SELECT * FROM view_detail_penjualan');
        return view('detail_penjualan.index', compact('detail_penjualans'));
    }

    public function create()
    {
        // Ambil data pendukung
        $penjualans = DB::table('penjualan')->get();
        $barangs = DB::table('barang')->get();

        return view('detail_penjualan.create', compact('penjualans', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idpenjualan' => 'required|exists:penjualan,idpenjualan',
            'idbarang' => 'required|exists:barang,idbarang',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            DB::select('CALL sp_create_detail_penjualan(?, ?, ?, ?, ?)', [
                $request->idpenjualan,
                $request->idbarang,
                $request->harga,
                $request->jumlah,
                0 // sub_total diisi 0 untuk dihitung otomatis
            ]);

            return redirect()->route('detail_penjualan.index')
                ->with('success', 'Detail Penjualan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function edit($iddetail_penjualan)
    {
        // Ambil detail penjualan menggunakan query SQL
        $detail = DB::select('SELECT * FROM view_detail_penjualan WHERE iddetail_penjualan = ?', [$iddetail_penjualan])[0];

        // Ambil daftar penjualan menggunakan query SQL
        $penjualans = DB::select('SELECT * FROM penjualan');

        // Ambil daftar barang menggunakan query SQL
        $barangs = DB::select('SELECT * FROM barang');

        return view('detail_penjualan.edit', compact('detail', 'penjualans', 'barangs'));
    }

    public function update(Request $request, $iddetail_penjualan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpenjualan' => 'required|exists:penjualan,idpenjualan',
            'idbarang' => 'required|exists:barang,idbarang',
            'jumlah' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:0'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_detail_penjualan(?, ?, ?, ?, ?) AS result', [
                $iddetail_penjualan,
                $validatedData['idpenjualan'],
                $validatedData['idbarang'],
                $validatedData['jumlah'],
                $validatedData['harga']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('detail_penjualan.index')
                    ->with('success', 'Detail Penjualan berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update detail penjualan')
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Tangani error yang mungkin terjadi
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($iddetail_penjualan)
    {
        try {
            // Ambil idpenjualan sebelum hapus
            $detail = DB::table('detail_penjualan')
                ->where('iddetail_penjualan', $iddetail_penjualan)
                ->first();

            // Hapus detail penjualan
            DB::table('detail_penjualan')
                ->where('iddetail_penjualan', $iddetail_penjualan)
                ->delete();

            // Update total penjualan
            $this->updatePenjualanTotal($detail->idpenjualan);

            return redirect()->route('detail_penjualan.index')
                ->with('success', 'Detail Penjualan berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    // Fungsi untuk update total penjualan
    private function updatePenjualanTotal($idpenjualan)
    {
        $margin = DB::table('penjualan')
            ->join('margin_penjualan', 'penjualan.idmargin_penjualan', '=', 'margin_penjualan.idmargin_penjualan')
            ->where('penjualan.idpenjualan', $idpenjualan)
            ->value('margin_penjualan.persen');

        $subtotal = DB::table('detail_penjualan')
            ->where('idpenjualan', $idpenjualan)
            ->sum('subtotal');

        $ppn = floor($subtotal * ($margin / 100));
        $total = $subtotal + $ppn;

        DB::table('penjualan')
            ->where('idpenjualan', $idpenjualan)
            ->update([
                'subtotal_nilai' => $subtotal,
                'ppn' => $ppn,
                'total_nilai' => $total
            ]);
    }
}
