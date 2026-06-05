<?php

namespace App\Imports;

use App\Models\JadwalAjar;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class JadwalAjarImport implements ToModel, WithHeadingRow
{
    private $errors = [];

    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['nama_guru']) || !isset($row['mata_pelajaran']) || !isset($row['nama_kelas'])) {
            return null;
        }

        $guru = User::where('name', trim($row['nama_guru']))->where('jabatan', 'guru')->first();
        $mapel = Mapel::where('name', trim($row['mata_pelajaran']))->first();
        $kelas = Kelas::where('name', trim($row['nama_kelas']))->first();
        $ruangan = Ruangan::where('name', trim($row['nama_ruangan']))->first();

        if (!$guru) {
            $this->errors[] = "Guru dengan nama '{$row['nama_guru']}' tidak ditemukan.";
            return null;
        }

        if (!$mapel) {
            $this->errors[] = "Mata Pelajaran dengan nama '{$row['mata_pelajaran']}' tidak ditemukan.";
            return null;
        }

        if (!$kelas) {
            $this->errors[] = "Kelas dengan nama '{$row['nama_kelas']}' tidak ditemukan.";
            return null;
        }

        if (!$ruangan && isset($row['nama_ruangan'])) {
            $this->errors[] = "Ruangan dengan nama '{$row['nama_ruangan']}' tidak ditemukan.";
            return null;
        }

        $jamMulai = trim($row['jam_mulai']);
        if (is_numeric($jamMulai)) {
            $jamMulai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($jamMulai)->format('H:i');
        }

        $jamSelesai = trim($row['jam_selesai']);
        if (is_numeric($jamSelesai)) {
            $jamSelesai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($jamSelesai)->format('H:i');
        }

        return new JadwalAjar([
            'id' => (string) Str::uuid(),
            'guru_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'ruangan_id' => $ruangan ? $ruangan->id : null,
            'hari' => trim($row['hari']),
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
        ]);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
