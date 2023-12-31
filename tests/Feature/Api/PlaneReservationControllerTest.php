<?php

namespace Tests\Feature\Api;

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
    
    public function test_get_reservations_by_plane_and_date_should_be_sorted_by_start_time(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin',
        ]);

        Sanctum::actingAs($user, ['*']);

        
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        PlaneReservation::create([
            'id' => '01HEBWJJGFE9WXK4SPQ8XXWGPB',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        PlaneReservation::create([
            'id' => '01HEBWPFCWB7FNHQTPM96QNEQJ',
            'user_id' => $admin->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        // should not be returned - different day
        PlaneReservation::create([
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-30 10:00:00',
            'ends_at' => '2023-10-30 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // should not be returned - same date, different plane
        PlaneReservation::create([
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        // when
        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_name',
                'starts_at',
                'ends_at',
                'is_confirmed',
                'can_remove',
            ],
        ]);
        $response->assertJson([
            [
                'id' => '01HEBWPFCWB7FNHQTPM96QNEQJ',
                'user_name' => 'Admin',
                'starts_at' => '08:00',
                'ends_at' => '10:00',
                'is_confirmed' => true,
                'can_remove' => false,
            ],
            [
                'id' => '01HEBWJJGFE9WXK4SPQ8XXWGPB',
                'user_name' => 'John Doe',
                'starts_at' => '10:00',
                'ends_at' => '12:00',
                'is_confirmed' => true,
                'can_remove' => true,
            ],
        ]);
    }

    /**
     * @dataProvider userRoleProvider
     */
    public function test_make_reservations_for_plane_and_date(UserRole $userRole): void
    {
        // given
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $user = User::factory()->create([
            'role' => $userRole,
            'id' => '01HEDPF462PP4CR8X0RCS0X155',
        ]);

        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        // when
        $response = $this->post('/api/plane/SP-KYS/reservation/2023-10-29', [
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
        ]);
        
        // then
        $response->assertStatus(201);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'user_id' => '01HEDPF462PP4CR8X0RCS0X155',
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
    }

    public static function userRoleProvider(): iterable
    {
        yield 'user' => [UserRole::User];
        yield 'admin' => [UserRole::Admin];
    }

    public function test_user_should_not_reserve_plane_for_other_user(): void
    {
        // given
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        $user2 = User::factory()->create([
            'role' => UserRole::User,
            'id' => '01HEDPF462PP4CR8X0RCS0X155',
        ]);

        Sanctum::actingAs($user, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        // when
        $response = $this->post('/api/plane/SP-KYS/reservation/2023-10-29', [
            'user_id' => '01HEDPF462PP4CR8X0RCS0X155',
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
        ]);
        
        // then
        $response->assertStatus(201);
    }

    public function test_admin_should_be_able_to_reserve_plane_for_other_user(): void
    {
        // given
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'id' => '01HEDPQRMC3DANY1MHXHTXBW37',
        ]);
        $user2 = User::factory()->create([
            'role' => UserRole::User,
            'id' => '01HEDPF462PP4CR8X0RCS0X155',
        ]);

        Sanctum::actingAs($admin, ['*']);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        // when
        $response = $this->post('/api/plane/SP-KYS/reservation/2023-10-29', [
            'user_id' => '01HEDPF462PP4CR8X0RCS0X155',
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 11:59:00',
        ]);
        
        // then
        $response->assertStatus(201);
        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'user_id' => '01HEDPF462PP4CR8X0RCS0X155',
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
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

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
        $this->assertEmpty($reponse->json());
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
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

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
        $this->assertEmpty($reponse->json());
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
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

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
        $this->assertNotEmpty($reponse->json());
    }


    public function test_it_should_be_impossible_to_remove_non_existing_reservation(): void
    {
        // given
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

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
        $this->assertNotEmpty($reponse->json());
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
