<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BonusService;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Bonus::with([
            'performance',
            'user:id,name',
        ]);


        if ($user->hasRole('Admin')) {

            $query->whereHas(
                'performance',
                function ($q) use ($user) {

                    $q->where(
                        'studio_id',
                        $user->studio_id
                    );

                }
            );

        }


        return response()->json([

            'data' => $query
                ->latest()
                ->get()
                ->map(function ($bonus) {

                    return [

                        'id' =>
                            $bonus->id,

                        'type' =>
                            'bonus',

                        'performance' => [

                            'id' =>
                                $bonus->performance?->id,

                            'name' =>
                                $bonus->performance?->name,

                        ],

                        'performed_by' =>
                            $bonus->user?->name,

                        'amount' =>
                            (float) $bonus->amount,

                        'currency' =>
                            'USD',

                        'reason' =>
                            $bonus->reason,

                        'date' =>
                            $bonus->date,

                    ];

                }),

        ]);

    }


    public function store(
        Request $request,
        BonusService $service
    ) {

        $data = $request->validate([

            'performance_id' => [
                'required',
                'exists:performances,id',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'date' => [
                'required',
                'date',
            ],

        ]);


        $user = $request->user();


        if (
            $user->hasRole('Performance') ||
            $user->hasRole('Monitor')
        ) {

            abort(
                403,
                'Not allowed to create bonuses'
            );

        }


        $bonus = $service->create(
            $data
        );


        return response()->json([

            'message' =>
                'Bonus created successfully',

            'data' => [

                'id' =>
                    $bonus->id,

                'type' =>
                    'bonus',

                'performance' =>
                    $bonus->performance,

                'performed_by' =>
                    $bonus->user?->name,

                'amount' =>
                    (float) $bonus->amount,

                'currency' =>
                    'USD',

                'reason' =>
                    $bonus->reason,

                'date' =>
                    $bonus->date,

            ],

        ], 201);

    }
}