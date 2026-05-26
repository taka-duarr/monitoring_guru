<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKelas extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function kelas() { return $this->belongsTo(Kelas::class, 'kelas_id'); }
}