<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EarningService;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index(
        Request $request,
        EarningService $service
    ) {

        return response()->json([

            'success' => true,

            'data' => $service->list(
                $request->user()
            )

        ]);

    }

    public function store(
        Request $request,
        EarningService $service
    ) {

        $data = $request->validate([

            'performance_id' => [
                'required',
                'exists:performances,id',
            ],

            'period_start' => [
                'required',
                'date',
            ],

            'period_end' => [
                'required',
                'date',
                'after_or_equal:period_start',
            ],

            'gross_usd' => [
                'required',
                'numeric',
                'min:0',
            ],

        ]);

        $user = $request->user();

        if (
            $user->hasRole('Performance') ||
            $user->hasRole('Monitor')
        ) {

            abort(403, 'Not allowed to create earnings');

        }

        $earning = $service->create($data);

        return response()->json([

            'success' => true,

            'message' => 'Earning created successfully',

            'data' => $earning,

        ], 201);

    }
}