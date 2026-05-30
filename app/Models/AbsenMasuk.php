<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenMasuk extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
    public function jadwalAjar() { return $this->belongsTo(JadwalAjar::class, 'jadwal_ajar_id'); }
    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function ruangan() { return $this->belongsTo(Ruangan::class, 'ruangan_id'); }
    public function absenKeluar() { return $this->hasOne(AbsenKeluar::class, 'absen_masuk_id'); }
}