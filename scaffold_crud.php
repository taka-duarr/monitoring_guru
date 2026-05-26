<?php
$models = [
    'Guru' => ['name', 'nik', 'jabatan'],
    'Kelas' => ['nama_kelas', 'tingkat'],
    'Jurusan' => ['nama_jurusan', 'singkatan'],
    'JadwalAjar' => ['hari', 'jam_mulai', 'jam_selesai'],
    'AbsenMasuk' => ['status', 'keterangan']
];

$routes = "\n// Generated CRUD Routes\n";
foreach ($models as $model => $fields) {
    $lower = strtolower($model);
    $routes .= "Route::resource('{$lower}', App\Http\Controllers\\{$model}Controller::class);\n";

    // 1. Create Controller
    $controllerCode = "<?php\nnamespace App\Http\Controllers;\nuse Illuminate\Http\Request;\nuse App\Models\\{$model};\n\nclass {$model}Controller extends Controller\n{\n";
    $controllerCode .= "    public function index()\n    {\n        \$data = {$model}::latest()->paginate(10);\n        return view('admin.{$lower}.index', compact('data'));\n    }\n}\n";
    file_put_contents(__DIR__ . "/app/Http/Controllers/{$model}Controller.php", $controllerCode);

    // 2. Create View Directory
    @mkdir(__DIR__ . "/resources/views/admin/{$lower}", 0777, true);

    // 3. Create Index View
    $th = implode("\n                            ", array_map(fn($f) => "<th class=\"px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider\">".ucfirst(str_replace('_', ' ', $f))."</th>", $fields));
    $td = implode("\n                            ", array_map(fn($f) => "<td class=\"px-6 py-4 whitespace-nowrap text-sm text-slate-700\">{{ \$row->$f }}</td>", $fields));

    $viewCode = "@extends('layouts.admin')
@section('title', 'Manajemen {$model}')
@section('page_title', 'Data {$model}')

@section('content')
<div class=\"bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100\">
    <div class=\"p-6 border-b border-slate-100 flex justify-between items-center\">
        <h3 class=\"text-lg font-bold text-slate-800\">Daftar {$model}</h3>
        <button class=\"px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors shadow-sm\">+ Tambah Data</button>
    </div>
    <div class=\"overflow-x-auto\">
        <table class=\"w-full\">
            <thead class=\"bg-slate-50 border-b border-slate-100\">
                <tr>
                    $th
                    <th class=\"px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider\">Aksi</th>
                </tr>
            </thead>
            <tbody class=\"divide-y divide-slate-100\">
                @forelse(\$data as \$row)
                <tr class=\"hover:bg-slate-50 transition-colors\">
                    $td
                    <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">
                        <a href=\"#\" class=\"text-blue-600 hover:text-blue-900 mr-3\">Edit</a>
                        <a href=\"#\" class=\"text-red-600 hover:text-red-900\">Hapus</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan=\"100%\" class=\"px-6 py-8 text-center text-slate-500\">
                        <svg class=\"w-12 h-12 mx-auto text-slate-300 mb-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path></svg>
                        Belum ada data $model.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class=\"p-4 border-t border-slate-100\">
        {{ \$data->links() }}
    </div>
</div>
@endsection";
    file_put_contents(__DIR__ . "/resources/views/admin/{$lower}/index.blade.php", $viewCode);
}

// Append routes
$web = file_get_contents(__DIR__ . '/routes/web.php');
$web = str_replace("})->name('dashboard');", "})->name('dashboard');\n    " . str_replace("\n", "\n    ", $routes), $web);
file_put_contents(__DIR__ . '/routes/web.php', $web);

echo "Scaffold generated successfully!";
