<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JadwalAjar;

class Kelas extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $fillable = [
        'id',
        'jurusan_id',
        'name',
        'angkatan_id',
        'grade',
        'is_active',
    ];

    public function jurusan() { return $this->belongsTo(Jurusan::class, 'jurusan_id'); }
    public function angkatan() { return $this->belongsTo(Angkatan::class, 'angkatan_id'); }
    public function ketua() { return $this->belongsTo(User::class, 'ketua_id'); }
    public function murids() { return $this->hasMany(Murid::class, 'kelas_id'); }
    public function statusKelas() { return $this->hasOne(StatusKelas::class, 'kelas_id'); }
    public function jadwalAjars() { return $this->hasMany(JadwalAjar::class, 'kelas_id'); }
}