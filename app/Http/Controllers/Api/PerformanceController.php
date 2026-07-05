<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FinancialSummaryService;
use App\Services\PerformanceAnalyticsService;
use App\Services\PerformanceService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(
        private PerformanceService $service,
        private PerformanceAnalyticsService $analytics,
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function index(Request $request)
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->list(
                $request
            ),

        ]);
    }

    public function store(Request $request)
    {
        $performance = $this->service->create(

            $request->validate(
                $this->service->storeRules()
            )

        );

        return response()->json([

            'success' => true,

            'data' => $performance,

        ], 201);
    }

    public function show($id)
    {
        $performance = $this->service->find(
            $id
        );

        return response()->json([

            'success' => true,

            'data' => $performance,

            'financial' =>

                $this->financialSummary
                    ->summary(
                        $performance
                    ),

        ]);
    }

    public function update(
        Request $request,
        $id
    ) {
        $performance = $this->service->update(

            $id,

            $request->validate(
                $this->service->updateRules()
            )

        );

        return response()->json([

            'success' => true,

            'data' => $performance,

        ]);
    }

    public function destroy($id)
    {
        $this->service->delete(
            $id
        );

        return response()->json([

            'success' => true,

            'message' => 'Deleted',

        ]);
    }

    public function analytics($id)
    {
        $performance = $this->service->find(
            $id
        );

        return response()->json([

            'success' => true,

            'data' => $this->analytics->summary(

                $performance,

                request('from'),

                request('to')

            ),

        ]);
    }

    public function leaderboard(
        Request $request
    ) {
        return response()->json([

            'success' => true,

            'data' => $this->service->leaderboard(

                (int) $request->get(
                    'limit',
                    20
                )

            ),

        ]);
    }

    public function platforms($id)
    {
        $performance = $this->service->find(
            $id
        );

        return response()->json([

            'success' => true,

            'data' => $performance->platforms,

        ]);
    }
}