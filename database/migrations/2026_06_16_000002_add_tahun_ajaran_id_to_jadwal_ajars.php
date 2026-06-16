<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('jadwal_ajars', function (Blueprint $table) {
            $table->uuid('tahun_ajaran_id')->nullable()->after('guru_id');
        });

        // Isi tahun_ajaran_id dari tahun ajaran yang aktif untuk semua jadwal lama
        $aktif = DB::table('tahun_ajarans')->where('is_active', true)->first();
        if ($aktif) {
            DB::table('jadwal_ajars')->whereNull('tahun_ajaran_id')->update([
                'tahun_ajaran_id' => $aktif->id,
            ]);
        }
    }
    public function down(): void {
        Schema::table('jadwal_ajars', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
