<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;
use Illuminate\Support\Str;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            'Pendidikan Agama Islam', 'Pendidikan Pancasila dan Kewarganegaraan', 'Bahasa Indonesia',
            'Matematika', 'Bahasa Inggris', 'Sejarah Indonesia', 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
            'Seni Budaya', 'Pemrograman Dasar', 'Dasar Desain Grafis', 'Komputer dan Jaringan Dasar',
            'Sistem Komputer', 'Pemrograman Web dan Perangkat Bergerak', 'Basis Data',
            'Administrasi Infrastruktur Jaringan', 'Teknologi Layanan Jaringan'
        ];

        foreach ($mapels as $m) {
            Mapel::create([
                'id' => Str::uuid(),
                'name' => $m,
            ]);
        }
    }
}
