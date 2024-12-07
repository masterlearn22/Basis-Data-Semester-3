<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
       DB::statement('
CREATE TRIGGER after_penjualan_insert
AFTER INSERT ON penjualan
FOR EACH ROW
BEGIN
    DECLARE ppn_value INT;

    -- Ambil persen dari margin_penjualan
    SELECT persen INTO ppn_value
    FROM margin_penjualan
    WHERE idmargin_penjualan = NEW.idmargin_penjualan;

    -- Hitung total_nilai
    UPDATE penjualan
    SET total_nilai = NEW.subtotal_nilai + (NEW.subtotal_nilai * (ppn_value / 100))
    WHERE idpenjualan = NEW.idpenjualan;
END;
');

DB::statement('
CREATE TRIGGER after_penjualan_update
AFTER UPDATE ON penjualan
FOR EACH ROW
BEGIN
    DECLARE ppn_value INT;

    -- Ambil persen dari margin_penjualan jika idmargin_penjualan berubah
    SELECT persen INTO ppn_value
    FROM margin_penjualan
    WHERE idmargin_penjualan = NEW.idmargin_penjualan;

    -- Hitung ulang total_nilai
    UPDATE penjualan
    SET total_nilai = NEW.subtotal_nilai + (NEW.subtotal_nilai * (ppn_value / 100))
    WHERE idpenjualan = NEW.idpenjualan;
END;
');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppn_penjualan');
    }
};
