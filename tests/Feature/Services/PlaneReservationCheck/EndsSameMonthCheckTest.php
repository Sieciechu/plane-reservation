<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\User;
use App\Services\PlaneReservationCheck\EndsSameMonthCheck;
use App\Services\PlaneReservationCheck\Exception;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class EndsSameMonthCheckTest extends TestCase
{
    private EndsSameMonthCheck $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new EndsSameMonthCheck();
    }

    /**
     * @test
     * @dataProvider reservationEndsSameMonthWrongDataProvider
     */
    public function whenReservationSplitsAcrossTheMonthThenCheckShouldNotPass(string $startDateString, string $endDateString): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation must end in the same month');
        
        $startDate = CarbonImmutable::parse($startDateString);
        $endDate = CarbonImmutable::parse($endDateString);
        
        $this->service->check($startDate, $endDate, $this->createMock(User::class), 'some plane id');
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
        
        $this->service->check($startDate, $endDate, $this->createMock(User::class), 'some plane id');
        
        $this->assertTrue(true);
    }
    
    public static function reservationEndsSameMonthProvider(): iterable
    {
        yield ['2021-01-01', '2021-01-31'];
        yield ['2021-01-31', '2021-01-31'];
        yield ['2021-01-16', '2021-01-18'];
    }
}
