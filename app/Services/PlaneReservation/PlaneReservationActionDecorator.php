<?php

declare(strict_types=1);

namespace App\Services\PlaneReservation;

use App\Models\PlaneReservation;
use App\Models\User;

class PlaneReservationActionDecorator
{
    private PlaneReservation $planeReservation;
    private User $actingUser;

    private array $result = [];

    public function __construct(
    ) {
    }

    public function setup(PlaneReservation $planeReservation, User $actingUser): self
    {
        $this->planeReservation = $planeReservation;
        $this->actingUser = $actingUser;
        return $this;
    }

    public function decorWithActions(): self
    {
        $this->result = [
            'id' => $this->planeReservation->id,
            'starts_at' => $this->planeReservation->starts_at->format('H:i'),
            'ends_at' => $this->planeReservation->ends_at->format('H:i'),
            'is_confirmed' => $this->planeReservation->confirmed_at !== null,
            'can_confirm' => $this->planeReservation->confirmed_at === null && $this->actingUser->isAdmin(),
            'can_remove' => $this->planeReservation->user_id === $this->actingUser->id || $this->actingUser->isAdmin(),
            'user_name' => $this->planeReservation->user->name,
            'user2_name' => $this->planeReservation->user2?->name ?? '',
            'comment' => $this->planeReservation->comment ?? '',
        ];

        return $this;
    }

    public function decorWithPlaneRegistration(): self
    {
        $this->result['plane_registration'] = $this->planeReservation->plane->registration;
        return $this;
    }

    public function decorWithDates(): self
    {
        $this->result['starts_at_date'] = $this->planeReservation->starts_at->format('Y-m-d');
        $this->result['ends_at_date'] = $this->planeReservation->ends_at->format('Y-m-d');
        return $this;
    }

    public function get(): array
    {
        return $this->result;
    }
}
