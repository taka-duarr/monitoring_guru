<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Str;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'school_name',
                'value' => 'SMKN 2 SURABAYA',
                'group' => 'school',
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Contoh No. 1, Surabaya, Jawa Timur',
                'group' => 'school',
            ],
            [
                'key' => 'school_phone',
                'value' => '(031) 000-0000',
                'group' => 'school',
            ],
            [
                'key' => 'school_logo',
                'value' => null,
                'group' => 'school',
            ],
            [
                'key' => 'headmaster_name',
                'value' => 'Drs. H. Mulyono, M.Pd.',
                'group' => 'school',
            ],
            [
                'key' => 'headmaster_nip',
                'value' => '197001011995011001',
                'group' => 'school',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'id' => (string) Str::uuid(),
                    'value' => $setting['value'],
                    'group' => $setting['group']
                ]
            );
        }
    }
}
