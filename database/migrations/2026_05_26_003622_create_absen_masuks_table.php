<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('absen_masuks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guru_id')->nullable();
            $table->uuid('jadwal_ajar_id')->nullable();
            $table->uuid('kelas_id')->nullable();
            $table->uuid('ruangan_id')->nullable();
            $table->date('tanggal');
            $table->string('jam_masuk');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('absen_masuks'); }
};