<?php

namespace Tests\Feature;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlaneReservationControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_reservations_by_plane_and_date(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Sanctum::actingAs($user, ['*']);

        
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::create([
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        // when
        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'plane_id',
                    'starts_at',
                    'ends_at',
                    'time',
                    'confirmed_at',
                    'confirmed_by',
                    'deleted_at',
                ],
            ],
        ]);
        $response->assertJson([
            'data' => [
                [
                    'user_id' => $user->id,
                    'plane_id' => $plane->id,
                    'starts_at' => '2023-10-29 10:00:00',
                    'ends_at' => '2023-10-29 11:59:00',
                    'time' => 119,
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
        Carbon::setTestNow('2023-10-28 12:13:14');

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        // when
        $response = $this->post('/api/plane/SP-KYS/reservation/2023-10-29', [
            'user_id' => $user->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
        ]);
        
        // then
        $response->assertStatus(201);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_remove_any_reservation(): void
    {
        // given
        Sanctum::actingAs(
            User::factory()->create(['role' => UserRole::Admin]),
            ['*']
        );

        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');

        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        // when
        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        
        // then
        $response->assertStatus(200);
        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => '2023-10-28 12:13:14',
        ]);

        $reponse = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertEmpty($reponse->json()['data']);
    }

    public function test_owner_can_remove_his_reservation(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');

        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        // when
        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        
        // then
        $response->assertStatus(200);
        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => '2023-10-28 12:13:14',
        ]);

        $reponse = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertEmpty($reponse->json()['data']);
    }

    public function test_regular_user_cannot_remove_others_reservation(): void
    {
        // given
        $user = User::factory()->create(['role' => UserRole::User]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        $user2 = User::factory()->create(['role' => UserRole::User]);
        Sanctum::actingAs($user2, ['*']);
        Carbon::setTestNow('2023-10-28 12:13:14');

        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        // when
        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        
        // then
        $response->assertStatus(403);
        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        $reponse = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertNotEmpty($reponse->json()['data']);
    }


    public function test_it_should_be_impossible_to_remove_non_existing_reservation(): void
    {
        // given
        Carbon::setTestNow('2023-10-28 12:13:14');

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        $response = $this->delete('/api/plane/reservation/', [
            'reservation_id' => '01HE68XAY50PSC2WKAFS2M7NXP',
        ]);
        
        // then
        $response->assertStatus(404);
        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        $reponse = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertNotEmpty($reponse->json()['data']);
    }

    public function test_confirm_reservation(): void
    {
        // given
        $user = User::factory()->create([
            'id' => '01HE69WJM5FNFEFPV321F9240Y',
            'role' => UserRole::Admin,
        ]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $response = $this->post('/api/plane/reservation/confirm', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        
        // then
        $response->assertStatus(200);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => '01HE69WJM5FNFEFPV321F9240Y',
            'deleted_at' => null,
        ]);
    }

    public function test_regular_user_cannot_confirm_any_reservation(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $response = $this->post('/api/plane/reservation/confirm', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        
        // then
        $response->assertStatus(403);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
    }

    public function test_it_should_be_impossible_to_confirm_non_existant_or_removed_reservation(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);
        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => '2023-10-30 11:59:00',
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');
        
        $response1 = $this->post('/api/plane/reservation/confirm', [
            'reservation_id' => '01HE68JBYDRR96FVYZYK7D7JS2',
        ]);
        $response2 = $this->post('/api/plane/reservation/confirm', [
            'reservation_id' => '01HE6AE4K6D2YYDE5GWHCK13GG',
        ]);
        
        // then
        $response1->assertStatus(404);
        $response2->assertStatus(404);
    }
}
