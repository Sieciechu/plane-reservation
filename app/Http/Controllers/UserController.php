<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterNewUser;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function register(RegisterNewUser $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']); // @phpstan-ignore-line
        $validated['role'] = UserRole::User;

        $user = User::create($validated);

        return response()->json([
            'data' => $user,
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }    
}
