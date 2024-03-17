<?php

namespace App\Providers;

use App;
use App\Http\Controllers\Api\PlaneReservationController;
use App\Http\Controllers\Api\SunTimeController;
use App\Infrastructure\Repository\EloquentPlaneRepository;
use App\Infrastructure\Repository\EloquentPlaneReservationRepository;
use App\Infrastructure\SmsSender\DummySmsClient;
use App\Infrastructure\SmsSender\SmsPlanetClient;
use App\Services\PlaneRepository;
use App\Services\PlaneReservation\PlaneReservationActionDecorator;
use App\Services\PlaneReservation\PlaneReservationRepository;
use App\Services\PlaneReservation\PlaneReservationService;
use App\Services\PlaneReservationCheck\DailyTimeLimitCheck;
use App\Services\PlaneReservationCheck\EndsSameMonthCheck;
use App\Services\PlaneReservationCheck\MonthAheadCheck;
use App\Services\PlaneReservationCheck\MonthlyLimitCheck;
use App\Services\PlaneReservationCheck\MultipleCheck;
use App\Services\PlaneReservationCheck\OverlapsConfirmedReservationCheck;
use App\Services\PlaneReservationCheck\OverlapsSameUserReservationCheck;
use App\Services\PlaneReservationCheck\SecondPilotByAdminOnlyCheck;
use App\Services\PlaneReservationCheck\SunriseCheck;
use App\Services\PlaneReservationCheck\SunsetCheck;
use App\Services\PlaneReservationChecker;
use App\Services\SmsSender\SmsSender;
use App\Services\SmsSender\SmsService;
use App\Services\SunTimeService;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

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
                    new SecondPilotByAdminOnlyCheck(),
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

            /** @var PlaneReservationService $planeReservationService */
            $planeReservationService = $this->app->get(PlaneReservationService::class);

            /** @var PlaneRepository $planeRepo */
            $planeRepo = $this->app->get(PlaneRepository::class);

            /** @var LoggerInterface $logger */
            $logger = $this->app->get(LoggerInterface::class);

            return new PlaneReservationController(
                reservationChecker: $checker,
                smsService: $epomSmsService,
                planeReservationService: $planeReservationService,
                planeRepository: $planeRepo,
                logger: $logger,
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
            /** @var string $titleFrom */
            $titleFrom = config('planereservation.airport.EPOM.sms.titleFrom');
            /** @var string $footerFrom */
            $footerFrom = config('planereservation.airport.EPOM.sms.footerFrom');
            /** @var SmsSender $smsSender */
            $smsSender = $this->app->get(SmsSender::class);
            return new SmsService(smsSender: $smsSender, smsTitleSenderName: $titleFrom, smsFooterSenderName: $footerFrom);
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
            if (App::isProduction() ||
                'true' === getenv('SMS_SERVICE_SEND_REAL_SMS')
            ) {
                return $this->app->get(SmsPlanetClient::class);
            }
            return $this->app->get(DummySmsClient::class);
        });

        $this->app->singleton(DummySmsClient::class, function () {
            /** @var LoggerInterface $logger */
            $logger = $this->app->get(LoggerInterface::class);
            return new DummySmsClient($logger);
        });

        $this->app->bind(PlaneRepository::class, function () {
            return new EloquentPlaneRepository();
        });

        $this->app->bind(PlaneReservationRepository::class, function ($app) {
            return new EloquentPlaneReservationRepository();
        });

        $this->app->bind(PlaneReservationService::class, function ($app) {
            /** @var PlaneRepository $planeRepository */
            $planeRepository = $app->get(PlaneRepository::class);
            /** @var PlaneReservationRepository $planeReservationRepository */
            $planeReservationRepository = $app->get(PlaneReservationRepository::class);
            return new PlaneReservationService(
                planeRepo: $planeRepository,
                planeReservationRepo: $planeReservationRepository,
                actionDecor: new PlaneReservationActionDecorator(),
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
