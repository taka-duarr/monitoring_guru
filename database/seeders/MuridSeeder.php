<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Murid;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        $kelasIds = Kelas::pluck('id');
        $nis = 10000;
        
        $firstNames = ['Adi', 'Budi', 'Citra', 'Dina', 'Eko', 'Fajar', 'Gita', 'Hadi', 'Indra', 'Joko', 'Kiki', 'Lina', 'Maman', 'Nina', 'Opan'];
        $lastNames = ['Saputra', 'Wijaya', 'Pratama', 'Lestari', 'Sari', 'Nugroho', 'Kusuma', 'Ramadhan', 'Hidayat', 'Putri'];

        foreach ($kelasIds as $kelasId) {
            for ($i = 1; $i <= 10; $i++) {
                $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
                Murid::create([
                    'kelas_id' => $kelasId,
                    'nis' => (string)$nis++,
                    'name' => $name,
                    'no_absen' => $i
                ]);
            }
        }
    }
}
