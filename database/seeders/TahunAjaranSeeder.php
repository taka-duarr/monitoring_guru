<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['tahun_mulai' => 2023, 'tahun_selesai' => 2024, 'semester' => 'Ganjil',  'is_active' => false],
            ['tahun_mulai' => 2023, 'tahun_selesai' => 2024, 'semester' => 'Genap',   'is_active' => false],
            ['tahun_mulai' => 2024, 'tahun_selesai' => 2025, 'semester' => 'Ganjil',  'is_active' => false],
            ['tahun_mulai' => 2024, 'tahun_selesai' => 2025, 'semester' => 'Genap',   'is_active' => true],
        ];

        foreach ($data as $row) {
            TahunAjaran::updateOrCreate(
                ['tahun_mulai' => $row['tahun_mulai'], 'semester' => $row['semester']],
                [
                    'id'            => (string) Str::uuid(),
                    'name'          => $row['tahun_mulai'] . '/' . $row['tahun_selesai'] . ' ' . $row['semester'],
                    'tahun_mulai'   => $row['tahun_mulai'],
                    'tahun_selesai' => $row['tahun_selesai'],
                    'semester'      => $row['semester'],
                    'is_active'     => $row['is_active'],
                ]
            );
        }
    }
}
