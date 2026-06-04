<?php

namespace App\Exports;

use App\Models\Mapel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MapelExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function array(): array
    {
        if ($this->isTemplate) {
            return [
                [1, 'Matematika Lanjut', 'Rekayasa Perangkat Lunak'],
                [2, 'Sejarah Indonesia', 'Teknik Komputer dan Jaringan'],
            ];
        }

        $mapels = Mapel::with('jurusan')->orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        foreach ($mapels as $mapel) {
            $data[] = [
                $no++,
                $mapel->name,
                $mapel->jurusan ? $mapel->jurusan->name : 'Umum / Semua Jurusan'
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Mata Pelajaran',
            'Kategori Jurusan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
