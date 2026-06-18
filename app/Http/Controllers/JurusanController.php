<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jurusan::latest();
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode_jurusan', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(15)->appends($request->query());
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
    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $jurusans = Jurusan::orderBy('name', 'asc')->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JurusanExport(), 'data-jurusan-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-jurusan-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($jurusans) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Jurusan', 'Kode Jurusan']);
                foreach ($jurusans as $index => $j) {
                    fputcsv($file, [
                        $index + 1,
                        $j->name,
                        $j->kode_jurusan ?: '-'
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.jurusan_pdf', compact('jurusans'));
            return $pdf->download('data-jurusan-' . date('Y-m-d') . '.pdf');
        }

        return view('admin.jurusan_pdf', compact('jurusans'));
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JurusanExport(true), 'template-import-jurusan.xlsx');
        }

        // Fallback if Excel not installed
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-jurusan.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama Jurusan', 'Kode Jurusan']);
            fputcsv($file, ['Rekayasa Perangkat Lunak', 'RPL']);
            fputcsv($file, ['Teknik Komputer dan Jaringan', 'TKJ']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\JurusanImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('jurusan.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('jurusan.index')->with('import_errors', $errors);
        }

        return redirect()->route('jurusan.index')->with('success', 'Data jurusan berhasil di-import massal.');
    }
}
