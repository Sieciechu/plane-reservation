<?php

namespace Database\Seeders;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaneReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', '!=', 'admin')->take(3)->get();
        $admin = User::where('name', 'Admin')->first();

        $plane1 = Plane::where('registration', 'SP-KYS')->first();

        $date = '2023-10-28';

        PlaneReservation::create([
            'user_id' => $users[0]->id,
            'plane_id' => $plane1->id,
            'starts_at_date' => $date,
            'ends_at_date' => $date,
            'starts_at_time' => '10:00',
            'ends_at_time' => '11:59',
            'confirmed_at' => $date,
            'confirmed_by' => $admin->id,
        ]);
        PlaneReservation::create([
            'user_id' => $users[1]->id,
            'plane_id' => $plane1->id,
            'starts_at_date' => $date,
            'ends_at_date' => $date,
            'starts_at_time' => '12:00',
            'ends_at_time' => '13:00',
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        PlaneReservation::create([
            'user_id' => $users[1]->id,
            'plane_id' => $plane1->id,
            'starts_at_date' => $date,
            'ends_at_date' => $date,
            'starts_at_time' => '15:00',
            'ends_at_time' => '15:45',
            'confirmed_at' => $date,
            'confirmed_by' => $admin->id,
        ]);
    }
}
