<?php

namespace Tests\Feature\Api;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlaneReservationControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseTruncation;
    
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
            'comment' => 'some comment',
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
            'comment' => 'some comment',
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

        $response = $this->delete('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2');
        // when
        $response = $this->delete('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2');
        
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

        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertEmpty($response->json());
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

        $response = $this->delete('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2');
        // when
        $response = $this->delete('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2');
        
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

        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertEmpty($response->json());
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

        // when
        $response = $this->delete('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2');
        
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

        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertNotEmpty($response->json());
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
        $response = $this->delete('/api/plane/reservation/01HE68XAY50PSC2WKAFS2M7NXP');
        
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

        $response = $this->get('/api/plane/SP-KYS/reservation/2023-10-29');
        $this->assertNotEmpty($response->json());
    }

    public function test_confirm_reservation(): void
    {
        // given
        $admin = User::factory()->create([
            'id' => '01HE69WJM5FNFEFPV321F9240Y',
            'role' => UserRole::Admin,
        ]);
        Sanctum::actingAs($admin, ['*']);

        User::factory()->create([
            'id' => '01HM5ASEDHKZF8JC66FD4ZAR3S',
            'role' => UserRole::User,
        ]);

        /** @var Plane $plane */
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => '01HM5ASEDHKZF8JC66FD4ZAR3S',
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

        $response = $this->patch('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2/confirm');
        
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

        $dummySmsClient = $this->app->get(\App\Infrastructure\SmsSender\DummySmsClient::class);
        $this->assertCount(1, $dummySmsClient->smses);
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

        $response = $this->patch('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2/confirm');
        
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
        
        $response1 = $this->patch('/api/plane/reservation/01HE68JBYDRR96FVYZYK7D7JS2/confirm');
        $response2 = $this->patch('/api/plane/reservation/01HE6AE4K6D2YYDE5GWHCK13GG/confirm');
        
        // then
        $response1->assertStatus(404);
        $response2->assertStatus(404);
    }

    public function test_get_all_reservations_for_date(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);
        Sanctum::actingAs($user, ['*']);

        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-ABC',
        ]);
        $plane2 = Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-DEF',
        ]);

        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS2',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-11-01 10:00:00',
            'ends_at' => '2023-11-01 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS3',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-11-01 12:00:00',
            'ends_at' => '2023-11-01 13:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);
        PlaneReservation::factory()->create([
            'id' => '01HE68JBYDRR96FVYZYK7D7JS4',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-11-01 14:00:00',
            'ends_at' => '2023-11-01 15:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
        ]);

        // when
        $response = $this->get('/api/plane/reservation/date/2023-11-01');
        
        // then
        $response->assertStatus(200);
        $response->assertJsonCount(2);

        $response->assertJsonStructure([
            '*' => [
                '*' => [
                    'id',
                    'starts_at',
                    'ends_at',
                    'is_confirmed',
                    'can_confirm',
                    'can_remove',
                    'user_name',
                    'comment',
                ],
            ],
        ]);
    }


    public function testAdminShouldBeAbleToReserveWithSecondUser(): void
    {
        // given
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);
        Sanctum::actingAs($admin, ['*']);

        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-ABC',
        ]);

        // when
        Carbon::setTestNow('2023-10-28 12:13:14');
        CarbonImmutable::setTestNow('2023-10-28 12:13:14');

        $response = $this->post('/api/plane/SP-ABC/reservation/2023-11-01', [
            'user_id' => $admin->id,
            'starts_at' => '2023-11-01 10:00:00',
            'ends_at' => '2023-11-01 11:59:00',
            'comment' => 'some comment',
            'user2_id' => $user->id,
        ]);
        
        // then
        $response->assertStatus(201);

        $this->assertDatabaseCount('plane_reservations', 1);
        $this->assertDatabaseHas('plane_reservations', [
            'user_id' => $admin->id,
            'user2_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-11-01 10:00:00',
            'ends_at' => '2023-11-01 11:59:00',
            'time' => 119,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'deleted_at' => null,
            'comment' => 'some comment',
        ]);
    }
}
