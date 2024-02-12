<?php

declare(strict_types=1);

namespace App\Services\PlaneReservation;

use App\Models\Plane;
use App\Models\User;
use App\Services\PlaneRepository;
use Carbon\CarbonImmutable;

class PlaneReservationService
{
    public function __construct(
        private PlaneRepository $planeRepo,
        private PlaneReservationRepository $planeReservationRepo,
        private PlaneReservationActionDecorator $actionDecor,
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
            $this->actionDecor->setup($r, $actingUser);
            $reservationsWithActions[$registration][] = $this->actionDecor->decorWithActions()->get();
        }
        return $reservationsWithActions;
    }

    /**
     * @return array<int, array>
     */
    public function getReservationsWithActionsForPlaneAndDate(Plane $plane, CarbonImmutable $date, User $actingUser): array
    {
        $reservations = $this->planeReservationRepo->getReservationsForPlaneAndDate($plane, $date);
        $reservationsWithActions = [];
        foreach ($reservations as $r) {
            $this->actionDecor->setup($r, $actingUser);
            $reservationsWithActions[] = $this->actionDecor->decorWithActions()->get();
        }
        return $reservationsWithActions;
    }

    /**
     * @return array<int, array>
     */
    public function getUserAllUpcomingReservationsStartingFromDate(User $user, CarbonImmutable $startsAt, User $actingUser): iterable
    {
        $reservations = $this->planeReservationRepo->getUserAllUpcomingReservationsStartingFromDate($user, $startsAt);
        $reservationsWithActions = [];
        foreach ($reservations as $r) {
            $this->actionDecor->setup($r, $actingUser);
            $reservationsWithActions[] = $this->actionDecor->decorWithActions()
                ->decorWithDates()
                ->decorWithPlaneRegistration()
                ->get();
        }
        return $reservationsWithActions;
    }
}
