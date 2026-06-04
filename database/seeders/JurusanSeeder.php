<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use Illuminate\Support\Str;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::create([
            'id' => Str::uuid(),
            'name' => 'Rekayasa Perangkat Lunak',
            'kode_jurusan' => 'RPL',
        ]);

        Jurusan::create([
            'id' => Str::uuid(),
            'name' => 'Teknik Komputer dan Jaringan',
            'kode_jurusan' => 'TKJ',
        ]);
    }
}
