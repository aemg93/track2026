<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Performance::query();

        if ($user->hasRole('Performance')) {
            $query->where('user_id', $user->id);
        }

        if ($user->hasRole('Admin')) {
            $query->where('studio_id', $user->studio_id);
        }

        $performances = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $performances
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $performance = Performance::with([
                'earnings',
                'bonuses',
                'penalties'
            ])
            ->findOrFail($id);

        if ($user->hasRole('Performance') && $performance->user_id !== $user->id) {
            abort(403);
        }

        if ($user->hasRole('Admin') && $performance->studio_id !== $user->studio_id) {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }
}