<?php

use App\Models\UserRole;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => getenv('APP_ADMIN_EMAIL'),
            'role' => UserRole::Admin,
            'password' => Hash::make(getenv('APP_ADMIN_PASSWORD')),
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
