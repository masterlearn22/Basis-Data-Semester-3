<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        // Mengambil semua data vendor
        $vendors = DB::select('SELECT * FROM vendor');
        return view('vendor.index', compact('vendors'));
    }

    public function create()
    {
        // Menampilkan form untuk membuat vendor baru
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_vendor' => 'required|max:100',
            'badan_hukum' => 'required|size:1',
            'status' => 'required|size:1',
        ]);

        DB::statement('CALL sp_create_vendor(?, ?, ?)', [
            $request->input('nama_vendor'),
            $request->input('badan_hukum'),
            $request->input('status'),
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
    }


    public function show($id)
    {
        // Menampilkan detail vendor tertentu
        $vendor = DB::select('SELECT * FROM vendor WHERE idvendor = ?', [$id]);

        if (empty($vendor)) {
            return redirect()->route('vendor.index')->with('error', 'Vendor tidak ditemukan.');
        }

        return view('vendor.show', ['vendor' => $vendor[0]]);
    }

    public function edit($id)
    {
        // Mengambil data vendor untuk diedit
        $vendor = DB::select('SELECT * FROM vendor WHERE idvendor = ?', [$id]);

        if (empty($vendor)) {
            return redirect()->route('vendors.index')->with('error', 'Vendor tidak ditemukan.');
        }

        return view('vendor.edit', ['vendor' => $vendor[0]]);
    }

    public function update(Request $request, $idvendor)
    {
        // Validasi input
       // dd($request->all());
        $validatedData = $request->validate([
            'nama_vendor' => 'nullable|string|max:100',
            'badan_hukum' => 'nullable',
            'status' => 'nullable|in:0,1'
        ]);

        // Panggil fungsi update dari database
        $result = DB::select('SELECT fn_update_vendor(?, ?, ?, ?) AS result', [
            $idvendor,
            $validatedData['nama_vendor'],
            $validatedData['badan_hukum'],
            $validatedData['status']
        ])[0]->result;

        // Cek hasil update
        if ($result === 1) {
            return redirect()->route('vendor.index')
                ->with('success', 'Vendor berhasil diupdate');
        } elseif ($result === -2) {
            return back()->with('error', 'Nama vendor sudah ada')
                ->withInput();
        } else {
            return back()->with('error', 'Gagal update vendor')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        // Menghapus data vendor
        DB::delete('DELETE FROM vendor WHERE idvendor = ?', [$id]);
        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
