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
            'password' => ['required']
        ]);

        if (!Auth::attempt($credentials)) {

            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);

        }

        $user = Auth::user();

        $token = $user
            ->createToken('track2026-token')
            ->plainTextToken;

        return response()->json([
            'success' => true,

            'token' => $token,

            'user' => [

                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'roles' => method_exists(
                    $user,
                    'getRoleNames'
                )
                ? $user->getRoleNames()
                : [],

                'permissions' => method_exists(
                    $user,
                    'getAllPermissions'
                )
                ? $user->getAllPermissions()
                        ->pluck('name')
                : [],

                'studio_id' => $user->studio_id ?? null,
            ]
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([

            'success' => true,

            'data' => [

                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'roles' => method_exists(
                    $user,
                    'getRoleNames'
                )
                ? $user->getRoleNames()
                : [],

                'permissions' => method_exists(
                    $user,
                    'getAllPermissions'
                )
                ? $user->getAllPermissions()
                        ->pluck('name')
                : [],

                'studio_id' => $user->studio_id ?? null,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada'
        ]);
    }
}