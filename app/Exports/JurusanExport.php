<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JurusanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
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
                [1, 'Rekayasa Perangkat Lunak', 'RPL'],
                [2, 'Teknik Komputer dan Jaringan', 'TKJ'],
            ];
        }

        $jurusans = Jurusan::orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        foreach ($jurusans as $j) {
            $data[] = [
                $no++,
                $j->name,
                $j->kode_jurusan ?: '-'
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Jurusan',
            'Kode Jurusan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
