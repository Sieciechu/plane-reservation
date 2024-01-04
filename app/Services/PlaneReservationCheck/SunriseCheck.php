<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;

class SunriseCheck implements Checker
{
    public function __construct(
        private SunTimeService $sunTimeService
    ) {
    }

    /** @throws Exception */
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        $sunriseTime = $this->sunTimeService->getSunriseTime($startsAt);
        if ($startsAt->startOfMinute()->lessThan($sunriseTime)) {
            throw new Exception('reservation cannot start before sunrise: ' . $sunriseTime->format('Y-m-d H:i'));
        }
    }
}
