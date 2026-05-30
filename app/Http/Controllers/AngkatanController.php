<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use App\Models\Angkatan;

class AngkatanController extends Controller
{
    public function index()
    {
        $data = Angkatan::latest()->paginate(10);
        return view('admin.angkatan', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Angkatan::create([
            'id' => Str::uuid(),
            'name' => $request->name,
        ]);
        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil ditambahkan.');
    }

    public function update(Request $request, Angkatan $angkatan)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $angkatan->update(['name' => $request->name]);
        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil diubah.');
    }

    public function destroy(Angkatan $angkatan)
    {
        $angkatan->delete();
        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil dihapus.');
    }
}
