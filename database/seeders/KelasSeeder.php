<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Angkatan;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = Jurusan::all();
        $angkatans = Angkatan::orderBy('name', 'desc')->get(); // e.g. 2026, 2025, 2024
        
        $grades = ['10', '11', '12'];
        
        foreach ($jurusans as $jurusan) {
            foreach ($grades as $gradeIndex => $grade) {
                // If angkatans has enough entries, map grade 10 to angkatan 0 (newest), grade 11 to angkatan 1, etc.
                $angkatan = isset($angkatans[$gradeIndex]) ? $angkatans[$gradeIndex] : $angkatans->first();
                
                // Create 2 classes per grade per jurusan
                for ($i = 1; $i <= 2; $i++) {
                    $className = $jurusan->kode_jurusan . ' ' . $i;
                    
                    // Create a Ketua Kelas for this class
                    $nik = '99' . rand(1000, 9999);
                    $ketua = User::create([
                        'id' => Str::uuid(),
                        'name' => 'Ketua ' . $grade . ' ' . $className,
                        'nik' => $nik,
                        'jabatan' => 'ketuakelas',
                        'password' => Hash::make($nik),
                        'kelas_id' => null, // will be updated below
                    ]);
                    
                    $kelas = Kelas::create([
                        'id' => Str::uuid(),
                        'name' => $className,
                        'grade' => $grade,
                        'index' => $i,
                        'jurusan_id' => $jurusan->id,
                        'angkatan_id' => $angkatan->id,
                        'ketua_id' => $ketua->id,
                        'is_active' => true,
                    ]);
                    
                    $ketua->update(['kelas_id' => $kelas->id]);
                }
            }
        }
    }
}
