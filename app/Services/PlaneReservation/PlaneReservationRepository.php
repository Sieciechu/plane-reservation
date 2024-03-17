<?php

declare(strict_types=1);

namespace App\Services\PlaneReservation;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use Carbon\CarbonImmutable;

interface PlaneReservationRepository
{
    /**
     * @return iterable<PlaneReservation>
     */
    public function getAllReservationsForDate(CarbonImmutable $date): iterable;

    /**
     * @return iterable<PlaneReservation>
     */
    public function getReservationsForPlaneAndDate(Plane $plane, CarbonImmutable $date): iterable;

    public function getUserAllUpcomingReservationsStartingFromDate(User $user, CarbonImmutable $startsAt): iterable;
}
