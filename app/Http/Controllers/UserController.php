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
        $user=$user[0];
        $roles=DB::select('SELECT * FROM role');
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'idrole' => 'required|numeric',
        ]);

        DB::update('UPDATE users SET username = ?, password = ?, idrole = ? WHERE iduser = ?', [
            $request->input('username'),
            bcrypt($request->input('password')),
            $request->input('idrole'),
            $id,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM users WHERE iduser = ?', [$id]);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
