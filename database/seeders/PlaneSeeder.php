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
    }
}
