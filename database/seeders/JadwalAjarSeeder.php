<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAjar;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\AbsenMasuk;
use App\Models\AbsenKeluar;
use App\Models\StatusKelas;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JadwalAjarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guru1 = User::where('name', 'Pak Budi')->first();
        $guru2 = User::where('name', 'Bu Ani')->first();

        $mapel1 = Mapel::where('name', 'Pemrograman Web')->first();
        $mapel2 = Mapel::where('name', 'Jaringan Dasar')->first();

        $kelas1 = Kelas::where('name', '10 RPL 1')->first();
        $kelas2 = Kelas::where('name', '10 TKJ 1')->first();

        $ruangan1 = Ruangan::where('name', 'Lab Komputer 1')->first();
        $ruangan2 = Ruangan::where('name', 'Lab Komputer 2')->first();

        if (!$guru1 || !$kelas1 || !$mapel1 || !$ruangan1) {
            return;
        }

        $currentDay = Carbon::now()->locale('id')->isoFormat('dddd');
        $todayStr = now()->toDateString();

        // 6a. Jadwal 1 (Sudah Selesai): 07:00 - 09:00
        $jadwalA = JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru1->id,
            'mapel_id'   => $mapel1->id,
            'kelas_id'   => $kelas1->id,
            'ruangan_id' => $ruangan1->id,
            'hari'       => $currentDay,
            'jam_mulai'  => '07:00',
            'jam_selesai'=> '09:00',
        ]);

        $absenMasukA = AbsenMasuk::create([
            'id'             => Str::uuid(),
            'guru_id'        => $guru1->id,
            'kelas_id'       => $kelas1->id,
            'jadwal_ajar_id' => $jadwalA->id,
            'tanggal'        => $todayStr,
            'jam_masuk'      => '07:05:00',
        ]);

        AbsenKeluar::create([
            'id'             => Str::uuid(),
            'absen_masuk_id' => $absenMasukA->id,
            'jam_keluar'     => '09:00:00',
            'status'         => 'selesai',
        ]);

        // 6b. Jadwal 2 (Sedang Belajar): 10:00 - 12:00
        $jadwalB = JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru1->id,
            'mapel_id'   => $mapel1->id,
            'kelas_id'   => $kelas1->id,
            'ruangan_id' => $ruangan1->id,
            'hari'       => $currentDay,
            'jam_mulai'  => '10:00',
            'jam_selesai'=> '12:00',
        ]);

        AbsenMasuk::create([
            'id'             => Str::uuid(),
            'guru_id'        => $guru1->id,
            'kelas_id'       => $kelas1->id,
            'jadwal_ajar_id' => $jadwalB->id,
            'tanggal'        => $todayStr,
            'jam_masuk'      => '10:02:00',
        ]);

        StatusKelas::create([
            'id'        => Str::uuid(),
            'kelas_id'  => $kelas1->id,
            'mapel'     => $mapel1->name,
            'pengajar'  => $guru1->name,
            'ruangan'   => $ruangan1->name,
            'is_active' => true,
        ]);

        // 6c. Jadwal 3 (Belum Mulai / Locked): Kelas di masa depan hari ini
        $nowHour = (int) now()->format('H');
        $futureHourStart = $nowHour + 1;
        $futureHourEnd = $nowHour + 3;
        
        $jamMulaiFuture = str_pad($futureHourStart, 2, '0', STR_PAD_LEFT) . ':00';
        $jamSelesaiFuture = str_pad($futureHourEnd, 2, '0', STR_PAD_LEFT) . ':00';

        JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru1->id,
            'mapel_id'   => $mapel1->id,
            'kelas_id'   => $kelas1->id,
            'ruangan_id' => $ruangan1->id,
            'hari'       => $currentDay,
            'jam_mulai'  => $jamMulaiFuture,
            'jam_selesai'=> $jamSelesaiFuture,
        ]);

        // 6d. Jadwal cadangan Sabtu/Minggu (seperti data lama)
        JadwalAjar::create([
            'id'         => Str::uuid(),
            'guru_id'    => $guru1->id,
            'mapel_id'   => $mapel1->id,
            'kelas_id'   => $kelas1->id,
            'ruangan_id' => $ruangan1->id,
            'hari'       => 'Sabtu',
            'jam_mulai'  => '08:00',
            'jam_selesai'=> '10:00',
        ]);

        if ($guru2 && $kelas2 && $mapel2 && $ruangan2) {
            JadwalAjar::create([
                'id'         => Str::uuid(),
                'guru_id'    => $guru2->id,
                'mapel_id'   => $mapel2->id,
                'kelas_id'   => $kelas2->id,
                'ruangan_id' => $ruangan2->id,
                'hari'       => 'Minggu',
                'jam_mulai'  => '10:00',
                'jam_selesai'=> '12:00',
            ]);
        }
    }
}
