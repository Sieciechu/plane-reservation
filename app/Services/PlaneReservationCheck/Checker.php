<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

interface Checker
{
    /**
     * @throws Exception
     */
    public function check(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        User $user,
        string $planeId,
        ?User $user2 = null,
    ): void;
}
