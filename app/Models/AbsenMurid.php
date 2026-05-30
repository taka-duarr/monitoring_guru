<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenMurid extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $guarded = [];

    public function absenMasuk() { return $this->belongsTo(AbsenMasuk::class, 'absen_masuk_id'); }
    public function murid() { return $this->belongsTo(Murid::class, 'murid_id'); }
}
