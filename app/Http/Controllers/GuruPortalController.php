<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Izin;

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

    public function storeIzin(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('izins', 'public');
        }

        Izin::create([
            'tanggal_izin' => $request->tanggal,
            'jam_izin' => '07:00', // default jam masuk
            'judul' => $request->jenis == 'sakit' ? 'Sakit' : 'Izin',
            'pesan' => $request->keterangan,
            'file' => $filePath,
            'approval' => false,
            'read' => false,
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Pengajuan izin berhasil dikirim! Menunggu persetujuan Admin.');
    }

    public function riwayatMapel($mapel_id)
    {
        $guru = \Illuminate\Support\Facades\Auth::user();
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $riwayat = \App\Models\AbsenMasuk::with(['kelas', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->whereHas('jadwalAjar', function ($query) use ($mapel_id) {
                $query->where('mapel_id', $mapel_id);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->get();

        return view('guru.riwayat_mapel', compact('mapel', 'riwayat'));
    }
}
