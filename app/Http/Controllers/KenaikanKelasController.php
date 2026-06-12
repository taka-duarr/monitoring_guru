<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Murid;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kelas yang aktif
        $kelasList = Kelas::withCount(['murids' => function($q) {
            $q->where('status', 'aktif');
        }])
        ->where('is_active', true)
        ->orderBy('grade')
        ->orderBy('name')
        ->get();

        return view('admin.kenaikan_kelas', compact('kelasList'));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'action' => 'required|in:naik_tingkat,luluskan'
        ]);

        $kelasIds = $request->kelas_ids;
        $count = 0;

        if ($request->action === 'naik_tingkat') {
            $kelasList = Kelas::whereIn('id', $kelasIds)->get();
            foreach ($kelasList as $kelas) {
                if ($kelas->grade == 10) {
                    $newName = preg_replace('/^X\s/', 'XI ', $kelas->name);
                    $kelas->update(['grade' => '11', 'name' => $newName]);
                    $count++;
                } elseif ($kelas->grade == 11) {
                    $newName = preg_replace('/^XI\s/', 'XII ', $kelas->name);
                    $kelas->update(['grade' => '12', 'name' => $newName]);
                    $count++;
                }
            }
            $message = "$count Kelas berhasil dinaikkan tingkatnya.";
        } else {
            // Luluskan
            $kelasList = Kelas::whereIn('id', $kelasIds)->get();
            foreach ($kelasList as $kelas) {
                // Update status kelas menjadi nonaktif
                $kelas->update(['is_active' => false]);
                // Luluskan semua siswa aktif di kelas tersebut
                Murid::where('kelas_id', $kelas->id)->where('status', 'aktif')->update(['status' => 'lulus']);
                $count++;
            }
            $message = "$count Kelas berhasil diluluskan beserta semua siswanya.";
        }

        return redirect()->route('kenaikan_kelas.index')->with('success', $message);
    }
}
