<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::select(' SELECT * FROM view_user
    ');

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = DB::select('SELECT * FROM role');
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'nullable',
            'idrole' => 'required|numeric',
        ]);

        DB::statement('CALL sp_create_user(?, ?, ?)', [
            $request->input('username'),
            bcrypt($request->input('password')),
            $request->input('idrole'),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $user = DB::select('SELECT * FROM users WHERE iduser = ?', [$id]);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }
        $user = $user[0];
        $roles = DB::select('SELECT * FROM role');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'username' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'idrole' => 'nullable|exists:role,idrole'
        ]);
    
        
            $result = DB::select('SELECT fn_update_user(?, ?, ?, ?) AS status', [
                $id,
                $validatedData['username'],
                $validatedData['password'] ?? null, // Gunakan null jika password kosong
                $validatedData['idrole']
            ])[0]->status;
    
            switch ($result) {
                case 1:
                    return redirect()->route('users.index')
                        ->with('success', 'User berhasil diperbarui');
                case -2:
                    return back()->with('error', 'Username sudah digunakan')
                        ->withInput();
                default:
                    return back()->with('error', 'Gagal memperbarui user')
                        ->withInput();
            }
       
    }
    
    public function destroy($id)
    {
        DB::delete('DELETE FROM users WHERE iduser = ?', [$id]);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
