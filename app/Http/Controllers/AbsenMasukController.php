<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AbsenMasuk;
use App\Models\Kelas;

class AbsenMasukController extends Controller
{
    public function index(Request $request)
    {
        $data = AbsenMasuk::with(['guru', 'kelas', 'jadwalAjar.mapel', 'absenKeluar'])
            ->when($request->filled('guru'), function ($q) use ($request) {
                $q->whereHas('guru', fn($g) => $g->where('name', 'LIKE', '%' . $request->guru . '%'));
            })
            ->when($request->filled('kelas_id'), function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            })
            ->when($request->filled('tanggal'), function ($q) use ($request) {
                $q->where('tanggal', $request->tanggal);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $allKelas = Kelas::orderBy('grade')->orderBy('name')->get();

        return view('admin.absenmasuk', compact('data', 'allKelas'));
    }

    public function create()
    {
        
        return view('admin.absenmasuk_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        AbsenMasuk::create($data);
        return redirect()->route('absenmasuk.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = AbsenMasuk::findOrFail($id);
        
        return view('admin.absenmasuk_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = AbsenMasuk::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('absenmasuk.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy(AbsenMasuk $absenmasuk)
    {
        $absenmasuk->delete();
        return redirect()->route('absenmasuk.index')->with('success', 'Data berhasil dihapus');
    }

    public function murid(AbsenMasuk $absenmasuk)
    {
        $murids = \App\Models\Murid::where('kelas_id', $absenmasuk->kelas_id)->orderBy('no_absen')->orderBy('name')->get();
        $absenMurids = \App\Models\AbsenMurid::where('absen_masuk_id', $absenmasuk->id)->get()->keyBy('murid_id');
        
        return view('admin.absenmasuk.murid', compact('absenmasuk', 'murids', 'absenMurids'));
    }
}
