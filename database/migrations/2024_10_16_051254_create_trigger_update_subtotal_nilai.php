<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerUpdateSubtotalNilai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
CREATE TRIGGER update_subtotal_nilai_after_insert
AFTER INSERT ON detail_pengadaan
FOR EACH ROW
BEGIN
    DECLARE new_subtotal_nilai INT;
    DECLARE ppn_value INT;

    -- Hitung subtotal_nilai berdasarkan idpengadaan yang relevan
    SELECT SUM(sub_total) INTO new_subtotal_nilai
    FROM detail_pengadaan
    WHERE idpengadaan = NEW.idpengadaan;

    -- Ambil nilai PPN yang ada di pengadaan
    SELECT ppn INTO ppn_value
    FROM pengadaan
    WHERE idpengadaan = NEW.idpengadaan;

    -- Update subtotal_nilai dan total_nilai di pengadaan
    UPDATE pengadaan
    SET subtotal_nilai = new_subtotal_nilai,
        total_nilai = new_subtotal_nilai + ((ppn_value/100) * new_subtotal_nilai),
        updated_at = NOW()
    WHERE idpengadaan = NEW.idpengadaan;
END;

        ');

        DB::statement('
CREATE TRIGGER update_subtotal_nilai_after_update
AFTER UPDATE ON detail_pengadaan
FOR EACH ROW
BEGIN
    DECLARE new_subtotal_nilai INT;
    DECLARE ppn_value INT;

    -- Hitung subtotal_nilai berdasarkan idpengadaan yang relevan
    SELECT SUM(sub_total) INTO new_subtotal_nilai
    FROM detail_pengadaan
    WHERE idpengadaan = NEW.idpengadaan;

    -- Ambil nilai PPN yang ada di pengadaan
    SELECT ppn INTO ppn_value
    FROM pengadaan
    WHERE idpengadaan = NEW.idpengadaan;

    -- Update subtotal_nilai dan total_nilai di pengadaan
    UPDATE pengadaan
    SET subtotal_nilai = new_subtotal_nilai,
        total_nilai = new_subtotal_nilai + ((ppn_value/100) * new_subtotal_nilai),
        updated_at = NOW()
    WHERE idpengadaan = NEW.idpengadaan;
END;

        ');

        DB::statement('
CREATE TRIGGER update_subtotal_nilai_after_delete
AFTER DELETE ON detail_pengadaan
FOR EACH ROW
BEGIN
    DECLARE new_subtotal_nilai INT;
    DECLARE ppn_value INT;

    -- Hitung subtotal_nilai berdasarkan idpengadaan yang relevan
    SELECT SUM(sub_total) INTO new_subtotal_nilai
    FROM detail_pengadaan
    WHERE idpengadaan = OLD.idpengadaan;

    -- Ambil nilai PPN yang ada di pengadaan
    SELECT ppn INTO ppn_value
    FROM pengadaan
    WHERE idpengadaan = OLD.idpengadaan;

    -- Update subtotal_nilai dan total_nilai di pengadaan
    UPDATE pengadaan
    SET subtotal_nilai = new_subtotal_nilai,
        total_nilai = new_subtotal_nilai + ((ppn_value/100) * new_subtotal_nilai),
        updated_at = NOW()
    WHERE idpengadaan = OLD.idpengadaan;
END;

        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TRIGGER IF EXISTS update_subtotal_nilai_after_insert');
        DB::statement('DROP TRIGGER IF EXISTS update_subtotal_nilai_after_update');
        DB::statement('DROP TRIGGER IF EXISTS update_subtotal_nilai_after_delete');
    }
}
