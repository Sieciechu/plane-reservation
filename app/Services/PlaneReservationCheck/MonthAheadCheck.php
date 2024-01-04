<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

class MonthAheadCheck implements Checker
{
    private int $maxReservationDaysAhead;

    public function __construct(int $maxReservationDaysAhead)
    {
        $this->maxReservationDaysAhead = $maxReservationDaysAhead;
    }

    /** @throws Exception */
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $now = CarbonImmutable::now()->startOfDay();
        $diffInDaysNowAndReservationStart = $now->diffInDays($startsAt->startOfDay());
        $diffInDaysNowAndReservationEnd = $now->diffInDays($endsAt->startOfDay());

        if ($diffInDaysNowAndReservationStart > $this->maxReservationDaysAhead
            || $diffInDaysNowAndReservationEnd > $this->maxReservationDaysAhead
        ) {
            throw new Exception("you can reserve plane for max {$this->maxReservationDaysAhead} days ahead");
        }
    }
}
