<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Drop triggers jika sudah ada
        DB::unprepared('DROP TRIGGER IF EXISTS after_penjualan_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_update');

        // Trigger update penjualan
        DB::unprepared('
        CREATE TRIGGER after_penjualan_update
        AFTER UPDATE ON penjualan
        FOR EACH ROW
        BEGIN
            DECLARE ppn_value DECIMAL(10,2);

            -- Ambil persen dari margin_penjualan
            SELECT IFNULL(persen, 0) INTO ppn_value
            FROM margin_penjualan
            WHERE idmargin_penjualan = NEW.idmargin_penjualan;

            -- Hindari rekursif dengan menggunakan kondisi
            IF ppn_value > 0 THEN
                UPDATE penjualan
                SET 
                    total_nilai = NEW.subtotal_nilai + (NEW.subtotal_nilai * (ppn_value / 100)),
                    ppn = NEW.subtotal_nilai * (ppn_value / 100)
                WHERE idpenjualan = NEW.idpenjualan;
            END IF;
        END;
        ');

        // Trigger insert detail penjualan
        DB::unprepared('
        CREATE TRIGGER trg_after_detail_penjualan_insert 
        AFTER INSERT ON detail_penjualan
        FOR EACH ROW
        BEGIN
            DECLARE v_sisa_stok INT DEFAULT 0;

            # Hitung sisa stok dari kartu_stok sebelumnya
            SELECT COALESCE(SUM(masuk - keluar), 0) INTO v_sisa_stok
            FROM kartu_stok 
            WHERE idbarang = NEW.idbarang;

            # Kurangi sisa stok
            SET v_sisa_stok = v_sisa_stok - NEW.jumlah;

            # Insert pencatatan kartu stok
            INSERT INTO kartu_stok (
                jenis_transaksi, 
                masuk, 
                keluar, 
                stock, 
                created_at, 
                id_transaksi, 
                idbarang
            ) VALUES (
                "J",  # Jual 
                0,    # Masuk 0 
                NEW.jumlah,  # Keluar sejumlah penjualan
                v_sisa_stok,  # Sisa stok setelah pengurangan
                NOW(), 
                NEW.idpenjualan, 
                NEW.idbarang
            );
        END;
        ');

        // Trigger delete detail penjualan
        DB::unprepared('
        CREATE TRIGGER trg_after_detail_penjualan_delete
        AFTER DELETE ON detail_penjualan
        FOR EACH ROW
        BEGIN
            # Hapus kartu stok untuk transaksi penjualan
            DELETE FROM kartu_stok 
            WHERE 
                jenis_transaksi = "J" 
                AND id_transaksi = OLD.idpenjualan 
                AND idbarang = OLD.idbarang;
        END;
        ');

        // Trigger update detail penjualan
        DB::unprepared('
        CREATE TRIGGER trg_after_detail_penjualan_update
        AFTER UPDATE ON detail_penjualan
        FOR EACH ROW
        BEGIN
            DECLARE v_current_stock INT;

            # Hitung ulang stok
            SELECT COALESCE(SUM(masuk - keluar), 0) INTO v_current_stock
            FROM kartu_stok 
            WHERE idbarang = NEW.idbarang;

            # Update kartu stok untuk transaksi penjualan
            UPDATE kartu_stok 
            SET 
                keluar = NEW.jumlah,
                stock = v_current_stock - NEW.jumlah
            WHERE 
                jenis_transaksi = "J"
                AND id_transaksi = NEW.idpenjualan 
                AND idbarang = NEW.idbarang;
        END;
        ');
    }

    public function down(): void
    {
        // Hapus trigger saat rollback
        DB::unprepared('DROP TRIGGER IF EXISTS after_penjualan_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_detail_penjualan_update');
    }
};