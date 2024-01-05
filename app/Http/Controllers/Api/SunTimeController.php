<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SunTimeService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class SunTimeController extends Controller
{
    public function __construct(
        private SunTimeService $sunTimeService,
    ) {
    }

    public function getSunriseAndSunsetTimes(string $date): JsonResponse
    {
        $startsAt = CarbonImmutable::parse($date);

        $sunrise = $this->sunTimeService->getSunriseTime($startsAt);
        $sunset = $this->sunTimeService->getSunsetTime($startsAt);

        return response()->json([
            'sunrise' => $sunrise->format('H:i'),
            'sunset' => $sunset->format('H:i'),
        ]);
    }
}
