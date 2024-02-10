<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

class SecondPilotByAdminOnlyCheck implements Checker
{
    public function __construct(
    ) {
    }

    /** @throws Exception */
    public function check(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        User $user,
        string $planeId,
        ?User $user2 = null,
    ): void {
        if (!$user->isAdmin() && null !== $user2) {
            throw new Exception('only admin can add second pilot');
        }
    }
}
