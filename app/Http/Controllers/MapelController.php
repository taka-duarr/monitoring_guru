<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Mapel;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::latest();
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(15)->appends($request->query());
        return view('admin.mapel', compact('data'));
    }

    public function create()
    {
        $jurusans = \App\Models\Jurusan::all();
        return view('admin.mapel_form', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Mapel::create($data);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Mapel::findOrFail($id);
        $jurusans = \App\Models\Jurusan::all();
        return view('admin.mapel_form', compact('data', 'jurusans'));
    }

    public function update(Request $request, $id)
    {
        $record = Mapel::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Mapel::destroy($id);
        return redirect()->route('mapel.index')->with('success', 'Data berhasil dihapus');
    }
    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $mapels = Mapel::with('jurusan')->orderBy('name', 'asc')->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MapelExport(), 'data-mapel-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-mapel-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($mapels) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Mata Pelajaran', 'Kategori Jurusan']);
                foreach ($mapels as $index => $mapel) {
                    fputcsv($file, [
                        $index + 1,
                        $mapel->name,
                        $mapel->jurusan ? $mapel->jurusan->name : 'Umum / Semua Jurusan'
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.mapel_pdf', compact('mapels'));
            return $pdf->download('data-mapel-' . date('Y-m-d') . '.pdf');
        }

        return view('admin.mapel_pdf', compact('mapels'));
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MapelExport(true), 'template-import-mapel.xlsx');
        }

        // Fallback if Excel not installed
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-mapel.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama Mata Pelajaran', 'Kategori Jurusan']);
            fputcsv($file, ['Matematika Lanjut', 'Rekayasa Perangkat Lunak']);
            fputcsv($file, ['Sejarah Indonesia', 'Teknik Komputer dan Jaringan']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\MapelImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('mapel.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('mapel.index')->with('import_errors', $errors);
        }

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil di-import massal.');
    }
}
