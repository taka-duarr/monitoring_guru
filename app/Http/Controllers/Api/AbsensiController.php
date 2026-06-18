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

        if (isset($payload['timestamp'])) {
            $diff = time() - ($payload['timestamp'] / 1000);
            // Toleransi 5 detik untuk delay internet/scanning (total 35 detik)
            if ($diff > 35) {
                return response()->json(['success' => false, 'message' => 'QR Code sudah kedaluwarsa! Silakan minta murid menampilkan QR terbaru.'], 400);
            }
        }

        $jadwalId = $payload['jadwal_id'];
        $jadwal = JadwalAjar::with(['kelas', 'mapel', 'ruangan'])->find($jadwalId);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal Mata Pelajaran tidak ditemukan!'], 404);
        }

        $user = $request->user();
        
        // Cek apakah user yang login adalah Guru (bukan ketua kelas)
        if (!$user || !in_array($user->jabatan, ['guru', 'admin'])) {
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

        // ── Validasi Waktu (Server-Side) ──────────────────────────────────────
        // Hanya berlaku untuk Absen MASUK
        $jamMulai = \Carbon\Carbon::createFromFormat('H:i', substr($jadwal->jam_mulai, 0, 5));
        $now      = \Carbon\Carbon::now();

        if ($now->lt($jamMulai)) {
            $sisaMenit = (int) $now->diffInMinutes($jamMulai, false) * -1;
            $label     = $jamMulai->format('H:i');
            return response()->json([
                'success' => false,
                'message' => "Belum bisa absen masuk. Kelas dimulai pukul {$label} " .
                             "(masih " . abs($sisaMenit) . " menit lagi).",
            ], 403);
        }
        // ─────────────────────────────────────────────────────────────────────

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

    public function getAbsenMurid(Request $request, $absen_masuk_id)
    {
        $absenMasuk = AbsenMasuk::find($absen_masuk_id);
        if (!$absenMasuk) {
            return response()->json(['success' => false, 'message' => 'Data absen guru tidak ditemukan'], 404);
        }

        $murids = \App\Models\Murid::where('kelas_id', $absenMasuk->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('no_absen')
            ->orderBy('name')
            ->get();
        $absenMurids = \App\Models\AbsenMurid::where('absen_masuk_id', $absen_masuk_id)->get()->keyBy('murid_id');

        $data = $murids->map(function ($murid) use ($absenMurids) {
            $status = 'hadir'; // default
            if ($absenMurids->has($murid->id)) {
                $status = $absenMurids[$murid->id]->status;
            }
            return [
                'id' => $murid->id,
                'no_absen' => $murid->no_absen,
                'nis' => $murid->nis,
                'name' => $murid->name,
                'status' => $status
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function saveAbsenMurid(Request $request, $absen_masuk_id)
    {
        $absenMasuk = AbsenMasuk::find($absen_masuk_id);
        if (!$absenMasuk) {
            return response()->json(['success' => false, 'message' => 'Data absen guru tidak ditemukan'], 404);
        }

        $muridsData = $request->input('murids', []);

        foreach ($muridsData as $mData) {
            if (!isset($mData['id']) || !isset($mData['status'])) continue;

            \App\Models\AbsenMurid::updateOrCreate(
                [
                    'absen_masuk_id' => $absen_masuk_id,
                    'murid_id' => $mData['id']
                ],
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'status' => $mData['status']
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Absensi murid berhasil disimpan!'
        ]);
    }
}
