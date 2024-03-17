<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\MonthlyLimitCheck;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyLimitCheckTest extends TestCase
{
    use RefreshDatabase;
    private MonthlyLimitCheck $service;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->sunTimeService = $this->createMock(SunTimeService::class);
        $this->service = new MonthlyLimitCheck(240);
    }
    /** @test */
    public function userShouldBeAbleToReserveUpToMonthlyLimit(): void
    {
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '8f9cac96-0c45-4890-9358-85f34ce3cbfc']);
        Plane::factory()->create(['id' => 'a45d1262-c710-4af0-bb70-1d7a4cc13d7c']);

        PlaneReservation::factory()->create([
            'plane_id' => '8f9cac96-0c45-4890-9358-85f34ce3cbfc',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => '8f9cac96-0c45-4890-9358-85f34ce3cbfc',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => 'a45d1262-c710-4af0-bb70-1d7a4cc13d7c',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-21 12:00:00',
            'ends_at' => '2021-01-21 15:00:00',
            'time' => 60,
        ]);

        
        // when
        $this->service->check(
            CarbonImmutable::parse('2021-01-10 13:00'),
            CarbonImmutable::parse('2021-01-10 14:00'),
            $user,
            'some plane id',
        );
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldNotBeAbleToReserveMoreThanMonthlyLimit(): void
    {
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '34efffb9-43a8-454f-8583-be59956991c7']);
        Plane::factory()->create(['id' => '03bac3ab-175c-4f39-92de-29f4af4370f6']);
        PlaneReservation::factory()->create([
            'plane_id' => '34efffb9-43a8-454f-8583-be59956991c7',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => '34efffb9-43a8-454f-8583-be59956991c7',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => '03bac3ab-175c-4f39-92de-29f4af4370f6',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-21 12:00:00',
            'ends_at' => '2021-01-21 15:00:00',
            'time' => 60,
        ]);

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('you can reserve planes for max 4 hours monthly');
        
        // when
        $this->service->check(
            CarbonImmutable::parse('2021-01-10 13:00'),
            CarbonImmutable::parse('2021-01-10 14:01'),
            $user,
            'some plane id',
        );
    }
}
