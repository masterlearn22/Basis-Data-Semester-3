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
            'jumlah_terima' => 'required|numeric',
        ]);
    
        try {
            // Ambil harga barang dari database
            $barang = DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$request->input('idbarang')]);
    
            if (empty($barang)) {
                return redirect()->route('detail_penerimaan.create')->with('error', 'Barang tidak ditemukan.');
            }
    
            // Hitung sub_total
            $harga = $barang[0]->harga ?? null;
            
            $sub_total = $harga * $request->input('jumlah_terima');

            
            // Masukkan data ke detail_penerimaan
            try {
                // Debug data yang akan diinsert
                $data = [
                    $request->input('idpenerimaan'),
                    $request->input('idbarang'),
                    $harga,
                    $request->input('jumlah_terima'),
                    $sub_total,
                ];
                // dd($data); // Pastikan semua data sesuai
            
                // Masukkan data ke tabel detail_penerimaan
                DB::insert(
                    'INSERT INTO detail_penerimaan (idpenerimaan, idbarang, harga_satuan, jumlah_terima, sub_total)
                     VALUES (?, ?, ?, ?, ?)',
                    $data
                );
            
                // dd('Insert success'); // Jika mencapai sini, query berhasil
            } catch (\Exception $e) {
                // Tangkap detail error
                dd('Error:', $e->getMessage());
            }
            
            
    
            return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil ditambahkan.');
        } catch (\Exception $e) {
    
            return redirect()->route('detail_penerimaan.create')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
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
        $selectedBarang = session('selectedBarang') ?: DB::select('SELECT harga_satuan FROM barang WHERE idbarang = ?', [$selectedIdBarang])[0];

        return view('detail_penerimaan.edit', [
            'detail_penerimaan' => $detail_penerimaan[0],
            'penerimaans' => $penerimaans,
            'barangs' => $barangs,
            'selectedBarang' => $selectedBarang,
            'selectedIdBarang' => $selectedIdBarang,
        ]);
    }

    public function update(Request $request, $detail_penerimaan)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idbarang' => 'required|numeric',
            'jumlah_terima' => 'required|numeric',
        ]);

        // Ambil harga barang dari database menggunakan raw SQL
        $barang = DB::select('SELECT harga FROM barang WHERE idbarang = ?', [$request->input('idbarang')]);

        if (empty($barang)) {
            return redirect()->route('detail_penerimaan.edit',  $detail_penerimaan)->with('error', 'Barang tidak ditemukan.');
        }

        // Hitung sub_total berdasarkan harga_satuan dan jumlah_terima
        $sub_total = $barang[0]->harga * $request->input('jumlah_terima');

        // Dapatkan harga satuan dari barang
        $harga_satuan = $barang[0]->harga;

        try {
            // Update data detail_penerimaan di database menggunakan raw SQL
            DB::update(
                '
            UPDATE detail_penerimaan
            SET idbarang = ?, harga_satuan = ?, jumlah_terima = ?, sub_total = ?, updated_at = NOW()
            WHERE iddetail_penerimaan = ?',
                [
                    $request->input('idbarang'),  // Make sure this is taken from the correct input
                    $harga_satuan,
                    $request->input('jumlah_terima'),
                    $sub_total,
                    $detail_penerimaan,
                ]
            );

            return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('detail_penerimaan.edit', $detail_penerimaan)->with('error', 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        // Hapus data berdasarkan id
        DB::delete('DELETE FROM detail_penerimaan WHERE iddetail_penerimaan = ?', [$id]);

        return redirect()->route('detail_penerimaan.index')->with('success', 'Detail Penerimaan berhasil dihapus.');
    }
}
