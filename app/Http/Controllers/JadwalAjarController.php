<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\JadwalAjar;

class JadwalAjarController extends Controller
{
    public function index()
    {
        $data = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])->latest()->paginate(15);
        return view('admin.jadwalajar', compact('data'));
    }

    public function create()
    {
        $gurus = \App\Models\User::where('jabatan', 'guru')->get();
        $mapels = \App\Models\Mapel::all();
        $kelas = \App\Models\Kelas::all();
        $ruangans = \App\Models\Ruangan::all();
        return view('admin.jadwalajar_form', compact('gurus', 'mapels', 'kelas', 'ruangans'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        JadwalAjar::create($data);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = JadwalAjar::findOrFail($id);
        $gurus = \App\Models\User::where('jabatan', 'guru')->get();
        $mapels = \App\Models\Mapel::all();
        $kelas = \App\Models\Kelas::all();
        $ruangans = \App\Models\Ruangan::all();
        return view('admin.jadwalajar_form', compact('data', 'gurus', 'mapels', 'kelas', 'ruangans'));
    }

    public function update(Request $request, $id)
    {
        $record = JadwalAjar::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        JadwalAjar::destroy($id);
        return redirect()->route('jadwalajar.index')->with('success', 'Data berhasil dihapus');
    }

    public function export(Request $request)
    {
        $format = $request->query('format', 'excel');
        $data = JadwalAjar::with(['guru', 'mapel', 'kelas', 'ruangan'])->orderBy('hari', 'asc')->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JadwalAjarExport($data), 'data-jadwalajar-' . date('Y-m-d') . '.xlsx');
            }
        }
        return redirect()->route('jadwalajar.index')->with('error', 'Format tidak didukung');
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JadwalAjarExport(null, true), 'template-import-jadwalajar.xlsx');
        }

        return redirect()->route('jadwalajar.index')->with('error', 'Fitur export tidak tersedia');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\JadwalAjarImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('jadwalajar.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('jadwalajar.index')->with('import_errors', $errors);
        }

        return redirect()->route('jadwalajar.index')->with('success', 'Data jadwal mengajar berhasil di-import massal.');
    }
}
