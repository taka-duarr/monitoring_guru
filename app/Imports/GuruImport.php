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
                    'id' => (string) Str::uuid(),
                    'name' => trim($row['nama_lengkap']),
                    'nik' => $nip,
                    'jabatan' => 'guru',
                    'status' => trim($row['status'] ?? 'Aktif'),
                    'jenis_kelamin' => trim($row['jenis_kelamin']),
                    'tempat_lahir' => trim($row['tempat_lahir'] ?? null),
                    'tanggal_lahir' => $row['parsed_tanggal_lahir'],
                    'no_telp' => trim($row['nomor_telepon'] ?? null),
                    'status_kepegawaian' => trim($row['status_kepegawaian']),
                    'golongan' => trim($row['golongan_pangkat'] ?? null),
                    'tmt' => $row['parsed_tmt'],
                    'jumlah_jam' => intval($row['jumlah_jam']),
                    'password' => Hash::make($nip), // Default password is NIP
                ]);

                // Create basic schedule for each class
                if (isset($row['matched_kelas_ids']) && isset($row['matched_mapel_id'])) {
                    foreach ($row['matched_kelas_ids'] as $kelasId) {
                        JadwalAjar::create([
                            'id' => (string) Str::uuid(),
                            'guru_id' => $user->id,
                            'mapel_id' => $row['matched_mapel_id'],
                            'kelas_id' => $kelasId,
                            'ruangan_id' => $firstRuangan ? $firstRuangan->id : null,
                            'hari' => 'Senin',
                            'jam_mulai' => '07:00',
                            'jam_selesai' => '08:00',
                        ]);
                    }
                }
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
            $this->addError($rowNum, 'NIP wajib diisi.');
            $validationFailed = true;
        } elseif (!is_numeric($nip)) {
            $this->addError($rowNum, 'NIP harus berupa angka.');
            $validationFailed = true;
        } elseif (strlen($nip) !== 18) {
            $this->addError($rowNum, 'NIP harus tepat 18 digit.');
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

        // 4. Status Kepegawaian validation
        $statusKepegawaian = trim($row['status_kepegawaian'] ?? '');
        if (empty($statusKepegawaian)) {
            $this->addError($rowNum, 'Status Kepegawaian wajib diisi (PNS / GTT / GTY / Honorer).');
            $validationFailed = true;
        } elseif (!in_array($statusKepegawaian, ['PNS', 'GTT', 'GTY', 'Honorer'])) {
            $this->addError($rowNum, "Status Kepegawaian tidak valid: '$statusKepegawaian'. Harus PNS, GTT, GTY, atau Honorer.");
            $validationFailed = true;
        }

        // 5. Golongan validation
        $golongan = null;
        if (isset($row['golongan_pangkat'])) {
            $golongan = trim($row['golongan_pangkat']);
        } elseif (isset($row['golongan'])) {
            $golongan = trim($row['golongan']);
        }
        
        if ($statusKepegawaian === 'PNS' && empty($golongan)) {
            $this->addError($rowNum, 'Golongan/Pangkat wajib diisi jika status kepegawaian adalah PNS.');
            $validationFailed = true;
        }
        $row['golongan_pangkat'] = $golongan;

        // 6. Status Keaktifan validation
        $status = isset($row['status']) ? trim($row['status']) : 'Aktif';
        if (!in_array($status, ['Aktif', 'Cuti', 'Pensiun'])) {
            $this->addError($rowNum, "Status Keaktifan tidak valid: '$status'. Harus Aktif, Cuti, atau Pensiun.");
            $validationFailed = true;
        }

        // 7. Jumlah Jam Mengajar validation
        $jumlahJam = null;
        if (isset($row['jumlah_jam_mengajar'])) {
            $jumlahJam = trim($row['jumlah_jam_mengajar']);
        } elseif (isset($row['jumlah_jam'])) {
            $jumlahJam = trim($row['jumlah_jam']);
        }

        if (is_null($jumlahJam) || $jumlahJam === '') {
            $this->addError($rowNum, 'Jumlah Jam Mengajar wajib diisi.');
            $validationFailed = true;
        } elseif (!is_numeric($jumlahJam) || intval($jumlahJam) < 0 || intval($jumlahJam) > 48) {
            $this->addError($rowNum, 'Jumlah Jam Mengajar harus berupa angka antara 0 hingga 48.');
            $validationFailed = true;
        }
        $row['jumlah_jam'] = $jumlahJam;

        // 8. Mata Pelajaran validation & matching
        $mapelName = trim($row['mata_pelajaran'] ?? '');
        if (empty($mapelName)) {
            $this->addError($rowNum, 'Mata Pelajaran wajib diisi.');
            $validationFailed = true;
        } else {
            // Case-insensitive match from the loaded mapels
            $matchedMapelId = null;
            foreach ($allMapels as $name => $id) {
                if (strcasecmp($name, $mapelName) === 0) {
                    $matchedMapelId = $id;
                    break;
                }
            }

            if (!$matchedMapelId) {
                $this->addError($rowNum, "Mata Pelajaran '$mapelName' tidak ditemukan di master data.");
                $validationFailed = true;
            } else {
                $row['matched_mapel_id'] = $matchedMapelId;
            }
        }

        // 9. Kelas Pengampu validation & matching
        $kelasInput = trim($row['kelas_pengampu'] ?? '');
        if (empty($kelasInput)) {
            $this->addError($rowNum, 'Kelas Pengampu wajib diisi.');
            $validationFailed = true;
        } else {
            $classNames = array_map('trim', explode(',', $kelasInput));
            $matchedKelasIds = [];
            $notFoundClasses = [];

            foreach ($classNames as $className) {
                $matchedId = null;
                foreach ($allKelas as $name => $id) {
                    if (strcasecmp($name, $className) === 0) {
                        $matchedId = $id;
                        break;
                    }
                }

                if ($matchedId) {
                    $matchedKelasIds[] = $matchedId;
                } else {
                    $notFoundClasses[] = $className;
                }
            }

            if (!empty($notFoundClasses)) {
                $this->addError($rowNum, "Kelas berikut tidak ditemukan di master data: '" . implode("', '", $notFoundClasses) . "'");
                $validationFailed = true;
            } else {
                $row['matched_kelas_ids'] = $matchedKelasIds;
            }
        }

        // Parse Birth Date & TMT
        $row['parsed_tanggal_lahir'] = $this->transformDate($row['tanggal_lahir'] ?? null);
        $row['parsed_tmt'] = $this->transformDate($row['tmt'] ?? null);

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
