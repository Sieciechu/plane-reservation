<?php

namespace App\Providers;

use App\Http\Controllers\Api\PlaneReservationController;
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
        $this->app->bind(PlaneReservationChecker::class, function () {
            /** @var int $monthlyLimit */
            $monthlyLimit = config('planereservation.monthlyTimeLimitInMinutes');
            /** @var int $dailyLimit */
            $dailyLimit = config('planereservation.dailyTimeLimitInMinutes');
            /** @var int $daysAheadLimit */
            $daysAheadLimit = config('planereservation.maxReservationDaysAhead');

            /** @var float $latitude */
            $latitude = config('planereservation.airport.EPOM.latitude');
            /** @var float $logitude */
            $logitude = config('planereservation.airport.EPOM.longitude');
            

            $airportSunTimeService = new SunTimeService($logitude, $latitude, 'Europe/Warsaw');
            
            return new PlaneReservationChecker(
                $monthlyLimit,
                $dailyLimit,
                $daysAheadLimit,
                $airportSunTimeService,
            );
        });
        $this->app->bind(PlaneReservationController::class, function () {
            /** @var PlaneReservationChecker $checker */
            $checker = $this->app->get(PlaneReservationChecker::class);
            return new PlaneReservationController(
                reservationChecker: $checker,
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
