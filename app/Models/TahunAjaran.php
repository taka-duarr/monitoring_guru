<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'tahun_mulai',
        'tahun_selesai',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jadwalAjars()
    {
        return $this->hasMany(JadwalAjar::class, 'tahun_ajaran_id');
    }

    /**
     * Get the currently active Tahun Ajaran
     */
    public static function aktif(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
