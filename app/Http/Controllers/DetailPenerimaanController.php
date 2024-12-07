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

        // Jika ada barang yang dipilih (melalui form submission)
        $selectedBarang = null;
        if ($request->has('idbarang') && !empty($request->input('idbarang'))) {
            // Ambil harga barang yang dipilih berdasarkan idbarang
            $selectedBarang = DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$request->input('idbarang')]);

            // Pastikan ada hasil dari query
            $selectedBarang = !empty($selectedBarang) ? $selectedBarang[0] : null;
        }

        // Jika tidak ada barang yang dipilih, gunakan barang pertama sebagai default
        if (!$selectedBarang) {
            $selectedBarang = $barangs[0] ?? null; // Ambil barang pertama jika ada
        }

        // Kirim data ke view
        return view('detail_penerimaan.create', compact('penerimaans', 'barangs', 'selectedBarang'));
    }

    public function store(Request $request)
    {
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


    public function edit($detail_penerimaan)
    {
        // Ambil detail penerimaan berdasarkan id
        $detail_penerimaan = DB::select('SELECT * FROM detail_penerimaan WHERE iddetail_penerimaan = ?', [$detail_penerimaan]);

        if (empty($detail_penerimaan)) {
            return redirect()->route('detail_penerimaan.index')->with('error', 'Detail Penerimaan tidak ditemukan.');
        }

        // Ambil data penerimaan dan barang untuk form edit
        $penerimaans = DB::select('SELECT * FROM penerimaan');
        $barangs = DB::select('SELECT * FROM barang');

        // Ambil harga_satuan barang yang dipilih dari session atau dari detail penerimaan
        $selectedIdBarang = $detail_penerimaan[0]->idbarang; // Use the idbarang from the detail penerimaan
        $selectedBarang = session('selectedBarang') ?: DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$selectedIdBarang])[0];

        return view('detail_penerimaan.edit', [
            'detail_penerimaan' => $detail_penerimaan[0],
            'penerimaans' => $penerimaans,
            'barangs' => $barangs,
            'selectedBarang' => $selectedBarang,
            'selectedIdBarang' => $selectedIdBarang,
        ]);
    }

    public function update(Request $request, $iddetail_penerimaan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idpenerimaan' => 'required|exists:penerimaan,idpenerimaan',
            'iddetail_pengadaan' => 'required|exists:detail_pengadaan,iddetail_pengadaan',
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            // Panggil fungsi update dengan parameter dari validasi
            $result = DB::select('SELECT fn_update_detail_penerimaan(?, ?, ?, ?) AS result', [
                $iddetail_penerimaan,
                $validatedData['idpenerimaan'],
                $validatedData['iddetail_pengadaan'],
                $validatedData['jumlah']
            ]);

            // Ambil hasil dari fungsi
            $rowsAffected = $result[0]->result;

            // Cek hasil update
            if ($rowsAffected > 0) {
                return redirect()->route('detail_penerimaan.index')
                    ->with('success', 'Detail Penerimaan berhasil diupdate');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal update detail penerimaan')
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
        // Hapus data berdasarkan id
        DB::delete('DELETE FROM detail_penerimaan WHERE iddetail_penerimaan = ?', [$id]);

        return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil dihapus.');
    }
}
