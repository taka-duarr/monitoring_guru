<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function jadwalAjar() { return $this->belongsTo(JadwalAjar::class, 'jadwal_ajar_id'); }
    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
}