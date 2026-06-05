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
                    '198505202010011001',
                    'Laki-laki',
                    'Surabaya',
                    '1985-05-20',
                    '081234567890',
                    'PNS',
                    'III/b',
                    '2010-01-01',
                    'Aktif',
                    'Matematika',
                    'X IPA 1, X IPA 2',
                    '24'
                ],
                [
                    'Siti Aminah, S.Pd.',
                    '199009122018022002',
                    'Perempuan',
                    'Sidoarjo',
                    '1990-09-12',
                    '089876543210',
                    'GTT',
                    '',
                    '2018-02-15',
                    'Aktif',
                    'Bahasa Indonesia',
                    'XI IPS 1',
                    '18'
                ]
            ];
        }

        $gurus = Guru::filter($this->filters)->with(['jadwalAjars.mapel', 'jadwalAjars.kelas'])->orderBy('name', 'asc')->get();
        $data = [];
        $no = 1;
        
        foreach ($gurus as $guru) {
            $mapels = $guru->jadwalAjars->pluck('mapel.name')->unique()->join(', ');
            $kelas = $guru->jadwalAjars->pluck('kelas.name')->unique()->join(', ');
            
            $data[] = [
                $no++,
                $guru->name,
                $guru->nik,
                $mapels ?: '-',
                $kelas ?: '-',
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
                'Tempat Lahir',
                'Tanggal Lahir',
                'Nomor Telepon',
                'Status Kepegawaian',
                'Golongan Pangkat',
                'TMT',
                'Status',
                'Mata Pelajaran',
                'Kelas Pengampu',
                'Jumlah Jam Mengajar'
            ];
        }

        return [
            'No',
            'Nama Lengkap',
            'NIK / NIP',
            'Mata Pelajaran',
            'Kelas Pengampu',
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
