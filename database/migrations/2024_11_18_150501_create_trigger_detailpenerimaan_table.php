<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
        CREATE TRIGGER after_penerimaan_insert
        AFTER INSERT ON detail_penerimaan
        FOR EACH ROW
        BEGIN
            DECLARE current_stock INT;
    
            -- Ambil stok saat ini dari kartu_stok
            SELECT IFNULL(SUM(masuk) - SUM(keluar), 0)
            INTO current_stock
            FROM kartu_stok
            WHERE idbarang = NEW.idbarang;
    
            -- Insert ke kartu_stok
            INSERT INTO kartu_stok (
                jenis_transaksi, masuk, keluar, stock, id_transaksi, idbarang
            )
            VALUES (
                'M',
                NEW.jumlah_terima,
                0,
                current_stock + NEW.jumlah_terima,
                NEW.idpenerimaan,
                NEW.idbarang
                );
            END;
        ");

        DB::unprepared("
        CREATE TRIGGER after_penerimaan_delete
        AFTER DELETE ON detail_penerimaan
        FOR EACH ROW
        BEGIN
            UPDATE kartu_stok
            SET stock = GREATEST(stock - OLD.jumlah_terima, 0),
                masuk = GREATEST(masuk - OLD.jumlah_terima, 0)
            WHERE idbarang = OLD.idbarang;
        END;

        ");
    

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_penerimaan_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS after_penerimaan_delete");
        DB::unprepared("DROP TRIGGER IF EXISTS after_penjualan_insert");
    }
};
