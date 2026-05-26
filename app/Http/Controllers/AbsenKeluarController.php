<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AbsenKeluar;

class AbsenKeluarController extends Controller
{
    public function index()
    {
        $data = AbsenKeluar::with([
            'absenMasuk.guru', 
            'absenMasuk.kelas', 
            'absenMasuk.jadwalAjar.mapel'
        ])->latest()->paginate(15);
        return view('admin.absenkeluar', compact('data'));
    }

    public function create()
    {
        $absenMasuks = \App\Models\AbsenMasuk::all();
        return view('admin.absenkeluar_form', compact('absenMasuks'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        AbsenKeluar::create($data);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = AbsenKeluar::findOrFail($id);
        $absenMasuks = \App\Models\AbsenMasuk::all();
        return view('admin.absenkeluar_form', compact('data', 'absenMasuks'));
    }

    public function update(Request $request, $id)
    {
        $record = AbsenKeluar::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        AbsenKeluar::destroy($id);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil dihapus');
    }
}
