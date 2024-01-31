<?php

declare(strict_types=1);

namespace App\Services\PlaneReservation;

use App\Models\PlaneReservation;
use Carbon\CarbonImmutable;

interface PlaneReservationRepository
{
    /**
     * @return iterable<PlaneReservation>
     */
    public function getAllReservationsForDate(CarbonImmutable $date): iterable;
}