<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruPortalController extends Controller
{
    public function dashboard()
    {
        $guru = \Illuminate\Support\Facades\Auth::user();
        
        // Ambil nama hari dalam bahasa Indonesia
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        
        // Ambil jadwal khusus guru ini dan khusus hari ini, urutkan berdasarkan jam mulai
        $jadwals = \App\Models\JadwalAjar::with(['mapel', 'kelas', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $today = \Carbon\Carbon::today()->toDateString();
        
        // Looping untuk menyuntikkan data absensi ke setiap jadwal
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

        return view('guru.dashboard', compact('jadwals', 'hariIni'));
    }

    public function scan()
    {
        return view('guru.scan');
    }

    public function izin()
    {
        return view('guru.izin');
    }
}
