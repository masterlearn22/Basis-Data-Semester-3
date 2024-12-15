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
        SELECT 
            detail_penerimaan.iddetail_penerimaan,
            detail_penerimaan.idpenerimaan,
            detail_penerimaan.idbarang,
            detail_penerimaan.harga_satuan,
            detail_penerimaan.jumlah_terima,
            detail_penerimaan.sub_total,
            penerimaan.status AS status_penerimaan,
            barang.nama AS nama_barang
        FROM detail_penerimaan
        JOIN penerimaan ON penerimaan.idpenerimaan = detail_penerimaan.idpenerimaan
        LEFT JOIN barang ON barang.idbarang = detail_penerimaan.idbarang
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
        SELECT dp.iddetail_penjualan, dp.idpenjualan, b.nama, dp.Jumlah AS jumlah, 
        dp.harga_satuan AS harga,dp.subtotal
        FROM detail_penjualan dp
        JOIN barang b ON dp.idbarang = b.idbarang
        JOIN penjualan p ON dp.idpenjualan = p.idpenjualan
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
        SELECT 
            b.idbarang,
            b.nama AS nama_barang,
            COALESCE(SUM(k.masuk), 0) AS total_masuk,
            COALESCE(SUM(k.keluar), 0) AS total_keluar,
            COALESCE(SUM(k.masuk - k.keluar), 0) AS stok_saat_ini,
            (SELECT jenis_transaksi 
             FROM kartu_stok 
             WHERE idbarang = b.idbarang 
             ORDER BY created_at DESC 
             LIMIT 1) AS jenis_transaksi_terakhir,
            (SELECT created_at 
             FROM kartu_stok 
             WHERE idbarang = b.idbarang 
             ORDER BY created_at DESC 
             LIMIT 1) AS waktu_transaksi_terakhir
        FROM barang b
        LEFT JOIN kartu_stok k ON b.idbarang = k.idbarang
        GROUP BY b.idbarang, b.nama
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
