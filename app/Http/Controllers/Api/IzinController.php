<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    /**
     * Tampilkan riwayat izin pengguna saat ini.
     * Karena saat ini tabel Izin tidak memiliki relasi langsung ke user di storeIzin web,
     * ini akan mengembalikan semua data izin atau dapat disesuaikan jika relasi ditambahkan nanti.
     */
    public function index(Request $request)
    {
        $izin = Izin::where('guru_id', $request->user()->id)->latest()->get();

        // Tambahkan URL lengkap untuk file
        $izin->transform(function ($item) {
            $item->file_url = $item->file ? asset('storage/' . $item->file) : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar riwayat izin',
            'data'    => $izin
        ], 200);
    }

    /**
     * Simpan pengajuan izin baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal'    => 'required|date',
            'jenis'      => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string',
            'file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('izins', 'public');
        }

        $izin = Izin::create([
            'guru_id'        => $request->user()->id,
            'jadwal_ajar_id' => $request->jadwal_ajar_id,
            'tanggal_izin'   => $request->tanggal,
            'judul'          => $request->jenis == 'sakit' ? 'Sakit' : 'Izin',
            'pesan'          => $request->keterangan,
            'file'           => $filePath,
            'approval'       => false,
            'read'           => false,
        ]);

        $izin->file_url = $filePath ? asset('storage/' . $filePath) : null;

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim',
            'data'    => $izin
        ], 201);
    }
}
