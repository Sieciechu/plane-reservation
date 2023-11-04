<?php

namespace App\Policies;

use App\Models\PlaneReservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaneReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     */
    public function remove(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->isAdmin() || $user->id === $planeReservation->user_id;
    }

    public function confirm(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->isAdmin();
    }
}
