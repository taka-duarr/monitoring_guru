<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Render the admin dashboard with real-time statistics and visualizations.
     */
    public function index()
    {
        // 1. Statistics Data
        $stats = [
            'total_guru' => 48,
            'hadir' => 42,
            'tidak_hadir' => 6,
            'persen_hadir' => 87.5,
            'change_hadir' => 3,        // +3 dibanding kemarin
            'change_tidak_hadir' => -1, // -1 dibanding kemarin
            'active_guru_change' => 2,   // ↑2 aktif
        ];

        // 2. Chart Data - Kehadiran 7 Hari Terakhir
        $chartData = [
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'hadir' => [42, 40, 44, 45, 41, 12, 0],
            'tidak_hadir' => [6, 8, 4, 3, 7, 36, 48],
        ];

        // 3. Donut Chart Data - Distribusi Status Hari Ini
        $donutData = [
            'hadir' => 42,
            'sakit' => 3,
            'izin' => 2,
            'alpha' => 1,
        ];

        // 4. Guru Tidak Hadir Hari Ini (Maks 5 data untuk tabel dashboard)
        $tidak_hadir_hari_ini = collect([
            (object)[
                'name' => 'Budi Santoso',
                'mapel' => 'Matematika',
                'kelas' => 'XII IPA 1',
                'status' => 'Sakit',
                'keterangan' => 'Surat dokter terlampir'
            ],
            (object)[
                'name' => 'Siti Rahma',
                'mapel' => 'Bahasa Inggris',
                'kelas' => 'XI IPS 2',
                'status' => 'Izin',
                'keterangan' => 'Menghadiri seminar nasional'
            ],
            (object)[
                'name' => 'Joko Susilo',
                'mapel' => 'Fisika',
                'kelas' => 'X IPA 3',
                'status' => 'Alpha',
                'keterangan' => 'Tanpa keterangan'
            ],
            (object)[
                'name' => 'Rini Handayani',
                'mapel' => 'Kimia',
                'kelas' => 'XI IPA 2',
                'status' => 'Sakit',
                'keterangan' => 'Demam tinggi'
            ],
            (object)[
                'name' => 'Andi Wijaya',
                'mapel' => 'Sejarah',
                'kelas' => 'XII IPS 1',
                'status' => 'Izin',
                'keterangan' => 'Keperluan dinas luar'
            ]
        ]);

        // 5. Jadwal Kelas Kosong Hari Ini (Tanpa guru pengganti)
        $jadwal_kosong = collect([
            (object)[
                'jam' => '07.00 – 08.30',
                'kelas' => 'XII IPA 1',
                'mapel' => 'Matematika',
                'status' => 'KOSONG'
            ],
            (object)[
                'jam' => '08.30 – 10.00',
                'kelas' => 'XI IPS 2',
                'mapel' => 'Bahasa Inggris',
                'status' => 'KOSONG'
            ],
            (object)[
                'jam' => '10.15 – 11.45',
                'kelas' => 'X IPA 3',
                'mapel' => 'Fisika',
                'status' => 'KOSONG'
            ]
        ]);

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'donutData',
            'tidak_hadir_hari_ini',
            'jadwal_kosong'
        ));
    }
}
