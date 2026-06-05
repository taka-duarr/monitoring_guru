<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\JadwalAjar;
use App\Models\Ruangan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $checkedNips = [];
        $validationFailed = false;

        // Fetch all rooms, subjects, and classes to optimize performance
        $allMapels = Mapel::all()->pluck('id', 'name');
        $allKelas = Kelas::all()->pluck('id', 'name');
        $firstRuangan = Ruangan::query()->first();

        // Step 1: Validate all rows first (Option A)
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Row number in Excel file (Header is row 1)

            // Skip empty rows
            if (empty($row['nama_lengkap']) && empty($row['nip'])) {
                continue;
            }

            if (!$this->validateRow($row, $rowNum, $allMapels, $allKelas, $checkedNips)) {
                $validationFailed = true;
            }
        }

        // Stop if validation failed (Option A: database transaction all or nothing)
        if ($validationFailed) {
            return;
        }

        // Step 2: Insert into Database with Transaction
        DB::transaction(function () use ($rows, $firstRuangan) {
            foreach ($rows as $row) {
                if (empty($row['nama_lengkap']) && empty($row['nip'])) {
                    continue;
                }

                $nip = trim($row['nip']);

                // Create User
                $user = User::create([
                    'id'                 => (string) Str::uuid(),
                    'name'               => trim($row['nama_lengkap']),
                    'nik'                => $nip,
                    'jabatan'            => 'guru',
                    'status'             => trim($row['status'] ?? 'Aktif'),
                    'jenis_kelamin'      => trim($row['jenis_kelamin']),
                    'no_telp'            => trim($row['nomor_telepon'] ?? null),
                    'password'           => Hash::make($nip), // Default password is NIP
                ]);


            }
        });
    }

    /**
     * Validate a single row from the Excel/CSV collection.
     */
    private function validateRow(&$row, $rowNum, $allMapels, $allKelas, &$checkedNips)
    {
        $validationFailed = false;

        // 1. Nama Lengkap validation
        if (empty($row['nama_lengkap'])) {
            $this->addError($rowNum, 'Nama Lengkap wajib diisi.');
            $validationFailed = true;
        }

        // 2. NIP validation
        $nip = trim($row['nip'] ?? '');
        if (empty($nip)) {
            $this->addError($rowNum, 'NIP/ID wajib diisi.');
            $validationFailed = true;
        } elseif (strlen($nip) > 50) {
            $this->addError($rowNum, 'NIP/ID maksimal 50 karakter.');
            $validationFailed = true;
        } elseif (in_array($nip, $checkedNips)) {
            $this->addError($rowNum, "NIP '$nip' duplikat di dalam berkas Excel.");
            $validationFailed = true;
        } else {
            $checkedNips[] = $nip;
            // Check uniqueness in database
            if (User::query()->where('nik', $nip)->exists()) {
                $this->addError($rowNum, "NIP '$nip' sudah terdaftar di sistem.");
                $validationFailed = true;
            }
        }

        // 3. Jenis Kelamin validation
        $jk = trim($row['jenis_kelamin'] ?? '');
        if (empty($jk)) {
            $this->addError($rowNum, 'Jenis Kelamin wajib diisi (Laki-laki / Perempuan).');
            $validationFailed = true;
        } elseif (!in_array($jk, ['Laki-laki', 'Perempuan'])) {
            $this->addError($rowNum, "Jenis Kelamin tidak valid: '$jk'. Harus 'Laki-laki' atau 'Perempuan'.");
            $validationFailed = true;
        }


        $status = isset($row['status']) ? trim($row['status']) : 'Aktif';
        if (!in_array($status, ['Aktif', 'Cuti', 'Pensiun'])) {
            $this->addError($rowNum, "Status Keaktifan tidak valid: '$status'. Harus Aktif, Cuti, atau Pensiun.");
            $validationFailed = true;
        }



        return !$validationFailed;
    }

    /**
     * Parse date value from Excel format to MySQL Y-m-d format.
     */
    private function transformDate($value)
    {
        $result = null;

        if (!empty($value)) {
            if (is_numeric($value)) {
                try {
                    $result = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
                } catch (\Exception $e) {
                    // ignore
                }
            }

            if (is_null($result)) {
                try {
                    $result = Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }

        return $result;
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
