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
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:ketua_kelas,nik',
            'kelas_id' => 'required'
        ], [
            'nik.unique' => 'NIK/NIS sudah terdaftar, silakan gunakan yang lain.'
        ]);

        $data = $request->except('_token');
        if (empty($data['password'])) {
            $data['password'] = bcrypt($data['nik']);
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
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:ketua_kelas,nik,' . $id,
            'kelas_id' => 'required'
        ], [
            'nik.unique' => 'NIK/NIS sudah terdaftar, silakan gunakan yang lain.'
        ]);

        $record = KetuaKelas::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        
        // Reset password jika NIK diubah
        if (isset($data['nik']) && $data['nik'] !== $record->nik) {
            $data['password'] = bcrypt($data['nik']);
        } elseif (isset($data['password']) && $data['password']) {
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
