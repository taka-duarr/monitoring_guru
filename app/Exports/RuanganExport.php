<?php

namespace App\Exports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RuanganExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
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
                ['Ruang Teori 1', '1'],
                ['Lab Komputer 1', '2'],
                ['Ruang Aula', ''],
            ];
        }

        $ruangans = Ruangan::orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        foreach ($ruangans as $r) {
            $data[] = [
                $no++,
                $r->name,
                $r->lantai ? $r->lantai : '-'
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        if ($this->isTemplate) {
            return [
                'Nama Ruangan',
                'Lantai (Opsional 1-6)'
            ];
        }

        return [
            'No',
            'Nama Ruangan',
            'Lantai'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
