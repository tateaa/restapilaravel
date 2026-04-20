<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Kredensial tidak valid.',
            ], 401);
        }

        // Abilities (scope token) ditentukan berdasarkan role
        $abilities = match (true) {
            $user->hasRole('admin')  => ['posts:read', 'posts:write', 'posts:delete',
                                         'comments:read', 'comments:write', 'comments:delete'],
            $user->hasRole('editor') => ['posts:read', 'posts:write',
                                         'comments:read', 'comments:write'],
            default                  => ['posts:read', 'comments:read'],
        };

        // Token kedaluwarsa dalam 2 jam
        $tokenResult = $user->createToken(
            name: 'api-token',
            abilities: $abilities,
            expiresAt: now()->addHours(2),
        );

        return response()->json([
            'token'      => $tokenResult->plainTextToken,
            'expires_at' => $tokenResult->accessToken->expires_at->toISOString(),
            'role'       => $user->getRoleNames()->first(),
            'abilities'  => $abilities,
        ], 200);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.'], 200);
    }

    public function me(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->getRoleNames()->first(),
        ]);
    }
}
