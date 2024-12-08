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
            'sp_update_penjualan_subtotal'
        ];

        foreach ($procedures as $procedure) {
            DB::statement("DROP PROCEDURE IF EXISTS $procedure");
        }

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
                IN p_iduser INT,
                IN p_status TINYINT
            )
            BEGIN
                DECLARE v_idpengadaan INT;

                INSERT INTO pengadaan 
                (idvendor, subtotal_nilai, total_nilai, ppn, iduser, status, created_at, updated_at) 
                VALUES 
                (p_idvendor, 0, 0, p_ppn, p_iduser, p_status, NOW(), NOW());

                SET v_idpengadaan = LAST_INSERT_ID();
                SELECT v_idpengadaan AS idpengadaan;
            END
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
                
                SELECT harga INTO v_harga_satuan 
                FROM barang 
                WHERE idbarang = p_idbarang;
                
                SET v_sub_total = p_jumlah * v_harga_satuan;
                
                INSERT INTO detail_pengadaan 
                (idpengadaan, idbarang, harga_satuan, jumlah, sub_total) 
                VALUES 
                (p_idpengadaan, p_idbarang, v_harga_satuan, p_jumlah, v_sub_total);
                
                SELECT LAST_INSERT_ID() AS iddetail_pengadaan;
            END
        ');

        // Stored Procedure untuk Penerimaan
        DB::statement('
            CREATE PROCEDURE sp_create_penerimaan(
                IN p_idpengadaan INT,
                IN p_status VARCHAR(1),
                IN p_iduser INT
            )
            BEGIN
                DECLARE v_idpenerimaan INT;

                INSERT INTO penerimaan (idpengadaan, status, created_at, iduser)
                VALUES (p_idpengadaan, p_status, NOW(), p_iduser);

                SET v_idpenerimaan = LAST_INSERT_ID();
                SELECT v_idpenerimaan AS idpenerimaan;
            END
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
                DECLARE v_sub_total DECIMAL(10,2);
                
                SELECT harga INTO v_harga_satuan 
                FROM barang 
                WHERE idbarang = p_idbarang;
                
                SET v_sub_total = p_jumlah_terima * v_harga_satuan;
                
                INSERT INTO detail_penerimaan 
                (idpenerimaan, idbarang, harga_satuan, jumlah_terima, sub_total) 
                VALUES 
                (p_idpenerimaan, p_idbarang, v_harga_satuan, p_jumlah_terima, v_sub_total);
                
                SELECT LAST_INSERT_ID() AS iddetail_penerimaan;
            END
        ');

        // Stored Procedure untuk Retur
        DB::statement('
            CREATE PROCEDURE sp_create_retur(
                IN p_idpenerimaan INT,
                IN p_iduser INT
            )
            BEGIN
                INSERT INTO retur (idpenerimaan, iduser, created_at)
                VALUES (p_idpenerimaan, p_iduser, NOW());
                SELECT LAST_INSERT_ID() AS idretur;
            END
        ');

        // Stored Procedure untuk Detail Retur
        DB::statement('
        CREATE PROCEDURE sp_create_detail_retur(
            IN p_idretur INT,
            IN p_iddetail_penerimaan INT,
            IN p_alasan VARCHAR(255),
            IN p_jumlah INT
        )
        BEGIN
            INSERT INTO detail_retur 
            (idretur, iddetail_penerimaan, alasan, jumlah) 
            VALUES 
            (p_idretur, p_iddetail_penerimaan, p_alasan, p_jumlah);
            
            SELECT LAST_INSERT_ID() AS iddetail_retur;
        END
         ');

        // Stored Procedure untuk Margin Penjualan
        DB::statement('
            CREATE PROCEDURE sp_create_margin_penjualan(
                IN p_persen DECIMAL(5,2)
            )
            BEGIN
                INSERT INTO margin_penjualan 
                (persen, created_at, updated_at) 
                VALUES 
                (p_persen, NOW(), NOW());
                
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
                subtotal_nilai, 
                ppn, 
                total_nilai, 
                idmargin_penjualan, 
                iduser, 
                created_at
            ) VALUES (
                0,  # subtotal_nilai awal 0 
                0,  # ppn awal 0
                0,  # total_nilai awal 0
                p_idmargin_penjualan, 
                p_iduser, 
                NOW()
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
            IN p_harga_satuan INT,
            IN p_jumlah INT,
            IN p_sub_total INT
        )
        BEGIN
            DECLARE v_stok_tersedia INT DEFAULT 0;
            DECLARE v_calculated_subtotal INT;
            DECLARE v_margin_rate DOUBLE;
            DECLARE v_ppn_value INT;
            DECLARE v_total_value INT;
            DECLARE v_current_subtotal INT;

            # Hitung subtotal berdasarkan jumlah * harga_satuan
            SET v_calculated_subtotal = p_jumlah * p_harga_satuan;

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
                idpenjualan, 
                idbarang, 
                harga_satuan, 
                jumlah, 
                subtotal
            ) VALUES (
                p_idpenjualan, 
                p_idbarang, 
                p_harga_satuan, 
                p_jumlah, 
                v_calculated_subtotal
            );

            # Hitung total subtotal dari detail penjualan
            SELECT COALESCE(SUM(subtotal), 0) INTO v_current_subtotal
            FROM detail_penjualan
            WHERE idpenjualan = p_idpenjualan;

            # Ambil margin rate
            SELECT persen INTO v_margin_rate
            FROM margin_penjualan m
            JOIN penjualan p ON p.idmargin_penjualan = m.idmargin_penjualan
            WHERE p.idpenjualan = p_idpenjualan;

            # Hitung PPN
            SET v_ppn_value = FLOOR(v_current_subtotal * (v_margin_rate / 100));
            SET v_total_value = v_current_subtotal + v_ppn_value;

            # Update penjualan tanpa trigger
            UPDATE penjualan 
            SET 
                subtotal_nilai = v_current_subtotal,
                ppn = v_ppn_value,
                total_nilai = v_total_value
            WHERE idpenjualan = p_idpenjualan;

            # Kembalikan ID detail penjualan
            SELECT LAST_INSERT_ID() AS iddetail_penjualan;
        END;
        ');

        DB::statement('
        CREATE PROCEDURE sp_update_penjualan_subtotal(
            IN p_idpenjualan INT
        )
        BEGIN
            DECLARE v_current_subtotal INT;
            DECLARE v_margin_rate DOUBLE;
            DECLARE v_ppn_value INT;
            DECLARE v_total_value INT;

            # Hitung total subtotal dari detail penjualan (jumlah * harga_satuan)
            SELECT COALESCE(SUM(jumlah * harga_satuan), 0) INTO v_current_subtotal
            FROM detail_penjualan
            WHERE idpenjualan = p_idpenjualan;

            # Ambil margin rate
            SELECT persen INTO v_margin_rate
            FROM margin_penjualan m
            JOIN penjualan p ON p.idmargin_penjualan = m.idmargin_penjualan
            WHERE p.idpenjualan = p_idpenjualan;

            # Hitung PPN
            SET v_ppn_value = FLOOR(v_current_subtotal * (v_margin_rate / 100));
            SET v_total_value = v_current_subtotal + v_ppn_value;

            # Update penjualan
            UPDATE penjualan 
            SET 
                subtotal_nilai = v_current_subtotal,
                ppn = v_ppn_value,
                total_nilai = v_total_value
            WHERE idpenjualan = p_idpenjualan;
        END;
        ');
    }
};