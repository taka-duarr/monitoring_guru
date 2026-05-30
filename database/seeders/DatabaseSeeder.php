<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
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
        // 1. Users (Admin, Guru, Ketua Kelas) - semua di tabel gurus
        $admin = User::create([
            'id'      => Str::uuid(),
            'name'    => 'Kepala Sekolah',
            'nik'     => '987654',
            'jabatan' => 'admin',
            'password' => Hash::make('987654'),
        ]);

        $guru1 = User::create([
            'id'      => Str::uuid(),
            'name'    => 'Pak Budi',
            'nik'     => '111111',
            'jabatan' => 'guru',
            'password' => Hash::make('111111'),
        ]);

        $guru2 = User::create([
            'id'      => Str::uuid(),
            'name'    => 'Bu Ani',
            'nik'     => '222222',
            'jabatan' => 'guru',
            'password' => Hash::make('222222'),
        ]);

        // 2. Jurusans
        $jurusanRPL = Jurusan::create([
            'id'           => Str::uuid(),
            'name'         => 'Rekayasa Perangkat Lunak',
            'kode_jurusan' => 'RPL',
        ]);

        $jurusanTKJ = Jurusan::create([
            'id'           => Str::uuid(),
            'name'         => 'Teknik Komputer dan Jaringan',
            'kode_jurusan' => 'TKJ',
        ]);

        // 3. Ketua Kelas (sekarang di tabel gurus, jabatan = 'ketuakelas')
        $ketua1 = User::create([
            'id'       => Str::uuid(),
            'name'     => 'Andi',
            'nik'      => '888888',
            'jabatan'  => 'ketuakelas',
            'password' => Hash::make('888888'),
            'kelas_id' => null,
        ]);

        $ketua2 = User::create([
            'id'       => Str::uuid(),
            'name'     => 'Siti',
            'nik'      => '999999',
            'jabatan'  => 'ketuakelas',
            'password' => Hash::make('999999'),
            'kelas_id' => null,
        ]);

        $kelas1 = Kelas::create([
            'id'         => Str::uuid(),
            'name'       => '10 RPL 1',
            'grade'      => '10',
            'index'      => 1,
            'jurusan_id' => $jurusanRPL->id,
            'ketua_id'   => $ketua1->id,
            'is_active'  => true,
        ]);
        $ketua1->update(['kelas_id' => $kelas1->id]);

        $kelas2 = Kelas::create([
            'id'         => Str::uuid(),
            'name'       => '10 TKJ 1',
            'grade'      => '10',
            'index'      => 1,
            'jurusan_id' => $jurusanTKJ->id,
            'ketua_id'   => $ketua2->id,
            'is_active'  => false,
        ]);
        $ketua2->update(['kelas_id' => $kelas2->id]);

        // 4. Mapel
        $mapel1 = Mapel::create([
            'id'         => Str::uuid(),
            'name'       => 'Pemrograman Web',
            'jurusan_id' => $jurusanRPL->id,
        ]);

        $mapel2 = Mapel::create([
            'id'         => Str::uuid(),
            'name'       => 'Jaringan Dasar',
            'jurusan_id' => $jurusanTKJ->id,
        ]);

        // 5. Ruangan
        $ruangan1 = Ruangan::create([
            'id'   => Str::uuid(),
            'name' => 'Lab Komputer 1',
        ]);

        $ruangan2 = Ruangan::create([
            'id'   => Str::uuid(),
            'name' => 'Lab Komputer 2',
        ]);
        
        // 6. Jadwal Ajar (Sabtu & Minggu)
        \App\Models\JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru1->id,
            'mapel_id'   => $mapel1->id,
            'kelas_id'   => $kelas1->id,
            'ruangan_id' => $ruangan1->id,
            'hari'       => 'Sabtu',
            'jam_mulai'  => '08:00',
            'jam_selesai' => '10:00',
        ]);

        \App\Models\JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru2->id,
            'mapel_id'   => $mapel2->id,
            'kelas_id'   => $kelas2->id,
            'ruangan_id' => $ruangan2->id,
            'hari'       => 'Minggu',
            'jam_mulai'  => '10:00',
            'jam_selesai' => '12:00',
        ]);

        echo "Database seeded successfully!\n";
    }
}
