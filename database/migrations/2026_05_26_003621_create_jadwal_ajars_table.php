<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_ajars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guru_id')->nullable();
            $table->uuid('mapel_id')->nullable();
            $table->uuid('kelas_id')->nullable();
            $table->uuid('ruangan_id')->nullable();
            $table->string('hari');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('last_editor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_ajars'); }
};