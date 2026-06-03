<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatLaporan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'riwayat_laporans';

    protected $guarded = [];

    protected $casts = [
        'parameter' => 'array',
    ];

    /**
     * Admin yang membuat laporan.
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Label jenis laporan dalam bahasa Indonesia.
     */
    public function getLabelJenisAttribute(): string
    {
        return match ($this->jenis_laporan) {
            'rekap_kehadiran' => 'Rekap Kehadiran Guru',
            'perizinan'       => 'Perizinan Guru',
            'kelas_kosong'    => 'Kelas Kosong',
            'jadwal_ajar'     => 'Jadwal Mengajar',
            default           => ucfirst($this->jenis_laporan),
        };
    }

    /**
     * Ikon Tabler untuk jenis laporan.
     */
    public function getIkonJenisAttribute(): string
    {
        return match ($this->jenis_laporan) {
            'rekap_kehadiran' => 'ti-users',
            'perizinan'       => 'ti-file-check',
            'kelas_kosong'    => 'ti-school',
            'jadwal_ajar'     => 'ti-calendar',
            default           => 'ti-file-report',
        };
    }
}
