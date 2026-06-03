<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_laporans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_laporan');           // "Rekap Kehadiran - Juni 2025"
            $table->string('jenis_laporan');          // rekap_kehadiran|perizinan|kelas_kosong|jadwal_ajar
            $table->json('parameter');                // filter yang digunakan (date range, guru_id, dll)
            $table->string('format');                 // pdf | excel
            $table->string('file_path');              // storage path
            $table->string('file_name');              // nama file untuk download
            $table->foreignUuid('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_laporans');
    }
};
