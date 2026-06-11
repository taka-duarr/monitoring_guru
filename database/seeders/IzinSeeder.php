<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;
use App\Models\User;
use App\Models\JadwalAjar;
use Illuminate\Support\Str;
use Carbon\Carbon;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guru1 = User::where('name', 'Pak Budi')->first();
        $guru2 = User::where('name', 'Bu Ani')->first();

        if (!$guru1 || !$guru2) {
            return;
        }

        $jadwalPakBudi = JadwalAjar::where('guru_id', $guru1->id)->first();
        $jadwalBuAni = JadwalAjar::where('guru_id', $guru2->id)->first();

        // 1. Izin Sakit Pak Budi - Approved
        Izin::create([
            'id' => (string) Str::uuid(),
            'guru_id' => $guru1->id,
            'jadwal_ajar_id' => $jadwalPakBudi ? $jadwalPakBudi->id : null,
            'tanggal_izin' => Carbon::now()->subDays(2)->toDateString(),
            'judul' => 'Sakit Flu & Demam',
            'pesan' => 'Mohon maaf tidak bisa mengajar hari ini karena sedang sakit demam tinggi dan disarankan dokter untuk istirahat.',
            'file' => null,
            'approval' => true,
            'read' => true,
        ]);

        // 2. Izin Dinas Bu Ani - Approved
        Izin::create([
            'id' => (string) Str::uuid(),
            'guru_id' => $guru2->id,
            'jadwal_ajar_id' => $jadwalBuAni ? $jadwalBuAni->id : null,
            'tanggal_izin' => Carbon::now()->subDays(1)->toDateString(),
            'judul' => 'Rapat Dinas Pendidikan',
            'pesan' => 'Mengikuti rapat dinas di Kantor Dinas Pendidikan Kota Surabaya terkait penyusunan kurikulum baru.',
            'file' => null,
            'approval' => true,
            'read' => true,
        ]);

        // 3. Izin Baru Pak Budi - Pending approval
        Izin::create([
            'id' => (string) Str::uuid(),
            'guru_id' => $guru1->id,
            'jadwal_ajar_id' => $jadwalPakBudi ? $jadwalPakBudi->id : null,
            'tanggal_izin' => Carbon::now()->toDateString(),
            'judul' => 'Ada Urusan Keluarga Mendadak',
            'pesan' => 'Mohon izin untuk jadwal ajar hari ini karena ada keperluan keluarga mendadak yang tidak bisa ditinggalkan.',
            'file' => null,
            'approval' => false,
            'read' => false,
        ]);
    }
}
