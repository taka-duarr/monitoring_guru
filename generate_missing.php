<?php

$pages = [
    'KetuaKelas' => [
        'route' => 'ketuakelas',
        'title' => 'Ketua Kelas',
        'columns' => [
            ['field' => 'nama',   'label' => 'Nama'],
            ['field' => 'nisn',   'label' => 'NISN'],
            ['field' => 'kelas_id','label'=> 'ID Kelas'],
        ],
    ],
    'Mapel' => [
        'route' => 'mapel',
        'title' => 'Mata Pelajaran',
        'columns' => [
            ['field' => 'nama_mapel', 'label' => 'Nama Mapel'],
            ['field' => 'kode_mapel', 'label' => 'Kode Mapel'],
        ],
    ],
    'Ruangan' => [
        'route' => 'ruangan',
        'title' => 'Ruangan',
        'columns' => [
            ['field' => 'nama_ruangan', 'label' => 'Nama Ruangan'],
        ],
    ],
    'Izin' => [
        'route' => 'izin',
        'title' => 'Pengajuan Izin',
        'columns' => [
            ['field' => 'jadwal_ajar_id', 'label' => 'ID Jadwal'],
            ['field' => 'alasan',         'label' => 'Alasan'],
            ['field' => 'status',         'label' => 'Status'],
            ['field' => 'created_at',     'label' => 'Tanggal'],
        ],
    ],
    'AbsenKeluar' => [
        'route' => 'absenkeluar',
        'title' => 'Rekap Absen Keluar',
        'columns' => [
            ['field' => 'absen_masuk_id', 'label' => 'ID Absen Masuk'],
            ['field' => 'waktu_keluar',   'label' => 'Waktu Keluar'],
            ['field' => 'status',         'label' => 'Status'],
        ],
    ],
    'StatusKelas' => [
        'route' => 'statuskelas',
        'title' => 'Status Kelas',
        'columns' => [
            ['field' => 'kelas_id', 'label' => 'ID Kelas'],
            ['field' => 'status',   'label' => 'Status'],
            ['field' => 'created_at','label'=> 'Tanggal'],
        ],
    ],
];

$viewDir = __DIR__ . '/resources/views/admin/';
$ctrlDir = __DIR__ . '/app/Http/Controllers/';

foreach ($pages as $model => $cfg) {
    $route = $cfg['route'];
    $title = $cfg['title'];
    $cols  = $cfg['columns'];

    // --- Controller ---
    $ctrlCode  = "<?php\nnamespace App\\Http\\Controllers;\nuse Illuminate\\Http\\Request;\nuse App\\Models\\{$model};\n\nclass {$model}Controller extends Controller\n{\n";
    $ctrlCode .= "    public function index()\n    {\n";
    $ctrlCode .= "        \$data = {$model}::latest()->paginate(15);\n";
    $ctrlCode .= "        return view('admin.{$route}', compact('data'));\n";
    $ctrlCode .= "    }\n}\n";
    file_put_contents($ctrlDir . "{$model}Controller.php", $ctrlCode);

    // --- View ---
    $ths = '';
    $tds = '';
    foreach ($cols as $col) {
        $ths .= "                    <th class=\"px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider\">{$col['label']}</th>\n";
        $tds .= "                    <td class=\"px-6 py-4 whitespace-nowrap text-sm text-slate-700\">{{ \$row->{$col['field']} }}</td>\n";
    }

    $viewCode = "@extends('layouts.admin')
@section('title', '{$title} - Monitoring Guru')
@section('page_title', 'Data {$title}')

@section('content')
<div class=\"bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100\">
    <div class=\"p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3\">
        <div>
            <h3 class=\"text-lg font-bold text-slate-800\">Daftar {$title}</h3>
            <p class=\"text-sm text-slate-500 mt-0.5\">Total: <strong>{{ \$data->total() }}</strong> data</p>
        </div>
        <a href=\"#\" class=\"inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-medium hover:bg-brand-700 transition-colors shadow-sm\">
            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 4v16m8-8H4\"></path></svg>
            Tambah Data
        </a>
    </div>
    <div class=\"overflow-x-auto\">
        <table class=\"w-full\">
            <thead class=\"bg-slate-50 border-b border-slate-100\">
                <tr>
{$ths}                    <th class=\"px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider\">Aksi</th>
                </tr>
            </thead>
            <tbody class=\"divide-y divide-slate-100\">
                @forelse(\$data as \$row)
                <tr class=\"hover:bg-slate-50/70 transition-colors\">
{$tds}                    <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3\">
                        <a href=\"#\" class=\"text-blue-600 hover:text-blue-800 font-semibold\">Edit</a>
                        <form method=\"POST\" action=\"#\" class=\"inline\" onsubmit=\"return confirm('Hapus data ini?')\">
                            @csrf @method('DELETE')
                            <button type=\"submit\" class=\"text-red-500 hover:text-red-700 font-semibold\">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan=\"100%\" class=\"px-6 py-16 text-center text-slate-400\">
                        <svg class=\"w-14 h-14 mx-auto text-slate-200 mb-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path></svg>
                        <p class=\"font-semibold text-slate-500\">Belum ada data {$title}</p>
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
    file_put_contents($viewDir . "{$route}.blade.php", $viewCode);
    echo "Created: {$model}\n";
}

echo "\nAll pages generated!";
