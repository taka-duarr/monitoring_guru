<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function jurusan() { return $this->belongsTo(Jurusan::class, 'jurusan_id'); }
    public function ketua() { return $this->belongsTo(User::class, 'ketua_id'); }
    public function statusKelas() { return $this->hasOne(StatusKelas::class, 'kelas_id'); }
}