<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAjar;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;

class JadwalAjarSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = User::where('jabatan', 'guru')->get();
        $mapels = Mapel::all();
        $kelass = Kelas::all();
        $ruangans = Ruangan::all();
        $tahunAjarans = TahunAjaran::all();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jams = [
            ['07:00', '08:30'],
            ['08:30', '10:00'],
            ['10:15', '11:45'],
            ['12:30', '14:00'],
            ['14:00', '15:30']
        ];

        foreach ($tahunAjarans as $ta) {
            foreach ($gurus as $guru) {
                // Generate 5 random schedules per guru per TA (1 per day)
                foreach ($hariList as $hari) {
                    // Randomly skip some days to make it realistic
                    if (rand(1, 10) > 7) continue;

                    $jam = $jams[array_rand($jams)];
                    $mapel = $mapels->random();
                    $kelas = $kelass->random();
                    $ruangan = $ruangans->random();

                    JadwalAjar::create([
                        'id' => Str::uuid(),
                        'guru_id' => $guru->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'ruangan_id' => $ruangan->id,
                        'tahun_ajaran_id' => $ta->id,
                        'hari' => $hari,
                        'jam_mulai' => $jam[0],
                        'jam_selesai' => $jam[1],
                    ]);
                }
            }
        }
    }
}
