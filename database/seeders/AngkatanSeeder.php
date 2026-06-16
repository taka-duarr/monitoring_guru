<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Angkatan;
use Illuminate\Support\Str;

class AngkatanSeeder extends Seeder
{
    public function run(): void
    {
        $angkatans = ['2022', '2023', '2024', '2025', '2026'];

        foreach ($angkatans as $a) {
            Angkatan::create([
                'id' => Str::uuid(),
                'name' => $a,
            ]);
        }
    }
}
