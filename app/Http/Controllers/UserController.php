<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterNewUser;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(RegisterNewUser $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = UserRole::User;

        $user = User::create($validated);

        return response()->json([
            'data' => $user,
        ]);
    }

    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }    
}
