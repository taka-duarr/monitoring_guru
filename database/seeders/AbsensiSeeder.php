<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalAjar;
use App\Models\AbsenMasuk;
use App\Models\AbsenKeluar;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $jadwals = JadwalAjar::all();
        
        $mapHariEng = [
            'Senin' => 'Monday',
            'Selasa' => 'Tuesday',
            'Rabu' => 'Wednesday',
            'Kamis' => 'Thursday',
            'Jumat' => 'Friday',
            'Sabtu' => 'Saturday',
            'Minggu' => 'Sunday',
        ];

        foreach ($jadwals as $jadwal) {
            $hariEng = $mapHariEng[$jadwal->hari];
            
            // Generate 3 random past meetings
            for ($i = 1; $i <= 3; $i++) {
                // Random weeks ago
                $weeksAgo = rand(1, 12);
                $tanggal = Carbon::now()->subWeeks($weeksAgo)->next($hariEng)->toDateString();
                
                // Avoid duplicate dates
                if (AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)->where('tanggal', $tanggal)->exists()) {
                    continue;
                }

                $isLats = rand(1, 10) > 8; // 20% chance telat
                $jamMulai = Carbon::createFromFormat('H:i', substr($jadwal->jam_mulai, 0, 5));
                
                if ($isLats) {
                    $jamMasuk = $jamMulai->addMinutes(rand(5, 30))->format('H:i');
                } else {
                    $jamMasuk = $jamMulai->subMinutes(rand(0, 15))->format('H:i');
                }

                $absenMasuk = AbsenMasuk::create([
                    'id' => Str::uuid(),
                    'jadwal_ajar_id' => $jadwal->id,
                    'guru_id' => $jadwal->guru_id,
                    'kelas_id' => $jadwal->kelas_id,
                    'ruangan_id' => $jadwal->ruangan_id,
                    'tanggal' => $tanggal,
                    'jam_masuk' => $jamMasuk,
                ]);

                // 90% chance to have AbsenKeluar
                if (rand(1, 10) <= 9) {
                    $jamSelesai = Carbon::createFromFormat('H:i', substr($jadwal->jam_selesai, 0, 5));
                    $jamKeluar = $jamSelesai->addMinutes(rand(0, 20))->format('H:i');
                    
                    AbsenKeluar::create([
                        'id' => Str::uuid(),
                        'absen_masuk_id' => $absenMasuk->id,
                        'jam_keluar' => $jamKeluar,
                        'status' => 'selesai',
                    ]);
                }
            }
            
            // Generate a meeting for TODAY if the schedule matches today's day
            $todayEng = Carbon::now()->format('l');
            if ($todayEng === $hariEng && $jadwal->tahunAjaran->is_active) {
                 if (!AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)->where('tanggal', Carbon::today()->toDateString())->exists()) {
                    $absenMasukToday = AbsenMasuk::create([
                        'id' => Str::uuid(),
                        'jadwal_ajar_id' => $jadwal->id,
                        'guru_id' => $jadwal->guru_id,
                        'kelas_id' => $jadwal->kelas_id,
                        'ruangan_id' => $jadwal->ruangan_id,
                        'tanggal' => Carbon::today()->toDateString(),
                        'jam_masuk' => substr($jadwal->jam_mulai, 0, 5), // Tepat waktu
                    ]);
                 }
            }
        }
    }
}
