<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('status_kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kelas_id')->nullable();
            $table->string('mapel')->nullable();
            $table->string('pengajar')->nullable();
            $table->string('ruangan')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('status_kelas'); }
};