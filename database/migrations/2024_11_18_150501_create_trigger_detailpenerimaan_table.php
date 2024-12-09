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
        CREATE TRIGGER before_detail_penerimaan_insert
        BEFORE INSERT ON detail_penerimaan
        FOR EACH ROW
        BEGIN
            DECLARE rencana_jumlah INT;
            DECLARE total_terima INT;
            DECLARE MESSAGE_TEXT VARCHAR(100);
        
            -- Ambil jumlah rencana dari detail_pengadaan
            SELECT jumlah INTO rencana_jumlah
            FROM detail_pengadaan 
            WHERE iddetail_pengadaan = (
                SELECT iddetail_pengadaan 
                FROM pengadaan p
                JOIN detail_pengadaan dp ON p.idpengadaan = dp.idpengadaan
                JOIN penerimaan pen ON p.idpengadaan = pen.idpengadaan
                WHERE pen.idpenerimaan = NEW.idpenerimaan 
                AND dp.idbarang = NEW.idbarang
            );
        
            -- Hitung total yang sudah diterima
            SELECT COALESCE(SUM(jumlah_terima), 0) INTO total_terima
            FROM detail_penerimaan dp
            JOIN penerimaan p ON dp.idpenerimaan = p.idpenerimaan
            JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
            JOIN detail_pengadaan dpg ON pg.idpengadaan = dpg.idpengadaan
            WHERE dpg.idbarang = NEW.idbarang;
        
            -- Validasi jumlah
            IF (total_terima + NEW.jumlah_terima) > rencana_jumlah THEN
                SIGNAL SQLSTATE "45000";
                SET MESSAGE_TEXT = "Jumlah penerimaan melebihi jumlah yang direncanakan";
            END IF;
        END;
        ');


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
    DECLARE current_stock INT;

    -- Hitung stok saat ini
    SELECT IFNULL(SUM(masuk) - SUM(keluar), 0)
    INTO current_stock
    FROM kartu_stok
    WHERE idbarang = OLD.idbarang;

    -- Insert record pengurangan stok
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
        'D',
        -OLD.jumlah_terima,
        0,
        current_stock - OLD.jumlah_terima,
        NOW(),
        OLD.idpenerimaan,
        OLD.idbarang
    );
END ;

        ");
    

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
