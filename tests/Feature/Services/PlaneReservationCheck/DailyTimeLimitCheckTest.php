<?php

declare(strict_types=1);

namespace Tests\Feature\PlaneReservationCheck;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\DailyTimeLimitCheck;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
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
        Plane::factory()->create(['id' => Ulid::fromString('01F9ZJZJZJZJZJZJZJZJZJZJZJ')]);

        $user = new User(['role' => UserRole::User]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 12:00');
        
        // when
        $this->service->check($startDate, $endDate, $user, '01F9ZJZJZJZJZJZJZJZJZJZJZJ');
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldBeAbleToReserveDailyUpToDailyLimitInTotal(): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1FBZEPC8SRGM7VQDQV4K9X')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1FBZEPC8SRGM7VQDQV4K9X'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);

        // when
        $this->service->check(
            CarbonImmutable::parse('2021-01-01 13:00'),
            CarbonImmutable::parse('2021-01-01 14:00'),
            $user,
            '01HE1FBZEPC8SRGM7VQDQV4K9X'
        );
        $this->service->check(
            CarbonImmutable::parse('2021-02-01 12:00'),
            CarbonImmutable::parse('2021-02-01 14:00'),
            $user,
            '01HE1FBZEPC8SRGM7VQDQV4K9X'
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
        Plane::factory()->create(['id' => Ulid::fromString('01HE1FN3P71S3V242YXJ9XMQVT')]);

        $user = new User(['role' => UserRole::User]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 12:01');
        
        // when
        $this->service->check($startDate, $endDate, $user, '01HE1FN3P71S3V242YXJ9XMQVT');
    }

    /** @test */
    public function whenUserExceedsDailyLimitInTotalThenCheckShouldFail(): void
    {
        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('you can reserve plane for max 2 hours daily');
        
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1FNZZX6XPBTDFTN8A66Y69')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1FNZZX6XPBTDFTN8A66Y69'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);

        $startDate = CarbonImmutable::parse('2021-01-01 13:00');
        $endDate = CarbonImmutable::parse('2021-01-01 14:01');
        
        // when
        $this->service->check($startDate, $endDate, $user, '01HE1FNZZX6XPBTDFTN8A66Y69');
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
