<?php

$dir = __DIR__ . '/app/Models/';

function updateModel($file, $className, $isAuth = false, $relations = "") {
    if (!file_exists($file)) {
        echo "File not found: " . $file . "\n";
        return;
    }
    
    $extends = $isAuth ? "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse Filament\Models\Contracts\FilamentUser;\nuse Filament\Panel;\n\nclass {$className} extends Authenticatable implements FilamentUser" : "use Illuminate\Database\Eloquent\Model;\n\nclass {$className} extends Model";
    $authMethods = $isAuth ? "\n    public function canAccessPanel(Panel \$panel): bool { return \$this->jabatan === 'kepala_sekolah'; }\n" : "";

    $content = <<<EOT
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
{$extends}
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected \$guarded = [];
{$authMethods}
{$relations}
}
EOT;

    file_put_contents($file, $content);
    echo "Updated: {$className}\n";
}

updateModel($dir . 'Guru.php', 'Guru', true, <<<EOT
    public function jadwalAjars() { return \$this->hasMany(JadwalAjar::class, 'guru_id'); }
EOT
);
updateModel($dir . 'KetuaKelas.php', 'KetuaKelas', true, <<<EOT
    public function kelas() { return \$this->belongsTo(Kelas::class, 'kelas_id'); }
EOT
);
updateModel($dir . 'Jurusan.php', 'Jurusan', false, <<<EOT
    public function mapels() { return \$this->hasMany(Mapel::class, 'jurusan_id'); }
    public function kelas() { return \$this->hasMany(Kelas::class, 'jurusan_id'); }
EOT
);
updateModel($dir . 'Kelas.php', 'Kelas', false, <<<EOT
    public function jurusan() { return \$this->belongsTo(Jurusan::class, 'jurusan_id'); }
    public function ketua() { return \$this->belongsTo(KetuaKelas::class, 'ketua_id'); }
    public function statusKelas() { return \$this->hasOne(StatusKelas::class, 'kelas_id'); }
EOT
);
updateModel($dir . 'Mapel.php', 'Mapel', false, <<<EOT
    public function jurusan() { return \$this->belongsTo(Jurusan::class, 'jurusan_id'); }
EOT
);
updateModel($dir . 'Ruangan.php', 'Ruangan', false, <<<EOT
EOT
);
updateModel($dir . 'JadwalAjar.php', 'JadwalAjar', false, <<<EOT
    public function guru() { return \$this->belongsTo(Guru::class, 'guru_id'); }
    public function mapel() { return \$this->belongsTo(Mapel::class, 'mapel_id'); }
    public function kelas() { return \$this->belongsTo(Kelas::class, 'kelas_id'); }
    public function ruangan() { return \$this->belongsTo(Ruangan::class, 'ruangan_id'); }
    public function absenMasuks() { return \$this->hasMany(AbsenMasuk::class, 'jadwal_ajar_id'); }
    public function izins() { return \$this->hasMany(Izin::class, 'jadwal_ajar_id'); }
EOT
);
updateModel($dir . 'StatusKelas.php', 'StatusKelas', false, <<<EOT
    public function kelas() { return \$this->belongsTo(Kelas::class, 'kelas_id'); }
EOT
);
updateModel($dir . 'AbsenMasuk.php', 'AbsenMasuk', false, <<<EOT
    public function guru() { return \$this->belongsTo(Guru::class, 'guru_id'); }
    public function jadwalAjar() { return \$this->belongsTo(JadwalAjar::class, 'jadwal_ajar_id'); }
    public function kelas() { return \$this->belongsTo(Kelas::class, 'kelas_id'); }
    public function ruangan() { return \$this->belongsTo(Ruangan::class, 'ruangan_id'); }
    public function absenKeluar() { return \$this->hasOne(AbsenKeluar::class, 'absen_masuk_id'); }
EOT
);
updateModel($dir . 'AbsenKeluar.php', 'AbsenKeluar', false, <<<EOT
    public function absenMasuk() { return \$this->belongsTo(AbsenMasuk::class, 'absen_masuk_id'); }
EOT
);
updateModel($dir . 'Izin.php', 'Izin', false, <<<EOT
    public function jadwalAjar() { return \$this->belongsTo(JadwalAjar::class, 'jadwal_ajar_id'); }
EOT
);

echo "All models updated successfully.\n";
