<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Murid extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $fillable = [
        'id',
        'kelas_id',
        'nis',
        'name',
        'no_absen',
        'status',
    ];

    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
}
