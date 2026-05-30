<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $data = User::whereIn('jabatan', ['guru', 'admin'])->latest()->paginate(15);
        return view('admin.guru', compact('data'));
    }

    public function create()
    {
        return view('admin.guru_form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'nik'     => 'required|numeric|unique:users,nik',
            'jabatan' => 'required'
        ], [
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.'
        ]);

        $data = $request->except('_token');
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['nik']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        User::create($data);
        return redirect()->route('guru.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = User::whereIn('jabatan', ['guru', 'admin'])->findOrFail($id);
        return view('admin.guru_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'nik'     => 'required|numeric|unique:users,nik,' . $id,
            'jabatan' => 'required'
        ], [
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.'
        ]);

        $record = User::whereIn('jabatan', ['guru', 'admin'])->findOrFail($id);
        $data = $request->except(['_token', '_method']);
        
        if (isset($data['nik']) && $data['nik'] !== $record->nik) {
            $data['password'] = Hash::make($data['nik']);
        } elseif (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $record->update($data);
        return redirect()->route('guru.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        User::whereIn('jabatan', ['guru', 'admin'])->findOrFail($id)->delete();
        return redirect()->route('guru.index')->with('success', 'Data berhasil dihapus');
    }
}
