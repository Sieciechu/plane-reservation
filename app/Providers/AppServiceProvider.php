<?php

namespace App\Providers;

use App\Http\Controllers\Api\PlaneReservationController;
use App\Http\Controllers\Api\SunTimeController;
use App\Services\PlaneReservationCheck\DailyTimeLimitCheck;
use App\Services\PlaneReservationCheck\EndsSameMonthCheck;
use App\Services\PlaneReservationCheck\MonthAheadCheck;
use App\Services\PlaneReservationCheck\MonthlyLimitCheck;
use App\Services\PlaneReservationCheck\MultipleCheck;
use App\Services\PlaneReservationCheck\OverlapsConfirmedReservationCheck;
use App\Services\PlaneReservationCheck\OverlapsSameUserReservationCheck;
use App\Services\PlaneReservationCheck\SunriseCheck;
use App\Services\PlaneReservationCheck\SunsetCheck;
use App\Services\PlaneReservationChecker;
use App\Services\SunTimeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('epomSunTimeService', function () {
            /** @var float $latitude */
            $latitude = config('planereservation.airport.EPOM.latitude');
            /** @var float $logitude */
            $logitude = config('planereservation.airport.EPOM.longitude');
            /** @var string $timezone */
            $timezone = config('planereservation.airport.EPOM.timezone');
            return new SunTimeService($logitude, $latitude, $timezone);
        });

        $this->app->bind(PlaneReservationChecker::class, function () {
            /** @var int $monthlyLimit */
            $monthlyLimit = config('planereservation.monthlyTimeLimitInMinutes');
            /** @var int $dailyLimit */
            $dailyLimit = config('planereservation.dailyTimeLimitInMinutes');
            /** @var int $daysAheadLimit */
            $daysAheadLimit = config('planereservation.maxReservationDaysAhead');

            return new PlaneReservationChecker(
                new MultipleCheck(
                    new SunriseCheck($this->app->get('epomSunTimeService')),
                    new SunsetCheck($this->app->get('epomSunTimeService')),
                    new DailyTimeLimitCheck($dailyLimit),
                    new EndsSameMonthCheck(),
                    new MonthAheadCheck($daysAheadLimit),
                    new MonthlyLimitCheck($monthlyLimit),
                    new OverlapsConfirmedReservationCheck(),
                    new OverlapsSameUserReservationCheck(),
                ),
            );
        });
        $this->app->bind(PlaneReservationController::class, function () {
            /** @var PlaneReservationChecker $checker */
            $checker = $this->app->get(PlaneReservationChecker::class);
            return new PlaneReservationController(
                reservationChecker: $checker,
            );
        });
        $this->app->bind(SunTimeController::class, function () {
            /** @var SunTimeService $sunTimeService */
            $sunTimeService = $this->app->get('epomSunTimeService');
            return new SunTimeController(
                sunTimeService: $sunTimeService,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
