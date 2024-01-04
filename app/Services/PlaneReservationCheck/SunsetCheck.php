<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;

class SunsetCheck implements Checker
{
    public function __construct(
        private SunTimeService $sunTimeService
    ) {
    }

    /** @throws Exception */
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        $sunsetTime = $this->sunTimeService->getSunsetTime($endsAt);
        if ($endsAt->startOfMinute()->greaterThan($sunsetTime)) {
            throw new Exception('reservation cannot end after sunset: ' . $sunsetTime->format('Y-m-d H:i'));
        }
    }
}
