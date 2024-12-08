<?php

use Illuminate\Support\Facades\DB;


use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $functions = [
            'fn_delete_role',
            'fn_delete_user',
            'fn_delete_vendor',
            'fn_delete_satuan',
            'fn_delete_barang',
            'fn_delete_pengadaan',
            'fn_delete_detail_pengadaan',
            'fn_delete_penerimaan',
            'fn_delete_detail_penerimaan',
            'fn_delete_retur',
            'fn_delete_detail_retur',
            'fn_delete_margin_penjualan',
            'fn_delete_penjualan',
            'fn_delete_detail_penjualan'
        ];

        foreach ($functions as $function) {
            DB::statement("DROP FUNCTION IF EXISTS $function");
        }

        DB::statement('
        CREATE FUNCTION fn_delete_role(p_idrole INT) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            DECLARE v_rows_affected INT;
            DECLARE v_user_count INT;
            
            -- Cek apakah role masih digunakan oleh user
            SELECT COUNT(*) INTO v_user_count
            FROM users 
            WHERE idrole = p_idrole;
            
            -- Jika tidak ada user yang menggunakan role, maka hapus
            IF v_user_count = 0 THEN
                DELETE FROM role 
                WHERE idrole = p_idrole;
                
                SET v_rows_affected = ROW_COUNT();
            ELSE
                -- Jika masih ada user, kembalikan -1
                SET v_rows_affected = -1;
            END IF;
            
            RETURN v_rows_affected;
        END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_user(p_iduser INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_transaksi_count INT;
    
    -- Cek user terlibat transaksi
    SELECT COUNT(*) INTO v_transaksi_count
    FROM (
        SELECT iduser FROM pengadaan WHERE iduser = p_iduser
        UNION
        SELECT iduser FROM penjualan WHERE iduser = p_iduser
    ) AS transaksi;
    
    -- Jika tidak ada transaksi, hapus
    IF v_transaksi_count = 0 THEN
        DELETE FROM users 
        WHERE iduser = p_iduser;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Masih ada transaksi
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_vendor(p_idvendor INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_transaksi_count INT;
    
    -- Cek vendor dalam transaksi
    SELECT COUNT(*) INTO v_transaksi_count
    FROM pengadaan
    WHERE idvendor = p_idvendor;
    
    -- Jika tidak ada transaksi, hapus
    IF v_transaksi_count = 0 THEN
        DELETE FROM vendor 
        WHERE idvendor = p_idvendor;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Masih ada transaksi
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_satuan(p_idsatuan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_barang_count INT;
    
    -- Cek satuan digunakan barang
    SELECT COUNT(*) INTO v_barang_count
    FROM barang
    WHERE idsatuan = p_idsatuan;
    
    -- Jika tidak ada barang, hapus
    IF v_barang_count = 0 THEN
        DELETE FROM satuan 
        WHERE idsatuan = p_idsatuan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Masih ada barang
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_barang(p_idbarang INT) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            DECLARE v_rows_affected INT;
            DECLARE v_used_in_transactions INT;
            
            -- Cek apakah barang sudah digunakan dalam transaksi
            SELECT COUNT(*) INTO v_used_in_transactions
            FROM (
                SELECT idbarang FROM detail_pengadaan WHERE idbarang = p_idbarang
                UNION
                SELECT idbarang FROM detail_penjualan WHERE idbarang = p_idbarang
                UNION
                SELECT idbarang FROM detail_penerimaan WHERE idbarang = p_idbarang
            ) AS transactions;
            
            -- Jika tidak digunakan dalam transaksi, maka hapus
            IF v_used_in_transactions = 0 THEN
                DELETE FROM barang WHERE idbarang = p_idbarang;
                SET v_rows_affected = ROW_COUNT();
            ELSE
                SET v_rows_affected = -1; -- Menandakan barang tidak bisa dihapus
            END IF;
            
            RETURN v_rows_affected;
        END ;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_pengadaan(p_idpengadaan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_penerimaan_count INT;
    
    -- Cek apakah sudah ada penerimaan
    SELECT COUNT(*) INTO v_penerimaan_count
    FROM penerimaan
    WHERE idpengadaan = p_idpengadaan;
    
    -- Jika belum ada penerimaan, hapus
    IF v_penerimaan_count = 0 THEN
        -- Hapus detail pengadaan terlebih dahulu
        DELETE FROM detail_pengadaan 
        WHERE idpengadaan = p_idpengadaan;
        
        -- Hapus pengadaan
        DELETE FROM pengadaan 
        WHERE idpengadaan = p_idpengadaan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Sudah ada penerimaan
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_detail_pengadaan(p_iddetail_pengadaan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_penerimaan_count INT;
    
    -- Cek apakah detail sudah ada di penerimaan
    SELECT COUNT(*) INTO v_penerimaan_count
    FROM detail_penerimaan dp
    JOIN detail_pengadaan dpe ON dp.iddetail_pengadaan = dpe.iddetail_pengadaan
    WHERE dpe.iddetail_pengadaan = p_iddetail_pengadaan;
    
    -- Jika belum ada di penerimaan, hapus
    IF v_penerimaan_count = 0 THEN
        DELETE FROM detail_pengadaan 
        WHERE iddetail_pengadaan = p_iddetail_pengadaan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Sudah ada di penerimaan
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_penerimaan(p_idpenerimaan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_retur_count INT;
    
    -- Cek apakah sudah ada retur
    SELECT COUNT(*) INTO v_retur_count
    FROM retur
    WHERE idpenerimaan = p_idpenerimaan;
    
    -- Jika belum ada retur, hapus
    IF v_retur_count = 0 THEN
        -- Hapus detail penerimaan terlebih dahulu
        DELETE FROM detail_penerimaan 
        WHERE idpenerimaan = p_idpenerimaan;
        
        -- Hapus penerimaan
        DELETE FROM penerimaan 
        WHERE idpenerimaan = p_idpenerimaan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Sudah ada retur
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_detail_penerimaan(p_iddetail_penerimaan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_retur_count INT;
    
    -- Cek apakah detail sudah ada di retur
    SELECT COUNT(*) INTO v_retur_count
    FROM detail_retur
    WHERE iddetail_penerimaan = p_iddetail_penerimaan;
    
    -- Jika belum ada di retur, hapus
    IF v_retur_count = 0 THEN
        DELETE FROM detail_penerimaan 
        WHERE iddetail_penerimaan = p_iddetail_penerimaan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Sudah ada di retur
        SET v_rows_affected = -1;
    END IF;
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_retur(p_idretur INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    -- Hapus retur
    DELETE FROM retur 
    WHERE idretur = p_idretur;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
       CREATE FUNCTION fn_delete_detail_retur(p_iddetail_retur INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    DELETE FROM detail_retur 
    WHERE iddetail_retur = p_iddetail_retur;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_margin_penjualan(p_idmargin INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    DELETE FROM margin_penjualan 
    WHERE idmargin = p_idmargin;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
CREATE FUNCTION fn_delete_penjualan(p_idpenjualan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    -- Hapus penjualan
    DELETE FROM penjualan 
    WHERE idpenjualan = p_idpenjualan;
    
    SET v_rows_affected = ROW_COUNT ();
    
    RETURN v_rows_affected;
END;
        ');

        DB::statement('
        CREATE FUNCTION fn_delete_detail_penjualan(p_iddetail_penjualan INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    DELETE FROM detail_penjualan 
    WHERE iddetail_penjualan = p_iddetail_penjualan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
        ');
    }
};
