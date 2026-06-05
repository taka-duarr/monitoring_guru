<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JadwalAjarExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;
    protected $isTemplate;

    public function __construct($data = null, $isTemplate = false)
    {
        $this->data = $data;
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([
                ['Budi Santoso', 'Matematika', 'X RPL 1', 'Ruang Teori 1', 'Senin', '07:00', '08:30'],
            ]);
        }

        return $this->data->map(function ($row, $i) {
            return [
                'no'     => $i + 1,
                'guru'   => $row->guru ?? '-',
                'nip'    => $row->nik ?? '-',
                'mapel'  => $row->mapel ?? '-',
                'kelas'  => $row->kelas ?? '-',
                'hari'   => $row->hari ?? '-',
                'jam'    => $row->jam ?? '-',
                'ruangan'=> $row->ruangan->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        if ($this->isTemplate) {
            return [
                'Nama Guru',
                'Mata Pelajaran',
                'Nama Kelas',
                'Nama Ruangan',
                'Hari',
                'Jam Mulai',
                'Jam Selesai',
            ];
        }

        return [
            'No',
            'Nama Guru',
            'NIP',
            'Mata Pelajaran',
            'Kelas',
            'Hari',
            'Jam',
            'Ruangan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B2A4A'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "A1:H{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Jadwal Mengajar';
    }
}
