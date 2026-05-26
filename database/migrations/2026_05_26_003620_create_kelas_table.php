<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ketua_id')->nullable();
            $table->string('grade')->nullable();
            $table->integer('index')->nullable();
            $table->uuid('jurusan_id')->nullable();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};