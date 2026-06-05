<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KetuaKelasExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
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
                [1, 'Andi Firmansyah', '676767', 'X RPL 1'],
                [2, 'Budi Santoso', '212121', 'XI TKJ 2'],
            ];
        }

        $ketuakelas = User::where('jabatan', 'ketuakelas')->with('kelas')->orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        foreach ($ketuakelas as $k) {
            $data[] = [
                $no++,
                $k->name,
                $k->nik,
                $k->kelas ? $k->kelas->name : '-'
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIS',
            'Kelas'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
