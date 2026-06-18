<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Render the admin dashboard with real-time statistics and visualizations.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $selectedDate = $request->input('date');
        try {
            $today = $selectedDate ? \Carbon\Carbon::parse($selectedDate)->startOfDay() : \Carbon\Carbon::today();
        } catch (\Exception $e) {
            $today = \Carbon\Carbon::today();
        }
        $yesterday = (clone $today)->subDay();

        // 1. Statistics Data
        $totalGuru = \App\Models\User::where('jabatan', 'guru')->count();

        $hadirToday = \App\Models\AbsenMasuk::whereDate('tanggal', $today)->distinct('guru_id')->count('guru_id');
        $hadirYesterday = \App\Models\AbsenMasuk::whereDate('tanggal', $yesterday)->distinct('guru_id')->count('guru_id');

        $tidakHadirToday = max(0, $totalGuru - $hadirToday);
        $tidakHadirYesterday = max(0, $totalGuru - $hadirYesterday);

        $persenHadir = $totalGuru > 0 ? round(($hadirToday / $totalGuru) * 100, 1) : 0;

        $stats = [
            'total_guru' => $totalGuru,
            'hadir' => $hadirToday,
            'tidak_hadir' => $tidakHadirToday,
            'persen_hadir' => $persenHadir,
            'change_hadir' => $hadirToday - $hadirYesterday,
            'change_tidak_hadir' => $tidakHadirToday - $tidakHadirYesterday,
            'active_guru_change' => $hadirToday - $hadirYesterday,
        ];

        // 2. Chart Data - Kehadiran 7 Hari Terakhir
        $labels = [];
        $hadirArr = [];
        $tidakHadirArr = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = (clone $today)->subDays($i);
            $labels[] = $date->locale('id')->translatedFormat('D');
            
            $h = \App\Models\AbsenMasuk::whereDate('tanggal', $date)->distinct('guru_id')->count('guru_id');
            $hadirArr[] = $h;
            $tidakHadirArr[] = max(0, $totalGuru - $h);
        }
        $chartData = [
            'labels' => $labels,
            'hadir' => $hadirArr,
            'tidak_hadir' => $tidakHadirArr,
        ];

        // 3. Donut Chart Data - Distribusi Status Hari Ini
        $izinsToday = \App\Models\Izin::whereDate('tanggal_izin', $today)->get();
        $sakitCount = $izinsToday->where('judul', 'Sakit')->unique('guru_id')->count();
        $izinCount = $izinsToday->where('judul', 'Izin')->unique('guru_id')->count();
        $alphaCount = max(0, $tidakHadirToday - $sakitCount - $izinCount);

        $donutData = [
            'hadir' => $hadirToday,
            'sakit' => $sakitCount,
            'izin' => $izinCount,
            'alpha' => $alphaCount,
        ];

        // 4. Guru Tidak Hadir Hari Ini (Dari Izin Guru)
        $izinsForTable = \App\Models\Izin::with(['guru', 'jadwalAjar.mapel', 'jadwalAjar.kelas'])
            ->whereDate('tanggal_izin', $today)
            ->latest()
            ->take(5)
            ->get();

        $tidak_hadir_hari_ini = $izinsForTable->map(function ($izin) {
            return (object)[
                'name' => $izin->guru ? $izin->guru->name : '-',
                'mapel' => $izin->jadwalAjar && $izin->jadwalAjar->mapel ? $izin->jadwalAjar->mapel->name : '-',
                'kelas' => $izin->jadwalAjar && $izin->jadwalAjar->kelas ? $izin->jadwalAjar->kelas->name : '-',
                'status' => $izin->judul, // 'Sakit' or 'Izin'
                'keterangan' => $izin->pesan ?: '-'
            ];
        });

        // 5. Jadwal Kelas Kosong Hari Ini (Tanpa AbsenMasuk)
        $hariIniStr = ucfirst(strtolower($today->locale('id')->isoFormat('dddd')));
        $jadwal_kosong_db = \App\Models\JadwalAjar::with(['kelas', 'mapel'])
            ->where('hari', $hariIniStr)
            ->whereDoesntHave('absenMasuks', function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            })
            ->take(5)
            ->get();

        $jadwal_kosong = $jadwal_kosong_db->map(function ($jadwal) {
            return (object)[
                'jam' => $jadwal->jam_mulai . ' – ' . $jadwal->jam_selesai,
                'kelas' => $jadwal->kelas ? $jadwal->kelas->name : '-',
                'mapel' => $jadwal->mapel ? $jadwal->mapel->name : '-',
                'status' => 'KOSONG'
            ];
        });

        $currentFilterDate = $today->format('Y-m-d');

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'donutData',
            'tidak_hadir_hari_ini',
            'jadwal_kosong',
            'currentFilterDate'
        ));
    }
}
