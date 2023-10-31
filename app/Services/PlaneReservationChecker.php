<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserReservationLimit;
use App\Models\UserRole;
use Carbon\CarbonImmutable;
use Illuminate\Support\ServiceProvider;

class PlaneReservationChecker extends ServiceProvider
{
    public function __construct(
        private int $monthlyTimeLimitInMinutes,
        private int $dailyTimeLimitInMinutes,
        private int $maxReservationDaysAhead,
    ){}
    /**
     * @param $params array {
     *      plane_registration: string,
     *      user_id: string,
     *      start_date: string,
     *      end_date: string,
     *      time: int,
     *  }
     */
    public function checkAll(array $params): void
    {
        /** @var User $user */
        $user = User::findOrFail($params['user_id']);
        /** @var Plane $plane */
        $plane = Plane::findOrFail($params['plane_id']);
        
        $startDate = CarbonImmutable::parse($params['start_date']);
        $endDate = CarbonImmutable::parse($params['end_date']);
        
        $this->checkReservationEndsSameMonth($startDate, $endDate);
        $this->checkMonthAhead($startDate, $endDate, $user);
        $this->checkDailyTimeLimit($startDate, $endDate, $user, $plane->id);
    }

    public function checkReservationEndsSameMonth(CarbonImmutable $startDate, CarbonImmutable $endDate): void
    {
        if ($startDate->month !== $endDate->month) {
            throw new \Exception('reservation must end in the same month');
        }
    }

    public function checkMonthAhead(CarbonImmutable $startDate, CarbonImmutable $endDate, User $user): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $now = CarbonImmutable::now()->startOfDay();
        $diffInDaysNowAndReservationStart = $now->diffInDays($startDate->startOfDay());
        $diffInDaysNowAndReservationEnd = $now->diffInDays($endDate->startOfDay());

        if ($diffInDaysNowAndReservationStart > $this->maxReservationDaysAhead 
            || $diffInDaysNowAndReservationEnd > $this->maxReservationDaysAhead
        ) {
            throw new \Exception("you can reserve plane for max {$this->maxReservationDaysAhead} days ahead");
        }
    }

    public function checkDailyTimeLimit(CarbonImmutable $startDate, CarbonImmutable $endDate, User $user, string $planeId): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $reservationTime = $startDate->diffInMinutes($endDate);

        $usedTime = PlaneReservation::where('user_id', $user->id)
            ->where('plane_id', $planeId)
            ->where('starts_at_date', $startDate->format('Y-m-d'))
            ->sum('time');

        $dailyTime = $usedTime + $reservationTime;
        if ($dailyTime > $this->dailyTimeLimitInMinutes) {
            $hours = $this->dailyTimeLimitInMinutes / 60;
            throw new \Exception("you can reserve plane for max $hours hours daily");
        }
    }

    public function checkUserMonthlyTimeLimit(CarbonImmutable $startDate, CarbonImmutable $endDate, User $user): void
    {
        if ($user->role === UserRole::Admin) {
            return;
        }

        $reservationTime = $startDate->diffInMinutes($endDate);

        $monthlyUsedTime = PlaneReservation::where('user_id', $user->id)
            ->whereYear('starts_at_date', $startDate->format('Y'))
            ->whereMonth('starts_at_date', $startDate->format('m'))
            ->sum('time') ?? 0;
        
        
        $monthlyTime = $monthlyUsedTime + $reservationTime;

        if ($monthlyTime > $this->monthlyTimeLimitInMinutes) {
            $hours = $this->monthlyTimeLimitInMinutes / 60;
            throw new \Exception("you can reserve planes for max $hours hours monthly");
        }
    }
}
