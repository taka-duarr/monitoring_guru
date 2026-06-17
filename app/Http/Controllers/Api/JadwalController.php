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

        $today = \Carbon\Carbon::today()->toDateString();
        $jabatan = $user->jabatan ?? 'guru';

        // 1. Ambil Tahun Ajaran (Aktif dan Semua)
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();
        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();
        $selectedTahunAjaranId = $request->tahun_ajaran_id ?? $tahunAjaranAktif?->id;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        // 2. Query Jadwal Hari Ini
        $queryHariIni = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])
            ->where('hari', $hariIni)
            ->whereHas('kelas', function($q) {
                $q->where('is_active', true);
            });

        if ($selectedTahunAjaranId) {
            $queryHariIni->where('tahun_ajaran_id', $selectedTahunAjaranId);
        }

        if ($jabatan === 'guru' || $jabatan === 'admin') {
            $queryHariIni->where('guru_id', $user->id);
        } elseif ($jabatan === 'ketuakelas') {
            $queryHariIni->where('kelas_id', $user->kelas_id);
        }

        $jadwalHariIni = $queryHariIni->orderBy('jam_mulai')->get();

        foreach ($jadwalHariIni as $j) {
            $absen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $j->id)
                        ->where('tanggal', $today)
                        ->first();
            
            $j->absen_masuk = $absen;
            $j->absen_keluar = null;
            
            if ($absen) {
                $j->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
            }
        }

        // 3. Query Semua Jadwal
        $querySemua = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])
            ->whereHas('kelas', function($q) {
                $q->where('is_active', true);
            });

        if ($selectedTahunAjaranId) {
            $querySemua->where('tahun_ajaran_id', $selectedTahunAjaranId);
        }

        if ($jabatan === 'guru' || $jabatan === 'admin') {
            $querySemua->where('guru_id', $user->id);
        } elseif ($jabatan === 'ketuakelas') {
            $querySemua->where('kelas_id', $user->kelas_id);
        }

        $semuaJadwalRaw = $querySemua->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $semuaJadwal = $semuaJadwalRaw->groupBy('hari');

        return response()->json([
            'success'               => true,
            'hari'                  => $hariIni,
            'data'                  => $jadwalHariIni,
            'tahun_ajarans'         => $tahunAjarans,
            'selected_tahun_ajaran' => $selectedTahunAjaranId,
            'semua_jadwal'          => $semuaJadwal
        ]);
    }

    public function riwayatMapel($mapel_id, Request $request)
    {
        $user = $request->user();
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $query = \App\Models\AbsenMasuk::with(['kelas', 'ruangan', 'guru'])
            ->whereHas('jadwalAjar', function ($q) use ($mapel_id) {
                $q->where('mapel_id', $mapel_id);
            });

        if ($user->jabatan === 'ketuakelas') {
            $query->where('kelas_id', $user->kelas_id);
        } else {
            $query->where('guru_id', $user->id);
        }

        $riwayat = $query->orderBy('tanggal', 'desc')
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
    public function riwayatGlobal(Request $request)
    {
        $user = $request->user();

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();
        
        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();
        $selectedId = $request->tahun_ajaran_id ?? $tahunAjaranAktif?->id;

        $query = \App\Models\AbsenMasuk::with(['kelas', 'ruangan', 'guru', 'jadwalAjar.mapel', 'jadwalAjar.tahunAjaran'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc');

        if ($user->jabatan === 'ketuakelas') {
            $query->where('kelas_id', $user->kelas_id);
        } else {
            $query->where('guru_id', $user->id);
        }

        if ($selectedId) {
            $query->whereHas('jadwalAjar', function($q) use ($selectedId) {
                $q->where('tahun_ajaran_id', $selectedId);
            });
        }

        $riwayat = $query->get()->map(function ($absen) {
            $absen->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
            return $absen;
        });

        $selectedTahunAjaran = $selectedId ? $tahunAjarans->firstWhere('id', $selectedId) : null;

        return response()->json([
            'success'               => true,
            'tahun_ajarans'         => $tahunAjarans,
            'selected_tahun_ajaran' => $selectedTahunAjaran,
            'data'                  => $riwayat
        ]);
    }
}
