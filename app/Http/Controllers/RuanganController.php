<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function index()
    {
        $data = Ruangan::latest()->paginate(15);
        return view('admin.ruangan', compact('data'));
    }

    public function create()
    {
        
        return view('admin.ruangan_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Ruangan::create($data);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Ruangan::findOrFail($id);
        
        return view('admin.ruangan_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = Ruangan::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Ruangan::destroy($id);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil dihapus');
    }
}
