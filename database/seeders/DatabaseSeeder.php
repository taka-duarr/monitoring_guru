<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\KetuaKelas;
use App\Models\Mapel;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Gurus (termasuk Admin/Kepala Sekolah)
        $admin = Guru::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Sekolah',
            'nik' => '987654',
            'jabatan' => 'admin',
            'password' => Hash::make('987654'),
        ]);

        $guru1 = Guru::create([
            'id' => Str::uuid(),
            'name' => 'Pak Budi',
            'nik' => '111111',
            'jabatan' => 'guru',
            'password' => Hash::make('111111'),
        ]);

        $guru2 = Guru::create([
            'id' => Str::uuid(),
            'name' => 'Bu Ani',
            'nik' => '222222',
            'jabatan' => 'guru',
            'password' => Hash::make('222222'),
        ]);

        // 2. Jurusans
        $jurusanRPL = Jurusan::create([
            'id' => Str::uuid(),
            'name' => 'Rekayasa Perangkat Lunak',
            'kode_jurusan' => 'RPL',
        ]);

        $jurusanTKJ = Jurusan::create([
            'id' => Str::uuid(),
            'name' => 'Teknik Komputer dan Jaringan',
            'kode_jurusan' => 'TKJ',
        ]);

        // 3. Kelas & Ketua Kelas
        $ketua1 = KetuaKelas::create([
            'id' => Str::uuid(),
            'name' => 'Andi',
            'nisn' => '888888',
            'password' => Hash::make('888888'),
            'kelas_id' => null,
        ]);

        $ketua2 = KetuaKelas::create([
            'id' => Str::uuid(),
            'name' => 'Siti',
            'nisn' => '999999',
            'password' => Hash::make('999999'),
            'kelas_id' => null,
        ]);

        $kelas1 = Kelas::create([
            'id' => Str::uuid(),
            'name' => '10 RPL 1',
            'grade' => '10',
            'index' => 1,
            'jurusan_id' => $jurusanRPL->id,
            'ketua_id' => $ketua1->id,
            'is_active' => true,
        ]);
        $ketua1->update(['kelas_id' => $kelas1->id]);

        $kelas2 = Kelas::create([
            'id' => Str::uuid(),
            'name' => '10 TKJ 1',
            'grade' => '10',
            'index' => 1,
            'jurusan_id' => $jurusanTKJ->id,
            'ketua_id' => $ketua2->id,
            'is_active' => false,
        ]);
        $ketua2->update(['kelas_id' => $kelas2->id]);

        // 4. Mapel
        $mapel1 = Mapel::create([
            'id' => Str::uuid(),
            'name' => 'Pemrograman Web',
            'jurusan_id' => $jurusanRPL->id,
        ]);

        $mapel2 = Mapel::create([
            'id' => Str::uuid(),
            'name' => 'Jaringan Dasar',
            'jurusan_id' => $jurusanTKJ->id,
        ]);

        // 5. Ruangan
        $ruangan1 = Ruangan::create([
            'id' => Str::uuid(),
            'name' => 'Lab Komputer 1',
        ]);

        $ruangan2 = Ruangan::create([
            'id' => Str::uuid(),
            'name' => 'Lab Komputer 2',
        ]);
        
        echo "Database seeded successfully!\n";
    }
}
