<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing stored procedures
        $procedures = [
            'sp_create_role', 'sp_create_user', 'sp_create_vendor', 
            'sp_create_satuan', 'sp_create_barang', 'sp_create_pengadaan', 
            'sp_create_detail_pengadaan', 'sp_create_penerimaan', 
            'sp_create_detail_penerimaan', 'sp_create_retur', 
            'sp_create_detail_retur', 'sp_create_penjualan', 
            'sp_create_margin_penjualan','sp_create_detail_penjualan',
            'sp_update_penjualan_subtotal','sp_approve_penerimaan'
        ];


        foreach ($procedures as $procedure) {
            DB::statement("DROP PROCEDURE IF EXISTS $procedure");
        }

        DB::statement('DROP FUNCTION IF EXISTS fn_update_penjualan_subtotal');

        // Stored Procedure untuk Role
        DB::statement('
            CREATE PROCEDURE sp_create_role(
                IN p_nama_role VARCHAR(255)
            )
            BEGIN
                INSERT INTO role (nama_role) 
                VALUES (p_nama_role);
                SELECT LAST_INSERT_ID() AS idrole;
            END
        ');

        // Stored Procedure untuk User
        DB::statement('
            CREATE PROCEDURE sp_create_user(
                IN p_username VARCHAR(255),
                IN p_password VARCHAR(255),
                IN p_idrole INT
            )
            BEGIN
                INSERT INTO users (username, password, idrole) 
                VALUES (p_username, p_password, p_idrole);
                SELECT LAST_INSERT_ID() AS iduser;
            END
        ');

        // Stored Procedure untuk Vendor
        DB::statement('
            CREATE PROCEDURE sp_create_vendor(
                IN p_nama_vendor VARCHAR(100),
                IN p_badan_hukum CHAR(1),
                IN p_status CHAR(1)
            )
            BEGIN
                INSERT INTO vendor (nama_vendor, badan_hukum, status) 
                VALUES (p_nama_vendor, p_badan_hukum, p_status);
                SELECT LAST_INSERT_ID() AS idvendor;
            END
        ');

        // Stored Procedure untuk Satuan
        DB::statement('
            CREATE PROCEDURE sp_create_satuan(
                IN p_nama_satuan VARCHAR(255),
                IN p_status INT
            )
            BEGIN
                INSERT INTO satuan (nama_satuan, status) 
                VALUES (p_nama_satuan, p_status);
                SELECT LAST_INSERT_ID() AS idsatuan;
            END
        ');

        // Stored Procedure untuk Barang
        DB::statement('
            CREATE PROCEDURE sp_create_barang(
                IN p_jenis CHAR(1),
                IN p_nama VARCHAR(100),
                IN p_status TINYINT,
                IN p_harga INT,
                IN p_idsatuan INT
            )
            BEGIN
                INSERT INTO barang (jenis, nama, status, harga, idsatuan)
                VALUES (p_jenis, p_nama, p_status, p_harga, p_idsatuan);
                SELECT LAST_INSERT_ID() AS idbarang;
            END
        ');

        // Stored Procedure untuk Pengadaan
        DB::statement('
        CREATE PROCEDURE sp_create_pengadaan(
            IN p_idvendor INT,
            IN p_ppn DECIMAL(5,2),
            IN p_iduser INT
        )
        BEGIN
            DECLARE v_idpengadaan INT;

            -- Insert pengadaan dengan status awal 0
            INSERT INTO pengadaan 
            (idvendor, subtotal_nilai, total_nilai, ppn, iduser, status, created_at,updateD_at) 
            VALUES 
            (p_idvendor, 0, 0, p_ppn, p_iduser, 0, NOW(),NOW());

            SET v_idpengadaan = LAST_INSERT_ID();
            
            -- Kembalikan ID pengadaan yang baru dibuat
            SELECT v_idpengadaan AS idpengadaan;
        END;
        ');

        // Stored Procedure untuk Detail Pengadaan
        DB::statement('
        CREATE PROCEDURE sp_create_detail_pengadaan(
            IN p_idpengadaan INT,
            IN p_idbarang INT,
            IN p_jumlah INT
        )
        BEGIN
            DECLARE v_harga_satuan DECIMAL(10,2);
            DECLARE v_sub_total DECIMAL(10,2);
            DECLARE v_total_subtotal DECIMAL(10,2);
            DECLARE v_total_nilai DECIMAL(10,2);
            DECLARE v_ppn DECIMAL(5,2);
            
            -- Ambil harga barang dari tabel barang
            SELECT harga INTO v_harga_satuan 
            FROM barang 
            WHERE idbarang = p_idbarang;
            
            -- Hitung subtotal
            SET v_sub_total = p_jumlah * v_harga_satuan;
            
            -- Insert ke detail pengadaan
            INSERT INTO detail_pengadaan 
            (idpengadaan, idbarang, harga_satuan, jumlah, sub_total) 
            VALUES 
            (p_idpengadaan, p_idbarang, v_harga_satuan, p_jumlah, v_sub_total);
            
            -- Hitung total subtotal untuk pengadaan ini
            SELECT SUM(sub_total), MAX(p.ppn) INTO v_total_subtotal, v_ppn
            FROM detail_pengadaan dp
            JOIN pengadaan p ON dp.idpengadaan = p.idpengadaan
            WHERE dp.idpengadaan = p_idpengadaan;
            
            -- Hitung total nilai termasuk PPN
            SET v_total_nilai = v_total_subtotal + (v_total_subtotal * (v_ppn/100));
            
            -- Update pengadaan dengan subtotal dan total nilai
            UPDATE pengadaan
            SET subtotal_nilai = v_total_subtotal,
                total_nilai = v_total_nilai
            WHERE idpengadaan = p_idpengadaan;
            
            -- Kembalikan ID detail pengadaan
            SELECT LAST_INSERT_ID() AS iddetail_pengadaan;
        END;
        ');

        // Stored Procedure untuk Penerimaan
        DB::statement('
CREATE PROCEDURE sp_approve_penerimaan(
    IN p_idpenerimaan INT
)
BEGIN
    DECLARE v_status CHAR(1);
    DECLARE v_idpengadaan INT;
    DECLARE v_ppn INT;
    
    -- Fetch idpengadaan based on penerimaan
    SELECT pg.idpengadaan 
    INTO v_idpengadaan
    FROM pengadaan pg
    JOIN penerimaan pn ON pg.idpengadaan = pn.idpengadaan
    WHERE pn.idpenerimaan = p_idpenerimaan;  -- Adjusted condition

    -- Fetch status from penerimaan
    SELECT status
    INTO v_status
    FROM penerimaan
    WHERE idpenerimaan = p_idpenerimaan;

    -- Check if already approved
    IF v_status = 1 THEN
        SIGNAL SQLSTATE "45000"
        SET MESSAGE_TEXT = "no ";
    END IF;

    -- Fetch ppn from pengadaan (assume single value for each pengadaan)
SELECT ppn
INTO v_ppn
FROM pengadaan
WHERE idpengadaan = v_idpengadaan
GROUP BY idpengadaan;  -- Menambahkan GROUP BY agar query valid


    -- Update penerimaan status to 1 (approved)
    UPDATE penerimaan 
    SET status = 1
    WHERE idpenerimaan = p_idpenerimaan;

    -- Update pengadaan status to 1 (approved)
    UPDATE pengadaan
    SET status = 1
    WHERE idpengadaan = v_idpengadaan;

END;

        ');

        // Stored Procedure untuk Detail Penerimaan
        DB::statement('
        CREATE PROCEDURE sp_create_detail_penerimaan(
            IN p_idpenerimaan INT,
            IN p_idbarang INT,
            IN p_jumlah_terima INT
        )
        BEGIN
            DECLARE v_harga_satuan DECIMAL(10,2);
            DECLARE v_sub_total BIGINT;

            -- Fetch harga_satuan from detail_pengadaan
            SELECT harga_satuan INTO v_harga_satuan 
            FROM detail_pengadaan 
            WHERE idbarang = p_idbarang
            LIMIT 1;
            
            -- Calculate sub_total
            SET v_sub_total = p_jumlah_terima * v_harga_satuan;
            
            -- Insert into detail_penerimaan
            INSERT INTO detail_penerimaan 
            (idpenerimaan, idbarang, harga_satuan, jumlah_terima, sub_total) 
            VALUES 
            (p_idpenerimaan, p_idbarang, v_harga_satuan, p_jumlah_terima, v_sub_total);
            
            SELECT LAST_INSERT_ID() AS iddetail_penerimaan;
        END;
        ');

        // Stored Procedure untuk Retur
        DB::statement('
CREATE PROCEDURE sp_create_retur(
    IN p_idpenerimaan INT,
    IN p_iduser INT
)
BEGIN
    DECLARE v_idretur INT;
    DECLARE v_existing_retur INT;
    DECLARE v_penerimaan_exists INT;
    DECLARE v_detail_penerimaan_exists INT;

    -- Cek apakah penerimaan dengan ID yang diberikan ada
    SELECT COUNT(*) INTO v_penerimaan_exists
    FROM penerimaan
    WHERE idpenerimaan = p_idpenerimaan;

    -- Cek apakah ada detail penerimaan untuk penerimaan ini
    SELECT COUNT(*) INTO v_detail_penerimaan_exists
    FROM detail_penerimaan
    WHERE idpenerimaan = p_idpenerimaan;

    -- Validasi penerimaan dan detail penerimaan
    IF v_penerimaan_exists = 0 THEN
        SIGNAL SQLSTATE "45000"
        SET MESSAGE_TEXT = "Penerimaan tidak ditemukan";
    END IF;

    IF v_detail_penerimaan_exists = 0 THEN
        SIGNAL SQLSTATE "45000"
        SET MESSAGE_TEXT = "Tidak ada detail penerimaan untuk penerimaan ini";
    END IF;

    -- Cek apakah sudah ada retur untuk penerimaan ini
    SELECT idretur INTO v_existing_retur
    FROM retur
    WHERE idpenerimaan = p_idpenerimaan
    LIMIT 1;

    -- Jika belum ada retur, buat baru dengan ID yang sama dengan penerimaan
    IF v_existing_retur IS NULL THEN
        -- Insert ke tabel retur menggunakan idpenerimaan sebagai idretur
        INSERT INTO retur (idretur, idpenerimaan, iduser, created_at)
        VALUES (p_idpenerimaan, p_idpenerimaan, p_iduser, NOW());

        SET v_idretur = p_idpenerimaan;
    ELSE
        -- Jika sudah ada, kembalikan idretur yang sudah ada
        SET v_idretur = v_existing_retur;
    END IF;

    -- Kembalikan ID retur
    SELECT v_idretur AS idretur;
END;
        ');

        // Stored Procedure untuk Detail Retur
        DB::statement('
CREATE PROCEDURE sp_create_detail_retur(
    IN p_idretur INT,
    IN p_jumlah INT,
    IN p_alasan VARCHAR(255)
)
BEGIN
    DECLARE v_iddetail_penerimaan INT;

    -- Ambil iddetail_penerimaan dari detail_penerimaan yang sesuai dengan retur
    SELECT iddetail_penerimaan INTO v_iddetail_penerimaan
    FROM detail_penerimaan dp
    JOIN penerimaan p ON dp.idpenerimaan = p.idpenerimaan
    WHERE p.idpenerimaan = p_idretur
    LIMIT 1;

    -- Insert ke detail_retur
    INSERT INTO detail_retur (
        idretur, 
        iddetail_penerimaan, 
        alasan, 
        jumlah
    ) 
    VALUES (
        p_idretur, 
        v_iddetail_penerimaan, 
        p_alasan, 
        p_jumlah
    );
    
    SELECT LAST_INSERT_ID() AS iddetail_retur;
END;
         ');

        // Stored Procedure untuk Margin Penjualan
        DB::statement('
            CREATE PROCEDURE sp_create_margin_penjualan(
                IN p_persen DECIMAL(5,2),
                IN p_status TINYINT
            )
            BEGIN
                INSERT INTO margin_penjualan 
                (persen,status, created_at, updated_at) 
                VALUES 
                (p_persen, p_status, NOW(), NOW());
                
                SELECT LAST_INSERT_ID() AS idmargin_penjualan;
            END
        ');

        // Stored Procedure untuk Penjualan
        DB::statement('
        CREATE PROCEDURE sp_create_penjualan(
            IN p_idmargin_penjualan INT,
            IN p_iduser INT
        )
        BEGIN
            DECLARE v_idpenjualan INT;
        
            # Insert penjualan dengan nilai awal 0
            INSERT INTO penjualan (
                subtotal_nilai, ppn, total_nilai, idmargin_penjualan, 
                iduser, created_at
            ) VALUES (
                0, 0, 0, p_idmargin_penjualan, p_iduser, NOW()
            );
            # Ambil ID penjualan yang baru saja dibuat
            SET v_idpenjualan = LAST_INSERT_ID();
            SELECT v_idpenjualan AS idpenjualan;
        END
        ');

        DB::statement('
        CREATE PROCEDURE sp_create_detail_penjualan(
            IN p_idpenjualan INT,
            IN p_idbarang INT,
            IN p_jumlah INT
        )
        BEGIN
            DECLARE v_harga_satuan INT;
            DECLARE v_stok_tersedia INT DEFAULT 0;
            DECLARE v_calculated_subtotal INT;
            DECLARE v_iddetail_penjualan INT;

            SELECT harga INTO v_harga_satuan 
                FROM barang 
                WHERE idbarang = p_idbarang;

            # Hitung subtotal berdasarkan jumlah * harga_satuan
            SET v_calculated_subtotal = p_jumlah * v_harga_satuan;
    
            # Cek stok barang - hitung stok tersedia dari kartu_stok
            SELECT COALESCE(SUM(masuk - keluar), 0) INTO v_stok_tersedia
            FROM kartu_stok 
            WHERE idbarang = p_idbarang;
    
            # Validasi stok
            IF p_jumlah > v_stok_tersedia THEN
                SIGNAL SQLSTATE "45000" 
                SET MESSAGE_TEXT = "Stok tidak mencukupi";
            END IF;
    
            # Insert detail penjualan
            INSERT INTO detail_penjualan (
                idpenjualan, idbarang, harga_satuan,jumlah,subtotal
            ) VALUES (
                p_idpenjualan, p_idbarang, v_harga_satuan, p_jumlah, v_calculated_subtotal
            );
            
        END;
        ');

    // Fungsi update subtotal penjualan

    }
};