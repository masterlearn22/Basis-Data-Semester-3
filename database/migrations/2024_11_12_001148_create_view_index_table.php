<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        CREATE OR REPLACE VIEW view_barang AS
        SELECT barang.*, satuan.nama_satuan 
        FROM barang 
        JOIN satuan ON satuan.idsatuan = barang.idsatuan;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_detail_penerimaan AS
        SELECT detail_penerimaan.*, penerimaan.status, barang.nama AS nama_barang
        FROM detail_penerimaan
        JOIN penerimaan ON penerimaan.idpenerimaan = detail_penerimaan.idpenerimaan
        LEFT JOIN barang ON barang.idbarang = detail_penerimaan.idbarang;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_detail_pengadaan AS
        SELECT detail_pengadaan.*, barang.nama, barang.harga
        FROM detail_pengadaan
        JOIN pengadaan ON pengadaan.idpengadaan = detail_pengadaan.idpengadaan 
        JOIN barang ON barang.idbarang = detail_pengadaan.idbarang;
        ');


        DB::statement('
        CREATE OR REPLACE VIEW view_detail_penjualan AS
        SELECT detail_penjualan.iddetail_penjualan, penjualan.idpenjualan,
        barang.nama, detail_penjualan.jumlah, barang.harga
        FROM detail_penjualan 
        JOIN penjualan ON detail_penjualan.idpenjualan = penjualan.idpenjualan
        LEFT JOIN barang ON detail_penjualan.idbarang = barang.idbarang;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_detailretur AS
        SELECT detail_retur.iddetail_retur, retur.idretur, detail_penerimaan.iddetail_penerimaan, detail_retur.alasan, detail_retur.jumlah
        FROM detail_retur
        JOIN retur ON detail_retur.idretur = retur.idretur
        JOIN detail_penerimaan ON detail_retur.iddetail_penerimaan = detail_penerimaan.iddetail_penerimaan;
        ');
    

        DB::statement('
        CREATE OR REPLACE VIEW view_kartustok AS
        SELECT kartu_stok.*, barang.nama 
        FROM kartu_stok
        JOIN barang ON barang.idbarang = kartu_stok.idbarang;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_penerimaan AS
        SELECT p.*, v.nama_vendor, u.username 
        FROM penerimaan p
        JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
        JOIN vendor v ON pg.idvendor = v.idvendor
        JOIN users u ON pg.iduser = u.iduser;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_pengadaan AS
        SELECT pengadaan.*, vendor.nama_vendor, users.username
        FROM pengadaan
        JOIN vendor ON vendor.idvendor = pengadaan.idvendor
        JOIN users ON users.iduser = pengadaan.iduser;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_penjualan AS
        SELECT penjualan.idpenjualan, penjualan.subtotal_nilai, penjualan.total_nilai,
        penjualan.ppn, margin_penjualan.persen, users.username                             
        FROM penjualan
        JOIN margin_penjualan ON penjualan.idmargin_penjualan = margin_penjualan.idmargin_penjualan
        LEFT JOIN users ON penjualan.iduser = users.iduser;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_retur AS
        SELECT penerimaan.idpenerimaan, users.username, retur.idretur
        FROM retur
        JOIN penerimaan ON retur.idpenerimaan = penerimaan.idpenerimaan
        LEFT JOIN users ON retur.iduser = users.iduser;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW view_user AS
        SELECT users.*, role.nama_role
        FROM users
        LEFT JOIN role ON users.idrole = role.idrole;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_barang');
        DB::statement('DROP VIEW IF EXISTS view_detail_penerimaan');
        DB::statement('DROP VIEW IF EXISTS view_detail_pengadaan');
        DB::statement('DROP VIEW IF EXISTS view_detail_penjualan');
        DB::statement('DROP VIEW IF EXISTS view_detailretur');
        DB::statement('DROP VIEW IF EXISTS view_kartustok');
        DB::statement('DROP VIEW IF EXISTS view_penerimaan');
        DB::statement('DROP VIEW IF EXISTS view_pengadaan');
        DB::statement('DROP VIEW IF EXISTS view_penjualan');
        DB::statement('DROP VIEW IF EXISTS view_retur');
        DB::statement('DROP VIEW IF EXISTS view_user');
    }
};
