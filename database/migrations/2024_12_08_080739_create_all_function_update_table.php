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
        $functions = [
            'fn_update_role',
            'fn_update_barang',
            'fn_update_user',
            'fn_update_vendor',
            'fn_update_satuan',
            'fn_update_pengadaan',
            'fn_update_detail_pengadaan',
            'fn_update_penerimaan',
            'fn_update_detail_penerimaan',
            'fn_update_retur',
            'fn_update_detail_retur',
            'fn_update_margin_penjualan',
            'fn_update_penjualan',
            'fn_update_detail_penjualan'
        ];

        foreach ($functions as $function) {
            DB::statement("DROP FUNCTION IF EXISTS $function");
        }

        DB::statement('
        CREATE FUNCTION fn_update_role(
             p_idrole INT,
             p_nama_role VARCHAR(255)
         ) 
         RETURNS INT
         DETERMINISTIC
         BEGIN
             DECLARE v_rows_affected INT;
             DECLARE v_existing_role_count INT;
             
             -- Cek apakah role dengan nama yang sama sudah ada
             SELECT COUNT(*) INTO v_existing_role_count
             FROM role 
             WHERE nama_role = p_nama_role AND idrole != p_idrole;
             
             -- Jika nama role belum ada, lakukan update
             IF v_existing_role_count = 0 THEN
                 UPDATE role 
                 SET 
                     nama_role = p_nama_role,
                     updated_at = NOW()
                 WHERE idrole = p_idrole;
                 
                 SET v_rows_affected = ROW_COUNT();
             ELSE
                 -- Jika nama role sudah ada, kembalikan -2
                 SET v_rows_affected = -2;
             END IF;
             
             RETURN v_rows_affected;
         END;
        ');


       DB::statement('
       CREATE FUNCTION fn_update_barang(
            p_idbarang INT,
            p_jenis CHAR(1),
            p_nama VARCHAR(100),
            p_status TINYINT,
            p_harga INT,
            p_idsatuan INT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            DECLARE v_rows_affected INT;
            
            UPDATE barang 
            SET 
                jenis = p_jenis,
                nama = p_nama,
                status = p_status,
                harga = p_harga,
                idsatuan = p_idsatuan
            WHERE idbarang = p_idbarang;
            
            SET v_rows_affected = ROW_COUNT();
            
            RETURN v_rows_affected;
        END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_user(
    p_iduser INT,
    p_username VARCHAR(50),
    p_password VARCHAR(255),
    p_idrole INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_existing_username_count INT;
    
    -- Cek username sudah ada
    SELECT COUNT(*) INTO v_existing_username_count
    FROM users 
    WHERE username = p_username AND iduser != p_iduser;
    
    -- Jika username belum ada
    IF v_existing_username_count = 0 THEN
        UPDATE users 
        SET 
            username = p_username,
            password = IF(p_password IS NOT NULL, p_password, password),
            idrole = p_idrole,
            updated_at = NOW()
        WHERE iduser = p_iduser;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Username sudah ada
        SET v_rows_affected = -2;
    END IF;
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_vendor(
    p_idvendor INT,
    p_nama_vendor VARCHAR(100),
    p_badan_hukum VARCHAR(50),
    p_status TINYINT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_existing_vendor_count INT;
    
    -- Cek nama vendor sudah ada
    SELECT COUNT(*) INTO v_existing_vendor_count
    FROM vendor 
    WHERE nama_vendor = p_nama_vendor AND idvendor != p_idvendor;
    
    -- Jika nama vendor belum ada
    IF v_existing_vendor_count = 0 THEN
        UPDATE vendor 
        SET 
            nama_vendor = p_nama_vendor,
            badan_hukum = p_badan_hukum,
            status = p_status,
            updated_at = NOW()
        WHERE idvendor = p_idvendor;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Nama vendor sudah ada
        SET v_rows_affected = -2;
    END IF;
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_satuan(
    p_idsatuan INT,
    p_nama_satuan VARCHAR(50),
    p_status TINYINT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    DECLARE v_existing_satuan_count INT;
    
    -- Cek nama satuan sudah ada
    SELECT COUNT(*) INTO v_existing_satuan_count
    FROM satuan 
    WHERE nama_satuan = p_nama_satuan AND idsatuan != p_idsatuan;
    
    -- Jika nama satuan belum ada
    IF v_existing_satuan_count = 0 THEN
        UPDATE satuan 
        SET 
            nama_satuan = p_nama_satuan,
            status = p_status,
            updated_at = NOW()
        WHERE idsatuan = p_idsatuan;
        
        SET v_rows_affected = ROW_COUNT();
    ELSE
        -- Nama satuan sudah ada
        SET v_rows_affected = -2;
    END IF;
    
    RETURN v_rows_affected;
END ;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_pengadaan(
    p_idpengadaan INT,
    p_idvendor INT,
    p_tanggal DATE,
    p_total_harga INT,
    p_status TINYINT,
    p_iduser INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE pengadaan 
    SET 
        idvendor = p_idvendor,
        tanggal = p_tanggal,
        total_harga = p_total_harga,
        status = p_status,
        iduser = p_iduser,
        updated_at = NOW()
    WHERE idpengadaan = p_idpengadaan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_detail_pengadaan(
    p_iddetail_pengadaan INT,
    p_idpengadaan INT,
    p_idbarang INT,
    p_jumlah INT,
    p_harga INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE detail_pengadaan 
    SET 
        idpengadaan = p_idpengadaan,
        idbarang = p_idbarang,
        jumlah = p_jumlah,
        harga = p_harga,
        updated_at = NOW()
    WHERE iddetail_pengadaan = p_iddetail_pengadaan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_penerimaan(
    p_idpenerimaan INT,
    p_idpengadaan INT,
    p_tanggal DATE,
    p_total_diterima INT,
    p_status TINYINT,
    p_iduser INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE penerimaan 
    SET 
        idpengadaan = p_idpengadaan,
        tanggal = p_tanggal,
        total_diterima = p_total_diterima,
        status = p_status,
        iduser = p_iduser,
        updated_at = NOW()
    WHERE idpenerimaan = p_idpenerimaan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_detail_penerimaan(
            p_iddetail_penerimaan INT,
            p_idpenerimaan INT,
            p_iddetail_pengadaan INT,
            p_jumlah INT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            DECLARE v_rows_affected INT;
            
            UPDATE detail_penerimaan 
            SET 
                idpenerimaan = p_idpenerimaan,
                iddetail_pengadaan = p_iddetail_pengadaan,
                jumlah = p_jumlah,
                updated_at = NOW()
            WHERE iddetail_penerimaan = p_iddetail_penerimaan;
            
            SET v_rows_affected = ROW_COUNT();
            
            RETURN v_rows_affected;
        END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_retur(
    p_idretur INT,
    p_idpenerimaan INT,
    p_tanggal DATE,
    p_total_retur INT,
    p_status TINYINT,
    p_iduser INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE retur 
    SET 
        idpenerimaan = p_idpenerimaan,
        tanggal = p_tanggal,
        total_retur = p_total_retur,
        status = p_status,
        iduser = p_iduser,
        updated_at = NOW()
    WHERE idretur = p_idretur;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_detail_retur(
    p_iddetail_retur INT,
    p_idretur INT,
    p_idbarang INT,
    p_jumlah INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE detail_retur 
    SET 
        idretur = p_idretur,
        idbarang = p_idbarang,
        jumlah = p_jumlah,
        updated_at = NOW()
    WHERE iddetail_retur = p_iddetail_retur;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_margin_penjualan(
    p_idmargin INT,
    p_idpenjualan INT,
    p_margin INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE margin_penjualan 
    SET 
        idpenjualan = p_idpenjualan,
        margin = p_margin,
        updated_at = NOW()
    WHERE idmargin = p_idmargin;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_penjualan(
    p_idpenjualan INT,
    p_iduser INT,
    p_tanggal DATE,
    p_total_penjualan INT,
    p_status TINYINT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE penjualan 
    SET 
        iduser = p_iduser,
        tanggal = p_tanggal,
        total_penjualan = p_total_penjualan,
        status = p_status,
        updated_at = NOW()
    WHERE idpenjualan = p_idpenjualan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');

       DB::statement('
       CREATE FUNCTION fn_update_detail_penjualan(
    p_iddetail_penjualan INT,
    p_idpenjualan INT,
    p_idbarang INT,
    p_jumlah INT,
    p_harga INT
) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_rows_affected INT;
    
    UPDATE detail_penjualan 
    SET 
        idpenjualan = p_idpenjualan,
        idbarang = p_idbarang,
        jumlah = p_jumlah,
        harga = p_harga,
        updated_at = NOW()
    WHERE iddetail_penjualan = p_iddetail_penjualan;
    
    SET v_rows_affected = ROW_COUNT();
    
    RETURN v_rows_affected;
END;
       ');
    }
};
