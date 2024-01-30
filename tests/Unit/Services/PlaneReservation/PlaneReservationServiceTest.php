<?php

declare(strict_types=1);

namespace Tests\Unit\Services\PlaneReservation;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneRepository;
use App\Services\PlaneReservation\PlaneReservationRepository;
use App\Services\PlaneReservation\PlaneReservationService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class PlaneReservationServiceTest extends TestCase
{
    private PlaneRepository|MockObject $planeRepo;
    private PlaneReservationRepository|MockObject $planeReservationRepo;
    private PlaneReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planeRepo = $this->createMock(PlaneRepository::class);
        $this->planeReservationRepo = $this->createMock(PlaneReservationRepository::class);

        $this->service = new PlaneReservationService($this->planeRepo, $this->planeReservationRepo);
    }

    public function testGetAllReservationsWithActionsForDateForNonAdminCannotConfirm()
    {
        // given
        $adminId = '5054ecfb-374f-4b22-87e5-66fcaee22310';
        $date = CarbonImmutable::parse('2023-10-29');
        $user = User::factory()->make([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);

        // Set up the planeRepo mock to return two planes
        $plane1 = Plane::factory()->make([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->make([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        $this->planeRepo->expects($this->once())->method('getAll')->willReturn([$plane1, $plane2]);

        // Set up the planeReservationRepo mock to return two reservations
        $reservation1 = PlaneReservation::make([
            'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
            'user_id' => $user->id,
            'plane_id' => $plane1->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $adminId,
        ]);
        $reservation1->user = $user;

        $reservation2 = PlaneReservation::make([
            'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        $reservation2->user = $user;

        $this->planeReservationRepo->expects($this->once())->method('getAllReservationsForDate')
            ->with(CarbonImmutable::parse('2023-10-29'))
            ->willReturn([$reservation1, $reservation2]);


        // when
        $result = $this->service->getAllReservationsWithActionsForDate($date, $user);

        // then
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('SP-KYS', $result);
        $this->assertArrayHasKey('SP-IGA', $result);

        $this->assertCount(1, $result['SP-KYS']);
        $this->assertCount(1, $result['SP-IGA']);

        $this->assertEquals(
            [
                'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
                'starts_at' => '10:00',
                'ends_at' => '12:00',
                'is_confirmed' => true,
                'can_confirm' => false,
                'can_remove' => true,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-KYS'][0]
        );
        $this->assertEquals(
            [
                'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
                'starts_at' => '08:00',
                'ends_at' => '10:00',
                'is_confirmed' => false,
                'can_confirm' => false,
                'can_remove' => true,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-IGA'][0]
        );
    }

    public function testGetAllReservationsWithActionsForDateForAdminCanConfirm()
    {
        // given
        $date = CarbonImmutable::parse('2023-10-29');

        $admin = User::factory()->make([
            'role' => UserRole::Admin,
            'name' => 'Some Admin',
        ]);
        $user = User::factory()->make([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);

        // Set up the planeRepo mock to return two planes
        $planeSpKys = Plane::factory()->make([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $planeSpIga = Plane::factory()->make([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        $this->planeRepo->expects($this->once())->method('getAll')->willReturn([$planeSpKys, $planeSpIga]);

        // Set up the planeReservationRepo mock to return two reservations
        $reservation1 = PlaneReservation::make([
            'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
            'user_id' => $user->id,
            'plane_id' => $planeSpKys->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        $reservation1->user = $user;

        $reservation2 = PlaneReservation::make([
            'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
            'user_id' => $user->id,
            'plane_id' => $planeSpIga->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        $reservation2->user = $user;

        $this->planeReservationRepo->expects($this->once())->method('getAllReservationsForDate')
            ->with(CarbonImmutable::parse('2023-10-29'))
            ->willReturn([$reservation1, $reservation2]);
        
        // when
        $result = $this->service->getAllReservationsWithActionsForDate($date, $admin);

        // then
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('SP-KYS', $result);
        $this->assertArrayHasKey('SP-IGA', $result);

        $this->assertCount(1, $result['SP-KYS']);
        $this->assertCount(1, $result['SP-IGA']);

        $this->assertEquals(
            [
                'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
                'starts_at' => '10:00',
                'ends_at' => '12:00',
                'is_confirmed' => true,
                'can_confirm' => false,
                'can_remove' => true,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-KYS'][0]
        );
        $this->assertEquals(
            [
                'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
                'starts_at' => '08:00',
                'ends_at' => '10:00',
                'is_confirmed' => false,
                'can_confirm' => true,
                'can_remove' => true,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-IGA'][0]
        );
    }

    public function testGetAllReservationsWithActionsForRegularNonOwningUserShouldNotBeAbleToConfirmNorRemove(): void
    {
        // given
        $date = CarbonImmutable::parse('2023-10-29');

        $admin = User::factory()->make([
            'role' => UserRole::Admin,
            'name' => 'Some Admin',
        ]);
        $user = User::factory()->make([
            'role' => UserRole::User,
            'name' => 'John Doe',
        ]);
        $user2 = User::factory()->make([
            'role' => UserRole::User,
            'name' => 'Some Other User',
        ]);

        // Set up the planeRepo mock to return two planes
        $plane1 = Plane::factory()->make([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $plane2 = Plane::factory()->make([
            'name' => 'PZL Koliber 160',
            'registration' => 'SP-IGA',
        ]);

        $this->planeRepo->expects($this->once())->method('getAll')->willReturn([$plane1, $plane2]);

        // Set up the planeReservationRepo mock to return two reservations
        $reservation1 = PlaneReservation::make([
            'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
            'user_id' => $user->id,
            'plane_id' => $plane1->id,
            'starts_at' => '2023-10-29 10:00:00',
            'ends_at' => '2023-10-29 12:00:00',
            'time' => 120,
            'confirmed_at' => '2023-10-28 12:13:14',
            'confirmed_by' => $admin->id,
        ]);
        $reservation1->user = $user;

        $reservation2 = PlaneReservation::make([
            'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
            'user_id' => $user->id,
            'plane_id' => $plane2->id,
            'starts_at' => '2023-10-29 08:00:00',
            'ends_at' => '2023-10-29 10:00:00',
            'time' => 120,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        $reservation2->user = $user;

        $this->planeReservationRepo->expects($this->once())->method('getAllReservationsForDate')
            ->with(CarbonImmutable::parse('2023-10-29'))
            ->willReturn([$reservation1, $reservation2]);
        
        // when
        $result = $this->service->getAllReservationsWithActionsForDate($date, $user2);

        // then
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('SP-KYS', $result);
        $this->assertArrayHasKey('SP-IGA', $result);

        $this->assertCount(1, $result['SP-KYS']);
        $this->assertCount(1, $result['SP-IGA']);

        $this->assertEquals(
            [
                'id' => '6b4066f9-4e2a-406b-a516-cfc9b35c4b6d',
                'starts_at' => '10:00',
                'ends_at' => '12:00',
                'is_confirmed' => true,
                'can_confirm' => false,
                'can_remove' => false,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-KYS'][0]
        );
        $this->assertEquals(
            [
                'id' => '06e24faf-dc79-4d72-b32e-4ea288864d86',
                'starts_at' => '08:00',
                'ends_at' => '10:00',
                'is_confirmed' => false,
                'can_confirm' => false,
                'can_remove' => false,
                'user_name' => 'John Doe',
                'comment' => '',
            ],
            $result['SP-IGA'][0]
        );
    }
}
