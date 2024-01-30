<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Models\PlaneReservation;
use App\Services\PlaneReservation\PlaneReservationRepository;
use Carbon\CarbonImmutable;

class EloquentPlaneReservationRepository implements PlaneReservationRepository
{
    public function __construct()
    {
    }

    /**
     * @return iterable<PlaneReservation>
     */
    public function getAllReservationsForDate(CarbonImmutable $date): iterable
    {
        return PlaneReservation::query()
            ->whereYear('starts_at', $date->format('Y'))
            ->whereMonth('starts_at', $date->format('m'))
            ->whereDay('starts_at', $date->format('d'))
            ->orderBy('starts_at')
            ->get();
    }
}
