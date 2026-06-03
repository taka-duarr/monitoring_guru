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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->after('foto');
            }
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('users', 'no_telp')) {
                $table->string('no_telp')->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('users', 'status_kepegawaian')) {
                $table->string('status_kepegawaian')->nullable()->after('no_telp');
            }
            if (!Schema::hasColumn('users', 'golongan')) {
                $table->string('golongan')->nullable()->after('status_kepegawaian');
            }
            if (!Schema::hasColumn('users', 'tmt')) {
                $table->date('tmt')->nullable()->after('golongan');
            }
            if (!Schema::hasColumn('users', 'jumlah_jam')) {
                $table->integer('jumlah_jam')->default(0)->after('tmt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'no_telp',
                'status_kepegawaian',
                'golongan',
                'tmt',
                'jumlah_jam'
            ]);
        });
    }
};
