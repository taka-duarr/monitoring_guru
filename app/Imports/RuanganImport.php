<?php

namespace App\Imports;

use App\Models\Ruangan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RuanganImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNames = [];
        $validationFailed = false;

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nama_ruangan'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $checkedNames)) {
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
                if (empty($row['nama_ruangan'])) {
                    continue;
                }

                $lantai = isset($row['lantai_opsional_1_6']) ? (int) $row['lantai_opsional_1_6'] : null;
                if ($lantai < 1 || $lantai > 6) {
                    $lantai = null;
                }

                Ruangan::create([
                    'id' => (string) Str::uuid(),
                    'name' => trim($row['nama_ruangan']),
                    'lantai' => $lantai
                ]);
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, &$checkedNames)
    {
        $validationFailed = false;

        // 1. Nama Ruangan validation
        $name = trim($row['nama_ruangan'] ?? '');
        if (empty($name)) {
            $this->addError($rowNum, 'Nama Ruangan wajib diisi.');
            $validationFailed = true;
        } else {
            // Check for duplicates in file
            $lowerName = strtolower($name);
            if (in_array($lowerName, $checkedNames)) {
                $this->addError($rowNum, "Nama Ruangan '$name' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedNames[] = $lowerName;
                
                // Check uniqueness in database
                if (Ruangan::whereRaw('LOWER(name) = ?', [$lowerName])->exists()) {
                    $this->addError($rowNum, "Nama Ruangan '$name' sudah terdaftar di sistem.");
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
