<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            UserSeeder::class,
            JurusanSeeder::class,
            AngkatanSeeder::class,
            KelasSeeder::class,
            MuridSeeder::class,
            MapelSeeder::class,
            RuanganSeeder::class,
            TahunAjaranSeeder::class,
            JadwalAjarSeeder::class,
            AbsensiSeeder::class,
            IzinSeeder::class,
        ]);

        echo "Database seeded successfully with all modularized seeders!\n";
    }
}

