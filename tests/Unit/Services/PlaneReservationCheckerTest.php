<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationChecker;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

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

}
