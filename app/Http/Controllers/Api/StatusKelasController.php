<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;

class StatusKelasController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kelas beserta status live-nya hari ini
        $kelas = Kelas::with(['live_status' => function($query) {
            $query->whereDate('created_at', now()->today());
        }])->get();

        $data = $kelas->map(function ($item) {
            $status = $item->live_status;
            $isActive = $status ? (bool) $status->is_active : false;

            return [
                'kelas_id' => $item->id,
                'kelas_name' => $item->name,
                'is_active' => $isActive,
                'mapel' => $isActive ? ($status->mapel ?? '-') : '-',
                'pengajar' => $isActive ? ($status->pengajar ?? '-') : '-',
                'ruangan' => $isActive ? ($status->ruangan ?? '-') : '-',
                'updated_at' => $status ? $status->updated_at->diffForHumans() : '-'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
