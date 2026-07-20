<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $ketua = Auth::user();
        $kelas = $ketua->kelas;
        
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        
        $allJadwals = collect([]);
        $jadwals = new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 6, 1);
        $today = \Carbon\Carbon::today()->toDateString();
        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();

        // Tahun ajaran list for filter
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();
        $selectedTahunAjaranId = $request->tahun_ajaran_id ?? $tahunAjaranAktif?->id;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        $jadwalSemua = collect([]);
        
        if ($kelas && $kelas->is_active) {
            $allJadwalsQuery = \App\Models\JadwalAjar::with(['guru', 'mapel', 'ruangan'])
                        ->where('kelas_id', $kelas->id)
                        ->where('hari', $hariIni);
            
            if ($tahunAjaranAktif) {
                $allJadwalsQuery->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            }

            $allJadwals = $allJadwalsQuery->orderBy('jam_mulai', 'asc')->get();

            foreach ($allJadwals as $jadwal) {
                $absen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)
                            ->where('tanggal', $today)
                            ->first();
                $jadwal->absen_masuk = $absen;
                $jadwal->absen_keluar = null;
                if ($absen) {
                    $jadwal->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
                }
                $izin = \App\Models\Izin::where('guru_id', $jadwal->guru_id)
                            ->where('tanggal_izin', $today)
                            ->where('approval', true)
                            ->where(function($q) use ($jadwal) {
                                $q->whereNull('jadwal_ajar_id')->orWhere('jadwal_ajar_id', $jadwal->id);
                            })
                            ->first();
                $jadwal->izin_guru = $izin;
            }

            $jadwalsQuery = \App\Models\JadwalAjar::with(['guru', 'mapel', 'ruangan'])
                        ->where('kelas_id', $kelas->id)
                        ->where('hari', $hariIni);
            if ($tahunAjaranAktif) {
                $jadwalsQuery->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            }
            $jadwals = $jadwalsQuery->orderBy('jam_mulai', 'asc')->paginate(6);

            foreach ($jadwals as $jadwal) {
                $absen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)
                            ->where('tanggal', $today)
                            ->first();
                $jadwal->absen_masuk = $absen;
                $jadwal->absen_keluar = null;
                if ($absen) {
                    $jadwal->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
                }
                $izin = \App\Models\Izin::where('guru_id', $jadwal->guru_id)
                            ->where('tanggal_izin', $today)
                            ->where('approval', true)
                            ->where(function($q) use ($jadwal) {
                                $q->whereNull('jadwal_ajar_id')->orWhere('jadwal_ajar_id', $jadwal->id);
                            })
                            ->first();
                $jadwal->izin_guru = $izin;
            }

            // Semua jadwal (semua hari) filter by tahun ajaran
            $jadwalSemaQuery = \App\Models\JadwalAjar::with(['guru', 'mapel', 'ruangan'])
                ->where('kelas_id', $kelas->id)
                ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
                ->orderBy('jam_mulai', 'asc');

            if ($selectedTahunAjaranId) {
                $jadwalSemaQuery->where('tahun_ajaran_id', $selectedTahunAjaranId);
            }

            $jadwalSemua = $jadwalSemaQuery->get()->groupBy('hari');
        }
        
        return view('siswa.dashboard', compact(
            'ketua', 'kelas', 'allJadwals', 'jadwals', 'hariIni',
            'jadwalSemua', 'tahunAjarans', 'selectedTahunAjaran', 'selectedTahunAjaranId'
        ));
    }


    public function checkStatus($id)
    {
        $today = \Carbon\Carbon::today()->toDateString();
        $absenMasuk = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $id)
                        ->where('tanggal', $today)
                        ->first();
                        
        $absenKeluar = null;
        if ($absenMasuk) {
            $absenKeluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absenMasuk->id)->first();
        }
        
        return response()->json([
            'absen_masuk' => $absenMasuk ? true : false,
            'absen_keluar' => $absenKeluar ? true : false,
        ]);
    }
}
