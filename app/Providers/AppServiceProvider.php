<?php

namespace App\Providers;

use App\Http\Controllers\PlaneReservationController;
use App\Services\PlaneReservationChecker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlaneReservationChecker::class, function() {
            return new PlaneReservationChecker(
                monthlyTimeLimitInMinutes: config('planereservation.monthlyTimeLimitInMinutes'),
                dailyTimeLimitInMinutes: config('planereservation.dailyTimeLimitInMinutes'),
                maxReservationDaysAhead: config('planereservation.maxReservationDaysAhead'),
            );
        });
        $this->app->bind(PlaneReservationController::class, function() {
            return new PlaneReservationController(
                reservationChecker: $this->app->get(PlaneReservationChecker::class),
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
