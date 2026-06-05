<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Angkatan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KelasImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNames = [];
        $validationFailed = false;

        $allJurusans = Jurusan::all()->pluck('id', 'name');
        $allAngkatans = Angkatan::all()->pluck('id', 'name');

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nama_kelas'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $allJurusans, $allAngkatans, $checkedNames)) {
                $validationFailed = true;
            }
        }

        // Stop if validation failed (all or nothing)
        if ($validationFailed) {
            return;
        }

        // Step 2: Insert into Database with Transaction
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row['nama_kelas'])) {
                    continue;
                }

                Kelas::create([
                    'id' => (string) Str::uuid(),
                    'name' => trim($row['nama_kelas']),
                    'grade' => trim($row['tingkat_grade']),
                    'jurusan_id' => $row['matched_jurusan_id'],
                    'angkatan_id' => $row['matched_angkatan_id'] ?? null,
                ]);
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, $allJurusans, $allAngkatans, &$checkedNames)
    {
        $validationFailed = false;

        // 1. Nama Kelas validation
        $name = trim($row['nama_kelas'] ?? '');
        if (empty($name)) {
            $this->addError($rowNum, 'Nama Kelas wajib diisi.');
            $validationFailed = true;
        } else {
            // Check for duplicates in file
            $lowerName = strtolower($name);
            if (in_array($lowerName, $checkedNames)) {
                $this->addError($rowNum, "Nama Kelas '$name' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedNames[] = $lowerName;
                
                // Check uniqueness in database
                if (Kelas::whereRaw('LOWER(name) = ?', [$lowerName])->exists()) {
                    $this->addError($rowNum, "Nama Kelas '$name' sudah terdaftar di sistem.");
                    $validationFailed = true;
                }
            }
        }

        // 2. Tingkat (Grade) validation
        $grade = trim($row['tingkat_grade'] ?? '');
        if (empty($grade)) {
            $this->addError($rowNum, 'Tingkat (Grade) wajib diisi.');
            $validationFailed = true;
        } elseif (!in_array($grade, ['10', '11', '12'])) {
            $this->addError($rowNum, "Tingkat '$grade' tidak valid, harus 10, 11, atau 12.");
            $validationFailed = true;
        }

        // 3. Jurusan validation
        $jurusanInput = trim($row['jurusan'] ?? '');
        if (empty($jurusanInput)) {
            $this->addError($rowNum, 'Jurusan wajib diisi.');
            $validationFailed = true;
        } else {
            $matchedJurusanId = null;
            foreach ($allJurusans as $jName => $jId) {
                if (strcasecmp($jName, $jurusanInput) === 0) {
                    $matchedJurusanId = $jId;
                    break;
                }
            }

            if (!$matchedJurusanId) {
                $this->addError($rowNum, "Jurusan '$jurusanInput' tidak ditemukan di master data.");
                $validationFailed = true;
            } else {
                $row['matched_jurusan_id'] = $matchedJurusanId;
            }
        }

        // 4. Angkatan validation
        $angkatanInput = trim($row['angkatan'] ?? '');
        if (!empty($angkatanInput)) {
            $matchedAngkatanId = null;
            foreach ($allAngkatans as $aName => $aId) {
                if (strcasecmp($aName, $angkatanInput) === 0) {
                    $matchedAngkatanId = $aId;
                    break;
                }
            }

            if (!$matchedAngkatanId) {
                $this->addError($rowNum, "Angkatan '$angkatanInput' tidak ditemukan di master data.");
                $validationFailed = true;
            } else {
                $row['matched_angkatan_id'] = $matchedAngkatanId;
            }
        } else {
            $row['matched_angkatan_id'] = null;
        }

        return !$validationFailed;
    }

    /**
     * Add validation error.
     */
    protected function addError($rowNum, $message)
    {
        $this->errors[] = "Baris $rowNum: $message";
    }

    /**
     * Get validation errors.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
