<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AbsenKeluar;
use App\Models\Kelas;

class AbsenKeluarController extends Controller
{
    public function index(Request $request)
    {
        $data = AbsenKeluar::with([
            'absenMasuk.guru',
            'absenMasuk.kelas',
            'absenMasuk.jadwalAjar.mapel'
        ])
            ->when($request->filled('guru'), function ($q) use ($request) {
                $q->whereHas('absenMasuk.guru', fn($g) => $g->where('name', 'LIKE', '%' . $request->guru . '%'));
            })
            ->when($request->filled('kelas_id'), function ($q) use ($request) {
                $q->whereHas('absenMasuk', fn($m) => $m->where('kelas_id', $request->kelas_id));
            })
            ->when($request->filled('tanggal'), function ($q) use ($request) {
                $q->whereHas('absenMasuk', fn($m) => $m->where('tanggal', $request->tanggal));
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $allKelas = Kelas::orderBy('name')->get();

        return view('admin.absenkeluar', compact('data', 'allKelas'));
    }

    public function create()
    {
        $absenMasuks = \App\Models\AbsenMasuk::all();
        return view('admin.absenkeluar_form', compact('absenMasuks'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        AbsenKeluar::create($data);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = AbsenKeluar::findOrFail($id);
        $absenMasuks = \App\Models\AbsenMasuk::all();
        return view('admin.absenkeluar_form', compact('data', 'absenMasuks'));
    }

    public function update(Request $request, $id)
    {
        $record = AbsenKeluar::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        AbsenKeluar::destroy($id);
        return redirect()->route('absenkeluar.index')->with('success', 'Data berhasil dihapus');
    }
}
