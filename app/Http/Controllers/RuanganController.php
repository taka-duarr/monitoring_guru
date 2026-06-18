<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::latest();
        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }
        $data = $query->paginate(15)->appends($request->query());
        return view('admin.ruangan', compact('data'));
    }

    public function create()
    {
        
        return view('admin.ruangan_form');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
        Ruangan::create($data);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Ruangan::findOrFail($id);
        
        return view('admin.ruangan_form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $record = Ruangan::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $record->update($data);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id)
    {
        Ruangan::destroy($id);
        return redirect()->route('ruangan.index')->with('success', 'Data berhasil dihapus');
    }

    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $ruangans = Ruangan::orderBy('name', 'asc')->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RuanganExport(), 'data-ruangan-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-ruangan-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($ruangans) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Ruangan']);
                foreach ($ruangans as $index => $r) {
                    fputcsv($file, [
                        $index + 1,
                        $r->name
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ruangan_pdf', compact('ruangans'));
            return $pdf->download('data-ruangan-' . date('Y-m-d') . '.pdf');
        }

        return view('admin.ruangan_pdf', compact('ruangans'));
    }

    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RuanganExport(true), 'template-import-ruangan.xlsx');
        }

        // Fallback if Excel not installed
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-ruangan.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Nama Ruangan']);
            fputcsv($file, ['Ruang Teori 1']);
            fputcsv($file, ['Lab Komputer 1']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\RuanganImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('ruangan.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('ruangan.index')->with('import_errors', $errors);
        }

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil di-import massal.');
    }
}
