<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Services\PlaneReservation\PlaneReservationRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * @return iterable<PlaneReservation>
     */
    public function getReservationsForPlaneAndDate(Plane $plane, CarbonImmutable $date): iterable
    {
        return PlaneReservation::query()
            ->where('plane_id', $plane->id)
            ->whereYear('starts_at', $date->format('Y'))
            ->whereMonth('starts_at', $date->format('m'))
            ->whereDay('starts_at', $date->format('d'))
            ->orderBy('starts_at')
            ->get();
    }

    public function getUserAllUpcomingReservationsStartingFromDate(User $user, CarbonImmutable $startsAt): iterable
    {
        return PlaneReservation::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->where('starts_at', '>=', $startsAt)
            ->orderBy('starts_at')
            ->get();
    }
}
