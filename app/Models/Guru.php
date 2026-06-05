<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Guru extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Boot the Guru model.
     */
    protected static function booted()
    {
        // Global scope to only return users who are teachers or admins
        static::addGlobalScope('role_guru', function (Builder $builder) {
            $builder->where('jabatan', 'guru');
        });
    }

    /**
     * Scope a query to filter teachers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });
    }

    /**
     * Helper to generate a short abbreviation for subject names.
     */
    public function getMapelAbbreviation($mapelName)
    {
        $words = explode(' ', str_replace(['&', 'dan', 'atau'], '', $mapelName));
        $words = array_filter($words);
        
        if (count($words) >= 3) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1));
        } elseif (count($words) == 2) {
            return strtoupper(substr($words[0], 0, 2) . substr($words[1], 0, 1));
        } else {
            // If single word, take first 3 chars, e.g., Matematika -> MTK (custom abbreviation mapping)
            $mapelNameLower = strtolower($mapelName);
            $customMap = [
                'matematika' => 'MTK',
                'sejarah' => 'SJR',
                'fisika' => 'FSK',
                'kimia' => 'KIM',
                'biologi' => 'BIO',
                'ekonomi' => 'EKO',
                'geografi' => 'GEO',
                'sosiologi' => 'SOS',
                'indonesia' => 'IND',
                'inggris' => 'ING',
            ];
            foreach ($customMap as $key => $abbr) {
                if (str_contains($mapelNameLower, $key)) {
                    return $abbr;
                }
            }
            return strtoupper(substr($mapelName, 0, 3));
        }
    }
}
