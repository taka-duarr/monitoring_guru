<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Izin;

class GuruPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $guru = \Illuminate\Support\Facades\Auth::user();
        
        // Ambil nama hari secara eksplisit untuk menghindari masalah locale/case
        $hariInggris = \Carbon\Carbon::now()->format('l');
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
        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();

        // Ambil semua jadwal khusus guru ini dan khusus hari ini (untuk kartu ringkasan)
        $allJadwalsQuery = \App\Models\JadwalAjar::with(['mapel', 'kelas', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->whereHas('kelas', function($q) {
                $q->where('is_active', true);
            });
        
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
            $izin = \App\Models\Izin::where('guru_id', $guru->id)
                        ->where('tanggal_izin', $today)
                        ->where('approval', true)
                        ->where(function($q) use ($jadwal) {
                            $q->whereNull('jadwal_ajar_id')->orWhere('jadwal_ajar_id', $jadwal->id);
                        })
                        ->first();
            $jadwal->izin_guru = $izin;
        }

        // Ambil jadwal terpaginasi hari ini (untuk kartu utama)
        $jadwalsQuery = \App\Models\JadwalAjar::with(['mapel', 'kelas', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->whereHas('kelas', function($q) {
                $q->where('is_active', true);
            });
            
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
            $izin = \App\Models\Izin::where('guru_id', $guru->id)
                        ->where('tanggal_izin', $today)
                        ->where('approval', true)
                        ->where(function($q) use ($jadwal) {
                            $q->whereNull('jadwal_ajar_id')->orWhere('jadwal_ajar_id', $jadwal->id);
                        })
                        ->first();
            $jadwal->izin_guru = $izin;
        }

        // Tahun Ajaran untuk filter "Semua Jadwal"
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();
        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();
        $selectedTahunAjaranId = $request->tahun_ajaran_id ?? $tahunAjaranAktif?->id;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        // Ambil semua jadwal (semua hari) untuk guru ini, filter by Tahun Ajaran
        $jadwalSemaQuery = \App\Models\JadwalAjar::with(['mapel', 'kelas', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->whereHas('kelas', function($q) {
                $q->where('is_active', true);
            })
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai', 'asc');

        if ($selectedTahunAjaranId) {
            $jadwalSemaQuery->where('tahun_ajaran_id', $selectedTahunAjaranId);
        }

        $jadwalSemua = $jadwalSemaQuery->get()->groupBy('hari');

        return view('guru.dashboard', compact(
            'allJadwals', 'jadwals', 'hariIni', 'jadwalSemua',
            'tahunAjarans', 'selectedTahunAjaran', 'selectedTahunAjaranId'
        ));
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

    public function riwayatJadwal($jadwal_ajar_id)
    {
        $guru = \Illuminate\Support\Facades\Auth::user();
        
        $jadwal = \App\Models\JadwalAjar::with(['mapel', 'kelas.angkatan', 'ruangan', 'tahunAjaran'])
            ->where('guru_id', $guru->id)
            ->findOrFail($jadwal_ajar_id);

        $riwayat = \App\Models\AbsenMasuk::with(['kelas.angkatan', 'ruangan'])
            ->where('guru_id', $guru->id)
            ->where('jadwal_ajar_id', $jadwal_ajar_id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->get();

        return view('guru.riwayat_jadwal', compact('jadwal', 'riwayat'));
    }


    public function riwayatGlobal(Request $request)
    {
        $guru = \Illuminate\Support\Facades\Auth::user();

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();

        $tahunAjaranAktif = \App\Models\TahunAjaran::aktif();
        $selectedId = $request->tahun_ajaran_id ?? $tahunAjaranAktif?->id;

        $query = \App\Models\AbsenMasuk::with(['kelas', 'ruangan', 'jadwalAjar.mapel', 'jadwalAjar.tahunAjaran'])
            ->where('guru_id', $guru->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc');

        if ($selectedId) {
            $query->whereHas('jadwalAjar', function($q) use ($selectedId) {
                $q->where('tahun_ajaran_id', $selectedId);
            });
        }

        $riwayat = $query->paginate(20);
        $selectedTahunAjaran = $selectedId ? $tahunAjarans->firstWhere('id', $selectedId) : null;

        return view('guru.riwayat', compact('riwayat', 'tahunAjarans', 'selectedTahunAjaran', 'selectedId'));
    }

    public function absenMurid($absen_masuk_id)
    {
        $absenMasuk = \App\Models\AbsenMasuk::with(['jadwalAjar.mapel', 'jadwalAjar.kelas'])->findOrFail($absen_masuk_id);
        
        if ($absenMasuk->guru_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $murids = \App\Models\Murid::where('kelas_id', $absenMasuk->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('no_absen')
            ->orderBy('name')
            ->get();
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
