<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Ruangan;
use App\Models\JadwalAjar;
use App\Models\AbsenMasuk;
use App\Models\AbsenKeluar;
use App\Models\AbsenMurid;
use App\Models\Murid;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure students are seeded first if empty
        if (Murid::count() === 0) {
            $this->call(MuridSeeder::class);
        }

        // 2. Fetch required entities
        $gurus = User::whereIn('jabatan', ['guru', 'admin'])->get();
        if ($gurus->isEmpty()) {
            $this->command->error('No teachers found. Please run DatabaseSeeder first.');
            return;
        }

        $kelas = Kelas::all();
        if ($kelas->isEmpty()) {
            $this->command->error('No classes found. Please run DatabaseSeeder first.');
            return;
        }

        $mapels = Mapel::all();
        if ($mapels->isEmpty()) {
            $this->command->error('No subjects (mapel) found. Please run DatabaseSeeder first.');
            return;
        }

        $ruangans = Ruangan::all();
        if ($ruangans->isEmpty()) {
            $this->command->error('No rooms found. Please run DatabaseSeeder first.');
            return;
        }

        // 3. Create extra schedules spread across weekdays to populate the dashboard nicely
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        foreach ($kelas as $k) {
            foreach ($days as $day) {
                // If this class has no schedule on this day, create one
                $hasSchedule = JadwalAjar::where('kelas_id', $k->id)
                    ->where('hari', $day)
                    ->exists();

                if (!$hasSchedule) {
                    $guru = $gurus->random();
                    $mapel = $mapels->random();
                    $ruangan = $ruangans->random();

                    // Shifts: morning or afternoon
                    $shifts = [
                        ['07:00', '09:00'],
                        ['09:00', '11:00'],
                        ['13:00', '15:00'],
                    ];
                    $shift = $shifts[array_rand($shifts)];

                    JadwalAjar::create([
                        'guru_id' => $guru->id,
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $k->id,
                        'ruangan_id' => $ruangan->id,
                        'hari' => $day,
                        'jam_mulai' => $shift[0],
                        'jam_selesai' => $shift[1],
                    ]);
                }
            }
        }

        // Get all schedules
        $schedules = JadwalAjar::all();

        $daysMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        // 4. Seeding check-in entries for the past 14 days
        $now = Carbon::now();
        
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayNameEnglish = $date->format('l');
            $dayNameIndonesian = $daysMap[$dayNameEnglish] ?? 'Senin';

            // Find schedules for this day
            $schedulesForDay = $schedules->where('hari', $dayNameIndonesian);

            foreach ($schedulesForDay as $schedule) {
                // If it's today, only create check-in if the scheduled start time is in the past
                if ($i === 0) {
                    $scheduleStart = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $schedule->jam_mulai);
                    if ($scheduleStart->isFuture()) {
                        continue;
                    }
                }

                // Randomize delay to make it realistic
                // 95% attendance rate for teachers
                $rand = rand(1, 100);
                if ($rand > 95) {
                    continue;
                }

                $startTime = Carbon::createFromFormat('H:i', $schedule->jam_mulai);
                
                if ($rand <= 70) {
                    // Early/on-time: up to 10 minutes early
                    $minutes = rand(0, 10);
                    $checkInTime = $startTime->copy()->subMinutes($minutes)->format('H:i:s');
                } else {
                    // Late: 1 to 20 minutes late
                    $minutes = rand(1, 20);
                    $checkInTime = $startTime->copy()->addMinutes($minutes)->format('H:i:s');
                }

                // Create check-in record
                $absenMasuk = AbsenMasuk::create([
                    'guru_id' => $schedule->guru_id,
                    'jadwal_ajar_id' => $schedule->id,
                    'kelas_id' => $schedule->kelas_id,
                    'ruangan_id' => $schedule->ruangan_id,
                    'tanggal' => $date->format('Y-m-d'),
                    'jam_masuk' => $checkInTime,
                ]);

                // Create check-out record
                $isPastDay = $i > 0;
                $shouldCheckOut = $isPastDay ? (rand(1, 100) <= 95) : false;
                
                if (!$isPastDay) {
                    $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $schedule->jam_selesai);
                    if ($scheduleEnd->isPast()) {
                        $shouldCheckOut = true;
                    }
                }

                if ($shouldCheckOut) {
                    $endTime = Carbon::createFromFormat('H:i', $schedule->jam_selesai);
                    // Checkout: usually near the scheduled end time (e.g. ±5 minutes)
                    $checkoutMinutes = rand(-5, 5);
                    $checkOutTime = $endTime->copy()->addMinutes($checkoutMinutes)->format('H:i:s');

                    AbsenKeluar::create([
                        'absen_masuk_id' => $absenMasuk->id,
                        'jam_keluar' => $checkOutTime,
                        'status' => 'selesai',
                    ]);
                }

                // Create student attendance logs
                $students = Murid::where('kelas_id', $schedule->kelas_id)->get();
                foreach ($students as $student) {
                    // Student status: 90% hadir, 4% sakit, 4% izin, 2% alpa
                    $studentRand = rand(1, 100);
                    if ($studentRand <= 90) {
                        $status = 'hadir';
                    } elseif ($studentRand <= 94) {
                        $status = 'sakit';
                    } elseif ($studentRand <= 98) {
                        $status = 'izin';
                    } else {
                        $status = 'alpa';
                    }

                    AbsenMurid::create([
                        'absen_masuk_id' => $absenMasuk->id,
                        'murid_id' => $student->id,
                        'status' => $status,
                    ]);
                }
            }
        }

        $this->command->info('Successfully seeded attendance monitoring dummy data!');
    }
}
