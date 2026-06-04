<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Angkatan;
use Illuminate\Support\Str;

class AngkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Angkatan::create([
            'id' => Str::uuid(),
            'name' => '2024',
        ]);

        Angkatan::create([
            'id' => Str::uuid(),
            'name' => '2025',
        ]);

        Angkatan::create([
            'id' => Str::uuid(),
            'name' => '2026',
        ]);
    }
}
