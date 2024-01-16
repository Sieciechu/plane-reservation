<?php

namespace App\Providers;

use App;
use App\Http\Controllers\Api\PlaneReservationController;
use App\Http\Controllers\Api\SunTimeController;
use App\Infrastructure\SmsSender\DummySmsClient;
use App\Infrastructure\SmsSender\SmsPlanetClient;
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
use App\Services\SmsSender\SmsSender;
use App\Services\SmsSender\SmsService;
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

            /** @var SunTimeService $sunTimeService */
            $sunTimeService = $this->app->get('epomSunTimeService');

            return new PlaneReservationChecker(
                new MultipleCheck(
                    new SunriseCheck($sunTimeService),
                    new SunsetCheck($sunTimeService),
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

            /** @var SmsService $epomSmsService */
            $epomSmsService = $this->app->get('epomSmsService');

            return new PlaneReservationController(
                reservationChecker: $checker,
                smsService: $epomSmsService,
            );
        });
        $this->app->bind(SunTimeController::class, function () {
            /** @var SunTimeService $sunTimeService */
            $sunTimeService = $this->app->get('epomSunTimeService');
            return new SunTimeController(
                sunTimeService: $sunTimeService,
            );
        });

        $this->app->bind('epomSmsService', function () {
            /** @var string $from */
            $from = config('planereservation.airport.EPOM.sms.from');
            /** @var SmsSender $smsSender */
            $smsSender = $this->app->get(SmsSender::class);
            return new SmsService(smsSender: $smsSender, smsSenderName: $from);
        });

        $this->app->bind(SmsPlanetClient::class, function () {
            /** @var string $smsPlanetApiToken */
            $smsPlanetApiToken = config('planereservation.smsplanet.apitoken');
            /** @var string $smsPlanetApiPassword */
            $smsPlanetApiPassword = config('planereservation.smsplanet.apipassword');
            $smsPlanetClient = new \SMSPLANET\PHP\Client([
                'key' => $smsPlanetApiToken,
                'password' => $smsPlanetApiPassword,
            ]);

            return new SmsPlanetClient($smsPlanetClient);
        });

        $this->app->bind(SmsSender::class, function () {
            if (App::isProduction()) {
                return $this->app->get(SmsPlanetClient::class);
            }
            return $this->app->get(DummySmsClient::class);
        });

        $this->app->singleton(DummySmsClient::class, function () {
            return new DummySmsClient();
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
