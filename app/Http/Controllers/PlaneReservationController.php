<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaneReservationListByDateRequest;
use App\Http\Requests\PlaneReservationMakeRequest;
use App\Models\Plane;
use App\Models\PlaneReservation;
use Illuminate\Http\JsonResponse;

class PlaneReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listByDate(PlaneReservationListByDateRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $planeReservations = PlaneReservation::where('starts_at_date', $request->starts_at_date)->get();

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

        /** @var string $planeRegistration */
        $planeRegistration = $validated['plane_registration'];
        $plane = Plane::where('registration', $planeRegistration)->firstOrFail();

        unset($validated['plane_registration']);
        $validated['plane_id'] = $plane->id;
        $validated['ends_at_date'] = $validated['starts_at_date'];
        
        $startTime = CarbonImmutable::parse($validated['starts_at_date'] . ' ' . $validated['starts_at_time']);
        $endTime = CarbonImmutable::parse($validated['ends_at_date'] . ' ' . $validated['ends_at_time']);
        $validated['time'] = $startTime->diffInMinutes($endTime);

        // $this->reservationChecker->checkAll($validated);

        $planeReservation = PlaneReservation::create($validated);

        // send mail to user
        // $user = User::where('id', $validated['user_id'])->firstOrFail();
        // $user->notify(new \App\Notifications\PlaneReservationCreated($planeReservation));


        return response()->json([], 201);
    }
}
