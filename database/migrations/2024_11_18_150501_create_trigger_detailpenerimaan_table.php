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
        DB::statement('
        CREATE TRIGGER after_penerimaan_status_update
        AFTER INSERT ON detail_penerimaan
        FOR EACH ROW
        BEGIN   
                DECLARE jumlah_terima INT;
                DECLARE v_status VARCHAR(255);

                SELECT status INTO v_status
                FROM penerimaan
                WHERE idpenerimaan  = NEW.idpenerimaan;

                SELECT dp.jumlah INTO jumlah_terima
                FROM detail_pengadaan dp
                JOIN penerimaan p ON p.idpengadaan = dp.idpengadaan
                WHERE p.idpenerimaan = NEW.idpenerimaan;

            -- Mengecek apakah status penerimaan telah diubah menjadi 1 (approved)
            IF v_status = 1 THEN
                -- Insert data ke kartu_stok jika status penerimaan disetujui
                INSERT INTO kartu_stok (
                    jenis_transaksi, 
                    masuk, 
                    keluar, 
                    stock, 
                    created_at, 
                    id_transaksi, 
                    idbarang
                )
                SELECT 
                    "M",  -- Jenis transaksi adalah "M" untuk masuk
                    jumlah_terima,  -- Jumlah yang diterima
                    0,  -- Tidak ada barang yang keluar
                    COALESCE((
                        SELECT SUM(masuk) - SUM(keluar) 
                        FROM kartu_stok 
                        WHERE idbarang = dp.idbarang
                    ), 0) + jumlah_terima,  -- Hitung stok saat ini dan tambahkan jumlah yang diterima
                    NOW(),  -- Waktu saat ini
                    NEW.idpenerimaan,  -- ID penerimaan yang baru diperbarui
                    dp.idbarang  -- ID barang dari detail penerimaan
                FROM detail_penerimaan dp
                WHERE dp.idpenerimaan = NEW.idpenerimaan;
            END IF;
        END;
    ');
    

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS before_penerimaan_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS after_penerimaan_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS after_penerimaan_delete");
    }
};
