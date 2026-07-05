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

            'platform_id' => [
                'required',
                'exists:platforms,id',
            ],

            'earned_at' => [
                'required',
                'date',
            ],

            'original_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'original_currency' => [
                'required',
                'in:usd,tokens',
            ],

        ]);


        $user = $request->user();


        if (
            $user->hasRole('Performance')
        ) {

            abort(
                403,
                'Not allowed to create earnings'
            );

        }


        $earning = $service->create(
            $data
        );


        return response()->json([

            'success' => true,

            'message' => 'Earning created successfully',

            'data' => $earning,

        ], 201);

    }
}