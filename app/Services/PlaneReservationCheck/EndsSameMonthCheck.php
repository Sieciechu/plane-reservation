<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

class EndsSameMonthCheck implements Checker
{
    /** @throws Exception */
    public function check(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        if ($startsAt->month !== $endsAt->month) {
            throw new Exception('reservation must end in the same month');
        }
    }
}
