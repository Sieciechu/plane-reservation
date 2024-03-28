<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plane;
use Illuminate\Database\Seeder;
use App\Models\PlaneReservation;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlaneReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        return;
        if (App::environment() === 'production') {
            return;
        }

        $users = User::where('role', '!=', 'admin')->take(3)->get();
        $admin = User::where('name', 'Admin')->first();

        $plane1 = Plane::where('registration', 'SP-KYS')->first();

        PlaneReservation::create([
            'user_id' => $users[0]->id,
            'plane_id' => $plane1->id,
            'starts_at' => '2023-10-28 10:00:00',
            'ends_at' => '2023-10-28 11:59:00',
            'time' => 119,
            'confirmed_at' => '2023-10-29 13:14:00',
            'confirmed_by' => $admin->id,
        ]);
        PlaneReservation::create([
            'user_id' => $users[1]->id,
            'plane_id' => $plane1->id,
            'starts_at' => '2023-10-28 12:00:00',
            'ends_at' => '2023-10-28 13:00:00',
            'time' => 60,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        PlaneReservation::create([
            'user_id' => $users[1]->id,
            'plane_id' => $plane1->id,
            'starts_at' => '2023-10-28 15:00:00',
            'ends_at' => '2023-10-28 15:45:00',
            'time' => 45,
            'confirmed_at' => '2023-10-29 13:14:00',
            'confirmed_by' => $admin->id,
        ]);
    }
}
