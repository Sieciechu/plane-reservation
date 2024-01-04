<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\PlaneReservation;
use App\Models\User;
use Carbon\CarbonImmutable;

class DailyTimeLimitCheck implements Checker
{
    public function __construct(
        private int $dailyTimeLimitInMinutes
    ) {
    }
    
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $reservationTime = $startsAt->diffInMinutes($endsAt);

        $usedTime = PlaneReservation::where('user_id', $user->id)
            ->where('plane_id', $planeId)
            ->whereDate('starts_at', $startsAt->format('Y-m-d'))
            ->sum('time');

        $dailyTime = $usedTime + $reservationTime;
        if ($dailyTime > $this->dailyTimeLimitInMinutes) {
            $hours = $this->dailyTimeLimitInMinutes / 60;
            throw new Exception("you can reserve plane for max $hours hours daily");
        }
    }
}
