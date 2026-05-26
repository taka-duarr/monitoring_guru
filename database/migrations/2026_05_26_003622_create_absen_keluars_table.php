<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('absen_keluars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('absen_masuk_id')->nullable();
            $table->string('jam_keluar');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('absen_keluars'); }
};