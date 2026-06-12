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

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'no_telp.string' => 'Nomor telepon harus berupa teks.',
            'no_telp.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'foto.image' => 'Berkas harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa jpeg, png, jpg, gif, atau svg.',
            'foto.max' => 'Ukuran foto tidak boleh lebih dari 2048 kilobytes (2 MB).',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $request->validate($rules, $messages);

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

