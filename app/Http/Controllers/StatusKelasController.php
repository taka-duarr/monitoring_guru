<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\StatusKelas;

class StatusKelasController extends Controller
{
    public function index()
    {
        $data = StatusKelas::with('kelas')->latest()->paginate(15);
        return view('admin.statuskelas', compact('data'));
    }

    public function create()
    {
        
        return view('admin.statuskelas_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        StatusKelas::create($data);
        return redirect()->route('statuskelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = StatusKelas::findOrFail($id);
        
        return view('admin.statuskelas_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = StatusKelas::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('statuskelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        StatusKelas::destroy($id);
        return redirect()->route('statuskelas.index')->with('success', 'Data berhasil dihapus');
    }
}
