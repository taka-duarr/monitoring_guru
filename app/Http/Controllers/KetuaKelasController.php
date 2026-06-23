<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KetuaKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('jabatan', 'ketuakelas')->with('kelas.angkatan')->latest();

        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('grade', $request->grade);
            });
        }

        if ($request->filled('angkatan_id')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('angkatan_id', $request->angkatan_id);
            });
        }

        $data = $query->paginate(15)->appends($request->query());
        
        $angkatans = \App\Models\Angkatan::orderBy('name')->get();
        // Option grades: X, XI, XII dll
        $grades = \App\Models\Kelas::select('grade')->distinct()->whereNotNull('grade')->orderBy('grade')->pluck('grade');

        return view('admin.ketuakelas', compact('data', 'angkatans', 'grades'));
    }

    public function create()
    {
        $kelass = \App\Models\Kelas::with('angkatan')->get();
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
        $kelass = \App\Models\Kelas::with('angkatan')->get();
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

    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        
        $query = User::where('jabatan', 'ketuakelas')->with('kelas.angkatan')->orderBy('name', 'asc');
        
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('grade', $request->grade);
            });
        }

        if ($request->filled('angkatan_id')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('angkatan_id', $request->angkatan_id);
            });
        }

        $ketuakelas = $query->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KetuaKelasExport(), 'data-ketuakelas-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-ketuakelas-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($ketuakelas) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Lengkap', 'NIS', 'Kelas', 'Tingkat', 'Angkatan']);
                foreach ($ketuakelas as $index => $k) {
                    fputcsv($file, [
                        $index + 1,
                        $k->name,
                        $k->nik,
                        $k->kelas ? $k->kelas->name : '-',
                        $k->kelas && $k->kelas->grade ? $k->kelas->grade : '-',
                        $k->kelas && $k->kelas->angkatan ? $k->kelas->angkatan->name : '-'
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ketuakelas_pdf', compact('ketuakelas'));
            return $pdf->download('data-ketuakelas-' . date('Y-m-d') . '.pdf');
        }

        return view('admin.ketuakelas_pdf', compact('ketuakelas'));
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KetuaKelasExport(true), 'template-import-ketuakelas.xlsx');
        }

        // Fallback
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-ketuakelas.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama Lengkap', 'NIS', 'Kelas']);
            fputcsv($file, ['Andi Firmansyah', '1012345678', 'X RPL 1']);
            fputcsv($file, ['Budi Santoso', '1012345679', 'XI TKJ 2']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\KetuaKelasImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('ketuakelas.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('ketuakelas.index')->with('import_errors', $errors);
        }

        return redirect()->route('ketuakelas.index')->with('success', 'Data ketua kelas berhasil di-import massal.');
    }
}
