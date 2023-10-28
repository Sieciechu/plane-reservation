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
     * Store a newly created resource in storage.
     */
    public function store(StorePlaneRequest $request): JsonResponse
    {
        // $this->authorize('create', $request->user());
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $plane = Plane::create($validated);
        
        return response()->json([
            'data' => $plane,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $plane_registration): JsonResponse
    {
        return response()->json([
            'data' => Plane::where('registration', $plane_registration)->firstOrFail(),
        ]);
    }
}
