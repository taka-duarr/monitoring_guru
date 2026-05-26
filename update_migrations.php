<?php

$dir = __DIR__ . '/database/migrations/';

function updateMigration($dir, $pattern, $content) {
    $files = glob($dir . $pattern);
    if (!empty($files)) {
        file_put_contents($files[0], $content);
        echo "Updated: " . basename($files[0]) . "\n";
    } else {
        echo "Not found: " . $pattern . "\n";
    }
}

// 1. Gurus
updateMigration($dir, '*_create_gurus_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('gurus', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('name');
            \$table->string('nip')->unique();
            \$table->string('jabatan');
            \$table->string('password');
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('gurus'); }
};
EOT
);

// 2. Jurusans
updateMigration($dir, '*_create_jurusans_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('jurusans', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('name');
            \$table->string('kode_jurusan')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('jurusans'); }
};
EOT
);

// 3. Ketua Kelas
updateMigration($dir, '*_create_ketua_kelas_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ketua_kelas', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('name');
            \$table->string('nisn')->unique();
            \$table->string('password');
            \$table->uuid('kelas_id')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('ketua_kelas'); }
};
EOT
);

// 4. Kelas
updateMigration($dir, '*_create_kelas_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('ketua_id')->nullable();
            \$table->string('grade')->nullable();
            \$table->integer('index')->nullable();
            \$table->uuid('jurusan_id')->nullable();
            \$table->string('name');
            \$table->boolean('is_active')->default(false);
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};
EOT
);

// 5. Mapels
updateMigration($dir, '*_create_mapels_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mapels', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('jurusan_id')->nullable();
            \$table->string('name');
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('mapels'); }
};
EOT
);

// 6. Ruangans
updateMigration($dir, '*_create_ruangans_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ruangans', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('name');
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('ruangans'); }
};
EOT
);

// 7. Jadwal Ajars
updateMigration($dir, '*_create_jadwal_ajars_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_ajars', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('guru_id')->nullable();
            \$table->uuid('mapel_id')->nullable();
            \$table->uuid('kelas_id')->nullable();
            \$table->uuid('ruangan_id')->nullable();
            \$table->string('hari');
            \$table->string('jam_mulai');
            \$table->string('jam_selesai');
            \$table->string('last_editor')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_ajars'); }
};
EOT
);

// 8. Status Kelas
updateMigration($dir, '*_create_status_kelas_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('status_kelas', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('kelas_id')->nullable();
            \$table->string('mapel')->nullable();
            \$table->string('pengajar')->nullable();
            \$table->string('ruangan')->nullable();
            \$table->boolean('is_active')->default(false);
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('status_kelas'); }
};
EOT
);

// 9. Absen Masuks
updateMigration($dir, '*_create_absen_masuks_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('absen_masuks', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('guru_id')->nullable();
            \$table->uuid('jadwal_ajar_id')->nullable();
            \$table->uuid('kelas_id')->nullable();
            \$table->uuid('ruangan_id')->nullable();
            \$table->date('tanggal');
            \$table->string('jam_masuk');
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('absen_masuks'); }
};
EOT
);

// 10. Absen Keluars
updateMigration($dir, '*_create_absen_keluars_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('absen_keluars', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('absen_masuk_id')->nullable();
            \$table->string('jam_keluar');
            \$table->string('status')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('absen_keluars'); }
};
EOT
);

// 11. Izins
updateMigration($dir, '*_create_izins_table.php', <<<EOT
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('izins', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->uuid('jadwal_ajar_id')->nullable();
            \$table->date('tanggal_izin');
            \$table->string('jam_izin');
            \$table->string('judul');
            \$table->text('pesan')->nullable();
            \$table->boolean('approval')->default(false);
            \$table->boolean('read')->default(false);
            \$table->timestamps();
            \$table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('izins'); }
};
EOT
);

echo "All migrations updated successfully.\n";
