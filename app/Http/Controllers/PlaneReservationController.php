<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaneReservationMakeRequest;
use App\Http\Requests\UpdatePlaneReservationRequest;
use App\Models\PlaneReservation;

class PlaneReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listByDate(PlaneReservationListByDateRequest $request)
    {
       $validated = $request->validated();

        $planeReservations = PlaneReservation::where('date_from', $request->date)->get();

        return response()->json([
            'data' => $planeReservations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function make(PlaneReservationMakeRequest $request)
    {
        $validated = $request->validated();

        $planeReservation = PlaneReservation::create($validated);

        return response()->json([], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlaneReservation $planeReservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlaneReservation $planeReservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaneReservationRequest $request, PlaneReservation $planeReservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlaneReservation $planeReservation)
    {
        //
    }
}
