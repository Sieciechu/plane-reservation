<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\SecondPilotByAdminOnlyCheck;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class SecondPilotByAdminOnlyCheckTest extends TestCase
{
    private SecondPilotByAdminOnlyCheck $service;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->service = new SecondPilotByAdminOnlyCheck();
    }
    public function testOnlyAdminCanMakeReservationWithSecondUser(): void
    {
        // given
        $user = new User();
        $user->role = UserRole::Admin;

        $user2 = new User();
        $user2->role = UserRole::User;

        // when
        $this->service->check(
            CarbonImmutable::parse('2023-01-01 05:00:00', 'Europe/Warsaw'),
            CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw'),
            $user,
            'some plane id',
            $user2
        );

        // then
        $this->assertTrue(true);
    }

    public function testNonAdminUserCannotMakeReservationWithSecondUser(): void
    {
        // given
        $user = new User();
        $user->role = UserRole::User;

        $user2 = new User();
        $user2->role = UserRole::User;

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('only admin can add second pilot');

        // when
        $this->service->check(
            CarbonImmutable::parse('2023-01-01 05:00:00', 'Europe/Warsaw'),
            CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw'),
            $user,
            'some plane id',
            $user2
        );
    }
}
