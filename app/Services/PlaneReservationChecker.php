<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\ServiceProvider;

class PlaneReservationChecker extends ServiceProvider
{
    public function __construct(
        private int $monthlyTimeLimitInMinutes,
        private int $dailyTimeLimitInMinutes,
        private int $maxReservationDaysAhead,
    ) {
    }
    /**
     * @param $params array {
     *     plane_registration: string,
     *     user_id: string,
     *     starts_at: string,
     *     ends_at: string,
     *     time: int,
     * }
     * @throws Exception
     */
    public function checkAll(array $params): void
    {
        /** @var User $user */
        $user = User::findOrFail($params['user_id']);
        /** @var Plane $plane */
        $plane = Plane::findOrFail($params['plane_id']);
        
        $startDate = CarbonImmutable::parse($params['starts_at']);
        $endDate = CarbonImmutable::parse($params['ends_at']);
        
        $this->checkReservationEndsSameMonth($startDate, $endDate);
        $this->checkMonthAhead($startDate, $endDate, $user);
        $this->checkDailyTimeLimit($startDate, $endDate, $user, $plane->id);
        $this->checkReservationOverlaps($startDate, $endDate, $plane->id);
    }

    /** @throws Exception */
    public function checkReservationEndsSameMonth(CarbonImmutable $startsAt, CarbonImmutable $endsAt): void
    {
        if ($startsAt->month !== $endsAt->month) {
            throw new Exception('reservation must end in the same month');
        }
    }

    /** @throws Exception */
    public function checkMonthAhead(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $now = CarbonImmutable::now()->startOfDay();
        $diffInDaysNowAndReservationStart = $now->diffInDays($startsAt->startOfDay());
        $diffInDaysNowAndReservationEnd = $now->diffInDays($endsAt->startOfDay());

        if ($diffInDaysNowAndReservationStart > $this->maxReservationDaysAhead
            || $diffInDaysNowAndReservationEnd > $this->maxReservationDaysAhead
        ) {
            throw new Exception("you can reserve plane for max {$this->maxReservationDaysAhead} days ahead");
        }
    }

    /** @throws Exception */
    public function checkDailyTimeLimit(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user, string $planeId): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $reservationTime = $startsAt->diffInMinutes($endsAt);

        $usedTime = PlaneReservation::where('user_id', $user->id)
            ->where('plane_id', $planeId)
            ->whereDate('starts_at', $startsAt->format('Y-m-d'))
            ->sum('time');

        $dailyTime = $usedTime + $reservationTime;
        if ($dailyTime > $this->dailyTimeLimitInMinutes) {
            $hours = $this->dailyTimeLimitInMinutes / 60;
            throw new Exception("you can reserve plane for max $hours hours daily");
        }
    }

    /** @throws Exception */
    public function checkUserMonthlyTimeLimit(CarbonImmutable $startsAt, CarbonImmutable $endsAt, User $user): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $reservationTime = $startsAt->diffInMinutes($endsAt);

        $monthlyUsedTime = PlaneReservation::where('user_id', $user->id)
            ->whereYear('starts_at', $startsAt->format('Y'))
            ->whereMonth('starts_at', $startsAt->format('m'))
            ->sum('time') ?? 0;
        
        
        $monthlyTime = $monthlyUsedTime + $reservationTime;

        if ($monthlyTime > $this->monthlyTimeLimitInMinutes) {
            $hours = $this->monthlyTimeLimitInMinutes / 60;
            throw new Exception("you can reserve planes for max $hours hours monthly");
        }
    }

    /** @throws Exception */
    public function checkReservationOverlaps(CarbonImmutable $startsAt, CarbonImmutable $endsAt, string $planeId): void
    {
        $overlappingReservationsCount = PlaneReservation::where('plane_id', $planeId)
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->whereBetween('starts_at', [$startsAt->subSecond(), $endsAt->subSeconds()])
                    ->orWhereBetween('ends_at', [$startsAt->addSecond(), $endsAt->addSecond()]);
            })
            ->count();

        if ($overlappingReservationsCount > 0) {
            throw new Exception('reservation overlaps with another reservation');
        }
    }
}
