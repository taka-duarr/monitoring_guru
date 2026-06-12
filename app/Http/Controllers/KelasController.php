<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Angkatan;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index()
    {
        $data = Kelas::latest()->paginate(15);
        return view('admin.kelas', compact('data'));
    }

    public function create()
    {
        $jurusans = \App\Models\Jurusan::all();
        $angkatans = \App\Models\Angkatan::all();
        return view('admin.kelas_form', compact('jurusans', 'angkatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'name' => 'required|string|max:255',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'grade' => 'required|in:10,11,12',
            'is_active' => 'required|boolean',
        ]);

        Kelas::create([
            'id' => Str::uuid(),
            'jurusan_id' => $request->jurusan_id,
            'name' => $request->name,
            'angkatan_id' => $request->angkatan_id,
            'grade' => $request->grade,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jurusans = Jurusan::all();
        $angkatans = Angkatan::all();
        $data = Kelas::findOrFail($id);
        return view('admin.kelas_form', compact('data', 'jurusans', 'angkatans'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'name' => 'required|string|max:255',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'grade' => 'required|in:10,11,12',
            'is_active' => 'required|boolean',
        ]);

        $kelas->update([
            'jurusan_id' => $request->jurusan_id,
            'name' => $request->name,
            'angkatan_id' => $request->angkatan_id,
            'grade' => $request->grade,
            'is_active' => $request->is_active,
        ]);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return redirect()->route('kelas.index')->with('success', 'Data berhasil dihapus');
    }
    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $kelas = Kelas::with(['jurusan', 'angkatan'])->orderBy('grade', 'asc')->orderBy('name', 'asc')->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KelasExport(), 'data-kelas-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-kelas-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($kelas) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Kelas', 'Tingkat (Grade)', 'Jurusan', 'Angkatan']);
                foreach ($kelas as $index => $k) {
                    fputcsv($file, [
                        $index + 1,
                        $k->name,
                        $k->grade,
                        $k->jurusan ? $k->jurusan->name : '-',
                        $k->angkatan ? $k->angkatan->name : '-'
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.kelas_pdf', compact('kelas'));
            return $pdf->download('data-kelas-' . date('Y-m-d') . '.pdf');
        }

        return view('admin.kelas_pdf', compact('kelas'));
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KelasExport(true), 'template-import-kelas.xlsx');
        }

        // Fallback if Excel not installed
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-kelas.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama Kelas', 'Tingkat Grade', 'Jurusan', 'Angkatan']);
            fputcsv($file, ['X RPL 1', '10', 'Rekayasa Perangkat Lunak', 'Angkatan 2026']);
            fputcsv($file, ['XI TKJ 2', '11', 'Teknik Komputer dan Jaringan', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\KelasImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('kelas.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('kelas.index')->with('import_errors', $errors);
        }

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil di-import massal.');
    }
}
