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
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'angkatan')) {
                $table->dropColumn('angkatan');
            }
            if (!Schema::hasColumn('kelas', 'angkatan_id')) {
                $table->foreignUuid('angkatan_id')->nullable()->after('name')->constrained('angkatans')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['angkatan_id']);
            $table->dropColumn('angkatan_id');
            $table->string('angkatan')->nullable()->after('name');
        });
    }
};
