<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kelas;
use App\Models\Murid;
use Illuminate\Support\Str;

class MuridController extends Controller
{
    public function index(Kelas $kelas)
    {
        $murids = $kelas->murids()->orderBy('name')->get();
        return view('admin.murid.index', compact('kelas', 'murids'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nis' => 'required|unique:murids,nis',
            'name' => 'required|string|max:255',
            'no_absen' => 'nullable|integer',
        ]);

        $kelas->murids()->create([
            'id' => Str::uuid(),
            'nis' => $request->nis,
            'name' => $request->name,
            'no_absen' => $request->no_absen,
        ]);

        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Murid berhasil ditambahkan');
    }

    public function update(Request $request, Kelas $kelas, Murid $murid)
    {
        $request->validate([
            'nis' => 'required|unique:murids,nis,' . $murid->id,
            'name' => 'required|string|max:255',
            'no_absen' => 'nullable|integer',
        ]);

        $murid->update([
            'nis' => $request->nis,
            'name' => $request->name,
            'no_absen' => $request->no_absen,
        ]);

        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Data murid berhasil diperbarui');
    }

    public function destroy(Kelas $kelas, Murid $murid)
    {
        $murid->delete();
        return redirect()->route('kelas.murid.index', $kelas->id)->with('success', 'Murid berhasil dihapus');
    }
}
