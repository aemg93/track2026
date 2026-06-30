<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Services\FinancialSynchronizationService;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function __construct(
        private FinancialSynchronizationService $financialSynchronizationService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Deduction::with('performance');


        if ($user->hasRole('Performance')) {

            $query->where(
                'user_id',
                $user->id
            );

        }


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
                ->map(function ($deduction) {

                    return [

                        'id' => $deduction->id,

                        'type' => 'deduction',

                        'performance' => [

                            'id' => $deduction->performance?->id,

                            'name' =>
                                $deduction->performance?->nickname

                        ],

                        'category' =>
                            $deduction->category,

                        'reason' =>
                            $deduction->reason,

                        'amount' =>
                            (float) $deduction->amount,

                        'date' =>
                            $deduction->date,

                    ];

                })

        ]);

    }


    public function store(Request $request)
    {

        $data = $request->validate([

            'performance_id' => [
                'required',
                'exists:performances,id'
            ],

            'category' => [
                'required',
                'string',
                'max:100'
            ],

            'reason' => [
                'required',
                'string',
                'max:255'
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0'
            ],

            'date' => [
                'required',
                'date'
            ],

        ]);


        $user = $request->user();


        $data['user_id'] = $user->id;


        if ($data['amount'] > 100000) {

            $installments = 3;

            $data['is_installment'] = true;

            $data['installments'] = $installments;

            $data['installment_value'] =
                round(
                    $data['amount'] / $installments,
                    2
                );

        } else {

            $data['is_installment'] = false;

        }


        $deduction = Deduction::create($data);


        /*
        |--------------------------------------------------------------------------
        | Financial synchronization
        |--------------------------------------------------------------------------
        */

        $this->financialSynchronizationService
            ->synchronizePerformance(
                $deduction->performance
            );


        return response()->json([

            'message' =>
                'Deduction created successfully',

            'data' => [

                'id' =>
                    $deduction->id,

                'type' =>
                    'deduction',

                'performance_id' =>
                    $deduction->performance_id,

                'category' =>
                    $deduction->category,

                'amount' =>
                    (float) $deduction->amount,

                'date' =>
                    $deduction->date,

            ]

        ], 201);

    }
}