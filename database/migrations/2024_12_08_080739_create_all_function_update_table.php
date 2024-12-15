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
            UPDATE role 
            SET nama_role = p_nama_role
            WHERE idrole = p_idrole;
        RETURN ROW_COUNT();
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
            DECLARE cek_nama_yang_sama INT;
            
            -- Cek username sudah ada
            SELECT COUNT(*) INTO cek_nama_yang_sama
            FROM users 
            WHERE username = p_username AND iduser != p_iduser;
            
            -- Jika username belum ada
            IF cek_nama_yang_sama = 0 THEN
                UPDATE users 
                SET 
                    username = p_username,
                    password = IF(p_password IS NOT NULL, p_password, password),
                    idrole = p_idrole
                WHERE iduser = p_iduser;
                
                RETURN 1;
            ELSE
                -- Username sudah ada
                RETURN -2;
            END IF;
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
            DECLARE cek_nama_vendor_yang_sama INT;
            
            -- Cek nama vendor sudah ada
            SELECT COUNT(*) INTO cek_nama_vendor_yang_sama
            FROM vendor 
            WHERE nama_vendor = p_nama_vendor AND idvendor != p_idvendor;
            
            -- Jika nama vendor belum ada
            IF cek_nama_vendor_yang_sama = 0 THEN
                UPDATE vendor 
                SET 
                    nama_vendor = p_nama_vendor,
                    badan_hukum = p_badan_hukum,
                    status = p_status
                WHERE idvendor = p_idvendor;
                
                RETURN 1;  -- Mengembalikan 1 jika update berhasil
            ELSE
                -- Nama vendor sudah ada
                RETURN -2;  -- Mengembalikan -2 jika nama vendor sudah ada
            END IF;
        END;
        ');

        DB::statement('
        CREATE FUNCTION fn_update_satuan(
            p_idsatuan INT,
            p_nama_satuan VARCHAR(45),
            p_status TINYINT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            DECLARE cek_nama_satuan_yang_sama INT;
            
            SELECT COUNT(*) INTO cek_nama_satuan_yang_sama
            FROM satuan 
            WHERE nama_satuan = p_nama_satuan AND idsatuan != p_idsatuan;
            
            IF cek_nama_satuan_yang_sama = 0 THEN
                UPDATE satuan 
                SET 
                    nama_satuan = p_nama_satuan,
                    status = p_status
                WHERE idsatuan = p_idsatuan;
                
                RETURN 1;
            ELSE
                RETURN -2;
            END IF;
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
            UPDATE barang 
            SET 
                jenis = p_jenis,
                nama = p_nama,
                status = p_status,
                harga = p_harga,
                idsatuan = p_idsatuan
            WHERE idbarang = p_idbarang;
            
            RETURN ROW_COUNT();
        END;
        ');
        

        DB::statement('
        CREATE FUNCTION fn_update_pengadaan(
            p_idpengadaan BIGINT,
            p_idvendor INT,
            p_status VARCHAR(1),
            p_iduser INT,
            p_subtotal_nilai BIGINT,
            p_ppn INT,
            p_total_nilai INT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            UPDATE pengadaan 
            SET 
                idvendor = COALESCE(p_idvendor, idvendor),
                status = COALESCE(p_status, status),
                iduser = COALESCE(p_iduser, iduser),
                subtotal_nilai = COALESCE(p_subtotal_nilai, subtotal_nilai),
                ppn = COALESCE(p_ppn, ppn),
                total_nilai = COALESCE(p_total_nilai, subtotal_nilai * (1 + (ppn/100))),
                updated_at = NOW()
            WHERE idpengadaan = p_idpengadaan;
            
            RETURN ROW_COUNT();
        END;
        ');

        DB::statement('
        CREATE FUNCTION fn_update_detail_pengadaan(
            p_iddetail_pengadaan BIGINT,
            p_idpengadaan BIGINT,
            p_idbarang INT,
            p_harga_satuan INT,
            p_jumlah INT,
            p_sub_total BIGINT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            UPDATE detail_pengadaan 
            SET 
                idpengadaan = COALESCE(p_idpengadaan, idpengadaan),
                idbarang = COALESCE(p_idbarang, idbarang),
                jumlah = COALESCE(p_jumlah, jumlah),
                sub_total = COALESCE(p_sub_total, harga_satuan * jumlah)
            WHERE iddetail_pengadaan = p_iddetail_pengadaan;
            
            RETURN ROW_COUNT();
        END;
        ');


    
        
        DB::statement('
        CREATE FUNCTION fn_update_margin_penjualan(
            p_idmargin_penjualan INT,
            p_persen INT,
            p_status TINYINT
        ) 
        RETURNS INT
        DETERMINISTIC
        BEGIN
            UPDATE margin_penjualan 
            SET 
                persen = p_persen,
                status = p_status,
                updated_at = NOW()
            WHERE idmargin_penjualan = p_idmargin_penjualan;
            
            RETURN ROW_COUNT();
        END;
        ');
    }
};
