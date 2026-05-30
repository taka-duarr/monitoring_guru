<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Angkatan;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index()
    {
        $data = Kelas::latest()->paginate(15);
        return view('admin.kelas', compact('data'));
    }

    public function create()
    {
        $jurusans = \App\Models\Jurusan::all();
        $angkatans = \App\Models\Angkatan::all();
        return view('admin.kelas_form', compact('jurusans', 'angkatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'name' => 'required|string|max:255',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'grade' => 'required|in:10,11,12',
        ]);

        Kelas::create([
            'id' => Str::uuid(),
            'jurusan_id' => $request->jurusan_id,
            'name' => $request->name,
            'angkatan_id' => $request->angkatan_id,
            'grade' => $request->grade,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jurusans = Jurusan::all();
        $angkatans = Angkatan::all();
        $data = Kelas::findOrFail($id);
        return view('admin.kelas_form', compact('data', 'jurusans', 'angkatans'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'name' => 'required|string|max:255',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'grade' => 'required|in:10,11,12',
        ]);

        $kelas->update([
            'jurusan_id' => $request->jurusan_id,
            'name' => $request->name,
            'angkatan_id' => $request->angkatan_id,
            'grade' => $request->grade,
        ]);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil dihapus');
    }
}
