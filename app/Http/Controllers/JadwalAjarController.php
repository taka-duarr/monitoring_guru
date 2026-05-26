<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\JadwalAjar;

class JadwalAjarController extends Controller
{
    public function index()
    {
        $data = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])->latest()->paginate(15);
        return view('admin.jadwalajar', compact('data'));
    }

    public function create()
    {
        $gurus = \App\Models\Guru::where('jabatan', 'guru')->get();
        $mapels = \App\Models\Mapel::all();
        $kelas = \App\Models\Kelas::all();
        $ruangans = \App\Models\Ruangan::all();
        return view('admin.jadwalajar_form', compact('gurus', 'mapels', 'kelas', 'ruangans'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        JadwalAjar::create($data);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = JadwalAjar::findOrFail($id);
        $gurus = \App\Models\Guru::where('jabatan', 'guru')->get();
        $mapels = \App\Models\Mapel::all();
        $kelas = \App\Models\Kelas::all();
        $ruangans = \App\Models\Ruangan::all();
        return view('admin.jadwalajar_form', compact('data', 'gurus', 'mapels', 'kelas', 'ruangans'));
    }

    public function update(Request $request, $id)
    {
        $record = JadwalAjar::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        JadwalAjar::destroy($id);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil dihapus');
    }
}
