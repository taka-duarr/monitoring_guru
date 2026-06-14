<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hariInggris = \Carbon\Carbon::now()->format('l');
$mapHari = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];
$hariIni = $mapHari[$hariInggris];

echo "Hari Ini: " . $hariIni . "\n";

// Find any guru
$guru = \App\Models\User::where('jabatan', 'guru')->first();

if (!$guru) {
    die("No guru found\n");
}
echo "Testing for Guru: " . $guru->name . " (ID: " . $guru->id . ")\n";

// Let's manually inject a schedule for Minggu for this guru
$kelas = \App\Models\Kelas::where('is_active', true)->first();
$mapel = \App\Models\Mapel::first();

if (!$kelas || !$mapel) {
    die("Need active kelas and mapel\n");
}

$jadwal = \App\Models\JadwalAjar::create([
    'id' => (string) \Illuminate\Support\Str::uuid(),
    'guru_id' => $guru->id,
    'mapel_id' => $mapel->id,
    'kelas_id' => $kelas->id,
    'hari' => 'Minggu',
    'jam_mulai' => '07:00',
    'jam_selesai' => '08:00',
]);
echo "Created Jadwal ID: " . $jadwal->id . " for hari Minggu\n";

// Now run the query from GuruPortalController
$allJadwals = \App\Models\JadwalAjar::with(['mapel', 'kelas', 'ruangan'])
    ->where('guru_id', $guru->id)
    ->where('hari', $hariIni)
    ->whereHas('kelas', function($q) {
        $q->where('is_active', true);
    })
    ->orderBy('jam_mulai', 'asc')
    ->get();

echo "Jadwal count found: " . $allJadwals->count() . "\n";
foreach($allJadwals as $j) {
    echo "Found: " . $j->hari . " - " . $j->jam_mulai . " - Kelas Active: " . $j->kelas->is_active . "\n";
}
