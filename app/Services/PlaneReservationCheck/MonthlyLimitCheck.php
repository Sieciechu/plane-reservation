<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\PlaneReservation;
use App\Models\User;
use Carbon\CarbonImmutable;

class MonthlyLimitCheck implements Checker
{
    public function __construct(
        private int $monthlyTimeLimitInMinutes,
    ) {
    }

    /** @throws Exception */
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $reservationTime = $startsAt->diffInMinutes($endsAt);

        $monthlyUsedTime = PlaneReservation::where('user_id', $user->id)
            ->whereYear('starts_at', $startsAt->format('Y'))
            ->whereMonth('starts_at', $startsAt->format('m'))
            ->sum('time') ?? 0;
        
        
        $monthlyTime = $monthlyUsedTime + $reservationTime;

        if ($monthlyTime > $this->monthlyTimeLimitInMinutes) {
            $hours = $this->monthlyTimeLimitInMinutes / 60;
            throw new Exception("you can reserve planes for max $hours hours monthly");
        }
    }
}
