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

class KehadiranExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected $data,
        protected string $periodeLabel = ''
    ) {}

    public function collection()
    {
        return $this->data->map(function ($row, $i) {
            return [
                'no'          => $i + 1,
                'nama'        => $row->nama_guru ?? '-',
                'nip'         => $row->nik ?? '-',
                'tanggal'     => $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
                'jam_masuk'   => $row->jam_masuk ?? '-',
                'jam_keluar'  => $row->jam_keluar ?? '-',
                'kelas'       => $row->kelas ?? '-',
                'mapel'       => $row->mapel ?? '-',
                'status'      => $row->status ?? 'Hadir',
                'keterangan'  => $row->keterangan ?? '-',
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
            'Jam Masuk',
            'Jam Keluar',
            'Kelas',
            'Mata Pelajaran',
            'Status',
            'Keterangan',
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
            "A1:J{$lastRow}" => [
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
        return 'Rekap Kehadiran';
    }
}
