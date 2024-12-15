<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\DetailReturController;
use App\Http\Controllers\DetailPengadaanController;
use App\Http\Controllers\DetailPenjualanController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\DetailPenerimaanController;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/d', function () {
    return view('layouts.dashboard');
});


Route::resource('users', UserController::class);
// Vendor
Route::resource('vendor', VendorController::class);

// Role
Route::resource('role', RoleController::class);

// Satuan
Route::resource('satuan', SatuanController::class);

// Stok Barang
Route::resource('stok_barang', KartuStokController::class);

// Pengadaan
Route::resource('pengadaan', PengadaanController::class);

// Penjualan
Route::resource('penjualan', PenjualanController::class);

// Penerimaan
Route::resource('penerimaan', PenerimaanController::class);

// Barang
Route::resource('barang', BarangController::class);


Route::resource('kartu_stok', KartuStokController::class);
Route::resource('detail_pengadaan', DetailPengadaanController::class);
Route::resource('detail_penerimaan', DetailPenerimaanController::class);
Route::resource('retur', ReturController::class);
Route::resource('detail_retur', DetailReturController::class);
Route::resource('detail_penjualan', DetailPenjualanController::class);
Route::resource('margin_penjualan', MarginPenjualanController::class);
Route::controller(PenerimaanController::class)->group(function () {
    Route::get('/penerimaan', 'index')->name('penerimaan.index');
    Route::patch('/penerimaan/{idpenerimaan}/approve', 'approvePenerimaan')
        ->name('penerimaan.approve');
});

