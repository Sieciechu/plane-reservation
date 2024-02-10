<?php

declare(strict_types=1);

namespace App\Services\PlaneReservationCheck;

use App\Models\PlaneReservation;
use App\Models\User;
use Carbon\CarbonImmutable;

class OverlapsConfirmedReservationCheck implements Checker
{
    public function check(
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        User $user,
        string $planeId,
        ?User $user2 = null,
    ): void {
        if ($user->isAdmin()) {
            return;
        }

        $overlappingReservationsCount = PlaneReservation::where('plane_id', $planeId)
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->whereBetween('starts_at', [$startsAt->subSecond(), $endsAt->subSeconds()])
                    ->orWhereBetween('ends_at', [$startsAt->addSecond(), $endsAt->addSecond()]);
            })
            ->where('confirmed_at', '!=', null)
            ->count();

        if ($overlappingReservationsCount > 0) {
            throw new Exception('reservation overlaps with another confirmed reservation');
        }
    }
}
