<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('izins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('jadwal_ajar_id')->nullable();
            $table->date('tanggal_izin');
            $table->string('jam_izin');
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->boolean('approval')->default(false);
            $table->boolean('read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('izins'); }
};