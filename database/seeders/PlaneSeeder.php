<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (\App\Models\Plane::count() > 0) {
            return;
        }

        \App\Models\Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        \App\Models\Plane::factory()->create([
            'name' => 'PZL Koliber 110',
            'registration' => 'SP-ARR',
        ]);
        \App\Models\Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);
        \App\Models\Plane::factory()->create([
            'name' => 'Viper SD-4 RTC',
            'registration' => 'SP-AOD',
        ]);
    }
}
