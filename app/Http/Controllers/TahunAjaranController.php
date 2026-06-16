<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $data = TahunAjaran::orderBy('tahun_mulai', 'desc')
            ->orderByRaw("FIELD(semester,'Genap','Ganjil')")
            ->get();
        return view('admin.tahun_ajaran', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_mulai'   => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100',
            'semester'      => 'required|in:Ganjil,Genap',
        ]);

        $name = $request->tahun_mulai . '/' . $request->tahun_selesai . ' ' . $request->semester;

        TahunAjaran::create([
            'id'            => (string) Str::uuid(),
            'name'          => $name,
            'tahun_mulai'   => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'semester'      => $request->semester,
            'is_active'     => false,
        ]);

        return redirect()->route('tahun-ajaran.index')->with('success', "Tahun Ajaran \"$name\" berhasil ditambahkan.");
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_mulai'   => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100',
            'semester'      => 'required|in:Ganjil,Genap',
        ]);

        $name = $request->tahun_mulai . '/' . $request->tahun_selesai . ' ' . $request->semester;
        $tahunAjaran->update([
            'name'          => $name,
            'tahun_mulai'   => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'semester'      => $request->semester,
        ]);

        return redirect()->route('tahun-ajaran.index')->with('success', "Tahun Ajaran berhasil diperbarui.");
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_active) {
            return redirect()->route('tahun-ajaran.index')->with('error', 'Tidak bisa menghapus Tahun Ajaran yang sedang aktif.');
        }
        $tahunAjaran->delete();
        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function setAktif(TahunAjaran $tahunAjaran)
    {
        // Nonaktifkan semua
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        // Aktifkan yang dipilih
        $tahunAjaran->update(['is_active' => true]);
        return redirect()->route('tahun-ajaran.index')->with('success', "Tahun Ajaran \"{$tahunAjaran->name}\" dijadikan aktif.");
    }
}
