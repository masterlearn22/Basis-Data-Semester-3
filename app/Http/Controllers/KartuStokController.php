<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class KartuStokController extends Controller
{
    public function index()
    {
        $kartu_stoks = DB::select('SELECT * FROM view_kartustok
        ');
        return view('kartu_stok.index', compact('kartu_stoks'));
    }

}
