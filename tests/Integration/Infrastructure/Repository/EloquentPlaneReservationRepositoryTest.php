<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Infrastructure\Repository\EloquentPlaneReservationRepository;
use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentPlaneReservationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentPlaneReservationRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo = new EloquentPlaneReservationRepository();
    }

    /** @test */
    public function itShouldReturnEmptyArrayWhenNoReservations(): void
    {
        // given
        $date = CarbonImmutable::parse('2021-01-01 00:00:00', 'Europe/Warsaw');

        // when
        $result = $this->repo->getAllReservationsForDate($date);

        // then
        $this->assertEmpty($result);
    }

    /** @test */
    public function itShouldReturnReservationsForGivenDate(): void
    {
        // given
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin',
        ]);
        $user = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);
        $user2 = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Harry Fisher',
        ]);

        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        // should be second as it starts later
        PlaneReservation::create([
            'id' => '43950b1e-cabd-413c-bd1e-c6c10dd9fbab',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // different user, should be 1st as it starts first
        PlaneReservation::create([
            'id' => '31ee2a45-7c99-4722-8194-e966fe24ac28',
            'user_id' => $user2->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        // different plane
        PlaneReservation::create([
            'id' => '01HNBQZ918VZ74DJ24F6R3JQNA',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
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
        
        // should not be returned - different day and user
        PlaneReservation::create([
            'user_id' => $user2->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-30 10:00:00',
            'ends_at' => '2023-10-30 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        $date = CarbonImmutable::parse('2023-10-29 00:00:00');

        // when
        $result = $this->repo->getAllReservationsForDate($date);

        // then
        $this->assertCount(3, $result);

        // Assert the second reservation
        $this->assertEquals('31ee2a45-7c99-4722-8194-e966fe24ac28', $result[0]->id);
        $this->assertEquals($user2->id, $result[0]->user_id);
        $this->assertEquals($plane->id, $result[0]->plane_id);
        $this->assertEquals('2023-10-29 08:00:00', $result[0]->starts_at);
        $this->assertEquals('2023-10-29 10:00:00', $result[0]->ends_at);
        $this->assertEquals(120, $result[0]->time);
        $this->assertNull($result[0]->confirmed_at);
        $this->assertNull($result[0]->confirmed_by);
        
        $this->assertEquals('43950b1e-cabd-413c-bd1e-c6c10dd9fbab', $result[1]->id);
        $this->assertEquals($user->id, $result[1]->user_id);
        $this->assertEquals($plane->id, $result[1]->plane_id);
        $this->assertEquals('2023-10-29 10:00:00', $result[1]->starts_at);
        $this->assertEquals('2023-10-29 12:00:00', $result[1]->ends_at);
        $this->assertEquals(120, $result[1]->time);
        $this->assertEquals('2023-10-28 12:13:14', $result[1]->confirmed_at);
        $this->assertEquals($admin->id, $result[1]->confirmed_by);

        // Assert the third reservation
        $this->assertEquals('01HNBQZ918VZ74DJ24F6R3JQNA', $result[2]->id);
        $this->assertEquals($user->id, $result[2]->user_id);
        $this->assertEquals($plane2->id, $result[2]->plane_id);
        $this->assertEquals('2023-10-29 10:00:00', $result[2]->starts_at);
        $this->assertEquals('2023-10-29 12:00:00', $result[2]->ends_at);
        $this->assertEquals(120, $result[2]->time);
        $this->assertEquals('2023-10-28 12:13:14', $result[2]->confirmed_at);
        $this->assertEquals($admin->id, $result[2]->confirmed_by);
    }

    public function testGetReservationsForPlaneAndDate(): void
    {
        // given
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin',
        ]);
        $user = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);
        $user2 = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Harry Fisher',
        ]);

        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        // should be second as it starts later
        PlaneReservation::create([
            'id' => '43950b1e-cabd-413c-bd1e-c6c10dd9fbab',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // different user, should be 1st as it starts first
        PlaneReservation::create([
            'id' => '31ee2a45-7c99-4722-8194-e966fe24ac28',
            'user_id' => $user2->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        // different plane
        PlaneReservation::create([
            'id' => '01HNBQZ918VZ74DJ24F6R3JQNA',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
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

        // should not be returned - different day and user
        PlaneReservation::create([
            'user_id' => $user2->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-30 10:00:00',
            'ends_at' => '2023-10-30 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        $date = CarbonImmutable::parse('2023-10-29 00:00:00');

        // when
        $result = $this->repo->getReservationsForPlaneAndDate($plane, $date);

        // then
        $this->assertCount(2, $result);

        $this->assertEquals('31ee2a45-7c99-4722-8194-e966fe24ac28', $result[0]->id);
        $this->assertEquals($user2->id, $result[0]->user_id);
        $this->assertEquals($plane->id, $result[0]->plane_id);
        $this->assertEquals('2023-10-29 08:00:00', $result[0]->starts_at);
        $this->assertEquals('2023-10-29 10:00:00', $result[0]->ends_at);
        $this->assertEquals(120, $result[0]->time);
        $this->assertNull($result[0]->confirmed_at);
        $this->assertNull($result[0]->confirmed_by);

        $this->assertEquals('43950b1e-cabd-413c-bd1e-c6c10dd9fbab', $result[1]->id);
        $this->assertEquals($user->id, $result[1]->user_id);
        $this->assertEquals($plane->id, $result[1]->plane_id);
        $this->assertEquals('2023-10-29 10:00:00', $result[1]->starts_at);
        $this->assertEquals('2023-10-29 12:00:00', $result[1]->ends_at);
        $this->assertEquals(120, $result[1]->time);
        $this->assertEquals('2023-10-28 12:13:14', $result[1]->confirmed_at);
        $this->assertEquals($admin->id, $result[1]->confirmed_by);
    }

    public function testWhenNothingFoundGetReservationsForPlaneAndDateShouldReturnEmptyResult(): void
    {
        // given
        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);

        $date = CarbonImmutable::parse('2023-10-29 00:00:00');

        // when
        $result = $this->repo->getReservationsForPlaneAndDate($plane, $date);

        // then
        $this->assertEmpty($result);
        $this->assertCount(0, $result);
    }

    public function testGetUserAllUpcomingReservationsStartingFromDate(): void
    {
        // given
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin',
        ]);
        $user = User::factory()->create([
            'id' => 'b77efc8e-76b9-49ba-a7fc-478f19369475',
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);
        $user2 = User::factory()->create([
            'role' => UserRole::User,
            'name' => 'Harry Fisher',
        ]);

        $plane = Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->create([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        // should be second as it starts later
        PlaneReservation::create([
            'id' => 'e0b98582-f1c5-469c-946c-d6fe01ab8efd',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 11:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // different user, should not be included
        PlaneReservation::create([
            'id' => '20f6e66f-e096-4566-b7da-8f0c1a2d6159',
            'user_id' => $user2->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        // different plane, should be included
        PlaneReservation::create([
            'id' => '7fdc6e02-b8b6-431e-9669-c855c5cf7843',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        // same user different date, should be included
        PlaneReservation::create([
            'id' => 'f8dcef0b-4c93-49eb-b271-cc885bfdf743',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-30 10:00:00',
            'ends_at' => '2023-10-30 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // same user different date, should be included
        PlaneReservation::create([
            'id' => '70c4ed40-4447-4336-a433-936150723462',
            'user_id' => $user->id,
            'plane_id' => $plane->id,
            'starts_at' => '2023-10-31 10:00:00',
            'ends_at' => '2023-10-31 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        // same user, different date and plane, should be included
        PlaneReservation::create([
            'id' => 'e517097f-22ac-438c-bc88-0543cd295321',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-31 10:00:00',
            'ends_at' => '2023-10-31 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);

        $startsAt = CarbonImmutable::parse('2023-10-29 00:00:00');

        // when
        $result = $this->repo->getUserAllUpcomingReservationsStartingFromDate($user, $startsAt);

        // then
        $this->assertCount(5, $result);
        
        $this->assertEquals('7fdc6e02-b8b6-431e-9669-c855c5cf7843', $result[0]->id);
        $this->assertEquals('b77efc8e-76b9-49ba-a7fc-478f19369475', $result[0]->user_id);

        $this->assertEquals('e0b98582-f1c5-469c-946c-d6fe01ab8efd', $result[1]->id);
        $this->assertEquals('b77efc8e-76b9-49ba-a7fc-478f19369475', $result[1]->user_id);

        $this->assertEquals('f8dcef0b-4c93-49eb-b271-cc885bfdf743', $result[2]->id);
        $this->assertEquals('b77efc8e-76b9-49ba-a7fc-478f19369475', $result[2]->user_id);

        $this->assertEquals('70c4ed40-4447-4336-a433-936150723462', $result[3]->id);
        $this->assertEquals('b77efc8e-76b9-49ba-a7fc-478f19369475', $result[3]->user_id);

        $this->assertEquals('e517097f-22ac-438c-bc88-0543cd295321', $result[4]->id);
        $this->assertEquals('b77efc8e-76b9-49ba-a7fc-478f19369475', $result[4]->user_id);
    }
}
