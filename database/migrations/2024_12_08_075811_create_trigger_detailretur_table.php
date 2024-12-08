<?php

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
        CREATE TRIGGER after_retur_insert
        AFTER INSERT ON detail_retur
        FOR EACH ROW
        BEGIN
            DECLARE v_idbarang INT;
            DECLARE v_current_stock INT;

            # Ambil idbarang dari detail_penerimaan
            SELECT idbarang INTO v_idbarang 
            FROM detail_penerimaan 
            WHERE iddetail_penerimaan = NEW.iddetail_penerimaan;

            # Hitung stok terakhir sebelum retur
            SELECT IFNULL(
                (SELECT stock FROM kartu_stok 
                WHERE idbarang = v_idbarang 
                ORDER BY idkartu_stok DESC 
                LIMIT 1), 0) 
            INTO v_current_stock;

            # Insert ke kartu_stok
            INSERT INTO kartu_stok (
                jenis_transaksi, 
                masuk, 
                keluar, 
                stock, 
                created_at, 
                id_transaksi, 
                idbarang
            )
            VALUES (
                'R',
                0,
                NEW.jumlah,
                v_current_stock - NEW.jumlah,
                NOW(),
                NEW.idretur,
                v_idbarang
            );
        END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_retur_insert");
    }
};
