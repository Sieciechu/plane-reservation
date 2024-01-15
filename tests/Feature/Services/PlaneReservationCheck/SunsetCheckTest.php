<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\User;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\SunsetCheck;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class SunsetCheckTest extends TestCase
{
    private SunsetCheck $service;
    private SunTimeService|MockObject $sunTimeService;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->sunTimeService = $this->createMock(SunTimeService::class);
        $this->service = new SunsetCheck($this->sunTimeService);
    }
    public function testCannotMakeReservationAfterSunset(): void
    {
        // given
        $reservationEnd = CarbonImmutable::parse('2023-01-01 15:52:01', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunsetTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw'));

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation cannot end after sunset: 2023-01-01 15:51');

        // when
        $this->service->check(
            $this->createMock(CarbonImmutable::class),
            $reservationEnd,
            $this->createMock(User::class),
            'some plane id',
        );
    }

    public function testItShouldBePossibleToMakeReservationBeforeSunset(): void
    {
        // given
        $reservationEnd = CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunsetTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 15:51:01', 'Europe/Warsaw'));

        // when
        $this->service->check(
            $this->createMock(CarbonImmutable::class),
            $reservationEnd,
            $this->createMock(User::class),
            'some plane id',
        );
        $this->assertTrue(true);
    }
}
