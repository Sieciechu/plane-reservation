<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

class MustEndAfterStartCheck implements Checker
{
    public function __construct()
    {
    }

    /** @throws Exception */
    public function check(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        User $user,
        string $planeId,
        ?User $user2 = null,
    ): void {
        // if ($endsAt->lte($startsAt)) {
        //     throw new Exception('End date must be after start date');
        // }
    }
}
