<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kelas;

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
        return view('admin.kelas_form', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Kelas::create($data);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Kelas::findOrFail($id);
        $jurusans = \App\Models\Jurusan::all();
        return view('admin.kelas_form', compact('data', 'jurusans'));
    }

    public function update(Request $request, $id)
    {
        $record = Kelas::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil dihapus');
    }
}
