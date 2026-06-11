<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil saya.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Perbarui data profil (nama, no_telp, password, foto).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];

        // Password hanya divalidasi jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        }

        $request->validate($rules);

        // Update data
        $user->name = $request->input('name');
        $user->no_telp = $request->input('no_telp');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Tangani upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru ke folder 'profil' di disk 'public'
            $path = $request->file('foto')->store('profil', 'public');
            $user->foto = $path;
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}

