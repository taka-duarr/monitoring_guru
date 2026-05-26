<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guru extends Authenticatable
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function jadwalAjars() { return $this->hasMany(JadwalAjar::class, 'guru_id'); }
}