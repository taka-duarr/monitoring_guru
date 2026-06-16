<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use Illuminate\Support\Str;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Ruangan::create([
                'id' => Str::uuid(),
                'name' => 'Ruang Teori ' . str_pad($i, 2, '0', STR_PAD_LEFT),
            ]);
        }
        
        for ($i = 1; $i <= 5; $i++) {
            Ruangan::create([
                'id' => Str::uuid(),
                'name' => 'Lab Komputer ' . $i,
            ]);
        }
    }
}
