<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\KetuaKelas;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'password' => 'required'
        ]);

        // Coba cek di tabel Guru
        $guru = Guru::where('nik', $request->nik)->first();
        if ($guru && Hash::check($request->password, $guru->password)) {
            $token = $guru->createToken('guru-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil sebagai Guru',
                'role' => 'guru',
                'user' => $guru,
                'token' => $token
            ]);
        }

        // Jika tidak ketemu/salah, coba cek di tabel KetuaKelas
        $ketua = KetuaKelas::where('nik', $request->nik)->first();
        if ($ketua && Hash::check($request->password, $ketua->password)) {
            // Load relasi kelas untuk Ketua Kelas
            $ketua->load('kelas');
            $token = $ketua->createToken('ketuakelas-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil sebagai Ketua Kelas',
                'role' => 'ketuakelas',
                'user' => $ketua,
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'NIK atau password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
