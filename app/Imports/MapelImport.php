<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MapelImport implements ToCollection, WithHeadingRow
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

        // Step 1: Validate all rows first
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            if (empty($row['nama_mata_pelajaran'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $allJurusans, $checkedNames)) {
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
                if (empty($row['nama_mata_pelajaran'])) {
                    continue;
                }

                Mapel::create([
                    'id' => (string) Str::uuid(),
                    'name' => trim($row['nama_mata_pelajaran']),
                    'jurusan_id' => $row['matched_jurusan_id'] ?? null,
                ]);
            }
        });
    }

    /**
     * Validate a single row
     */
    private function validateRow(&$row, $rowNum, $allJurusans, &$checkedNames)
    {
        $validationFailed = false;

        // 1. Nama Mapel validation
        $name = trim($row['nama_mata_pelajaran'] ?? '');
        if (empty($name)) {
            $this->addError($rowNum, 'Nama Mata Pelajaran wajib diisi.');
            $validationFailed = true;
        } else {
            // Check for duplicates in file
            $lowerName = strtolower($name);
            if (in_array($lowerName, $checkedNames)) {
                $this->addError($rowNum, "Nama Mata Pelajaran '$name' duplikat di dalam berkas Excel.");
                $validationFailed = true;
            } else {
                $checkedNames[] = $lowerName;
                
                // Check uniqueness in database
                if (Mapel::whereRaw('LOWER(name) = ?', [$lowerName])->exists()) {
                    $this->addError($rowNum, "Nama Mata Pelajaran '$name' sudah terdaftar di sistem.");
                    $validationFailed = true;
                }
            }
        }

        // 2. Kategori Jurusan validation
        $jurusanInput = trim($row['kategori_jurusan'] ?? '');
        if (!empty($jurusanInput)) {
            $matchedJurusanId = null;
            foreach ($allJurusans as $jName => $jId) {
                if (strcasecmp($jName, $jurusanInput) === 0) {
                    $matchedJurusanId = $jId;
                    break;
                }
            }

            if (!$matchedJurusanId) {
                $this->addError($rowNum, "Kategori Jurusan '$jurusanInput' tidak ditemukan di master data.");
                $validationFailed = true;
            } else {
                $row['matched_jurusan_id'] = $matchedJurusanId;
            }
        } else {
            $row['matched_jurusan_id'] = null; // Umum / Semua Jurusan
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
