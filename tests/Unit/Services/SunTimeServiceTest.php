<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class SunTimeServiceTest extends TestCase
{
    private SunTimeService $service;

    public function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('UTC');

        $this->service = new SunTimeService(17.84688, 51.70154, 'Europe/Warsaw');
    }

    /**
     * @dataProvider expectedSunriseProvider
     */
    public function testGetSunriseTime(CarbonImmutable $date, CarbonImmutable $expected): void
    {
        $this->assertEquals($expected, $this->service->getSunriseTime($date));
    }

    public static function expectedSunriseProvider(): iterable
    {
        yield [CarbonImmutable::parse('2023-01-01 23:59:59', 'Europe/Warsaw'), CarbonImmutable::parse('2023-01-01 07:53:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-06-01 12:00:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-06-01 04:33:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-12-17 00:02:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-12-17 07:47:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-12-23 00:02:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-12-23 07:51:00', 'Europe/Warsaw')];
    }

    /**
     * @dataProvider expectedSunsetProvider
     */
    public function testGetSunsetTime(CarbonImmutable $date, CarbonImmutable $expected): void
    {
        $this->assertEquals($expected, $this->service->getSunsetTime($date));
    }

    public static function expectedSunsetProvider(): iterable
    {
        yield [CarbonImmutable::parse('2023-01-01 23:59:59', 'Europe/Warsaw'), CarbonImmutable::parse('2023-01-01 15:51:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-06-01 12:00:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-06-01 20:58:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-06-22 12:00:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-06-22 21:13:00', 'Europe/Warsaw')];
        yield [CarbonImmutable::parse('2023-12-17 00:00:00', 'Europe/Warsaw'), CarbonImmutable::parse('2023-12-17 15:41:00', 'Europe/Warsaw')];
    }
}
