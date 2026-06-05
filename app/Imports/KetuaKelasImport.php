<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KetuaKelasImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNis = [];
        $validationFailed = false;

        $allKelas = Kelas::all()->pluck('id', 'name');

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nama_lengkap'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $allKelas, $checkedNis)) {
                $validationFailed = true;
            }
        }

        // Stop if validation failed
        if ($validationFailed) {
            return;
        }

        // Step 2: Insert into Database with Transaction
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row['nama_lengkap'])) {
                    continue;
                }

                User::updateOrCreate(
                    ['nik' => trim($row['nis'])],
                    [
                        'id' => (string) Str::uuid(),
                        'name' => trim($row['nama_lengkap']),
                        'kelas_id' => $row['matched_kelas_id'],
                        'jabatan' => 'ketuakelas',
                        'password' => Hash::make(trim($row['nis']))
                    ]
                );
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, $allKelas, &$checkedNis)
    {
        $validationFailed = false;

        // 1. Nama validation
        if (empty(trim($row['nama_lengkap'] ?? ''))) {
            $this->addError($rowNum, 'Nama Lengkap wajib diisi.');
            $validationFailed = true;
        }

        // 2. NIS validation
        $nis = trim($row['nis'] ?? '');
        if (empty($nis)) {
            $this->addError($rowNum, 'NIS wajib diisi.');
            $validationFailed = true;
        } else {
            // Check for duplicates in file
            if (in_array($nis, $checkedNis)) {
                $this->addError($rowNum, "NIS '$nis' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedNis[] = $nis;
            }
        }

        // 3. Kelas validation
        $kelasName = trim($row['kelas'] ?? '');
        if (empty($kelasName)) {
            $this->addError($rowNum, 'Kelas wajib diisi.');
            $validationFailed = true;
        } else {
            // Case-insensitive search
            $kelasId = null;
            foreach ($allKelas as $name => $id) {
                if (strtolower(trim($name)) === strtolower($kelasName)) {
                    $kelasId = $id;
                    break;
                }
            }
            
            if (!$kelasId) {
                $this->addError($rowNum, "Kelas '$kelasName' tidak ditemukan di master data Kelas.");
                $validationFailed = true;
            } else {
                $row['matched_kelas_id'] = $kelasId;
            }
        }

        return !$validationFailed;
    }

    protected function addError($rowNum, $message)
    {
        $this->errors[] = "Baris $rowNum: $message";
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
