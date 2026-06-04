<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin / Kepala Sekolah
        User::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Sekolah',
            'nik' => '987654',
            'jabatan' => 'admin',
            'password' => Hash::make('987654'),
        ]);

        // Guru 1 (Pak Budi)
        User::create([
            'id' => Str::uuid(),
            'name' => 'Pak Budi',
            'nik' => '111111',
            'jabatan' => 'guru',
            'password' => Hash::make('111111'),
        ]);

        // Guru 2 (Bu Ani)
        User::create([
            'id' => Str::uuid(),
            'name' => 'Bu Ani',
            'nik' => '222222',
            'jabatan' => 'guru',
            'password' => Hash::make('222222'),
        ]);

        // Ketua Kelas 1 (Andi)
        User::create([
            'id' => Str::uuid(),
            'name' => 'Andi',
            'nik' => '888888',
            'jabatan' => 'ketuakelas',
            'password' => Hash::make('888888'),
            'kelas_id' => null,
        ]);

        // Ketua Kelas 2 (Siti)
        User::create([
            'id' => Str::uuid(),
            'name' => 'Siti',
            'nik' => '999999',
            'jabatan' => 'ketuakelas',
            'password' => Hash::make('999999'),
            'kelas_id' => null,
        ]);
    }
}
