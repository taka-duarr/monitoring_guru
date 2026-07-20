<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $role = Auth::user()->jabatan;

            if ($role === 'guru') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'nik' => 'Akses ditolak. Guru hanya dapat login melalui aplikasi mobile.',
                ])->onlyInput('nik');
            }

            $request->session()->regenerate();
            
            if ($role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($role === 'ketuakelas') {
                return redirect()->intended('/siswa/dashboard');
            }
            return redirect('/');
        }

        return back()->withErrors([
            'nik' => 'NIK atau password salah.',
        ])->onlyInput('nik');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
