<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\User;
use App\Models\JadwalAjar;

class IzinController extends Controller
{
    public function index()
    {
        $data = Izin::with(['guru', 'jadwalAjar.mapel', 'jadwalAjar.guru', 'jadwalAjar.kelas'])
            ->latest()
            ->paginate(15);
        return view('admin.izin', compact('data'));
    }

    public function create()
    {
        $gurus = User::query()->where('jabatan', 'guru')->orderBy('name', 'asc')->get();
        $jadwals = JadwalAjar::with(['mapel', 'kelas'])->orderBy('hari')->get();
        return view('admin.izin_form', compact('gurus', 'jadwals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'jadwal_ajar_id' => 'nullable|exists:jadwal_ajars,id',
            'tanggal_izin' => 'required|date',
            'jam_izin' => 'required|string',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'approval' => 'required|boolean',
        ]);

        $data = $request->except('_token');
        $data['read'] = false;

        Izin::create($data);

        return redirect()->route('izin.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Izin::findOrFail($id);
        $gurus = User::query()->where('jabatan', 'guru')->orderBy('name', 'asc')->get();
        $jadwals = JadwalAjar::with(['mapel', 'kelas'])->orderBy('hari')->get();
        return view('admin.izin_form', compact('data', 'gurus', 'jadwals'));
    }

    public function update(Request $request, $id)
    {
        $record = Izin::findOrFail($id);

        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'jadwal_ajar_id' => 'nullable|exists:jadwal_ajars,id',
            'tanggal_izin' => 'required|date',
            'jam_izin' => 'required|string',
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'approval' => 'required|boolean',
        ]);

        $data = $request->except(['_token', '_method']);
        $record->update($data);

        return redirect()->route('izin.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Izin::destroy($id);
        return redirect()->route('izin.index')->with('success', 'Data berhasil dihapus');
    }
}

