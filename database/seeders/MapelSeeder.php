<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;
use App\Models\Jurusan;
use Illuminate\Support\Str;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusanRPL = Jurusan::where('kode_jurusan', 'RPL')->first();
        $jurusanTKJ = Jurusan::where('kode_jurusan', 'TKJ')->first();

        Mapel::create([
            'id' => Str::uuid(),
            'name' => 'Pemrograman Web',
            'jurusan_id' => $jurusanRPL ? $jurusanRPL->id : null,
        ]);

        Mapel::create([
            'id' => Str::uuid(),
            'name' => 'Jaringan Dasar',
            'jurusan_id' => $jurusanTKJ ? $jurusanTKJ->id : null,
        ]);
    }
}
