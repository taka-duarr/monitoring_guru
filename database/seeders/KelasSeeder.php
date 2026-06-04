<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Angkatan;
use Illuminate\Support\Str;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusanRPL = Jurusan::where('kode_jurusan', 'RPL')->first();
        $jurusanTKJ = Jurusan::where('kode_jurusan', 'TKJ')->first();
        
        $angkatan2025 = Angkatan::where('name', '2025')->first();
        $angkatan2026 = Angkatan::where('name', '2026')->first();
        
        $ketua1 = User::where('name', 'Andi')->first();
        $ketua2 = User::where('name', 'Siti')->first();

        $kelas1 = Kelas::create([
            'id' => Str::uuid(),
            'name' => '10 RPL 1',
            'grade' => '10',
            'index' => 1,
            'jurusan_id' => $jurusanRPL ? $jurusanRPL->id : null,
            'angkatan_id' => $angkatan2025 ? $angkatan2025->id : null,
            'ketua_id' => $ketua1 ? $ketua1->id : null,
            'is_active' => true,
        ]);
        
        if ($ketua1) {
            $ketua1->update(['kelas_id' => $kelas1->id]);
        }

        $kelas2 = Kelas::create([
            'id' => Str::uuid(),
            'name' => '10 TKJ 1',
            'grade' => '10',
            'index' => 1,
            'jurusan_id' => $jurusanTKJ ? $jurusanTKJ->id : null,
            'angkatan_id' => $angkatan2026 ? $angkatan2026->id : null,
            'ketua_id' => $ketua2 ? $ketua2->id : null,
            'is_active' => false,
        ]);
        
        if ($ketua2) {
            $ketua2->update(['kelas_id' => $kelas2->id]);
        }
    }
}
