<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\User;
use Carbon\CarbonImmutable;

class MultipleCheck implements Checker
{
    /** @var Checker[] */
    private array $checkers;

    public function __construct(Checker ...$checkers)
    {
        $this->checkers = $checkers;
    }

    /** @throws Exception */
    public function check(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        User $user,
        string $planeId,
        ?User $user2 = null,
    ): void {
        foreach ($this->checkers as $checker) {
            $checker->check($startsAt, $endsAt, $user, $planeId);
        }
    }
}
