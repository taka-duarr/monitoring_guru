<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Guru;

class GuruController extends Controller
{
    public function index()
    {
        $data = Guru::latest()->paginate(15);
        return view('admin.guru', compact('data'));
    }

    public function create()
    {
        
        return view('admin.guru_form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:gurus,nik',
            'jabatan' => 'required'
        ], [
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.'
        ]);

        $data = $request->except('_token');
        if (empty($data['password'])) {
            $data['password'] = bcrypt($data['nik']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        Guru::create($data);
        return redirect()->route('guru.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Guru::findOrFail($id);
        
        return view('admin.guru_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:gurus,nik,' . $id,
            'jabatan' => 'required'
        ], [
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.'
        ]);

        $record = Guru::findOrFail($id);
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
        return redirect()->route('guru.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Guru::destroy($id);
        return redirect()->route('guru.index')->with('success', 'Data berhasil dihapus');
    }
}
