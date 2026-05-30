<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absen_murids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('absen_masuk_id');
            $table->uuid('murid_id');
            $table->string('status')->default('hadir'); // hadir, alpa, izin, sakit
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen_murids');
    }
};
