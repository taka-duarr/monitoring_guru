<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use Illuminate\Support\Str;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ruangan::create([
            'id' => Str::uuid(),
            'name' => 'Lab Komputer 1',
        ]);

        Ruangan::create([
            'id' => Str::uuid(),
            'name' => 'Lab Komputer 2',
        ]);
    }
}
