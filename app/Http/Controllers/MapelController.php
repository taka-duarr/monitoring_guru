<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Mapel;

class MapelController extends Controller
{
    public function index()
    {
        $data = Mapel::latest()->paginate(15);
        return view('admin.mapel', compact('data'));
    }

    public function create()
    {
        $jurusans = \App\Models\Jurusan::all();
        return view('admin.mapel_form', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Mapel::create($data);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Mapel::findOrFail($id);
        $jurusans = \App\Models\Jurusan::all();
        return view('admin.mapel_form', compact('data', 'jurusans'));
    }

    public function update(Request $request, $id)
    {
        $record = Mapel::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Mapel::destroy($id);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil dihapus');
    }
}
