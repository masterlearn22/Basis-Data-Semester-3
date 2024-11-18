<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = DB::select('SELECT * FROM role');
        return view('role.index', compact('roles'));
    }

    public function create()
    {   
        
        return view('role.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_role' => 'required',
        ]);
    
        DB::statement('CALL sp_create_role(?)', [
            $request->input('nama_role'),
        ]);
    
        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan.');
    }
    

    public function edit($id)
    {
        $role = DB::select('SELECT * FROM role WHERE idrole = ?', [$id]);
    
        // Pastikan ada hasil yang didapat
        if (empty($role)) {
            return redirect()->route('role.index')->with('error', 'Role tidak ditemukan.');
        }
    
        // Ambil elemen pertama dari hasil array
        $role = $role[0];
    
        return view('role.edit', compact('role'));
    }
    


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_role' => 'required',
        ]);

        DB::update('UPDATE role SET nama_role = ? WHERE idrole = ?', [
            $request->input('nama_role'),
            $id,
        ]);

        return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM role WHERE idrole = ?', [$id]);
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus.');
    }
}
