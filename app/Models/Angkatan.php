<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Angkatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['id', 'name'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'angkatan_id');
    }
}
