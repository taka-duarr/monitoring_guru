<?php

namespace App\Imports;

use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JurusanImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNames = [];
        $checkedCodes = [];
        $validationFailed = false;

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nama_jurusan'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $checkedNames, $checkedCodes)) {
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
                if (empty($row['nama_jurusan'])) {
                    continue;
                }

                Jurusan::create([
                    'id' => (string) Str::uuid(),
                    'name' => trim($row['nama_jurusan']),
                    'kode_jurusan' => !empty($row['kode_jurusan']) ? trim($row['kode_jurusan']) : null,
                ]);
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, &$checkedNames, &$checkedCodes)
    {
        $validationFailed = false;

        // 1. Nama Jurusan validation
        $name = trim($row['nama_jurusan'] ?? '');
        if (empty($name)) {
            $this->addError($rowNum, 'Nama Jurusan wajib diisi.');
            $validationFailed = true;
        } else {
            // Check for duplicates in file
            $lowerName = strtolower($name);
            if (in_array($lowerName, $checkedNames)) {
                $this->addError($rowNum, "Nama Jurusan '$name' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedNames[] = $lowerName;
                
                // Check uniqueness in database
                if (Jurusan::whereRaw('LOWER(name) = ?', [$lowerName])->exists()) {
                    $this->addError($rowNum, "Nama Jurusan '$name' sudah terdaftar di sistem.");
                    $validationFailed = true;
                }
            }
        }

        // 2. Kode Jurusan validation (optional but should be unique if provided)
        $kode = trim($row['kode_jurusan'] ?? '');
        if (!empty($kode)) {
            $lowerKode = strtolower($kode);
            if (in_array($lowerKode, $checkedCodes)) {
                $this->addError($rowNum, "Kode Jurusan '$kode' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedCodes[] = $lowerKode;
                
                if (Jurusan::whereRaw('LOWER(kode_jurusan) = ?', [$lowerKode])->exists()) {
                    $this->addError($rowNum, "Kode Jurusan '$kode' sudah terdaftar di sistem.");
                    $validationFailed = true;
                }
            }
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
