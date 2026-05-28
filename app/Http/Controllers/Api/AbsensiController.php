<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalAjar;
use App\Models\AbsenMasuk;
use App\Models\AbsenKeluar;
use App\Models\StatusKelas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function scan(Request $request)
    {
        $payload = json_decode($request->qr_data, true);

        if (!$payload || !isset($payload['type']) || $payload['type'] !== 'absen_jadwal') {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid!'], 400);
        }

        $jadwalId = $payload['jadwal_id'];
        $jadwal = JadwalAjar::with(['kelas', 'mapel', 'ruangan'])->find($jadwalId);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal Mata Pelajaran tidak ditemukan!'], 404);
        }

        $user = $request->user();
        
        // Cek apakah user yang login adalah Guru
        if (!$user instanceof \App\Models\Guru) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Guru yang dapat melakukan scan absensi.'
            ], 403);
        }

        if ($jadwal->guru_id !== $user->id) {
            return response()->json([
                'success' => false, 
                'message' => "Ditolak! Ini adalah jadwal mengajar Guru lain."
            ], 403);
        }

        $kelasId = $jadwal->kelas_id;
        $today = now()->toDateString();

        $absenMasuk = AbsenMasuk::where('jadwal_ajar_id', $jadwalId)
                                ->where('tanggal', $today)
                                ->first();
        
        if ($absenMasuk) {
            $absenKeluar = AbsenKeluar::where('absen_masuk_id', $absenMasuk->id)->first();
            
            if ($absenKeluar) {
                return response()->json([
                    'success' => false, 
                    'message' => "Anda sudah menyelesaikan absen MASUK dan KELUAR untuk jadwal ini."
                ], 400);
            }
            
            AbsenKeluar::create([
                'id' => Str::uuid(),
                'absen_masuk_id' => $absenMasuk->id,
                'jam_keluar' => now()->format('H:i:s'),
                'status' => 'selesai',
            ]);
            
            $statusKelas = StatusKelas::where('kelas_id', $kelasId)->whereDate('created_at', now()->today())->first();
            if ($statusKelas) {
                $statusKelas->update(['is_active' => false]);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Berhasil absen KELUAR untuk ' . ($jadwal->mapel->name ?? 'Mata Pelajaran')
            ]);
        }

        $absenMasukBaru = AbsenMasuk::create([
            'id' => Str::uuid(),
            'guru_id' => $user->id,
            'kelas_id' => $kelasId,
            'jadwal_ajar_id' => $jadwalId,
            'tanggal' => $today,
            'jam_masuk' => now()->format('H:i:s'),
        ]);

        $statusKelas = StatusKelas::where('kelas_id', $kelasId)->whereDate('created_at', now()->today())->first();

        if ($statusKelas) {
            $statusKelas->update([
                'mapel' => $jadwal->mapel->name ?? 'Tidak Diketahui',
                'pengajar' => $user->name,
                'ruangan' => $jadwal->ruangan->name ?? '-',
                'is_active' => true,
            ]);
        } else {
            StatusKelas::create([
                'id' => Str::uuid(),
                'kelas_id' => $kelasId,
                'mapel' => $jadwal->mapel->name ?? 'Tidak Diketahui',
                'pengajar' => $user->name,
                'ruangan' => $jadwal->ruangan->name ?? '-',
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil absen MASUK untuk ' . ($jadwal->mapel->name ?? 'Mata Pelajaran')
        ]);
    }
}
