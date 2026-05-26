<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index()
    {
        $data = Jurusan::latest()->paginate(15);
        return view('admin.jurusan', compact('data'));
    }

    public function create()
    {
        
        return view('admin.jurusan_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Jurusan::create($data);
        return redirect()->route('jurusan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Jurusan::findOrFail($id);
        
        return view('admin.jurusan_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = Jurusan::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('jurusan.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Jurusan::destroy($id);
        return redirect()->route('jurusan.index')->with('success', 'Data berhasil dihapus');
    }
}
