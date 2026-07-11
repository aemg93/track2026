<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerformanceRequest;
use App\Services\FinancialSummaryService;
use App\Services\PerformanceAnalyticsService;
use App\Services\PerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(
        private PerformanceService $service,
        private PerformanceAnalyticsService $analytics,
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->list($request),
        ]);
    }

    public function store(PerformanceRequest $request): JsonResponse
    {
        $performance = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $performance,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $performance = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => $performance,
            'financial' => $this->financialSummary->summary($performance),
        ]);
    }

    public function update(
        PerformanceRequest $request,
        int $id
    ): JsonResponse {

        $performance = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Deleted',
        ]);
    }

    public function analytics(
        Request $request,
        int $id
    ): JsonResponse {

        $performance = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => $this->analytics->summary(
                $performance,
                $request->input('from'),
                $request->input('to')
            ),
        ]);
    }

    public function leaderboard(
        Request $request
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'data' => $this->service->leaderboard(
                (int) $request->input('limit', 20)
            ),
        ]);
    }

    public function platforms(int $id): JsonResponse
    {
        $performance = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => $performance->platforms,
        ]);
    }
}