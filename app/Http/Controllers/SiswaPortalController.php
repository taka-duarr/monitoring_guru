<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaPortalController extends Controller
{
    public function dashboard()
    {
        $ketua = Auth::user();
        $kelas = $ketua->kelas; // relasi dari model Guru
        
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        
        $jadwals = [];
        $today = \Carbon\Carbon::today()->toDateString();
        if ($kelas) {
            $jadwals = \App\Models\JadwalAjar::with(['guru', 'mapel', 'ruangan'])
                        ->where('kelas_id', $kelas->id)
                        ->where('hari', $hariIni)
                        ->orderBy('jam_mulai', 'asc')
                        ->get();

            foreach ($jadwals as $jadwal) {
                $absen = \App\Models\AbsenMasuk::where('jadwal_ajar_id', $jadwal->id)
                            ->where('tanggal', $today)
                            ->first();
                
                $jadwal->absen_masuk = $absen;
                $jadwal->absen_keluar = null;
                
                if ($absen) {
                    $jadwal->absen_keluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absen->id)->first();
                }
            }
        }
        
        return view('siswa.dashboard', compact('ketua', 'kelas', 'jadwals', 'hariIni'));
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
