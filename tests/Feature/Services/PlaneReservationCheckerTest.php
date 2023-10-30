<?php

namespace Tests\Unit;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationChecker;
use Carbon\CarbonImmutable;
use Exception;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class PlaneReservationCheckerTest extends TestCase
{
    private PlaneReservationChecker $service;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->service = new PlaneReservationChecker(
            monthlyTimeLimitInMinutes: 240,
            dailyTimeLimitInMinutes: 120,
            maxReservationDaysAhead: 30,
        );
    }
    
    /**
     * @test
     * @dataProvider reservationEndsSameMonthWrongDataProvider
     */
    public function whenReservationSplitsAcrosTheMonthThenCheckShouldNotPass(string $startDateString, string $endDateString): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('reservation must end in the same month');
        
        $startDate = CarbonImmutable::parse($startDateString);
        $endDate = CarbonImmutable::parse($endDateString);
        
        $this->service->checkReservationEndsSameMonth($startDate, $endDate);
    }
    
    public static function reservationEndsSameMonthWrongDataProvider(): iterable
    {
        yield ['2021-01-01', '2021-02-01'];
        yield ['2021-01-31', '2021-02-01'];
        yield ['2021-01-16', '2021-04-13'];
    }

    /**
     * @test
     * @dataProvider reservationEndsSameMonthProvider
     */
    public function whenReservationIsWithinSingleMonthThenCheckShouldPass(string $startDateString, string $endDateString): void
    {
        $startDate = CarbonImmutable::parse($startDateString);
        $endDate = CarbonImmutable::parse($endDateString);
        
        $this->service->checkReservationEndsSameMonth($startDate, $endDate);
        
        $this->assertTrue(true);
    }
    
    public static function reservationEndsSameMonthProvider(): iterable
    {
        yield ['2021-01-01', '2021-01-31'];
        yield ['2021-01-31', '2021-01-31'];
        yield ['2021-01-16', '2021-01-18'];
    }

    /** 
     * @test 
     * @dataProvider reservationMoreThan30DaysAheadDateProvider
     */
    public function whenReservationIsMoreThan30AheadThenCheckShouldNotPass(string $now, string $startDateString, string $endDateString): void
    {
        // assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('you can reserve plane for max 30 days ahead');
        
        // given
        $user = new User(['role' => UserRole::User]);
        CarbonImmutable::setTestNow($now);
        $startDate = CarbonImmutable::parse($startDateString);
        $endDate = CarbonImmutable::parse($endDateString);
        
        $this->service->checkMonthAhead($startDate, $endDate, $user);
    }

    public static function reservationMoreThan30DaysAheadDateProvider(): iterable
    {
        yield [
            'now' => '2021-01-01', 
            'startDate' => '2021-01-31',
            'endDate' => '2021-02-01',
        ];
        yield 'even if current day is almost gone, the hours do not matter, check should fail' => [
            'now' => '2021-01-01 23:59:59', 
            'startDate' => '2021-01-20',
            'endDate' => '2021-02-01 00:00:00.00000',
        ];
        yield 'when end date exceeds limit, check should fail' => [
            'now' => '2021-01-01', 
            'startDate' => '2021-01-20',
            'endDate' => '2021-02-01',
        ];
        yield 'when start date exceeds limit, check should fail' => [
            'now' => '2021-01-01', 
            'startDate' => '2021-02-01',
            'endDate' => '2021-01-01',
        ];
    }

    /** 
     * @test 
     * @dataProvider reservationUpTo30DaysAheadDateProvider
     */
    public function whenReservationIsWithin30DaysAheadThenCheckShouldPass(string $now, string $startDateString, string $endDateString): void
    {
        // given
        $user = new User(['role' => UserRole::User]);
        CarbonImmutable::setTestNow($now);
        $startDate = CarbonImmutable::parse($startDateString);
        $endDate = CarbonImmutable::parse($endDateString);
        
        $this->service->checkMonthAhead($startDate, $endDate, $user);
        $this->assertTrue(true);
    }

    public static function reservationUpTo30DaysAheadDateProvider(): iterable
    {
        yield [
            'now' => '2021-01-01', 
            'startDate' => '2021-01-31',
            'endDate' => '2021-01-31',
        ];
        yield [
            'now' => '2021-01-01', 
            'startDate' => '2021-01-20',
            'endDate' => '2021-01-31 23:59:59.99999',
        ];
        yield [
            'now' => '2021-01-01', 
            'startDate' => '2021-01-31 23:59:59.99999',
            'endDate' => '2021-01-31 23:59:59.99999',
        ];
        yield [
            'now' => '2021-02-01', 
            'startDate' => '2021-02-02 23:59:59.99999',
            'endDate' => '2021-02-02 23:59:59.99999',
        ];
        yield [
            'now' => '2021-01-16', 
            'startDate' => '2021-02-14 23:59:59.99999',
            'endDate' => '2021-02-14 23:59:59.99999',
        ];
    }

    /** @test */
    public function whenReservationIsMonthAheadButUserIsAdminThenHeShouldBeAllowed(): void
    {
        $user = new User(['role' => UserRole::Admin]);
        CarbonImmutable::setTestNow('2021-01-01');
        $startDate = CarbonImmutable::parse('2021-02-01');
        $endDate = CarbonImmutable::parse('2021-02-01');
        
        $this->service->checkMonthAhead($startDate, $endDate, $user);
        
        $this->assertTrue(true);
    }

    public function adminShouldBeAbleToReserveDailyWithoutLimit(): void
    {
        // given
        $admin = new User(['role' => UserRole::Admin]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 18:00');
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $admin, "some plane id");
    }

    /** @test */
    public function userShouldBeAbleToReserveDailyUpTo120MinutesWithOneGo(): void
    {
        // given
        Plane::factory()->create(['id' => Ulid::fromString('01F9ZJZJZJZJZJZJZJZJZJZJZJ')]);

        $user = new User(['role' => UserRole::User]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-01-01 12:00');
        
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $user, '01F9ZJZJZJZJZJZJZJZJZJZJZJ');
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldBeAbleToReserveDailyUpTo120MinutesInTotal(): void
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
            'starts_at_date' => '2021-01-01',
            'starts_at_time' => '10:00',
            'ends_at_date' => '2021-01-01',
            'ends_at_time' => '11:00',
            'time' => 60,
        ]);

        $startDate = CarbonImmutable::parse('2021-01-01 13:00');
        $endDate = CarbonImmutable::parse('2021-01-01 14:00');
        
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $user, '01HE1FBZEPC8SRGM7VQDQV4K9X');
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
        $this->service->checkDailyTimeLimit($startDate, $endDate, $user, '01HE1FN3P71S3V242YXJ9XMQVT');
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
            'starts_at_date' => '2021-01-01',
            'starts_at_time' => '10:00',
            'ends_at_date' => '2021-01-01',
            'ends_at_time' => '11:00',
            'time' => 60,
        ]);

        $startDate = CarbonImmutable::parse('2021-01-01 13:00');
        $endDate = CarbonImmutable::parse('2021-01-01 14:01');
        
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $user, '01HE1FNZZX6XPBTDFTN8A66Y69');
    }
}
