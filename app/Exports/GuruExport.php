<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $isTemplate;

    /**
     * Constructor to receive filters and template flag.
     */
    public function __construct(array $filters = [], $isTemplate = false)
    {
        $this->filters = $filters;
        $this->isTemplate = $isTemplate;
    }

    /**
     * Return array of data.
     */
    public function array(): array
    {
        if ($this->isTemplate) {
            return [
                [
                    'Budi Hartono, S.Pd.',
                    ' 198505202010011001',
                    'Laki-laki',
                    '081234567890',
                    'Aktif'
                ],
                [
                    'Siti Aminah, S.Pd.',
                    ' 199009122018022002',
                    'Perempuan',
                    '089876543210',
                    'Aktif'
                ]
            ];
        }

        $gurus = Guru::filter($this->filters)->orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        
        foreach ($gurus as $guru) {
            $data[] = [
                $no++,
                $guru->name,
                ' ' . $guru->nik, // Prepend space to force Excel to treat as string
                $guru->status,
                $guru->jabatan
            ];
        }
        
        return $data;
    }

    /**
     * Define column headings.
     */
    public function headings(): array
    {
        if ($this->isTemplate) {
            return [
                'Nama Lengkap',
                'NIP',
                'Jenis Kelamin',
                'Nomor Telepon',
                'Status'
            ];
        }

        return [
            'No',
            'Nama Lengkap',
            'NIK / NIP',
            'Status',
            'Jabatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
