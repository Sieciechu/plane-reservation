<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => getenv('APP_ADMIN_EMAIL'),
            'role' => UserRole::Admin,
            'password' => Hash::make(getenv('APP_ADMIN_PASSWORD')),
        ]);
        
        if (App::environment() === 'production') {
            return;
        }
        \App\Models\User::factory(10)->create();
    }
}
