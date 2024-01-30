<?php

declare(strict_types=1);

namespace App\Services\PlaneReservation;

use App\Models\User;
use App\Services\PlaneRepository;
use Carbon\CarbonImmutable;

class PlaneReservationService
{
    public function __construct(
        private PlaneRepository $planeRepo,
        private PlaneReservationRepository $planeReservationRepo,
    ) {
    }

    /**
     * @return array<string, array>
     */
    public function getAllReservationsWithActionsForDate(CarbonImmutable $date, User $actingUser): array
    {
        $planes = $this->planeRepo->getAll();
        $reservationsWithActions = [];
        $planeIdToRegistrationMap = [];
        foreach ($planes as $plane) {
            $reservationsWithActions[$plane->registration] = [];
            $planeIdToRegistrationMap[$plane->id] = $plane->registration;
        }
        $reservations = $this->planeReservationRepo->getAllReservationsForDate($date);

        /** @var \App\Models\PlaneReservation $r */
        foreach ($reservations as $r) {
            $registration = $planeIdToRegistrationMap[$r->plane_id];
            $reservationsWithActions[$registration][] = [
                'id' => $r->id,
                'starts_at' => $r->starts_at->format('H:i'),
                'ends_at' => $r->ends_at->format('H:i'),
                'is_confirmed' => $r->confirmed_at !== null,
                'can_confirm' => $r->confirmed_at === null && $actingUser->isAdmin(),
                'can_remove' => $r->user_id === $actingUser->id || $actingUser->isAdmin(),
                'user_name' => $r->user->name,
                'comment' => $r->comment ?? '',
            ];
        }
        return $reservationsWithActions;
    }
}
