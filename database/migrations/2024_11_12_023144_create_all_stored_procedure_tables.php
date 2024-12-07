<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stored Procedure untuk barang
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_barang');
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_user');
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_role');
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_vendor');
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_satuan');
        DB::statement("DROP PROCEDURE IF EXISTS sp_create_penerimaan");
        DB::statement("DROP PROCEDURE IF EXISTS sp_create_retur");
        DB::statement("DROP PROCEDURE IF EXISTS sp_create_penjualan");
        DB::statement('
            CREATE PROCEDURE sp_create_barang(
                IN p_jenis CHAR(1),
                IN p_nama VARCHAR(100),
                IN p_status TINYINT,
                IN p_harga INT,
                IN p_idsatuan INT
            )
            BEGIN
                INSERT INTO barang (jenis, nama, status, harga, idsatuan)
                VALUES (p_jenis, p_nama, p_status, p_harga, p_idsatuan);
            END
        ');

        // Stored Procedure untuk user
        DB::statement('
            CREATE PROCEDURE sp_create_user(
                IN p_username VARCHAR(255),
                IN p_password VARCHAR(255),
                IN p_idrole INT
            )
            BEGIN
                INSERT INTO users (username, password, idrole) 
                VALUES (p_username, p_password, p_idrole);
            END
        ');

        // Stored Procedure untuk role
        DB::statement('
            CREATE PROCEDURE sp_create_role(
                IN p_nama_role VARCHAR(255)
            )
            BEGIN
                INSERT INTO role (nama_role) VALUES (p_nama_role);
            END
        ');

        // Stored Procedure untuk vendor
        DB::statement('
            CREATE PROCEDURE sp_create_vendor(
                IN p_nama_vendor VARCHAR(100),
                IN p_badan_hukum CHAR(1),
                IN p_status CHAR(1)
            )
            BEGIN
                INSERT INTO vendor (nama_vendor, badan_hukum, status) 
                VALUES (p_nama_vendor, p_badan_hukum, p_status);
            END
        ');

        // Stored Procedure untuk satuan
        DB::statement('
            CREATE PROCEDURE sp_create_satuan(
                IN p_nama_satuan VARCHAR(255),
                IN p_status INT
            )
            BEGIN
                INSERT INTO satuan (nama_satuan, status) 
                VALUES (p_nama_satuan, p_status);
            END
        ');

        DB::statement('
         CREATE PROCEDURE sp_create_penerimaan(
        IN p_idpengadaan INT,
        IN p_status VARCHAR(255),
        IN p_iduser INT
    )
    BEGIN
        INSERT INTO penerimaan (idpengadaan, status, created_at, iduser)
        VALUES (p_idpengadaan, p_status, NOW(), p_iduser);
    END 
        ');

        DB::statement('
        CREATE PROCEDURE sp_create_retur(
    IN p_idpenerimaan INT,
    IN p_iduser INT,
    IN p_jumlah INT
)
BEGIN
    INSERT INTO retur (idpenerimaan, iduser, jumlah, created_at, updated_at)
    VALUES (p_idpenerimaan, p_iduser, p_jumlah, NOW(), NOW());
END
        ');

        DB::statement('
CREATE PROCEDURE sp_create_penjualan(
    IN p_subtotal_nilai INT,
    IN p_idmargin_penjualan INT,
    IN p_iduser INT
)
BEGIN
    DECLARE ppn_rate DOUBLE;
    DECLARE ppn_value INT;
    DECLARE total_value INT;

    -- Ambil persentase PPN dari margin_penjualan
    SELECT persen INTO ppn_rate
    FROM margin_penjualan
    WHERE idmargin_penjualan = p_idmargin_penjualan;

    -- Hitung PPN dan total nilai
    SET ppn_value = p_subtotal_nilai * (ppn_rate / 100);
    SET total_value = p_subtotal_nilai + ppn_value;

    -- Masukkan data ke tabel penjualan
    INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, idmargin_penjualan, iduser, created_at, updated_at)
    VALUES (p_subtotal_nilai, ppn_value, total_value, p_idmargin_penjualan, p_iduser, NOW(), NOW());
END;

        ');

    //     DB::statement('
        
    //     ');

    //     DB::statement('
        
    //     ');
    }


};
