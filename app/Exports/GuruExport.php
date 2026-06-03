<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuruExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    /**
     * Constructor to receive filters from request.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Return collection of filtered gurus.
     */
    public function collection()
    {
        return Guru::filter($this->filters)->with(['jadwalAjars.mapel', 'jadwalAjars.kelas'])->get();
    }

    /**
     * Define column headings.
     */
    public function headings(): array
    {
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

    /**
     * Map each row of teacher data.
     */
    public function map($guru): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $mapels = $guru->jadwalAjars->pluck('mapel.name')->unique()->join(', ');
        $kelas = $guru->jadwalAjars->pluck('kelas.name')->unique()->join(', ');

        return [
            $rowNumber,
            $guru->name,
            $guru->nik,
            $mapels ?: '-',
            $kelas ?: '-',
            $guru->status,
            $guru->jabatan
        ];
    }
}
