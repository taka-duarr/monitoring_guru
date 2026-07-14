<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        // no_telp can be nullable
        $request->validate([
            'name' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'password' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $request->name;
        
        if ($request->has('no_telp')) {
            $phone = preg_replace('/[^0-9]/', '', $request->no_telp);
            if (str_starts_with($phone, '620')) {
                $phone = '62' . substr($phone, 3);
            } elseif (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $user->no_telp = $phone;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $file = $request->file('foto');
            $path = $file->store('guru/foto', 'public');
            $user->foto = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }
}
