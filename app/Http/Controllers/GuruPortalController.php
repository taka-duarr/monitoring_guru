<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $guru = \Illuminate\Support\Facades\Auth::user();
        $jadwals = \App\Models\JadwalAjar::with(['mapel', 'kelas'])->where('guru_id', $guru->id)->orderBy('hari')->get();
        return view('guru.izin', compact('jadwals'));
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'jadwal_ajar_id' => 'required|exists:jadwal_ajars,id',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('izins', 'public');
        }

        Izin::create([
            'guru_id'      => \Illuminate\Support\Facades\Auth::id(),
            'jadwal_ajar_id' => $request->jadwal_ajar_id,
            'tanggal_izin' => $request->tanggal,
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

    public function absenMurid($absen_masuk_id)
    {
        $absenMasuk = \App\Models\AbsenMasuk::with(['jadwalAjar.mapel', 'jadwalAjar.kelas'])->findOrFail($absen_masuk_id);
        
        if ($absenMasuk->guru_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $murids = \App\Models\Murid::where('kelas_id', $absenMasuk->kelas_id)->orderBy('no_absen')->orderBy('name')->get();
        $absenMurids = \App\Models\AbsenMurid::where('absen_masuk_id', $absen_masuk_id)->get()->keyBy('murid_id');

        return view('guru.absen_murid', compact('absenMasuk', 'murids', 'absenMurids'));
    }

    public function storeAbsenMurid(Request $request, $absen_masuk_id)
    {
        $absenMasuk = \App\Models\AbsenMasuk::findOrFail($absen_masuk_id);
        if ($absenMasuk->guru_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $muridsData = $request->input('status', []); // Array of murid_id => status

        foreach ($muridsData as $muridId => $status) {
            \App\Models\AbsenMurid::updateOrCreate(
                [
                    'absen_masuk_id' => $absen_masuk_id,
                    'murid_id' => $muridId
                ],
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'status' => $status
                ]
            );
        }

        return redirect()->route('guru.dashboard')->with('success', 'Absensi murid berhasil disimpan!');
    }
}
