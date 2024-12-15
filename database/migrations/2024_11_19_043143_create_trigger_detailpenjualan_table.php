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

        // Trigger insert detail penjualan
        DB::unprepared('
CREATE TRIGGER trg_after_detail_penjualan_insert 
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
    DECLARE v_sisa_stok INT DEFAULT 0;
    DECLARE v_subtotal_penjualan DECIMAL(10,2) DEFAULT 0;
    DECLARE v_margin_persen DECIMAL(5,2) DEFAULT 0;
    DECLARE v_ppn_value DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total_value DECIMAL(10,2) DEFAULT 0;

    # Hitung sisa stok dari kartu_stok sebelumnya
    SELECT COALESCE(SUM(masuk) - SUM(keluar), 0) INTO v_sisa_stok
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
        "J",  # Jenis transaksi Jual
        0,  
        NEW.jumlah,  # Keluar sejumlah penjualan
        v_sisa_stok,  # Sisa stok setelah pengurangan
        NOW(), 
        NEW.idpenjualan, 
        NEW.idbarang
    );

    # Hitung total subtotal dasar untuk penjualan ini
    SELECT COALESCE(SUM(jumlah * harga_satuan), 0) INTO v_subtotal_penjualan
    FROM detail_penjualan
    WHERE idpenjualan = NEW.idpenjualan;

    # Ambil margin/ppn dari margin_penjualan
    SELECT COALESCE(persen, 0) INTO v_margin_persen
    FROM margin_penjualan mp
    JOIN penjualan p ON p.idmargin_penjualan = mp.idmargin_penjualan
    WHERE p.idpenjualan = NEW.idpenjualan;

    # Hitung total dengan margin
    SET v_total_value = v_subtotal_penjualan * (1 + (v_margin_persen / 100));
    
    # Hitung PPN
    SET v_ppn_value = v_total_value - v_subtotal_penjualan;

    # Update penjualan dengan subtotal, ppn, dan total baru
    UPDATE penjualan 
    SET 
        subtotal_nilai = v_subtotal_penjualan,
        ppn = v_ppn_value,
        total_nilai = v_total_value
    WHERE idpenjualan = NEW.idpenjualan;
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