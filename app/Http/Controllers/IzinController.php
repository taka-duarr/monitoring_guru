<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Izin;

class IzinController extends Controller
{
    public function index()
    {
        $data = Izin::latest()->paginate(15);
        return view('admin.izin', compact('data'));
    }

    public function create()
    {
        
        return view('admin.izin_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Izin::create($data);
        return redirect()->route('izin.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Izin::findOrFail($id);
        
        return view('admin.izin_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = Izin::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('izin.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Izin::destroy($id);
        return redirect()->route('izin.index')->with('success', 'Data berhasil dihapus');
    }
}
