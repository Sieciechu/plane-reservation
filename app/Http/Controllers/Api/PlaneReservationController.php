<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaneReservationConfirmRequest;
use App\Http\Requests\PlaneReservationGetAllForDate;
use App\Http\Requests\PlaneReservationListByDateRequest;
use App\Http\Requests\PlaneReservationMakeRequest;
use App\Http\Requests\PlaneReservationRemoveRequest;
use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Services\PlaneReservation\PlaneReservationService;
use App\Services\PlaneReservationChecker;
use App\Services\SmsSender\SmsService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;

class PlaneReservationController extends Controller
{
    public function __construct(
        private PlaneReservationChecker $reservationChecker,
        private SmsService $smsService,
        private PlaneReservationService $planeReservationService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function listByDate(PlaneReservationListByDateRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $plane = Plane::where('registration', $validated['plane_registration'])->firstOrFail();

        $startsAt = CarbonImmutable::parse($validated['starts_at_date'])->startOfDay(); // @phpstan-ignore-line

        $planeReservations = PlaneReservation::query()
            ->where('plane_id', $plane->id)
            ->whereYear('starts_at', $startsAt->format('Y'))
            ->whereMonth('starts_at', $startsAt->format('m'))
            ->whereDay('starts_at', $startsAt->format('d'))
            ->get()
            ->sortBy('starts_at')
            ->map(fn (PlaneReservation $r): array => [
                'id' => $r->id,
                'starts_at' => $r->starts_at->format('H:i'),
                'ends_at' => $r->ends_at->format('H:i'),
                'is_confirmed' => $r->confirmed_at !== null,
                'can_confirm' => $r->confirmed_at === null && $user->isAdmin(),
                'can_remove' => $r->user_id === $user->id || $user->isAdmin(),
                'user_name' => $r->user->name,
                'user2_name' => $r?->user2?->name ?? '',
                'comment' => $r->comment ?? '',
            ])->values();
            
        return response()->json(
            $planeReservations,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function make(PlaneReservationMakeRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $reservationDto */
        $reservationDto = $request->validated();
        
        /** @var User $user */
        $user = $request->user();
        
        if (!$user->isAdmin() || !isset($reservationDto['user_id'])) {
            $reservationDto['user_id'] = $user->id;
        }

        /** @var string $planeRegistration */
        $planeRegistration = $reservationDto['plane_registration'];
        $plane = Plane::where('registration', $planeRegistration)->firstOrFail();

        unset($reservationDto['plane_registration']);
        $reservationDto['plane_id'] = $plane->id;
        
        $startsAt = CarbonImmutable::parse($reservationDto['starts_at'])->seconds(0)->milliseconds(0); // @phpstan-ignore-line
        $endsAt = CarbonImmutable::parse($reservationDto['ends_at'])->seconds(0)->milliseconds(0); // @phpstan-ignore-line
        $endsAt = $startsAt->setTimeFromTimeString($endsAt->toTimeString());
        $reservationDto['time'] = $startsAt->diffInMinutes($endsAt);

        try {
            $this->reservationChecker->checkAll($reservationDto);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $planeReservation = PlaneReservation::create($reservationDto);

        // send mail to user
        // $user = User::where('id', $validated['user_id'])->firstOrFail();
        // $user->notify(new \App\Notifications\PlaneReservationCreated($planeReservation));

        return response()->json([], 201);
    }

    public function removeReservation(PlaneReservationRemoveRequest $request): JsonResponse
    {
        /** @var array<string, string> $validated */
        $validated = $request->validated();
        
        /** @var User $user */
        $user = $request->user();
        /** @var PlaneReservation $planeReservation */
        $planeReservation = PlaneReservation::withTrashed()->where('id', $validated['reservation_id'])->firstOrFail();
        
        $this->authorize('remove', $planeReservation);

        $planeReservation->delete();
        // TODO: add removed by

        $isAuthorOfReservation = $user->id === $planeReservation->user_id;
        if (!$isAuthorOfReservation) {
            $this->smsService->sendReservationCancellation($planeReservation);
        }

        return response()->json([], 200);
    }

    public function confirmReservation(PlaneReservationConfirmRequest $request): JsonResponse
    {
        /** @var array<string, string> $validated */
        $validated = $request->validated();
        
        /** @var User $user */
        $user = $request->user();
        
        $planeReservation = PlaneReservation::where('id', $validated['reservation_id'])->firstOrFail();
        $this->authorize('confirm', $planeReservation);

        $planeReservation->confirmed_at = CarbonImmutable::now();
        $planeReservation->confirmed_by = $user->id;
        $planeReservation->save();

        $this->smsService->sendReservationConfirmation($planeReservation);

        return response()->json([], 200);
    }

    public function getAllReservationsForDate(PlaneReservationGetAllForDate $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $date = CarbonImmutable::parse($validated['date'])->startOfDay(); // @phpstan-ignore-line
        $reservations = $this->planeReservationService->getAllReservationsWithActionsForDate($date, $user);

        return response()->json($reservations);
    }
}
