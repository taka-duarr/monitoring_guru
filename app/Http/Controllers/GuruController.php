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
        $record = Guru::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
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
