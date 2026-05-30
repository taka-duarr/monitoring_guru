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
        if ($kelas) {
            $jadwals = \App\Models\JadwalAjar::with(['guru', 'mapel', 'ruangan'])
                        ->where('kelas_id', $kelas->id)
                        ->where('hari', $hariIni)
                        ->orderBy('jam_mulai', 'asc')
                        ->get();
        }
        
        return view('siswa.dashboard', compact('ketua', 'kelas', 'jadwals', 'hariIni'));
    }
}
