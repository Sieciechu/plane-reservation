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
            'id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '01HE1G3R4YDG9H3WRGQPQ8FKV9']);
        Plane::factory()->create(['id' => '01HE1GCNTM44CKBFPMRX33WZ1D']);

        PlaneReservation::factory()->create([
            'plane_id' => '01HE1G3R4YDG9H3WRGQPQ8FKV9',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => '01HE1G3R4YDG9H3WRGQPQ8FKV9',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => '01HE1GCNTM44CKBFPMRX33WZ1D',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
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
            'id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '01HE1GB7NJSMF037F76BVR1D1M']);
        Plane::factory()->create(['id' => '01HE1GRM0B8RQTEX4KYFT7Q7TR']);
        PlaneReservation::factory()->create([
            'plane_id' => '01HE1GB7NJSMF037F76BVR1D1M',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => '01HE1GB7NJSMF037F76BVR1D1M',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => '01HE1GRM0B8RQTEX4KYFT7Q7TR',
            'user_id' => '01HE1F50RYFHQS5HCTYWHDWYKY',
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
