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

    public function update(Request $request, $id)
    {
        // Validasi data yang diinput
        $validatedData = $request->validate([
            'nama_vendor' => 'required|max:100',
            'badan_hukum' => 'required|size:1',
            'status' => 'required|size:1'
        ]);

        // Update data di database
        DB::update('UPDATE vendor SET nama_vendor = ?, badan_hukum = ?, status = ? WHERE idvendor = ?', [
            $request->input('nama_vendor'),
            $request->input('badan_hukum'),
            $request->input('status'),
            $id,
        ]);

        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Menghapus data vendor
        DB::delete('DELETE FROM vendor WHERE idvendor = ?', [$id]);
        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
