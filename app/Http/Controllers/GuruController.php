<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Exports\GuruExport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruController extends Controller
{
    /**
     * Display a filtered, sorted, and paginated listing of teachers.
     */
    public function index(Request $request)
    {
        // 1. Gather all filters
        $filters = $request->only(['search', 'status', 'mapel', 'kelas']);
        
        // 2. Sorting parameters
        $sort = $request->query('sort', 'name');
        $dir = $request->query('dir', 'asc');
        
        // Allowed sort columns
        $allowedSort = ['name', 'nik', 'status', 'jabatan'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'name';
        }
        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        // 3. Paginate settings
        $perPage = (int) $request->query('per_page', 25);
        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 25;
        }

        // 4. Fetch data with relationships loaded to optimize N+1 queries
        $data = Guru::filter($filters)
            ->orderBy($sort, $dir)
            ->paginate($perPage)
            ->appends($request->query());

        // 6. Calculate active filter count
        $activeFilterCount = collect($filters)->filter()->count();

        return view('admin.guru', compact(
            'data',
            'filters',
            'sort',
            'dir',
            'perPage',
            'activeFilterCount'
        ));
    }

    /**
     * Export teachers data based on current filters.
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $filters = $request->only(['search', 'status']);

        $gurus = Guru::filter($filters)->get();

        if ($format === 'excel') {
            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new GuruExport($filters), 'data-guru-' . date('Y-m-d') . '.xlsx');
            }
            
            // Fallback: direct CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data-guru-' . date('Y-m-d') . '.csv"',
            ];
            $callback = function() use ($gurus) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['No', 'Nama Lengkap', 'NIK / NIP', 'Status', 'Jabatan']);
                foreach ($gurus as $index => $guru) {
                    fputcsv($file, [
                        $index + 1,
                        $guru->name,
                        ' ' . $guru->nik,
                        $guru->status,
                        $guru->jabatan
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // PDF Format
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.guru_pdf', compact('gurus'));
            return $pdf->download('data-guru-' . date('Y-m-d') . '.pdf');
        }

        // Fallback: printer-friendly HTML view
        return view('admin.guru_pdf', compact('gurus'));
    }

    /**
     * Show form to create a new teacher.
     */
    public function create()
    {
        return view('guru.create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(\App\Http\Requests\GuruRequest $request)
    {
        $data = $request->only([
            'name', 'nik', 'jenis_kelamin',
            'no_telp', 'status'
        ]);

        $data['jabatan'] = 'guru';
        $data['password'] = Hash::make($request->nik); // default password is NIK

        // Handle profile photo upload if exists
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('guru/foto', 'public');
            $data['foto'] = $path;
        }

        Guru::create($data);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Show form to edit an existing teacher.
     */
    public function edit($id)
    {
        $data = Guru::findOrFail($id);
        return view('guru.edit', compact('data'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(\App\Http\Requests\GuruRequest $request, $id)
    {
        $record = Guru::findOrFail($id);

        $data = $request->only([
            'name', 'nik', 'jenis_kelamin',
            'no_telp', 'status'
        ]);

        // If NIK changed, update password to reflect new NIK
        if ($request->nik !== $record->nik) {
            $data['password'] = Hash::make($request->nik);
        }

        // Handle profile photo upload if exists
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('guru/foto', 'public');
            $data['foto'] = $path;
        }

        $record->update($data);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        // Force delete associated schedules to keep database clean
        $guru->jadwalAjars()->forceDelete();
        $guru->delete();
        
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

    /**
     * Process bulk import of teachers from CSV/Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $import = new \App\Imports\GuruImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Terjadi kesalahan membaca berkas import: ' . $e->getMessage());
        }

        $errors = $import->getErrors();

        if (count($errors) > 0) {
            return redirect()->route('guru.index')->with('import_errors', $errors);
        }

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil di-import massal.');
    }

    /**
     * Download the CSV import template with sample data.
     */
    public function downloadTemplate()
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new GuruExport([], true), 'template-import-guru.xlsx');
        }

        // Fallback jika Excel tidak tersedia
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-guru.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Nama Lengkap', 'NIP', 'Jenis Kelamin', 
                'Nomor Telepon', 'Status'
            ]);

            fputcsv($file, [
                'Budi Hartono, S.Pd.', ' 198505202010011001', 'Laki-laki', 
                '081234567890', 'Aktif'
            ]);

            fputcsv($file, [
                'Siti Aminah, S.Pd.', ' 199009122018022002', 'Perempuan', 
                '089876543210', 'Aktif'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
