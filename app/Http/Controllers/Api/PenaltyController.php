<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PenaltyService;
use App\Models\Penalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Penalty::with('performance');

        if ($user->hasRole('Performance')) {
            $query->where('user_id', $user->id);
        }

        if ($user->hasRole('Admin')) {
            $query->whereHas('performance', function ($q) use ($user) {
                $q->where('studio_id', $user->studio_id);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->latest()->get()
        ]);
    }

    public function store(Request $request, PenaltyService $service)
    {
        $data = $request->validate([
            'performance_id' => ['required', 'exists:performances,id'],
            'reason'         => ['required', 'string', 'max:255'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'date'           => ['required', 'date'],
        ]);

        $user = $request->user();

        if ($user->hasRole('Performance') || $user->hasRole('Monitor')) {
            abort(403, 'Not allowed to create penalties');
        }

        $penalty = $service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Penalty created successfully',
            'data' => $penalty
        ], 201);
    }
}