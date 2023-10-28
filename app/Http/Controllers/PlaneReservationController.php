<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaneReservationListByDateRequest;
use App\Http\Requests\PlaneReservationMakeRequest;
use App\Http\Requests\UpdatePlaneReservationRequest;
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

        $planeReservations = PlaneReservation::where('date_from', $request->date)->get();

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

        $planeReservation = PlaneReservation::create($validated);

        return response()->json([], 201);
    }
}
