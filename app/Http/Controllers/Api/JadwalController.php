<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalAjar;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Konversi hari ini ke format Bahasa Indonesia
        // Carbon default menggunakan locale 'id' jika sudah diset di config, atau manual map.
        $hariInggris = Carbon::now()->format('l');
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

        $query = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])->where('hari', $hariIni);

        if ($user instanceof \App\Models\Guru) {
            $query->where('guru_id', $user->id);
        } elseif ($user instanceof \App\Models\KetuaKelas) {
            $query->where('kelas_id', $user->kelas_id);
        }

        $jadwal = $query->orderBy('jam_mulai')->get();

        return response()->json([
            'success' => true,
            'hari' => $hariIni,
            'data' => $jadwal
        ]);
    }
}
