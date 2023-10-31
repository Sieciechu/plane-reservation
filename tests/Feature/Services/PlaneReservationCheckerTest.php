<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationChecker;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class PlaneReservationCheckerTest extends TestCase
{
    use RefreshDatabase;

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
            'starts_at_date' => '2021-01-01',
            'starts_at_time' => '10:00',
            'ends_at_date' => '2021-01-01',
            'ends_at_time' => '11:00',
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
            'starts_at_date' => '2021-01-01',
            'starts_at_time' => '10:00',
            'ends_at_date' => '2021-01-01',
            'ends_at_time' => '11:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1G3R4YDG9H3WRGQPQ8FKV9'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at_date' => '2021-01-16',
            'starts_at_time' => '15:00',
            'ends_at_date' => '2021-01-16',
            'ends_at_time' => '16:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GCNTM44CKBFPMRX33WZ1D'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at_date' => '2021-01-21',
            'starts_at_time' => '12:00',
            'ends_at_date' => '2021-01-21',
            'ends_at_time' => '15:00',
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
            'starts_at_date' => '2021-01-01',
            'starts_at_time' => '10:00',
            'ends_at_date' => '2021-01-01',
            'ends_at_time' => '11:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at_date' => '2021-01-16',
            'starts_at_time' => '15:00',
            'ends_at_date' => '2021-01-16',
            'ends_at_time' => '16:00',
            'time' => 60,
        ]);
        // this is different plane, monthly it should also count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at_date' => '2021-01-21',
            'starts_at_time' => '12:00',
            'ends_at_date' => '2021-01-21',
            'ends_at_time' => '15:00',
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
}
