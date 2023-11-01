<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlaneReservationConfirmRequest;
use App\Http\Requests\PlaneReservationListByDateRequest;
use App\Http\Requests\PlaneReservationMakeRequest;
use App\Http\Requests\PlaneReservationRemoveRequest;
use App\Http\Requests\PlaneReservationUpdateRequest;
use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserReservationLimit;
use App\Models\UserRole;
use App\Services\PlaneReservationChecker;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class PlaneReservationController extends Controller
{
    public function __construct(
         private PlaneReservationChecker $reservationChecker,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function listByDate(PlaneReservationListByDateRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $startsAt = CarbonImmutable::parse($validated['starts_at_date'])->startOfDay(); // @phpstan-ignore-line
        $planeReservations = PlaneReservation::query()
            ->whereYear('starts_at', $startsAt->format('Y'))
            ->whereMonth('starts_at', $startsAt->format('m'))
            ->get();

        return response()->json([
            'data' => $planeReservations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function make(PlaneReservationMakeRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        
        $user = $request->user();

        /** @var string $planeRegistration */
        $planeRegistration = $validated['plane_registration'];
        $plane = Plane::where('registration', $planeRegistration)->firstOrFail();

        unset($validated['plane_registration']);
        $validated['plane_id'] = $plane->id;
        
        $startsAt = CarbonImmutable::parse($validated['starts_at']); // @phpstan-ignore-line
        $endsAt = CarbonImmutable::parse($validated['ends_at']); // @phpstan-ignore-line
        $endsAt = $startsAt->setTimeFromTimeString($endsAt->toTimeString());
        $validated['time'] = $startsAt->diffInMinutes($endsAt);

        $this->reservationChecker->checkAll($validated);

        $planeReservation = PlaneReservation::create($validated);

        // send mail to user
        // $user = User::where('id', $validated['user_id'])->firstOrFail();
        // $user->notify(new \App\Notifications\PlaneReservationCreated($planeReservation));


        return response()->json([], 201);
    }

    public function removeReservation(PlaneReservationRemoveRequest $request): JsonResponse
    {
        /** @var array<string, string> $validated */
        $validated = $request->validated();
        
        $user = $request->user();
        $planeReservation = PlaneReservation::withTrashed()->where('id', $validated['reservation_id'])->firstOrFail();
        
        $this->authorize('remove', $planeReservation);

        $planeReservation->delete();

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

        return response()->json([], 200);
    }
}
