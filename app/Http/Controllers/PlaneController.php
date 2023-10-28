<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaneRequest;
use App\Http\Requests\UpdatePlaneRequest;
use App\Models\Plane;
use Illuminate\Http\JsonResponse;

class PlaneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Plane::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlaneRequest $request): JsonResponse
    {
        // $this->authorize('create', $request->user());
        $validated = $request->validated();
        $plane = Plane::create($validated);
        
        return response()->json([
            'data' => $plane,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $planeRegistration): JsonResponse
    {
        return response()->json([
            'data' => Plane::where('registration', $planeRegistration)->firstOrFail(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plane $plane)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaneRequest $request, Plane $plane)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plane $plane)
    {
        //
    }
}
