<?php

namespace App\Policies;

use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PlaneReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     */
    public function remove(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin || $user->id === $planeReservation->user_id;
    }

    public function confirm(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin;
    }
}
