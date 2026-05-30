<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MuridSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasIds = \App\Models\Kelas::pluck('id');
        
        $nis = 1000;
        foreach ($kelasIds as $kelasId) {
            for ($i = 1; $i <= 10; $i++) {
                \App\Models\Murid::create([
                    'kelas_id' => $kelasId,
                    'nis' => (string)$nis++,
                    'name' => 'Siswa Dummy ' . $i . ' Kelas ' . substr($kelasId, 0, 4),
                    'no_absen' => $i
                ]);
            }
        }
    }
}
