<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;
use Illuminate\Support\Str;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = [
            ['name' => 'Rekayasa Perangkat Lunak', 'kode' => 'RPL'],
            ['name' => 'Teknik Komputer dan Jaringan', 'kode' => 'TKJ'],
            ['name' => 'Desain Komunikasi Visual', 'kode' => 'DKV'],
            ['name' => 'Akuntansi dan Keuangan Lembaga', 'kode' => 'AKL'],
            ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'kode' => 'OTKP'],
        ];

        foreach ($jurusans as $j) {
            Jurusan::create([
                'id' => Str::uuid(),
                'name' => $j['name'],
                'kode_jurusan' => $j['kode'],
            ]);
        }
    }
}
