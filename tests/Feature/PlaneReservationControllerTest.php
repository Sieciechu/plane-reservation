<?php

namespace Tests\Feature;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaneReservationControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_reservations_by_plane_and_date(): void
    {
        // given
        $user = User::factory()->create([
            'role' => \App\Models\UserRole::User,
        ]);
        $admin = User::factory()->create([
            'role' => \App\Models\UserRole::Admin,
        ]);

        
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::create([
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at_date' => '2023-10-29',
            'ends_at_date' => '2023-10-29',
            'starts_at_time' => '10:00',
            'ends_at_time' => '11:59',
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        // when
        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'user_id' => $user->id,
                    'plane_id' => $plane->id,
                    'starts_at_date' => '2023-10-29',
                    'ends_at_date' => '2023-10-29',
                    'starts_at_time' => '10:00:00',
                    'ends_at_time' => '11:59:00',
                    'confirmed_at' => '2023-10-28 12:13:14',
                    'confirmed_by' => $admin->id,
                    'deleted_at' => null,
                ],
            ],
        ]);
    }

    public function test_make_reservations_for_plane_and_date(): void
    {
        // given
        $user = User::factory()->create([
            'role' => \App\Models\UserRole::User,
        ]);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        // when
        $response = $this->post('/api/plane/SP-KYS/reservation/2023-10-29', [
            'user_id' => $user->id,
            'starts_at_time' => '10:00',
            'ends_at_time' => '11:59',
        ]);
        
        // then
        $response->assertStatus(201);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at_date' => '2023-10-29',
            'ends_at_date' => '2023-10-29',
            'starts_at_time' => '10:00',
            'ends_at_time' => '11:59',
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
    }
}
