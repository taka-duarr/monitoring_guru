<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\StatusKelas;

class StatusKelasController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today();
        // Ambil SEMUA kelas agar kepala sekolah bisa melihat daftar lengkap
        $data = \App\Models\Kelas::orderBy('name', 'asc')->paginate(20);
        
        foreach ($data as $kelas) {
            $status = StatusKelas::where('kelas_id', $kelas->id)
                                 ->whereDate('created_at', $today)
                                 ->first();
            $kelas->live_status = $status;
        }

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
