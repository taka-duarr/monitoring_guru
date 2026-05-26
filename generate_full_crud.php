<?php

$pages = [
    'Guru' => [
        'route' => 'guru', 'title' => 'Guru',
        'columns' => [
            ['field' => 'name',    'label' => 'Nama', 'type' => 'text'],
            ['field' => 'nik',     'label' => 'NIK', 'type' => 'text'],
            ['field' => 'jabatan', 'label' => 'Jabatan', 'type' => 'select', 'options' => ['guru' => 'Guru', 'kepala_sekolah' => 'Kepala Sekolah', 'admin' => 'Admin']],
        ],
    ],
    'Kelas' => [
        'route' => 'kelas', 'title' => 'Kelas',
        'columns' => [
            ['field' => 'name',      'label' => 'Nama Kelas', 'type' => 'text'],
            ['field' => 'grade',     'label' => 'Grade', 'type' => 'text'],
            ['field' => 'jurusan_id','label' => 'Jurusan', 'type' => 'relation', 'model' => 'Jurusan', 'display' => 'name'],
        ],
    ],
    'Jurusan' => [
        'route' => 'jurusan', 'title' => 'Jurusan',
        'columns' => [
            ['field' => 'name',         'label' => 'Nama Jurusan', 'type' => 'text'],
            ['field' => 'kode_jurusan', 'label' => 'Kode Jurusan', 'type' => 'text'],
        ],
    ],
    'JadwalAjar' => [
        'route' => 'jadwalajar', 'title' => 'Jadwal Ajar',
        'columns' => [
            ['field' => 'hari',        'label' => 'Hari', 'type' => 'select', 'options' => ['Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat']],
            ['field' => 'jam_mulai',   'label' => 'Jam Mulai', 'type' => 'time'],
            ['field' => 'jam_selesai', 'label' => 'Jam Selesai', 'type' => 'time'],
        ],
    ],
    'AbsenMasuk' => [
        'route' => 'absenmasuk', 'title' => 'Absen Masuk',
        'columns' => [
            ['field' => 'tanggal',   'label' => 'Tanggal', 'type' => 'date'],
            ['field' => 'jam_masuk', 'label' => 'Jam Masuk', 'type' => 'time'],
        ],
    ],
    'KetuaKelas' => [
        'route' => 'ketuakelas', 'title' => 'Ketua Kelas',
        'columns' => [
            ['field' => 'name',   'label' => 'Nama', 'type' => 'text'],
            ['field' => 'nisn',   'label' => 'NISN', 'type' => 'text'],
            ['field' => 'kelas_id','label'=> 'Kelas', 'type' => 'relation', 'model' => 'Kelas', 'display' => 'name'],
        ],
    ],
    'Mapel' => [
        'route' => 'mapel', 'title' => 'Mata Pelajaran',
        'columns' => [
            ['field' => 'name',       'label' => 'Nama Mapel', 'type' => 'text'],
            ['field' => 'jurusan_id', 'label' => 'Jurusan', 'type' => 'relation', 'model' => 'Jurusan', 'display' => 'name'],
        ],
    ],
    'Ruangan' => [
        'route' => 'ruangan', 'title' => 'Ruangan',
        'columns' => [
            ['field' => 'name', 'label' => 'Nama Ruangan', 'type' => 'text'],
        ],
    ],
    'Izin' => [
        'route' => 'izin', 'title' => 'Pengajuan Izin',
        'columns' => [
            ['field' => 'tanggal_izin',   'label' => 'Tanggal', 'type' => 'date'],
            ['field' => 'judul',          'label' => 'Judul', 'type' => 'text'],
            ['field' => 'pesan',          'label' => 'Pesan', 'type' => 'textarea'],
            ['field' => 'approval',       'label' => 'Disetujui?', 'type' => 'select', 'options' => [0 => 'Belum', 1 => 'Ya']],
        ],
    ],
    'AbsenKeluar' => [
        'route' => 'absenkeluar', 'title' => 'Rekap Absen Keluar',
        'columns' => [
            ['field' => 'absen_masuk_id', 'label' => 'ID Absen Masuk', 'type' => 'relation', 'model' => 'AbsenMasuk', 'display' => 'tanggal'],
            ['field' => 'jam_keluar',     'label' => 'Waktu Keluar', 'type' => 'time'],
            ['field' => 'status',         'label' => 'Status', 'type' => 'text'],
        ],
    ],
    'StatusKelas' => [
        'route' => 'statuskelas', 'title' => 'Status Kelas',
        'columns' => [
            ['field' => 'mapel',    'label' => 'Mapel', 'type' => 'text'],
            ['field' => 'pengajar', 'label' => 'Pengajar', 'type' => 'text'],
            ['field' => 'is_active','label' => 'Aktif?', 'type' => 'select', 'options' => [0 => 'Tidak', 1 => 'Ya']],
        ],
    ],
];

