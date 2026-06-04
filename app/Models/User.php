<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'users';
    
    protected $guarded = [];

    /**
     * Kolom yang digunakan untuk autentikasi (primary key model)
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function jadwalAjars() { return $this->hasMany(JadwalAjar::class, 'guru_id'); }
    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function izins() { return $this->hasMany(Izin::class, 'guru_id'); }
}
