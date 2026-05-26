<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\KetuaKelas;

class KetuaKelasController extends Controller
{
    public function index()
    {
        $data = KetuaKelas::latest()->paginate(15);
        return view('admin.ketuakelas', compact('data'));
    }

    public function create()
    {
        $kelass = \App\Models\Kelas::all();
        return view('admin.ketuakelas_form', compact('kelass'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (empty($data['password'])) {
            $data['password'] = bcrypt($data['nisn']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        KetuaKelas::create($data);
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = KetuaKelas::findOrFail($id);
        $kelass = \App\Models\Kelas::all();
        return view('admin.ketuakelas_form', compact('data', 'kelass'));
    }

    public function update(Request $request, $id)
    {
        $record = KetuaKelas::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        KetuaKelas::destroy($id);
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil dihapus');
    }
}
