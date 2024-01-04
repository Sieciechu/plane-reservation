<?php

declare(strict_types=1);

namespace Tests\Feature\PlaneReservationCheck;

use App\Models\User;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\SunriseCheck;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class SunriseCheckTest extends TestCase
{
    private SunriseCheck $service;
    private SunTimeService|MockObject $sunTimeService;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->sunTimeService = $this->createMock(SunTimeService::class);
        $this->service = new SunriseCheck($this->sunTimeService);
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
        $this->expectExceptionMessage('reservation cannot start before sunrise: 2023-01-01 15:51');

        // when
        $this->service->check(
            $reservationStart,
            $this->createMock(CarbonImmutable::class),
            $this->createMock(User::class),
            'some plane id'
        );
    }

    public function testItShouldBePossibleToMakeReservationAfterSunrise(): void
    {
        // given
        $reservationStart = CarbonImmutable::parse('2023-01-01 05:00:00', 'Europe/Warsaw');
        $this->sunTimeService->expects(self::once())
            ->method('getSunriseTime')
            ->willReturn(CarbonImmutable::parse('2023-01-01 04:33:00', 'Europe/Warsaw'));

        // when
        $this->service->check(
            $reservationStart,
            $this->createMock(CarbonImmutable::class),
            $this->createMock(User::class),
            'some plane id'
        );
        $this->assertTrue(true);
    }
}
