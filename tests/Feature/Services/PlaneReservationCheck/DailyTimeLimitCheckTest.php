<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\DailyTimeLimitCheck;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyTimeLimitCheckTest extends TestCase
{
    use RefreshDatabase;

    private DailyTimeLimitCheck $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new DailyTimeLimitCheck(120);
    }

    public function adminShouldBeAbleToReserveDailyWithoutLimit(): void
    {
        // given
        $admin = new User(['role' => UserRole::Admin]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 18:00');
        // when
        $this->service->check($startDate, $endDate, $admin, "some plane id");
    }

    /** @test */
    public function userShouldBeAbleToReserveDailyUpToDailyLimitWithOneGo(): void
    {
        // given
        Plane::factory()->create(['id' => '442a26b4-0f2b-42dc-ac8d-a0e18730f2a4']);

        $user = new User(['role' => UserRole::User]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 12:00');
        
        // when
        $this->service->check($startDate, $endDate, $user, '442a26b4-0f2b-42dc-ac8d-a0e18730f2a4');
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldBeAbleToReserveDailyUpToDailyLimitInTotal(): void
    {
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => 'a8c35cc5-9dbc-4806-951a-5a3be4871f3d']);
        PlaneReservation::factory()->create([
            'plane_id' => 'a8c35cc5-9dbc-4806-951a-5a3be4871f3d',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);

        // when
        $this->service->check(
            CarbonImmutable::parse('2021-01-01 13:00'),
            CarbonImmutable::parse('2021-01-01 14:00'),
            $user,
            'a8c35cc5-9dbc-4806-951a-5a3be4871f3d'
        );
        $this->service->check(
            CarbonImmutable::parse('2021-02-01 12:00'),
            CarbonImmutable::parse('2021-02-01 14:00'),
            $user,
            'a8c35cc5-9dbc-4806-951a-5a3be4871f3d'
        );
        $this->assertTrue(true);
    }

    /** @test */
    public function whenUserExceedsDailyLimitWithOneGoThenCheckShouldFail(): void
    {
        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('you can reserve plane for max 2 hours daily');

        // given
        Plane::factory()->create(['id' => '2097a341-7303-4fef-bc34-e3886863d4e4']);

        $user = new User(['role' => UserRole::User]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 12:01');
        
        // when
        $this->service->check($startDate, $endDate, $user, '2097a341-7303-4fef-bc34-e3886863d4e4');
    }

    /** @test */
    public function whenUserExceedsDailyLimitInTotalThenCheckShouldFail(): void
    {
        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('you can reserve plane for max 2 hours daily');
        
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '93355f28-ea65-4d5a-9faa-356ecbcf8bea']);
        PlaneReservation::factory()->create([
            'plane_id' => '93355f28-ea65-4d5a-9faa-356ecbcf8bea',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);

        $startDate = CarbonImmutable::parse('2021-01-01 13:00');
        $endDate = CarbonImmutable::parse('2021-01-01 14:01');
        
        // when
        $this->service->check($startDate, $endDate, $user, '93355f28-ea65-4d5a-9faa-356ecbcf8bea');
    }

    /** @test */
    public function adminShouldBeAbleToReserveWithoutMonthlyLimit(): void
    {
        // given
        $admin = new User(['role' => UserRole::Admin]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-03-01 18:00');
        // when
        $this->service->check($startDate, $endDate, $admin, "some plane id");
        $this->assertTrue(true);
    }
}