$viewDir = __DIR__ . '/resources/views/admin/';
$ctrlDir = __DIR__ . '/app/Http/Controllers/';

foreach ($pages as $model => $cfg) {
    $route = $cfg['route'];
    $title = $cfg['title'];
    $cols  = $cfg['columns'];

    // 1. GENERATE CONTROLLER
    $relationsQuery = [];
    $compacts = ["'data'"];
    $relationsVariables = [];
    $relationsVariablesCreate = [];

    foreach ($cols as $col) {
        if ($col['type'] === 'relation') {
            $relModel = $col['model'];
            $relVarName = lcfirst($relModel) . "s";
            $relationsVariables[] = "\${$relVarName} = \App\Models\\{$relModel}::all();";
            $compacts[] = "'{$relVarName}'";
        }
    }

    $relLines = implode("\n        ", $relationsVariables);
    $compactStr = implode(', ', $compacts);

    $ctrlCode = "<?php
namespace App\\Http\\Controllers;
use Illuminate\\Http\\Request;
use App\\Models\\{$model};

class {$model}Controller extends Controller
{
    public function index()
    {
        \$data = {$model}::latest()->paginate(15);
        return view('admin.{$route}', compact('data'));
    }

    public function create()
    {
        {$relLines}
        return view('admin.{$route}_form', compact(" . str_replace("'data', ", "", $compactStr) . "));
    }

    public function store(Request \$request)
    {
        \$data = \$request->except('_token');
        if (isset(\$data['password'])) \$data['password'] = bcrypt(\$data['password']);
        {$model}::create(\$data);
        return redirect()->route('{$route}.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(\$id)
    {
        \$data = {$model}::findOrFail(\$id);
        {$relLines}
        return view('admin.{$route}_form', compact({$compactStr}));
    }

    public function update(Request \$request, \$id)
    {
        \$record = {$model}::findOrFail(\$id);
        \$data = \$request->except(['_token', '_method']);
        if (isset(\$data['password']) && \$data['password']) {
            \$data['password'] = bcrypt(\$data['password']);
        } else {
            unset(\$data['password']);
        }
        \$record->update(\$data);
        return redirect()->route('{$route}.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy(\$id)
    {
        {$model}::destroy(\$id);
        return redirect()->route('{$route}.index')->with('success', 'Data berhasil dihapus');
    }
}
";
    file_put_contents($ctrlDir . "{$model}Controller.php", $ctrlCode);

    // 2. MODIFY INDEX VIEW TO LINK TO CREATE, EDIT, AND DELETE
    $indexViewPath = $viewDir . "{$route}.blade.php";
    if (file_exists($indexViewPath)) {
        $indexContent = file_get_contents($indexViewPath);
        // Replace create button
        $indexContent = preg_replace('/href="[^"]*"([^>]*)>\s*<svg[^>]*>.*Tambah Data\s*<\/a>/s', 'href="{{ route(\'' . $route . '.create\') }}"$1>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Data
        </a>', $indexContent);

        // Replace edit button
        $indexContent = preg_replace('/href="[^"]*"\s*class="text-blue-600[^"]*">Edit<\/a>/', 'href="{{ route(\'' . $route . '.edit\', $row->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>', $indexContent);

        // Replace form action
        $indexContent = preg_replace('/action="[^"]*"/', 'action="{{ route(\'' . $route . '.destroy\', $row->id) }}"', $indexContent);
        // Sometimes my regex is lazy, let's just do a specific string replace for the delete form
        $indexContent = str_replace('<form method="POST" action="#" class="inline"', '<form method="POST" action="{{ route(\''.$route.'.destroy\', $row->id) }}" class="inline"', $indexContent);

        file_put_contents($indexViewPath, $indexContent);
    }

    // 3. GENERATE CREATE/EDIT FORM VIEW (admin/{route}_form.blade.php)
    $formFields = '';
    foreach ($cols as $col) {
        $field = $col['field'];
        $label = $col['label'];
        $type = $col['type'];

        $formFields .= "        <div class=\"mb-4\">\n";
        $formFields .= "            <label class=\"block text-sm font-medium text-slate-700 mb-1\">{$label}</label>\n";
        
        if ($type === 'text' || $type === 'time' || $type === 'date') {
            $inputType = $type === 'time' ? 'time' : ($type === 'date' ? 'date' : 'text');
            $formFields .= "            <input type=\"{$inputType}\" name=\"{$field}\" value=\"{{ old('{$field}', \$data->{$field} ?? '') }}\" class=\"w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500\" required>\n";
        } elseif ($type === 'textarea') {
            $formFields .= "            <textarea name=\"{$field}\" rows=\"3\" class=\"w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500\" required>{{ old('{$field}', \$data->{$field} ?? '') }}</textarea>\n";
        } elseif ($type === 'select') {
            $formFields .= "            <select name=\"{$field}\" class=\"w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500\" required>\n";
            foreach ($col['options'] as $val => $optLbl) {
                $formFields .= "                <option value=\"{$val}\" @if(old('{$field}', \$data->{$field} ?? '') == '{$val}') selected @endif>{$optLbl}</option>\n";
            }
            $formFields .= "            </select>\n";
        } elseif ($type === 'relation') {
            $relModel = $col['model'];
            $relVarName = lcfirst($relModel) . "s";
            $displayCol = $col['display'];
            $formFields .= "            <select name=\"{$field}\" class=\"w-full rounded-lg border-slate-300 border px-4 py-2 focus:ring-brand-500 focus:border-brand-500\">\n";
            $formFields .= "                <option value=\"\">-- Pilih {$label} --</option>\n";
            $formFields .= "                @foreach(\${$relVarName} as \$rel)\n";
            $formFields .= "                <option value=\"{{ \$rel->id }}\" @if(old('{$field}', \$data->{$field} ?? '') == \$rel->id) selected @endif>{{ \$rel->{$displayCol} }}</option>\n";
            $formFields .= "                @endforeach\n";
            $formFields .= "            </select>\n";
        }
        $formFields .= "        </div>\n";
    }

    $formCode = "@extends('layouts.admin')
@section('title', (isset(\$data) ? 'Edit' : 'Tambah') . ' {$title}')
@section('page_title', (isset(\$data) ? 'Edit' : 'Tambah') . ' {$title}')

@section('content')
<div class=\"max-w-2xl bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100 p-6\">
    <form action=\"{{ isset(\$data) ? route('{$route}.update', \$data->id) : route('{$route}.store') }}\" method=\"POST\">
        @csrf
        @if(isset(\$data)) @method('PUT') @endif
        
{$formFields}
        <div class=\"mt-6 flex gap-3\">
            <button type=\"submit\" class=\"px-6 py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm\">Simpan</button>
            <a href=\"{{ route('{$route}.index') }}\" class=\"px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition\">Batal</a>
        </div>
    </form>
</div>
@endsection";
    file_put_contents($viewDir . "{$route}_form.blade.php", $formCode);
    
    echo "CRUD Generated for: {$model}\n";
}

echo "\nAll CRUD Forms Generated Successfully!";
