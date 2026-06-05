<?php

namespace App\Imports;

use App\Models\Murid;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MuridImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNis = [];
        $validationFailed = false;

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nis']) && empty($row['nama_murid'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $checkedNis)) {
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
                if (empty($row['nis']) && empty($row['nama_murid'])) {
                    continue;
                }

                Murid::create([
                    'id' => (string) Str::uuid(),
                    'kelas_id' => $this->kelasId,
                    'nis' => trim($row['nis']),
                    'name' => trim($row['nama_murid']),
                    'no_absen' => !empty($row['no_absen']) ? (int) $row['no_absen'] : null,
                ]);
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, &$checkedNis)
    {
        $validationFailed = false;

        // 1. Nama Murid validation
        $name = trim($row['nama_murid'] ?? '');
        if (empty($name)) {
            $this->addError($rowNum, 'Nama Murid wajib diisi.');
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
                
                // Check uniqueness in database (NIS must be unique across all students)
                if (Murid::where('nis', $nis)->exists()) {
                    $this->addError($rowNum, "NIS '$nis' sudah terdaftar di sistem.");
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
