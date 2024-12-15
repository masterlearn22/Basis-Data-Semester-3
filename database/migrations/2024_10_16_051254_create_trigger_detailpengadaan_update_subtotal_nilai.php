<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
        CREATE TRIGGER trg_after_insert_pengadaan 
        AFTER INSERT ON pengadaan
        FOR EACH ROW
        BEGIN
            -- Insert otomatis ke tabel penerimaan dengan status 0
            INSERT INTO penerimaan 
            (idpengadaan,iduser, status, created_at) 
            VALUES 
            (NEW.idpengadaan, NEW.iduser, 0, NOW());
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
        
        DB::statement('DROP TRIGGER IF EXISTS trg_after_insert_pengadaan ');
        DB::statement('DROP TRIGGER IF EXISTS update_pengadaan_status_trigger');
    }
};
