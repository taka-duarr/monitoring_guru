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
                if ($kelas->grade == 10 || $kelas->grade == 11) {
                    $newGrade = $kelas->grade + 1;
                    $prefixOld = $kelas->grade == 10 ? '/^X\s/' : '/^XI\s/';
                    $prefixNew = $kelas->grade == 10 ? 'XI ' : 'XII ';
                    $newName = preg_replace($prefixOld, $prefixNew, $kelas->name);

                    // Buat kelas baru
                    $newKelas = Kelas::create([
                        'jurusan_id' => $kelas->jurusan_id,
                        'angkatan_id' => $kelas->angkatan_id,
                        'name' => $newName,
                        'grade' => $newGrade,
                        'ketua_id' => $kelas->ketua_id,
                        'is_active' => true,
                        'index' => $kelas->index,
                    ]);

                    // Pindahkan murid aktif ke kelas baru dengan cara diduplikasi (copy)
                    $activeMurids = Murid::where('kelas_id', $kelas->id)
                                         ->where('status', 'aktif')
                                         ->get();
                                         
                    foreach ($activeMurids as $murid) {
                        $newMurid = $murid->replicate();
                        $newMurid->kelas_id = $newKelas->id;
                        $newMurid->save();
                    }

                    // Ubah status murid lama di kelas lama menjadi naik_kelas
                    Murid::where('kelas_id', $kelas->id)
                         ->where('status', 'aktif')
                         ->update(['status' => 'naik_kelas']);

                    // Nonaktifkan kelas lama
                    $kelas->update(['is_active' => false]);
                    $count++;
                }
            }
            $message = "$count Kelas beserta muridnya berhasil dinaikkan tingkatnya ke kelas baru (Kelas lama dinonaktifkan).";
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
