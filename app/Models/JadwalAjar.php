<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAjar extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function guru() { return $this->belongsTo(User::class, 'guru_id')->withTrashed(); }
    public function mapel() { return $this->belongsTo(Mapel::class, 'mapel_id')->withTrashed(); }
    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id')->withTrashed(); }
    public function ruangan() { return $this->belongsTo(Ruangan::class, 'ruangan_id')->withTrashed(); }
    public function absenMasuks() { return $this->hasMany(AbsenMasuk::class, 'jadwal_ajar_id'); }
    public function izins() { return $this->hasMany(Izin::class, 'jadwal_ajar_id'); }
}