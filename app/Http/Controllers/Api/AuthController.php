<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nik'      => 'required',
            'password' => 'required'
        ]);

        $user = User::where('nik', $request->nik)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK atau password salah'
            ], 401);
        }

        $roleMap = [
            'admin'      => 'admin',
            'guru'       => 'guru',
            'ketuakelas' => 'ketuakelas',
        ];
        $role = $roleMap[$user->jabatan] ?? 'guru';

        if ($role === 'ketuakelas') {
            $user->load('kelas');
        }

        $token = $user->createToken($role . '-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'role'    => $role,
            'user'    => $user,
            'token'   => $token
        ]);
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
