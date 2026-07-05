<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('track2026-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatUser($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada',
        ]);
    }

    private function formatUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,

            'roles' => method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()->values()
                : [],

            'permissions' => method_exists($user, 'getAllPermissions')
                ? $user->getAllPermissions()->pluck('name')->values()
                : [],

            'studio_id' => $user->studio_id ?? null,
        ];
    }
}