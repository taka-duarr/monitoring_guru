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

class PerizinanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected $data) {}

    public function collection()
    {
        return $this->data->map(function ($row, $i) {
            return [
                'no'          => $i + 1,
                'nama'        => $row->nama_guru ?? '-',
                'nip'         => $row->nik ?? '-',
                'tanggal'     => $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
                'jenis_izin'  => $row->jenis_izin ?? '-',
                'keterangan'  => $row->keterangan ?? '-',
                'status'      => $row->status ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'NIP',
            'Tanggal',
            'Jenis Izin',
            'Keterangan',
            'Status',
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
            "A1:G{$lastRow}" => [
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
        return 'Perizinan Guru';
    }
}
