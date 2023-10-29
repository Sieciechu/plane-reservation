<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterNewUser;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterNewUser $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']); // @phpstan-ignore-line
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

    public function login(UserLoginRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $email = $validated['email'];
        
        /** @var string $password */
        $password = $validated['password'];
    
        $user = User::where('email', $email)->first();
       
        if (null === $user || !Hash::check($password, $user->password)) {
            return response()->json([
                'error' => 'Invalid login or password',
            ], 401);
        }

        $authToken = $user->createToken('authToken')->plainTextToken;
        return response()->json(['auth_token' => $authToken], 200);
    }
}
