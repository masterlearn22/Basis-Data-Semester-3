<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS sp_create_barang');
    }
};
