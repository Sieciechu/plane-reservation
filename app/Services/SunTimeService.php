<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

class SunTimeService
{
    private float $longitude;
    private float $latitude;
    private string $timezone;

    public function __construct(float $longitude, float $latitude, string $timezone)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->timezone = $timezone;
    }

    public function getSunriseTime(CarbonImmutable $date): CarbonImmutable
    {
        $sunriseTime = (int) date_sun_info(
            (int) $date->setHours(12)->setTimezone('UTC')->timestamp,
            $this->latitude,
            $this->longitude
        )['sunrise'];
        return CarbonImmutable::createFromTimestamp($sunriseTime, 'UTC')->setTimezone($this->timezone)->seconds(0);
    }

    public function getSunsetTime(CarbonImmutable $date): CarbonImmutable
    {
        $sunsetTime = (int) date_sun_info(
            (int) $date->setHours(12)->setTimezone('UTC')->timestamp,
            $this->latitude,
            $this->longitude
        )['sunset'];
        return CarbonImmutable::createFromTimestamp($sunsetTime, 'UTC')->setTimezone($this->timezone)->seconds(0);
    }
}
