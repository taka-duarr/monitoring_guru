<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KetuaKelasController extends Controller
{
    public function index()
    {
        $data = User::where('jabatan', 'ketuakelas')->latest()->paginate(15);
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
            'name'     => 'required|string|max:255',
            'nik'      => 'required|numeric|unique:users,nik',
            'kelas_id' => 'required'
        ], [
            'nik.unique' => 'NIK/NIS sudah terdaftar, silakan gunakan yang lain.'
        ]);

        $data = $request->except('_token');
        $data['jabatan'] = 'ketuakelas';
        if (empty($data['password'])) {
            $data['password'] = Hash::make($data['nik']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        User::create($data);
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = User::where('jabatan', 'ketuakelas')->findOrFail($id);
        $kelass = \App\Models\Kelas::all();
        return view('admin.ketuakelas_form', compact('data', 'kelass'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nik'      => 'required|numeric|unique:users,nik,' . $id,
            'kelas_id' => 'required'
        ], [
            'nik.unique' => 'NIK/NIS sudah terdaftar, silakan gunakan yang lain.'
        ]);

        $record = User::where('jabatan', 'ketuakelas')->findOrFail($id);
        $data = $request->except(['_token', '_method']);

        if (isset($data['nik']) && $data['nik'] !== $record->nik) {
            $data['password'] = Hash::make($data['nik']);
        } elseif (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $record->update($data);
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        User::where('jabatan', 'ketuakelas')->findOrFail($id)->delete();
        return redirect()->route('ketuakelas.index')->with('success', 'Data berhasil dihapus');
    }
}
