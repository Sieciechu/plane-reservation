<?php

namespace App\Policies;

use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Access\Response;

class PlaneReservationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlaneReservation $planeReservation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin || $user->id === $planeReservation->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin || $user->id === $planeReservation->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlaneReservation $planeReservation): bool
    {
        return $user->role === UserRole::Admin;
    }
}
