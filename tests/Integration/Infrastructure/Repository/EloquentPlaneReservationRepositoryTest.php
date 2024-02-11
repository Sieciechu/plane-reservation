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
            'id' => '01HEBWJJGFE9WXK4SPQ8XXWGPB',
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
            'id' => '01HEBWPFCWB7FNHQTPM96QNEQJ',
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
        $this->assertEquals('01HEBWPFCWB7FNHQTPM96QNEQJ', $result[0]->id);
        $this->assertEquals($user2->id, $result[0]->user_id);
        $this->assertEquals($plane->id, $result[0]->plane_id);
        $this->assertEquals('2023-10-29 08:00:00', $result[0]->starts_at);
        $this->assertEquals('2023-10-29 10:00:00', $result[0]->ends_at);
        $this->assertEquals(120, $result[0]->time);
        $this->assertNull($result[0]->confirmed_at);
        $this->assertNull($result[0]->confirmed_by);
        
        $this->assertEquals('01HEBWJJGFE9WXK4SPQ8XXWGPB', $result[1]->id);
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
            'id' => '01HEBWJJGFE9WXK4SPQ8XXWGPB',
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
            'id' => '01HEBWPFCWB7FNHQTPM96QNEQJ',
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

        $this->assertEquals('01HEBWPFCWB7FNHQTPM96QNEQJ', $result[0]->id);
        $this->assertEquals($user2->id, $result[0]->user_id);
        $this->assertEquals($plane->id, $result[0]->plane_id);
        $this->assertEquals('2023-10-29 08:00:00', $result[0]->starts_at);
        $this->assertEquals('2023-10-29 10:00:00', $result[0]->ends_at);
        $this->assertEquals(120, $result[0]->time);
        $this->assertNull($result[0]->confirmed_at);
        $this->assertNull($result[0]->confirmed_by);

        $this->assertEquals('01HEBWJJGFE9WXK4SPQ8XXWGPB', $result[1]->id);
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
}
