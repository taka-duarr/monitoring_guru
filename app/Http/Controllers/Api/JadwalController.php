<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalAjar;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $hariInggris = Carbon::now()->format('l');
        $mapHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        $hariIni = $mapHari[$hariInggris];

        $query = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])->where('hari', $hariIni);

        // Filter berdasarkan jabatan (role)
        $jabatan = $user->jabatan ?? 'guru';
        if ($jabatan === 'guru' || $jabatan === 'admin') {
            $query->where('guru_id', $user->id);
        } elseif ($jabatan === 'ketuakelas') {
            $query->where('kelas_id', $user->kelas_id);
        }

        $jadwal = $query->orderBy('jam_mulai')->get();

        $today = \Carbon\Carbon::today()->toDateString();
        foreach ($jadwal as $j) {
            $absen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $j->id)
                        ->where('tanggal', $today)
                        ->first();
            
            $j->absen_masuk = $absen;
            $j->absen_keluar = null;
            
            if ($absen) {
                $j->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
            }
        }

        return response()->json([
            'success' => true,
            'hari'    => $hariIni,
            'data'    => $jadwal
        ]);
    }

    public function riwayatMapel($mapel_id, Request $request)
    {
        $user = $request->user();
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $riwayat = \App\Models\AbsenMasuk::with(['kelas', 'ruangan'])
            ->where('guru_id', $user->id)
            ->whereHas('jadwalAjar', function ($query) use ($mapel_id) {
                $query->where('mapel_id', $mapel_id);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->get()
            ->map(function ($absen) {
                $absen->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
                return $absen;
            });

        return response()->json([
            'success' => true,
            'mapel'   => $mapel,
            'data'    => $riwayat
        ]);
    }
}
