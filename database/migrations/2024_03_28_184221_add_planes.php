<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
        \App\Models\Plane::factory()->create([
            'name' => 'Viper SD-4 RTC',
            'registration' => 'SP-AOD',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
