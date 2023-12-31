<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationChecker;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class PlaneReservationCheckerTest extends TestCase
{
    use RefreshDatabase;

    private PlaneReservationChecker $service;

    /** @var SunTimeService|\PHPUnit\Framework\MockObject\MockObject */
    private $sunTimeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->sunTimeService = $this->createMock(SunTimeService::class);
        
        $this->service = new PlaneReservationChecker(
            240,
            120,
            30,
            $this->sunTimeService,
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
    public function userShouldBeAbleToReserveDailyUpToDailyLimitWithOneGo(): void
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
        $this->service->checkDailyTimeLimit(
            CarbonImmutable::parse('2021-01-01 13:00'),
            CarbonImmutable::parse('2021-01-01 14:00'),
            $user,
            '01HE1FBZEPC8SRGM7VQDQV4K9X'
        );
        $this->service->checkDailyTimeLimit(
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
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);

        $startDate = CarbonImmutable::parse('2021-01-01 13:00');
        $endDate = CarbonImmutable::parse('2021-01-01 14:01');
        
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $user, '01HE1FNZZX6XPBTDFTN8A66Y69');
    }

    public function adminShouldBeAbleToReserveWithoutMonthlyLimit(): void
    {
        // given
        $admin = new User(['role' => UserRole::Admin]);
        $startDate = CarbonImmutable::parse('2021-01-01 10:00');
        $endDate = CarbonImmutable::parse('2021-03-01 18:00');
        // when
        $this->service->checkDailyTimeLimit($startDate, $endDate, $admin, "some plane id");
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldBeAbleToReserveUpToMonthlyLimit(): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1G3R4YDG9H3WRGQPQ8FKV9')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GCNTM44CKBFPMRX33WZ1D')]);

        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1G3R4YDG9H3WRGQPQ8FKV9'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1G3R4YDG9H3WRGQPQ8FKV9'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GCNTM44CKBFPMRX33WZ1D'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-21 12:00:00',
            'ends_at' => '2021-01-21 15:00:00',
            'time' => 60,
        ]);

        
        // when
        $this->service->checkUserMonthlyTimeLimit(
            CarbonImmutable::parse('2021-01-10 13:00'),
            CarbonImmutable::parse('2021-01-10 14:00'),
            $user,
        );
        $this->assertTrue(true);
    }

    /** @test */
    public function userShouldNotBeAbleToReserveMoreThanMonthlyLimit(): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-21 12:00:00',
            'ends_at' => '2021-01-21 15:00:00',
            'time' => 60,
        ]);

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('you can reserve planes for max 4 hours monthly');
        
        // when
        $this->service->checkUserMonthlyTimeLimit(
            CarbonImmutable::parse('2021-01-10 13:00'),
            CarbonImmutable::parse('2021-01-10 14:01'),
            $user,
        );
    }

    /**
     * @test
     * @dataProvider overlapingReservationsProvider
     */
    public function whenOverlapsConfirmedReservationItShouldBeImpossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);
        // this is different plane, it should not count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-10 13:00:00',
            'ends_at' => '2021-01-10 13:30:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation overlaps with another confirmed reservation');
        
        // when
        $this->service->checkOverlapsConfirmedReservation(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            '01HE1GB7NJSMF037F76BVR1D1M',
        );
    }

    public static function overlapingReservationsProvider(): iterable
    {
        yield ['2021-01-01 09:00', '2021-01-01 10:01'];
        yield ['2021-01-01 10:59', '2021-01-01 12:00'];
        yield ['2021-01-01 10:00', '2021-01-01 14:01'];
        yield ['2021-01-01 09:00', '2021-01-01 10:01'];
        yield ['2021-01-15 12:30', '2021-01-16 17:00'];
        yield ['2021-01-16 12:30', '2021-01-16 15:30'];
    }

    /**
     * @test
     * @dataProvider nonOverlapingReservationsProvider
     */
    public function whenReservationDoesNotOverlapConfirmedItShouldBePossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, it should not count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-10 13:00:00',
            'ends_at' => '2021-01-10 14:00:00',
            'time' => 60,
        ]);
        
        // when
        $this->service->checkOverlapsConfirmedReservation(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            '01HE1GB7NJSMF037F76BVR1D1M',
        );
        $this->assertTrue(true);
    }

    public static function nonOverlapingReservationsProvider(): iterable
    {
        yield 'does not overlap any #1' => ['2021-01-01 09:00', '2021-01-01 10:00'];
        yield 'does not overlap any #2' => ['2021-01-01 11:00', '2021-01-01 12:00'];
        yield 'does not overlap any #3' => ['2021-01-10 13:00', '2021-01-10 14:00'];
        yield 'does not overlap any #4' => ['2021-01-10 09:00', '2021-01-10 09:59'];
        yield 'does not overlap any #5' => ['2021-01-10 11:40', '2021-01-10 14:59'];
        yield 'when overlaps unconfirmed it should be possible to reserver' => ['2021-01-10 10:20', '2021-01-10 14:59'];
    }

    public function testCannotMakeReservationBeforeSunrise(): void
    {
        // given
        $reservationStart = CarbonImmutable::parse('2023-01-01 05:00:00', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunriseTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw'));

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation cannot start before sunrise');

        // when
        $this->service->checkReservationForSunrise($reservationStart);
    }

    public function testItShouldBePossibleToMakeReservationAfterSunrise(): void
    {
        // given
        $reservationStart = CarbonImmutable::parse('2023-01-01 05:00:00', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunriseTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 04:33:00', 'Europe/Warsaw'));

        // when
        $this->service->checkReservationForSunrise($reservationStart);
        $this->assertTrue(true);
    }

    public function testCannotMakeReservationAfterSunset(): void
    {
        // given
        $reservationEnd = CarbonImmutable::parse('2023-01-01 15:51:01', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunsetTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw'));

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation cannot end after sunset');

        // when
        $this->service->checkReservationForSunset($reservationEnd);
    }

    public function testItShouldBePossibleToMakeReservationBeforeSunset(): void
    {
        // given
        $reservationEnd = CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunsetTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 15:51:01', 'Europe/Warsaw'));

        // when
        $this->service->checkReservationForSunset($reservationEnd);
        $this->assertTrue(true);
    }
}
