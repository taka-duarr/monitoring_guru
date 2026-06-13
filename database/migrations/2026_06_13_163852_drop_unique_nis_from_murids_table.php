<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('murids', function (Blueprint $table) {
            $table->dropUnique('murids_nis_unique');
        });
        
        // Use raw SQL to modify enum since Doctrine DBAL might not be installed
        DB::statement("ALTER TABLE murids MODIFY COLUMN status ENUM('aktif', 'lulus', 'pindah', 'keluar', 'naik_kelas') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE murids MODIFY COLUMN status ENUM('aktif', 'lulus', 'pindah', 'keluar') DEFAULT 'aktif'");
        
        Schema::table('murids', function (Blueprint $table) {
            $table->unique('nis');
        });
    }
};
