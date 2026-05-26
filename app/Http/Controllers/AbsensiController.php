<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\AbsenMasuk;
use App\Models\StatusKelas;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function processQr(Request $request)
    {
        $payload = json_decode($request->qr_data, true);

        if (!$payload || !isset($payload['type']) || $payload['type'] !== 'absen_jadwal') {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid!'], 400);
        }

        $jadwalId = $payload['jadwal_id'];
        $jadwal = \App\Models\JadwalAjar::with('kelas')->find($jadwalId);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal Mata Pelajaran tidak ditemukan!'], 404);
        }

        $guru = Auth::user();
        
        // Validasi: Apakah QR yang di-scan BENAR-BENAR milik guru yang sedang login?
        if ($jadwal->guru_id !== $guru->id) {
            return response()->json([
                'success' => false, 
                'message' => "Ditolak! Ini adalah QR untuk mata pelajaran Guru lain."
            ], 403);
        }

        $kelasId = $jadwal->kelas_id;
        $today = \Carbon\Carbon::today();

        // Cari data Absen Masuk untuk jadwal ini hari ini
        $absenMasuk = AbsenMasuk::where('jadwal_ajar_id', $jadwalId)
                                ->where('tanggal', now()->toDateString())
                                ->first();
        
        if ($absenMasuk) {
            // Jika sudah absen masuk, cek apakah sudah absen keluar
            $absenKeluar = \App\Models\AbsenKeluar::where('absen_masuk_id', $absenMasuk->id)->first();
            
            if ($absenKeluar) {
                return response()->json([
                    'success' => false, 
                    'message' => "Anda sudah melakukan absen MASUK dan KELUAR untuk jadwal ini."
                ], 400);
            }
            
            // Lakukan Absen Keluar
            \App\Models\AbsenKeluar::create([
                'id' => Str::uuid(),
                'absen_masuk_id' => $absenMasuk->id,
                'jam_keluar' => now()->format('H:i:s'),
                'status' => 'selesai',
            ]);
            
            // Matikan status kelas (is_active = false)
            $statusKelas = StatusKelas::where('kelas_id', $kelasId)
                                      ->whereDate('created_at', $today)
                                      ->first();
            if ($statusKelas) {
                $statusKelas->update(['is_active' => false]);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Berhasil absen KELUAR untuk mata pelajaran ' . ($jadwal->mapel->name ?? 'ini')
            ]);
        }

        // Jika BELUM absen masuk, lakukan Absen Masuk
        $absenMasukBaru = AbsenMasuk::create([
            'id' => Str::uuid(),
            'guru_id' => $guru->id,
            'kelas_id' => $kelasId,
            'jadwal_ajar_id' => $jadwalId,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->format('H:i:s'),
        ]);

        // Update status kelas
        $statusKelas = StatusKelas::where('kelas_id', $kelasId)
                                  ->whereDate('created_at', $today)
                                  ->first();

        if ($statusKelas) {
            $statusKelas->update([
                'mapel' => $jadwal->mapel->name ?? 'Mapel Tidak Diketahui',
                'pengajar' => $guru->name,
                'ruangan' => $jadwal->ruangan->name ?? '-',
                'is_active' => true,
            ]);
        } else {
            StatusKelas::create([
                'id' => Str::uuid(),
                'kelas_id' => $kelasId,
                'mapel' => $jadwal->mapel->name ?? 'Mapel Tidak Diketahui',
                'pengajar' => $guru->name,
                'ruangan' => $jadwal->ruangan->name ?? '-',
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil absen MASUK untuk mata pelajaran ' . ($jadwal->mapel->name ?? 'ini') . ' di kelas ' . ($jadwal->kelas->name ?? '')
        ]);
    }
}
