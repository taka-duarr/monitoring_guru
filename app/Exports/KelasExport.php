<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
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
                [1, 'X RPL 1', '10', 'Rekayasa Perangkat Lunak', '2026'],
                [2, 'XI TKJ 2', '11', 'Teknik Komputer dan Jaringan', ''],
            ];
        }

        $kelas = Kelas::with(['jurusan', 'angkatan'])->orderBy('grade', 'asc')->orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        foreach ($kelas as $k) {
            $data[] = [
                $no++,
                $k->name,
                $k->grade,
                $k->jurusan ? $k->jurusan->name : '-',
                $k->angkatan ? $k->angkatan->name : '-'
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Kelas',
            'Tingkat Grade',
            'Jurusan',
            'Angkatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
