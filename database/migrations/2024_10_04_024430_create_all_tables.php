<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAllTables extends Migration
{
    public function up()
    {
        // Table: role
        DB::statement("
                CREATE TABLE role (
                idrole INT PRIMARY KEY AUTO_INCREMENT,
                nama_role VARCHAR(100)
            );
        ");

        // Table: users
        DB::statement("
            CREATE TABLE users (
                iduser INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(45),
                password VARCHAR(100),
                idrole INT,
                FOREIGN KEY (idrole) REFERENCES role(idrole) ON DELETE SET NULL
            );
        ");

        // Table: vendor
        DB::statement("
            CREATE TABLE vendor (
                idvendor INT PRIMARY KEY AUTO_INCREMENT,
                nama_vendor VARCHAR(100),
                badan_hukum CHAR(1),
                status CHAR(1)
            );
        ");

        // Table: satuan
        DB::statement("
            CREATE TABLE satuan (
                idsatuan INT PRIMARY KEY AUTO_INCREMENT,
                nama_satuan VARCHAR(45),
                status TINYINT
            );
        ");

        // Table: barang
        DB::statement("
            CREATE TABLE barang (
                idbarang INT PRIMARY KEY AUTO_INCREMENT,
                jenis CHAR(1),
                kode_jns_barang CHAR(1),
                nama VARCHAR(100),
                idsatuan INT,
                status TINYINT,
                harga INT,
                FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan) ON DELETE SET NULL
            );
        ");

        // Table: kartu_stok
        DB::statement("
            CREATE TABLE kartu_stok (
                idkartu_stok BIGINT PRIMARY KEY AUTO_INCREMENT,
                jenis_transaksi CHAR(1),
                masuk INT,
                keluar INT,
                stock INT,
                created_at TIMESTAMP,
                id_transaksi INT, 
                idbarang INT,
                FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE CASCADE
            );
        ");

        // Table: pengadaan
        DB::statement("
            CREATE TABLE pengadaan (
                idpengadaan BIGINT PRIMARY KEY AUTO_INCREMENT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                iduser INT,
                status VARCHAR(1),
                idvendor INT,
                subtotal_nilai INT,
                ppn INT,
                total_nilai INT,
                FOREIGN KEY (idvendor) REFERENCES vendor(idvendor) ON DELETE SET NULL,
                FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL
            );
        ");

        // Table: detail_pengadaan
        DB::statement("
            CREATE TABLE detail_pengadaan (
                iddetail_pengadaan BIGINT PRIMARY KEY AUTO_INCREMENT,
                harga_satuan INT,
                jumlah INT,
                sub_total INT,
                idbarang INT,
                idpengadaan BIGINT,
                FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan) ON DELETE CASCADE,
                FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE SET NULL
            );
        ");

        // Table: penerimaan
        DB::statement("
            CREATE TABLE penerimaan (
                idpenerimaan BIGINT PRIMARY KEY AUTO_INCREMENT,
                created_at TIMESTAMP,
                status VARCHAR(1),
                idpengadaan BIGINT,
                iduser INT,
                FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan) ON DELETE CASCADE,
                FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL
            );
        ");

        // Table: detail_penerimaan
        DB::statement("
            CREATE TABLE detail_penerimaan (
                iddetail_penerimaan BIGINT PRIMARY KEY AUTO_INCREMENT,
                idpenerimaan BIGINT,
                idbarang INT,
                jumlah_terima INT,
                harga_satuan INT,
                sub_total_terima INT,
                FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan) ON DELETE CASCADE,
                FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE SET NULL
            );
        ");

        // Table: retur
        DB::statement("
            CREATE TABLE retur (
                idretur BIGINT PRIMARY KEY AUTO_INCREMENT,
                created_at TIMESTAMP,
                idpenerimaan BIGINT,
                iduser INT,
                FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan) ON DELETE CASCADE,
                FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL
            );
        ");

        // Table: detail_retur
        DB::statement("
            CREATE TABLE detail_retur (
                iddetail_retur INT PRIMARY KEY AUTO_INCREMENT,
                jumlah INT,
                alasan VARCHAR(200),
                idretur BIGINT,
                iddetail_penerimaan BIGINT,
                FOREIGN KEY (idretur) REFERENCES retur(idretur) ON DELETE CASCADE,
                FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan) ON DELETE CASCADE
            );
        ");

        DB::statement("
        CREATE TABLE margin_penjualan (
            idmargin_penjualan INT PRIMARY KEY AUTO_INCREMENT,
            created_at TIMESTAMP,
            persen DOUBLE,
            status TINYINT,
            iduser INT,
            updated_at TIMESTAMP,
            FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL
        );
    ");

        // Table: penjualan
        DB::statement("
            CREATE TABLE penjualan (
                idpenjualan INT PRIMARY KEY AUTO_INCREMENT,
                created_at TIMESTAMP,
                subtotal_nilai INT,
                ppn INT,
                total_nilai INT,
                iduser INT,
                idmargin_penjualan INT,
                FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL,
                FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan) ON DELETE CASCADE
            );
        ");

        // Table: detail_penjualan
        DB::statement("
            CREATE TABLE detail_penjualan (
                iddetail_penjualan BIGINT PRIMARY KEY AUTO_INCREMENT,
                harga_satuan INT,
                jumlah INT,
                subtotal INT,
                idpenjualan INT,
                idbarang INT,
                FOREIGN KEY (idpenjualan) REFERENCES penjualan(idpenjualan) ON DELETE CASCADE,
                FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE SET NULL
            );
        ");
    }

    public function down()
    {
        // Drop all tables in reverse order of their creation
        DB::statement("DROP TABLE IF EXISTS detail_penjualan;");
        DB::statement("DROP TABLE IF EXISTS penjualan;");
        DB::statement("DROP TABLE IF EXISTS margin_penjualan;");
        DB::statement("DROP TABLE IF EXISTS detail_retur;");
        DB::statement("DROP TABLE IF EXISTS retur;");
        DB::statement("DROP TABLE IF EXISTS detail_penerimaan;");
        DB::statement("DROP TABLE IF EXISTS penerimaan;");
        DB::statement("DROP TABLE IF EXISTS detail_pengadaan;");
        DB::statement("DROP TABLE IF EXISTS pengadaan;");
        DB::statement("DROP TABLE IF EXISTS kartu_stok;");
        DB::statement("DROP TABLE IF EXISTS barang;");
        DB::statement("DROP TABLE IF EXISTS satuan;");
        DB::statement("DROP TABLE IF EXISTS vendor;");
        DB::statement("DROP TABLE IF EXISTS users;");
        DB::statement("DROP TABLE IF EXISTS role;");
    }
}
