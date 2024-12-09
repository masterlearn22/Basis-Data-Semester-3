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
        // Trigger Insert (yang sudah Anda miliki)
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
                'K',
                0,
                NEW.jumlah,
                v_current_stock - NEW.jumlah,
                NOW(),
                NEW.idretur,
                v_idbarang
            );
        END
        ");

        // Trigger Update
        DB::unprepared("
        CREATE TRIGGER after_retur_update
        AFTER UPDATE ON detail_retur
        FOR EACH ROW
        BEGIN
            DECLARE v_idbarang INT;
            DECLARE v_current_stock INT;
            DECLARE v_old_jumlah INT;
            DECLARE v_new_jumlah INT;

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

            # Hitung selisih jumlah
            SET v_old_jumlah = OLD.jumlah;
            SET v_new_jumlah = NEW.jumlah;

            # Insert ke kartu_stok dengan perhitungan selisih
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
                'K', # Retur Update
                0,
                v_new_jumlah - v_old_jumlah, # Selisih jumlah
                v_current_stock - (v_new_jumlah - v_old_jumlah),
                NOW(),
                NEW.idretur,
                v_idbarang
            );
        END
        ");

        // Trigger Delete
        DB::unprepared("
        CREATE TRIGGER after_retur_delete
        AFTER DELETE ON detail_retur
        FOR EACH ROW
        BEGIN
            DECLARE v_idbarang INT;
            DECLARE v_current_stock INT;

            # Ambil idbarang dari detail_penerimaan
            SELECT idbarang INTO v_idbarang 
            FROM detail_penerimaan 
            WHERE iddetail_penerimaan = OLD.iddetail_penerimaan;

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
                'D', # Retur Delete
                OLD.jumlah, # Kembalikan stok
                0,
                v_current_stock + OLD.jumlah,
                NOW(),
                OLD.idretur,
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
        DB::unprepared("DROP TRIGGER IF EXISTS after_retur_update");
        DB::unprepared("DROP TRIGGER IF EXISTS after_retur_delete");
    }
};